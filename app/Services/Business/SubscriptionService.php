<?php
declare(strict_types=1);
namespace App\Services\Business;

use App\Exceptions\PriceException;
use App\Helpers\Helper;
use App\Models\Business\Business;
use App\Models\Business\BusinessUser;
use App\Models\Business\Invoice;
use App\Models\Business\Order;
use App\Models\Shared\Price;
use App\Models\Shared\Setting;
use App\Services\Business\Payment\Stripe as StripePayment;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Exceptions\IncompletePayment;
use Stripe\Exception\ApiErrorException;
use Stripe\Exception\CardException;
use Stripe\Exception\InvalidRequestException;
use Stripe\Exception\SubcriptionNotFoundException;
use Stripe\Exception\ItemsNotFoundException;
use Stripe\Stripe;
use Stripe\Subscription;
use Illuminate\Http\JsonResponse;
use InvalidArgumentException;
use Illuminate\Support\Str;
use App\Exceptions\TeamCountException;
use App\Models\Business\Technician\Technician;

/**
 * Subscription Service
 *
 * Handles all subscription-related operations including creation, updates, cancellations,
 * and payment processing for business subscriptions.
 *
 * @property-read \App\Models\Business\User $user
 * @property-read \App\Models\Shared\Setting $setting
 * @property-read \App\Models\Business\Business $business
 * @property-read \App\Models\Business\Subscription $subscription
 * @property-read \App\Models\Business\Order|null $order
 * @property-read bool $isSubscribed
 */
class SubscriptionService
{
    private const ADMIN = 'admin';

    private const TECHNICIAN = 'technician';

    private const DEFAULT_SUBSCRIPTION = 'default';

    private const GUARD = 'business';

    private const STATUS_PAID = 'paid';

    private const STATUS_ACTIVE = 'active';

    private const NO_DECIMAL = 0;

    private const MONTHS_IN_HALF_YEAR = 6;
    private const MONTHS_IN_YEAR = 12;
    private const HALF_YEARLY = 'half-yearly';
    private const MONTHLY = 'monthly';


    /**
     * Constructor.
     * @param StripePayment $stripe
     * @param PricingService $pricingService
     */
    public function __construct(
        private readonly StripePayment $stripe,
        private readonly PricingService $pricingService,
        private readonly BusinessService $businessService
    ) {
        $this->initializeProperties();
    }

    /**
     * Initialize Subscription Service properties
     */
    private function initializeProperties(): void
    {
        $this->user = Auth::guard(self::GUARD)->user();
        $this->business = $this->user->business;
        $this->isSubscribed = $this->business->subscribed(self::DEFAULT_SUBSCRIPTION);
        $this->subscription = $this->business->subscription(self::DEFAULT_SUBSCRIPTION);
        $this->prices = $this->pricingService->getPrices();
        $this->setting = Setting::first();
        $this->setOrder();
    }

    /**
     * Check if Stripe is properly configured
     */
    public function isStripeConfigured(): bool
    {
        return !empty(config('cashier.key')) && !empty(config('cashier.secret'));
    }

    /**
     * Check if valid product IDs exist
     */
    private function hasValidProductIds(): bool
    {
        return $this->setting?->admin_product_id && $this->setting?->technician_product_id;
    }

    /**
     * Check if both product types exist
     */
    private function hasBothProductTypes($grouped): bool
    {
        return isset($grouped[self::ADMIN]) && isset($grouped[self::TECHNICIAN]);
    }

    /**
     * Find common intervals between product types
     */
    private function findCommonIntervals($grouped): array
    {
        $adminIntervals = $grouped[self::ADMIN]->pluck('interval')->unique()->toArray();
        $technicianIntervals = $grouped[self::TECHNICIAN]->pluck('interval')->unique()->toArray();

        return array_intersect($adminIntervals, $technicianIntervals);
    }

