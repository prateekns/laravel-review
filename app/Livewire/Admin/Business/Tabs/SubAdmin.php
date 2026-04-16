<?php

namespace App\Livewire\Admin\Business\Tabs;

use App\Models\Business\Business;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class SubAdmin extends Component
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
        $subAdmins = [];

        try {
            $status = $this->statusFilter == 'active' ? 1 : 0;

            $subAdmins = $this->business->subAdmins()
                ->when($this->search, function ($query) {
                    $query->where('first_name', 'like', '%'.$this->search.'%')
                        ->orWhere('last_name', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%')
                        ->orWhere('phone', 'like', '%'.$this->search.'%');
                })
                ->when($this->statusFilter, function ($query) use ($status) {
                    $query->where('status', $status);
                })
                ->orderBy('id', 'asc')
                ->paginate($this->pagination);

        } catch (\Exception $e) {
            $this->error = true;
        }

        return view('livewire.admin.business.tabs.sub-admins', [
            'subAdmins' => $subAdmins,
        ]);
    }
}
