<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class TestMail extends Mailable
{
    public function build()
    {
        return $this->subject('TCMS Test Email')
                    ->html('<h2>Email Sending Works!</h2><p>Your Laravel Gmail SMTP is configured successfully.</p>');
    }
}