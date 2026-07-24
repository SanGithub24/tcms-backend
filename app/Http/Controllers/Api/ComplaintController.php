<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Complaint;
use App\Models\Location;
use App\Models\Evidence;
use App\Models\Notification;

use App\Models\User;
use App\Models\ComplaintAssignment;
use Carbon\Carbon;

use App\Mail\ComplaintRejectedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use App\Models\TouristNotification;

class ComplaintController extends Controller
{

    public function submitComplaint(Request $request)
    {
        try {
            
            $location = Location::create([
                'city' => $request->city,
                'district' => $request->district,
                'province' => $request->province,
                'latitude' => $request->latitude ?? 0.0,
                'longitude' => $request->longitude ?? 0.0,
                'description' => $request->location_description,
            ]);

            
            $complaint = Complaint::create([
                'touristID' => $request->touristID,
                'locationID' => $location->locationID,
                'category' => $request->category,
                'description' => $request->description,
                'incident_date' => $request->incident_date,
                'complaint_date' => now()->toDateString(),
                'status' => 'Submitted',
                'contact_method' => $request->contactMethod,
                'contact_number' => $request->phone,
            ]);

            $location->complaintID = $complaint->complaintID;
            $location->save();

            TouristNotification::create([
                'touristID'   => $complaint->touristID,
                'complaintID' => $complaint->complaintID,
                'title'       => 'Complaint Submitted',
                'message'     => 'Your complaint (CMP'.$complaint->complaintID.') has been submitted successfully.',
                'type'        => 'complaint',
                'is_read'     => false,
            ]);

            $tourist = \App\Models\Tourist::find($request->touristID);
            if ($tourist) {
                try {
                    $subject = "TCMS: Complaint Submitted Successfully";
                    \Illuminate\Support\Facades\Mail::send('emails.complaint_submitted', [
                        'user' => $tourist,
                        'complaint' => $complaint
                    ], function ($message) use ($tourist, $subject) {
                        $message->to($tourist->email)
                                ->subject($subject);
                    });

                    \App\Models\Email::create([
                        'complaintID' => $complaint->complaintID ?? null,
                        'recipient_email' => $tourist->email,
                        'subject' => $subject,
                        'sent_status' => 'Sent',
                        'sent_at' => now(),
                        'email_type' => 'Complaint Submission',
                    ]);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Complaint submission email failed: ' . $e->getMessage());
                    \App\Models\Email::create([
                        'complaintID' => $complaint->complaintID ?? null,
                        'recipient_email' => $tourist->email,
                        'subject' => "TCMS: Complaint Submitted Successfully",
                        'sent_status' => 'Failed',
                        'sent_at' => null,
                        'email_type' => 'Complaint Submission',
                    ]);
                }
            }

            if ($location) {
            $police = User::where('user_type', 'police')
                ->where('district', $location->district)
                ->first();

            if ($police) {
                $assignmentReason = 'Automatically assigned based on incident location matching the officer\'s district.';

                ComplaintAssignment::create([
                    'complaintID' => $complaint->complaintID,
                    'userID_police' => $police->userID,
                    'assigned_by_admin' => null,
                    'assigned_at' => Carbon::now(),
                    'assignment_type' => 'auto',
                    'assignment_status' => 'active',
                    'assignment_reason' => $assignmentReason
                ]);

                $complaint->status = 'Assigned';
                $complaint->save();

                Notification::create([
                    'userID' => $police->userID,
                    'complaintID' => $complaint->complaintID,
                    'title' => 'New Complaint Assigned',
                    'message' => 'Complaint TCMS-' . $complaint->complaintID . ' has been automatically assigned to you. Reason: ' . $assignmentReason,
                    'is_read' => false,
                ]);

                try {
                    $subject = "TCMS: New Complaint Auto-Assigned";
                    \Illuminate\Support\Facades\Mail::send('emails.complaint_assigned', [
                        'user' => $police,
                        'complaint' => $complaint,
                        'assignment_reason' => $assignmentReason
                    ], function ($message) use ($police, $subject) {
                        $message->to($police->email)
                                ->subject($subject);
                    });

                    \App\Models\Email::create([
                        'complaintID' => $complaint->complaintID ?? null,
                        'recipient_email' => $police->email,
                        'subject' => $subject,
                        'sent_status' => 'Sent',
                        'sent_at' => now(),
                        'email_type' => 'Complaint Assignment',
                    ]);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Police complaint assignment email failed: ' . $e->getMessage());
                    \App\Models\Email::create([
                        'complaintID' => $complaint->complaintID ?? null,
                        'recipient_email' => $police->email,
                        'subject' => "TCMS: New Complaint Auto-Assigned",
                        'sent_status' => 'Failed',
                        'sent_at' => null,
                        'email_type' => 'Complaint Assignment',
                    ]);
                }

            }
        }

            if ($request->hasFile('evidence')) {
                foreach ($request->file('evidence') as $file) {
                    $path = $file->store('evidences', 'public');
                    Evidence::create([
                        'complaintID' => $complaint->complaintID,
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'file_type' => $file->getClientMimeType(),
                        'uploaded_time' => now(),
                    ]);
                }
            }

            return response()->json([
                'message' => 'Complaint submitted successfully',
                'complaint' => $complaint
            ], 201);

        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getTouristComplaints(int $touristID)
    {
        $complaints = Complaint::where('touristID', $touristID)
            ->with([
                'tourist',
                'location',
                'evidence',
                'assignments.officer'
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'complaints' => $complaints
        ]);
    }

    public function updateComplaint(Request $request, int $id)
    {
        $complaint = Complaint::with([
            'location',
            'assignments.officer'
        ])->find($id);

        if (!$complaint) {
            return response()->json([
                'message' => 'Complaint not found.'
            ], 404);
        }

        if (Carbon::parse($complaint->created_at)->addMinutes(15)->isPast()) {
            return response()->json([
                'message' => 'The 15-minute editing period for this complaint has expired.'
            ], 403);
        }

        $request->validate([
            'category' => 'required|string|max:255',
            'description' => 'required|string',
            'incident_date' => 'required|date',
            'contact_method' => 'required|string|max:255',
            'contact_number' => 'nullable|string|max:50',

            'province' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'landmark' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',

            'deleted_evidence_ids' => 'nullable|array',
            'deleted_evidence_ids.*' => 'integer',

            'new_evidence' => 'nullable|array',
            'new_evidence.*' => 'file|mimes:jpg,jpeg,png,pdf,mp4,mov,avi|max:20480',
        ]);

        try {

            DB::beginTransaction();

            $complaint->update([
                'category' => $request->category,
                'description' => $request->description,
                'incident_date' => $request->incident_date,
                'contact_method' => $request->contact_method,
                'contact_number' => $request->contact_number,
            ]);


            $location = Location::where(
                'locationID',
                $complaint->locationID
            )->first();

            if ($location) {

                $location->update([
                    'province' => $request->province,
                    'district' => $request->district,
                    'city' => $request->city,
                    'description' => $request->landmark,
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                ]);

            }

            $deletedEvidenceIds = $request->input(
                'deleted_evidence_ids',
                []
            );

            if (!empty($deletedEvidenceIds)) {

                $evidenceToDelete = Evidence::where(
                    'complaintID',
                    $complaint->complaintID
                )
                ->whereIn(
                    'evidenceID',
                    $deletedEvidenceIds
                )
                ->get();

                foreach ($evidenceToDelete as $evidence) {

                    if (
                        $evidence->file_path &&
                        Storage::disk('public')->exists(
                            $evidence->file_path
                        )
                    ) {
                        Storage::disk('public')->delete(
                            $evidence->file_path
                        );
                    }

                    $evidence->delete();

                }

            }

            if ($request->hasFile('new_evidence')) {

                foreach (
                    $request->file('new_evidence')
                    as $file
                ) {

                    $path = $file->store(
                        'evidences',
                        'public'
                    );

                    Evidence::create([
                        'complaintID' =>
                            $complaint->complaintID,

                        'file_name' =>
                            $file->getClientOriginalName(),

                        'file_path' =>
                            $path,

                        'file_type' =>
                            $file->getClientMimeType(),

                        'uploaded_time' =>
                            now(),
                    ]);

                }

            }

            foreach ($complaint->assignments as $assignment) {

                if ($assignment->userID_police) {

                    Notification::create([
                        'userID' =>
                            $assignment->userID_police,

                        'complaintID' =>
                            $complaint->complaintID,

                        'title' =>
                            'Complaint Updated',

                        'message' =>
                            'Tourist has updated complaint CMP' .
                            $complaint->complaintID .
                            '. Please review the latest complaint details.',

                        'is_read' =>
                            false,
                    ]);

                }

            }

            $admins = User::where(
                'user_type',
                'admin'
            )->get();

            foreach ($admins as $admin) {

                Notification::create([
                    'userID' =>
                        $admin->userID,

                    'complaintID' =>
                        $complaint->complaintID,

                    'title' =>
                        'Complaint Updated',

                    'message' =>
                        'Tourist has updated complaint CMP' .
                        $complaint->complaintID .
                        '. Please review the latest complaint details.',

                    'is_read' =>
                        false,
                ]);

            }

            DB::commit();

            $updatedComplaint = Complaint::with([
                'tourist',
                'location',
                'evidence',
                'assignments.officer'
            ])->find(
                $complaint->complaintID
            );

            return response()->json([
                'message' =>
                    'Complaint updated successfully.',

                'complaint' =>
                    $updatedComplaint
            ], 200);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'message' =>
                    'Failed to update complaint.',

                'error' =>
                    $e->getMessage()
            ], 500);

        }
    }

