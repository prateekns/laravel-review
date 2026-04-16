<?php

namespace App\Http\Requests\Business;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class CreatePriceRequest extends FormRequest
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
            'payment_uuid' => [
                'required',
                'string',
                'exists:orders,payment_uuid,status,pending',
            ],
            'interval' => ['required', 'string', 'in:month,half-yearly,year'],
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
            'payment_uuid.required' => __('payments.invalid_payment_session'),
            'payment_uuid.exists' => __('payments.invalid_payment_session'),
            'interval.required' => __('payments.billing_cycle_required'),
            'interval.in' => __('payments.billing_cycle_in'),
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        if ($this->expectsJson()) {
            throw new ValidationException($validator, response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422));
        }

        throw (new ValidationException($validator))
            ->errorBag($this->errorBag)
            ->redirectTo($this->getRedirectUrl());
    }
}
