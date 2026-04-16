<?php

namespace App\Livewire\Admin\Business;

use App\Models\Business\Business;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Exception;

class Show extends Component
{
    public int $businessId;

    public $activeTab = 'account';

    public $business;

    public $confirm = false;

    public $confirmDelete = false;

    public $error = false;

    public $modelId = null;

    public const DEFAULT_SUBSCRIPTION = 'default';

    /**
     * Mount the component
     *
     * @param  Business  $business  Business model
     * @return void|Redirect
     */
    public function mount($businessId)
    {
        try {
            $this->businessId = $businessId;
            $this->business = Business::findOrFail($this->businessId);
        } catch (ModelNotFoundException $e) {
            session()->flash('error', __('admin.alert.business_already_deleted'));
            return redirect()->route('admin.business.index');
        }
    }

    /**
     * Render the component
     *
     * @return View
     */
    public function render(): View
    {
        return view('livewire.admin.business.show', [
            'business' => $this->business,
        ]);
    }

    public function switchTab($tab)
    {
        if ($this->activeTab === $tab) {
            return;
        }
        $this->activeTab = $tab;
    }

    /**
     * Show delete confirmation modal
     */
    public function showDeleteConfirm($businessId)
    {
        $this->modelId = $businessId;
        $this->confirm = false;
        $this->confirmDelete = true;
        $this->error = false;
    }

    /**
     * Show login confirmation modal
     */
    public function showLoginConfirm()
    {
        $this->confirmDelete = false;
        $this->confirm = true;
        $this->error = false;
    }

    /**
     * Cancel any modal actions
     */
    public function cancelAction()
    {
        $this->confirmDelete = false;
        $this->confirm = false;
        $this->error = false;
        $this->modelId = null;
    }

    /**
     * Delete a business
     *
     * @return void
     */
    public function delete()
    {
        try {
            $business = Business::findOrFail($this->modelId);
            if ($business) {
                DB::transaction(function () use ($business) {
                    $business->subscription(self::DEFAULT_SUBSCRIPTION)->cancel();
                    $business->delete();
                    $this->confirmDelete = false;
                    $this->modelId = null;
                    session()->flash('success', __('admin.alert.business_deleted'));
                });

                return redirect()->route('admin.business.index');
            }
        } catch (ModelNotFoundException $e) {
            $this->confirmDelete = false;
            $this->modelId = null;
            session()->flash('error', __('admin.alert.business_already_deleted'));

            return redirect()->route('admin.business.index');
        } catch (Exception $e) {
            $this->confirmDelete = false;
            $this->modelId = null;
            $this->error = __('admin.alert.delete_failed');
            $this->dispatch('notify');
        }
    }

    /**
     * Login as the business user
     *
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function loginAs()
    {
        try {
            $business = Business::findOrFail($this->businessId);
            // Get the primary user of the business
            $businessUser = $business->primaryUser;

            if (! $businessUser) {
                $this->confirm = false;
                session()->flash('error', __('No primary user found for this business.'));
                return false;
            }

            // Generate a signed URL with an expiration time (e.g., 5 minutes)
            $signedUrl = URL::temporarySignedRoute(
                'admin.business.auto-login',
                now()->addMinutes(60),
                ['user' => $businessUser->id]
            );

            // Mark session as impersonated
            session(['impersonated' => true]);
            $this->confirm = false;

            // Dispatch browser event to open the URL in a new tab
            $this->dispatch('openNewTab', url: $signedUrl);

        } catch (Exception $e) {
            $this->confirm = false;
            session()->flash('error', __('admin.alert.business_already_deleted'));
            return redirect()->route('admin.business.index');
        }
    }
}
