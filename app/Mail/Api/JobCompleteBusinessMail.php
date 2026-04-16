<?php

namespace App\Mail\Api;

use App\Models\Business\WorkOrder\WorkOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Database\Eloquent\Collection;

class JobCompleteBusinessMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        protected WorkOrder $workOrder,
        protected Collection $itemSold,
        protected string $jobCompletedAt,
        protected ?string $message_business = '',
        protected ?array $attachment = [],
        protected ?string $extra_work = '',
    ) {
    }


    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $techEmail = $this->workOrder->technician->email;
        $techName = $this->workOrder->technician->fullName;
        $subject = 'Field Notes – ' . $this->workOrder->type . $this->workOrder->id . ' – From ' . $techName;
        return new Envelope(
            subject: $subject,
            from: new Address(config('app.contact.email'), config('app.name')),
            replyTo: [
                new Address($techEmail, $techName),
            ],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.api.job-complete-business',
            with: [
                'message_business' => $this->message_business,
                'workOrder' => $this->workOrder,
                'attachment' => $this->attachment,
                'extra_work' => $this->extra_work,
                'itemSold' => $this->itemSold,
                'jobCompletedAt' => $this->jobCompletedAt
            ],
        );
    }
}
