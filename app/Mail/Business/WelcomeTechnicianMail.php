<?php

namespace App\Mail\Business;

use Illuminate\Mail\Mailable;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Business\Technician\Technician;
use Illuminate\Contracts\Queue\ShouldQueue;

class WelcomeTechnicianMail extends Mailable implements ShouldQueue
{
    use SerializesModels;
    use Queueable;

    /**
     * The technician data.
     *
     * @var Technician
     */
    public $technician;

    /**
     * The password for the technician.
     *
     * @var string
     */
    public $password;

    /**
     * Create a new message instance.
     */
    public function __construct(Technician $technician, string $password)
    {
        $this->technician = $technician;
        $this->password = $password;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to ' . config('app.name') . " - Your Technician Account Details",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.business.technician-welcome',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
