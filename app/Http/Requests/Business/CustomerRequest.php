<?php

namespace App\Http\Requests\Business;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

/**
 * Form request validation for customer-related operations.
 *
 * This class handles validation rules for customer creation, updates,
 * and imports. It includes rules for personal information, contact details,
 * pool specifications, and equipment details.
 */
class CustomerRequest extends FormRequest
{
    /**
     * Base validation rule prefixes
     */
    public const RULE_MAX_PREFIX = 'max:';
    public const RULE_MIN_PREFIX = 'min:';

    /**
     * Common validation rule combinations
     */
    public const RULE_REQUIRED_STRING_100 = ['required', 'string', self::MAX_LENGTH_100];
    public const RULE_REQUIRED_STRING_ADDRESS = ['required', 'string', self::MAX_LENGTH_200, self::MIN_LENGTH_3];
    public const RULE_NULLABLE_STRING_ADDRESS_3_200 = ['nullable', 'string', self::MAX_LENGTH_200, self::MIN_LENGTH_3];
    public const RULE_NULLABLE_STRING_500 = ['nullable', 'string', self::MAX_LENGTH_500];
    public const RULE_NULLABLE_IMAGE = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'];

    /**
     * Validation length constraints
     */
    public const MAX_LENGTH_100 = self::RULE_MAX_PREFIX . self::LENGTH_100;
    public const MAX_LENGTH_200 = self::RULE_MAX_PREFIX . self::LENGTH_200;
    public const MIN_LENGTH_3 = self::RULE_MIN_PREFIX . self::LENGTH_3;
    public const MAX_LENGTH_255 = self::RULE_MAX_PREFIX . self::LENGTH_255;
    public const MAX_LENGTH_500 = self::RULE_MAX_PREFIX . self::LENGTH_500;
    public const MIN_LENGTH_1 = self::RULE_MIN_PREFIX . self::LENGTH_1;
    public const MIN_LENGTH_4 = self::RULE_MIN_PREFIX . self::LENGTH_4;

    /**
     * Regular expression patterns for field validation
     */
    public const REGEX_ALPHANUMERIC = 'regex:/^[a-zA-Z0-9\s\'-]+$/';
    public const REGEX_NAME = 'regex:/^[a-zA-Z\s\'-]+$/';
    public const REGEX_PHONE = 'regex:/^\+?[\d\s-]{10,}$/';
    public const REGEX_ZIP_CODE = 'regex:/^[a-zA-Z0-9\s\-\.]+$/';
    public const REGEX_DECIMAL_NUMBER = 'regex:/^\d+(\.\d{1,2})?$/';

    /**
     * Base numeric length values
     */
    private const LENGTH_1 = 1;      // Minimum name length
    private const LENGTH_2 = 2;      // Minimum name length
    private const LENGTH_3 = 3;      // Minimum address length
    private const LENGTH_4 = 4;      // Minimum zip code length
    private const LENGTH_20 = 20;    // Phone number, zip code max length
    private const LENGTH_50 = 50;    // Name max length, pool depth
    private const LENGTH_100 = 100;  // Basic field max length
    private const LENGTH_200 = 200;  // Commercial details max length
    private const LENGTH_255 = 255;  // Extended field max length
    private const LENGTH_500 = 500;  // Notes max length
    private const LENGTH_999 = 999;  // Pool length/width max
    private const LENGTH_1000 = 1000;// Minimum pool size in gallons    //NOSONAR
    private const LENGTH_999999 = 999999; // Maximum pool size in gallons

    /**
     * Field-specific validation rules
     */
    private const RULE_REQUIRED_STRING = ['required', 'string'];
    private const RULE_NULLABLE_STRING = ['nullable', 'string'];
    private const RULE_NULLABLE_NUMERIC = ['nullable', 'numeric'];
    private const RULE_REQUIRED_EMAIL = ['required', 'email'];
    private const RULE_NULLABLE_EMAIL = ['nullable', 'email'];

    /**
     * Field-specific length constraints
     */
    private const MAX_PHONE_LENGTH = self::LENGTH_20;
    private const MAX_COMMERCIAL_DETAILS = self::LENGTH_200;
    private const MAX_NAME_LENGTH = self::LENGTH_50;

