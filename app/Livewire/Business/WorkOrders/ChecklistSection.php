<?php

namespace App\Livewire\Business\WorkOrders;

use Livewire\Component;
use App\Models\Business\Templates;
use Livewire\Attributes\On;

class ChecklistSection extends Component
{
    public $templateId;
    public $workOrder;
    public $maintenance;
    public $checklistItems = [];
    public $newCustomItem = '';
    public $errorMessage = '';
    public $originalTemplateId = null;
    public $isEditMode = false;

    public function mount($templateId = null, $workOrder = null, $maintenance = null)
    {
        $this->templateId = $templateId;
        $this->workOrder = $workOrder;
        $this->maintenance = $maintenance;
        $this->isEditMode = !is_null($workOrder) || !is_null($maintenance);

        if ($this->workOrder) {
            $this->originalTemplateId = $this->workOrder->template_id;
            $this->loadWorkOrderChecklist();
        } elseif ($this->maintenance) {
            $this->originalTemplateId = $this->maintenance->template_id;
            $this->loadMaintenanceChecklist();
        } elseif ($this->templateId) {
            $this->loadTemplateChecklist($this->templateId);
        }

        // Load old input data if exists
        if (old('checklist_items')) {
            $oldItems = collect(old('checklist_items'))->map(function ($item) {
                return [
                    'item_text' => $item['item_text'],
                    'is_visible' => isset($item['is_visible']),
                    'is_custom' => $item['is_custom'] == '1',
                    'is_default' => $item['is_default'] == '1',
                    'sort_order' => $item['sort_order']
                ];
            })->sortBy('sort_order')->values()->toArray();

            $this->checklistItems = $oldItems;
        }
    }

    #[On('template-changed')]
    public function handleTemplateChange($templateId)
    {
        // Extract template ID from array parameter
        $id = is_array($templateId) ? $templateId[0] : $templateId;

        // If no change in template ID, return
        if ($this->templateId == $id) {
            return;
        }

        $this->templateId = $id;

        // In edit mode and returning to original template, load work order checklist
        if ($this->isEditMode && $id == $this->originalTemplateId) {
            if ($this->workOrder) {
                $this->loadWorkOrderChecklist();
            } elseif ($this->maintenance) {
                $this->loadMaintenanceChecklist();
            }
            return;
        }

        // Keep custom items when template changes
        $customItems = collect($this->checklistItems)
            ->filter(function ($item) {
                return $item['is_custom'];
            })
            ->values()
            ->toArray();

        $this->loadTemplateChecklist($id);

        // Append custom items after template items
        if (!empty($customItems)) {
            $startIndex = count($this->checklistItems);
            foreach ($customItems as $index => $item) {
                $item['sort_order'] = $startIndex + $index;
                $this->checklistItems[] = $item;
            }
        }
    }

    public function loadTemplateChecklist($templateId)
    {
        if (!$templateId) {
            $this->checklistItems = [];
            return;
        }

        $template = Templates::with(['checklistItems' => function ($query) {
            $query
                ->where('is_visible', true)
                ->orderBy('sort_order');
        }])->find($templateId);

        if ($template && $template->checklistItems) {
            $this->checklistItems = $template->checklistItems->map(function ($item, $index) {
                return [
                    'item_text' => $item->item_text,
                    'is_visible' => $item->is_visible,
                    'is_custom' => false,
                    'is_default' => true,
                    'sort_order' => $index
                ];
            })->toArray();
        } else {
            $this->checklistItems = [];
        }
    }

    public function loadWorkOrderChecklist()
    {
        if (!$this->workOrder) {
            return;
        }

        // Load the relationship if not loaded
        if (!$this->workOrder->relationLoaded('checklist')) {
            $this->workOrder->load(['checklist' => function ($query) {
                $query->whereNull('instance_id');
            }]);
        }

        $this->checklistItems = $this->workOrder->checklist
            ? $this->workOrder->checklist->map(function ($item, $index) {
                return [
                    'item_text' => $item->description,
                    'is_visible' => $item->is_visible,
                    'is_custom' => $item->is_custom,
                    'is_default' => $item->is_default,
                    'sort_order' => $index
                ];
            })->toArray()
            : [];
    }

    public function loadMaintenanceChecklist()
    {
        if (!$this->maintenance) {
            return;
        }

        // Load the relationship if not loaded
        if (!$this->maintenance->relationLoaded('checklistItems')) {
            $this->maintenance->load(['checklist' => function ($query) {
                $query->whereNull('instance_id');
            }]);
        }

        $this->checklistItems = $this->maintenance->checklistItems
            ? $this->maintenance->checklistItems->map(function ($item, $index) {
                return [
                    'item_text' => $item->description,
                    'is_visible' => true,
                    'is_custom' => false,
                    'is_default' => true,
                    'sort_order' => $index
                ];
            })->toArray()
            : [];
    }

    /**
     * Add a new checklist item
     */
    public function addChecklistItem($text = null)
    {
        // If text is passed directly, use it, otherwise use the newCustomItem property
        $itemText = is_array($text) ? trim($text['text']) : (trim($text) ?? trim($this->newCustomItem));

        if (empty($itemText)) {
            $this->errorMessage = __('business.customer.validation.required');
            return;
        }

        if (strlen($itemText) > 255) {
            $this->errorMessage = __('business.work_orders.validation.max_length');
            return;
        }

        // Check for duplicates
        $isDuplicate = collect($this->checklistItems)->contains(function ($item) use ($itemText) {
            return strtolower($item['item_text']) === strtolower($itemText);
        });

        if ($isDuplicate) {
            $this->errorMessage = __('business.work_orders.validation.checklist_item_unique');
            return;
        }

        $this->checklistItems[] = [
            'item_text' => $itemText,
            'is_visible' => true,
            'is_custom' => true,
            'is_default' => false,
            'sort_order' => count($this->checklistItems)
        ];

        $this->newCustomItem = '';
        $this->errorMessage = '';
    }

    public function removeCustomItem($index)
    {
        if (isset($this->checklistItems[$index]) && $this->checklistItems[$index]['is_custom']) {
            unset($this->checklistItems[$index]);
            $this->checklistItems = array_values($this->checklistItems);

            // Reorder remaining items
            $this->checklistItems = collect($this->checklistItems)
                ->values()
                ->map(function ($item, $index) {
                    $item['sort_order'] = $index;
                    return $item;
                })
                ->toArray();
        }
    }

    public function toggleVisibility($index)
    {
        if (isset($this->checklistItems[$index])) {
            $this->checklistItems[$index]['is_visible'] = !$this->checklistItems[$index]['is_visible'];
        }
    }

    public function render()
    {
        return view('livewire.business.work-orders.checklist-section');
    }
}
