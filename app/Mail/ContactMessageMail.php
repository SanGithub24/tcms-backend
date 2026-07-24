<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class ContactMessageMail extends Mailable
{
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build()
    {
        return $this->subject('New Tourist Contact Message')
            ->view('emails.contact_message', ['data' => $this->data]);
    }
}