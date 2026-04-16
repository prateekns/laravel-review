<?php

namespace App\Models\Business\WorkOrder;

use App\Enums\WorkOrderStatus;
use App\Models\Business\Business;
use App\Models\Business\Customer;
use App\Models\Business\Templates;
use App\Models\Business\Technician\Technician;
use App\Models\Business\WorkOrder\Concerns\ManagesActiveState;
use App\Models\Business\WorkOrder\Concerns\WorkOrderDateTimeTrait;
use App\Models\Business\WorkOrder\Concerns\WorkOrderStatusTrait;
use App\Models\Business\WorkOrder\Concerns\WorkOrderRecurrenceTrait;
use App\Models\Business\WorkOrder\Concerns\WorkOrderInstancesTrait;
use App\Models\Business\WorkOrder\Concerns\WorkOrderImageTrait;
use App\Models\Business\WorkOrder\Concerns\WorkOrderBusinessTimezoneTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use App\Models\Business\CompletedJobCustomer;

class WorkOrder extends Model
{
    use HasFactory;
    use SoftDeletes;
    use ManagesActiveState;
    use WorkOrderDateTimeTrait;
    use WorkOrderStatusTrait;
    use WorkOrderRecurrenceTrait;
    use WorkOrderInstancesTrait;
    use WorkOrderImageTrait;
    use WorkOrderBusinessTimezoneTrait;

