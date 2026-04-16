<?php

namespace App\Livewire\Admin\SubAdmin;

use App\Models\Admin\User;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Exception;

class SubAdmin extends Component
{
    use WithoutUrlPagination;
    use WithPagination;

    public string $statusFilter = '';

    public bool $isDeleting = false;

    public string $search = '';

    public ?User $selectedAdmin = null;

    public bool $confirmDelete = false;

    public bool $confirmStatus = false;

    public int $pagination = 10;

    public int $userId;

    public string $message = '';

    public bool $showToast = false;

    public function mount()
    {
        $this->userId = Auth::guard('admin')->user()->id;
    }

    public function render()
    {
        $admins = [];
        $error = false;

        try {
            $status = $this->statusFilter == 'active' ? true : false;
            $admins = User::notPrimary()
                ->when($this->search, function ($query) {
                    $query->where(function ($q) {
                        $q->where('name', 'like', '%'.trim($this->search).'%')
                            ->orWhere('email', 'like', '%'.trim($this->search).'%');
                    });
                })
                ->when($this->statusFilter, function ($query) use ($status) {
                    $query->where('status', $status);
                })
                ->orderBy('created_at', 'asc')
                ->paginate($this->pagination);

        } catch (Exception $e) {
            $error = true;
        }

        return view('livewire.admin.sub-admin.index', [
            'admins' => $admins,
            'user_id' => $this->userId,
            'error' => $error,
        ]);
    }

    public function showDeactivateModal($adminId)
    {
        $this->selectedAdmin = User::findOrFail($adminId);
        $this->isDeleting = true;
    }

    public function showActivateModal($adminId)
    {
        $this->selectedAdmin = User::findOrFail($adminId);
        $this->isDeleting = false;
        $this->isActivating = true;
    }

    public function changeStatus()
    {
        try {
            if ($this->selectedAdmin) {
                $this->selectedAdmin->status = $this->selectedAdmin->status ? false : true;
                $this->selectedAdmin->save();
                session()->flash('success', __('admin.message.sub_admin_updated'));
            }
        } catch (Exception $e) {
            session()->flash('success', __('admin.message.sub_admin_update_failed'));
        }

        $this->selectedAdmin = null;
        $this->confirmStatus = false;
    }

    public function delete($adminId)
    {
        try {
            $admin = User::findOrFail($adminId);
            $admin->delete();
            $this->showToast = true;
            $this->message = __('admin.message.sub_admin_deleted');
            $this->dispatch('hide-toast');
        } catch (Exception $e) {
            session()->flash('error', __('admin.message.sub_admin_delete_fail'));
        }
        $this->confirmDelete = false;
    }

    public function reactivateUser($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();
        session()->flash('success', __('admin.message.sub_admin_reactivate'));
    }

    public function cancleAction()
    {
        $this->selectedAdmin = null;
        $this->isDeleting = false;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
}
