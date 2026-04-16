<?php

namespace App\Mail\Subscription;

use App\Models\Business\Business;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoicePaidMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public array $invoice, public Business $business)
    {
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Invoice Payment Successful',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.subscription.invoice_paid',
            with: [
                'customerName' => $this->invoice['customer_name'] ?? 'Valued Customer',
                'invoiceNumber' => $this->invoice['number'],
                'amountPaid' => number_format($this->invoice['amount_paid'] / 100, 2),
                'currency' => strtoupper($this->invoice['currency']),
                'invoicePdf' => $this->invoice['invoice_pdf'],
                'hostedInvoiceUrl' => $this->invoice['hosted_invoice_url'],
            ],
        );
    }
}
