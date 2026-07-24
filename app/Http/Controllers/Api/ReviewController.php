<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Complaint;
use App\Models\Notification;
use App\Models\User;
use App\Models\Email;
use App\Mail\NewReviewSubmittedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'complaintID' => 'required|integer|exists:complaints,complaintID',
            'touristID' => 'required|integer|exists:tourists,touristID',
            'rating' => 'required|integer|min:1|max:5',
            'description' => 'required|string|max:2000',
        ]);

        $complaint = Complaint::find($validated['complaintID']);

        if ((int) $complaint->touristID !== (int) $validated['touristID']) {
            return response()->json([
                'success' => false,
                'message' => 'You are not allowed to review this complaint.',
            ], 403);
        }

        if (strtolower(trim($complaint->status)) !== 'resolved') {
            return response()->json([
                'success' => false,
                'message' => 'You can only review a resolved complaint.',
            ], 422);
        }

        $existingReview = Review::where(
            'complaintID',
            $validated['complaintID']
        )->first();

        if ($existingReview) {
            return response()->json([
                'success' => false,
                'message' => 'A review has already been submitted for this complaint.',
            ], 409);
        }

        $review = Review::create([
            'complaintID' => $validated['complaintID'],
            'touristID' => $validated['touristID'],
            'rating' => $validated['rating'],
            'description' => $validated['description'],
            'status' => 'Active',
        ]);

        $tourist = $review->tourist;

        $admins = User::where('user_type', 'admin')
            ->where('status', 'Active')
            ->get();

        foreach ($admins as $admin) {

            Notification::create([
                'userID' => $admin->userID,
                'complaintID' => $complaint->complaintID,
                'title' => 'New Tourist Review',
                'message' => 'A new review has been submitted for complaint CMP'
                    . $complaint->complaintID . '.',
                'is_read' => false,
            ]);

            if (!empty($admin->email)) {

                $subject = 'New Tourist Review - CMP'
                    . $complaint->complaintID;

                try {

                    Mail::to($admin->email)->send(
                        new NewReviewSubmittedMail(
                            $review,
                            $complaint,
                            $tourist
                        )
                    );

                    Email::create([
                        'complaintID' => $complaint->complaintID,
                        'recipient_email' => $admin->email,
                        'subject' => $subject,
                        'sent_status' => 'Sent',
                        'sent_at' => now(),
                        'email_type' => 'New Review Submitted',
                        'sent_at' => now(),
                    ]);

                } catch (\Exception $e) {

                    Email::create([
                        'complaintID' => $complaint->complaintID,
                        'recipient_email' => $admin->email,
                        'subject' => $subject,
                        'sent_status' => 'Failed',
                        'sent_at' => null,
                        'email_type' => 'New Review Submitted',
                        'sent_at' => null,
                    ]);

                    Log::error(
                        'Failed to send new review email to admin '
                        . $admin->userID
                        . ': '
                        . $e->getMessage()
                    );
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Review submitted successfully.',
            'review' => $review,
        ], 201);
    }

    public function index()
    {
        $reviews = Review::with([
            'tourist',
            'complaint',
        ])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'reviews' => $reviews,
        ]);
    }

    public function show($reviewID)
    {
        $review = Review::with([
            'tourist',
            'complaint.location',
            'complaint.assignments.officer',
        ])->find($reviewID);

        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Review not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'review' => $review,
        ]);
    }

    public function reject($reviewID)
    {
        $review = Review::find($reviewID);

        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Review not found.',
            ], 404);
        }

        if ($review->status === 'Rejected') {
            return response()->json([
                'success' => false,
                'message' => 'This review has already been rejected.',
            ], 422);
        }

        $review->status = 'Rejected';
        $review->save();

        return response()->json([
            'success' => true,
            'message' => 'Review rejected successfully.',
            'review' => $review,
        ]);
    }

    public function publicReviews()
    {
        $reviews = Review::with([
            'tourist',
        ])
            ->where('status', 'Active')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'reviews' => $reviews,
        ]);
    }

    public function getByComplaint($complaintID)
    {
        $review = Review::where('complaintID', $complaintID)->first();

        return response()->json([
            'success' => true,
            'has_review' => $review !== null,
            'review' => $review,
        ]);
    }
}