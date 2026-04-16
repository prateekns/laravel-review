<?php

namespace App\Livewire\Business\Checklists;

use App\Models\Business\Templates;
use App\Models\Business\Checklist;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Livewire component for managing checklist items within templates
 */
class ManageChecklist extends Component
{
    public array $templates = [];
    public ?int $selectedTemplate = null;
    public bool $showAddModal = false;
    public string $newItemText = '';
    public ?int $currentTemplateId = null;

    protected function rules(): array
    {
        return [
            'newItemText' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {  //NOSONAR
                    $trimmedValue = trim($value);
                    $exists = Checklist::where('business_id', Auth::guard('business')->user()->business_id)
                        ->where('template_id', $this->currentTemplateId)
                        ->where(DB::raw('LOWER(TRIM(BOTH FROM item_text))'), strtolower($trimmedValue))
                        ->exists();

                    if ($exists) {
                        $fail(__('business.checklist.item_unique'));
                    }
                },
            ]
        ];
    }

    protected function messages(): array
    {
        return [
            'newItemText.required' => __('business.customer.validation.required'),
            'newItemText.max' => __('business.checklist.item_max_length'),
            'newItemText.unique' => __('business.checklist.item_unique'),
        ];
    }

    public function mount(): void
    {
        $this->loadTemplates();

        // Get template ID from URL and expand it if present
        $templateId = request()->query('template');
        if ($templateId && collect($this->templates)->contains('id', $templateId)) {
            $this->selectedTemplate = (int) $templateId;
        }
    }

    public function loadTemplates(): void
    {
        $businessId = Auth::guard('business')->user()->business_id;

        $this->templates = Templates::query()
            ->select(['id', 'name'])
            ->where('business_id', $businessId)
            ->orderBy('name')
            ->get()
            ->all();
    }

    public function toggleTemplate(int $templateId): void
    {
        $this->selectedTemplate = $this->selectedTemplate === $templateId ? null : $templateId;
    }

    public function openAddModal(int $templateId): void
    {
        $this->currentTemplateId = $templateId;
        $this->showAddModal = true;
        $this->newItemText = '';
        $this->resetValidation('newItemText');
    }

    public function closeAddModal(): void
    {
        $this->showAddModal = false;
        $this->newItemText = '';
        $this->currentTemplateId = null;
        $this->resetValidation('newItemText');
    }

    public function saveNewItem(): void
    {
        if (!$this->currentTemplateId) {
            session()->flash('error', __('business.checklist.template_required'));
            $this->dispatch('showAlert');
            return;
        }

        try {
            $this->resetValidation('newItemText');
            $this->validate();

            DB::beginTransaction();

            $businessUser = Auth::guard('business')->user();

            Checklist::create([
                'business_id' => $businessUser->business_id,
                'template_id' => $this->currentTemplateId,
                'item_text' => trim($this->newItemText),
                'is_visible' => true,
                'sort_order' => Checklist::where('template_id', $this->currentTemplateId)->max('sort_order') + 1,
            ]);

            DB::commit();

            $this->closeAddModal();
            $this->dispatch('checklistItemAdded')->to('business.checklists.checklist-items');

            session()->flash('success', __('business.checklist.item_added'));
            $this->dispatch('showAlert');
            $this->reset(['newItemText']);

        } catch (ValidationException $e) {
            $this->resetValidation('newItemText');
            $this->addError('newItemText', $e->validator->errors()->first('newItemText'));
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', __('business.checklist.add_failed'));
            $this->dispatch('showAlert');
            $this->reset(['newItemText']);
        }
    }

    #[Computed]
    public function selectedTemplateName(): string
    {
        if (!$this->currentTemplateId) {
            return '';
        }

        $template = collect($this->templates)->firstWhere('id', $this->currentTemplateId);
        return $template ? $template->name : '';
    }

    public function render()
    {
        return view('livewire.business.checklists.manage-checklist');
    }
}
