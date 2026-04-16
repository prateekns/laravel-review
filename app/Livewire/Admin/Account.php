<?php

namespace App\Livewire\Admin;

use App\Helpers\FileHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Events\PasswordChanged;

class Account extends Component
{
    use WithFileUploads;

    public $user;

    public $name = '';

    public $current_password; //NOSONAR

    public $new_password; //NOSONAR

    public $confirm_password; //NOSONAR

    #[Validate('nullable|image|mimes:jpg,png,jpeg|max:1024')]
    public $avatar = '';

    public $avatarPreview;

    public function mount()
    {
        $this->user = Auth::guard('admin')->user();
        $this->name = $this->user->name;
        $this->avatarPreview = $this->user->avatar ? Storage::url($this->user->avatar) : '';
    }

    public function updatedAvatar()
    {
        $this->validate();

        try {
            $user = Auth::guard('admin')->user();
            if ($this->avatar) {

                $data['avatar'] = FileHelper::storeFile($this->avatar, 'avatars');

                // Delete old avatar if exists
                if ($user->avatar) {
                    FileHelper::deleteFile($user->avatar);
                }
                // Store new avatar using FileHelper
                $user->update($data);
                $this->avatarPreview = Storage::url($user->avatar);
                $this->avatar = null;
                $this->dispatch('avatar-updated');
            }

        } catch (\Exception $e) {
            session()->flash('error', __('admin.message.avatar_update_failed'));
        }
    }

    public function updateProfile()
    {
        $this->validateProfile();

        $user = Auth::guard('admin')->user();
        $data = ['name' => $this->name];

        try {
            $user->update($data);
            session()->flash('success', __('admin.message.profile_updated'));
            // Reload the page after a short delay to show the success message
            $this->dispatch('profile-updated');
        } catch (\Exception $e) {
            session()->flash('error', __('admin.message.profile_update_failed'));
        }
    }

    private function validateProfile()
    {
        $this->validate([
            'name' => ['required', 'max:50', 'min:3'],
        ], [
            'name.required' => __('admin.validation.required'),
        ]);
    }

    protected function validatePassword()
    {

        $this->validate([
            'current_password' => ['required', 'current_password:admin'],
            'new_password' => [
                'required',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]).{8,}$/',
                'different:current_password',
            ],
            'confirm_password' => ['required', 'same:new_password'],
        ], [
            'current_password.required' => __('admin.validation.required'),
            'current_password.current_password' => __('admin.validation.current_password_incorrect'),
            'new_password.required' => __('admin.validation.required'),
            'new_password.min' => __('admin.validation.password_min'),
            'new_password.regex' => __('admin.validation.password_rules'),
            'new_password.different' => __('admin.validation.password_different'),
            'confirm_password.required' => __('admin.validation.required'),
            'confirm_password.same' => __('admin.validation.confirm_mismatch'),
        ]);
    }

    public function updatePassword()
    {
        $this->validatePassword();

        try {
            $user = Auth::guard('admin')->user();
            $user->update([
                'password' => Hash::make($this->new_password),
            ]);

            // Logout admin and invalidate session
            event(new PasswordChanged($user));
            Auth::guard('admin')->logout();

            return redirect()->route('admin.login')->with('message', __('admin.message.password_changed'));

        } catch (\Exception $e) {
            Log::error('Password update failed: '.$e->getMessage());
            session()->flash('error', __('admin.message.password_update_failed'));
        }
    }

    public function render()
    {
        return view('livewire.admin.account');
    }
}
