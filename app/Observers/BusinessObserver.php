<?php

namespace App\Observers;

use App\Models\Business\Business;
use Illuminate\Support\Facades\DB;

class BusinessObserver
{
    /**
     * Handle the Business "deleting" event.
     */
    public function deleting(Business $business): void
    {
        DB::transaction(function () use ($business) {
            $business->users()->update(['deleted_at' => now()]);
            $business->technicians()->update(['deleted_at' => now()]);
        });
    }

    /**
     * Handle the Business "restoring" event.
     */
    public function restoring(Business $business): void
    {
        DB::transaction(function () use ($business) {
            $business->users()->withTrashed()->update(['deleted_at' => null]);
        });
    }

    /**
     * Handle the Business "updating" event.
     */
    public function updating(Business $business): void
    {
        if ($business->isDirty('status')) {
            DB::transaction(function () use ($business) {
                $business->users()->update(['status' => $business->status]);
                $business->technicians()->update(['status' => $business->status]);
            });
        }
    }
}
