<?php

namespace App\Models\Business\WorkOrder\Concerns;

use Illuminate\Support\Facades\Auth;

trait WorkOrderBusinessTimezoneTrait
{
    /**
     * Get default timezone from config
     */
    private function getDefaultTimezone(): string
    {
        return config('datetime.timezones.default');
    }

    /**
     * Get UTC timezone from config
     */
    public function getUtcTimezone(): string
    {
        return config('datetime.timezones.utc');
    }

    /**
     * Get the business timezone.
     */
    public function getBusinessTimezone(): string
    {
        $user = Auth::guard('business')->user() ?? auth()->user();
        return $user?->business->timezone ?? $this->getDefaultTimezone();
    }
}
