<?php

namespace App\Livewire\Business\SubAdmin;

use App\Models\Business\BusinessUser;
use App\Services\Business\BusinessService;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Exception;

class Create extends Component
{
    public $first_name; //NOSONAR
    public $last_name; //NOSONAR
    public $email;
    public $status;
    public $subAdmin = null;
    private $isOwnProfile = false;

    /**
     * Mount the component.
     *
     * @param BusinessUser $subAdmin
     * @return void
     */
    public function mount(?BusinessUser $subAdmin = null)
    {
        if ($subAdmin) {
            $this->subAdmin = $subAdmin ? $subAdmin : new BusinessUser();
            $this->first_name = $subAdmin->first_name;
            $this->last_name = $subAdmin->last_name;
            $this->email = $subAdmin->email;
            $this->status = $subAdmin->status;
            $this->isOwnProfile = Auth::guard('business')->user()->id === $subAdmin->id;
        }
    }

    /**
     * Construct the component.
     *
     * @return void
     */
    public function __construct()
    {
        $this->businessService = app(BusinessService::class);
    }

    /**
     * Get the validation rules.
     *
     * @return array
     */
    protected function rules()
    {
        return [
            'first_name' => ['required', 'min:1', 'max:50'],
            'last_name' => ['required', 'min:1', 'max:50'],
            'email' => ['required', 'email:dns', 'unique:business_users,email,' . $this->subAdmin->id],
        ];
    }

    /**
     * Get the messages for the validation rules.
     *
     * @return array
     */
    protected function messages()
    {
        return [
            'first_name.required' => __('common.auth.required'),
            'first_name.min' => __('common.auth.min_1'),
            'first_name.max' => __('common.auth.max_50'),
            'last_name.required' => __('common.auth.required'),
            'last_name.min' => __('common.auth.min_1'),
            'last_name.max' => __('common.auth.max_50'),
            'email.required' => __('common.auth.required'),
            'email.email' => __('common.auth.invalid_email'),
            'email.unique' => __('business.message.business_sub_admin_exists'),
        ];
    }


    /**
     * Save the sub-admin.
     *
     * @return void
     */
    public function save()
    {
        if ($this->subAdmin->id) {
            return $this->update();
        }

        $this->validate();

        try {
            $canCreateSubAdmin = true;
            // Check if business has active plan
            if (!$this->businessService->hasActivePlan()) {
                session()->flash('error', __('business.sub_admin.no_active_plan'));
                $canCreateSubAdmin = false;
            }

            // Check if business has reached sub-admin limit using BusinessService
            if ($this->businessService->isAdminLimitReached()) {
                session()->flash('error', __('business.sub_admin.limit_reached_alert'));
                $canCreateSubAdmin = false;
            }

            if (!$canCreateSubAdmin) {
                return redirect()->route('business.sub-admins.index');
            }

            // Use BusinessService to create sub-admin
            $this->businessService->createSubAdmin([
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
            ]);

            session()->flash('success', __('business.message.sub_admin_created'));

        } catch (Exception $e) {
            session()->flash('error', __('business.message.failed_to_create_sub_admin'));
        }

        return redirect()->route('business.sub-admins.index');
    }

    /**
     * Update the sub-admin.
     *
     * @return void
     */
    public function update()
    {
        $this->validate();

        try {

            if (!$this->businessService->hasActivePlan()) {
                session()->flash('error', __('business.sub_admin.no_active_plan'));
                return redirect()->route('business.sub-admins.index');
            }

            if ($this->businessService->isAdminLimitReached() && $this->status) {
                session()->flash('error', __('business.no_more_profiles'));
                return redirect()->route('account.my-plan');
            }

            return $this->processUpdate();
        } catch (Exception $e) {
            session()->flash('error', __('business.message.failed_to_update_sub_admin'));
        }
    }

    /**
     * Update the status of the sub-admin.
     *
     * @return void
     */
    public function updateStatus()
    {
        try {
            if (!$this->businessService->hasActivePlan()) {
                session()->flash('error', __('business.no_active_plan'));
                return redirect()->route('business.sub-admins.index');
            }

            // Prevent deactivating own profile
            if (Auth::guard('business')->user()->id === $this->subAdmin->id && !$this->status) {
                session()->flash('error', __('business.sub_admin.cannot_deactivate_own_profile'));
                $this->dispatch('close-cancel');
                return redirect()->route('business.sub-admins.index');
            }

            $this->subAdmin->status = $this->status;
            $this->subAdmin->save();
            $this->dispatch('close');
            session()->flash('success', __('business.message.sub_admin_updated'));

        } catch (Exception $e) {
            session()->flash('error', __('business.message.sub_admin_update_failed'));
        }

        return redirect()->route('business.sub-admins.index');
    }

    /**
     * Process the update of the sub-admin.
     *
     * @return void
     */
    protected function processUpdate()
    {
        try {

            // Prevent deactivating own profile
            if (Auth::guard('business')->user()->id === $this->subAdmin->id && !$this->status) {
                session()->flash('error', __('business.sub_admin.cannot_deactivate_own_profile'));
                $this->dispatch('close-cancel');
                return redirect()->route('business.sub-admins.index');
            }

            // Use BusinessService to update sub-admin
            $this->businessService->updateSubAdmin($this->subAdmin->id, [
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'status' => $this->status,
            ]);

            session()->flash('success', __('business.message.sub_admin_updated'));

        } catch (Exception $e) {
            session()->flash('error', __('business.message.sub_admin_update_failed'));
        }

        return redirect()->route('business.sub-admins.index');
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render()
    {
        $limitReached = $this->businessService->isAdminLimitReached();
        return view('livewire.business.sub-admin.form', ['subAdmin' => $this->subAdmin, 'limitReached' => $limitReached]);
    }
}
