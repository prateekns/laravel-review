<?php

namespace App\Livewire\Admin\Dashboard;

use App\Services\Admin\AdminService;
use Livewire\Component;

class Dashboard extends Component
{
    public string $filter = 'monthly';

    public string $fromDate = '';

    public string $toDate = '';

    public array $dashboardData = [];

    /**
     * Mount the component
     *
     * @return void
     */
    public function mount(AdminService $adminService)
    {
        $this->dashboardData = $adminService->getDashboardData($this->filter, $this->fromDate, $this->toDate);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function clearSearch()
    {
        $this->search = '';
        $this->resetPage();
    }

    /**
     * Update the component when the time filter, from date, or to date is changed
     *
     * @param  string  $property
     * @return void
     */
    public function updated($property)
    {
        $this->dashboardData = app(AdminService::class)->getDashboardData($this->filter, $this->fromDate, $this->toDate);
    }

    /**
     * Render the component
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render()
    {
        return view('livewire.admin.dashboard.dashboard');
    }
}
