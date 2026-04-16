<?php

namespace App\Models\Business;

use App\Helpers\FileHelper;
use App\Models\Business\Technician\SkillType;
use App\Models\Business\Technician\Technician;
use App\Models\Business\WorkOrder\Item;
use App\Models\Shared\City;
use App\Models\Shared\Country;
use App\Models\Shared\State;
use App\Models\Business\WorkOrder\WorkOrder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Cashier\Billable;
use App\Helpers\Helper;

class Business extends Model
{
    use Billable;
    use HasFactory;
    use SoftDeletes;

    public const ONBOARDING_COMPLETED = 1;

    public const IMAGE_PATH = 'business/profile-images/thumbnail';

    public const STATUS_ACTIVE = 1;

    public const STATUS_INACTIVE = 0;

    public const JOBS_LIMIT = 5;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'isd_code',
        'phone',
        'address',
        'country_id',
        'state_id',
        'city_id',
        'street',
        'zipcode',
        'website_url',
        'logo',
        'num_admin',
        'num_technician',
        'onboarding_completed',
        'status',
        'trial_ends_at',
        'trial_end_at',
        'business_category_id',
        'credit_balance',
        'billing_period',
        'timezone',
    ];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>|bool
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'status' => 'boolean',
        'trial_ends_at' => 'datetime',
        'trial_end_at' => 'datetime',
    ];

    /**
     * Get the country for the business.
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class)->where('status', 1);
    }

    /**
     * Get the state for the business.
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    /**
     * Get the city for the business.
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Get the users for the business.
     */
    public function users(): HasMany
    {
        return $this->hasMany(BusinessUser::class);
    }

    /**
     * Get the technicians for the business.
     */
    public function technicians(): HasMany
    {
        return $this->hasMany(Technician::class);
    }

    /**
     * Get the active technicians for the business.
     */
    public function activeTechnicians(): HasMany
    {
        return $this->hasMany(Technician::class)->where('status', 1);
    }

    /**
     * Get the active Sub Admins for the business.
     */
    public function activeSubAdmins(): HasMany
    {
        return $this->hasMany(BusinessUser::class)->where('is_primary', 0)->where('status', 1);
    }

    /**
     * Get all skill types through technicians.
     */
    public function technicianSkills(): HasManyThrough
    {
        return $this->hasManyThrough(
            SkillType::class,
            Technician::class,
            'business_id', // Foreign key on technicians table
            'id', // Foreign key on skill_types table
            'id', // Local key on businesses table
            'id' // Local key on technicians table
        );
    }

    /**
     * Get the clients for the business.
     */
    public function clients(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    /**
     * Get the invoices for the business.
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class)
            ->where('invoice_status', 'paid')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Get the primary user for the business.
     */
    public function primaryUser(): HasOne
    {
        return $this->hasOne(BusinessUser::class)->where('is_primary', 1);
    }

    /**
     * Get the business logo.
     */
    public function getBusinessLogoAttribute(): ?string
    {
        return FileHelper::getImageUrl($this->logo, self::IMAGE_PATH);
    }

    /**
     * Get the Sub Admins for the business.
     */
    public function subAdmins(): HasMany
    {
        return $this->hasMany(BusinessUser::class)->where('is_primary', 0);
    }

    /**
     * Get the latest invoice for the business.
     */
    public function latestInvoice(): HasOne
    {
        return $this->hasOne(Invoice::class)->latestOfMany();
    }

    /**
     * Get the business chemicals for this business.
     */
    public function businessChemicals(): HasMany
    {
        return $this->hasMany(\App\Models\Business\Chemical\BusinessChemical::class);
    }

    /**
     * Get the initials for the business User.
     */
    public function getUserInitialsAttribute()
    {
        return Helper::getInitials($this->name);
    }

    /**
     * Get the items for the business.
     */
    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    /**
     * Get the jobs for the business.
     */
    public function jobs(): HasMany
    {
        return $this->hasMany(WorkOrder::class);
    }

    /**
     * Get the unassigned jobs for the business.
     */
    public function unassignedJobs(bool $limit = false): HasMany
    {
        return $this->hasMany(WorkOrder::class)
            ->whereNull('technician_id')
            ->where('is_active', self::STATUS_ACTIVE)
            ->latest()
            ->when($limit, fn ($query) => $query->limit(self::JOBS_LIMIT));
    }

    public function onTrial(): bool
    {
        return $this->trial_end_at && $this->trial_end_at->isFuture();
    }

}
