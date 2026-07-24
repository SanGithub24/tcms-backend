<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ComplaintAssignment;
use App\Models\Notification;

use Carbon\Carbon;

class AssignmentController extends Controller
{
    public function assignPolice(Request $request)
    {
        $validated = $request->validate([
            'complaintID' => 'required|exists:complaints,complaintID',
            'userID_police' => 'required|exists:users,userID',
            'assigned_by_admin' => 'required|exists:users,userID',
            'assignment_type' => 'required|string',
            'assignment_reason' => 'required|string'
        ]);

        $complaint = \App\Models\Complaint::with('tourist')->find($validated['complaintID']);

        // Find existing assigned officers
        $existingAssignments = ComplaintAssignment::where('complaintID', $validated['complaintID'])
            ->where('assignment_status', 'active')
            ->where('userID_police', '!=', $validated['userID_police'])
            ->with('officer')
            ->get();

        $newOfficer = \App\Models\User::find($validated['userID_police']);

        $assignment = ComplaintAssignment::create([
            'complaintID' => $validated['complaintID'],
            'userID_police' => $validated['userID_police'],
            'assigned_by_admin' => $validated['assigned_by_admin'],
            'assigned_at' => Carbon::now(),
            'assignment_type' => $validated['assignment_type'],
            'assignment_status' => 'active',
            'assignment_reason' => $validated['assignment_reason']
        ]);

        if ($complaint->status === 'Submitted') {
            $complaint->status = 'Assigned';
            $complaint->save();
        }

        Notification::create([
            'userID' => $validated['userID_police'],
            'complaintID' => $validated['complaintID'],
            'title' => 'Complaint Assigned',
            'message' => 'Complaint TCMS-' . $validated['complaintID'] . ' has been assigned to you. Reason: ' . $validated['assignment_reason'],
            'is_read' => false,
        ]);

        // Email to new officer
        try {
            $subject = "TCMS: Complaint Assigned";
            \Illuminate\Support\Facades\Mail::send('emails.complaint_assigned', [
                'user' => $newOfficer,
                'complaint' => $complaint,
                'assignment_reason' => $validated['assignment_reason']
            ], function ($message) use ($newOfficer, $subject) {
                $message->to($newOfficer->email)->subject($subject);
            });
            \App\Models\Email::create([
                        'complaintID' => $complaint->complaintID ?? null,
                        'recipient_email' => $newOfficer->email,
                'subject' => $subject,
                'sent_status' => 'Sent',
                        'sent_at' => now(),
                'email_type' => 'Complaint Assignment',
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Police assignment email failed: ' . $e->getMessage());
        }

        // Emails to existing officers
        foreach ($existingAssignments as $existing) {
            if ($existing->officer) {
                Notification::create([
                    'userID' => $existing->officer->userID,
                    'complaintID' => $validated['complaintID'],
                    'title' => 'Additional Officer Assigned',
                    'message' => 'An additional officer has been assigned to TCMS-' . $validated['complaintID'] . '. Reason: ' . $validated['assignment_reason'],
                    'is_read' => false,
                ]);

                try {
                    $subject = "TCMS: Additional Officer Assigned to Complaint";
                    \Illuminate\Support\Facades\Mail::send('emails.complaint_additional_officer', [
                        'user' => $existing->officer,
                        'newOfficer' => $newOfficer,
                        'complaint' => $complaint,
                        'assignment_reason' => $validated['assignment_reason']
                    ], function ($message) use ($existing, $subject) {
                        $message->to($existing->officer->email)->subject($subject);
                    });
                    \App\Models\Email::create([
                        'complaintID' => $complaint->complaintID ?? null,
                        'recipient_email' => $existing->officer->email,
                        'subject' => $subject,
                        'sent_status' => 'Sent',
                        'sent_at' => now(),
                        'email_type' => 'Complaint Assignment',
                    ]);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Additional officer email failed: ' . $e->getMessage());
                }
            }
        }

        return response()->json([
            'message' => 'Police assigned successfully',
            'assignment' => $assignment
        ], 201);
    }

    public function removePolice(Request $request)
    {
        $validated = $request->validate([
            'complaintID' => 'required|exists:complaints,complaintID',
            'userID_police' => 'required|exists:users,userID',
            'removal_reason' => 'required|string'
        ]);

        $assignment = ComplaintAssignment::where('complaintID', $validated['complaintID'])
            ->where('userID_police', $validated['userID_police'])
            ->first();

        if (!$assignment) {
            return response()->json(['message' => 'Assignment not found'], 404);
        }

        $officer = \App\Models\User::find($validated['userID_police']);
        $complaint = \App\Models\Complaint::find($validated['complaintID']);

        // Delete the assignment
        $assignment->delete();

        // Note: As per user instructions, we DO NOT change the complaint status 
        // even if no officers remain. Status remains as is.

        // Send Notification to the removed officer
        Notification::create([
            'userID' => $validated['userID_police'],
            'complaintID' => $validated['complaintID'],
            'title' => 'Unassigned from Complaint',
            'message' => 'You have been unassigned from TCMS-' . $validated['complaintID'] . '. Reason: ' . $validated['removal_reason'],
            'is_read' => false,
        ]);

        // Send Email to the removed officer
        if ($officer) {
            try {
                $subject = "TCMS: Unassigned from Complaint";
                \Illuminate\Support\Facades\Mail::send('emails.complaint_unassigned', [
                    'user' => $officer,
                    'complaint' => $complaint,
                    'removal_reason' => $validated['removal_reason']
                ], function ($message) use ($officer, $subject) {
                    $message->to($officer->email)->subject($subject);
                });
                \App\Models\Email::create([
                        'complaintID' => $complaint->complaintID ?? null,
                        'recipient_email' => $officer->email,
                    'subject' => $subject,
                    'sent_status' => 'Sent',
                        'sent_at' => now(),
                    'email_type' => 'Complaint Unassignment',
                ]);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Police unassignment email failed: ' . $e->getMessage());
            }
        }

        // Notify remaining officers
        $remainingAssignments = ComplaintAssignment::where('complaintID', $validated['complaintID'])
            ->where('assignment_status', 'active')
            ->where('userID_police', '!=', $validated['userID_police'])
            ->with('officer')
            ->get();

        foreach ($remainingAssignments as $remaining) {
            if ($remaining->officer) {
                Notification::create([
                    'userID' => $remaining->officer->userID,
                    'complaintID' => $validated['complaintID'],
                    'title' => 'Colleague Unassigned from Complaint',
                    'message' => 'Officer ' . $officer->full_name . ' has been unassigned from TCMS-' . $validated['complaintID'] . '. Reason: ' . $validated['removal_reason'],
                    'is_read' => false,
                ]);

                try {
                    $subject = "TCMS: Colleague Unassigned from Complaint";
                    \Illuminate\Support\Facades\Mail::send('emails.colleague_unassigned', [
                        'remainingOfficer' => $remaining->officer,
                        'removedOfficer' => $officer,
                        'complaint' => $complaint,
                        'removal_reason' => $validated['removal_reason']
                    ], function ($message) use ($remaining, $subject) {
                        $message->to($remaining->officer->email)->subject($subject);
                    });
                    \App\Models\Email::create([
                        'complaintID' => $complaint->complaintID ?? null,
                        'recipient_email' => $remaining->officer->email,
                        'subject' => $subject,
                        'sent_status' => 'Sent',
                        'sent_at' => now(),
                        'email_type' => 'Complaint Unassignment',
                    ]);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Colleague unassignment email failed: ' . $e->getMessage());
                }
            }
        }

        return response()->json([
            'message' => 'Police officer unassigned successfully'
        ], 200);
    }

    public function getAssignedComplaints(int $userID)
    {
        $assignments = ComplaintAssignment::where('userID_police', $userID)
            ->with('complaint.location')
            ->whereHas('complaint')
            ->orderByDesc('assigned_at')
            ->get();

        $complaints = $assignments->map(function ($assignment) {
            return [
                'complaintID' => $assignment->complaint->complaintID,
                'category' => $assignment->complaint->category,
                'complaint_date' => $assignment->complaint->complaint_date,
                'status' => $assignment->complaint->status,
                'location' => $assignment->complaint->location,
            ];
        })->values();

        return response()->json([
            'complaints' => $complaints
        ]);
    }

    public function policeDashboardStats(int $userID)
    {
        $assigned = ComplaintAssignment::where('userID_police', $userID)->count();

        $pending = ComplaintAssignment::where('userID_police', $userID)
            ->whereHas('complaint', function ($q) {
                $q->whereNotIn('status', ['Resolved', 'Rejected']);
            })->count();

        $resolved = ComplaintAssignment::where('userID_police', $userID)
            ->whereHas('complaint', function ($q) {
                $q->where('status', 'Resolved');
            })->count();

        $recent = ComplaintAssignment::with('complaint.tourist')
            ->where('userID_police', $userID)
            ->latest()
            ->take(5)
            ->get();

        return response()->json([
            'assigned' => $assigned,
            'pending' => $pending,
            'resolved' => $resolved,
            'recent' => $recent
        ]);
    }

}
