<?php

namespace App\Models\Business\WorkOrder\Concerns;

use Carbon\Carbon;
use Recurr\Rule;

trait WorkOrderRecurrenceTrait
{
    use WorkOrderBusinessTimezoneTrait;
    /**
     * Configure weekly recurrence rule
     */
    protected function configureWeeklyRule(Rule $rule, int $maxInterval = 4): void
    {
        $rule->setFreq('WEEKLY');
        $interval = min($maxInterval, $this->repeat_after ?? 1);
        if ($interval > 1) {
            $rule->setInterval($interval);
        }

        if ($this->selected_days && is_array($this->selected_days)) {
            $byDay = array_map(
                fn ($day) => strtoupper(substr($day, 0, 2)),
                $this->selected_days
            );
            $rule->setByDay($byDay);
        }
    }

    /**
     * Configure monthly recurrence rule
     */
    protected function configureMonthlyRule(Rule $rule): void
    {
        $rule->setFreq('MONTHLY');
        $interval = min(12, $this->repeat_after ?? 1);
        if ($interval > 1) {
            $rule->setInterval($interval);
        }

        if ($this->monthly_day_type && $this->monthly_day_of_week) {
            $dayNumber = $this->getMonthlyDayNumber();
            $dayOfWeek = strtoupper(substr($this->monthly_day_of_week, 0, 2));
            $rule->setByDay([$dayNumber . $dayOfWeek]);
        } else {
            $rule->setByMonthDay([Carbon::parse($this->preferred_start_date)->day]);
        }
    }

    /**
     * Get start date and time for recurrence rule
     */
    public function getRecurrenceStartDateTime(): Carbon
    {
        // Create datetime in UTC since that's how it's stored
        $startDate = Carbon::parse($this->preferred_start_date, 'UTC');
        $startTime = $this->getRecurrenceStartTime();

        // Convert to business timezone for RRULE
        return $startDate->copy()
            ->setTimeFromTimeString($startTime)
            ->setTimezone($this->getBusinessTimezone());
    }

    /**
     * Get the start time for recurrence calculation
     */
    public function getRecurrenceStartTime(): string
    {
        $startTime = $this->preferred_start_time;
        if ($startTime && strpos($startTime, ' ') !== false) {
            return Carbon::parse($startTime)->format($this->getTimeFormat());
        }
        return $startTime ?: $this->getDefaultStartTime();
    }

    /**
     * Generate RRULE string from current recurrence fields
     */
    public function generateRRule(): ?string
    {
        if (!$this->is_recurring) {
            return null;
        }

        $rule = new Rule();
        $rule->setStartDate($this->getRecurrenceStartDateTime()); // Use timezone from Carbon instance

        // Set end date if provided
        if ($this->end_date) {
            // Convert UTC end date to local timezone for RRULE
            $endDateUtc = Carbon::parse($this->end_date->format(config('datetime.formats.date')) . ' ' . config('datetime.defaults.end_of_day'), 'UTC');
            $endDateLocal = $endDateUtc->copy()->setTimezone($this->getBusinessTimezone());
            $rule->setUntil($endDateLocal); // Use timezone from Carbon instance
        }

        // Configure frequency and interval
        switch ($this->frequency) {
            case 'daily':
                $rule->setFreq('DAILY');
                break;

            case 'weekly':
                $this->configureWeeklyRule($rule);
                break;

            case 'semi_monthly':
                $this->configureWeeklyRule($rule, 2);
                break;

            case 'monthly':
                $this->configureMonthlyRule($rule);
                break;

            default:
                return null;
        }

        return $rule->getString();
    }

    /**
     * Get the monthly day number for RRULE
     */
    protected function getMonthlyDayNumber(): int
    {
        $dayNumber = 0;

        switch ($this->monthly_day_type) {
            case 'first':
                $dayNumber = 1;
                break;

            case 'second':
                $dayNumber = 2;
                break;

            case 'third':
                $dayNumber = 3;
                break;

            case 'fourth':
                $dayNumber = 4;
                break;

            case 'last':
                $dayNumber = -1;
                break;

            default:
                $dayNumber = 1;
        }

        return $dayNumber;
    }

    /**
     * Get frequency label
     */
    public function getFrequencyLabelAttribute(): ?string
    {
        return match ($this->frequency) {
            'daily' => 'Daily',
            'weekly' => 'Weekly',
            'semi_monthly' => 'Semi-Monthly',
            'monthly' => 'Monthly',
            default => null
        };
    }

    /**
     * Get repeat after label
     */
    public function getRepeatAfterLabelAttribute(): ?string
    {
        if (!$this->repeat_after) {
            return null;
        }

        return match ($this->frequency) {
            'weekly' => $this->repeat_after . ' Week' . ($this->repeat_after > 1 ? 's' : ''),
            'monthly' => $this->repeat_after . ' Month' . ($this->repeat_after > 1 ? 's' : ''),
            default => null
        };
    }

    /**
     * Get selected days label
     */
    public function getSelectedDaysLabelAttribute(): ?string
    {
        if (!$this->selected_days || !is_array($this->selected_days)) {
            return null;
        }

        $dayLabels = [
            'monday' => 'Monday',
            'tuesday' => 'Tuesday',
            'wednesday' => 'Wednesday',
            'thursday' => 'Thursday',
            'friday' => 'Friday',
            'saturday' => 'Saturday',
            'sunday' => 'Sunday'
        ];

        $selectedDayLabels = array_map(fn ($day) => $dayLabels[$day] ?? $day, $this->selected_days);
        return implode(', ', $selectedDayLabels);
    }

    /**
     * Get monthly day type label
     */
    public function getMonthlyDayTypeLabelAttribute(): ?string
    {
        return match ($this->monthly_day_type) {
            'first' => 'First',
            'second' => 'Second',
            'third' => 'Third',
            'fourth' => 'Fourth',
            default => null
        };
    }

    /**
     * Get monthly day of week label
     */
    public function getMonthlyDayOfWeekLabelAttribute(): ?string
    {
        return match ($this->monthly_day_of_week) {
            'monday' => 'Monday',
            'tuesday' => 'Tuesday',
            'wednesday' => 'Wednesday',
            'thursday' => 'Thursday',
            'friday' => 'Friday',
            'saturday' => 'Saturday',
            'sunday' => 'Sunday',
            default => null
        };
    }

    /**
     * Get frequency label from rrule string
     */
    public function getFrequencyFromRruleAttribute(): ?string
    {
        $frequency = null;

        if ($this->is_recurring) {
            $rrule = $this->recurrence_rule['rrule'] ?? null;

            if ($rrule) {
                $rule = new Rule($rrule);
                $frequency = strtolower($rule->getFreqAsText());
            }
        }

        return $frequency;
    }
}
