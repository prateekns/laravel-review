<?php

namespace App\Http\Requests\Business\WorkOrder;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;

class MaintenanceRequest extends FormRequest
{
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('delete_photo')) {
            $this->merge([
                'delete_photo' => filter_var($this->delete_photo, FILTER_VALIDATE_BOOLEAN)
            ]);
        }

        $this->trimInputFields();
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
                if (isset($item['description'])) {
                    $checklistItems[$index]['description'] = trim($item['description']);
                }
            }
            $this->merge(['checklist_items' => $checklistItems]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = [
            'customer_id' => ['required', 'exists:customers,id'],
            'template_id' => ['required', 'exists:templates,id'],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:1200'],
            'additional_task' => ['nullable', 'string', 'max:255'],
            'preferred_start_date' => ['required', 'date'],
            'preferred_start_time' => ['required', 'date_format:H:i'],
            'technician_customer_coordination' => ['boolean'],
            'is_recurring' => ['boolean'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:3072'],
            'delete_photo' => ['nullable', 'boolean'],
            'checklist_items' => ['nullable', 'array'],
            'checklist_items.*.description' => ['required', 'string', 'max:255', 'distinct'],
            'checklist_items.*.sort_order' => ['nullable', 'integer'],
            'action' => ['nullable', 'string', 'in:save,save_and_assign'],
        ];

        // Add is_active field for edit requests
        if ($this->isMethod('PUT')) {
            $rules['is_active'] = ['required', 'boolean'];
        }

        return $rules;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'customer_id' => __('business.maintenance.customer'),
            'template_id' => __('business.maintenance.service_type'),
            'name' => __('business.maintenance.job_name'),
            'description' => __('business.maintenance.job_description'),
            'additional_task' => __('business.maintenance.additional_task'),
            'preferred_start_date' => __('business.maintenance.preferred_start_date'),
            'preferred_start_time' => __('business.maintenance.preferred_start_time'),
            'technician_customer_coordination' => __('business.maintenance.communication_notes'),
            'is_recurring' => __('business.maintenance.is_recurring'),
            'photo' => __('business.maintenance.photo'),
            'checklist_items.*.description' => __('business.maintenance.checklist_item'),
            'is_active' => __('business.maintenance.status'),
            'delete_photo' => __('business.maintenance.delete_photo'),
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'customer_id.required' => __('validation.required', ['attribute' => __('business.maintenance.customer')]),
            'template_id.required' => __('validation.required', ['attribute' => __('business.maintenance.service_type')]),
            'name.required' => __('validation.required', ['attribute' => __('business.maintenance.job_name')]),
            'name.max' => __('validation.max.string', ['attribute' => __('business.maintenance.job_name'), 'max' => 150]),
            'description.max' => __('validation.max.string', ['attribute' => __('business.maintenance.job_description'), 'max' => 1200]),
            'additional_task.max' => __('validation.max.string', ['attribute' => __('business.maintenance.additional_task'), 'max' => 255]),
            'preferred_start_date.required' => __('validation.required', ['attribute' => __('business.maintenance.preferred_start_date')]),
            'preferred_start_time.required' => __('validation.required', ['attribute' => __('business.maintenance.preferred_start_time')]),
            'photo.image' => __('validation.image', ['attribute' => __('business.maintenance.photo')]),
            'photo.mimes' => __('validation.mimes', ['attribute' => __('business.maintenance.photo'), 'values' => 'jpeg, png']),
            'photo.max' => __('validation.max.file', ['attribute' => __('business.maintenance.photo'), 'max' => '3MB']),
            'checklist_items.*.description.required' => __('validation.required', ['attribute' => __('business.maintenance.checklist_item')]),
            'checklist_items.*.description.max' => __('validation.max.string', ['attribute' => __('business.maintenance.checklist_item'), 'max' => 255]),
            'checklist_items.*.description.distinct' => __('validation.distinct', ['attribute' => __('business.maintenance.checklist_item')]),
            'is_active.required' => __('validation.required', ['attribute' => __('business.maintenance.status')]),
            'delete_photo.boolean' => __('validation.boolean', ['attribute' => __('business.maintenance.delete_photo')]),
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $items = $this->input('checklist_items');

            $hasAtLeastOne = is_array($items) && count(array_filter($items, function ($item) {
                return isset($item['description']) && trim((string) $item['description']) !== '';
            })) > 0;

            if (!$hasAtLeastOne) {
                $validator->errors()->add(
                    'checklist_items',
                    __('validation.required', ['attribute' => __('business.work_orders.checklist_items')])
                );
                return;
            }

            // Check if at least one item is visible/checked (maintenance uses different structure)
            $hasVisibleItem = false;
            foreach ($items as $item) {
                if (isset($item['description']) && trim((string) $item['description']) !== '') {
                    $hasVisibleItem = true; // For maintenance, if description exists, it's considered "checked"
                    break;
                }
            }

            if (!$hasVisibleItem) {
                $validator->errors()->add(
                    'checklist_items',
                    __('business.work_orders.validation.select_checklist_items_required')
                );
            }
        });
    }

    public function failedValidation(Validator $validator): void
    {
        Log::error('MaintenanceRequest validation failed', [
            'errors' => $validator->errors()->toArray(),
            'input' => $this->all()
        ]);

        throw new HttpResponseException(
            redirect()->back()
                ->withErrors($validator)
                ->withInput()
        );
    }
}
