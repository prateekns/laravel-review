<?php

namespace App\Livewire\Admin\SubAdmin;

use App\Mail\Admin\SubAdminWelcomeMail;
use App\Models\Admin\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Create extends Component
{
    public $name;

    public $email;

    public $password;

    public $confirm_password; //NOSONAR

    public $status;

    public ?User $user;

    public $subAdmin;

    public function mount(User $user)
    {
        $this->subAdmin = $user ? $user : new User();
        $this->name = $this->subAdmin->name;
        $this->email = $this->subAdmin->email;
        $this->status = $this->subAdmin->status;
    }

    public function render()
    {
        return view('livewire.admin.sub-admin.create', ['subAdmin' => $this->subAdmin]);
    }

    protected function rulesForCreate()
    {
        return [
            'name' => ['required', 'regex:/^[a-zA-Z\s]+$/', 'min:3', 'max:50'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                "regex:/^[^\s@]+@[^\s@]+\.[^\s@]+$/",
                Rule::unique('users', 'email'),
            ],
            'password' => [
                'required',
                "regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\\d)(?=.*[!@#\$%\^&*()_+\-=\[\]{};':\\\"\\|,.<>\/?]).{8,}$/",
            ],
            'confirm_password' => ['required', 'same:password'],
        ];
    }

    protected function rulesForUpdate()
    {
        return [
            'name' => ['required', 'min:3', 'max:50', 'regex:/^[a-zA-Z\s]+$/'],
        ];
    }

    protected function messagesForValidation()
    {
        return [
            'name.required' => __('admin.validation.required'),
            'name.regex' => __('admin.validation.only_alpha_space'),
            'name.min' => __('admin.validation.name_min_3'),
            'name.max' => __('admin.validation.name_max_50'),
            'email.required' => __('admin.validation.required'),
            'email.email' => __('admin.validation.invalid_email'),
            'email.regex' => __('admin.validation.invalid_email'),
            'email.unique' => __('admin.validation.email_exists'),
            'password.required' => __('admin.validation.required'),
            'password.min' => __('admin.validation.password_min'),
            'password.regex' => __('admin.validation.password_rules'),
            'confirm_password.required' => __('admin.validation.required'),
            'confirm_password.same' => __('admin.validation.confirm_mismatch'),
        ];
    }

    public function addAdmin()
    {
        $this->validate($this->rulesForCreate(), $this->messagesForValidation());
        try {
            $user = User::create([
                'name' => trim($this->name),
                'email' => trim($this->email),
                'password' => Hash::make($this->password),
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('admin.message.sub_admin_not_created'));
        }

        // Send welcome email with credentials
        try {
            if ($user && $this->password) {
                Mail::to($this->email)->send(new SubAdminWelcomeMail(
                    trim($this->name),
                    trim($this->email),
                    trim($this->password)
                ));
            }
        } catch (\Exception $e) {
            Log::error('Sub admin welcome email failed: Sub Admin Id: '.$user->id.' -'.$e->getMessage());
        }

        $this->reset(['name', 'email', 'password', 'confirm_password']);

        return redirect()->route('admin.sub-admin')->with('success', __('admin.message.sub_admin_created'));
    }

    public function updateAdmin()
    {
        $this->validate($this->rulesForUpdate(), $this->messagesForValidation());
        try {
            $this->subAdmin->name = $this->name;
            $this->subAdmin->status = $this->status;
            $this->subAdmin->save();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('admin.message.sub_admin_not_updated'));
        }

        return redirect()->route('admin.sub-admin')->with('success', __('admin.message.sub_admin_updated'));
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
        } catch (\Exception $e) {
            session()->flash('success', __('admin.message.sub_admin_update_failed'));
        }

        $this->selectedAdmin = null;
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
