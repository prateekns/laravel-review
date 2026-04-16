<div
x-data="{
    confirmCreatePrice: false,
    showToast:false,
    showSuccess:false,
    showError:false,
    successMessage: '',
    errorMessage: '',
    }"
    @confirm-create-price.window="
        confirmCreatePrice = true;
    "
    @price-created.window="
        confirmCreatePrice=false;
        successMessage = $event.detail[0].message;
        showSuccess = true;
        showToast = true;
        setTimeout(() => {
            showToast = false;
            showSuccess = false;
        }, 5000);
    "
    @price-create-failed.window="
        confirmCreatePrice=false;
        errorMessage = $event.detail[0].message;
        showError = true;
        showToast = true;
        setTimeout(() => {
            showToast = false;
            showError = false;
        }, 5000);
    ">
    <div x-show="showSuccess" x-cloak>
        <x-toast type="success" message="successMessage" x-show="successMessage"/>
    </div>
    <div x-show="showError" x-cloak>
        <x-toast type="error"  message="errorMessage" x-show="errorMessage"/>
    </div>

    <div class="p-6 bg-white rounded-lg shadow-sm">
        <form wire:submit="save" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-7 gap-6">
                <!-- Monthly Prices Section -->
                <div class="col-span-3 space-y-4">
                    <h3 class="text-lg font-semibold text-gray-900">Monthly Base Prices</h3>
                    <div>
                        <label for="adminMonthlyPrice" class="block text-sm font-medium text-gray-700">
                            Admin Monthly Price
                        </label>
                        <div class="mt-1 relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">$</span>
                            </div>
                            <input type="number" step="0.01" wire:model="adminMonthlyPrice" id="adminMonthlyPrice"
                                class="block w-4/5 rounded-md bg-white px-6 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-1 focus:-outline-offset-1 focus:outline-indigo-600 sm:text-sm/6 "
                                placeholder="0.00">
                        </div>
                        @error('adminMonthlyPrice') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="technicianMonthlyPrice" class="block text-sm font-medium text-gray-700">
                            Technician Monthly Price
                        </label>
                        <div class="mt-1 relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">$</span>
                            </div>
                            <input type="number" step="0.01" wire:model="technicianMonthlyPrice" id="technicianMonthlyPrice"
                                class="block w-4/5 rounded-md bg-white px-6 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-1 focus:-outline-offset-1 focus:outline-indigo-600 sm:text-sm/6 "
                                placeholder="0.00">
                        </div>
                        @error('technicianMonthlyPrice') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Discount Percentages Section -->
                <div class="col-span-3 space-y-4">
                    <h3 class="text-lg font-semibold text-gray-900">Discount Percentages</h3>
                    
                    <div>
                        <label for="halfYearDiscountPercent" class="block text-sm font-medium text-gray-700">
                            Half Year Discount
                        </label>
                        <div class="mt-1 relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">%</span>
                            </div>
                            <input type="number" step="0.1" wire:model="halfYearDiscountPercent" id="halfYearDiscountPercent"
                                class="block w-4/5 rounded-md bg-white px-7 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-1 focus:-outline-offset-1 focus:outline-indigo-600 sm:text-sm/6 "
                                placeholder="0">
                        </div>
                        @error('halfYearDiscountPercent') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="yearlyDiscountPercent" class="block text-sm font-medium text-gray-700">
                            Yearly Discount
                        </label>
                        <div class="mt-1 relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">%</span>
                            </div>
                            <input type="number" step="0.1" wire:model="yearlyDiscountPercent" id="yearlyDiscountPercent"
                                class="block w-4/5 rounded-md bg-white px-7 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-1 focus:-outline-offset-1 focus:outline-indigo-600 sm:text-sm/6 "
                                placeholder="0">
                        </div>
                        @error('yearlyDiscountPercent') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="col-span-1 space-y-4">
                <button type="button" wire:click="preview"
                        class="cursor-pointer inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        {{ __('Preview') }}
                    </button>
                </div>
            </div>

            @if($prices)
            <!-- Price Calculations Display -->
            <div class="mt-10">
                <h4 class="text-lg font-semibold text-gray-900">Subscription Price Summary</h4>
                <p class="sub-heading">The pricing table below illustrates the monthly, half-yearly, and yearly pricing for one admin and one technician.</p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
                    <!-- Monthly Subscription -->
                    <div class="bg-white p-6 rounded-lg border border-gray-200">
                        <h4 class="font-medium text-gray-900 text-xl mb-4">Monthly Subscription</h4>
                        <div class="space-y-4">
                            <div>
                                <p class="text-sm text-gray-600">Admin Price</p>
                                <p class="text-lg font-semibold text-gray-900">${{ number_format($prices['admin']['monthly'], 2) }}/month</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Technician Price</p>
                                <p class="text-lg font-semibold text-gray-900">${{ number_format($prices['technician']['monthly'], 2) }}/month</p>
                            </div>
                            <div class="pt-4 border-t border-gray-200">
                                <p class="text-sm text-gray-600">Total Price (1 Month)</p>
                                <p class="text-xl font-bold text-gray-900">${{ number_format($prices['admin']['monthly'] + $prices['technician']['monthly'], 2) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Half-Yearly Subscription -->
                    <div class="bg-white p-6 rounded-lg border border-gray-200">
                        <h4 class="font-medium text-gray-900 text-xl mb-4">Half-Yearly Subscription</h4>
                        <div class="space-y-4">
                            @if($prices['half_year_discount'])
                            <div class="text-sm text-green-600 mb-2">{{ $prices['discounts']['half-yearly'] }}% Discount Applied</div>
                            @endif
                            <div>
                                <p class="text-sm text-gray-600">Admin Price</p>
                                <p class="text-lg font-semibold text-gray-900">${{ number_format($prices['admin']['half-yearly'], 2) }}/6mo</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Technician Price</p>
                                <p class="text-lg font-semibold text-gray-900">${{ number_format($prices['technician']['half-yearly'], 2) }}/6mo</p>
                            </div>
                            <div class="pt-4 border-t border-gray-200">
                                <p class="text-sm text-gray-600">Total Price (6 Months)</p>
                                <p class="text-xl font-bold text-gray-900">${{ number_format(($prices['admin']['half-yearly'] + $prices['technician']['half-yearly']), 2) }}</p>
                                @if($prices['half_year_discount'])
                                    <p class="text-sm text-green-600 mt-1">Save ${{ number_format($prices['half_year_discount'], 2) }}</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Yearly Subscription -->
                    <div class="bg-white p-6 rounded-lg border border-gray-200">
                        <h4 class="font-medium text-gray-900 text-xl mb-4">Yearly Subscription</h4>
                        <div class="space-y-4">
                            @if($prices['yearly_discount'])
                                <div class="text-sm text-green-600 mb-2">{{ $prices['discounts']['yearly'] }}% Discount Applied</div>
                            @endif
                            <div>
                                <p class="text-sm text-gray-600">Admin Price</p>
                                <p class="text-lg font-semibold text-gray-900">${{ number_format($prices['admin']['yearly'], 2) }}/year</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Technician Price</p>
                                <p class="text-lg font-semibold text-gray-900">${{ number_format($prices['technician']['yearly'], 2) }}/year</p>
                            </div>
                            <div class="pt-4 border-t border-gray-200">
                                <p class="text-sm text-gray-600">Total Price (12 Months)</p>
                                <p class="text-xl font-bold text-gray-900">${{ number_format(($prices['admin']['yearly'] + $prices['technician']['yearly']), 2) }}</p>
                                @if($prices['yearly_discount'])
                                    <p class="text-sm text-green-600 mt-1">Save ${{ number_format($prices['yearly_discount'], 2) }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end mt-6 space-x-4">
                <button type="button" wire:click="validatePrices"
                    class="cursor-pointer inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    {{ __('Save Updated Prices') }}
                </button>
            </div>
            @endif

            <x-loading :target="'validatePrices,preview'"/>
           
            <div x-show="confirmCreatePrice">
                <x-admin.price-create-confirm/>
            </div>
        </form>
    </div>
</div>
