<?php

namespace App\Models\Business\WorkOrder;

use App\Models\Business\Technician\Technician;
use App\Enums\WorkOrderStatus;
use App\Models\Business\CompletedJobCustomer;
use App\Models\Business\Templates;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Helpers\FileHelper;
use App\Constants\StoragePaths;

class WorkOrderAssignment extends Model
{
    protected $fillable = [
        'type',
        'work_order_id',
        'technician_id',
        'instance_id',
        'business_id',
        'customer_id',
        'template_id',
        'name',
        'description',
        'additional_task',
        'preferred_start_date',
        'preferred_start_time',
        'communication_notes',
        'technician_customer_coordination',
        'is_recurring',
        'frequency',
        'repeat_after',
        'selected_days',
        'end_date',
        'monthly_day_type',
        'monthly_day_of_week',
        'photo',
        'extra_work_done',
        'scheduled_date',
        'scheduled_time',
        'status',
        'is_exception',
        'recurrence_rule',
        'effective_from',
        'effective_until',
        'job_id',
        'completed_at',
        'is_active'
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'recurrence_rule' => 'array',
        'effective_from' => 'datetime',
        'effective_until' => 'datetime',
        'is_exception' => 'boolean',
        'status' => WorkOrderStatus::class,
        'is_active' => 'boolean',
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

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(Technician::class);
    }

    /**
     * Get the template associated with the work order
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(Templates::class, 'template_id');
    }

    public function completedJobCustomers(): HasMany
    {
        return $this->hasMany(CompletedJobCustomer::class, 'instance_id', 'instance_id');
    }

    protected static function booted(): void
    {
        $touchParent = function (WorkOrderAssignment $assignment): void {
            if ($assignment->relationLoaded('workOrder')) {
                $assignment->workOrder->touch();
            } else {
                // Touch parent without loading relation
                WorkOrder::withoutGlobalScopes()
                    ->where('id', $assignment->work_order_id)
                    ->when(!($assignment->isDirty('status') && $assignment->status === WorkOrderStatus::COMPLETED), function ($query) {
                        $query->update(['updated_at' => now()]);
                    });
            }
        };

        static::created($touchParent);
        static::updated($touchParent);
        static::deleted($touchParent);
    }

    /**
     * Delete reassigned instances/windows, preserving Completed and In-Progress.
     * work_order_id is required.
     * @param int $workOrderId
     * @return int
     */
    public static function deleteReassignmentsPreservingProgress(int $workOrderId): int
    {
        $query = static::query()
            ->where('work_order_id', $workOrderId)
            ->whereNotIn('status', [
                WorkOrderStatus::COMPLETED->value,
                WorkOrderStatus::IN_PROGRESS->value,
            ]);

        return $query->delete();
    }

    /**
     * Unassign reassigned instances/windows, preserving Completed and In-Progress.
     * technician_id is required.
     * @param int $technicianId
     * @return int
     */
    public static function unassignReassignmentsPreservingProgress(int $technicianId): int
    {
        $query = static::query()
            ->where('technician_id', $technicianId)
            ->whereNotIn('status', [
                WorkOrderStatus::COMPLETED->value,
                WorkOrderStatus::IN_PROGRESS->value,
            ]);

        return $query->update(['technician_id' => null, 'status' => WorkOrderStatus::PENDING->value]);
    }

    /**
     * Calculate the job status for display purposes
     * This method determines if a job is 'Pending' or 'Upcoming' based on preferred start date & time
     */
    public function calculateWorkOrderAssignmentStatus(): string
    {
        // Calculate status based on preferred start date & time in business timezone
        try {

            if (!$this->status->isPending()) {
                return $this->status->label();
            }

            $businessTimezone = $this->workOrder->getBusinessTimezone();

            // Create UTC datetime from stored values
            $startTime = $this->scheduled_time;

            if (strpos($startTime, ' ') !== false) {
                $startTime = Carbon::parse($startTime)->format($this->getTimeFormat());
            }
            $utcDateTime = Carbon::parse(
                $this->scheduled_date->format('Y-m-d') . ' ' . ($startTime ?? config('jobs.time.default_midnight')),
                'UTC'
            );

            // Convert to business timezone for comparison
            $localDateTime = $utcDateTime->setTimezone($businessTimezone);

            return $localDateTime->isPast() ? 'Pending' : 'Upcoming';
        } catch (\Exception $e) {
            Log::error('Failed to calculate job status', [
                'job_id' => $this->id,
                'error' => $e->getMessage()
            ]);
            return 'Pending';
        }
    }


