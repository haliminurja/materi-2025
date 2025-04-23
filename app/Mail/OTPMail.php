<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;;
use Illuminate\Queue\SerializesModels;

class OTPMail extends Mailable
{
    use SerializesModels;

    private string $otp;

    public function __construct(string $otp)
    {
        $this->otp = $otp;
    }

    public function build(): self
    {
        return $this->subject('OTP')
            ->view('email.otp')
            ->with([
                'otp' => $this->otp,
            ]);
    }
}
