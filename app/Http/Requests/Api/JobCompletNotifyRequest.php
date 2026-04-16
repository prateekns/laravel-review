<?php

namespace App\Http\Requests\Api;

use App\Constants\ApiStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;
use App\Rules\ValidInstanceId;

class JobCompletNotifyRequest extends FormRequest
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
            'work_order_id' => ['required'],
            'instance_id' => [
                'required',
                new ValidInstanceId($this->work_order_id),
            ],
            'completed_at' => ['required', Rule::date()->format('Y-m-d H:i:s'),],
            'message_customer' => ['string', 'nullable'],
            'message_business' => ['string', 'nullable'],
            'attachment_customer' => ['array', 'max:2', 'nullable'],
            'attachment_customer.*' => ['file', 'mimes:jpeg,png,jpg', 'max:3072', 'nullable'],
            'attachment_business' => ['array', 'max:2', 'nullable'],
            'attachment_business.*' => ['file', 'mimes:jpeg,png,jpg', 'max:3072', 'nullable'],
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
