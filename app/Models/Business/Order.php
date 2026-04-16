<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_FAILED = 'failed';

    public const ORDER_TYPE_UPGRADE = 'upgrade';
    public const ORDER_TYPE_DOWNGRADE = 'downgrade';
    public const ORDER_TYPE_CREATE = 'create';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'payment_uuid',
        'business_id',
        'business_user_id',
        'admin_price',
        'technician_price',
        'num_admin',
        'num_technician',
        'admin_qty_change',
        'technician_qty_change',
        'total_admin',
        'total_technician',
        'proration_amt',
        'total_price',
        'status',
        'order_type',
        'billing_frequency'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'num_admin' => 'integer',
        'num_technician' => 'integer',
        'total_price' => 'decimal:2',
        'proration_amount' => 'decimal:2',
        'status' => 'boolean',
    ];

    /**
     * Get the business that owns the temporary team.
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get the business user that owns the temporary team.
     */
    public function businessUser(): BelongsTo
    {
        return $this->belongsTo(BusinessUser::class);
    }
}
