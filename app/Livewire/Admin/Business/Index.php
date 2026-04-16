<?php

namespace App\Livewire\Admin\Business;

use App\Models\Business\Business;
use App\Actions\ClearUserSession;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Exports\BusinessExport;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class Index extends Component
{
    use WithoutUrlPagination;
    use WithPagination;

    public $search = '';

    public $statusFilter = '';

    public $isDeleting = false;

    public $isActivating = false;

    public $selectedBusiness = null;

    public $confirmStatus = false;

    public $error = false;

    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    public const DEFAULT_SUBSCRIPTION = 'default';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
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

    public function downloadBusinessList()
    {
        try {
            $export = app(BusinessExport::class);
            $filename = 'businesses_' . now()->format('Y_m_d') . '.xlsx';
            return Excel::download($export, $filename);
        } catch (\Throwable $th) {
            Log::error('Error in business export: '. $th->getMessage());
            $this->dispatch('business-export-failed', ['message' => __('admin.message.business_export_failed')]);
        }
    }

    public function render()
    {
        $businesses = [];
        $this->error = false;
        try {
            $status = $this->statusFilter == 'active' ? 1 : 0;
            $query = Business::query()
                ->when($this->search, function ($query) {
                    $query->where(function ($q) {
                        $q->where('name', 'like', '%'.$this->search.'%')
                            ->orWhere('email', 'like', '%'.$this->search.'%')
                            ->orWhere('phone', 'like', '%'.$this->search.'%');
                    });
                })
                ->when($this->statusFilter, function ($query) use ($status) {
                    $query->where('status', $status);
                });

            // Handle sorting
            if ($this->sortField === 'created_at') {
                // Sort by month and year for created_at
                $query->orderByRaw("DATE_FORMAT(created_at, '%Y-%m') {$this->sortDirection}");
            } else {
                $query->orderBy($this->sortField, $this->sortDirection);
            }

            $businesses = $query->paginate(10);

        } catch (Exception $e) {
            $this->error = true;
        }

        return view('livewire.admin.business.index', [
            'businesses' => $businesses,
            'error' => $this->error,
        ]);

    }

    public function showDeactivateModal($businessId)
    {
        $this->selectedBusiness = Business::findOrFail($businessId);
        $this->isDeleting = true;
    }

    public function showActivateModal($businessId)
    {
        $this->selectedBusiness = Business::findOrFail($businessId);
        $this->isDeleting = false;
    }

    public function changeStatus()
    {
        try {
            DB::transaction(function () {
                if ($this->selectedBusiness) {
                    $this->selectedBusiness->status = $this->selectedBusiness->status ? false : true;
                    $this->selectedBusiness->save();
                    $subAdminIds = $this->selectedBusiness->users()->pluck('id')->toArray();
                    if (!$this->selectedBusiness->status) {
                        app(ClearUserSession::class)->handle($subAdminIds);
                        // Cancel the subscription if the business is deactivated
                        if ($this->selectedBusiness->subscribed()) {
                            $this->selectedBusiness->subscription(self::DEFAULT_SUBSCRIPTION)->cancel();
                        }
                    }
                    session()->flash('success', __('admin.alert.business_updated'));
                }
            });
        } catch (Exception $e) {
            session()->flash('success', __('admin.alert.business_updated_failed'));
        }

        $this->selectedBusiness = null;
        $this->confirmStatus = false;
    }

    public function cancleAction()
    {
        $this->selectedBusiness = null;
        $this->isDeleting = false;
    }
}
