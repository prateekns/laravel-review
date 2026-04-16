<?php

namespace App\Livewire\Admin\Business\Tabs;

use App\Models\Business\Business;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Exception;

class Account extends Component
{
    use WithPagination;

    public Business $business;

    public ?string $trial_end_at = null; //NOSONAR

    public function mount(Business $business)
    {
        $this->business = $business;
        $this->trial_end_at = Carbon::parse($this->business->trial_end_at)->format('Y-m-d');
    }

    /**
     * Get the validation rules.
     *
     * @return array
     */
    protected function rules()
    {
        return [
            'trial_end_at' => ['required', 'date', 'after_or_equal:today'],
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
            'trial_end_at.required' => __('admin.validation.required'),
            'trial_end_at.after_or_equal' => __('admin.validation.trial_end_at_after_or_equal'),
            'trial_end_at.date' => __('admin.validation.date'),
        ];
    }

    /**
     * Update the trial end date for the current business
     *
     * @return void
     */
    public function updateTrialEndAt(): void
    {
        $this->validate();

        try {
            $this->business->update([
                'trial_end_at' => Carbon::parse($this->trial_end_at)->endOfDay(),
            ]);

            $this->dispatch('update-success', [
                'message' => __('admin.message.trial_date_updated')
            ]);
        } catch (Exception $e) {
            Log::error('Failed to update trial end date: ' . $e->getMessage());
            $this->dispatch('update-error', [
                'message' => __('admin.message.trial_date_update_failed')
            ]);
        }
    }


    /**
     * Render the component
     *
     * @return View
     */
    public function render(): View
    {
        return view('livewire.admin.business.tabs.account', [
            'business' => null,
            'trial_end_date' => Carbon::parse($this->business->trial_end_at)->format('Y-m-d'),
            'is_trial_active' => $this->business->trial_end_at ? $this->business->trial_end_at->isFuture() : false,
            'is_subscribed' => $this->business->subscribed(),
            'subscription' => $this->business->subscription()
        ]);
    }
}
