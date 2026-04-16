<?php

namespace App\Http\Requests\API\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use App\Models\Business\Technician\Technician;
use App\Constants\ApiStatus;

class TechnicianLoginRequest extends FormRequest
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
            'staff_id' => ['required',
            Rule::exists('technicians', 'staff_id')->where(function ($query) {
                $query->where('status', Technician::ACTIVE_STATUS);
            })],
            'password' => 'required',
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
            'staff_id.required' => __('api.required'),
            'staff_id.exists' => __('api.invalid_credentials'),
            'password.required' => __('api.required'),
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

        $failed = $validator->failed();

        if (isset($failed['password']['Required']) || isset($failed['staff_id']['Required'])) {
            $message = __('api.credentials_required');
        } elseif (isset($failed['staff_id']['Exists'])) {
            $message = __('api.invalid_credentials');
        }

        throw new HttpResponseException(response()->json([
            'success' => false,
            'code' => ApiStatus::OK,
            'message' => $message,
            'error_code' => ApiStatus::VALIDATION_ERROR,
        ], ApiStatus::OK));
    }


}
