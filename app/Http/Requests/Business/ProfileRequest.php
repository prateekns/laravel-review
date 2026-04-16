<?php

namespace App\Http\Requests\Business;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only authenticated business users can update their profile
        return auth()->guard('business')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'business_name' => ['required', 'string', 'min:2'],
            'phone' => ['required', 'string'],
        ];
    }

    /**
     * Custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'business_name.required' => __('business.name_required'),
            'business_name.min' => __('business.name_min', ['min' => 2]),
            'phone.required' => __('business.phone_required', ['field' => 'Phone']),
        ];
    }
}
