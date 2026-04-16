<?php

declare(strict_types=1);

namespace App\Models\Business\WorkOrder\Concerns;

use App\Models\Business\WorkOrder\WorkOrderAssignment;
use App\Services\Business\WorkOrder\WorkOrderInstanceService;
use App\Services\Business\Scheduler\DateTimeService;
use App\Models\Business\Technician\Technician;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * Instance and assignment helper methods for WorkOrder model.
 */
trait WorkOrderInstancesTrait
{
    /**
     * Get job instances for recurring jobs
     */
    public function jobInstances(): HasMany
    {
        return $this->hasMany(WorkOrderAssignment::class);
    }

    /**
     * Get assignments for this work order
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(WorkOrderAssignment::class);
    }

    /**
     * Generate unique instance ID
     */
    public function generateInstanceId($date): string
    {
        $dateStr = Carbon::parse($date)->format('Ymd');
        return "{$this->id}{$dateStr}";
    }

    /**
     * Generate unique job ID
     */
    public function generateJobId(): string
    {
        $dateStr = Carbon::parse($this->preferred_start_date)->format('Ymd');
        return "{$this->id}{$dateStr}";
    }

    /**
     * Calculate instances with unique IDs for a date range using RRULE
     *
     * @param string $startDate   Date in UTC timezone
     * @param string $endDate     Date in UTC timezone
     * @param bool $api           Whether to return API format
     * @param bool $firstOnly     Whether to return only the first instance
     * @return Collection         Collection of instances
     */
    public function calculateInstances($startDate, $endDate, bool $api = false, bool $firstOnly = false): Collection
    {
        $instanceService = app(WorkOrderInstanceService::class);
        return $instanceService->calculateInstances($this, $startDate, $endDate, $api, $firstOnly);
    }

    private function getCallerFunction()
    {
        $backtrace = debug_backtrace();
        // The calling function is at index 1 in the backtrace
        return $backtrace[2]['function'] . '() in ' . $backtrace[2]['file'] . ' on line ' . $backtrace[2]['line'];
    }

    /**
     * Check if specific instance is assigned
     */
    public function isInstanceAssigned($instanceId): bool
    {
        return $this->jobInstances()
            ->where('instance_id', $instanceId)
            ->exists();
    }

    /**
     * Get specific instance status
     */
    public function getInstanceStatus($instanceId): ?string
    {
        $instance = $this->jobInstances()
            ->where('instance_id', $instanceId)
            ->first();

        return $instance ? $instance->status : null;
    }

    /**
     * Resolve the technician responsible for the instance occurring on the given business-local date.
     * Returns reassigned instance technician if present; else RRULE window technician; else main job technician.
     */
    public function resolveTechnicianForDate(string $businessYmd): ?Technician
    {
        /** @var DateTimeService $dt */
        $dt = app(DateTimeService::class);
        $businessTz = $dt->getBusinessTimezone();

        // Generate instance for that business date
        $instances = $this->calculateInstances($businessYmd, $businessYmd);
        $instance = $instances->first();
        if ($instance) {
            $instanceId = $instance['instance_id'] ?? null;
            if ($instanceId) {
                $byInstance = WorkOrderAssignment::where('work_order_id', $this->id)
                    ->whereNull('recurrence_rule')
                    ->where('instance_id', $instanceId)
                    ->with('technician')
                    ->first();
                if ($byInstance && $byInstance->technician) {
                    return $byInstance->technician;
                }
            }
        }

        // Resolve RRULE window covering this date
        $utcYmd = Carbon::parse($businessYmd . ' ' . config('datetime.defaults.midnight'), $businessTz)
            ->copy()
            ->setTimezone('UTC')
            ->format(config('datetime.formats.date'));

        $window = WorkOrderAssignment::where('work_order_id', $this->id)
            ->whereNotNull('recurrence_rule')
            ->whereDate('effective_from', '<=', $utcYmd)
            ->where(function ($q) use ($utcYmd) {
                $q->whereNull('effective_until')->orWhereDate('effective_until', '>=', $utcYmd);
            })
            ->with('technician')
            ->orderBy('effective_from', 'desc')
            ->first();

        if ($window && $window->technician) {
            return $window->technician;
        }

        // Fallback to main job technician
        return $this->technician;
    }

    /**
     * Accessor: current_technician – resolves today's responsible technician in business timezone.
     */
    public function getCurrentTechnicianAttribute(): ?Technician
    {
        /** @var DateTimeService $dt */
        $dt = app(DateTimeService::class);
        if ($this->is_recurring) {
            $todayBusiness = $dt->getBusinessTimezoneNow()->format(config('datetime.formats.date'));
            return $this->resolveTechnicianForDate($todayBusiness) ?: $this->technician;
        }
        return $this->technician;
    }

    public function getNextOccurrenceAttribute(): ?string
    {
        $businessTimezone = $this->getBusinessTimezone();

        // Non-recurring: show the preferred date in business timezone
        if (!$this->preferred_start_date) {
            return null;
        }

        $time = $this->preferred_start_time ?? $this->getDefaultMidnightTime();
        $utc = Carbon::parse($this->preferred_start_date->format($this->getCustomDateFormat()) . ' ' . $time, $this->getUtcTimezone());
        $returnDate = $utc->setTimezone($businessTimezone)->format(config('datetime.formats.display.datetime'));

        if ($this->is_recurring) {
            // Recurring: compute next future instance in business timezone
            $nowLocal = Carbon::now($businessTimezone);
            $startDateLocal = $nowLocal->copy()->format(config('datetime.formats.date'));
            $endDateLocal = $nowLocal->copy()->addDays(30)->format(config('datetime.formats.date'));
            $instances = $this->calculateInstances($startDateLocal, $endDateLocal, false, true); // Get only first instance

            foreach ($instances as $instance) {
                $instDateYmd = Carbon::parse(($instance['date'] ?? ''), $this->getUtcTimezone())->format(config('datetime.formats.date'));
                $instanceLocal = Carbon::parse($instDateYmd . ' ' . ($instance['time'] ?? $this->getDefaultMidnightTime()), $this->getUtcTimezone())
                    ->setTimezone($businessTimezone);
                if ($instanceLocal->isToday($nowLocal) || $instanceLocal->greaterThan($nowLocal)) {
                    $returnDate = $instanceLocal->format(config('datetime.formats.display.datetime'));
                    break;
                }
            }
        }

        return $returnDate;
    }
}
