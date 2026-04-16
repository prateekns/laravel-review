<?php

namespace App\Livewire\Business\Templates;

use App\Models\Business\Templates;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

/**
 * Livewire component for managing templates (list, add, delete)
 */
class ManageTemplates extends Component
{
    public string $name = '';
    public string $description = '';
    public string $type = 'WO';
    public bool $is_active = true; //NOSONAR
    public array $templates = [];
    public ?string $notification = null;
    public string $notificationType = 'success';

    protected $listeners = ['refreshTemplates' => 'loadTemplates'];

    protected function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:100',
                function ($attribute, $value, $fail) { //NOSONAR
                    $trimmedValue = trim($value);
                    if (empty($trimmedValue)) {
                        return; // Let the 'required' rule handle empty values
                    }

                    $businessId = Auth::guard('business')->user()->business_id;
                    // Get all templates for this business (this uses the index efficiently)
                    $existingTemplates = Templates::where('business_id', $businessId)
                        ->pluck('name');

                    // Check for case-insensitive match in PHP
                    $exists = $existingTemplates->contains(function ($name) use ($trimmedValue) {
                        return strcasecmp($name, $trimmedValue) === 0;
                    });

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
            'type' => [
                'required',
                'string',
                'in:WO,MO',
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
            'type.in' => __('business.templates.invalid_type'),
        ];
    }

    public function mount(): void
    {
        $this->loadTemplates();
    }

    public function loadTemplates(): void
    {
        $businessId = Auth::guard('business')->user()->business_id;
        $this->templates = Templates::where('business_id', $businessId)
            ->orderBy('name')
            ->get()
            ->toArray();
    }

    /**
     * Add a new template and redirect to checklist page
     */
    public function addTemplate()
    {
        $this->validate();

        $template = Templates::create([
            'business_id' => Auth::guard('business')->user()->business_id,
            'name' => trim($this->name),
            'description' => trim($this->description),
            'type' => $this->type,
            'is_active' => $this->is_active,
        ]);

        session()->flash('success', __('business.templates.saved_success'));

        // Redirect to checklist page with the new template selected
        return redirect()->route('business.checklists.index', ['template' => $template->id]);
    }

    public function deleteTemplate(int $id): void
    {
        Templates::where('id', $id)
            ->where('business_id', Auth::guard('business')->user()->business_id)
            ->delete();

        $this->notification = __('business.templates.deleted_success');
        $this->notificationType = 'success';
        $this->loadTemplates();
    }

    public function clearForm(): void
    {
        $this->reset(['name', 'description', 'type']);
        $this->type = 'WO';
        $this->notification = null;
        $this->resetValidation();
    }

    public function clearNotification(): void
    {
        $this->notification = null;
    }

    public function toggleStatus(int $id): void
    {
        $template = Templates::where('id', $id)
            ->where('business_id', Auth::guard('business')->user()->business_id)
            ->first();

        if ($template) {
            $template->update(['is_active' => !$template->is_active]);
            $this->notification = __('business.templates.status_updated');
            $this->notificationType = 'success';
            $this->loadTemplates();
        }
    }

    public function render()
    {
        return view('livewire.business.templates.manage-templates');
    }
}
