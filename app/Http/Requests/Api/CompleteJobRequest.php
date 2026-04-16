<?php

namespace App\Http\Requests\Api;

use App\Enums\WorkOrderStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Constants\ApiStatus;

class CompleteJobRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'work_order_id' => $this->route('id')
        ]);
    }

    /**
     * Get the validation rules that apply to the request
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'work_order_id' => [
                'required',
                'exists:work_orders,id',
                Rule::notIn(
                    \App\Models\Business\WorkOrder\WorkOrder::query()
                        ->whereIn('status', [WorkOrderStatus::COMPLETED, WorkOrderStatus::CANCELLED])
                        ->pluck('id')
                        ->toArray()
                ),
            ],
            'instance_id' => 'required',
            'is_recurring' => 'required|boolean',
            'preferred_start_date' => 'required|date',
            'preferred_start_time' => 'required|date_format:H:i:s',
            'customer.id' => 'required|exists:customers,id',
        ];
    }

    /**
     * Get custom messages for validator errors
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'work_order_id.required' => 'The work order ID is required.',
            'work_order_id.exists' => 'The work order does not exist.',
            'work_order_id.not_in' => __('api.job_already_completed'),
            'instance_id.required' => 'The instance ID is required.',
            'customer.id.required' => 'The customer ID is required.',
            'customer.id.exists' => 'The customer does not exist.',
            'is_recurring.required' => 'The is recurring field is required.',
            'is_recurring.boolean' => 'The is recurring field must be a boolean.',
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