    /**
     * Check if the work order is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === WorkOrderStatus::COMPLETED;
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

        return CompletedJobCustomer::where('work_order_id', $this->work_order_id)
            ->where('instance_id', $this->instance_id)
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
     * Get the formatted preferred start time in business timezone
     */
    public function getScheduledTimeFormattedAttribute(): ?string
    {
        if (!$this->scheduled_time) {
            return null;
        }

        try {
            // Convert UTC time back to business timezone
            $businessTimezone = $this->workOrder->getBusinessTimezone();

            // Create a datetime with the stored date and time (in UTC)
            $dateTimeString = $this->scheduled_date->format('Y-m-d') . ' ' . $this->scheduled_time;
            $utcDateTime = Carbon::parse($dateTimeString, 'UTC');

            // Convert to business timezone and return only the time
            return $utcDateTime->setTimezone($businessTimezone)->format($this->workOrder->getDisplayTimeFormat());
        } catch (\Exception $e) {
            return null;
        }
    }


    /**
     * Get the preferred start date in business timezone
     */
    public function getScheduledDateFormattedAttribute(): ?string
    {
        if (!$this->scheduled_date) {
            return null;
        }

        try {
            // Convert UTC date back to business timezone
            $businessTimezone = $this->workOrder->getBusinessTimezone();

            // Create a datetime with the stored date and time (in UTC)
            $dateTimeString = $this->scheduled_date->format($this->workOrder->getCustomDateFormat()) . ' ' . ($this->scheduled_time ?? $this->workOrder->getDefaultMidnightTime());
            $utcDateTime = Carbon::parse($dateTimeString, 'UTC');

            // Convert to business timezone and return only the date
            return $utcDateTime->setTimezone($businessTimezone)->format(config('datetime.formats.display.date'));
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get the end date in business timezone
     */
    public function getEndDateFormattedAttribute(): ?string
    {
        if (!$this->end_date) {
            return null;
        }

        try {
            // Convert UTC date back to business timezone
            $businessTimezone = $this->getBusinessTimezone();

            // Create a datetime with the stored date and time (in UTC)
            $dateTimeString = $this->end_date->format($this->getCustomDateFormat()) . ' ' . ($this->preferred_start_time ?? $this->getDefaultMidnightTime());
            $utcDateTime = Carbon::parse($dateTimeString, 'UTC');

            // Convert to business timezone and return only the date
            return $utcDateTime->setTimezone($businessTimezone)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }


    /**
     * Get the photo URL attribute
     */
    public function getPhotoUrlAttribute(): ?string
    {
        if (!$this->photo) {
            return null;
        }
        $path = $this->type == 'WO' ? StoragePaths::WORK_ORDER_IMAGES : StoragePaths::MAINTENANCE_IMAGES;
        $path .= $this->photo;
        // Generate a temporary signed URL for S3 images
        return FileHelper::getS3ImageUrl($path);
    }

    /**
     * Get the thumbnail photo URL attribute
     */
    public function getPhotoThumbUrlAttribute(): ?string
    {
        if (!$this->photo) {
            return null;
        }

        $path = $this->type == 'WO' ? StoragePaths::WORK_ORDER_THUMBNAIL_IMAGES : StoragePaths::MAINTENANCE_THUMBNAIL_IMAGES;

        $path = $path . $this->photo;
        // Generate a temporary signed URL for S3 images
        return FileHelper::getS3ImageUrl($path);
    }

    /**
     * Get job duration (in minutes) for this assignment via the parent work order.
     */
    public function getJobDurationAttribute(): int
    {
        return (int) config('datetime.durations.default');
    }

    public function workOrderNotification(): BelongsTo
    {
        return $this->belongsTo(WorkOrderCompleteNotification::class, 'instance_id', 'instance_id');
    }
}
