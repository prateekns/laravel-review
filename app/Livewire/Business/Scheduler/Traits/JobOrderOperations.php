<?php

declare(strict_types=1);

namespace App\Livewire\Business\Scheduler\Traits;

use App\Models\Business\WorkOrder\WorkOrder;
use App\Models\Business\WorkOrder\WorkOrderPosition;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait JobOrderOperations
{
    /**
     * Update the order of a job in the technician's schedule
     *
     * @param int $jobId
     * @param int $techId
     * @param string $dateIso
     * @param int $newPosition
     * @return void
     */
    public function updateJobOrder(
        int $jobId,
        int $techId,
        string $dateIso,
        int $newPosition
    ): void {
        try {
            DB::beginTransaction();

            // Validate job belongs to current business
            $businessId = auth()->guard('business')->user()->business_id;
            WorkOrder::where('business_id', $businessId)->findOrFail($jobId);

            // Technician-wide ordering (ignore date): build from all visible jobs across week
            $allJobs = collect($this->technicians)
                ->firstWhere('id', (int) $techId)['schedule'] ?? [];
            $flatJobs = [];
            foreach ($allJobs as $jobs) {
                if (!is_array($jobs) || isset($jobs['isNotAvailable'])) {
                    continue;
                }
                foreach ($jobs as $job) {
                    $flatJobs[$job['id']] = $job; // de-duplicate by work_order_id
                }
            }

            // Ensure every job has a position row for this technician (use any day; we ignore date for sort)
            $existing = WorkOrderPosition::query()
                ->where('technician_id', (int) $techId)
                ->pluck('position', 'work_order_id');

            $nextPos = $existing->isEmpty() ? 0 : ($existing->max() + 1);
            foreach (array_keys($flatJobs) as $wid) {
                if (!isset($existing[$wid])) {
                    WorkOrderPosition::create([
                        'work_order_id' => (int) $wid,
                        'technician_id' => (int) $techId,
                        // store the date of the first visible occurrence; order is technician-wide
                        'scheduled_date' => $dateIso,
                        'position' => $nextPos++,
                        'instance_id' => $flatJobs[$wid]['instance_id'] ?? null,
                    ]);
                }
            }

            // Re-fetch technician-wide ordering counts
            $total = (int) WorkOrderPosition::query()
                ->where('technician_id', (int) $techId)
                ->count();

            // Determine current (old) position from DB if present
            $existingOld = WorkOrderPosition::query()
                ->where('technician_id', (int) $techId)
                ->where('work_order_id', $jobId)
                ->value('position');

            if ($existingOld === null) {
                // If the job has no row yet, append and treat its old as end
                $existingOld = $total; // append to end
                WorkOrderPosition::create([
                    'work_order_id' => (int) $jobId,
                    'technician_id' => (int) $techId,
                    'scheduled_date' => $dateIso,
                    'position' => (int) $existingOld,
                ]);
                $total++;
            }

            $oldPos = (int) $existingOld;
            $newPos = max(0, min((int) $newPosition, max(0, $total - 1)));

            if ($newPos === $oldPos) {
                DB::commit();
                $this->notify('success', 'business.scheduler.job_order_updated_successfully');
                $this->dispatch('refreshCalendar');
                return;
            }

            // Shift the range in a single SQL update
            if ($newPos < $oldPos) {
                // Move up: shift down [newPos, oldPos-1]
                DB::table('work_order_positions')
                    ->where('technician_id', (int) $techId)
                    ->whereBetween('position', [$newPos, $oldPos - 1])
                    ->increment('position');
            } else {
                // Move down: shift up [oldPos+1, newPos]
                DB::table('work_order_positions')
                    ->where('technician_id', (int) $techId)
                    ->whereBetween('position', [$oldPos + 1, $newPos])
                    ->decrement('position');
            }

            // Place the moved job at its new position
            WorkOrderPosition::query()
                ->where('technician_id', (int) $techId)
                ->where('work_order_id', (int) $jobId)
                ->update(['position' => $newPos]);

            DB::commit();

            $this->notify('success', 'business.scheduler.job_order_updated_successfully');
            $this->dispatch('refreshCalendar');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update job order', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->notify('error', 'business.scheduler.job_order_updated_error');
        }
    }
}
