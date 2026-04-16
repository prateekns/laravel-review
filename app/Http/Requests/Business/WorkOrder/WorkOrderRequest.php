<?php

namespace App\Http\Requests\Business\WorkOrder;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class WorkOrderRequest extends FormRequest
{
    /**
     * Validation rule constant for required string fields
     */
    private const REQUIRED_STRING = 'required|string';

    /**
     * Translation key constants
     */
    private const TRANS_REQUIRED = 'business.customer.validation.required';
    private const TRANS_MAX_LENGTH = 'business.work_orders.validation.max_length';
    private const TRANS_PHOTO_REQUIREMENTS = 'business.work_orders.validation.photo_requirements';
    private const TRANS_PHOTO_SIZE = 'business.work_orders.validation.photo_size';
    private const TRANS_DATE_FORMAT = 'business.customer.validation.date_format';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer_id' => 'required_if:customer_type,existing',
            'template_id' => 'required',
            'name' => 'required|max:150',
            'description' => 'nullable|max:1200',
            'photo' => 'nullable|image|mimes:jpeg,jpg,png|max:3072', // 3MB = 3072KB
            'additional_task' => 'nullable|max:255',
            'preferred_start_date' => 'required|date_format:Y-m-d',
            // Note: prepareForValidation converts time to H:i:s before validation
            'preferred_start_time' => 'required|date_format:H:i:s',
            'end_date' => 'nullable|date_format:Y-m-d|after_or_equal:preferred_start_date',
            'technician_customer_coordination' => 'boolean',
            'is_recurring' => 'boolean',
            'frequency' => 'required_if:is_recurring,1|nullable|in:daily,weekly,semi_monthly,monthly',
            'repeat_after' => 'required_if:is_recurring,1|nullable|integer|min:1',
            'selected_days' => 'nullable|array',
            'selected_days.*' => 'string|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',

