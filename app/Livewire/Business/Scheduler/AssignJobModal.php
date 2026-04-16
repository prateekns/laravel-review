<?php

namespace App\Livewire\Business\Scheduler;

use Livewire\Component;
use App\Models\Business\WorkOrder\WorkOrder;
use App\Models\Business\Technician\Technician;
use Illuminate\Support\Facades\Auth;
use App\Services\Business\Scheduler\DateTimeService;
use App\Services\Business\Scheduler\JobAssignmentService;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class AssignJobModal extends Component
{
    public $showModal = false;
    public $jobId;
    public $techId;
    public $dateIso;
    public $time;
    public $instanceId;
    public $assignAllFuture = false;
    public $selectedTechnicianId;
    public $selectedTechnician = null;
    public $job;
    public $currentTechnician;
    public $availableTechnicians = [];
    private $dateTimeService;
    private $jobAssignmentService;

    /**
     * Auto-open modal when parameters are passed from the parent view/component.
     */
    public function mount(
        ?int $jobId = null
    ): void {
        if ($jobId) {
            // If jobId is provided, trigger the assign modal
            $job = WorkOrder::where('business_id', auth()->guard('business')->user()->business_id)
                ->whereNull('technician_id')
                ->where('is_active', true)
                ->findOrFail($this->jobId);

            if ($job) {
                $date = $job->preferred_start_date->format(config('datetime.formats.date'));

                // Convert to business timezone for display
                $utcDateTime = Carbon::parse($date . ' ' . $job->preferred_start_time, 'UTC');
                $businessDateTime = $utcDateTime->setTimezone($job->getBusinessTimezone());
                $date = $businessDateTime->format(config('datetime.formats.date'));
                $time = $businessDateTime->format(config('datetime.formats.time'));

                $this->jobId = $jobId;
                $this->techId = null;
                $this->dateIso = $date;
                $this->time = $time;
                $this->instanceId = null;
                $this->assignAllFuture = $job->is_recurring ? true : false;

                $this->loadJobDetails();
                $this->loadAvailableTechnicians();
                $this->showModal = true;
            }
        }
    }

    public function updatedSelectedTechnicianId($value)
    {
        if ($value) {
            $businessTimezone = $this->dateTimeService->getBusinessTimezone();

            // Get the full technician model for validation
            $technician = Technician::where('business_id', auth()->guard('business')->user()->business_id)
                ->findOrFail($value);

            $job = WorkOrder::where('business_id', auth()->guard('business')->user()->business_id)
                ->findOrFail($this->jobId);

            // Check availability
            // Use instance date (dateIso) when assigning only this job; fallback to job's preferred date
            $validationDate = $this->dateIso
                ?: $this->job->preferred_start_date->copy()->setTimezone($businessTimezone)->format('Y-m-d');

            $availability = $this->jobAssignmentService->validateAssignment(
                $job,
                $technician,
                $validationDate,
                true, // always check time conflicts in modal preview
                $this->time,
                $this->instanceId,
                (bool)$this->assignAllFuture
            );

            $this->selectedTechnician = array_merge($technician->toArray(), [
                'available' => [
                    'status' => $availability['valid'],
                    'message' => $availability['message']
                ]
            ]);
        } else {
            $this->selectedTechnician = null;
        }
    }

    #[\Livewire\Attributes\On('assign-modal-open')]
    public function open($jobId, $techId, $dateIso, $instanceId = null, $assignAllFuture = false, $time = null)
    {
        $this->jobId = $jobId;
        $this->techId = $techId;
        $this->dateIso = $dateIso;
        $this->time = $time;
        $this->instanceId = $instanceId;
        $this->assignAllFuture = $assignAllFuture;

        $this->loadJobDetails();
        $this->loadAvailableTechnicians();

        $this->showModal = true;
    }

    public function boot(
        DateTimeService $dateTimeService,
        JobAssignmentService $jobAssignmentService
    ) {
        $this->dateTimeService = $dateTimeService;
        $this->jobAssignmentService = $jobAssignmentService;
    }


    public function resetState()
    {
        $this->showModal = false;
        $this->jobId = null;
        $this->techId = null;
        $this->dateIso = null;
        $this->time = null;
        $this->instanceId = null;
        $this->assignAllFuture = false;
        $this->selectedTechnicianId = null;
        $this->selectedTechnician = null;
        $this->job = null;
        $this->currentTechnician = null;
        $this->availableTechnicians = [];
    }


    public function close()
    {
        $this->resetState();
    }

    protected function loadJobDetails()
    {
        try {
            $businessId = Auth::guard('business')->user()->business_id;

            $this->job = WorkOrder::where('business_id', $businessId)
                ->findOrFail($this->jobId);

            if ($this->techId) {
                $this->currentTechnician = Technician::where('business_id', $businessId)
                    ->findOrFail($this->techId);
            } else {
                $this->currentTechnician = new Collection();
            }
        } catch (\Exception $e) {
            $this->notifyError('business.scheduler.error_loading_job');
            $this->close();
        }
    }

    protected function loadAvailableTechnicians()
    {
        try {
            $businessId = Auth::guard('business')->user()->business_id;

            $technicians = Technician::where('business_id', $businessId)
                ->where('status', '1')
                ->where('id', '!=', $this->techId)
                ->get();

            $this->availableTechnicians = $technicians->map(function ($tech) {
                return [
                    'id' => $tech->id,
                    'name' => $tech->full_name
                ];
            })->toArray();
        } catch (\Exception $e) {
            $this->notifyError('business.scheduler.error_loading_technicians');
            $this->close();
        }
    }

    public function assignJob()
    {
        if (!$this->selectedTechnicianId) {
            $this->notifyError('business.scheduler.select_technician_required');
            return;
        }

        // Tell the calendar to show an immediate loading overlay
        $this->dispatch('begin-assign-processing');

        $eventData = [
            'jobId' => $this->jobId,
            'technicianId' => $this->selectedTechnicianId,
            'date' => $this->dateIso,
            'time' => $this->time,
            'instanceId' => $this->instanceId,
            'assignAllFuture' => $this->assignAllFuture,
            'origin' => 'menu',
        ];
        $this->dispatch('scheduler-assign-job', jobData: $eventData);
        $this->close();
    }

    private function notifyError(string $message): void
    {
        $this->dispatch('notify', [
            'type' => 'error',
            'message' => __($message)
        ]);
    }

    public function render()
    {
        return view('livewire.business.scheduler.assign-job-modal');
    }
}
