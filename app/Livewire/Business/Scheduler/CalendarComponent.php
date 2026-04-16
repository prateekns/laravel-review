<?php

declare(strict_types=1);

namespace App\Livewire\Business\Scheduler;

use App\Livewire\Business\Scheduler\Traits\AssignmentOperations;
use App\Livewire\Business\Scheduler\Traits\CancellationOperations;
use App\Livewire\Business\Scheduler\Traits\ExportOperations;
use App\Livewire\Business\Scheduler\Traits\JobOrderOperations;
use App\Services\Business\WorkOrder\WorkOrderService;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Services\Business\Scheduler\DateTimeService;
use App\Services\Business\Scheduler\TechnicianScheduleService;
use App\Services\Business\Scheduler\JobAssignmentService;
use App\Services\Business\Scheduler\ExportService;

class CalendarComponent extends Component
{
    use AssignmentOperations;
    use CancellationOperations;
    use ExportOperations;
    use JobOrderOperations;

    public string $currentDate = '';
    public string $anchorDate = '';
    public array $weekDates = [];
    public string $searchTerm = '';
    public array $technicians = [];
    public array $expandedTechs = [];
    public ?int $jobId = null;

    protected $listeners = [
        'refreshCalendar' => 'loadTechnicians',
        'refreshScheduler' => 'refreshCalendar',
        'scheduler-assign-job' => 'handleAssignJobEvent',
        'updateJobOrder' => 'updateJobOrder'
    ];

    private DateTimeService $dateTimeService;
    private TechnicianScheduleService $technicianScheduleService;
    private JobAssignmentService $jobAssignmentService;
    private ExportService $exportService;
    private WorkOrderService $workOrderService;

    public function boot(
        DateTimeService $dateTimeService,
        TechnicianScheduleService $technicianScheduleService,
        JobAssignmentService $jobAssignmentService,
        ExportService $exportService,
        WorkOrderService $workOrderService
    ) {
        $this->dateTimeService = $dateTimeService;
        $this->technicianScheduleService = $technicianScheduleService;
        $this->jobAssignmentService = $jobAssignmentService;
        $this->exportService = $exportService;
        $this->workOrderService = $workOrderService;
    }

    public function updated($property)
    {
        if ($property === 'searchTerm') {
            $this->dispatch('$refresh');
        }
    }

    /**
     * Refresh the calendar data
     *
     * @return void
     */
    public function refreshCalendar(): void
    {
        $this->loadTechnicians();
        $this->generateWeekDates();
    }

    /**
     * Get the current week date range in business timezone
     *
     * @return array<string, string>
     */
    public function getCurrentWeekRange(): array
    {
        return $this->dateTimeService->getCurrentWeekRange($this->currentDate);
    }

    /**
     * Get the current week date range in business timezone for display
     *
     * @return array<string, string>
     */
    public function getCurrentWeekRangeDisplay(): array
    {
        return $this->dateTimeService->getCurrentWeekRangeDisplay($this->currentDate);
    }

