<?php

namespace App\Livewire\Business;

use Livewire\Component;
use App\Models\Business\WorkOrder\Item;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ItemsSold extends Component
{
    public $itemName = '';
    public $editingItemId = null;
    public $editingItemName = '';
    public $confirmingDelete = false;
    public $itemToDelete = null;

    protected function messages(): array
    {
        return [
            'itemName.required' => __('business.customer.validation.required'),
            'itemName.max' => __('business.items_sold.validation.item_name_max'),
            'itemName.regex' => __('business.customer.validation.special_chars_not_allowed'),
            'itemName.unique' => __('business.items_sold.validation.item_exists'),
            'editingItemName.required' => __('business.customer.validation.required'),
            'editingItemName.max' => __('business.items_sold.validation.item_name_max'),
            'editingItemName.regex' => __('business.customer.validation.special_chars_not_allowed'),
            'editingItemName.unique' => __('business.items_sold.validation.item_exists'),
        ];
    }

    protected function getAddRules()
    {
        return [
            'itemName' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-Z0-9\s\-_]+$/',
                function ($attribute, $value, $fail) { //NOSONAR
                    $trimmedValue = trim($value);
                    $exists = Item::where('business_id', Auth::guard('business')->user()->business_id)
                        ->where(DB::raw('LOWER(TRIM(BOTH FROM name))'), strtolower($trimmedValue))
                        ->exists();

                    if ($exists) {
                        $fail(__('business.items_sold.validation.item_exists', ['name' => $trimmedValue]));
                    }
                }
            ]
        ];
    }

    protected function getEditRules()
    {
        return [
            'editingItemName' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-Z0-9\s\-_]+$/',
                function ($attribute, $value, $fail) { //NOSONAR
                    $trimmedValue = trim($value);
                    $exists = Item::where('business_id', Auth::guard('business')->user()->business_id)
                        ->where('id', '!=', $this->editingItemId)
                        ->where(DB::raw('LOWER(TRIM(BOTH FROM name))'), strtolower($trimmedValue))
                        ->exists();

                    if ($exists) {
                        $fail(__('business.items_sold.validation.item_exists', ['name' => $trimmedValue]));
                    }
                }
            ]
        ];
    }

    public function render()
    {
        $items = Item::where('business_id', Auth::guard('business')->user()->business_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.business.items-sold', [
            'items' => $items
        ]);
    }

    public function save()
    {
        $this->validate($this->getAddRules());

        try {
            Item::create([
                'name' => trim($this->itemName),
                'business_id' => Auth::guard('business')->user()->business_id
            ]);

            $this->reset('itemName');
            session()->flash('success', __('business.items_sold.item_saved'));
        } catch (\Exception $e) {
            Log::error('Error saving item: ' . $e->getMessage());
            session()->flash('error', __('business.items_sold.item_add_error'));
        }
    }

    public function startEdit($itemId)
    {
        $item = Item::find($itemId);
        if ($item) {
            $this->editingItemId = $itemId;
            $this->editingItemName = $item->name;
        }
    }

    public function cancelEdit()
    {
        $this->reset(['editingItemId', 'editingItemName']);
        $this->resetValidation('editingItemName');
    }

    public function updateItem()
    {
        $this->validate($this->getEditRules());

        try {
            $item = Item::find($this->editingItemId);

            if ($item && $item->business_id === Auth::guard('business')->user()->business_id) {
                $item->update([
                    'name' => trim($this->editingItemName)
                ]);

                $this->reset(['editingItemId', 'editingItemName']);
                session()->flash('success', __('business.items_sold.item_updated'));
            }
        } catch (\Exception $e) {
            Log::error('Error updating item: ' . $e->getMessage());
            session()->flash('error', __('business.items_sold.item_update_error'));
        }
    }

    public function confirmDelete($itemId)
    {
        $this->itemToDelete = Item::find($itemId);
        $this->dispatch('item-ready-for-delete');
    }

    public function deleteItem($itemId)
    {
        try {
            $item = Item::find($itemId);
            if ($item && $item->business_id === Auth::guard('business')->user()->business_id) {
                $item->delete();

                // Reset states
                $this->itemToDelete = null;

                session()->flash('success', __('business.items_sold.item_deleted'));

                // Refresh the component
                $this->dispatch('refresh-items');
            }
        } catch (\Exception $e) {
            Log::error('Error deleting item: ' . $e->getMessage());
            session()->flash('error', __('business.items_sold.item_deleted_error'));
        }
    }

    public function clear()
    {
        $this->reset('itemName');
        $this->resetValidation('itemName');
    }
}
