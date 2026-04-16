<?php

namespace App\Mail\Subscription;

use App\Models\Business\Business;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionCancelledMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public array $subscription, public Business $business)
    {
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Subscription Has Been Cancelled',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.subscription.cancelled',
            with: [
                'customerName' => $this->business->name ?? 'Valued Customer',
                'subscriptionId' => $this->subscription['id'],
                'cancelDate' => date('F j, Y', $this->subscription['canceled_at']),
                'endDate' => date('F j, Y', $this->subscription['cancel_at']),
                'cancelAfterPeriodEnd' => $this->subscription['cancel_at_period_end'],
                'business' => $this->business,
            ],
        );
    }
}
