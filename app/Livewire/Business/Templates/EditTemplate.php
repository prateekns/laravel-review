<?php

namespace App\Livewire\Business\Templates;

use App\Models\Business\Templates;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

/**
 * Livewire component for editing a template
 */
class EditTemplate extends Component
{
    public int $templateId;
    public string $name = '';
    public string $description = '';
    public string $type = 'WO';
    public bool $is_active = true; //NOSONAR
    public ?string $notification = null;
    public string $notificationType = 'success';

    protected function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:100',
                function ($attribute, $value, $fail) {  //NOSONAR
                    $trimmedValue = trim($value);
                    $exists = DB::table('templates')
                        ->where('business_id', Auth::guard('business')->user()->business_id)
                        ->where('id', '!=', $this->templateId)
                        ->where(DB::raw('LOWER(TRIM(BOTH FROM name))'), strtolower($trimmedValue))
                        ->exists();

                    if ($exists) {
                        $fail(__('business.templates.name_unique'));
                    }
                },
            ],
            'description' => [
                'required',
                'string',
                'max:1200',
            ],
            'is_active' => [
                'required',
                'boolean',
            ],
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required' => __('business.customer.validation.required'),
            'name.max' => __('business.customer.validation.max_100'),
            'name.unique' => __('business.templates.name_unique'),
            'description.required' => __('business.customer.validation.required'),
            'description.max' => __('business.work_orders.validation.max_length'),
            'type.required' => __('business.customer.validation.required'),
            'is_active.required' => __('business.customer.validation.required'),
            'is_active.boolean' => __('business.templates.invalid_status'),
        ];
    }

    public function mount(int $templateId): void
    {
        $this->templateId = $templateId;
        $template = Templates::where('id', $templateId)
            ->where('business_id', Auth::guard('business')->user()->business_id)
            ->firstOrFail();
        $this->name = $template->name;
        $this->description = $template->description;
        $this->is_active = $template->is_active;
    }

    /**
     * Update the template.
     */
    public function updateTemplate()
    {
        $this->validate($this->rules());

        try {
            $template = Templates::where('id', $this->templateId)
                ->where('business_id', Auth::guard('business')->user()->business_id)
                ->firstOrFail();

            $template->update([
                'name' => trim($this->name),
                'description' => trim($this->description),
                'is_active' => $this->is_active,
            ]);

            session()->flash('notificationMessage', __('business.templates.saved_success'));
            session()->flash('notificationType', 'success');

            return redirect()->route('templates.index');
        } catch (\Exception $e) {
            session()->flash('notificationMessage', __('business.templates.update_error'));
            session()->flash('notificationType', 'error');
        }
    }

    /**
     * Cancel editing and redirect to index.
     */
    public function cancelEdit(): void
    {
        $this->redirectRoute('templates.index');
    }

    public function clearNotification(): void
    {
        $this->notification = null;
    }

    protected function listeners(): array
    {
        return [
            'confirmed' => 'handleConfirm',
            'cancelled' => 'handleCancel',
        ];
    }

    public function handleConfirm(): void
    {
        // Keep the inactive status
        $this->is_active = false;
    }

    public function handleCancel(): void
    {
        // Revert back to active status
        $this->is_active = true;
    }

    public function render()
    {
        return view('livewire.business.templates.edit-template');
    }
}
