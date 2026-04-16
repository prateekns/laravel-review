<?php

declare(strict_types=1);

namespace App\Livewire\Business\Scheduler\Traits;

use App\Models\Business\WorkOrder\WorkOrder;
use Illuminate\Support\Facades\Log;

trait CancellationOperations
{
    public array $pendingCancellation = [];
    public bool $showCancelConfirmModal = false;

    /**
     * Prepare job cancellation
     *
     * @param int $jobId
     * @param int $instanceId
     * @param string $dateTime
     * @return void
     */
    public function cancelJob(int $jobId, int $instanceId, string $dateTime): void
    {
        try {
            $job = WorkOrder::where('business_id', auth()->guard('business')->user()->business_id)
                ->findOrFail($jobId);

            $this->pendingCancellation = [
                'jobId' => $jobId,
                'instanceId' => $instanceId,
                'dateTime' => $dateTime,
                'jobName' => $job->name
            ];

            $this->showCancelConfirmModal = true;
        } catch (\Exception $e) {
            Log::error('Failed to prepare job cancellation', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->notify('error', 'business.scheduler.job_cancelled_error');
        }
    }

    /**
     * Confirm the pending job cancellation
     *
     * @return void
     */
    public function confirmCancellation(): void
    {
        if (empty($this->pendingCancellation)) {
            return;
        }

        try {
            if ($this->workOrderService->cancelJob(
                $this->pendingCancellation['jobId'],
                $this->pendingCancellation['instanceId'],
                $this->pendingCancellation['dateTime']
            )) {
                $this->notify('success', 'business.scheduler.job_cancelled_successfully');
                $this->dispatch('refreshCalendar');
                $this->dispatch('refreshUnassignedJobs');
                $this->dispatch('refreshScheduler');
            } else {
                $this->notify('error', 'business.scheduler.job_cancelled_error');
            }
        } catch (\Exception $e) {
            Log::error('Failed to cancel job', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->notify('error', 'business.scheduler.job_cancelled_error');
        } finally {
            $this->resetCancellationState();
        }
    }

    /**
     * Cancel the pending cancellation
     *
     * @return void
     */
    public function cancelCancellation(): void
    {
        $this->resetCancellationState();
    }

    /**
     * Reset the cancellation state
     *
     * @return void
     */
    private function resetCancellationState(): void
    {
        $this->pendingCancellation = [];
        $this->showCancelConfirmModal = false;
    }
}
