<?php

namespace App\Livewire\Admin\Business\Tabs;

use App\Models\Business\Business;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class Customer extends Component
{
    use WithoutUrlPagination;
    use WithPagination;

    public Business $business;

    public $search = '';

    public $statusFilter = '';

    public $error = false;

    protected $pagination = 10;

    public function mount(Business $business)
    {
        $this->business = $business;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $customers = [];

        try {
            $status = $this->statusFilter == 'active' ? 1 : 0;
            $customers = $this->business->clients()
                ->when($this->search, function ($query) {
                    $query->where('invoice_number', 'like', '%'.$this->search.'%')
                        ->orWhere('billing_reason', 'like', '%'.$this->search.'%');
                })
                ->when($this->statusFilter, function ($query) use ($status) {
                    $query->where('status', $status);
                })
                ->select('first_name', 'last_name', 'email_1', 'status')
                ->orderBy('id', 'asc')
                ->paginate($this->pagination);

        } catch (\Exception $e) {
            $this->error = true;
        }

        return view('livewire.admin.business.tabs.customers', [
            'customers' => $customers,
        ]);
    }
}
