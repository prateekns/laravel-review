<?php

namespace App\Livewire\Business;

use Livewire\Component;
use App\Models\Shared\Setting;
use App\Mail\Business\BusinessFeedbackMail;
use Illuminate\Support\Facades\Mail;
use App\Models\Business\Business;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * Business Footer Livewire Component
 *
 * @package App\Livewire\Business
 */
class Footer extends Component
{
    /**
     * The website URL
     *
     * @var string
     */
    public string $websiteUrl;

    /**
     * The email address
     *
     * @var string
     */
    public string $emailAddress;

    /**
     * The admin email address
     *
     * @var string
     */
    public string $adminEmail;

    /**
     * The phone number
     *
     * @var string
     */
    public string $phoneNumber;

    /**
     * The feedback message
     *
     * @var string
     */
    public string $feedback = '';

    /**
     * The show feedback modal
     *
     * @var bool
     */
    public bool $showFeedbackModal = false;

    /**
     * Get the validation rules.
     *
     * @return array
     */
    protected function rules()
    {
        return [
            'feedback' => ['required','min:5', 'max:200'],
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
            'feedback.min' => __('business.feedback.min_length'),
            'feedback.required' => __('business.feedback.min_length'),
            'feedback.max' => __('business.feedback.max_length'),
        ];
    }

    /**
     * Initialize the component state.
     *
     * @return void
     */
    public function mount()
    {
        $settings = Setting::first();

        $this->websiteUrl = $settings?->website ?? config('app.contact.website_url');
        $this->emailAddress = $settings?->info_email ?? config('app.contact.email');
        $this->adminEmail = $settings?->admin_email ?? config('app.admin_email');
        $this->phoneNumber = $settings?->phone ?? config('app.contact.phone');
        $this->feedback = '';
    }

    /**
     * Send the feedback
     *
     * @return void
     */
    public function sendFeedback()
    {
        $this->validate();

        try {
            $business = auth()->user()->business;

            Mail::to($this->adminEmail)
                ->send(new BusinessFeedbackMail(
                    business: $business,
                    feedbackMessage: $this->feedback
                ));

            $this->showFeedbackModal = false;
            $this->feedback = '';

            $this->dispatch('feedback-success', [
                'message' => __('business.feedback.success')
            ]);
        } catch (Exception $e) {
            Log::error('Error sending feedback: ' . $e->getMessage());
            $this->dispatch('feedback-error', [
                'message' => __('business.feedback.error')
            ]);
        }
    }

    /**
     * Close the feedback modal
     *
     * @return void
     */
    public function closeFeedbackModal()
    {
        $this->showFeedbackModal = false;
        $this->feedback = '';
    }

    /**
     * Render the component
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.business.footer');
    }
}
