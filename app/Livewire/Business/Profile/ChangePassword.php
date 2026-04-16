<?php

namespace App\Livewire\Business\Profile;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;
use Illuminate\View\View;
use App\Events\PasswordChanged;
use App\Models\Business\BusinessUser;

class ChangePassword extends Component
{
    public $user;

    #[Validate('required|current_password:business', message: [
        'required' => 'common.auth.required',
        'current_password' => 'common.validation.current_password_incorrect'
    ])]
    public $current_password = ''; //NOSONAR

    #[Validate('required|between:8,20|regex:#^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$#|different:current_password')]
    public $password = '';

    #[Validate('required|same:password', message: [
        'required' => 'common.auth.required',
        'same' => 'common.auth.confirm_mismatch'
    ])]
    public $confirm_password = ''; //NOSONAR


    /**
     * Mount the component
     *
     * @return void
     */
    public function mount(): void
    {
        $this->user = Auth::guard('business')->user();
    }

    /**
     * Get custom validation messages
     *
     * @return array
     */
    protected function messages(): array
    {
        return [
            'password.required' => __('common.auth.required'),
            'password.between' => __('common.auth.password_between'),
            'password.regex' => __('common.auth.password_rule'),
            'password.different' => __('common.validation.no_same_password'),
        ];
    }

    /**
     * Change the password
     *
     * @return Redirector|RedirectResponse
     */
    public function changePassword(): Redirector|RedirectResponse
    {
        $this->validate();

        try {
            /** @var BusinessUser $user */
            $user = Auth::guard('business')->user();
            $user->password = Hash::make($this->password);
            $user->save();

            // Logout admin and invalidate session
            event(new PasswordChanged($user));
            Auth::guard('business')->logout();

            return redirect()->route('login')->with('status', __('common.message.business_password_changed'));

        } catch (\Exception $e) {
            session()->flash('error', __('admin.message.password_update_failed'));
            return back();
        }
    }

    /**
     * Render the component
     *
     * @return View
     */
    public function render(): View
    {
        return view('livewire.business.profile.change-password');
    }
}