    /**
     * Mount the Livewire component
     *
     * @return void
     */
    public function mount(): void
    {
        try {
            $nowIso = $this->dateTimeService->getBusinessTimezoneNow()->format('Y-m-d');
            $this->currentDate = $nowIso;
            $this->anchorDate = $nowIso;
            $this->weekDates = [];
            $this->technicians = [];
            $this->generateWeekDates();
            $this->loadTechnicians();
        } catch (\Exception $e) {
            Log::error('Failed to mount calendar component', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->currentDate = now()->format('Y-m-d');
            $this->anchorDate = $this->currentDate;
            $this->weekDates = [];
            $this->technicians = [];
        }
    }

    /**
     * Load technicians with their schedules for the current week
     *
     * @return void
     */
    public function loadTechnicians(): void
    {
        try {
            $businessId = Auth::guard('business')->user()->business_id;
            $businessTimezone = $this->dateTimeService->getBusinessTimezone();

            [$startOfWeek, $endOfWeek, $nonRecurringStartDate, $nonRecurringEndDate] = $this->dateTimeService->buildDateWindows($this->currentDate); //NOSONAR

            $technicians = $this->technicianScheduleService->fetchTechniciansWithOrders(
                $businessId,
                $nonRecurringStartDate,
                $nonRecurringEndDate,
                $startOfWeek
            );

            $this->technicians = $technicians
                ->map(fn ($tech) => $this->technicianScheduleService->buildTechnicianScheduleEntry($tech, $startOfWeek, $businessTimezone))
                ->toArray();
        } catch (\Exception $e) {
            Log::error('CalendarComponent.loadTechnicians failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->technicians = [];
        }
    }

    /**
     * Navigate to the next week
     *
     * @return void
     */
    public function nextWeek(): void
    {
        $businessTimezone = $this->dateTimeService->getBusinessTimezone();
        $proposed = Carbon::parse($this->currentDate, $businessTimezone)->addWeek();
        $anchorStart = Carbon::parse($this->anchorDate, $businessTimezone)->startOfWeek(Carbon::MONDAY);
        $proposedStart = $proposed->copy()->startOfWeek(Carbon::MONDAY);
        $diffWeeks = $anchorStart->diffInWeeks($proposedStart, true);
        if ($diffWeeks > 4) {
            $this->notify('error', 'business.scheduler.navigation_limit');
            return;
        }
        $this->currentDate = $proposed->format('Y-m-d');
        $this->generateWeekDates();
        $this->loadTechnicians();
    }

    /**
     * Navigate to the previous week
     *
     * @return void
     */
    public function prevWeek(): void
    {
        $businessTimezone = $this->dateTimeService->getBusinessTimezone();
        $proposed = Carbon::parse($this->currentDate, $businessTimezone)->subWeek();
        $anchorStart = Carbon::parse($this->anchorDate, $businessTimezone)->startOfWeek(Carbon::MONDAY);
        $proposedStart = $proposed->copy()->startOfWeek(Carbon::MONDAY);
        $diffWeeks = $anchorStart->diffInWeeks($proposedStart, true);
        if ($diffWeeks > 4) {
            $this->notify('error', 'business.scheduler.navigation_limit');
            return;
        }
        $this->currentDate = $proposed->format('Y-m-d');
        $this->generateWeekDates();
        $this->loadTechnicians();
    }

    /**
     * Show notification when technician is not available
     *
     * @return void
     */
    public function showNotAvailableNotification(): void
    {
        $this->notify('error', 'business.scheduler.technician_not_available');
    }

    /**
     * Generate week dates for the calendar
     *
     * @return void
     */
    private function generateWeekDates(): void
    {
        try {
            $this->weekDates = $this->dateTimeService->generateWeekDates($this->currentDate);
            if (empty($this->weekDates)) {
                $this->weekDates = [];
            }
        } catch (\Exception $e) {
            Log::error('Failed to generate week dates', [
                'error' => $e->getMessage(),
                'current_date' => $this->currentDate
            ]);
            $this->weekDates = [];
        }
    }

    /**
     * Send a notification to the user
     *
     * @param string $type
     * @param string $messageKey
     * @param bool $refresh
     * @return void
     */
    private function notify(string $type, string $messageKey, bool $refresh = false): void
    {
        // Support passing raw keys or already-resolved strings
        $message = __($messageKey);
        $this->dispatch('notify', [
            'type' => $type,
            'message' => $message
        ]);

        if ($refresh) {
            $this->dispatch('refreshCalendar');
            $this->dispatch('refreshUnassignedJobs');
            $this->dispatch('$refresh');
            $this->dispatch('refreshScheduler');
        }
    }

    /**
     * Render the Livewire component
     *
     * @return \Illuminate\View\View
     */
    public function render(): \Illuminate\View\View
    {
        $filteredTechs = $this->technicianScheduleService->getFilteredTechnicians($this->technicians, $this->searchTerm);

        return view('livewire.business.scheduler.calendar-component', [
            'filteredTechnicians' => $filteredTechs
        ]);
    }
}
