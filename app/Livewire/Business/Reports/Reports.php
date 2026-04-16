<?php

namespace App\Livewire\Business\Reports;

use Livewire\Component;
use Livewire\WithPagination;
use App\Services\Business\Reports\ReportSummaryService;

class Reports extends Component
{
    use WithPagination;

    public ?string $search = '';
    public ?int $instanceId = null;
    public int $perPage = 15;
    public string $activeTab = 'chemicals';
    public $startDate;
    public $endDate;
    public $summary = [];
    public $days = 30;

    public function mount(?int $instanceId = null): void
    {
        $this->instanceId = $instanceId ?? request()->integer('instance_id');

        // Set default date range to last 30 days
        $this->endDate = now()->format('Y-m-d');
        $this->startDate = now()->subDays($this->days)->format('Y-m-d');

        // Get initial summary
        $this->updateSummary();
    }

    public function updateSummary()
    {
        $service = app(ReportSummaryService::class);
        $this->summary = $service->getSummary($this->startDate, $this->endDate);
    }

    public function updatedStartDate()
    {
        $this->updateSummary();
    }

    public function updatedEndDate()
    {
        $this->updateSummary();
    }

    public function render()
    {
        return view('livewire.business.reports.reports');
    }
}
