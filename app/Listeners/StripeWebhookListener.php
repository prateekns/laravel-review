<?php

namespace App\Listeners;

use Laravel\Cashier\Events\WebhookReceived;
use App\Models\Business\Invoice;
use App\Models\Business\Order;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\Business\Business;
use App\Mail\Subscription\SubscriptionCreatedMail;
use App\Mail\Subscription\InvoicePaidMail;
use App\Mail\Subscription\SubscriptionCancelledMail;
use App\Mail\Subscription\SubscriptionUpdatedMail;
use Illuminate\Support\Facades\Mail;
use Exception;

class StripeWebhookListener
{
    /**
     * Priority levels for different invoice statuses
     *
     * @var array
     */
    protected array $statusPriority = [
        'draft' => 0,
        'open' => 1,
        'paid' => 3,
        'uncollectible' => 4,
        'void' => 5,
    ];

    /**
     * Error message for when a business is not found by Stripe customer ID
     *
     * @var string
     */
    private const ERROR_BUSINESS_NOT_FOUND = 'Business not found for %s event. Stripe Customer ID: %s';
    private const ORDER_NOT_FOUND = 'Order not found for %s event. Stripe Customer ID: %s';
    private const RECURRING = 'recurring';
    private const CANCELLED = 'cancelled';
    private const PENDING = 'pending';

    /**
     * Handle received Stripe webhooks.
     */
    public function handle(WebhookReceived $event): void
    {
        $events = [
             'invoice.payment_succeeded',
             'invoice.voided',
             'invoice.payment_failed',
             'invoice.payment_action_required',
             'invoice.deleted'
        ];

        if ($event->payload['type'] === 'customer.subscription.deleted') {
            $this->handleSubscriptionDeleted($event->payload);
        }

        if ($event->payload['type'] === 'invoice.created' || $event->payload['type'] === 'invoice.upcoming') {
            $this->storeInvoice($event->payload);
        }

        if (in_array($event->payload['type'], $events)) {
            $this->handleInvoiceEvents($event->payload);
        }

        if ($event->payload['type'] === 'customer.subscription.updated') {
            $this->handleSubscriptionUpdated($event->payload);
        }
    }

    /**
     * Get the order for the business.
     *
     * @param int $business_id
     * @param string $status
     * @return Order|null
     */
    public function getOrder(int $business_id, string $status): Order|null
    {
        return Order::where('business_id', $business_id)
            ->where('status', $status)
            ->latest()
            ->first();
    }


    /**
     * Handle the invoice.created webhook
     */
    public function storeInvoice($payload)
    {
        try {
            $invoiceData = $payload['data']['object'];
            $business = Business::where('stripe_id', $invoiceData['customer'])->first();

            if (!$business) {
                Log::error(sprintf(self::ERROR_BUSINESS_NOT_FOUND, 'INVOICE_CREATE', $invoiceData['customer']));
                return;
            }

            $this->createOrUpdateInvoice($invoiceData, $business);

        } catch (Exception $e) {
            Log::error('Error creating invoice record: ' . $e->getMessage());
        }
    }

    /**
     * Create or update the invoice record
     *
     * @param array $invoiceData
     * @param Business $business
     * @return void
     */
    protected function createOrUpdateInvoice(array $invoiceData, Business $business): Invoice
    {
        try {

            $subscription = $business->subscription('default');
            $startDate = $invoiceData['lines']['data'][0]['period']['start'];
            $endDate = $invoiceData['lines']['data'][0]['period']['end'];
            $orderId = $invoiceData['parent']['subscription_details']['metadata']['order_id'] ?? null;

            return Invoice::updateOrCreate(
                ['invoice_id' => $invoiceData['id']],
                [
                    'business_id' => $business->id,
                    'invoice_number' => $invoiceData['number'],
                    'customer_id' => $invoiceData['customer'],
                    'subscription_id' => $invoiceData['subscription'] ?? $subscription->stripe_id,
                    'amount_due' => $invoiceData['amount_due'],
                    'amount_paid' => $invoiceData['amount_paid'],
                    'total' => $invoiceData['subtotal'],
                    'description' => $invoiceData['description'],
                    'billing_reason' => $invoiceData['billing_reason'],
                    'currency' => $invoiceData['currency'],
                    'invoice_status' => $invoiceData['status'],
                    'invoice_url' => $invoiceData['hosted_invoice_url'],
                    'invoice_pdf' => $invoiceData['invoice_pdf'],
                    'order_id' => $orderId,
                    'period_start' => $invoiceData['period_start'] ? Carbon::createFromTimestamp($startDate) : null,
                    'period_end' => $invoiceData['period_end'] ? Carbon::createFromTimestamp($endDate) : null,
                    'created' => $invoiceData['created'] ? Carbon::createFromTimestamp($invoiceData['created']) : null,

                ]
            );

        } catch (Exception $e) {
            Log::error('Error creating invoice record: ', [
                'exception' => $e->getMessage(),
                'line' => $e->getLine(),
                'business_id' => $business->id,
                'invoice_id' => $invoiceData['id'],
            ]);
        }
    }

