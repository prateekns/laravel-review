<?php

namespace App\Livewire\Admin;

use App\Helpers\Helper;
use App\Models\Business\Invoice;
use App\Services\Admin\AdminService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class Earning extends Component
{
    use WithoutUrlPagination;
    use WithPagination;

    public string $filter = 'monthly';

    public string $search = '';

    public string $fromDate = '';

    public string $toDate = '';

    protected int $pagination = 10;

    public bool $error = false;

    /**
     * Handle from date update
     */
    public function updatedFromDate(): void
    {
        $this->validate([
            'fromDate' => 'required|date|before_or_equal:toDate',
        ]);
        $this->resetPage();
    }

    /**
     * Handle to date update
     */
    public function updatedToDate(): void
    {
        $this->validate([
            'toDate' => 'required|date|after_or_equal:fromDate',
        ]);
        $this->resetPage();
    }

    /**
     * Handle property updates
     */
    public function updated($property)
    {
        $this->resetPage();
    }

    /**
     * Apply search filters to query
     */
    private function applySearchFilters(Builder $query, string $searchTerm): Builder
    {
        return $query->where(function ($q) use ($searchTerm) {
            $q->whereHas('business', function ($businessQuery) use ($searchTerm) {
                $businessQuery->where('name', 'like', '%'.$searchTerm.'%')
                    ->orWhere('email', 'like', '%'.$searchTerm.'%');
            })
                ->orWhere('invoice_number', 'like', '%'.$searchTerm.'%');
        });
    }

    /**
     * Render Earnings Page
     */
    public function render(): View
    {
        $earnings = [];
        $total = 0;

        try {
            $query = Invoice::query()->paid();
            $filteredQuery = app(AdminService::class)->queryFilter(
                $query,
                $this->filter,
                $this->fromDate,
                $this->toDate
            );

            if ($this->search) {
                $searchTerm = trim($this->search);
                $filteredQuery = $this->applySearchFilters($filteredQuery, $searchTerm);
            }

            $total = $filteredQuery->sum('amount_paid');
            $earnings = $filteredQuery->paginate($this->pagination);

        } catch (\Exception $e) {
            $this->error = true;
            // No Logging Needed
        }

        return view('livewire.admin.earning', [
            'earnings' => $earnings,
            'total' => Helper::getFormattedAmountWithCurrency($total),
            'error' => $this->error,
        ]);
    }
}
