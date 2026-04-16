<?php

declare(strict_types=1);

namespace App\Livewire\Business\Scheduler\Traits;

use App\Models\Business\WorkOrder\WorkOrder;
use App\Models\Business\Technician\Technician;
use Illuminate\Support\Facades\Log;

trait AssignmentOperations
{
    public array $pendingAssignment = [];
    public bool $showConfirmModal = false;

    /**
     * Open the assignment modal with the provided parameters
     *
     * @param int $jobId
     * @param int $techId
     * @param string $dateIso
     * @param int|null $instanceId
     * @param bool $assignAllFuture
     * @param string|null $time
     * @return void
     */
    public function openAssignModal(
        int $jobId,
        int $techId,
        string $dateIso,
        ?int $instanceId = null,
        bool $assignAllFuture = false,
        ?string $time = null
    ): void {
        $this->dispatch(
            'assign-modal-open',
            jobId: $jobId,
            techId: $techId,
            dateIso: $dateIso,
            instanceId: $instanceId,
            assignAllFuture: $assignAllFuture,
            time: $time
        );
    }

    /**
     * Handle assign job event from Livewire
     *
     * @param array|null $jobData
     * @return void
     */
    #[\Livewire\Attributes\On('scheduler-assign-job')]
    public function handleAssignJobEvent(?array $jobData = null): void
    {
        if (!is_array($jobData)) {
            return;
        }

        $this->assignJob(
            $jobData['jobId'],
            (int)$jobData['technicianId'],
            $jobData['date'],
            (string)$jobData['instanceId'] ?? null,
            $jobData['time'] ?? null,
            $jobData['assignAllFuture'] ?? false,
            $jobData['origin'] ?? 'menu'
        );
    }

    /**
     * Assign a job to a technician
     *
     * @param int $jobId
     * @param int $technicianId
     * @param string $date
     * @param string|null $instanceId
     * @param string|null $time
     * @param bool $assignAllFuture
     * @param string $origin
     * @return void
     */
    public function assignJob(
        int $jobId,
        int $technicianId,
        string $date,
        ?string $instanceId = null,
        ?string $time = null,
        bool $assignAllFuture = false,
        string $origin = 'drag'
    ): void {
        try {
            $job = WorkOrder::where('business_id', auth()->guard('business')->user()->business_id)
                ->findOrFail($jobId);
            $technician = Technician::where('business_id', auth()->guard('business')->user()->business_id)
                ->findOrFail($technicianId);

            $validateTimeConflict = !($assignAllFuture || $instanceId);
            $validation = $this->jobAssignmentService->validateAssignment(
                $job,
                $technician,
                $date,
                $validateTimeConflict,
                $time,
                $instanceId,
                (bool)$assignAllFuture
            );

            if (!$validation['valid']) {
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => $validation['message']
                ]);
                return;
            }

            $this->pendingAssignment = [
                'jobId' => $jobId,
                'technicianId' => $technicianId,
                'date' => $date,
                'instanceId' => $instanceId,
                'time' => $time,
                'origin' => $origin,
                'assignAllFuture' => (bool) $assignAllFuture,
                'jobName' => $job->name,
                'technicianName' => $technician->full_name,
                'isRecurring' => $job->is_recurring
            ];

            $this->showConfirmModal = true;
            $this->dispatch('setPendingAssignment');
            $this->dispatch('end-assign-processing');
        } catch (\Exception $e) {
            Log::error('CalendarComponent.assignJob failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('end-assign-processing');
        }
    }

    /**
     * Confirm the pending job assignment
     *
     * @return void
     */
    public function confirmAssignment(): void
    {
        try {
            if (!empty($this->pendingAssignment)) {
                $response = $this->jobAssignmentService->jobAssignment($this->pendingAssignment);

                if (isset($response['valid']) && !$response['valid']) {
                    $this->dispatch('notify', [
                        'type' => 'error',
                        'message' => $response['message']
                    ]);

                    return;
                }

                $this->notify(
                    'success',
                    $response['job']->is_recurring
                        ? 'business.scheduler.recurring_job_assigned_success'
                        : 'business.scheduler.job_assigned_success',
                    true
                );
            }
        } catch (\Exception $e) {
            Log::error('CalendarComponent.confirmAssignment failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->notify('error', 'business.scheduler.error_assigning');
        } finally {
            $this->resetAssignmentState();
        }
    }

    /**
     * Cancel the pending assignment
     *
     * @return void
     */
    public function cancelAssignment(): void
    {
        $this->pendingAssignment = [];
        $this->showConfirmModal = false;
    }

    /**
     * Reset the assignment state
     *
     * @return void
     */
    private function resetAssignmentState(): void
    {
        $this->pendingAssignment = [];
        $this->showConfirmModal = false;
        $this->dispatch('resetPendingAssignment');
    }
}
