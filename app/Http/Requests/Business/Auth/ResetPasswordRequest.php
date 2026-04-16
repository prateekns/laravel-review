<?php

namespace App\Http\Requests\Business\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => [
                'required',
                'between:8,20',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]).{8,}$/',
                'confirmed',
            ],
            'password_confirmation' => ['required'],
        ];
    }

    public function messages(): array
    {
        return [
            'password.required' => __('common.auth.required'),
            'password.confirmed' => __('common.auth.confirm_mismatch'),
            'password.regex' => __('common.auth.password_rule'),
            'password_confirmation.required' => __('common.auth.required'),
            'password.between' => __('common.auth.password_between', ['min' => 8, 'max' => 20]),
        ];
    }
}
