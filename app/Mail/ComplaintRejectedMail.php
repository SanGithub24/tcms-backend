<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ComplaintRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $complaint;
    public $reason;

    public function __construct($complaint, $reason)
    {
        $this->complaint = $complaint;
        $this->reason = $reason;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Update Regarding Your Complaint: CMP-{$this->complaint->complaintID}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.complaint_rejected',
        );
    }
}