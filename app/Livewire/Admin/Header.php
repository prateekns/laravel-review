<?php

namespace App\Livewire\Admin;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Header extends Component
{
    public $showMobileMenu = false;

    protected $listeners = ['avatar-updated' => '$refresh'];

    public function toggleMobileMenu()
    {
        $this->showMobileMenu = ! $this->showMobileMenu;
    }

    public function closeMobileMenu()
    {
        $this->showMobileMenu = false;
    }

    /*
     * Logout the Super Admin user
     *
     * @return \Livewire\Features\SupportRedirects\Redirector
     */
    public function logout()
    {
        try {
            Auth::guard('admin')->logout();
            session()->invalidate();
            session()->regenerateToken();

            return redirect()->route('admin.login')->with('message', __('messages.logout_success'));
        } catch (\Exception $e) {
            return redirect()->back()->with('message', __('messages.logout_failed'));
        }
    }

    /*
     * Render the header component
     *
     * @return View
     */
    public function render(): View
    {
        return view('livewire.admin.layout.header', [
            'user' => Auth::guard('admin')->user(),
        ]);
    }
}
