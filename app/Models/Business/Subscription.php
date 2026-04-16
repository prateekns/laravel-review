<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'business_id',
        'plan_id',
        'transaction_id',
        'price',
        'created_at',
        'stripe_id',
        'stripe_status',
        'next_billing_date',
        'amount_due',
    ];

    /**
     * Get the invoices for the subscription.
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'subscription_id', 'stripe_id');
    }

    /**
     * Get the subscription items for the subscription.
     */
    public function items(): HasMany
    {
        return $this->hasMany(SubscriptionItem::class, 'subscription_id', 'stripe_id');
    }
}
