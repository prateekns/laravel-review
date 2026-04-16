<?php

namespace App\Http\Requests\Business;

use Illuminate\Foundation\Http\FormRequest;

class TechnicianFormRequest extends FormRequest
{
    public const MAX_LENGTH = 255;
    public const MAX_PHONE_LENGTH = 10;
    public const MIN_WORKING_DAYS = 1;

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
            'first_name' => ['required', 'string', 'min:1','max:' . self::MAX_LENGTH],
            'last_name' => ['required', 'string', 'min:1','max:' . self::MAX_LENGTH],
            'email' => ['required', 'email', 'max:' . self::MAX_LENGTH, 'unique:technicians,email,' . $this->route('technician')?->id],
            'phone' => ['required', 'string', 'max:' . self::MAX_PHONE_LENGTH],
            'skill_type' => ['required'],
            'working_days' => ['required', 'array', 'min:' . self::MIN_WORKING_DAYS],
            'status' => ['nullable'],
            'confirmed' => ['nullable', 'boolean'],
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
            'first_name.required' => __('business.customer.validation.required'),
            'first_name.min' => __('common.auth.min_1'),
            'last_name.min' => __('common.auth.min_1'),
            'last_name.required' => __('business.customer.validation.required'),
            'email.required' => __('business.customer.validation.required'),
            'phone.required' => __('business.customer.validation.required'),
            'skill_type.required' => __('business.technician.skill_type_required'),
            'working_days.required' =>  __('business.message.one_working_day_required'),
            'working_days.array' => __('business.technician.working_days_array'),
            'working_days.min' => __('business.message.one_working_day_required'),
            'email.unique' => __('common.auth.email_in_use'),
        ];
    }
}
