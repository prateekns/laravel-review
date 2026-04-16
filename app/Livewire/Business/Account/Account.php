<?php

namespace App\Livewire\Business\Account;

use Illuminate\Support\Facades\Auth;
use App\Services\Business\SubscriptionService;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Helpers\FileHelper;
use Exception;
use App\Models\Shared\Setting;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Route;

class Account extends Component
{
    use WithFileUploads;
    public $activeTab = 'index';
    public $subscriptionDetails;
    public $logo;
    public $logoUrl;
    public $logoPath = 'business/profile-images';
    public $thumbPath = 'business/profile-images/thumbnail';
    public $user;
    public string $adminEmail;

    /**
     * Mount the component.
     *
     * @param SubscriptionService $subscriptionService
     * @return void
     */
    public function mount(SubscriptionService $subscriptionService): void
    {
        $this->user = Auth::guard('business')->user();
        $settings = Setting::first();
        $this->subscriptionDetails = $subscriptionService->getSubscriptionDetails();
        $this->logoUrl = $this->getlogoUrl();
        $this->adminEmail = $settings?->info_email ?? config('app.contact.email');
        $this->setActiveTab();
    }

    public function getlogoUrl(): ?string
    {
        if ($this->user->business->logo) {
            return Storage::disk(config('filesystems.default'))->url($this->thumbPath . '/' . $this->user->business->logo);
        }

        return $this->user->business->business_logo;
    }

    /**
     * Set the active tab.
     *
     * @return void
     */
    private function setActiveTab(): void
    {
        $routeName = Route::currentRouteName();
        $this->activeTab = match ($routeName) {
            'account.index' => 'index',
            'account.profile' => 'profile',
            'account.my-plan' => 'my-plan',
            default => 'index',
        };
    }


    /**
     * Validate the profile
     *
     * @return void
     */
    public function validateLogo(): void
    {
        $this->validate([
            'logo' => ['image', 'max:3072', 'extensions:jpeg,png,jpg', 'dimensions:max_width=1024,max_height=1024'],
        ], [
            'logo.image' => __('business.message.logo_mime_error'),
            'logo.max' => __('business.message.logo_size_error'),
            'logo.dimensions' => __('business.message.logo_dimensions_error'),
            'logo.extensions' => __('business.message.logo_mime_error'),
        ]);
    }

    /**
     * Update the logo.
     *
     * @param string $value
     * @return void
     */
    public function updatedLogo($value): void
    {
        $this->validateLogo();
        try {

            $imagePath = 'business/profile-images';
            $thumbPath = $imagePath . '/thumbnail';
            $thumbnailWidth = 400;
            $thumbnailHeight = 400;

            $path = FileHelper::uploadFileWithThumbnailToS3($this->logo, $imagePath, $thumbPath, $thumbnailWidth, $thumbnailHeight);
            if (! $path) {
                session()->flash('error', __('business.message.logo_upload_failed'));
            }

            $this->logoUrl = Storage::disk(config('filesystems.default'))->url($thumbPath . '/' . $path);

            $this->user->business->logo = $path;
            $this->user->business->save();
            $this->dispatch('logo-uploaded', $this->logoUrl);

        } catch (Exception $e) {
            session()->flash('error', __('business.message.logo_upload_failed'));
        }
    }

    /**
     * Cancel the subscription.
     *
     * @return void
     */
    public function cancelSubscription(): void
    {
        $this->dispatch('close-cancel');
        session()->put('tab', 'my-plan');
        $response = app(SubscriptionService::class)->cancelSubscription();
        if ($response['success']) {
            session()->flash('success', $response['message']);
        } else {
            session()->flash('error', $response['message']);
        }

        $this->redirect(route('account.my-plan'));
    }

    /**
     * Resume the subscription.
     *
     * @return void
     */
    public function resumeSubscription(): void
    {
        $this->user->business->subscription('default')->resume();
        $this->redirect(route('account.my-plan'));
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render(): View
    {
        return view('livewire.business.account.index', $this->subscriptionDetails);
    }
}