    public function deleteTouristComplaint(int $id)
    {
        $complaint = Complaint::find($id);

        if (!$complaint) {
            return response()->json([
                'message' => 'Complaint not found.'
            ], 404);
        }

        // Tourist can delete the complaint only within 15 minutes
        // after submission, even if it was automatically assigned.
        if (Carbon::parse($complaint->created_at)->addMinutes(15)->isPast()) {
            return response()->json([
                'message' => 'The 15-minute deletion period for this complaint has expired.'
            ], 403);
        }

        try {

            DB::beginTransaction();

            $evidenceFiles = Evidence::where(
                'complaintID',
                $complaint->complaintID
            )->get();

            foreach ($evidenceFiles as $evidence) {

                if (
                    $evidence->file_path &&
                    Storage::disk('public')->exists($evidence->file_path)
                ) {
                    Storage::disk('public')->delete(
                        $evidence->file_path
                    );
                }

                $evidence->delete();
            }

            $assignments = ComplaintAssignment::with('officer')
                ->where('complaintID', $complaint->complaintID)
                ->get();

            foreach ($assignments as $assignment) {
                $officer = $assignment->officer;
                if ($officer && $officer->email) {
                    try {
                        $subject = "TCMS: Complaint CMP-{$complaint->complaintID} Cancelled";
                        $complaintID = $complaint->complaintID;
                        
                        \Illuminate\Support\Facades\Mail::send('emails.complaint_deleted_by_tourist', [
                            'officer' => $officer,
                            'complaintID' => $complaintID
                        ], function ($message) use ($officer, $subject) {
                            $message->to($officer->email)->subject($subject);
                        });
                        
                        \App\Models\Email::create([
                        'complaintID' => $complaint->complaintID ?? null,
                        'recipient_email' => $officer->email,
                            'subject' => $subject,
                            'sent_status' => 'Sent',
                        'sent_at' => now(),
                            'email_type' => 'Complaint Deleted (Tourist)',
                        ]);
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Police complaint deleted email failed: ' . $e->getMessage());
                        \App\Models\Email::create([
                        'complaintID' => $complaint->complaintID ?? null,
                        'recipient_email' => $officer->email,
                            'subject' => $subject ?? "TCMS: Complaint CMP-{$complaint->complaintID} Cancelled",
                            'sent_status' => 'Failed',
                        'sent_at' => null,
                            'email_type' => 'Complaint Deleted (Tourist)',
                        ]);
                    }
                }
            }

            ComplaintAssignment::where(
                'complaintID',
                $complaint->complaintID
            )->delete();

            Notification::where(
                'complaintID',
                $complaint->complaintID
            )->delete();

            $locationID = $complaint->locationID;

            $complaint->delete();

            if ($locationID) {
                Location::where(
                    'locationID',
                    $locationID
                )->delete();
            }

            DB::commit();

            return response()->json([
                'message' => 'Complaint deleted successfully.'
            ], 200);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'message' => 'Failed to delete complaint.',
                'error' => $e->getMessage()
            ], 500);

        }
    }

    public function updateStatus(Request $request)
    {
        $complaint = Complaint::with('tourist')->find($request->complaintID);

        if (!$complaint) {
            return response()->json([
                'message' => 'Complaint not found'
            ], 404);
        }

        $complaint->status = $request->status;
        $complaint->save();

        if ($request->status === 'Resolved') {
            TouristNotification::create([
                'touristID'   => $complaint->touristID,
                'complaintID' => $complaint->complaintID,
                'title'       => 'Complaint Resolved',
                'message'     => 'Your complaint (CMP'.$complaint->complaintID.') has been resolved. Investigation Note: ' . $complaint->final_resolution_note,
                'type'        => 'status',
                'is_read'     => false,
            ]);

            $tourist = $complaint->tourist;
            if ($tourist && $tourist->email) {
                try {
                    $subject = "TCMS: Update Regarding Your Complaint CMP-" . $complaint->complaintID;
                    \Illuminate\Support\Facades\Mail::send('emails.complaint_resolved', [
                        'user' => $tourist,
                        'complaint' => $complaint,
                        'police_note' => $complaint->final_resolution_note
                    ], function ($message) use ($tourist, $subject) {
                        $message->to($tourist->email)->subject($subject);
                    });
                    
                    \App\Models\Email::create([
                        'complaintID' => $complaint->complaintID ?? null,
                        'recipient_email' => $tourist->email,
                        'subject' => $subject,
                        'sent_status' => 'Sent',
                        'sent_at' => now(),
                        'email_type' => 'Complaint Resolution (Tourist)',
                    ]);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Tourist resolution email failed: ' . $e->getMessage());
                    \App\Models\Email::create([
                        'complaintID' => $complaint->complaintID ?? null,
                        'recipient_email' => $tourist->email,
                        'subject' => $subject ?? 'TCMS: Complaint Resolved',
                        'sent_status' => 'Failed',
                        'sent_at' => null,
                        'email_type' => 'Complaint Resolution (Tourist)',
                    ]);
                }
            }
        } else {
            TouristNotification::create([
                'touristID'   => $complaint->touristID,
                'complaintID' => $complaint->complaintID,
                'title'       => 'Complaint Status Updated',
                'message'     => 'Your complaint (CMP'.$complaint->complaintID.') status has been updated to '.$request->status.'.',
                'type'        => 'status',
                'is_read'     => false,
            ]);
        }

        return response()->json([
            'message' => 'Status updated successfully'
        ]);
    }

    public function savePoliceNote(Request $request)
    {
        $request->validate([
            'complaintID' => 'required|exists:complaints,complaintID',
            'police_note' => 'required|string'
        ]);

        $complaint = Complaint::find($request->complaintID);

        $officer = auth()->user();
        $date = now()->format('Y-m-d h:i A');
        $newNoteBlock = "--- INVESTIGATION NOTE ---\n";
        $newNoteBlock .= "Date: {$date}\n";
        if ($officer) {
            $newNoteBlock .= "Officer: {$officer->full_name} (Badge: {$officer->badge_number})\n";
        }
        $newNoteBlock .= "--------------------------\n";
        $newNoteBlock .= trim($request->police_note);
        
        if (!empty($complaint->police_note)) {
            $complaint->police_note = $complaint->police_note . "\n\n" . $newNoteBlock;
        } else {
            $complaint->police_note = $newNoteBlock;
        }

        $complaint->save();

        $complaint->load([
            'tourist',
            'location',
            'evidence',
            'assignments.officer'
        ]);

        return response()->json([
            'message' => 'Investigation note saved successfully.',
            'complaint' => $complaint
        ]);
    }

    public function getAllComplaints()
    {
        $complaints = Complaint::with([
            'tourist',
            'location',
            'assignments.officer'
        ])
        ->orderBy('created_at', 'desc')
        ->get();

        return response()->json([
            'complaints' => $complaints
        ]);
    }

    public function deleteComplaint(int $id)
    {
        $complaint = Complaint::find($id);

        if (!$complaint) {
            return response()->json([
                'message' => 'Complaint not found'
            ], 404);
        }

        $complaint->delete();

        return response()->json([
            'message' => 'Complaint deleted successfully'
        ]);
    }

    public function getComplaintById(int $id)
    {

        $complaint = Complaint::with([
            'tourist',
            'location',
            'evidence',
            'assignments.officer'
        ])->find($id);

        if (!$complaint) {
            return response()->json([
                'message' => 'Complaint not found'
            ], 404);
        }

        return response()->json([
            'complaint' => $complaint
        ]);
    }

    //Admin Dash

    public function dashboardStats()
    {
        $today = Complaint::whereDate('created_at', today())->count();

        $monthly = Complaint::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $pending = Complaint::whereNotIn('status', ['Resolved', 'Rejected'])->count();

        $resolved = Complaint::where('status', 'Resolved')->count();

        return response()->json([
            'today' => $today,
            'monthly' => $monthly,
            'pending' => $pending,
            'resolved' => $resolved
        ]);
    }

    public function complaintsByCategory()
    {
        $data = Complaint::selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->get();

        return response()->json($data);
    }

    public function complaintsByDistrict()
    {
        $data = Complaint::join('locations', 'complaints.locationID', '=', 'locations.locationID')
            ->selectRaw('district, COUNT(*) as count')
            ->groupBy('district')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        return response()->json($data);
    }

    public function recentComplaints()
    {
        $data = Complaint::with('tourist')
            ->latest()
            ->take(5)
            ->get();

        return response()->json($data);
    }

    public function rejectComplaint(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000'
        ]);

        $complaint = Complaint::with(['tourist', 'assignments.officer'])->find($id);

        if (!$complaint) {
            return response()->json(['message' => 'Complaint not found'], 404);
        }

        try {
            DB::beginTransaction();

            $complaint->status = 'Rejected';
            
            $newNoteBlock = "--- INVESTIGATION NOTE ---\n";
            $newNoteBlock .= "Date: " . now()->format('Y-m-d h:i A') . "\n";
            $newNoteBlock .= "Action: COMPLAINT REJECTED BY ADMIN\n";
            $newNoteBlock .= "--------------------------\n";
            $newNoteBlock .= "Reason: " . trim($request->rejection_reason);
            
            if (!empty($complaint->police_note)) {
                $complaint->police_note = $complaint->police_note . "\n\n" . $newNoteBlock;
            } else {
                $complaint->police_note = $newNoteBlock;
            }
            
            $complaint->save();

            // NOTIFY & EMAIL - TOURIST
            DB::table('tourist_notifications')->insert([
                'touristID'   => $complaint->touristID,
                'title'       => "Complaint CMP-{$complaint->complaintID} Rejected",
                'message'     => "Your complaint has been rejected. Reason: " . $request->rejection_reason,
                'is_read'     => false,
                'created_at'  => now(),
                'updated_at'  => now()
            ]);

            $touristEmailSent = false;
            if ($complaint->tourist && $complaint->tourist->email) {
                Mail::to($complaint->tourist->email)->send(
                    new ComplaintRejectedMail($complaint, $request->rejection_reason)
                );
                $touristEmailSent = true;
            }

            DB::table('emails')->insert([
                'complaintID'     => $complaint->complaintID,
                'recipient_email' => $complaint->tourist->email ?? 'unknown@tourist.com',
                'subject'         => "Update Regarding Your Complaint: CMP-{$complaint->complaintID}",
                'sent_status'     => $touristEmailSent ? 'Sent' : 'Failed',
                'email_type'      => 'Complaint Rejection (Tourist)',
                'sent_at'         => now(),
                'created_at'      => now(),
                'updated_at'      => now()
            ]);

            // NOTIFY & EMAIL - POLICE OFFICER
            if ($complaint->assignments && $complaint->assignments->count() > 0) {
                foreach ($complaint->assignments as $assignment) {
                    $officer = $assignment->officer;
                    
                    if ($officer && $officer->email) {
                        
                        // Add in-app notification for the police dashboard
                        DB::table('notifications')->insert([
                            'userID'      => $officer->userID,
                            'complaintID' => $complaint->complaintID,
                            'title'       => "Assigned Complaint Rejected",
                            'message'     => "Admin has officially rejected complaint CMP-{$complaint->complaintID}. Reason: {$request->rejection_reason}",
                            'is_read'     => false,
                            'created_at'  => now(),
                            'updated_at'  => now()
                        ]);

                        // email to the officer
                        $subject = "Assigned Complaint Rejected: CMP-{$complaint->complaintID}";

                        try {
                            Mail::send('emails.police_complaint_rejected', [
                                'officer' => $officer,
                                'complaint' => $complaint,
                                'rejectionReason' => $request->rejection_reason
                            ], function ($message) use ($officer, $subject) {
                                $message->to($officer->email)
                                        ->subject($subject);
                            });

                            DB::table('emails')->insert([
                                'complaintID'     => $complaint->complaintID,
                                'recipient_email' => $officer->email,
                                'subject'         => $subject,
                                'sent_status' => 'Sent',
                        'sent_at' => now(),
                                'email_type'      => 'Complaint Rejection (Police)',
                                'sent_at'         => now(),
                                'created_at'      => now(),
                                'updated_at'      => now()
                            ]);
                        } catch (\Exception $e) {
                            DB::table('emails')->insert([
                                'complaintID'     => $complaint->complaintID,
                                'recipient_email' => $officer->email,
                                'subject'         => $subject,
                                'sent_status' => 'Failed',
                        'sent_at' => null,
                                'email_type'      => 'Complaint Rejection (Police)',
                                'created_at'      => now(),
                                'updated_at'      => now()
                            ]);
                        }
                    }
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Complaint rejected successfully. The Tourist and Assigned Officer have been notified.'
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to complete rejection flow.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    // public function rejectComplaint(Request $request, $id)
    // {
    //     $request->validate([
    //         'rejection_reason' => 'required|string|max:1000'
    //     ]);

    //     $complaint = Complaint::with('tourist')->find($id);

    //     if (!$complaint) {
    //         return response()->json(['message' => 'Complaint not found'], 404);
    //     }

    //     try {
    //         DB::beginTransaction();

    //         $complaint->status = 'Rejected';
    //         $complaint->police_note = $request->rejection_reason;
    //         $complaint->save();

    //         DB::table('tourist_notifications')->insert([
    //             'touristID'   => $complaint->touristID,
    //             'title'       => "Complaint CMP-{$complaint->complaintID} Rejected",
    //             'message'     => "Your complaint has been rejected. Reason: " . $request->rejection_reason,
    //             'is_read'     => false,
    //             'created_at'  => now(),
    //             'updated_at'  => now()
    //         ]);

    //         $emailSent = false;
    //         if ($complaint->tourist && $complaint->tourist->email) {
    //             Mail::to($complaint->tourist->email)->send(
    //                 new ComplaintRejectedMail($complaint, $request->rejection_reason)
    //             );
    //             $emailSent = true;
    //         }

    //         DB::table('emails')->insert([
    //             'complaintID'     => $complaint->complaintID,
    //             'recipient_email' => $complaint->tourist->email ?? 'unknown@tourist.com',
    //             'subject'         => "Update Regarding Your Complaint: CMP-{$complaint->complaintID}",
    //             'sent_status'     => $emailSent ? 'Sent' : 'Failed',
    //             'email_type'      => 'Complaint Rejection',
    //             'sent_at'         => now(),
    //             'created_at'      => now(),
    //             'updated_at'      => now()
    //         ]);

    //         DB::commit();

    //         return response()->json([
    //             'message' => 'Complaint rejected successfully, logged in email history, and tourist notified.'
    //         ], 200);

    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return response()->json([
    //             'message' => 'Failed to complete rejection flow.',
    //             'error'   => $e->getMessage()
    //         ], 500);
    //     }
    // }

}