    /**
     * Check if common intervals exist between admin and technician products
     */
    private function hasCommonIntervals(): bool
    {
        $prices = $this->pricingService->getActivePrices();
        $grouped = $prices->groupBy('type');

        if (!$this->hasBothProductTypes($grouped)) {
            return false;
        }

        $commonIntervals = $this->findCommonIntervals($grouped);
        return !empty($commonIntervals);
    }

    /**
     * Check if the subscription can be upgraded.
     */
    public function canSubscribe(): bool
    {
        if (!$this->isStripeConfigured()) {
            return false;
        }

        if (!$this->hasValidProductIds()) {
            return false;
        }

        return $this->hasCommonIntervals();
    }

    /**
     * Check if the subscription can be upgraded.
     *
     * @return bool
     */
    public function canUpgradeSubscription(): bool
    {
        return $this->order !== null;
    }

    /**
     * Load the existing order session.
     */
    private function setOrder(): void
    {
        $this->order = Order::where('business_id', $this->business->id)
            ->where('status', Order::STATUS_PENDING)
            ->latest()
            ->first();
    }

    /**
     * Get the most recent order data.
     */
    public function getRecentOrder(): ?Order
    {
        return Order::where('business_id', $this->business->id)
            ->where('status', Order::STATUS_COMPLETED)
            ->latest()
            ->first();
    }

    /**
     * Get the order total price.
     */
    public function getOrderTotalPrice(): string
    {
        return $this->order->total_price;
    }

    public function getOrderTotalWithCurrency(): string
    {
        return Helper::getFormattedAmountWithCurrency($this->order->total_price, self::NO_DECIMAL);
    }

    public function getOrderTotalDecimalWithCurrency(): string
    {
        return Helper::getFormattedAmountWithCurrency($this->order->total_price);
    }

    /**
     * Get the order by payment uuid.
     * @param string $payment_uuid
     * @return Order|null
     */
    public function getOrderByPaymentUuid(string $payment_uuid): ?Order
    {
        return Order::where('payment_uuid', $payment_uuid)->first();
    }

    /**
     * Check for existing pending checkout session and update if needed.
     */
    private function handleExistingCheckoutSession(array $teamData, array $pricing, array $changedData): ?Order
    {
        $existingSession = Order::where('business_id', $this->business->id)
            ->where('status', Order::STATUS_PENDING)
            ->latest()
            ->first();

        if (! $existingSession) {
            return null;
        }

        // Check if admin or technician count has changed
        if ($existingSession->num_admin != $teamData['admin'] ||
            $existingSession->num_technician != $teamData['technician']) {

            // Update the existing session with new counts
            $total_price = ($teamData['admin']  * $pricing['monthly']['admin']['price']) +
                    ($teamData['technician'] * $pricing['monthly']['technician']['price']);

            $existingSession->update(array_merge($changedData, [
                'num_admin' => $teamData['admin'],
                'num_technician' => $teamData['technician'],
                'total_price' => $total_price,
            ]));

            return $existingSession;
        }

        return $existingSession;
    }

