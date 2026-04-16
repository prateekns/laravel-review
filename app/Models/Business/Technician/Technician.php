<?php

namespace App\Models\Business\Technician;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Business\Business;
use App\Models\Business\WorkOrder\WorkOrderAssignment;
use App\Models\Business\WorkOrder\WorkOrder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Auth;

class Technician extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    public const ACTIVE_STATUS = 1;

    public const STATUS_INACTIVE = 0;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'business_id',
        'staff_id',
        'password',
        'first_name',
        'last_name',
        'isd_code',
        'phone',
        'email',
        'working_days',
        'status',
        'image',
        'jobs_synced_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'working_days' => 'array',
        'status' => 'boolean'
    ];

    /**
     * Get the business that owns the technician.
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get the skill type of this technician.
     */
    public function skillType(): BelongsTo
    {
        return $this->belongsTo(SkillType::class, 'skill_type');
    }

    public function skills()
    {
        return $this->belongsToMany(SkillType::class, 'technician_skills', 'technician_id', 'skill_type_id');
    }

    /**
     * Get the technician full name
     *
     * @return Attribute
     */
    protected function fullName(): Attribute
    {
        return Attribute::get(fn () => "{$this->first_name} {$this->last_name}");
    }

    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class, 'technician_id');
    }

    public function workOrderAssignments()
    {
        return $this->hasMany(WorkOrderAssignment::class, 'technician_id');
    }

    public function workOrderPositions()
    {
        return $this->hasMany(\App\Models\Business\WorkOrder\WorkOrderPosition::class, 'technician_id');
    }

    /**
     * Format phone number by masking middle digits
     * Examples:
     * +14155552671 → +1 41xxxxx2671
     * @return string Formatted phone number with masked digits
     */
    public function getMaskedPhoneAttribute(): string
    {
        $phone = $this->phone;
        if (!preg_match('/^\+(\d{1,4})(\d{2})(\d{3,})(\d{5})$/', $phone, $matches)) {
            return $phone;
        }

        // Extract country code (everything between + and the remaining number)
        [, $countryCode, $prefix, $middle, $last4] = $matches;
        // Create the masked middle section
        $maskedMiddle = str_repeat('x', strlen($middle));

        // Format: +[country_code] [prefix]xxxxx[last4]
        return "+{$countryCode} {$prefix}{$maskedMiddle}{$last4}";
    }

    /**
     * Get the business timezone or default
     */
    public function getBusinessTimezone(): string
    {
        $user = Auth::guard('business')->user() ?? auth()->user();
        $user->loadMissing('business');
        return $user->business?->timezone ?? $this->getDefaultTimezone();
    }

    /**
     * Check if the technician is available on a given date
     *
     * @param \Carbon\Carbon|string $date   Date in local business timezone
     * @return bool
     */
    public function isAvailableOn($date): bool
    {
        if (empty($this->working_days)) {
            return true;
        }

        $dateObj = Carbon::parse($date, $this->getBusinessTimezone());
        $dayOfWeek = strtolower($dateObj->format('l')); // e.g., "monday"
        $workingDays = (array)$this->working_days;

        return isset($workingDays[$dayOfWeek]) && $workingDays[$dayOfWeek] === true;
    }

    /**
     * Check if the technician has a conflicting job at the given date and time
     *
     * @param WorkOrder $job        WorkOrder object
     * @param array $times          Array of start and end times in Local business timezone
     * @return bool
     */
    public function hasConflictingJob($job, array $times): bool
    {
        $timezone = $this->getBusinessTimezone();

        // Parse start and end times
        $newJobStartDateTime = Carbon::parse($times['start'], $timezone);
        $newJobEndDateTime = Carbon::parse($times['end'], $timezone);

        // Convert to UTC for DB comparison
        $utcStartDateTime = $newJobStartDateTime->copy()->setTimezone('UTC');
        $utcEndDateTime = $newJobEndDateTime->copy()->setTimezone('UTC');

        $newJobDate = $utcStartDateTime->format(config('datetime.formats.date'));

        // Get all non-recurring jobs on the same date (recurring handled separately via ConflictDetector)
        $potentialConflicts = $this->workOrders()
            ->where('id', '!=', $job->id)
            ->where('is_active', true)
            ->where('is_recurring', false)
            ->whereNotIn('status', [
                \App\Enums\WorkOrderStatus::COMPLETED->value,
                \App\Enums\WorkOrderStatus::CANCELLED->value
            ])
            ->whereDate('preferred_start_date', $newJobDate)
            ->get();

        // Check each job for time overlap
        foreach ($potentialConflicts as $existingJob) {
            // Get existing job's time range in UTC
            $existingStartDateTime = Carbon::parse(
                $existingJob->preferred_start_date->format(config('datetime.formats.date')) . ' ' .
                    ($existingJob->preferred_start_time ?? config('datetime.defaults.start')),
                'UTC'
            );
            $existingDuration = $existingJob->job_duration ?? config('datetime.durations.default');
            $existingEndDateTime = $existingStartDateTime->copy()->addMinutes($existingDuration);

            // Check for overlap
            if ($existingStartDateTime->lt($utcEndDateTime) &&  $existingEndDateTime->gt($utcStartDateTime)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Create a lookup map for job instances
     *
     * @param array $instances          Array of instances (generated by calculateInstances)
     * @param WorkOrder $job            WorkOrder object
     * @return array                    Dates are in local business timezone
     */
    private function createInstanceMap($instances, $job): array
    {
        $map = [];

        // Handle non-recurring jobs (single WorkOrder object)
        if (!$job->is_recurring) {
            // For non-recurring jobs, calculateInstances returns the WorkOrder object itself
            $firstInstance = $instances[0];
            if (isset($firstInstance['preferred_start_date'])) {
                // Handle case where preferred_start_date might be a string or Carbon object
                $preferredStartDate = Carbon::parse($firstInstance['preferred_start_date'], 'UTC')->format(config('datetime.formats.date'));
                $preferredStartDateTime = Carbon::parse($preferredStartDate . ' ' . $firstInstance['preferred_start_time'], 'UTC');
                $preferredStartTimeLocal = $preferredStartDateTime->copy()->setTimezone($this->getBusinessTimezone());

                $date = $preferredStartTimeLocal->format(config('datetime.formats.date'));
                $time = $preferredStartTimeLocal->format(config('datetime.formats.time'));

                $key = $date . '_' . $time;
                $map[$key] = [
                    'date' => $date,
                    'time' => $time,
                    'work_order_id' => $firstInstance['id'],
                    'job_duration' => $job->job_duration ?? config('datetime.durations.default')
                ];
            }
        } else {
            // Handle recurring jobs (array of instances)
            $instanceArray = is_array($instances) ? $instances : $instances->toArray();
            foreach ($instanceArray as $instance) {
                $key = $instance['date_local'] . '_' . $instance['time_local'];
                $map[$key] = [
                    'date' => $instance['date_local'],
                    'time' => $instance['time_local'],
                    'work_order_id' => $instance['work_order_id'],
                    'job_duration' => $job->job_duration ?? config('datetime.durations.default')
                ];
            }
        }

        return $map;
    }

    /**
     * Get active assigned jobs for technician
     */
    private function getActiveAssignedJobs($jobToExclude): Collection
    {
        return $this->workOrders()
            ->where('id', '!=', $jobToExclude->id)
            ->where('is_active', true)
            ->whereNotNull('technician_id')
            ->whereNotIn('status', [
                \App\Enums\WorkOrderStatus::COMPLETED->value,
                \App\Enums\WorkOrderStatus::CANCELLED->value
            ])
            ->get();
    }

    /**
     * Check for conflicts with recurring job
     *
     * @param WorkOrder $assignedJob
     * @param Carbon $startDate             Date in local business timezone
     * @param Carbon $endDate               Date in local business timezone
     * @param array $newJobInstanceMap      Dates are in local business timezone
     * @return array
     */
    private function checkRecurringJobConflicts($assignedJob, $startDate, $endDate, array $newJobInstanceMap): array
    {
        if (config('logging.scheduler.detailed_conflict_logs')) {
            Log::info('Check recurring job conflicts', ['assignedJob' => $assignedJob, 'startDate' => $startDate, 'endDate' => $endDate, 'newJobInstanceMap' => $newJobInstanceMap]);
        }

        $conflicts = [];
        $assignedInstances = $assignedJob->calculateInstances($startDate->format(config('datetime.formats.date')), $endDate->format(config('datetime.formats.date')));

        if (config('logging.scheduler.detailed_conflict_logs')) {
            Log::info('Assigned instances', ['assignedInstances' => $assignedInstances, 'count' => count($assignedInstances)]);
        }

        $assignedDuration = $assignedJob->job_duration ?? config('datetime.durations.default');

        // get assignments for this job
        $assignments = WorkOrderAssignment::where('work_order_id', $assignedJob->id)->get();

        foreach ($assignedInstances as $assignedInstance) {
            // Check if this specific instance is actually assigned to this technician
            // (considering individual instance reassignments)
            $instanceId = $assignedInstance['instance_id'];
            $actualAssignedTechId = $this->resolveWinnerForInstance($assignedJob, $assignments, $instanceId, $assignedInstance['date'], $assignedInstance['time']);

            // Skip if this instance is not actually assigned to this technician
            if ($actualAssignedTechId !== $this->id) {
                continue;
            }

            // calculateInstances returns local business timezone date and time
            $assignedStartLocal = Carbon::parse($assignedInstance['date_local'] . ' ' . $assignedInstance['time_local'], $this->getBusinessTimezone());
            $assignedEndLocal = $assignedStartLocal->copy()->addMinutes($assignedDuration);

            foreach ($newJobInstanceMap as $newInstance) {
                $newStartLocal = Carbon::parse($newInstance['date'] . ' ' . $newInstance['time'], $this->getBusinessTimezone());
                $newEndLocal = $newStartLocal->copy()->addMinutes($newInstance['job_duration']);

                if ($assignedStartLocal->lt($newEndLocal) && $assignedEndLocal->gt($newStartLocal)) {
                    $conflicts[] = [
                        'date' => $assignedInstance['date_local'],
                        'time' => $assignedInstance['time_local'],
                        'conflicting_job_name' => $assignedJob->name,
                        'conflicting_job_id' => $assignedJob->id
                    ];
                    break; // Found a conflict, no need to check other instances
                }
            }
        }

        return $conflicts;
    }

    /**
     * Resolve which technician "owns" a specific instance considering reassignments
     *
     * @param WorkOrder $job
     * @param Collection $assignments       Assignments collection
     * @param string $instanceId            Instance ID
     * @param string $date                  Date in UTC timezone
     * @param string $time                  Time in UTC timezone
     * @return int|null
     */
    private function resolveWinnerForInstance($job, Collection $assignments, $instanceId, $date, $time): ?int
    {
        // Check specific instance assignments first
        foreach ($assignments as $assignment) {
            if (!empty($assignment->instance_id) && empty($assignment->recurrence_rule) && (string)$assignment->instance_id === (string)$instanceId) {
                return (int)$assignment->technician_id;
            }
        }

        // Check recurring assignments
        $utcDateTime = Carbon::parse($date . ' ' . $time, 'UTC');
        $recurringAssignments = $assignments->filter(fn ($a) => !empty($a->recurrence_rule))
            ->sortByDesc(fn ($a) => $a->effective_from ?? $a->scheduled_date);

        foreach ($recurringAssignments as $assignment) {
            $fromUtc = Carbon::parse(($assignment->effective_from ?? $assignment->scheduled_date)->format(config('datetime.formats.date')) . ' ' . config('datetime.defaults.midnight'), 'UTC');
            $untilUtc = $assignment->effective_until
                ? Carbon::parse($assignment->effective_until->format(config('datetime.formats.date')) . ' ' . config('datetime.defaults.end_of_day'), 'UTC')
                : null;

            if ($utcDateTime->gte($fromUtc) && ($untilUtc === null || $utcDateTime->lte($untilUtc))) {
                return (int)$assignment->technician_id;
            }
        }

        // Fallback to top-level assignment
        return $job->technician_id;
    }

    /**
     * Check for conflicts with regular job
     *
     * @param WorkOrder $assignedJob        workOrders collection
     * @param array $newJobInstanceMap      Dates are in local business timezone
     * @param int $newJobDuration           Job duration in minutes
     * @return array
     */
    private function checkRegularJobConflicts($assignedJob, array $newJobInstanceMap, int $newJobDuration): array
    {
        $conflicts = [];
        $assignedDate = $assignedJob->preferred_start_date->format(config('datetime.formats.date'));
        $assignedTime = $assignedJob->preferred_start_time ?? config('datetime.defaults.start');
        $assignedDuration = $assignedJob->job_duration ?? config('datetime.durations.default');

        // date and time in UTC timezone
        $assignedStartUtc = Carbon::parse($assignedDate . ' ' . $assignedTime, 'UTC');
        // convert to local business timezone
        $assignedStartLocal = $assignedStartUtc->copy()->setTimezone($this->getBusinessTimezone());
        $assignedEndLocal = $assignedStartLocal->copy()->addMinutes($assignedDuration);

        foreach ($newJobInstanceMap as $instance) {
            $instDateYmd = Carbon::parse(($instance['date'] ?? ''), $this->getBusinessTimezone())->format(config('datetime.formats.date'));
            $instStartLocal = Carbon::parse($instDateYmd . ' ' . ($instance['time'] ?? config('datetime.defaults.midnight')), $this->getBusinessTimezone());
            $instEndLocal = $instStartLocal->copy()->addMinutes($newJobDuration);

            if ($assignedStartLocal->lt($instEndLocal) && $assignedEndLocal->gt($instStartLocal)) {
                $conflicts[] = [
                    'date' => $assignedDate,
                    'time' => $assignedTime,
                    'conflicting_job_name' => $assignedJob->name,
                    'conflicting_job_id' => $assignedJob->id
                ];

                break;
            }
        }

        return $conflicts;
    }

    /**
     * Process job instances and check for conflicts
     *
     * @param WorkOrder $job
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param SupportCollection $newJobInstances
     * @return array
     */
    private function processJobConflicts(
        WorkOrder $job,
        Carbon $startDate,
        Carbon $endDate,
        SupportCollection $newJobInstances
    ): array {
        if ($newJobInstances->isEmpty()) {
            return [];
        }

        $newJobInstanceMap = $this->createInstanceMap($newJobInstances->toArray(), $job);
        $assignedJobs = $this->getActiveAssignedJobs($job);
        $newJobDuration = $job->job_duration ?? config('datetime.durations.default');

        foreach ($assignedJobs as $assignedJob) {
            $conflicts = $assignedJob->is_recurring
                ? $this->checkRecurringJobConflicts($assignedJob, $startDate, $endDate, $newJobInstanceMap)
                : $this->checkRegularJobConflicts($assignedJob, $newJobInstanceMap, $newJobDuration);

            if (!empty($conflicts)) {
                return $conflicts;
            }
        }

        return [];
    }

    /**
     * Check if the technician has conflicts with recurring job instances
     *
     * @param WorkOrder $job
     * @param Carbon $startDate                     Date in local business timezone
     * @param Carbon $endDate                       Date in local business timezone
     * @param SupportCollection $newJobInstances    Instances collection
     * @return array
     */
    public function hasConflictingRecurringJob($job, $startDate, $endDate, ?SupportCollection $newJobInstances = null): array
    {
        try {
            $instances = $newJobInstances ?? $job->calculateInstances(
                $startDate->format(config('datetime.formats.date')),
                $endDate->format(config('datetime.formats.date'))
            );

            $conflicts = $this->processJobConflicts($job, $startDate, $endDate, $instances);

            return [
                'has_conflicts' => !empty($conflicts),
                'conflicts' => $conflicts
            ];
        } catch (\Exception $e) {
            if (config('logging.scheduler.detailed_conflict_logs')) {
                Log::info('Has conflicting recurring job error', ['error' => $e->getMessage()]);
            }
            return ['has_conflicts' => false, 'conflicts' => []];
        }
    }
}
