<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Constants\ApiStatus;

class PasswordChangeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'current_password' => ['required', 'current_password'],
            'new_password' => [
                'required',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]).{8,}$/',
                'different:current_password',
                'between:8,20',
            ],
            'confirm_password' => ['required','same:new_password'],
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.required' => __('admin.validation.required'),
            'current_password.current_password' => __('api.invalid_current_password'),
            'new_password.required' => __('admin.validation.required'),
            'new_password.min' => __('admin.validation.password_min'),
            'new_password.regex' => __('admin.validation.password_rules'),
            'new_password.different' => __('api.new_password_different'),
            'confirm_password.required' => __('admin.validation.required'),
            'confirm_password.same' => __('api.passwords_do_not_match'),
            'new_password.between' => __('api.password_between'),
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'code' => ApiStatus::OK,
            'message' => $validator->errors()->first(),
            'error_code' => ApiStatus::VALIDATION_ERROR,
        ], ApiStatus::OK));
    }
}
