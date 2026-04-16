<?php

namespace App\Models\Business\WorkOrder;

use App\Models\Business\Technician\Technician;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkOrderCompleteNotification extends Model
{
    public const PATH = 'emails/';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'work_order_id',
        'technician_id',
        'instance_id',
        'customer_message',
        'customer_image_1',
        'customer_image_2',
        'business_message',
        'business_image_1',
        'business_image_2',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the work order that owns the notification.
     */
    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    /**
     * Get the technician that owns the notification.
     */
    public function technician(): BelongsTo
    {
        return $this->belongsTo(Technician::class);
    }

    /**
     * Get customer images array
     *
     * @return array
     */
    public function getCustomerImages(): array
    {
        $images = [];

        if ($this->customer_image_1) {
            $images[] = $this->customer_image_1;
        }
        if ($this->customer_image_2) {
            $images[] = $this->customer_image_2;
        }

        return $images;
    }

    /**
     * Get business images array
     *
     * @return array
     */
    public function getBusinessImages(): array
    {
        $images = [];

        if ($this->business_image_1) {
            $images[] = $this->business_image_1;
        }
        if ($this->business_image_2) {
            $images[] = $this->business_image_2;
        }

        return $images;
    }

    /**
     * Check if notification has customer content
     *
     * @return bool
     */
    public function hasCustomerContent(): bool
    {
        return !empty($this->customer_message) ||
            !empty($this->customer_image_1) ||
            !empty($this->customer_image_2);
    }

    /**
     * Check if notification has business content
     *
     * @return bool
     */
    public function hasBusinessContent(): bool
    {
        return !empty($this->business_message) ||
            !empty($this->business_image_1) ||
            !empty($this->business_image_2);
    }
}