    /**
     * Handle the invoice.paid webhook
     * @param array $payload
     * @return void
     */
    protected function handleInvoiceEvents(array $payload): void
    {
        try {

            $invoiceData = $payload['data']['object'];
            $business = Business::where('stripe_id', $invoiceData['customer'])->first();

            if (!$business) {
                Log::error(sprintf(self::ERROR_BUSINESS_NOT_FOUND, 'invoice paid', $invoiceData['customer']));
                return;
            }

            // Update the invoice status
            $invoice = Invoice::where('invoice_id', $invoiceData['id'])->first();
            if ($invoice) {
                $updateInvoice = $this->statusPriority[$invoiceData['status']] >= $this->statusPriority[$invoice->invoice_status];

                if ($updateInvoice) {
                    $invoice->update([
                        'invoice_number' => $invoiceData['number'],
                        'invoice_status' => $invoiceData['status'],
                        'amount_due' => $invoiceData['amount_due'],
                        'amount_paid' => $invoiceData['amount_paid'],
                        'invoice_url' => $invoiceData['hosted_invoice_url'],
                        'invoice_pdf' => $invoiceData['invoice_pdf'],
                    ]);
                }

            } else {
                $invoice = $this->createOrUpdateInvoice($invoiceData, $business);
            }

            // Send notification to Business
            if ($payload['type'] === 'invoice.payment_succeeded' && $invoice) {
                match ($invoiceData['billing_reason']) {
                    'subscription_create' => $this->invoicePaid($payload),
                    'subscription_update' => $this->invoicePaid($payload),
                    'subscription_cycle' => $this->handleSubscriptionCycle($invoiceData, $business, $invoice),
                    default => null,
                };
            } else {
                Log::error('Invoice not found/created: ', [
                    'invoice_id' => $invoiceData['id'],
                    'business_id' => $business->id,
                ]);
            }

        } catch (Exception $e) {
            Log::error('Error handling ' . $payload['type'] . ' webhook: ' . $e->getMessage());
        }
    }


    /**
     * Handle the subscription cycle webhook
     *
     * @param array $invoiceData
     * @param Business $business
     * @param Invoice $invoice
     * @return void
     */
    protected function handleSubscriptionCycle(array $invoiceData, Business $business, Invoice $invoice): void
    {
        try {
            $order = $this->getOrder($business->id, Order::STATUS_COMPLETED);
            if ($order) {
                $invoice->update(['order_id' => $order->id]);
            }

            // Update the credit balance if any
            Business::where('id', $business->id)->update(['credit_balance' => abs($invoiceData['ending_balance'])]);
            Invoice::where('invoice_id', $invoiceData['id'])->update(['invoice_type' => self::RECURRING]);

            $this->storeNextBillingData($business);

            try {
                Mail::to($business->email)->send(new InvoicePaidMail($invoiceData, $business));
            } catch (Exception $e) {
                Log::error('Error sending invoice paid email: ', [
                    'exception' => $e->getMessage(),
                    'line' => $e->getLine(),
                    'business_id' => $business->id,
                ]);
            }

        } catch (Exception $e) {
            Log::error('Error handling subscription cycle webhook: ', [
                'exception' => $e->getMessage(),
                'line' => $e->getLine()
            ]);
        }
    }


    /**
     * Handle the invoice.paid webhook
     *
     * @param array $payload
     * @return void
     */
    protected function invoicePaid(array $payload): void
    {
        try {
            $invoice = $payload['data']['object'];
            $business = Business::where('stripe_id', $invoice['customer'])->first();
            if (!$business) {
                Log::error(sprintf(self::ERROR_BUSINESS_NOT_FOUND, 'invoice paid', $invoice['customer']));
                return;
            }

            // Store the next billing data
            $this->storeNextBillingData($business);

            // Get the order
            $order = $this->getOrder($business->id, Order::STATUS_PENDING);

            if ($order) {
                // Update the business data - number of admin and technician after subscription update, credit balance if any
                $userData = ['num_admin' => $order->total_admin,'num_technician' => $order->total_technician,'credit_balance' => abs($invoice['ending_balance'])];
                if ($business->trial_end_at?->isFuture()) {
                    $userData['trial_end_at'] = now();
                }
                Business::where('id', $business->id)->update($userData);
                Invoice::where('invoice_id', $invoice['id'])->update(['order_id' => $order->id,'invoice_type' => $order->order_type]);
                $prorationAmt = [];
                if ($order->order_type === Order::ORDER_TYPE_UPGRADE || $order->order_type === Order::ORDER_TYPE_DOWNGRADE) {
                    $prorationAmt['proration_amt'] = $invoice['total'];
                    $prorationAmt['total_price'] = $invoice['total'] / 100;
                }
                Order::where('id', $order->id)->update(array_merge(['status' => Order::STATUS_COMPLETED], $prorationAmt));

                $business->refresh();

                // Send notification to Business for subscription created or updated
                try {
                    match ($invoice['billing_reason']) {
                        'subscription_create' => Mail::to($business->email)->send(new SubscriptionCreatedMail($invoice, $business)),
                        'subscription_update' => Mail::to($business->email)->send(new SubscriptionUpdatedMail($invoice, $business)),
                        default => null,
                    };
                } catch (Exception $e) {
                    Log::error('Error sending invoice paid email: ', [
                        'exception' => $e->getMessage(),
                        'line' => $e->getLine(),
                        'business_id' => $business->id,
                    ]);
                }

            } else {
                Log::error(sprintf(self::ORDER_NOT_FOUND, 'INVOICE_PAID', $invoice['customer']));
            }

        } catch (Exception $e) {
            Log::error('Error handling subscription created webhook: ', [
                'customer' => $invoice['customer'],
                'exception' => $e->getMessage(),
                'line' => $e->getLine()
            ]);
        }
    }

