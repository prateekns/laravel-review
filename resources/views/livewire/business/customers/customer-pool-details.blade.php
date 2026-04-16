<div>
    <div wire:loading.remove>
        @if($showDetails)
            <div class="pool-details-box mt-[24px]">
                <!-- Pool Details Section - Only visible if pool data exists -->
                @if(!$hasNoPoolDetails)
                    @include('livewire.business.customers.customer-pool-details-content')
                @endif

                <!-- Address Section - Always visible -->
                <div class="flex flex-col mt-[24px]">
                    <h3 class="text-[14px] font-[400] text-[#000000] mb-[4px]">{{ __('business.work_orders.address') }}</h3>
                    <p class="text-[16px] font-[400] text-[#000000]" id="customer-address">{{ $poolDetails['address'] . ', ' . $poolDetails['street'] }}</p>
                    <p class="text-[16px] font-[400] text-[#000000]">{{ __('business.customer.zip_code') }}: {{ $poolDetails['zip_code'] }}</p>
                    <p class="text-[16px] font-[400] text-[#000000]">{{ $poolDetails['city'] . ', ' . $poolDetails['state'] . ', ' . $poolDetails['country'] }}</p>
                </div>
            </div>
        @endif
    </div>
</div>
