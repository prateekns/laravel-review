<?php

namespace App\Livewire\Business\Reports;

use Livewire\Component;
use Livewire\WithPagination;
use App\Services\Business\Reports\ChemicalReportService;
use App\Exports\ChemicalReportExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class ChemicalReport extends Component
{
    use WithPagination;

    /** @var string|null */
    public ?string $search = '';

    /** @var int|null */
    public ?int $instanceId = null;

    /** @var int */
    public int $perPage = 10;

    public ?string $startDate = null;
    public ?string $endDate = null;
    public ?string $startDateLocal = null;
    public ?string $localTime = null;
    public ?string $endDateLocal = null;
    public ?string $minDate = null;
    public ?string $maxDate = null;
    public $sortField = 'chemical_name';
    public $sortDirection = 'asc';
    public $selectedChemicals = [];
    public $availableChemicals = [];
    public ?string $businessTimezone = null;

    public function mount(?int $instanceId = null): void
    {
        $this->instanceId = $instanceId ?? request()->integer('instance_id');
        $this->businessTimezone = auth()->guard('business')->user()?->business?->timezone
            ?? config('datetime.timezones.default');

        $businessNow = now($this->businessTimezone);
        $this->localTime = $businessNow->copy()->format(config('datetime.formats.time'));


        // Set date constraints for the picker (business timezone)
        $this->maxDate = $businessNow->copy()->format('Y-m-d');
        $this->minDate = $businessNow->copy()->subYears(10)->format('Y-m-d');

        // Set default date range to last 30 days (business timezone for inputs, UTC for queries)
        $this->endDateLocal = $this->maxDate;
        $this->startDateLocal = $businessNow->copy()->subDays(30)->format('Y-m-d');

        $this->startDate = $this->convertToUtcBoundary($this->startDateLocal);
        $this->endDate = $this->convertToUtcBoundary($this->endDateLocal);

        // Get available chemicals
        /** @var ChemicalReportService $service */
        $service = app(ChemicalReportService::class);
        $this->availableChemicals = $service->getAvailableChemicals();
        $this->selectedChemicals = $this->availableChemicals;
    }

    public function showDateRangeError()
    {
        $this->showError(__('business.reports.validation.date_range_60_days'));
    }

    public function showError(string $message)
    {
        $this->dispatch('notify', [
            'type' => 'error',
            'message' => $message
        ]);
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function applyDateFilter($startDate, $endDate)
    {
        $this->startDateLocal = $startDate;
        $this->endDateLocal = $endDate;
        $this->startDate = $this->convertToUtcBoundary($startDate);
        $this->endDate = $this->convertToUtcBoundary($endDate);
        $this->resetPage();
    }

    /**
     * Download the chemical report as an Excel file.
     *
     * @return \Maatwebsite\Excel\Facades\Excel
     */
    public function downloadChemicalReport()
    {
        $export = app(ChemicalReportExport::class, [
            'filters' => [
                'search' => $this->search,
                'startDate' => $this->startDate,
                'endDate' => $this->endDate,
                'selectedChemicals' => $this->selectedChemicals
            ]
        ]);

        $filename = 'chemical_report_' . now()->format('Y_m_d') . '.xlsx';
        return Excel::download($export, $filename);
    }

    public function render()
    {
        /** @var ChemicalReportService $service */
        $service = app(ChemicalReportService::class);
        $chemicalLogs = $service->paginateChemicalLogs(
            instanceId: $this->instanceId,
            search: $this->search,
            selectedChemicals: $this->selectedChemicals,
            dateRange: [
                'startDate' => $this->startDate,
                'endDate' => $this->endDate,
            ],
            perPage: $this->perPage,
            sortField: $this->sortField,
            sortDirection: $this->sortDirection
        );

        return view('livewire.business.reports.chemical-report', [
            'chemicalLogs' => $chemicalLogs,
        ]);
    }

    /**
     * Convert a business-timezone date into a UTC datetime boundary.
     */
    private function convertToUtcBoundary(?string $date): ?string
    {
        if (!$date) {
            return null;
        }

        $dateTime = Carbon::parse($date . ' ' . $this->localTime, $this->businessTimezone);

        return $dateTime
            ->setTimezone(config('datetime.timezones.utc'))
            ->format(config('datetime.formats.datetime'));
    }
}
