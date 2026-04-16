<?php

namespace App\Livewire\Business\Customers;

use App\Models\Business\Customer;
use App\Helpers\FileHelper;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use App\Constants\StoragePaths;

class CustomerPoolDetails extends Component
{
    public $customerId = null;
    public $poolDetails = null;
    public $isViewMode = false;
    public $showDetails = false;
    public $hasNoPoolDetails = true;

    /**
     * Handle customer selection event
     */
    #[On('customer-selected')]
    public function customerSelected($customerId)
    {
        if (empty($customerId)) {
            Log::warning('No customer ID provided in selection event');
            $this->reset('customerId', 'poolDetails', 'showDetails');
            return;
        }

        $this->customerId = is_numeric($customerId) ? (int)$customerId : null;
        if ($this->customerId) {
            $this->loadPoolDetails();
            $this->showDetails = true;
        }
    }

    /**
     * Mount the component
     */
    public function mount($customerId = null, bool $isViewMode = false)
    {
        $this->isViewMode = $isViewMode;
        $this->customerId = $customerId;

        // Load pool details if customer ID is provided
        if ($this->customerId) {
            $this->loadPoolDetails();
        } else {
            $this->showDetails = false;
        }
    }

    /**
     * Check if all pool details are empty
     */
    protected function hasNoPoolDetails($details): bool
    {
        $fieldsToCheck = [
            'pool_size_gallons',
            'pool_type',
            'clean_psi',
            'filter_details',
            'pump_details',
            'heater_details',
            'cleaner_details',
            'salt_system_details',
            'heat_pump_details',
            'aux_details'
        ];

        foreach ($fieldsToCheck as $field) {
            if (!empty($details[$field])) {
                return false;
            }
        }

        // Check if any equipment images exist
        if (!empty($details['equipment_images']) && $details['equipment_images']->isNotEmpty()) {
            return false;
        }

        return true;
    }

    /**
     * Load pool details for a customer
     */
    protected function loadPoolDetails()
    {
        if (!$this->validateCustomerId()) {
            return;
        }

        try {
            $customer = $this->getCustomer();

            if (!$customer) {
                $this->resetPoolDetails();
                return;
            }

            $poolDetails = $this->buildPoolDetails($customer);
            $this->updatePoolDetailsState($poolDetails);

        } catch (\Exception $e) {
            $this->resetPoolDetails();
        }
    }

    /**
     * Validate customer ID
     */
    private function validateCustomerId(): bool
    {
        if (empty($this->customerId)) {
            Log::warning('Attempted to load pool details without customer ID');
            return false;
        }
        return true;
    }

    /**
     * Get customer with business scope
     */
    private function getCustomer(): ?Customer
    {
        return Customer::where('business_id', auth()->guard('business')->user()->business_id)
            ->where('id', $this->customerId)
            ->with(['country'])
            ->first();
    }

    /**
     * Reset pool details state
     */
    private function resetPoolDetails(): void
    {
        $this->poolDetails = null;
        $this->showDetails = false;
        $this->hasNoPoolDetails = true;
    }

    /**
     * Build equipment image array for a specific type
     */
    private function buildEquipmentImage(Customer $customer, string $type, string $field, ?string $details): array
    {
        $filename = $customer->getRawOriginal($field);

        if (!$filename) {
            return [
                'type' => $type,
                'url' => null,
                'thumb_url' => null,
                'details' => $details
            ];
        }

        $url = FileHelper::getS3ImageTempUrl(StoragePaths::CUSTOMER_EQUIPMENT_IMAGES.$filename, 5);
        $thumbUrl = FileHelper::getS3ImageTempUrl(StoragePaths::CUSTOMER_EQUIPMENT_THUMBNAIL_IMAGES.$filename, 5);

        return [
            'type' => $type,
            'url' => $url,
            'thumb_url' => $thumbUrl,
            'details' => $details
        ];
    }

    /**
     * Build pool details array from customer data
     */
    private function buildPoolDetails(Customer $customer): array
    {
        $basicDetails = [
            'pool_size_gallons' => $customer->pool_size_gallons,
            'pool_type' => $customer->pool_type_label,
            'clean_psi' => $customer->clean_psi,
            'entry_instruction' => $customer->entry_instruction,
            'tech_notes' => $customer->tech_notes,
            'admin_notes' => $customer->admin_notes,
            'full_address' => $customer->full_address,
            'address' => $customer->address,
            'street' => $customer->street,
            'city' => $customer->city_name,
            'state' => $customer->state_name,
            'zip_code' => $customer->zip_code,
            'country' => $customer->country_name,
            'filter_details' => $customer->filter_details,
            'pump_details' => $customer->pump_details,
            'heater_details' => $customer->heater_details,
            'cleaner_details' => $customer->cleaner_details,
            'salt_system_details' => $customer->salt_system_details,
            'heat_pump_details' => $customer->heat_pump_details,
            'aux_details' => $customer->aux_details,
            'aux2_details' => $customer->aux2_details,
        ];

        $equipmentImages = $this->buildEquipmentImages($customer);

        return array_merge($basicDetails, ['equipment_images' => $equipmentImages]);
    }

    /**
     * Build equipment images collection
     */
    private function buildEquipmentImages(Customer $customer): Collection
    {
        $images = collect([
            $this->buildEquipmentImage($customer, 'filter', 'filter_image', $customer->filter_details),
            $this->buildEquipmentImage($customer, 'pump', 'pump_image', $customer->pump_details),
            $this->buildEquipmentImage($customer, 'cleaner', 'cleaner_image', $customer->cleaner_details),
            $this->buildEquipmentImage($customer, 'heater', 'heater_image', $customer->heater_details),
            $this->buildEquipmentImage($customer, 'heat_pump', 'heat_pump_image', $customer->heat_pump_details),
            $this->buildEquipmentImage($customer, 'aux', 'aux_image', $customer->aux_details),
            $this->buildEquipmentImage($customer, 'aux2', 'aux2_image', $customer->aux2_details),
            $this->buildEquipmentImage($customer, 'salt_system', 'salt_system_image', $customer->salt_system_details)
        ]);

        return $images->filter(fn ($image) => !empty($image['url']))->values();
    }

    /**
     * Update pool details state
     */
    private function updatePoolDetailsState(array $poolDetails): void
    {
        $this->hasNoPoolDetails = $this->hasNoPoolDetails($poolDetails);
        $this->poolDetails = $poolDetails;
        $this->showDetails = true;
    }

    /**
     * Render the component
     */
    public function render()
    {
        return view('livewire.business.customers.customer-pool-details');
    }
}
