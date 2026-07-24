<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notice;
use App\Models\TouristNotification;
use App\Models\Tourist;

class NoticeController extends Controller
{
    public function getAllNotices()
    {
        return Notice::with('user')->latest()->get();
    }

    public function createNotice(Request $request)
    {
        try {

            $request->validate([
                'title' => 'required|string|min:5|max:50',
                'description' => 'required|string|min:5|max:1000',
                'category' => 'required',
                'priority' => 'required',
                'location' => 'nullable|string|max:50',
                'expires_at' => 'required|date|after_or_equal:today',
                'userID' => 'required'
            ]);

            $notice = Notice::create([
                'title' => $request->title,
                'description' => $request->description,
                'category' => $request->category,
                'priority' => $request->priority,
                'location' => $request->location,
                'is_featured' => $request->is_featured,
                'expires_at' => $request->expires_at,
                // 'image_url' => $request->image_url,
                'image' => $request->image_url,
                'created_by' => $request->userID,
                'status' => 'Published'
            ]);

            $tourists = Tourist::all();

            foreach ($tourists as $tourist) {

                TouristNotification::create([
                    'touristID' => $tourist->touristID,
                    'title' => 'New Tourist Notice',
                    'message' => $notice->title,
                    'type' => 'notice'
                ]);
            }

            return response()->json([
                'message' => 'Notice created',
                'notice' => $notice
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getPublishedNotices()
    {
        $today = now()->toDateString();

        return Notice::where('status', 'Published')
            ->where(function ($query) use ($today) {
                $query->whereNull('expires_at')
                    ->orWhereDate('expires_at', '>=', $today);
            })
            ->latest()
            ->get();
    }

    // public function createNotice(Request $request)
    // {
    //     $notice = Notice::create([
    //         'title' => $request->title,
    //         'description' => $request->description,
    //         'category' => $request->category,
    //         'priority' => $request->priority,
    //         'location' => $request->location,
    //         'is_featured' => $request->is_featured,
    //         'expires_at' => $request->expires_at,
    //         'image_url' => $request->image_url,
    //         'created_by' => $request->userID,
    //         'status' => 'Published'
    //     ]);

    //     $tourists = Tourist::all();

    //     foreach ($tourists as $tourist) {

    //         TouristNotification::create([
    //             'touristID' => $tourist->touristID,
    //             'title' => 'New Tourist Notice',
    //             'message' => $notice->title,
    //             'type' => 'notice'
    //         ]);
    //     }

    //     return response()->json([
    //         'message' => 'Notice created',
    //         'notice' => $notice
    //     ]);
    // }

    public function updateNotice(Request $request, int $id)
    {
        $notice = Notice::find($id);

        if (!$notice) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $notice->update([
            'title' => $request->title,
            'description' => $request->description,
            'category' => $request->category,
            'priority' => $request->priority,
            'location' => $request->location,
            'expires_at' => $request->expires_at,
            'is_featured' => $request->is_featured,
        ]);

        return response()->json([
            'message' => 'Notice updated successfully',
            'notice' => $notice
        ]);
    }

    public function deactivateNotice(int $id)
    {
        $notice = Notice::findOrFail($id);

        $notice->status = "Inactive";
        $notice->save();

        return response()->json([
            "message" => "Notice deactivated successfully."
        ]);
    }

    public function republishNotice(int $id)
    {
        $notice = Notice::findOrFail($id);

        $notice->status = "Published";

        if ($notice->expires_at) {
            $notice->expires_at = now()->addDays(7);
        }

        $notice->save();

        return response()->json([
            "message" => "Notice republished successfully."
        ]);
    }

    public function rejectNotice(int $id)
    {
        try {
            $notice = Notice::with('user')->findOrFail($id);
            $notice->status = 'Rejected';
            $notice->save();

            // Send Email to the Police Officer
            if ($notice->user) {
                $subject = "TCMS: Notice Rejected";
                \Illuminate\Support\Facades\Mail::send('emails.notice_rejected', [
                    'user' => $notice->user,
                    'notice' => $notice
                ], function ($message) use ($notice, $subject) {
                    $message->to($notice->user->email)->subject($subject);
                });

                \App\Models\Email::create([
                    'recipient_email' => $notice->user->email,
                    'subject' => $subject,
                    'sent_status' => 'Sent',
                        'sent_at' => now(),
                    'email_type' => 'Notice Rejected',
                ]);
            }

            return response()->json([
                'message' => 'Notice rejected successfully',
                'notice' => $notice
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to reject notice: ' . $e->getMessage()
            ], 500);
        }
    }
}