<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CompleteJobRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'notes' => 'nullable|string',
            'service_images' => 'nullable|array',
            'service_images.*' => 'string',
            'customer_signature' => 'nullable|string',
            'checklist' => 'nullable|array',
            'checklist.*.checklist_item_id' => 'sometimes|integer|exists:checklist_items,id',
            'checklist.*.completed' => 'sometimes|boolean',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'service_images.array' => 'Service images must be an array',
            'service_images.*.string' => 'Each service image must be a string (base64)',
            'customer_signature.string' => 'Customer signature must be a string (base64)',
            'checklist.array' => 'Checklist must be an array',
            'checklist.*.checklist_item_id.integer' => 'Checklist item ID must be an integer',
            'checklist.*.checklist_item_id.exists' => 'Checklist item does not exist',
            'checklist.*.completed.boolean' => 'Completed status must be boolean',
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
            'status' => 'error',
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 422));
    }
}
