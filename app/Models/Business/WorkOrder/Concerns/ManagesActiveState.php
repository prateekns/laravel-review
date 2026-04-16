<?php

namespace App\Models\Business\WorkOrder\Concerns;

use Illuminate\Database\Eloquent\Casts\Attribute;

trait ManagesActiveState
{
    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function isInactive(): bool
    {
        return !$this->is_active;
    }

    protected function isActiveText(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->is_active
                ? __('business.customer.status.active')
                : __('business.customer.status.inactive')
        );
    }

    protected function workTypeText(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->is_recurring
                ? __('business.work_orders.work_type.recurring')
                : __('business.work_orders.work_type.non_recurring')
        );
    }
}