    /**
     * Pool-specific dimension constraints
     */
    private const MAX_POOL_SIZE_GALLONS = self::LENGTH_999999;
    private const MIN_POOL_SIZE_GALLONS = self::LENGTH_100;
    private const MAX_POOL_LENGTH = self::LENGTH_999;
    private const MAX_POOL_DEPTH = self::LENGTH_50;
    private const MIN_POOL_DIMENSION = 1;

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
        return $this->getCommonRules() + self::getEquipmentImageRules();
    }

    /**
     * Get common validation rules that apply to all operations.
     *
     * @return array<string, mixed>
     */
    private static function getCommonRules(): array
    {
        return [
            'pool_type' => [
                'sometimes',  // Only validate if the field is present
                'nullable',   // Allow null in edit mode
                'integer',
                'in:1,2'
            ],
            'first_name' => [
                'nullable',
                'required_if:pool_type,1',  // Required only for residential pools
                'string',
                'min:' . self::LENGTH_1,
                'max:' . self::MAX_NAME_LENGTH,
                self::REGEX_NAME
            ],
            'last_name' => [
                'nullable',
                'required_if:pool_type,1',  // Required only for residential pools
                'string',
                'min:' . self::LENGTH_1,
                'max:' . self::MAX_NAME_LENGTH,
                self::REGEX_NAME
            ],
            'email_1' => [...self::RULE_REQUIRED_EMAIL, self::MAX_LENGTH_255],
            'email_2' => [...self::RULE_NULLABLE_EMAIL, self::MAX_LENGTH_255],
            'isd_code' => ['nullable'],
            'phone_1' => [
                ...self::RULE_REQUIRED_STRING,
                'max:' . self::MAX_PHONE_LENGTH,
                self::REGEX_PHONE
            ],
            'phone_2' => [
                ...self::RULE_NULLABLE_STRING,
                'max:' . self::MAX_PHONE_LENGTH,
                self::REGEX_PHONE
            ],
            'status' => ['boolean'],
            'address' => self::RULE_REQUIRED_STRING_ADDRESS,
            'street' => self::RULE_NULLABLE_STRING_ADDRESS_3_200,
            'zip_code' => [
                ...self::RULE_REQUIRED_STRING,
                'min:3',
                'max:12',
                self::REGEX_ZIP_CODE
            ],
            'city' => self::RULE_REQUIRED_STRING_100,
            'state' => self::RULE_REQUIRED_STRING_100,
            'country_id' => ['required', 'exists:countries,id'],
            'commercial_pool_details' => [
                'required_if:pool_type,2',  // Required only for commercial pools
                'nullable',
                'string',
                'max:' . self::MAX_COMMERCIAL_DETAILS
            ],
            'pool_size_gallons' => [
                'nullable',
                'numeric',
                self::REGEX_DECIMAL_NUMBER,
                'min:' . self::MIN_POOL_SIZE_GALLONS,
                'max:' . self::MAX_POOL_SIZE_GALLONS
            ],
            'pool_length' => [
                ...self::RULE_NULLABLE_NUMERIC,
                self::REGEX_DECIMAL_NUMBER,
                'min:' . self::MIN_POOL_DIMENSION,
                'max:' . self::MAX_POOL_LENGTH
            ],
            'pool_width' => [
                ...self::RULE_NULLABLE_NUMERIC,
                self::REGEX_DECIMAL_NUMBER,
                'min:' . self::MIN_POOL_DIMENSION,
                'max:' . self::MAX_POOL_LENGTH
            ],
            'pool_depth' => [
                ...self::RULE_NULLABLE_NUMERIC,
                self::REGEX_DECIMAL_NUMBER,
                'min:' . self::MIN_POOL_DIMENSION,
                'max:' . self::MAX_POOL_DEPTH
            ],
            'clean_psi' => [
                ...self::RULE_NULLABLE_STRING,
                self::MAX_LENGTH_100,
                self::REGEX_ALPHANUMERIC
            ],
            'entry_instruction' => self::RULE_NULLABLE_STRING_500,
            'tech_notes' => self::RULE_NULLABLE_STRING_500,
            'admin_notes' => self::RULE_NULLABLE_STRING_500,
            'filter_details' => [
                ...self::RULE_NULLABLE_STRING,
                self::MAX_LENGTH_100,
                self::REGEX_ALPHANUMERIC
            ],
            'pump_details' => [
                ...self::RULE_NULLABLE_STRING,
                self::MAX_LENGTH_100,
                self::REGEX_ALPHANUMERIC
            ],
            'cleaner_details' => [
                ...self::RULE_NULLABLE_STRING,
                self::MAX_LENGTH_100,
                self::REGEX_ALPHANUMERIC
            ],
            'heater_details' => [
                ...self::RULE_NULLABLE_STRING,
                self::MAX_LENGTH_100,
                self::REGEX_ALPHANUMERIC
            ],
            'heat_pump_details' => [
                ...self::RULE_NULLABLE_STRING,
                self::MAX_LENGTH_100,
                self::REGEX_ALPHANUMERIC
            ],
            'aux_details' => [
                ...self::RULE_NULLABLE_STRING,
                self::MAX_LENGTH_100,
                self::REGEX_ALPHANUMERIC
            ],
            'aux2_details' => [
                ...self::RULE_NULLABLE_STRING,
                self::MAX_LENGTH_100,
                self::REGEX_ALPHANUMERIC
            ],
            'salt_system_details' => [
                ...self::RULE_NULLABLE_STRING,
                self::MAX_LENGTH_100,
                self::REGEX_ALPHANUMERIC
            ]
        ];
    }

    /**
     * Get validation rules for equipment images
     *
     * @return array
     */
    private static function getEquipmentImageRules(): array
    {
        return [
            'clean_psi_image' => self::RULE_NULLABLE_IMAGE,
            'filter_image' => self::RULE_NULLABLE_IMAGE,
            'pump_image' => self::RULE_NULLABLE_IMAGE,
            'cleaner_image' => self::RULE_NULLABLE_IMAGE,
            'heater_image' => self::RULE_NULLABLE_IMAGE,
            'heat_pump_image' => self::RULE_NULLABLE_IMAGE,
            'aux_image' => self::RULE_NULLABLE_IMAGE,
            'aux2_image' => self::RULE_NULLABLE_IMAGE,
            'salt_system_image' => self::RULE_NULLABLE_IMAGE
        ];
    }

    /**
     * Get validation rules specific to import operations
     *
     * @return array
     */
    private static function getImportRules(): array
    {
        return [
            'business_id' => 'required|exists:businesses,id',
            'pool_type' => 'required|in:1,2',
            'country_id' => ['nullable'],
        ];
    }

    /**
     * Validate customer data for import
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public static function validateImportData(array $data): \Illuminate\Contracts\Validation\Validator
    {
        $rules = array_merge(self::getCommonRules(), self::getImportRules());

        return Validator::make($data, $rules, self::getValidationMessages());
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return self::getValidationMessages();
    }

    /**
     * Get validation messages for all scenarios
     *
     * @return array<string, string>
     */
    private static function getValidationMessages(): array
    {
        return [
            'first_name.regex' => __('business.customer.validation.special_chars_not_allowed'),
            'last_name.regex' => __('business.customer.validation.special_chars_not_allowed'),
            'first_name.min' => __('business.customer.validation.min_max_characters', ['min' => self::LENGTH_1, 'max' => self::MAX_NAME_LENGTH]),
            'first_name.max' => __('business.customer.validation.min_max_characters', ['min' => self::LENGTH_2, 'max' => self::MAX_NAME_LENGTH]),
            'last_name.min' => __('business.customer.validation.min_max_characters', ['min' => self::LENGTH_1, 'max' => self::MAX_NAME_LENGTH]),
            'last_name.max' => __('business.customer.validation.min_max_characters', ['min' => self::LENGTH_2, 'max' => self::MAX_NAME_LENGTH]),
            'email' => __('business.customer.validation.email'),
            'phone_1.regex' => __('business.customer.validation.phone_invalid'),
            'phone_2.regex' => __('business.customer.validation.phone_invalid'),
            'zip_code.regex' => __('business.customer.validation.zip_code_invalid'),
            'zip_code.min' => __('business.customer.validation.min_max_characters', ['min' => 3, 'max' => 12]),
            'zip_code.max' => __('business.customer.validation.min_max_characters', ['min' => 3, 'max' => 12]),
            'first_name.required' => __('business.customer.validation.required'),
            'last_name.required' => __('business.customer.validation.required'),
            'email_1.required' => __('business.customer.validation.required'),
            'email_1.email' => __('business.customer.validation.email_invalid'),
            'email_2.email' => __('business.customer.validation.email_invalid'),
            'phone_1.required' => __('business.customer.validation.required'),
            'address.required' => __('business.customer.validation.required'),
            'street.required' => __('business.customer.validation.required'),
            'zip_code.required' => __('business.customer.validation.required'),
            'city.required' => __('business.customer.validation.required'),
            'state.required' => __('business.customer.validation.required'),
            'state.string' => __('business.customer.validation.invalid_state'),
            'state.max' => __('business.customer.validation.max_100'),
            'country.required' => __('business.customer.validation.required'),
            'country.string' => __('business.customer.validation.invalid_country'),
            'country.max' => __('business.customer.validation.max_100'),
            'filter_details.regex' => __('business.customer.validation.special_chars_not_allowed'),
            'pump_details.regex' => __('business.customer.validation.special_chars_not_allowed'),
            'cleaner_details.regex' => __('business.customer.validation.special_chars_not_allowed'),
            'heater_details.regex' => __('business.customer.validation.special_chars_not_allowed'),
            'heat_pump_details.regex' => __('business.customer.validation.special_chars_not_allowed'),
            'aux_details.regex' => __('business.customer.validation.special_chars_not_allowed'),
            'aux2_details.regex' => __('business.customer.validation.special_chars_not_allowed'),
            'salt_system_details.regex' => __('business.customer.validation.special_chars_not_allowed'),
            'filter_image.image' => __('business.customer.validation.invalid_image_type'),
            'pump_image.image' => __('business.customer.validation.invalid_image_type'),
            'cleaner_image.image' => __('business.customer.validation.invalid_image_type'),
            'heater_image.image' => __('business.customer.validation.invalid_image_type'),
            'heat_pump_image.image' => __('business.customer.validation.invalid_image_type'),
            'aux_image.image' => __('business.customer.validation.invalid_image_type'),
            'aux2_image.image' => __('business.customer.validation.invalid_image_type'),
            'salt_system_image.image' => __('business.customer.validation.invalid_image_type'),
            'filter_image.max' => __('business.customer.validation.image_size'),
            'pump_image.max' => __('business.customer.validation.image_size'),
            'cleaner_image.max' => __('business.customer.validation.image_size'),
            'heater_image.max' => __('business.customer.validation.image_size'),
            'heat_pump_image.max' => __('business.customer.validation.image_size'),
            'aux_image.max' => __('business.customer.validation.image_size'),
            'aux2_image.max' => __('business.customer.validation.image_size'),
            'salt_system_image.max' => __('business.customer.validation.image_size'),
            'filter_image.mimes' => __('business.customer.validation.invalid_image_type'),
            'pump_image.mimes' => __('business.customer.validation.invalid_image_type'),
            'cleaner_image.mimes' => __('business.customer.validation.invalid_image_type'),
            'heater_image.mimes' => __('business.customer.validation.invalid_image_type'),
            'heat_pump_image.mimes' => __('business.customer.validation.invalid_image_type'),
            'aux_image.mimes' => __('business.customer.validation.invalid_image_type'),
            'aux2_image.mimes' => __('business.customer.validation.invalid_image_type'),
            'salt_system_image.mimes' => __('business.customer.validation.invalid_image_type'),
            'tech_notes.min' => __('business.customer.validation.min_max_characters', ['min' => self::LENGTH_1, 'max' => self::LENGTH_500]),
            'tech_notes.max' => __('business.customer.validation.min_max_characters', ['min' => self::LENGTH_1, 'max' => self::LENGTH_500]),
            'admin_notes.min' => __('business.customer.validation.min_max_characters', ['min' => self::LENGTH_1, 'max' => self::LENGTH_500]),
            'admin_notes.max' => __('business.customer.validation.min_max_characters', ['min' => self::LENGTH_1, 'max' => self::LENGTH_500]),
            'entry_instruction.min' => __('business.customer.validation.min_max_characters', ['min' => self::LENGTH_1, 'max' => self::LENGTH_500]),
            'entry_instruction.max' => __('business.customer.validation.min_max_characters', ['min' => self::LENGTH_1, 'max' => self::LENGTH_500]),
            'clean_psi.max' => __('business.customer.validation.min_max_characters', ['min' => self::LENGTH_1, 'max' => self::LENGTH_100]),
            'clean_psi.min' => __('business.customer.validation.min_max_characters', ['min' => self::LENGTH_1, 'max' => self::LENGTH_100]),
            'filter_details.max' => __('business.customer.validation.min_max_characters', ['min' => self::LENGTH_1, 'max' => self::LENGTH_100]),
            'pump_details.max' => __('business.customer.validation.min_max_characters', ['min' => self::LENGTH_1, 'max' => self::LENGTH_100]),
            'cleaner_details.max' => __('business.customer.validation.min_max_characters', ['min' => self::LENGTH_1, 'max' => self::LENGTH_100]),
            'heater_details.max' => __('business.customer.validation.min_max_characters', ['min' => self::LENGTH_1, 'max' => self::LENGTH_100]),
            'heat_pump_details.max' => __('business.customer.validation.min_max_characters', ['min' => self::LENGTH_1, 'max' => self::LENGTH_100]),
            'aux_details.max' => __('business.customer.validation.min_max_characters', ['min' => self::LENGTH_1, 'max' => self::LENGTH_100]),
            'aux2_details.max' => __('business.customer.validation.min_max_characters', ['min' => self::LENGTH_1, 'max' => self::LENGTH_100]),
            'salt_system_details.max' => __('business.customer.validation.min_max_characters', ['min' => self::LENGTH_1, 'max' => self::LENGTH_100]),
            'commercial_pool_details.max' => __('business.customer.validation.max_200'),
            'equipment_details.max' => __('business.customer.validation.max_100'),
            'pool_item.max' => __('business.customer.validation.max_100'),
            'image_only' => __('business.customer.validation.image_only'),
            'image_size' => __('business.customer.validation.image_size'),
            'image_type' => __('business.customer.validation.image_type'),
            'pool_type.required_without' => __('business.customer.validation.pool_type_required'),
            'pool_type.in' => __('business.customer.validation.pool_type_invalid'),
            'first_name.required_if' => __('business.customer.validation.first_name_required_residential'),
            'last_name.required_if' => __('business.customer.validation.last_name_required_residential'),
            'commercial_pool_details.required_if' => __('business.customer.validation.commercial_name_required'),
            'pool_size_gallons.numeric' => __('business.customer.validation.pool_size_numeric'),
            'pool_size_gallons.regex' => __('business.customer.validation.decimal_two_places'),
            'pool_size_gallons.min' => __('business.customer.validation.pool_size_min'),
            'pool_size_gallons.max' => __('business.customer.validation.pool_size_max'),
            'pool_length.regex' => __('business.customer.validation.decimal_two_places'),
            'pool_width.regex' => __('business.customer.validation.decimal_two_places'),
            'pool_depth.regex' => __('business.customer.validation.decimal_two_places'),
            'pool_length.min' => __('business.customer.validation.pool_length_min'),
            'pool_width.min' => __('business.customer.validation.pool_width_min'),
            'pool_depth.min' => __('business.customer.validation.pool_depth_min'),
            'pool_length' . self::RULE_MAX_PREFIX => __('business.customer.validation.pool_length_max'),
            'pool_width' . self::RULE_MAX_PREFIX => __('business.customer.validation.pool_width_max'),
            'pool_depth' . self::RULE_MAX_PREFIX => __('business.customer.validation.pool_depth_max'),
        ];
    }
}
