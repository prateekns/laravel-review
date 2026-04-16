<?php

namespace App\Livewire\Business\Scheduler;

use App\Enums\WorkOrderStatus;
use App\Models\Business\WorkOrder\WorkOrder;
use App\Services\Business\WorkOrder\WorkOrderInstanceService;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class UnassignedJobs extends Component
{
    public $unassignedJobs = [];
    public $jobTypes = [];
    protected WorkOrderInstanceService $workOrderInstanceService;
    public $jobId;
    public WorkOrder|null $job;

    protected $listeners = [
        'refreshUnassignedJobs' => 'loadUnassignedJobs',
        'refreshScheduler' => 'loadUnassignedJobs'
    ];

    public function boot(WorkOrderInstanceService $workOrderInstanceService)
    {
        $this->workOrderInstanceService = $workOrderInstanceService;
    }

    public function unassignJob($jobId, $instanceId = null)
    {
        try {
            // Handle job unassignment (both regular and recurring jobs)
            $job = WorkOrder::where('business_id', auth()->guard('business')->user()->business_id)
                ->findOrFail($jobId);

            // Double check if the job belongs to the current business
            abort_unless($job->business_id === auth()->guard('business')->user()->business_id, 403);

            // Check if job is already unassigned
            if ($job->technician_id === null) {
                $this->dispatch('notify', ['type' => 'info', 'message' => 'Job is already unassigned']);
            } else {
                $job->technician_id = null;
                $job->status = WorkOrderStatus::PENDING->value;
                $job->save();

                $this->dispatch('notify', ['type' => 'success', 'message' => __('business.scheduler.job_unassigned')]);
            }

            // Always refresh both components
            $this->loadUnassignedJobs(); // Refresh unassigned jobs
            $this->dispatch('refreshCalendar'); // Refresh calendar
            $this->dispatch('refreshScheduler'); // Additional refresh
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(403);
        } catch (\Exception $e) {
            $this->dispatch('notify', ['type' => 'error', 'message' => __('business.scheduler.error_unassigning')]);
        }
    }

    public function mount()
    {
        // If jobId & job is provided, trigger the assign modal
        if ($this->jobId && $this->job && $this->jobId === $this->job->id) {
            try {
                abort_unless($this->job->business_id === auth()->guard('business')->user()->business_id, 403);

                // Scroll logic with safe selector (no interpolation of untrusted strings)
                $this->js(sprintf("
                    (function(){
                        var container = document.querySelector('[x-ref=\"unassignedContainer\"]');
                        var el = container ? container.querySelector('[data-job-id=\"%d\"]') : null;
                        if (el && el.scrollIntoView) {
                        el.scrollIntoView({behavior:'smooth', block:'nearest', inline:'center'});
                        el.classList.add('ring-2','ring-white');
                        setTimeout(function(){ el.classList.remove('ring-2','ring-white'); }, 1500);
                        }
                    })();
                    ", (int) $this->job->id));
            } catch (\Exception $e) {
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => __('business.scheduler.error_loading_job')
                ]);
            }
        }

        $this->jobTypes = [
            'Maintenance Order' => [
                'icon' => '<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8.37333 4.05329C8.83184 3.32071 9.15687 2.51266 9.33333 1.66663C9.66667 3.33329 10.6667 4.93329 12 5.99996C13.3333 7.06663 14 8.33329 14 9.66663C14.0038 10.5882 13.7339 11.4901 13.2245 12.258C12.7151 13.026 11.9892 13.6254 11.1388 13.9803C10.2883 14.3352 9.35161 14.4296 8.44745 14.2515C7.54328 14.0734 6.71236 13.6309 6.06 12.98M4.66667 10.52C6.13333 10.52 7.33333 9.29996 7.33333 7.81996C7.33333 7.04663 6.95333 6.31329 6.19333 5.69329C5.43333 5.07329 4.86 4.15329 4.66667 3.18663C4.47333 4.15329 3.90667 5.07996 3.14 5.69329C2.37333 6.30663 2 7.05329 2 7.81996C2 9.29996 3.2 10.52 4.66667 10.52Z" stroke="black" stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round"/></svg>',
                'themeClasses' => 'theme-maintenance-box',
                'cardClass' => 'bg-scheduler-card-maintenance',
            ],
            'Work Order' => [
                'icon' => '<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10.4188 5.08762C10.1548 4.82361 10.0228 4.6916 9.97331 4.53939C9.92981 4.40549 9.92981 4.26126 9.97331 4.12736C10.0228 3.97514 10.1548 3.84314 10.4188 3.57913L12.311 1.68688C11.8089 1.45979 11.2515 1.33337 10.6645 1.33337C8.4554 1.33337 6.66454 3.12424 6.66454 5.33337C6.66454 5.66073 6.70386 5.97891 6.77805 6.28343C6.85749 6.60953 6.89721 6.77259 6.89015 6.87559C6.88277 6.98343 6.86669 7.04081 6.81696 7.13678C6.76946 7.22846 6.67844 7.31947 6.49642 7.5015L2.33121 11.6667C1.77892 12.219 1.77892 13.1144 2.33121 13.6667C2.88349 14.219 3.77892 14.219 4.33121 13.6667L8.49642 9.5015C8.67844 9.31947 8.76946 9.22846 8.86113 9.18095C8.9571 9.13122 9.01448 9.11514 9.12232 9.10776C9.22533 9.10071 9.38838 9.14043 9.71448 9.21987C10.019 9.29405 10.3372 9.33337 10.6645 9.33337C12.8737 9.33337 14.6645 7.54251 14.6645 5.33337C14.6645 4.74644 14.5381 4.18902 14.311 3.68688L12.4188 5.57913C12.1548 5.84314 12.0228 5.97514 11.8706 6.0246C11.7367 6.06811 11.5924 6.06811 11.4585 6.0246C11.3063 5.97514 11.1743 5.84314 10.9103 5.57913L10.4188 5.08762Z" stroke="black" stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round"/></svg>',
                'themeClasses' => 'theme-work-box',
                'cardClass' => 'bg-scheduler-card-work',
            ],
            'default' => [
                'icon' => '<svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>',
                'themeClasses' => 'border-gray-400 text-gray-400'
            ]
        ];

        $this->loadUnassignedJobs();
    }

    /**
     * Open the Assign Job modal from the Unassigned list.
     *
     * @param int $jobId
     * @param int|string|null $techId
     * @param string|null $dateIso
     * @param string|null $instanceId
     * @param bool $assignAllFuture
     * @param string|null $time
     */
    public function openAssignModal(int $jobId, int|string|null $techId = null, ?string $dateIso = null, ?string $instanceId = null, bool $assignAllFuture = false, ?string $time = null): void
    {
        $techId = ($techId === '' || $techId === 0) ? null : $techId;

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
     * Get the business timezone
     */
    private function getBusinessTimezone(): string
    {
        $user = Auth::guard('business')->user();
        return $user->business->timezone ?? 'America/New_York';
    }

    public function loadUnassignedJobs()
    {
        try {
            $businessId = Auth::guard('business')->user()->business_id;

            // Get all unassigned jobs (both regular and recurring)
            $allUnassignedJobs = WorkOrder::where('business_id', $businessId)
                ->whereNull('technician_id')
                ->where('is_active', true)
                ->whereNotIn('status', [WorkOrderStatus::COMPLETED->value, WorkOrderStatus::CANCELLED->value])
                ->with(['customer'])
                ->orderBy('preferred_start_date', 'asc')
                ->orderBy('preferred_start_time', 'asc')
                ->get();

            // Get reassigned job instances that can be reassigned to other technicians
            $reassignedInstances = $this->getReassignedInstances($businessId);
            $businessTimezone = $this->getBusinessTimezone();
            $allJobs = collect();

            // Process regular unassigned jobs
            foreach ($allUnassignedJobs as $job) {
                $date = $job->preferred_start_date->format(config('datetime.formats.date'));
                $datetime = $date;
                if ($job->preferred_start_time) {
                    // Convert to business timezone for display
                    $utcDateTime = Carbon::parse($date . ' ' . $job->preferred_start_time, 'UTC');
                    $businessDateTime = $utcDateTime->setTimezone($businessTimezone);
                    $datetime = $businessDateTime->format(config('datetime.formats.datetime'));
                }

                $allJobs->push([
                    'id' => $job->id,
                    'instance_id' => null, // No instance ID for main jobs
                    'type' => $job->type_label,
                    'name' => $job->name,
                    'datetime' => $datetime,
                    'time' => $businessDateTime->format(config('datetime.formats.time')),
                    'status' => $job->calculateJobStatus(),
                    'is_recurring' => $job->is_recurring,
                    'customer' => $job->customer,
                    'is_reassigned_instance' => false
                ]);
            }

            // Process reassigned instances
            foreach ($reassignedInstances as $instance) {
                $allJobs->push($instance);
            }

            // Sort by datetime
            $this->unassignedJobs = $allJobs->sortBy('datetime')->values()->toArray();
        } catch (\Exception $e) {
            // Handle error silently or dispatch notification if needed
        }
    }

    /**
     * Get reassigned job instances that can be re-assigned to other technicians.
     * These are instances from work_order_assignments that are not completed/in-progress/cancelled.
     */
    private function getReassignedInstances(int $businessId): array
    {
        $businessTimezone = $this->getBusinessTimezone();
        $reassignedJobs = [];


        try {
            // Get reassigned instances (not completed/in-progress/cancelled) to show as re-assignable
            $assignments = $this->workOrderInstanceService->getReassignedInstances($businessId);

            foreach ($assignments as $assignment) {
                // Convert scheduled time to business timezone for display
                $utcDateTime = Carbon::parse($assignment->scheduled_date->format(config('datetime.formats.date')) . ' ' . $assignment->scheduled_time, 'UTC');
                $businessDateTime = $utcDateTime->setTimezone($businessTimezone);

                $reassignedJobs[] = [
                    'id' => $assignment->work_order_id,
                    'assignment_id' => $assignment->id,
                    'instance_id' => $assignment->instance_id ?? $assignment->job_id,
                    'type' => $assignment->workOrder->type_label ?? 'Work Order',
                    'name' => $assignment->workOrder->name,
                    'datetime' => $businessDateTime->format(config('datetime.formats.datetime')),
                    'time' => $businessDateTime->format(config('datetime.formats.time')),
                    'status' => $assignment->calculateWorkOrderAssignmentStatus(),
                    'is_recurring' => $assignment->recurrence_rule ? true : false,
                    'customer' => $assignment->workOrder->customer,
                    'current_technician' => $assignment->technician->full_name ?? 'Unknown Technician',
                    'is_reassigned_instance' => true
                ];
            }
        } catch (\Exception $e) {
            Log::error('Error getting reassigned instances', ['error' => $e->getMessage()]);
        }

        return $reassignedJobs;
    }

    public function render()
    {
        return view('livewire.business.scheduler.unassigned-jobs');
    }
}
