<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use App\Traits\ApiResponse;

class SignatureRequest extends FormRequest
{
    use ApiResponse;

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
            'device_id' => 'required|string|max:255',
            'timestamp' => 'required|integer|digits:10',
            'secret' => 'required|string',
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
            'device_id.required' => __('api.device_id_required'),
            'device_id.string' => __('api.device_id_string'),
            'device_id.max' => __('api.device_id_max'),
            'timestamp.required' => __('api.timestamp_required'),
            'timestamp.integer' => __('api.unix_timestamp'),
            'timestamp.digits' => __('api.unix_timestamp'),
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator): void
    {

        $errors = collect($validator->errors()->toArray())
        ->map(function ($messages) {
            return $messages[0]; // Only return the first error message for each field
        });

        throw new HttpResponseException(
            response()->json([
                'status' => 'error',
                'code' => 422,
                'errors' => $errors,
            ], Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
