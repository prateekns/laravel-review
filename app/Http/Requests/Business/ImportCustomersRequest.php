<?php

namespace App\Http\Requests\Business;

use Illuminate\Foundation\Http\FormRequest;

class ImportCustomersRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->guard('business')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'csvFile' => [
                'required',
                'file',
                'max:5120', // 5MB max
                function ($attribute, $value, $fail) { //NOSONAR
                    // Check if file is empty
                    if ($value->getSize() === 0) {
                        $fail(__('business.customers.import.validation.empty_file'));
                        return;
                    }

                    $extension = strtolower($value->getClientOriginalExtension());
                    $mimeType = $value->getMimeType();

                    $validMimeTypes = [
                        'text/csv',
                        'text/plain',
                        'application/csv',
                        'application/vnd.ms-excel',
                    ];

                    if ($extension !== 'csv' || !in_array($mimeType, $validMimeTypes)) {
                        $fail(__('business.customers.import.validation.format'));
                    }
                }
            ]
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
            'csvFile.required' => __('business.customers.import.validation.required'),
            'csvFile.file' => __('business.customers.import.validation.invalid'),
            'csvFile.mimes' => __('business.customers.import.validation.format'),
            'csvFile.max' => __('business.customers.import.validation.size'),
        ];
    }
}
