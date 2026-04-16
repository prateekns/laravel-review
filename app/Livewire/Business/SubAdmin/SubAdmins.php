<?php

namespace App\Livewire\Business\SubAdmin;

use App\Models\Business\BusinessUser;
use App\Services\Business\BusinessService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;

/**
 * SubAdmins Livewire Component
 *
 * This component handles the listing of sub-admins for a business.
 * It provides functionality to view, search and paginate sub-admins.
 */
class SubAdmins extends Component
{
    use WithPagination;
    use WithOutUrlPagination;

    /**
     * Number of items to show per page
     */
    protected int $perPage = 10;

    /**
     * Search query string
     */
    public string $search = '';
    public bool $canAddSubAdmin = false;

    /**
     * User instance
     */
    public BusinessUser $user;

    /**
     * @var BusinessService
     */
    protected BusinessService $businessService;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->businessService = app(BusinessService::class);
        $this->user = Auth::guard('business')->user();
        $this->canAddSubAdmin = $this->canAddSubAdmin();
    }

    /**
     * Get the list of sub-admins for the current business.
     */
    public function getSubAdmins()
    {
        $query = BusinessUser::query()
            ->where('business_id', $this->user->business_id)
            ->notPrimary();

        if (strlen($this->search) >= 3) {
            $query->where(function ($query) {
                $query->where('first_name', 'like', '%' . trim($this->search) . '%')
                    ->orWhere('last_name', 'like', '%' . trim($this->search) . '%')
                    ->orWhere('email', 'like', '%' . trim($this->search) . '%')
                    ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ['%' . trim($this->search) . '%']);
            });
        }

        return $query->latest()->paginate($this->perPage);
    }

    /**
     * Reset pagination when search is updated
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Check if the business has reached its sub-admin limit.
     */
    public function hasReachedSubAdminLimit(): bool
    {
        return $this->businessService->isAdminLimitReached();
    }

    /**
     * Check if the business has an active subscription plan.
     */
    public function hasActivePlan(): bool
    {
        $business = Auth::guard('business')->user()->business;

        // Check if trial is active or has active subscription
        return $this->businessService->isTrialActive() || $business->subscribed('default');
    }

    /**
     * Get the warning message for the current plan status.
     */
    public function getPlanWarningMessage(): ?string
    {
        $errorMessage = null;
        if ($this->businessService->isPastDuePlan()) {
            $errorMessage =  __('business.past_due_plan');
        } elseif (!$this->hasActivePlan()) {
            $errorMessage =  __('business.no_active_plan');
        } elseif ($this->hasReachedSubAdminLimit()) {
            $errorMessage =  __('business.sub_admin.limit_reached');
        }

        return $errorMessage;
    }

    public function canAddSubAdmin(): bool
    {
        $this->canAddSubAdmin = !$this->hasReachedSubAdminLimit() && $this->hasActivePlan();
        return $this->canAddSubAdmin;
    }

    /**
     * Render the component.
     */
    public function render(): View
    {
        return view('livewire.business.sub-admin.sub-admins', [
            'subAdmins' => $this->getSubAdmins(),
            'hasReachedLimit' => $this->hasReachedSubAdminLimit(),
            'hasActivePlan' => $this->hasActivePlan(),
            'warningMessage' => $this->getPlanWarningMessage(),
            'pastDue' => $this->businessService->isPastDuePlan(),
        ]);
    }

    /**
     * Show no plan notification
     */
    public function showNoPlanNotification(): void
    {
        if (!$this->canAddSubAdmin) {
            session()->flash('error', __('business.sub_admin.no_active_plan'));
        }
    }
}
