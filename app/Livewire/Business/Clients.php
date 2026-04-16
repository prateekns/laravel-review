<?php

namespace App\Livewire\Business;

use App\Models\Business\Client;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Clients extends Component
{
    use WithPagination;

    public $search = '';

    public $statusFilter = '';

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
        $status = $this->statusFilter == 'active' ? 1 : 0;
        $business = Auth::guard('business')->user()->business;
        $query = Client::query()
            ->where('business_id', $business->id)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('first_name', 'like', '%'.$this->search.'%')
                        ->orWhere('last_name', 'like', '%'.$this->search.'%')
                        ->orWhere('email1', 'like', '%'.$this->search.'%')
                        ->orWhere('email2', 'like', '%'.$this->search.'%')
                        ->orWhere('phone1', 'like', '%'.$this->search.'%')
                        ->orWhere('phone2', 'like', '%'.$this->search.'%');
                });

            })
            ->when($this->statusFilter, function ($query) use ($status) {
                $query->where('status', $status);
            });

        $clients = $query->orderBy('id', 'asc')
            ->paginate(10);

        return view('livewire.business.clients', [
            'clients' => $clients,
        ]);
    }
}
