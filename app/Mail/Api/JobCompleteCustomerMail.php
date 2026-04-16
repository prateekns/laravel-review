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

class JobCompleteCustomerMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        protected WorkOrder $workOrder,
        protected $jobCompletedAt,
        protected $businessLogoUrl,
        protected $chemicalLogs,
        protected ?string $customer_message = '',
        protected ?array $attachment = []
    ) {
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = 'Job Completion Update – ' . $this->workOrder->type . $this->workOrder->id . ' – From ' . $this->workOrder->business->name;
        return new Envelope(
            subject: $subject,
            from: new Address(config('app.contact.email'), config('app.name')),
            replyTo: [
                new Address($this->workOrder->business->email, $this->workOrder->business->name),
            ],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.api.job-complete-customer',
            with: [
                'customer_message' => $this->customer_message,
                'workOrder' => $this->workOrder,
                'attachment' => $this->attachment,
                'jobCompletedAt' => $this->jobCompletedAt,
                'businessLogoUrl' => $this->businessLogoUrl,
                'chemicalLogs' => $this->chemicalLogs
            ],
        );
    }
}
