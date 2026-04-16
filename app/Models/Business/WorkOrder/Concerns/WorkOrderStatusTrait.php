<?php

namespace App\Models\Business\WorkOrder\Concerns;

use App\Enums\WorkOrderStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

trait WorkOrderStatusTrait
{
    use WorkOrderTypesTrait;
    use WorkOrderBusinessTimezoneTrait;
    /**
     * Check if this is a work order
     */
    public function isWorkOrder(): bool
    {
        return $this->type === self::TYPE_WORK_ORDER;
    }

    /**
     * Check if this is a maintenance order
     */
    public function isMaintenanceOrder(): bool
    {
        return $this->type === self::TYPE_MAINTENANCE;
    }

    /**
     * Check if the work order is in progress
     */
    public function isInProgress(): bool
    {
        return $this->status === WorkOrderStatus::IN_PROGRESS;
    }

    /**
     * Check if the work order can be edited
     */
    public function canBeEdited(): bool
    {
        return !$this->isInProgress() && !$this->isCompleted();
    }

    /**
     * Check if the work order is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === WorkOrderStatus::COMPLETED;
    }

    /**
     * Get the type label
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_WORK_ORDER => 'Work Order',
            self::TYPE_MAINTENANCE => 'Maintenance Order',
            default => 'Unknown'
        };
    }

    /**
     * Calculate the job status for display purposes
     * This method determines if a job is 'Pending' or 'Upcoming' based on preferred start date & time
     */
    public function calculateJobStatus(): string
    {
        // Calculate status based on preferred start date & time in business timezone
        try {
            if (!$this->status->isPending()) {
                return $this->status->label();
            }

            $businessTimezone = $this->getBusinessTimezone();

            // Create UTC datetime from stored values
            $startTime = $this->preferred_start_time;

            if (strpos($startTime, ' ') !== false) {
                $startTime = Carbon::parse($startTime)->format($this->getTimeFormat());
            }

            $utcDateTime = Carbon::parse(
                $this->preferred_start_date->format('Y-m-d') . ' ' . ($startTime ?? config('jobs.time.default_midnight')),
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
     * Scope to filter by work orders only
     */
    public function scopeWorkOrders($query)
    {
        return $query->where('type', self::TYPE_WORK_ORDER);
    }

    /**
     * Scope to filter by maintenance orders only
     */
    public function scopeMaintenanceOrders($query)
    {
        return $query->where('type', self::TYPE_MAINTENANCE);
    }
}
