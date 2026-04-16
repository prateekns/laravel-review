<?php

namespace App\Livewire\Business;

use App\Models\Business\Chemical\Chemical as ChemicalModel;
use App\Models\Business\Chemical\BusinessChemical;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Chemical extends Component
{
    public $chemicals;
    public $businessChemicals;
    public $editField = '';
    public $editValues = [];
    public $editing = false;

    private const EDIT_VALUES_KEY = 'editValues.*';

    /**
     * Mount the component
     */
    public function mount()
    {
        $this->loadChemical();
        $this->editValues = [];
    }

    /**
     * Load chemical data for the business
     */
    protected function loadChemical()
    {
        $business = Auth::guard('business')->user()->business;

        // Get all chemicals from master table where type = 1
        $this->chemicals = ChemicalModel::where('type', true)->get();

        // Get or create business chemicals
        $this->businessChemicals = collect();
        foreach ($this->chemicals as $chemical) {
            $businessChemical = BusinessChemical::firstOrCreate(
                [
                    'business_id' => $business->id,
                    'chemical_id' => $chemical->id
                ],
                [
                    'ideal_target' => $chemical->ideal_target
                ]
            );
            $this->businessChemicals->put($chemical->id, $businessChemical);
        }
    }

    /**
     * Start editing a field
     */
    public function startEdit($chemicalId, $value)
    {
        $this->resetValidation("editValues.$chemicalId");
        $this->editField = $chemicalId;
        $this->editValues[$chemicalId] = $value;
        $this->editing = true;
    }

    /**
     * Cancel editing
     */
    public function cancelEdit()
    {
        $this->resetValidation();
        $this->editField = '';
        $this->editing = false;
    }

    /**
     * Get the validation rules.
     *
     * @return array
     */
    /**
     * Get validation rules for a specific chemical
     */
    protected function getChemicalValidationRules($chemicalId)
    {
        $chemical = $this->chemicals->find($chemicalId);
        if (!$chemical || empty($chemical->range)) {
            return ['min:0.1', 'max:9999.99'];
        }

        // Parse range values (e.g., "1-5 ppm" -> [1, 5])
        preg_match('/(\d+\.?\d*)\s*[-–]\s*(\d+\.?\d*)/', $chemical->range, $matches);
        if (count($matches) === 3) {
            return ['min:' . $matches[1], 'max:' . $matches[2]];
        }

        return ['min:0.1', 'max:9999.99'];
    }

    /**
     * Get the validation rules
     */
    protected function rules()
    {
        return [
            'editValues.*' => [
                'required',
                'numeric',
            ],
        ];
    }

    /**
     * Get custom validation messages
     */
    protected function messages()
    {
        return [
            self::EDIT_VALUES_KEY . '.required' => __('common.auth.required'),
            self::EDIT_VALUES_KEY . '.numeric' => __('business.chemical_calculator.validation.numeric'),
        ];
    }

    /**
     * Update chemical value
     */
    public function updateChemical($chemicalId)
    {
        $validatedData = $this->validate();

        try {
            $businessChemical = $this->businessChemicals->get($chemicalId);
            if ($businessChemical) {
                $businessChemical->update([
                    'ideal_target' => $validatedData['editValues'][$chemicalId]
                ]);
            }

            $this->editField = '';
            $this->editing = false;

            $this->dispatch('notify-success', [
                'message' => __('business.message.chemical_updated_success')
            ]);

        } catch (\Exception $e) {
            $this->dispatch('notify-error');
        }
    }

    /**
     * Real-time validation as user types
     */
    public function updated($propertyName)
    {
        $this->validate();
    }

    /**
     * Render the component
     */
    public function render()
    {
        return view('livewire.business.chemical.chemical-list', [
            'chemicals' => $this->chemicals,
            'businessChemicals' => $this->businessChemicals
        ]);
    }
}
