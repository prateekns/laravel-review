<?php

namespace App\Http\Requests\Business\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ForgotPasswordRequest extends FormRequest
{
    protected $redirectRoute = 'password.request';

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'exists:business_users,email'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => __('common.auth.required'),
            'email.email' => __('common.auth.invalid_email'),
            'email.exists' => __('common.validation.email_not_found'),
        ];
    }
}
