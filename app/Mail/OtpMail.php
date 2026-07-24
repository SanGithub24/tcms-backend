<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class OtpMail extends Mailable
{
    public int $otp;

    public function __construct($otp)
    {
        $this->otp = $otp;
    }

    public function build()
    {
        return $this->subject('Your TCMS Verification OTP')
                    ->view('emails.otp', ['otp' => $this->otp]);
    }
}