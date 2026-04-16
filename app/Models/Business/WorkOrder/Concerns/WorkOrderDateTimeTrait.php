<?php

namespace App\Models\Business\WorkOrder\Concerns;

use Carbon\Carbon;

trait WorkOrderDateTimeTrait
{
    use WorkOrderBusinessTimezoneTrait;
    /**
     * Get time format from config
     */
    public function getTimeFormat(): string
    {
        return config('datetime.formats.time');
    }

    /**
     * Get time format from config
     */
    public function getDisplayTimeFormat(): string
    {
        return config('datetime.formats.display.time');
    }

    /**
     * Get date format from config
     */
    public function getCustomDateFormat(): string
    {
        return config('datetime.formats.date');
    }

    /**
     * Get datetime format from config
     */
    public function getDateTimeFormat(): string
    {
        return config('datetime.formats.datetime');
    }

    /**
     * Get default midnight time from config
     */
    public function getDefaultMidnightTime(): string
    {
        return config('datetime.defaults.midnight');
    }

    /**
     * Get default start time from config
     */
    public function getDefaultStartTime(): string
    {
        return config('datetime.defaults.start');
    }

    /**
     * Get the formatted preferred start time in business timezone
     */
    public function getPreferredStartTimeFormattedAttribute(): ?string
    {
        if (!$this->preferred_start_time) {
            return null;
        }

        try {
            // Convert UTC time back to business timezone
            $businessTimezone = $this->getBusinessTimezone();

            // Create a datetime with the stored date and time (in UTC)
            $dateTimeString = $this->preferred_start_date->format('Y-m-d') . ' ' . $this->preferred_start_time;
            $utcDateTime = Carbon::parse($dateTimeString, 'UTC');

            // Convert to business timezone and return only the time (display format)
            return $utcDateTime->setTimezone($businessTimezone)->format($this->getDisplayTimeFormat());
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get the preferred start date in business timezone
     */
    public function getPreferredStartDateFormattedAttribute(): ?string
    {
        if (!$this->preferred_start_date) {
            return null;
        }

        try {
            // Convert UTC date back to business timezone
            $businessTimezone = $this->getBusinessTimezone();

            // Create a datetime with the stored date and time (in UTC)
            $dateTimeString = $this->preferred_start_date->format($this->getCustomDateFormat()) . ' ' . ($this->preferred_start_time ?? $this->getDefaultMidnightTime());
            $utcDateTime = Carbon::parse($dateTimeString, 'UTC');

            // Convert to business timezone and return only the date
            return $utcDateTime->setTimezone($businessTimezone)->format('Y-m-d');
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
     * Set the preferred start time (already converted to UTC in Form Request)
     */
    public function setPreferredStartTimeAttribute($value): void
    {
        if (is_array($value)) {
            $value = $value[0] ?? null;
        }

        if (!$value) {
            $this->attributes['preferred_start_time'] = null;
            return;
        }

        // Handle case where time might be a full datetime
        if (strpos($value, ' ') !== false) {
            $value = Carbon::parse($value)->format($this->getTimeFormat());
        }

        // Value is already in UTC format from Form Request
        $this->attributes['preferred_start_time'] = $value;
    }

    /**
     * Set the preferred start date (already converted to UTC in Form Request)
     */
    public function setPreferredStartDateAttribute($value): void
    {
        if (!$value) {
            $this->attributes['preferred_start_date'] = null;
            return;
        }

        // Value is already in UTC format from Form Request
        $this->attributes['preferred_start_date'] = $value;
    }
}
