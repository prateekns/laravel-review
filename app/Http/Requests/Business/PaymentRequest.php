<?php

namespace App\Http\Requests\Business;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class PaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * @return bool
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
            'payment_uuid' => ['required', 'exists:orders,payment_uuid'],
            'card_holder_name' => ['required', 'string'],
            'payment_method' => [
                'required',
                'string',
                'starts_with:pm_',
            ],
            'interval' => [
                'required',
                'string',
                'in:daily,monthly,half-yearly,yearly'
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
            'payment_uuid.required' => __('payments.invalid_payment_session'),
            'payment_uuid.exists' => __('payments.invalid_payment_session'),
            'card_holder_name.required' => __('payments.card_holder_name_required'),
            'payment_method.required' => __('business.message.payment_method_required'),
            'payment_method.string' => __('business.message.payment_method_required'),
            'payment_method.starts_with' => __('business.message.payment_method_required'),
            'interval.required' => __('payments.billing_cycle_required'),
            'interval.in' => __('payments.billing_cycle_in')
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
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422)
        );
    }
}