    public const ACTIVE_STATUS = 1;

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'is_active' => true,
        'status' => WorkOrderStatus::PENDING->value,
        'type' => 'WO'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'business_id',
        'customer_id',
        'template_id',
        'technician_id',
        'name',
        'description',
        'photo',
        'additional_task',
        'preferred_start_date',
        'preferred_start_time',
        'communication_notes',
        'is_recurring',
        'recurrence_rule',
        'frequency',
        'repeat_after',
        'selected_days',
        'end_date',
        'effective_until',
        'monthly_day_type',
        'monthly_day_of_week',
        'is_active',
        'status',
        'type',
        'extra_work_done',
        'technician_customer_coordination',
        'job_id',
        'completed_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'preferred_start_date' => 'date',
        'end_date' => 'date',
        'effective_until' => 'date',
        'is_recurring' => 'boolean',
        'is_active' => 'boolean',
        'technician_customer_coordination' => 'boolean',
        'selected_days' => 'array',
        'recurrence_rule' => 'array',
        'status' => WorkOrderStatus::class,
        'completed_at' => 'datetime',
    ];

    /**
     * Attributes that should be appended to array/JSON form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'job_duration',
        'photo_url',
        'photo_thumb_url',
    ];

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::saving(function ($workOrder) {
            $workOrder->handleImageManagement();
        });

        static::addGlobalScope('business', function ($builder) {
            if (auth()->guard('business')->check()) {
                $builder->where('business_id', auth()->guard('business')->user()->business_id);
            }
        });

        static::addGlobalScope('default_select', function ($builder) {
            $builder->select([
                'work_orders.id',
                'work_orders.business_id',
                'work_orders.customer_id',
                'work_orders.technician_id',
                'work_orders.template_id',
                'work_orders.preferred_start_date',
                'work_orders.preferred_start_time',
                'work_orders.name',
                'work_orders.description',
                'work_orders.additional_task',
                'work_orders.photo',
                'work_orders.communication_notes',
                'work_orders.is_recurring',
                'work_orders.recurrence_rule',
                'work_orders.frequency',
                'work_orders.repeat_after',
                'work_orders.selected_days',
                'work_orders.end_date',
                'work_orders.monthly_day_type',
                'work_orders.monthly_day_of_week',
                'work_orders.is_active',
                'work_orders.status',
                'work_orders.type',
                'work_orders.extra_work_done',
                'work_orders.technician_customer_coordination',
                'work_orders.completed_at',
                'work_orders.created_at',
                'work_orders.updated_at'
            ]);
        });

        static::creating(function ($workOrder) {
            if (!$workOrder->job_id) {
                $workOrder->job_id = $workOrder->generateJobId();
            }
        });

        static::saving(function ($workOrder) {
            if (!$workOrder->is_recurring) {
                $workOrder->recurrence_rule = null;
                $workOrder->frequency = null;
                $workOrder->repeat_after = null;
                $workOrder->selected_days = null;
                $workOrder->end_date = null;
                $workOrder->monthly_day_type = null;
                $workOrder->monthly_day_of_week = null;
                $workOrder->effective_until = null;
                return;
            }

            $rruleString = $workOrder->generateRRule();
            if ($rruleString) {
                $workOrder->recurrence_rule = ['rrule' => $rruleString];
            }
        });
    }

    /**
     * Get the business that owns the work order
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get the customer that owns the work order
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the template associated with the work order
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(Templates::class, 'template_id');
    }

    /**
     * Get the technician assigned to the work order
     */
    public function technician(): BelongsTo
    {
        return $this->belongsTo(Technician::class);
    }

    /**
     * Get the items sold for this work order
     */
    public function itemsSold(): HasMany
    {
        return $this->hasMany(ItemSold::class)->where('business_id', auth()->guard('business')->user()->business_id);
    }

    /**
     * Get the items sold for this work order
     */
    public function itemSold(): HasMany
    {
        return $this->hasMany(ItemSold::class)
            ->when(request('instance_id'), function ($query, $instance_id) {
                return $query->where('instance_id', $instance_id);
            });
    }

    /**
     * Get the chemical logs for this work order
     */
    public function chemicalLogs(): HasMany
    {
        return $this->hasMany(\App\Models\Business\Chemical\ChemicalLog::class, 'work_order_id')
        ->when(request('instance_id'), function ($query, $instance_id) {
            return $query->where('instance_id', $instance_id);
        });
    }

    /**
     * Get the checklist items for the work order
     */
    public function checklist(): HasMany
    {
        return $this->hasMany(WorkOrderChecklistItem::class);
    }

    /**
     * Get the used maintenance items for this work order
     */
    public function usedMaintenanceItems(): HasMany
    {
        return $this->hasMany(UsedMaintenanceItem::class);
    }

    /**
     * Get completed job customer snapshots for this work order
     */
    public function completedJobCustomers(): HasMany
    {
        return $this->hasMany(CompletedJobCustomer::class);
    }

    // Instance and assignment helpers moved to WorkOrderInstancesTrait

    // generateInstanceId moved to WorkOrderInstancesTrait

    // generateJobId moved to WorkOrderInstancesTrait

    // calculateInstances moved to WorkOrderInstancesTrait

    // getAssignedInstances moved to WorkOrderInstancesTrait

    // isInstanceAssigned moved to WorkOrderInstancesTrait

    // getInstanceStatus moved to WorkOrderInstancesTrait

    /**
     * Get the next occurrence date for recurring maintenance orders.
     */
    /**
     * Get the end time of the job based on start time and duration
     */
    public function getEndTime(): Carbon
    {
        $startTime = $this->preferred_start_time ?? $this->getDefaultStartTime();
        $startDateTime = Carbon::parse($this->preferred_start_date->format('Y-m-d') . ' ' . $startTime, 'UTC');
        $duration = $this->job_duration ?? config('datetime.durations.default');

        return $startDateTime->copy()->addMinutes($duration);
    }

    /**
     * Virtual job duration accessor (not persisted in DB).
     * If not set at runtime, falls back to configured default duration.
     */
    public function getJobDurationAttribute(): int
    {
        $value = $this->attributes['job_duration'] ?? null;
        return $value !== null ? (int) $value : (int) config('datetime.durations.default');
    }

    private function getUtcTime()
    {
        $time = $this->preferred_start_time ?? $this->getDefaultMidnightTime();
        return Carbon::parse($this->preferred_start_date->format($this->getCustomDateFormat()) . ' ' . $time, $this->getUtcTimezone());
    }

    /**
     * Get the formatted Work Order preferred date.
     */
    public function getPreferredDateAttribute(): string
    {
        $businessTimezone = $this->getBusinessTimezone();
        return $this->getUtcTime()->setTimezone($businessTimezone)->format('M d, Y g:i A');
    }

    /**
     * Get the formatted Work Order preferred time.
     */
    public function getPreferredTimeAttribute(): string
    {
        $businessTimezone = $this->getBusinessTimezone();
        return $this->getUtcTime()->setTimezone($businessTimezone)->format('g:i A');
    }

    /**
     * Get the formatted Work Order created date.
     */
    public function getJobStatusAttribute(): string
    {
        return WorkOrderStatus::UPCOMING->label();
    }

    // current_technician accessor moved to WorkOrderInstancesTrait

    /**
     * Get the latest CompletedJobCustomer for this work order when completed.
     */
    public function getCompletedJobCustomerAttribute(): ?CompletedJobCustomer
    {
        if (!$this->isCompleted()) {
            return null;
        }

        return CompletedJobCustomer::where('work_order_id', $this->id)
            ->first();
    }

    /**
     * Determine if the completed job customer has no pool details.
     */
    public function getHasNoPoolDetailsAttribute(): bool
    {
        $completed = $this->completed_job_customer;
        if (!$completed) {
            return true;
        }

        $fields = [
            'pool_type',
            'pool_size_gallons',
            'clean_psi',
            'filter_details',
            'pump_details',
            'heater_details',
            'cleaner_details',
            'salt_system_details',
            'heat_pump_details',
            'aux_details',
            'aux2_details',
        ];

        foreach ($fields as $field) {
            if (!empty($completed->{$field})) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the work order notification for this work order
     */
    public function workOrderNotification(): BelongsTo
    {
        return $this->belongsTo(WorkOrderCompleteNotification::class, 'id', 'work_order_id');
    }
}
