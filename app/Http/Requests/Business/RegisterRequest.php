<?php

namespace App\Http\Requests\Business;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'business_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:businesses'],
            'password' => ['required', 'confirmed', Password::defaults()],
            // 'phone' => ['nullable', 'string', 'max:20'],
            // 'address' => ['nullable', 'string', 'max:255'],
            // 'city' => ['nullable', 'string', 'max:100'],
            // 'state' => ['nullable', 'string', 'max:100'],
            // 'zip_code' => ['nullable', 'string', 'max:20'],
            // 'business_type' => ['nullable', 'string', 'max:100'],
            // 'description' => ['nullable', 'string'],
            // 'website' => ['nullable', 'url', 'max:255'],
        ];
    }
}
