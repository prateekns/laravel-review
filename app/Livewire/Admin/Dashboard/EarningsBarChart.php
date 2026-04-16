<?php

namespace App\Livewire\Admin\Dashboard;

use App\Services\Admin\AdminService;
use Livewire\Component;

class EarningsBarChart extends Component
{
    public $labels = [];

    public $totals = [];

    public $filter = 'monthly'; // Default filter

    /**
     * Mount the component
     *
     * @param  string  $filter
     * @return void
     */
    public function mount($filter = 'monthly')
    {
        $this->filter = $filter;
        $this->loadBarChartData();
    }

    /**
     * Update the filter
     *
     * @return void
     */
    public function updatedFilter()
    {
        $this->loadBarChartData();

        $this->dispatch('chart-updated', [
            'labels' => $this->labels,
            'totals' => $this->totals,
            'filter' => $this->filter,
        ]);
    }

    /**
     * Load the bar chart data
     *
     * @return void
     */
    protected function loadBarChartData()
    {
        $adminService = app(AdminService::class);

        switch ($this->filter) {
            case 'daily':
                $revenueData = $adminService->getDailyRevenueData();
                break;
            case 'weekly':
                $revenueData = $adminService->getWeeklyRevenueData();
                break;
            case 'yearly':
                $revenueData = $adminService->getYearlyRevenueData();
                break;
            case 'monthly':
            default:
                $revenueData = $adminService->getMonthlyRevenueData();
                break;
        }

        $this->labels = $revenueData['labels'];
        $this->totals = $revenueData['totals'];
    }

    /**
     * Render the component
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render()
    {
        return view('livewire.admin.dashboard.earnings-bar-chart');
    }
}
