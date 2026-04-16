<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Models\Business\WorkOrder\WorkOrder;
use App\Models\Shared\Country;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Constants\StoragePaths;
use App\Helpers\FileHelper;
use App\Casts\CustomerEquipmentImageUrl;
use App\Casts\CustomerEquipmentImageThumbUrl;

class Customer extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Concerns\ManagesActiveState;

    /**
     * Pool type constants
     * @var int
     */
    public const POOL_TYPE_RESIDENTIAL = 1;
    public const POOL_TYPE_COMMERCIAL = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'pool_type',
        'first_name',
        'last_name',
        'email_1',
        'email_2',
        'isd_code',
        'phone_1',
        'phone_2',
        'address',
        'street',
        'city',
        'state',
        'country_id',
        'zip_code',
        'commercial_pool_details',
        'pool_size_gallons',
        'pool_length',
        'pool_width',
        'pool_depth',
        'clean_psi',
        'clean_psi_image',
        'entry_instruction',
        'tech_notes',
        'admin_notes',
        'status',
        'filter_details',
        'filter_image',
        'pump_details',
        'pump_image',
        'cleaner_details',
        'cleaner_image',
        'heater_details',
        'heater_image',
        'heat_pump_details',
        'heat_pump_image',
        'aux_details',
        'aux_image',
        'aux2_details',
        'aux2_image',
        'salt_system_details',
        'salt_system_image',
        'delete_clean_psi_image',
        'delete_filter_image',
        'delete_pump_image',
        'delete_cleaner_image',
        'delete_heater_image',
        'delete_heat_pump_image',
        'delete_aux_image',
        'delete_aux2_image',
        'delete_salt_system_image',
    ];

    protected $guarded = ['business_id'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => 'boolean',
        'pool_type' => 'integer', // 1=Residential, 2=Commercial
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
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'status_text',
        'clean_psi_image_thumb',
        'filter_image_thumb',
        'pump_image_thumb',
        'cleaner_image_thumb',
        'heater_image_thumb',
        'heat_pump_image_thumb',
        'aux_image_thumb',
        'aux2_image_thumb',
        'salt_system_image_thumb',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'status' => self::STATUS_ACTIVE // Default status is true (active)
    ];

    /**
     * Boot the model and register any model events.
     */
    /**
     * Handle image deletion for a given field
     */
    private function handleImageDeletion(string $field, ?string $oldImage): void //NOSONAR
    {
        if (!$oldImage) {
            return;
        }

        FileHelper::deleteFile(StoragePaths::CUSTOMER_EQUIPMENT_IMAGES . $oldImage, 's3');
        FileHelper::deleteFile(StoragePaths::CUSTOMER_EQUIPMENT_THUMBNAIL_IMAGES . $oldImage, 's3');
    }

    /**
     * Process equipment image fields during save
     */
    private function processEquipmentImages(): void
    {
        $imageFields = [
            'clean_psi_image', 'filter_image', 'pump_image', 'cleaner_image',
            'heater_image', 'heat_pump_image', 'aux_image', 'aux2_image',
            'salt_system_image'
        ];

        foreach ($imageFields as $field) {
            // Handle image replacement
            if ($this->isDirty($field)) {
                $this->handleImageDeletion($field, $this->getRawOriginal($field));
                continue;
            }

            // Handle explicit deletion
            $deleteField = "delete_{$field}";
            if (!request()->boolean($deleteField)) {
                continue;
            }

            $this->handleImageDeletion($field, $this->attributes[$field] ?? null);
            $this->$field = null;
        }
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($customer) {
            $customer->isd_code = $customer->business?->country?->isd_code ?? config('app.isd_code');
        });

        static::saving(function ($customer) {
            $customer->zip_code = isset($customer->zip_code) ? trim($customer->zip_code) : $customer->zip_code;
            $customer->processEquipmentImages();
        });
    }

    /**
     * Get the text representation of the status.
     * This is an example of the Accessor Pattern in Laravel.
     */
    protected function statusText(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status === self::STATUS_ACTIVE ? __('business.customer.status.active') : __('business.customer.status.inactive')
        );
    }

    /**
     * Get the customer's name with support for different formats.
     */
    protected function formatName(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    /**
     * Get the customer's full name.
     */
    public function getFullNameAttribute(): string //NOSONAR
    {
        return $this->formatName();
    }

    /**
     * Get the customer's name (alias for full_name).
     */
    public function getNameAttribute(): string //NOSONAR
    {
        return $this->formatName();
    }

    /**
     * Get the customer's display name.
     */
    public function getCustomerNameAttribute(): string
    {
        return $this->pool_type == 2 ? $this->commercial_pool_details : $this->formatName();
    }

    /**
     * Format the full address from components.
     */
    protected function formatAddress(): string
    {
        $parts = array_filter([
            $this->address,
            $this->street,
            $this->city,
            $this->state,
            $this->zip_code,
            $this->country?->name,
        ], fn ($value) => !is_null($value) && $value !== '');

        return implode(', ', $parts);
    }

    /**
     * Get the customer's full address.
     */
    public function getFullAddressAttribute(): string
    {
        return $this->formatAddress();
    }

    /**
     * Location accessors
     */
    public function getCityNameAttribute(): ?string
    {
        return $this->city;
    }
    public function getStateNameAttribute(): ?string
    {
        return $this->state;
    }
    public function getCountryNameAttribute(): ?string
    {
        return $this->country?->name;
    }
    public function getPhoneTwoAttribute(): string
    {
        return $this->phone_2 ? "{$this->isd_code}-{$this->phone_2}" : '-';
    }

    /**
     * Get the pool type label
     */
    public function getPoolTypeLabelAttribute(): string
    {
        return $this->pool_type == 1
            ? __('business.customer.pool_type.residential')
            : __('business.customer.pool_type.commercial');
    }

    /**
     * Relationships
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function workOrders(): HasMany
    {
        return $this->hasMany(WorkOrder::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}
