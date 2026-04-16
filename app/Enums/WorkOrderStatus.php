<?php

namespace App\Enums;

enum WorkOrderStatus: int
{
    case UPCOMING = 0;
    case PENDING = 1;
    case SCHEDULED = 2;
    case IN_PROGRESS = 3;
    case COMPLETED = 4;
    case CANCELLED = 5;

    public function label(): string
    {
        return match($this) {
            self::PENDING => __('business.work_orders.status.pending'),
            self::SCHEDULED => __('business.work_orders.status.scheduled'),
            self::IN_PROGRESS => __('business.work_orders.status.in_progress'),
            self::COMPLETED => __('business.work_orders.status.completed'),
            self::CANCELLED => __('business.work_orders.status.cancelled'),
            self::UPCOMING => __('business.work_orders.status.upcoming'),
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PENDING => 'status-pending',
            self::SCHEDULED => 'status-scheduled',
            self::IN_PROGRESS => 'status-in-progress',
            self::COMPLETED => 'status-completed',
            self::CANCELLED => 'status-cancelled',
            self::UPCOMING => 'status-upcoming',
        };
    }

    public function isPending(): bool
    {
        return $this === self::PENDING;
    }

    public function isScheduled(): bool
    {
        return $this === self::SCHEDULED;
    }

    public function isInProgress(): bool
    {
        return $this === self::IN_PROGRESS;
    }

    public function isCompleted(): bool
    {
        return $this === self::COMPLETED;
    }

    public function isCancelled(): bool
    {
        return $this === self::CANCELLED;
    }

    public function isUpcoming(): bool
    {
        return $this === self::UPCOMING;
    }
}