    /**
     * Calculate pricing of selected team and Store the team data in a temporary table to proceed with payment.
     */
    public function createOrder(array $orderData, bool $dowgrade = false): Order
    {
        try {
            // Validate the order data
            $this->validateOrderData($orderData);

            // Get the pricing
            $pricing = $this->pricingService->getPrices();

            // For downgrades, calculate the difference from current quantities
            if ($dowgrade) {
                $changedData = [
                 'admin_qty_change' => $this->business->num_admin - $orderData['admin'],
                 'technician_qty_change' => $this->business->num_technician - $orderData['technician'],
                 'total_admin' => $orderData['admin'],
                 'total_technician' => $orderData['technician'],
                 'order_type' => Order::ORDER_TYPE_DOWNGRADE,
                ];
            } else {
                $changedData = [
                    'admin_qty_change' => $orderData['admin'],
                    'technician_qty_change' => $orderData['technician'],
                    'total_admin' => $orderData['admin'] + $this->business->num_admin,
                    'total_technician' => $orderData['technician'] + $this->business->num_technician,
                    'order_type' => ($this->business->num_admin + $this->business->num_technician > 0) ? Order::ORDER_TYPE_UPGRADE : Order::ORDER_TYPE_CREATE,
                   ];
            }

            // Check for existing session first
            $existingSession = $this->handleExistingCheckoutSession($orderData, $pricing, $changedData);
            if ($existingSession) {
                return $existingSession;
            }


            $total_price = ($orderData['admin'] * $pricing['monthly']['admin']['price'])
                + ($orderData['technician'] * $pricing['monthly']['technician']['price']);

            $payment_uuid = Str::uuid();
            return Order::create(array_merge($changedData, [
                'payment_uuid' => $payment_uuid,
                'business_id' => $this->business->id,
                'business_user_id' => $this->user->id,
                'admin_price' => $pricing['monthly']['admin']['price'],
                'technician_price' => $pricing['monthly']['technician']['price'],
                'num_admin' => $orderData['admin'],
                'num_technician' => $orderData['technician'],
                'total_price' => $total_price,
            ]));

        } catch (Exception $e) {
            Log::error('Unable to create order: ', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Validate the order data.
     */
    public function validateOrderData(array $orderData): void
    {
        $isTrialActive = $this->businessService->isTrialActive();
        if ($isTrialActive) {
            $adminCount = $this->business->activeSubAdmins()->count();
            $technicianCount = $this->business->activeTechnicians()->count();

            if ($orderData['admin'] < $adminCount || $orderData['technician'] < $technicianCount) {
                throw new TeamCountException(trans('business.message.trial_team_count_error'));
            }
        }
    }

    /**
     * Get the subscription.
     */
    public function getSubscriptionDetails(): array
    {

        $recentOrder = $this->getRecentOrder();
        return [
            'user' => $this->user,
            'pricing' => $this->pricingService->getPrices(),
            'isSubscribed' => $this->isSubscribed,
            'subscription' => $this->subscription,
            'canSubscribe' => $this->canSubscribe(),
            'amountDue' => $this->subscription ? $this->formattedAmount($this->subscription?->amount_due ?? 0) : 0,
            'nextPayment' => $recentOrder ? $this->formattedAmount($recentOrder->next_payment) : 0,
            'creditBalance' => $this->subscription ? $this->formattedAmount($this->business->credit_balance) : 0,
            'nextPaymentDate' => $this->formatNextPaymentDate(),
            'trial_admin' => $this->setting->trial_admin ?? config('app.trial_admin'),
            'trial_technician' => $this->setting->trial_technician ?? config('app.trial_technician'),
            'trial_period' => $this->setting->trial_period ?? config('app.trial_period'),
            'pastDue' => $this->business->subscription('default')?->pastDue(),
        ];
    }

    /**
     * Format amount due for display
     */
    public function formattedAmount($amount = 0): ?string
    {

        return Helper::getFormattedAmountWithCurrency($amount ?? $this->subscription?->amount_due ?? 0);
    }

    /**
     * Format next payment date for display
     */
    private function formatNextPaymentDate(): ?string
    {
        return $this->subscription?->next_billing_date
            ? Helper::getFormattedDate($this->subscription->next_billing_date)
            : null;
    }

    /**
     * Create a new setup intent.
     *
     * @throws Exception
     */
    public function createSetupIntent()
    {
        try {
            $this->business->createOrGetStripeCustomer();
            return $this->business->createSetupIntent();
        } catch (InvalidRequestException $e) {
            return $this->handleStripeCustomerError($e);
        } catch (Exception $e) {
            Log::error('Stripe SetupIntent Creation Failed: ', [
                'error' => $e->getMessage(),
                'business_id' => $this->business->id
            ]);
            throw $e;
        }
    }

    /**
     * Handle Stripe customer error
     * @param InvalidRequestException $e
     * @return string
     * @throws Exception
     */
    private function handleStripeCustomerError(InvalidRequestException $e): string
    {
        try {
            // If the customer does not exist, create a new one
            if (str_contains($e->getMessage(), 'No such customer')) {
                $this->business->forceFill(['stripe_id' => null])->save();
                $this->business->createOrGetStripeCustomer();
                return $this->business->createSetupIntent();
            }
        } catch (Exception $e) {
            Log::error('Stripe customer error: ', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }

        return [];
    }

    /**
     * Update the payment method.
     *
     * @param  array  $requestData
     * @return JsonResponse
     */
    public function updatePaymentMethod($requestData): JsonResponse
    {
        try {
            $isExistingCard = $this->stripe->validatePaymentMethod($requestData['payment_method'], $this->business->stripe_id);
            if ($isExistingCard) {
                $paymentMethod = $isExistingCard->id;
                $this->business->updateDefaultPaymentMethod($paymentMethod);
                return response()->json(['success' => true, 'message' => __('business.message.payment_method_updated')]);
            }

            // Set the payment method as the default payment method
            $this->business->updateDefaultPaymentMethod($requestData['payment_method']);
            $this->business->refresh();
            $message = __('business.message.active_subscription_card_updated');
            if (!$this->business->subscribed('default')) {
                $message = __('business.message.inactive_subscription_card_updated');
            }
            return response()->json(['success' => true, 'pm_last_four' => $this->business->pm_last_four, 'message' => $message]);
        } catch (Exception $e) {
            Log::error('Payment method update error: '.$e->getMessage());
            return response()->json(['success' => false, 'message' => __('business.message.payment_method_updated_failed')]);
        }
    }

    /**
     * Cancel a business subscription.
     *
     */
    public function cancelSubscription()
    {
        try {
            if ($this->business->subscribed()) {
                $nextBillingDate = $this->business->subscription(self::DEFAULT_SUBSCRIPTION)->next_billing_date;
                $this->business->subscription(self::DEFAULT_SUBSCRIPTION)->cancel();
                return [
                    'success' => true,
                    'message' => __('business.message.subscription_cancel_success', ['date' => Helper::getFormattedDate($nextBillingDate)]),
                ];
            }

            return ['success' => false, 'message' => __('business.message.cancel_subscription_error')];

        } catch (Exception $e) {
            Log::error('Error canceling subscription: '.$e->getMessage());
            return ['success' => false, 'message' => __('business.message.cancel_subscription_error')];
        }
    }

    /**
     * Rollback the subscription.
     *
     * @return JsonResponse
     */
    public function rollbackSubscription(): JsonResponse
    {
        try {
            if ($this->business->subscriptions()->incomplete()->first()) {
                $this->business->subscriptions()->incomplete()->first()->cancelNow();
            }
            return response()->json(['success' => true]);
        } catch (Exception $e) {
            Log::error('Error Cancel Incomplete Subscription: '.$e->getMessage());
            return response()->json(['success' => false]);
        }
    }

    /**
     * Preview the upcoming invoice.
     * @return array
     */
    public function previewInvoice(bool $downgrade = false): array
    {
        if ($this->order) {
            $amount = $nextCycleAmount = 0;
            $subscription = $this->business->subscription(self::DEFAULT_SUBSCRIPTION);
            $items = $downgrade ? $this->getDowngradeItems() : $this->getSubscriptionItems();
            $invoice = $this->stripe->previewInvoice($items, $this->business->stripe_id, $subscription->stripe_id);

            foreach ($invoice->lines->data as $line) {
                if ($line->proration) {
                    $amount += $line->amount;
                    $startDate = Helper::getFormattedDate($line->period->start);
                    $endDate = Helper::getFormattedDate($line->period->end);

                } else {
                    $nextCycleAmount += $line->amount;
                    $nextCycleStartDate = Helper::getFormattedDate($line->period->start);
                    $nextCycleEndDate = Helper::getFormattedDate($line->period->end);
                }
            }

            Order::where('id', $this->order->id)
                ->update(['proration_amt' => $amount ,'total_price' => $amount / 100, 'next_payment' => $nextCycleAmount]);

            return [
                'proration_amount' => Helper::getFormattedAmount($amount),
                'next_cycle_amount' =>  Helper::getFormattedAmount($nextCycleAmount),
                'start_date' => $startDate ?? $nextCycleStartDate,
                'end_date' => $endDate ?? $nextCycleEndDate,
                'next_cycle_start_date' => $nextCycleStartDate ?? $startDate,
                'next_cycle_end_date' => $nextCycleEndDate ?? $endDate,
            ];
        }
    }

    /**
     * Process the payment and create or update the subscription.
     *
     * @param  array  $requestData
     * @return \Illuminate\Http\JsonResponse
     */
    public function processPayment($requestData): JsonResponse
    {
        try {
            $this->validatePaymentRequest($requestData);
            $paymentMethod = $requestData['payment_method'];

            $result = $this->business->subscribed(self::DEFAULT_SUBSCRIPTION)
                ? $this->updateSubscription($paymentMethod)
                : $this->createSubscription($requestData);

            return response()->json($result, 200);

        } catch (InvalidRequestException $e) {
            Log::error('Invalid Stripe Request: '.$e->getMessage());
            throw $e;
        } catch (IncompletePayment $e) {
            Log::error('Incomplete Payment Request: '.$e->getMessage());
            throw $e;
        } catch (CardException $e) {
            Log::error('Card Exception: '.$e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (ApiErrorException $e) {
            Log::error('API Error Exception: '.$e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (Exception $e) {
            Log::error('Payment Exception: '.$e->getMessage());
            throw $e;
        }
    }

    /**
     * Validate payment request
     */
    private function validatePaymentRequest(array $requestData): void
    {
        if (!isset($requestData['payment_method']) || empty($requestData['payment_method'])) {
            throw new InvalidArgumentException(trans('business.message.payment_method_required'));
        }

        if (!$this->order) {
            throw new PriceException(trans('payments.payment_session_invalid'));
        }
    }

    /**
     * Create a new subscription.
     *
     * @param  array  $requestData
     * @return array
     */
    public function createSubscription($requestData): array
    {
        $paymentMethod = $requestData['payment_method'];
        $interval = $requestData['interval'];

        $this->business->createOrGetStripeCustomer();

        return "Test Return";

        // Check if the payment method is already attached to the customer
        $isExistingCard = $this->stripe->validatePaymentMethod($paymentMethod, $this->business->stripe_id);
        if ($isExistingCard) {
            // If the payment method is already attached to the customer, use it
            $paymentMethod = $isExistingCard->id;
        }

        // Set the payment method as the default payment method
        $this->business->updateDefaultPaymentMethod($paymentMethod);

        $prices = $this->pricingService->getPrices($interval);
        $priceIds = array_column($prices[$interval], 'price_id');

        $adminQty = $this->order->num_admin + $this->business->num_admin;
        $technicianQty = $this->order->num_technician + $this->business->num_technician;

        //Update total price to order
        $this->updatePriceToOrder($interval);

        try {
            $subscription = $this->business->newSubscription(self::DEFAULT_SUBSCRIPTION, $priceIds)
            ->quantity($adminQty, $prices[$interval]['admin']['price_id'])
            ->quantity($technicianQty, $prices[$interval]['technician']['price_id'])
            ->create($paymentMethod, [
                'payment_behavior' => 'default_incomplete',
            ], [
                'metadata' => ['order_id' => $this->order->id],
            ]);

            if ($subscription->stripe_status == self::STATUS_ACTIVE) {
                $this->business->update(['billing_period' => $interval]);
                return $this->handleSuccessfulPayment();
            }

        } catch (IncompletePayment $e) {
            $intent = $e?->payment->asStripePaymentIntent();
            if ($intent && $intent->last_payment_error?->code === 'card_declined') {
                $this->business->subscriptions()->incomplete()->first()->cancelNow();
            }
            $this->business->update(['billing_period' => $interval]);
            throw $e;
        }

        return ['success' => false, 'message' => __('payments.payment_error')];
    }


    /**
     * Update subscription total_price to order
     * @param string $interval
     * @return void
     */
    private function updatePriceToOrder(string $interval): void
    {
        try {
            $adminSubTotal = $this->order->admin_qty_change * $this->prices[$interval]['admin']['price'];
            $technicianSubtotoal = $this->order->technician_qty_change * $this->prices[$interval]['technician']['price'];
            $totalPrice = $adminSubTotal + $technicianSubtotoal;
            $this->order->update(['total_price' => $totalPrice, 'billing_frequency' => $interval]);
        } catch (Exception $e) {
            Log::error('Fn:updatePriceToOrder - Unable to update total price to order:', [
                'error' => $e->getMessage(),
                'business_id' => $this->business->id,
                'order_id' => $this->order->id
            ]);
        }
    }

    /**
     * Upgrade the subscription.
     *
     * @param  string  $paymentMethod
     * @return array
     */
    public function updateSubscription($paymentMethod): array
    {
        if (! $this->order) {
            throw new PriceException(trans('payments.payment_session_invalid'));
        }

        try {
            // Check if the payment method is already attached to the customer
            $isExistingCard = $this->stripe->validatePaymentMethod($paymentMethod, $this->business->stripe_id);
            if ($isExistingCard) {
                // If the payment method is already attached to the customer, use it
                $paymentMethod = $isExistingCard->id;
            }

            // Set the payment method as the default payment method
            $this->business->updateDefaultPaymentMethod($paymentMethod);

            // Get the subscription items
            $subscription_items = $this->getSubscriptionItems();
            $subscription = $this->business->subscription(self::DEFAULT_SUBSCRIPTION);

            // Validate the subscription exists
            if (!$subscription) {
                throw new SubcriptionNotFoundException('Subscription not found');
            }

            // Validate subscription items
            if (empty($subscription_items)) {
                throw new ItemsNotFoundException('Invalid subscription items');
            }

            $updatedSubscription = $this->stripe->updateSubscription($subscription_items, $subscription->stripe_id, $paymentMethod);
            $invoice = $updatedSubscription->latest_invoice;
            $paymentIntent = $invoice->payment_intent;

            if ($paymentIntent->status === 'requires_action') {
                // You need to confirm the payment on the frontend using client_secret
                return $this->processPaymentIntent($paymentIntent);
            } elseif ($updatedSubscription->latest_invoice->status == self::STATUS_PAID) {
                $reutnResponse = $this->handleSuccessfulPayment();
                $reutnResponse['amount_paid'] = $updatedSubscription->latest_invoice->amount_paid;
                return $reutnResponse;
            }

        } catch (Exception $e) {
            Log::error('Subscription Update Error', [
                'message' => $e->getMessage(),
                'business_id' => $this->business->id
            ]);
        }

        return ['success' => false, 'message' => __('payments.payment_error')];
    }

    /**
     * Handle a Stripe PaymentIntent and return a standard response.
     *
     * @param  \Stripe\PaymentIntent|null  $intent
     * @return array
     */
    public function processPaymentIntent($intent): array
    {
        if (! $intent) {
            Log::error("Order Id:{$this->order->id} - No payment intent found.");
            return ['success' => false, 'message' => __('payments.payment_error')];
        }

        return match($intent->status) {
            'succeeded' => $this->handleSuccessfulPayment(),
            'requires_action' => $this->handleRequiresAction($intent),
            'requires_confirmation' => $this->handleRequiresConfirmation($intent),
            default => $this->handleFailedPayment($intent),
        };
    }

    /**
     * Handle successful payment intent.
     */
    private function handleSuccessfulPayment(): array
    {
        return ['success' => true, 'message' => 'Payment successful.', 'amount_paid' => 0 ];
    }

    /**
     * Handle payment intent that requires action.
     */
    private function handleRequiresAction(\Stripe\PaymentIntent $intent): array
    {
        Log::info('From SubscriptionService processPaymentIntent: requires_action : ' . $intent->status);
        return [
            'success' => false,
            'requires_action' => true,
            'client_secret' => $intent->client_secret,
            'order_id' => $this->order->payment_uuid
        ];
    }

    /**
     * Handle payment intent that requires confirmation.
     */
    private function handleRequiresConfirmation(\Stripe\PaymentIntent $intent): array
    {
        try {
            Log::info('From SubscriptionService processPaymentIntent: Requires Confirmation: ' . $intent->status);
            $confirmedIntent = $this->stripe->confirmPaymentIntent($intent->id);
            return $this->processPaymentIntent($confirmedIntent);
        } catch (\Stripe\Exception\CardException $e) {
            return ['success' => false, 'message' => 'Card declined: ' . $e->getMessage()];
        }
    }

    /**
     * Handle failed payment intent.
     */
    private function handleFailedPayment(\Stripe\PaymentIntent $intent): array
    {
        $errorMsg = $intent->last_payment_error->message;
        return ['success' => false, 'message' => $errorMsg];
    }

    /**
     * Get the subscription items.
     * @param bool $rollback
     * @return array
     */
    public function getSubscriptionItems(bool $rollback = false): array
    {
        $items = [];
        $subscription_items = $this->business->subscription(self::DEFAULT_SUBSCRIPTION)->items;
        $prices = $this->pricingService->getPrices($this->business->billing_period);

        if (! $subscription_items || ! $this->order) {
            return [];
        }

        foreach ($subscription_items as $subs_item) {
            $item = [];
            $item['id'] = $subs_item->stripe_id;
            if ($this->setting->admin_product_id == $subs_item->stripe_product) {
                $item['quantity'] = $rollback ? $this->business->num_admin : $this->order->num_admin + $this->business->num_admin;
                $item['price'] = $prices[$this->business->billing_period]['admin']['price_id'];
            } else {
                $item['quantity'] = $rollback ? $this->business->num_technician : $this->order->num_technician + $this->business->num_technician;
                $item['price'] = $prices[$this->business->billing_period]['technician']['price_id'];
            }

            $items[] = $item;
        }

        return $items;
    }

    /**
     * Get the downgrade subscription items.
     * @param bool $rollback
     * @return array
     */
    public function getDowngradeItems(): array
    {
        $items = [];
        $subscription_items = $this->business->subscription(self::DEFAULT_SUBSCRIPTION)->items;
        $prices = $this->pricingService->getPrices($this->business->billing_period);

        if (! $subscription_items || ! $this->order) {
            return [];
        }

        foreach ($subscription_items as $subs_item) {
            $item = [];
            $item['id'] = $subs_item->stripe_id;
            if ($this->setting->admin_product_id == $subs_item->stripe_product) {
                $item['quantity'] =  $this->order->num_admin;
                $item['price'] = $prices[$this->business->billing_period]['admin']['price_id'];
            } else {
                $item['quantity'] =  $this->order->num_technician;
                $item['price'] = $prices[$this->business->billing_period]['technician']['price_id'];
            }

            $items[] = $item;
        }

        return $items;
    }

    /**
     * Process the downgrade.
     */
    public function processDowngrade(): bool
    {
        try {
            // Get the subscription items
            $subscription_items = $this->getDowngradeItems();
            $this->stripe->updateSubscription($subscription_items, $this->subscription->stripe_id);

            //Deactivate all subadmins except the primary subadmin and currently logged in subadmin
            $this->business->subAdmins()
                ->where('id', '!=', $this->user->id)
                ->update(['status' => BusinessUser::STATUS_INACTIVE]);

            //Deactivate all technicians except the currently logged in technician
            $this->business->technicians()->update(['status' => Technician::STATUS_INACTIVE]);

            //Update the order status to completed
            $this->order->update(['status' => Order::STATUS_COMPLETED]);

            return true;

        } catch (Exception $e) {
            Log::error('Error processing downgrade: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get the customer credit balance.
     * @return int
     */
    public function getCustomerCreditBalance(): int
    {
        $customer = $this->stripe->getCustomer($this->business->stripe_id);
        // Credit balance is stored as a negative value in "balance" field
        return (int) $customer->balance; // in cents
    }

    /**
     * Create subscription pricing for both admin and technician roles
     *
     * @param array $prices Array containing admin and technician prices for different intervals
     * @return array Array of created price objects
     * @throws \Exception
     */
    public function createSubscriptionPricing(array $prices): array
    {

        $this->getMonthlyPrices();
        try {
            $pricingConfigs = $this->preparePricingConfigs($prices);
            $createdPrices = [];

            foreach ($pricingConfigs as $config) {
                $createdPrices[$config['role']][$config['lookup_key']] = $this->stripe->createPrice([
                    'currency' => 'usd',
                    'unit_amount' => (int)($config['amount'] * 100), // Convert to cents
                    'recurring' => [
                        'interval' => $config['interval'],
                        'interval_count' => $config['interval_count']
                    ],
                    'product' => $config['product_id'],
                    'lookup_key' => $config['lookup_key'],
                    'transfer_lookup_key' => true
                ]);
            }

            //Update the prices
            $this->updateLocalPrices($createdPrices);
            //Update the discount half yearly and yearly
            $this->updateDiscounts($prices['discounts']);

            return $createdPrices;
        } catch (Exception $e) {
            Log::error('Error creating subscription pricing: ' . $e->getMessage(), [
                'prices' => $prices
            ]);
            throw $e;
        }
    }

    /**
     * Update the local prices
     * @param array $createdPrices
     * @return void
     */
    protected function updateLocalPrices(array $createdPrices): void
    {
        foreach ($createdPrices as $role => $pricesByInterval) {
            foreach ($pricesByInterval as $interval => $price) {
                Price::where('type', $role)
                    ->where('interval', $interval)
                    ->update([
                        'price_id' => $price->id,
                        'price' => $price->unit_amount / 100,
                    ]);
            }
        }
    }

    /**
     * Update the discounts
     * @param array $discounts
     * @return void
     */
    protected function updateDiscounts(array $discounts): void
    {
        $this->setting->update([
            'discount_half_yearly' => $discounts['half-yearly'] ?? 0,
            'discount_yearly' => $discounts['yearly'] ?? 0,
        ]);
    }

    /**
     * Prepare pricing configurations for Stripe price creation
     *
     * @param array $prices
     * @return array
     */
    /**
     * Get monthly prices for admin and technician subscriptions
     *
     * @return array Returns array with admin and technician monthly prices
     */
    public function getMonthlyPrices(): array
    {
        return $this->prices['monthly'];

    }

    private function preparePricingConfigs(array $prices): array
    {
        $configs = [];
        $roles = ['admin', 'technician'];
        $intervals = [
            'monthly' => ['interval' => 'month', 'count' => 1],
            'half-yearly' => ['interval' => 'month', 'count' => 6],
            'yearly' => ['interval' => 'year', 'count' => 1]
        ];

        foreach ($roles as $role) {
            $productId = $role === self::ADMIN ?
                $this->setting->admin_product_id :
                $this->setting->technician_product_id;

            foreach ($intervals as $key => $intervalConfig) {
                $baseAmount = $prices[$role][$key];
                $billingInterval = ($key === self::HALF_YEARLY) ? self::MONTHS_IN_HALF_YEAR : self::MONTHS_IN_YEAR;
                $totalAmount = $key === self::MONTHLY ?
                    $baseAmount :
                    $baseAmount * $billingInterval;

                $configs[] = [
                    'amount' => $totalAmount,
                    'interval' => $intervalConfig['interval'],
                    'interval_count' => $intervalConfig['count'],
                    'product_id' => $productId,
                    'lookup_key' => $key,
                    'role' => $role
                ];
            }
        }

        return $configs;
    }
}
