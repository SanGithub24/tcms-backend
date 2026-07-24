<?php

namespace App\Mail;

use App\Models\Review;
use App\Models\Complaint;
use App\Models\Tourist;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewReviewSubmittedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Review $review;
    public Complaint $complaint;
    public Tourist $tourist;

    public function __construct(
        Review $review,
        Complaint $complaint,
        Tourist $tourist
    ) {
        $this->review = $review;
        $this->complaint = $complaint;
        $this->tourist = $tourist;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "New Tourist Review - CMP{$this->complaint->complaintID}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.new_review_submitted',
        );
    }
}