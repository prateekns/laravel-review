<?php

namespace App\Livewire\Business\Checklists;

use App\Models\Business\Checklist;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Collection;

/**
 * Livewire component for managing individual checklist items
 */
class ChecklistItems extends Component
{
    public ?int $templateId = null;
    public string $editItemText = '';
    public ?int $editingItemId = null;
    public array $tempVisibility = [];
    public array $originalVisibility = [];
    public Collection $checklists;

    public function __construct()
    {
        $this->checklists = collect();
    }

    protected function rules(): array
    {
        return [
            'editItemText' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) { //NOSONAR
                    $trimmedValue = trim($value);
                    $exists = Checklist::where('business_id', Auth::guard('business')->user()->business_id)
                        ->where('template_id', $this->templateId)
                        ->whereRaw('TRIM(item_text) = ?', [$trimmedValue])
                        ->when($this->editingItemId, function ($query) {
                            return $query->where('id', '!=', $this->editingItemId);
                        })
                        ->exists();

                    if ($exists) {
                        $fail(__('business.checklist.item_unique'));
                    }
                },
            ],
        ];
    }

    protected function messages(): array
    {
        return [
            'editItemText.required' => __('business.customer.validation.required'),
            'editItemText.max' => __('business.checklist.item_max_length'),
            'editItemText.unique' => __('business.checklist.item_unique'),
        ];
    }

    protected $listeners = ['checklistItemAdded' => 'handleNewItem'];

    public function mount(?int $templateId = null): void
    {
        $this->templateId = $templateId;
        $this->checklists = collect();
        $this->tempVisibility = [];
        $this->originalVisibility = [];
        $this->loadChecklists();
    }

    public function loadChecklistItems(): void
    {
        $this->loadChecklists();
        $this->initializeTempVisibility();
    }

    protected function initializeVisibilityArrays(): void
    {
        foreach ($this->checklists as $item) {
            $this->tempVisibility[$item->id] = $item->is_visible;
            $this->originalVisibility[$item->id] = $item->is_visible;
        }
    }

    protected function initializeTempVisibility(): void
    {
        foreach ($this->checklists as $item) {
            $this->tempVisibility[$item->id] = $item->is_visible;
        }
    }

    public function saveVisibilityChanges(): void
    {
        try {
            foreach ($this->tempVisibility as $id => $isVisible) {
                $item = Checklist::find($id);
                if ($item && $item->is_visible !== $isVisible) {
                    $item->update(['is_visible' => $isVisible]);
                }
            }

            $this->loadChecklists();
            session()->flash('success', __('business.checklist.visibility_updated'));
            $this->dispatch('showAlert');
        } catch (\Exception $e) {
            session()->flash('error', __('business.checklist.update_failed'));
            $this->dispatch('showAlert');
        }
    }

    public function cancelVisibilityChanges(): void
    {
        $this->loadChecklists();
        $this->initializeTempVisibility();
        $this->dispatch('showAlert');
    }

    public function toggleVisibility(int $itemId): void
    {
        try {
            $item = Checklist::find($itemId);
            $item->update(['is_visible' => !$item->is_visible]);

            $this->loadChecklists();

            session()->flash('success', __('business.checklist.visibility_updated'));
            $this->dispatch('showAlert');
        } catch (\Exception $e) {
            session()->flash('error', __('business.checklist.update_failed'));
            $this->dispatch('showAlert');
        }
    }

    public function loadChecklists(): void
    {
        if (!$this->templateId) {
            return;
        }

        $businessId = auth()->guard('business')->user()->business_id;

        $this->checklists = Checklist::query()
            ->select(['id', 'item_text', 'is_visible', 'sort_order'])
            ->where('template_id', $this->templateId)
            ->where('business_id', $businessId)
            ->orderBy('sort_order')
            ->get();

        $this->initializeTempVisibility();
    }

    public function handleNewItem(): void
    {
        $this->loadChecklists();
    }

    #[On('checklistItemAdded')]
    public function handleItemAdded(): void
    {
        $this->loadChecklists();
    }

    public function startEditing(int $itemId, string $currentText): void
    {
        $this->editingItemId = $itemId;
        $this->editItemText = $currentText;
        $this->resetValidation('editItemText');
    }

    public function cancelEdit(): void
    {
        $this->editingItemId = null;
        $this->editItemText = '';
        $this->resetValidation('editItemText');
    }

    public function saveEdit(): void
    {
        try {
            $this->resetValidation('editItemText');
            $this->validate();

            DB::beginTransaction();
            $item = Checklist::find($this->editingItemId);
            $item->update(['item_text' => trim($this->editItemText)]);

            DB::commit();

            $this->editingItemId = null;
            $this->editItemText = '';
            $this->loadChecklists();

            session()->flash('success', __('business.checklist.item_updated'));
            $this->dispatch('showAlert');

        } catch (ValidationException $e) {
            $this->resetValidation('editItemText');
            $this->addError('editItemText', $e->validator->errors()->first('editItemText'));
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', __('business.checklist.update_failed'));
            $this->dispatch('showAlert');
        }
    }

    public function deleteItem(int $itemId): void
    {
        try {
            $item = Checklist::find($itemId);

            if (!$item) {
                session()->flash('error', __('business.checklist.item_not_found'));
                $this->dispatch('showAlert');
                return;
            }

            $item->delete();
            $this->loadChecklists();
            session()->flash('success', __('business.checklist.item_deleted'));
            $this->dispatch('showAlert');
        } catch (\Exception $e) {
            session()->flash('error', __('business.checklist.delete_failed'));
            $this->dispatch('showAlert');
        }
    }

    public function render()
    {
        return view('livewire.business.checklists.checklist-items');
    }
}
