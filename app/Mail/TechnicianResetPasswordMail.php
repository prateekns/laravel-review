<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TechnicianResetPasswordMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public $resetUrl;

    public function __construct($resetUrl)
    {
        $this->resetUrl = $resetUrl;
    }

    public function build()
    {
        return $this->subject('Reset Your Technician Password')
            ->view('emails.technician_reset_password')
            ->with(['resetUrl' => $this->resetUrl]);
    }
}
