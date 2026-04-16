<?php

namespace App\Mail\Business;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Business\Business;
use Illuminate\Contracts\Queue\ShouldQueue;

class BusinessFeedbackMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Business $business,
        public string $feedbackMessage
    ) {
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Feedback Received from ' . $this->business->name,
            from: $this->business->email,
            replyTo: [$this->business->email],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.business.feedback',
            with: [
                'business' => $this->business,
                'feedbackMessage' => $this->feedbackMessage,
            ],
        );
    }
}