            'monthly_day_type' => 'required_if:frequency,monthly|nullable|in:first,second,third,fourth',
            'monthly_day_of_week' => 'required_if:frequency,monthly|nullable|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'is_active' => 'boolean',
            'checklist_items' => 'nullable|array',
            'checklist_items.*.is_visible' => 'nullable|string|in:on',
            'checklist_items.*.is_custom' => self::REQUIRED_STRING,
            'checklist_items.*.is_default' => self::REQUIRED_STRING,
            'checklist_items.*.sort_order' => self::REQUIRED_STRING,
            'checklist_items.*.item_text' => self::REQUIRED_STRING . '|max:255',
            'action' => 'nullable|string|in:save,save_and_assign'
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
            'end_date.date_format' => __(self::TRANS_DATE_FORMAT),
            'end_date.after_or_equal' => __('business.work_orders.validation.end_date_after_start'),
            'required' => __(self::TRANS_REQUIRED),
            'customer_id.required_if' => __(self::TRANS_REQUIRED),
            'template_id.required' => __(self::TRANS_REQUIRED),
            'name.required' => __(self::TRANS_REQUIRED),
            'name.max' => __(self::TRANS_MAX_LENGTH),
            'description.max' => __(self::TRANS_MAX_LENGTH),
            'photo.image' => __(self::TRANS_PHOTO_REQUIREMENTS),
            'photo.mimes' => __(self::TRANS_PHOTO_REQUIREMENTS),
            'photo.max' => __(self::TRANS_PHOTO_SIZE),
            'additional_task.max' => __(self::TRANS_MAX_LENGTH),
            'preferred_start_date.required' => __(self::TRANS_REQUIRED),
            'preferred_start_date.date_format' => __(self::TRANS_DATE_FORMAT),
            'preferred_start_time.required' => __(self::TRANS_REQUIRED),
            'preferred_start_time.date_format' => __('business.work_orders.validation.time_format'),
            'frequency.required_if' => __('business.work_orders.validation.frequency_required'),
            'frequency.in' => __('business.work_orders.validation.frequency_required'),
            'repeat_after.required_if' => __('business.work_orders.validation.repeat_after_required'),
            'repeat_after.integer' => __('business.work_orders.validation.repeat_after_required'),
            'repeat_after.min' => __('business.work_orders.validation.repeat_after_required'),
            'repeat_after.max' => __('business.work_orders.validation.repeat_after_required'),
            'selected_days.required_if' => __('business.work_orders.validation.select_days_required'),
            'selected_days.array' => __('business.work_orders.validation.select_days_required'),
            'selected_days.*.in' => __('business.work_orders.validation.select_days_required'),
            'monthly_day_type.required_if' => __('business.work_orders.validation.monthly_day_selection_incomplete'),
            'monthly_day_type.in' => __('business.work_orders.validation.monthly_day_selection_incomplete'),
            'monthly_day_of_week.required_if' => __('business.work_orders.validation.monthly_day_selection_incomplete'),
            'monthly_day_of_week.in' => __('business.work_orders.validation.monthly_day_selection_incomplete'),
            'checklist_items.array' => __('business.work_orders.validation.checklist_items'),
            'checklist_items.*.item_text.required' => __(self::TRANS_REQUIRED),
            'checklist_items.*.item_text.max' => __(self::TRANS_MAX_LENGTH),
            'checklist_items.*.is_custom.required' => __(self::TRANS_REQUIRED),
            'checklist_items.*.is_default.required' => __(self::TRANS_REQUIRED),
            'checklist_items.*.sort_order.required' => __(self::TRANS_REQUIRED),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        $this->prepareBooleanFields();
        $this->prepareChecklistItems();
        $this->clearIrrelevantRecurringFields();
        $this->trimInputFields();
        $this->prepareDateTimeFields();
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $this->validateChecklistItemsPresence($validator);
            $this->validateRecurringFields($validator);
            if ($this->has('__has_duplicate_tasks') && $this->boolean('__has_duplicate_tasks')) {
                $validator->errors()->add('checklist_items', __('business.work_orders.validation.checklist_item_unique'));
            }
        });
    }

    /**
     * Ensure at least one checklist item is provided.
     */
    private function validateChecklistItemsPresence($validator): void
    {
        $items = $this->input('checklist_items');

        if (!is_array($items) || count($items) < 1) {
            $validator->errors()->add('checklist_items', __(self::TRANS_REQUIRED));
            return;
        }

        // Check if at least one item is visible/checked
        $hasVisibleItem = false;
        foreach ($items as $item) {
            if (isset($item['is_visible']) && $item['is_visible'] === 'on') {
                $hasVisibleItem = true;
                break;
            }
        }

        if (!$hasVisibleItem) {
            $validator->errors()->add('checklist_items', __('business.work_orders.validation.select_checklist_items_required'));
        }
    }

    /**
     * Prepare boolean fields for validation.
     */
    private function prepareBooleanFields(): void
    {
        $this->merge([
            'technician_customer_coordination' => $this->boolean('technician_customer_coordination'),
            'is_recurring' => $this->boolean('is_recurring'),
            'is_active' => $this->boolean('is_active')
        ]);
    }

    /**
     * Prepare checklist items for validation.
     */
    private function prepareChecklistItems(): void
    {
        if (!$this->has('checklist_items')) {
            return;
        }

        $items = $this->input('checklist_items');
        if (!is_array($items)) {
            return;
        }

        $filteredItems = array_filter($items, fn ($item) => !empty($item['item_text']));

        // Normalize and check duplicates (case-insensitive, trimmed)
        $normalized = array_map(
            fn ($item) => strtolower(trim((string)($item['item_text'] ?? ''))),
            $filteredItems
        );
        $normalized = array_filter($normalized, fn ($text) => $text !== '');

        if (count($normalized) !== count(array_unique($normalized))) {
            // Flag; add error in withValidator so messages() can still be used
            $this->merge(['__has_duplicate_tasks' => true]);
        }

        $this->merge(['checklist_items' => array_values($filteredItems)]);
    }

    /**
     * Validate recurring fields.
     */
    private function validateRecurringFields($validator): void
    {
        if (!$this->input('is_recurring')) {
            return;
        }

        $this->validateSelectedDays($validator);
        $this->validateMonthlyDaySelection($validator);
        $this->validateRepeatAfterLimits($validator);
    }

    /**
     * Validate selected days for weekly/semi-monthly frequency.
     */
    private function validateSelectedDays($validator): void
    {
        $frequency = $this->input('frequency');
        if (!in_array($frequency, ['weekly', 'semi_monthly'])) {
            return;
        }

        $selectedDays = $this->input('selected_days', []);
        $this->validateSelectedDaysPresence($validator, $selectedDays, $frequency);
        $this->validateSemiMonthlyDayCount($validator, $selectedDays, $frequency);
    }

    /**
     * Validate presence of selected days.
     */
    private function validateSelectedDaysPresence($validator, array $selectedDays, string $frequency): void
    {
        if (empty($selectedDays) || !is_array($selectedDays)) {
            $messageKey = $frequency === 'semi_monthly'
                ? 'business.work_orders.validation.semi_monthly_select_days_required'
                : 'business.work_orders.validation.select_days_required';

            $validator->errors()->add('selected_days', __($messageKey));
        }
    }

    /**
     * Validate semi-monthly day count.
     */
    private function validateSemiMonthlyDayCount($validator, array $selectedDays, string $frequency): void
    {
        if ($frequency === 'semi_monthly' && count($selectedDays) > 1) {
            $validator->errors()->add(
                'selected_days',
                __('business.work_orders.validation.day_of_month_required')
            );
        }
    }

    /**
     * Validate monthly day selection.
     */
    private function validateMonthlyDaySelection($validator): void
    {
        if ($this->input('frequency') !== 'monthly') {
            return;
        }

        if (!$this->input('monthly_day_type') || !$this->input('monthly_day_of_week')) {
            $validator->errors()->add(
                'monthly_day_type',
                __('business.work_orders.validation.monthly_day_selection_incomplete')
            );
        }
    }

    /**
     * Validate repeat after limits based on frequency.
     */
    private function validateRepeatAfterLimits($validator): void
    {
        $frequency = $this->input('frequency');
        if (!$frequency) {
            return;
        }

        $this->validateRepeatAfterPresence($validator, $frequency);
        $this->validateRepeatAfterMaxValue($validator, $frequency);
    }

    /**
     * Validate presence of repeat_after for required frequencies.
     */
    private function validateRepeatAfterPresence($validator, string $frequency): void
    {
        if (in_array($frequency, ['weekly', 'monthly']) && !$this->input('repeat_after')) {
            $validator->errors()->add(
                'repeat_after',
                __('business.work_orders.validation.repeat_after_required')
            );
        }
    }

    /**
     * Validate maximum value for repeat_after based on frequency.
     */
    private function validateRepeatAfterMaxValue($validator, string $frequency): void
    {
        $repeatAfter = (int) $this->input('repeat_after', 1);

        if ($frequency === 'weekly' && $repeatAfter > 4) {
            $validator->errors()->add('repeat_after', __('business.weekly_repeat_after_max'));
        }

        if ($frequency === 'monthly' && $repeatAfter > 12) {
            $validator->errors()->add('repeat_after', __('business.monthly_repeat_after_max'));
        }
    }

    /**
     * Clear irrelevant recurring fields based on frequency.
     */
    private function clearIrrelevantRecurringFields(): void
    {
        $frequency = $this->input('frequency');
        $isRecurring = $this->input('is_recurring');

        // If not recurring, clear all recurring fields
        if (!$isRecurring) {
            $this->merge([
                'frequency' => null,
                'repeat_after' => null,
                'selected_days' => null,
                'monthly_day_type' => null,
                'monthly_day_of_week' => null,
            ]);
            return;
        }

        // If recurring but no frequency, return
        if (!$frequency) {
            return;
        }

        // Clear fields that are not relevant to the current frequency
        switch ($frequency) {
            case 'daily':
                // Daily doesn't need selected_days, monthly_day_type, monthly_day_of_week
                $this->merge(['selected_days' => null]);
                $this->merge(['monthly_day_type' => null]);
                $this->merge(['monthly_day_of_week' => null]);
                break;

            case 'weekly':
                // Weekly doesn't need monthly_day_type, monthly_day_of_week
                $this->merge(['monthly_day_type' => null]);
                $this->merge(['monthly_day_of_week' => null]);
                break;

            case 'semi_monthly':
                // Semi-monthly doesn't need repeat_after, monthly_day_type, monthly_day_of_week
                $this->merge(['repeat_after' => null]);
                $this->merge(['monthly_day_type' => null]);
                $this->merge(['monthly_day_of_week' => null]);
                break;

            case 'monthly':
                // Monthly doesn't need selected_days
                $this->merge(['selected_days' => null]);
                break;

            default:
                // For unrecognized frequencies, clear all recurring-specific fields
                $this->merge([
                    'selected_days' => null,
                    'monthly_day_type' => null,
                    'monthly_day_of_week' => null,
                    'repeat_after' => null
                ]);
                break;
        }
    }

    /**
     * Trim leading and trailing whitespace from input fields.
     */
    private function trimInputFields(): void
    {
        $fieldsToTrim = [
            'name',
            'description',
            'additional_task',
        ];

        foreach ($fieldsToTrim as $field) {
            if ($this->has($field)) {
                $this->merge([$field => trim($this->input($field))]);
            }
        }

        // Trim checklist item text fields
        if ($this->has('checklist_items') && is_array($this->input('checklist_items'))) {
            $checklistItems = $this->input('checklist_items');
            foreach ($checklistItems as $index => $item) {
                if (isset($item['item_text'])) {
                    $checklistItems[$index]['item_text'] = trim($item['item_text']);
                }
            }
            $this->merge(['checklist_items' => $checklistItems]);
        }
    }

    /**
     * Prepare date and time fields for storage (convert to UTC).
     */
    private function prepareDateTimeFields(): void
    {
        if ($this->has('preferred_start_date') && $this->has('preferred_start_time')) {
            $date = $this->input('preferred_start_date');
            $time = $this->input('preferred_start_time');

            // Convert business timezone to UTC for storage
            $utcDateTime = $this->convertBusinessTimezoneToUTC($date, $time);
            $utcDate = Carbon::parse($utcDateTime)->format('Y-m-d');
            $utcTime = Carbon::parse($utcDateTime)->format('H:i:s');

            // Update the date and time fields with UTC values
            $this->merge([
                'preferred_start_date' => $utcDate,
                'preferred_start_time' => $utcTime,
            ]);

            // Convert end_date to UTC date (treat as end of day in business TZ to preserve inclusivity)
            if ($this->filled('end_date')) {
                $endDate = (string) $this->input('end_date');
                $utcEndDateTime = $this->convertBusinessTimezoneToUTC($endDate, $time);
                $utcEndDate = Carbon::parse($utcEndDateTime)->format('Y-m-d');

                $this->merge([
                    'end_date' => $utcEndDate,
                ]);
            } else {
                $this->merge([
                    'end_date' => null,
                ]);
            }
        }
    }

    /**
     * Get the business timezone.
     */
    private function getBusinessTimezone(): string
    {
        $user = Auth::guard('business')->user();
        if ($user && $user->business) {
            return $user->business->timezone ?? 'America/New_York';
        }

        return 'America/New_York'; // Default fallback
    }

    /**
     * Convert date and time from business timezone to UTC for storage.
     */
    private function convertBusinessTimezoneToUTC(string $date, string $time): string
    {
        $businessTimezone = $this->getBusinessTimezone();
        $dateTimeString = $date . ' ' . $time;

        return Carbon::parse($dateTimeString, $businessTimezone)
            ->utc()
            ->format('Y-m-d H:i:s');
    }
}
