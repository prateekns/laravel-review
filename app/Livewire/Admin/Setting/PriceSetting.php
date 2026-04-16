<?php

namespace App\Livewire\Admin\Setting;

use Livewire\Component;
use App\Services\Admin\AdminPriceService;
use App\Models\Shared\Setting;
use Illuminate\Support\Facades\Log;
use Exception;
use App\Exceptions\Admin\PriceCreateException;

class PriceSetting extends Component
{
    public $adminMonthlyPrice;
    public $technicianMonthlyPrice;
    public $halfYearDiscountPercent;
    public $yearlyDiscountPercent;
    public $halfYearlyMultiplier;
    public $yearlyMultiplier;
    public $setting;
    public array $basePrices = [];
    public array $prices = [];

    public function mount()
    {
        $this->setting = Setting::first();
        $this->basePrices = app(AdminPriceService::class)->getMonthlyPrices();
        $this->adminMonthlyPrice = $this->basePrices['admin']['price'] ?? 0;
        $this->technicianMonthlyPrice = $this->basePrices['technician']['price'] ?? 0;
        $this->halfYearDiscountPercent = $this->setting->discount_half_yearly ?? 0;
        $this->yearlyDiscountPercent = $this->setting->discount_yearly ?? 0;
    }

    /**
     * Get the validation rules.
     *
     * @return array
     */
    protected function rules(): array
    {
        return [
            'adminMonthlyPrice' => ['required','numeric','gt:0'],
            'technicianMonthlyPrice' => ['required','numeric','gt:0'],
            'halfYearDiscountPercent' => ['nullable','numeric','between:0,100'],
            'yearlyDiscountPercent' => ['nullable','numeric','between:0,100'],
        ];
    }

    /**
     * Get the messages for the validation rules.
     *
     * @return array
     */
    protected function messages(): array
    {
        return [
            'adminMonthlyPrice.required' => __('admin.validation.invalid_price'),
            'adminMonthlyPrice.numeric' => __('admin.validation.invalid_price'),
            'adminMonthlyPrice.gt' => __('admin.validation.invalid_price'),
            'technicianMonthlyPrice.required' => __('admin.validation.invalid_price'),
            'technicianMonthlyPrice.numeric' => __('admin.validation.invalid_price'),
            'technicianMonthlyPrice.gt' => __('admin.validation.invalid_price'),
            'halfYearDiscountPercent.between' => __('admin.validation.discount_percent'),
            'yearlyDiscountPercent.between' => __('admin.validation.discount_percent'),
        ];
    }

    /**
     * Validate the entered discount value
     * @param $value discount value
     * @return boolean
     */
    public function isValidDiscount($value): bool
    {
        // Allow null or empty string
        if (is_null($value) || $value === '') {
            return true;
        }

        // Check if numeric and NOT negative
        if (is_numeric($value) && $value >= 0) {
            return true;
        }

        // Otherwise invalid
        return false;
    }

    /**
     * Calculate discount multiplier
     * @return void
     */
    public function discountMultiplier(): void
    {
        $halfYearlyDiscount = $this->halfYearDiscountPercent ? $this->halfYearDiscountPercent / 100 : 0;
        $yearlyDiscount = $this->yearlyDiscountPercent ? $this->yearlyDiscountPercent / 100 : 0;
        $this->halfYearlyMultiplier = 6 * (1 - $halfYearlyDiscount);
        $this->yearlyMultiplier = 12 * (1 - $yearlyDiscount);
    }


    /**
     * Pricie calculations
     * @return void
     */
    public function calculatePrices(): void
    {
        if ($this->adminMonthlyPrice > 0
            && $this->technicianMonthlyPrice > 0
            && $this->isValidDiscount($this->halfYearDiscountPercent)
            && $this->isValidDiscount($this->yearlyDiscountPercent)
        ) {

            $this->discountMultiplier();

            // Calculate and round Admin prices
            $adminHalfYearlyPrice = round($this->adminMonthlyPrice * $this->halfYearlyMultiplier, 2);
            $adminYearlyPrice = round($this->adminMonthlyPrice * $this->yearlyMultiplier, 2);

            // Calculate and round Technician prices
            $technicianHalfYearlyPrice = round($this->technicianMonthlyPrice * $this->halfYearlyMultiplier, 2);
            $technicianYearlyPrice = round($this->technicianMonthlyPrice * $this->yearlyMultiplier, 2);

            $this->prices = [
                'admin' => [
                    'monthly' => $this->adminMonthlyPrice,
                    'half-yearly' => $adminHalfYearlyPrice,
                    'yearly' => $adminYearlyPrice,
                ],
                'technician' => [
                    'monthly' => $this->technicianMonthlyPrice,
                    'half-yearly' => $technicianHalfYearlyPrice,
                    'yearly' => $technicianYearlyPrice,
                ],
                'discounts' => [
                    'half-yearly' => $this->halfYearDiscountPercent,
                    'yearly' => $this->yearlyDiscountPercent,
                ],
                'half_year_discount' => $this->getAmoutSavedAfterDiscount($adminHalfYearlyPrice, $technicianHalfYearlyPrice, 6),
                'yearly_discount' => $this->getAmoutSavedAfterDiscount($adminYearlyPrice, $technicianYearlyPrice, 12),
            ];
        }
    }

    /**
     * Getting amount saved after applying the discount
     * @param $adminPrice Admin price
     * @param $technicianPrice
     * @param $interval 6|12 for half-yearly or yearly
     */
    public function getAmoutSavedAfterDiscount($adminPrice, $technicianPrice, $interval)
    {
        return ($this->adminMonthlyPrice + $this->technicianMonthlyPrice) * $interval - ($adminPrice + $technicianPrice);
    }

    /**
     * Preview the prices
     */
    public function preview()
    {
        $this->validate();
        $this->calculatePrices();
    }

    /**
     * Validate new prices
     */
    public function validatePrices()
    {
        $this->validate();
        $this->dispatch('confirm-create-price');
    }

    /**
     * Save new prices
     */
    public function save()
    {
        $this->validate();

        if (!$this->canCreatePrice()) {
            $this->dispatch('price-create-failed', ['message' => __('admin.message.price_not_changed')]);
            return;
        }

        try {
            app(AdminPriceService::class)->createSubscriptionPricing($this->prices);
        } catch (PriceCreateException $e) {
            $this->dispatch('price-create-failed', ['message' => __('admin.message.price_saving_failed')]);
        } catch (Exception $e) {
            Log::error('Failed to update prices: '.$e->getMessage());
            $this->dispatch('price-create-failed', ['message' => __('admin.message.price_create_failed')]);
        }

        $this->dispatch('price-created', ['message' => __('admin.message.price_created')]);
    }

    /**
     * Validate if new prices can be created
     */
    public function canCreatePrice(): bool
    {
        return $this->basePrices['admin']['price'] !== $this->adminMonthlyPrice
            || $this->basePrices['technician']['price'] !== $this->technicianMonthlyPrice
            || $this->setting->discount_half_yearly !== $this->halfYearDiscountPercent
            || $this->setting->discount_yearly !== $this->yearlyDiscountPercent;
    }

    /**
     * Render the view
     */
    public function render()
    {
        $this->calculatePrices();
        return view('livewire.admin.setting.price-setting', [
            'prices' => $this->prices,
        ]);
    }
}
