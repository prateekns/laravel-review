<?php

namespace App\Models\Business\Concerns;

use Illuminate\Database\Eloquent\Casts\Attribute;

trait ManagesActiveState
{
    public const STATUS_ACTIVE = true;
    public const STATUS_INACTIVE = false;

    protected function statusText(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status === self::STATUS_ACTIVE
                ? __('business.customer.status.active')
                : __('business.customer.status.inactive')
        );
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', self::STATUS_INACTIVE);
    }
}