    /**
     * Handle the subscription deleted webhook
     *
     * @param array $payload
     * @return void
     */
    protected function handleSubscriptionDeleted(array $payload): void
    {
        try {
            $subscriptionData = $payload['data']['object'];
            $business = Business::where('stripe_id', $subscriptionData['customer'])->first();
            if (!$business) {
                Log::error(sprintf(self::ERROR_BUSINESS_NOT_FOUND, 'subscription deleted', $subscriptionData['customer']));
                return;
            }

            $updatedBusiness = Business::where('id', $business->id)->update([
                'num_admin' => 0,
                'num_technician' => 0
            ]);

            $business->technicians()->update(['status' => 0]);
            $business->subAdmins()->update(['status' => 0]);

            // Cancel the pending order
            $order = $this->getOrder($business->id, self::PENDING);
            if ($order) {
                Order::where('business_id', $business->id)
                    ->where('status', self::PENDING)
                    ->update(['status' => self::CANCELLED]);
            }

            if ($updatedBusiness) {
                Mail::to($business->email)->send(new SubscriptionCancelledMail($subscriptionData, $business));
            }

        } catch (Exception $e) {
            Log::error('Error handling subscription deleted webhook: ', [
                'customer' => $subscriptionData['customer'],
                'exception' => $e->getMessage(),
                'line' => $e->getLine()
            ]);
        }
    }

    /**
     * Handle the subscription updated webhook
     *
     * @param array $payload
     * @return void
     */
    protected function handleSubscriptionUpdated(array $payload): void
    {
        try {

            $subscriptionData = $payload['data']['object'];

            $business = Business::where('stripe_id', $subscriptionData['customer'])->first();
            if (!$business) {
                Log::error(sprintf(self::ERROR_BUSINESS_NOT_FOUND, 'subscription updated', $subscriptionData['customer']));
                return;
            }

            if ($subscriptionData['cancel_at_period_end']) {
                // Inform business that their subscription is cancelled
                Mail::to($business->email)->send(new SubscriptionCancelledMail($subscriptionData, $business));

            } elseif ($subscriptionData['cancel_at'] && !$subscriptionData['cancel_at_period_end']) {
                // Inform business that their subscription is cancelled
                Mail::to($business->email)->send(new SubscriptionCancelledMail($subscriptionData, $business));

            } elseif ($subscriptionData['status'] === 'past_due') {
                //Deactivate all Admin and technician users
                try {
                    $business->technicians()->update(['status' => 0]);
                    $business->subAdmins()->update(['status' => 0]);
                } catch (Exception $e) {
                    Log::error('Error deactivating technicians and admins: ' . $e->getMessage());
                }
            }

        } catch (Exception $e) {
            Log::error('Error handling subscription updated webhook: ', [
                'customer' => $subscriptionData['customer'],
                'exception' => $e->getMessage(),
                'line' => $e->getLine()
            ]);
        }
    }

    /**
     * Set the next billing date
     *
     * @param Business $business
     * @return void
     */
    protected function storeNextBillingData(Business $business): void
    {
        try {
            $subscription = $business->subscription('default');
            $invoice = $subscription->upcomingInvoice();
            $nextPaymentDate = Carbon::createFromTimestamp($invoice->next_payment_attempt);
            $subscription->update([
                'amount_due' => $invoice->amount_due,
                'next_billing_date' => $nextPaymentDate,
                ]);
        } catch (Exception $e) {
            Log::error('Error updating upcoming invoice details in subscription cycle webhook: ', [
                'exception' => $e->getMessage(),
                'line' => $e->getLine(),
                'business_id' => $business->id,
            ]);
        }
    }
}
