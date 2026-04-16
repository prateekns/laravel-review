<?php

namespace App\Http\Requests\Business;

use App\Helpers\FileHelper;
use Illuminate\Foundation\Http\FormRequest;

class OnboardingRequest extends FormRequest
{
    public const ALPHA_SPACES_REGEX = 'regex:/^[a-zA-Z\s]+$/';
    public const MIN_LENGTH_1 = 'min:1';
    public const MIN_LENGTH_2 = 'min:2';
    public const MIN_LENGTH_3 = 'min:3';
    public const MAX_LENGTH_12 = 'max:12';
    public const MAX_LENGTH_50 = 'max:50';
    public const ZIPCODE_REGEX = 'regex:/^[a-zA-Z0-9 .\-_]+$/';

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
            // Personal Information
            'first_name' => ['required', self::ALPHA_SPACES_REGEX, self::MIN_LENGTH_1, self::MAX_LENGTH_50],
            'last_name' => ['required', self::ALPHA_SPACES_REGEX, self::MIN_LENGTH_1, self::MAX_LENGTH_50],
            'phone_number' => ['required', 'regex:/^\+?[\d\s-]{10,}$/'],

            // Business Information
            'business_name' => ['required', self::ALPHA_SPACES_REGEX, self::MIN_LENGTH_1],
            'website_url' => ['nullable', 'min:5', 'max:100'],
            'logo' => [function ($attribute, $value, $fail) { //NOSONAR
                $size = FileHelper::imgSize($value);
                if ($size['mb'] > 3) {
                    return $fail(__('common.auth.business_logo'));
                }
            }],

            // Address Information
            'address' => ['required', 'string', 'min:5', 'max:200'],
            'street' => ['required', 'string', 'min:3', 'max:100'],
            'country' => ['required', 'exists:countries,id'],
            'state' => ['required', 'exists:states,id'],
            'city' => ['required', 'exists:cities,id'],
            'zipcode' => ['required', 'string', self::MIN_LENGTH_3,self::MAX_LENGTH_12, self::ZIPCODE_REGEX],
            'timezone' => ['required', 'string'],
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
            // Personal Information
            'first_name.required' => __('common.auth.required'),
            'first_name.min' => __('common.auth.required'),
            'first_name.max' => __('common.auth.max_50'),
            'last_name.max' => __('common.auth.max_50'),
            'first_name.regex' => __('common.auth.alpha_only'),
            'last_name.required' => __('common.auth.required'),
            'last_name.min' => __('common.auth.required'),
            'last_name.regex' => __('common.auth.alpha_only'),
            'email.required' => __('common.auth.required'),
            'email.email' => __('common.auth.invalid_email'),
            'email.unique' => __('common.auth.account_exists'),
            'phone_number.required' => __('common.auth.required'),
            'phone_number.regex' => __('common.auth.invalid_phone'),

            // Business Information
            'business_name.required' => __('common.auth.required'),
            'business_name.min' => __('common.auth.required'),
            'business_name.regex' => __('common.auth.alpha_only'),
            'website_url.url' => __('common.auth.website_url'),
            'logo.image' => __('common.auth.business_logo'),
            'logo.max' => __('common.auth.business_logo'),

            // Address Information
            'address.required' => __('common.auth.required'),
            'address.min' => __('common.auth.min_5_max_200'),
            'address.max' => __('common.auth.min_5_max_200'),
            'street.required' => __('common.auth.required'),
            'street.min' => __('common.auth.min_3_max_100'),
            'street.max' => __('common.auth.min_3_max_100'),
            'country.required' => __('common.auth.required'),
            'state.required' => __('common.auth.required'),
            'city.required' => __('common.auth.required'),
            'zipcode.required' => __('common.auth.required'),
            'zipcode.regex' => __('common.auth.zipcode_error'),
            'zipcode.min' => __('common.auth.zipcode_min_3_max_12'),
            'zipcode.max' => __('common.auth.zipcode_min_3_max_12'),
            'timezone.required' => __('common.auth.required'),
        ];
    }
}
