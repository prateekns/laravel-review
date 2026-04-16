<?php

namespace App\Mail\Subscription;

use App\Helpers\Helper;
use App\Models\Business\Business;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionCreatedMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public array $payload, public Business $business)
    {
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Subscription is Active!',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.subscription.created',
            with: [
                'customerName' => $this->business->name ?? 'Valued Customer',
                'amount_paid' => Helper::getFormattedAmountWithCurrency($this->payload['amount_paid']),
                'business' => $this->business,
            ],
        );
    }
}
