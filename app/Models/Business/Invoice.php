<?php

namespace App\Models\Business;

use App\Helpers\Helper;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Invoice Model
 *
 * @property int $id
 * @property int $business_id
 * @property string $invoice_id
 * @property string $invoice_number
 * @property int $customer_id
 * @property string $subscription_id
 * @property float $amount_due
 * @property float $amount_paid
 * @property string $billing_reason
 * @property string $description
 * @property string $currency
 * @property string $invoice_status
 * @property string $invoice_url
 * @property string $invoice_pdf
 * @property int $order_id
 * @property string $created
 * @property string $period_start
 * @property string $period_end
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Invoice extends Model
{
    use HasFactory;

    public const STATUS_OPEN = 'open';

    public const STATUS_PAID = 'paid';

    public const STATUS_VOID = 'void';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'business_id',
        'invoice_id',
        'invoice_number',
        'customer_id',
        'subscription_id',
        'amount_due',
        'amount_paid',
        'total',
        'billing_reason',
        'invoice_type',
        'description',
        'currency',
        'invoice_status',
        'invoice_url',
        'invoice_pdf',
        'order_id',
        'created',
        'period_start',
        'period_end',
        'created_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount_paid' => 'decimal:2',
        'amount_due' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    /**
     * Get the subscription that owns the invoice.
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class, 'subscription_id', 'stripe_id');
    }

    /**
     * Get the order associated with the invoice.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the business that owns the invoice.
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class)->withTrashed();
    }

    /**
     * Get the formatted invoice amount with currency.
     */
    public function getInvoiceAmountAttribute(): string
    {
        $amount = abs($this->total) > 0 ? $this->total : $this->amount_paid;
        $amountPaid = Helper::getFormattedAmountWithCurrency(abs($amount));
        return $this->invoice_type == 'downgrade' ? '('.$amountPaid.')' : $amountPaid;
    }


    /**
     * Get the formatted billing period.
     */
    public function getBillingPeriodAttribute(): string
    {
        return Helper::getFormattedDate($this->period_start).' - '.Helper::getFormattedDate($this->period_end);
    }

    /**
     * Get the formatted Invoice created date.
     */
    public function getCreatedAtAttribute(): string
    {
        return Helper::getFormattedDate($this->created);
    }

    /**
     * Scope a query to only include paid invoices.
     */
    #[Scope]
    protected function paid(Builder $query): Builder
    {
        return $query->where('invoice_status', self::STATUS_PAID);
    }

    /**
     * Scope a query to select specific fields.
     */
    #[Scope]
    protected function selectFields(Builder $query): Builder
    {
        return $query->select(
            'order_id',
            'invoice_number',
            'billing_reason',
            'amount_paid',
            'total',
            'invoice_type',
            'created',
            'period_start',
            'period_end'
        );
    }

    /**
     * Scope a query to only include invoices from non-deleted businesses.
     */
    #[Scope]
    public function businessNotDeleted(Builder $query): Builder
    {
        return $query->whereHas('business', function (Builder $query): void {
            $query->whereNull('deleted_at');
        });
    }
}
