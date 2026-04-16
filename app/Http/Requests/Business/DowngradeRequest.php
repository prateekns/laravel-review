<?php

namespace App\Http\Requests\Business;

use Illuminate\Foundation\Http\FormRequest;

class DowngradeRequest extends FormRequest
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
            'admin' => ['nullable', 'numeric', 'required_without:technician'],
            'technician' => ['nullable', 'numeric', 'required_without:admin'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'admin.required' => __('validation.pricing.admin.required'),
            'technician.required' => __('validation.pricing.technician.required'),
            'admin.numeric' => __('validation.pricing.admin.numeric'),
            'technician.numeric' => __('validation.pricing.technician.numeric'),
            'admin.min' => __('validation.pricing.admin.min'),
            'technician.min' => __('validation.pricing.technician.min'),
        ];
    }
}
