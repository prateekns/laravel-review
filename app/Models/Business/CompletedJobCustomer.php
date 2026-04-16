<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Business\WorkOrder\WorkOrder;
use App\Casts\CustomerEquipmentImageThumbUrl;
use App\Casts\CustomerEquipmentImageUrl;

class CompletedJobCustomer extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'work_order_id',
        'instance_id',
        'customer_id',
        'name',
        'first_name',
        'last_name',
        'company_name',
        'email_1',
        'email_2',
        'phone_1',
        'phone_2',
        'address',
        'street',
        'city',
        'state',
        'country',
        'zip_code',
        'pool_type',
        'commercial_pool_details',
        'pool_size_gallons',
        'pool_length',
        'pool_width',
        'pool_depth',
        'clean_psi',
        'clean_psi_image',
        'pump_details',
        'pump_image',
        'filter_details',
        'filter_image',
        'cleaner_details',
        'cleaner_image',
        'heat_pump_details',
        'heat_pump_image',
        'aux_details',
        'aux_image',
        'aux2_details',
        'aux2_image',
        'heater_details',
        'heater_image',
        'salt_system_details',
        'salt_system_image',
        'entry_instruction',
        'tech_notes',
        'admin_notes'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'pool_size_gallons' => 'double',
        'pool_length' => 'double',
        'pool_width' => 'double',
        'pool_depth' => 'double',
        'clean_psi_image_thumb' => CustomerEquipmentImageThumbUrl::class,
        'filter_image_thumb' => CustomerEquipmentImageThumbUrl::class,
        'pump_image_thumb' => CustomerEquipmentImageThumbUrl::class,
        'cleaner_image_thumb' => CustomerEquipmentImageThumbUrl::class,
        'heater_image_thumb' => CustomerEquipmentImageThumbUrl::class,
        'heat_pump_image_thumb' => CustomerEquipmentImageThumbUrl::class,
        'aux_image_thumb' => CustomerEquipmentImageThumbUrl::class,
        'aux2_image_thumb' => CustomerEquipmentImageThumbUrl::class,
        'salt_system_image_thumb' => CustomerEquipmentImageThumbUrl::class,
        'clean_psi_image' => CustomerEquipmentImageUrl::class,
        'filter_image' => CustomerEquipmentImageUrl::class,
        'pump_image' => CustomerEquipmentImageUrl::class,
        'cleaner_image' => CustomerEquipmentImageUrl::class,
        'heater_image' => CustomerEquipmentImageUrl::class,
        'heat_pump_image' => CustomerEquipmentImageUrl::class,
        'aux_image' => CustomerEquipmentImageUrl::class,
        'aux2_image' => CustomerEquipmentImageUrl::class,
        'salt_system_image' => CustomerEquipmentImageUrl::class,
    ];

    /**
     * Get the work order that owns this completed job customer.
     */
    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    /**
     * Get the original customer if still exists.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the customer's name (alias for full_name).
     */
    public function getCustomerNameAttribute(): string
    {
        return 'Commercial Pool' == $this->pool_type ? $this->company_name : $this->first_name . ' ' . $this->last_name;
    }
}
