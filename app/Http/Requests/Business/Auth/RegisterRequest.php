<?php

namespace App\Http\Requests\Business\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'business_name' => ['required', 'string', 'max:100'],
            'email' => [
                'required',
                'email',
                'unique:businesses,email',
                'unique:business_users,email',
            ],
            'password' => [
                'required',
                'between:8,20',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]).{8,}$/',
                Password::defaults(),
                'confirmed',
            ],
            'password_confirmation' => ['required'],
        ];
    }

    public function messages(): array
    {
        return [
            'business_name.required' => __('common.auth.required'),
            'business_name.max' => __('common.auth.business_max'),
            'email.required' => __('common.auth.required'),
            'email.email' => __('common.auth.invalid_email'),
            'email.unique' => __('common.auth.account_exists'),
            'password.required' => __('common.auth.required'),
            'password_confirmation.required' => __('common.auth.required'),
            'password.regex' => __('common.auth.password_rule'),
            'password.confirmed' => __('common.auth.confirm_mismatch'),
            'password.between' => __('common.auth.password_between', ['min' => 8, 'max' => 20]),
        ];
    }
}
