<div class="bg-white">

    @include('business.account.tabs.parts.info')

    <div class="overflow-hidden">
        <div class="subsription-price-block">
            @if ($canSubscribe)
                <div class="price-box">
                    <div class="title-head">
                            <h1 class="text-[24px] font-[600] text-[#1C1D1D] break-auto-phrase">{{ __("Pay as you go prices") }}</h1>
                    </div>
                    <div class="prices-block">
                            <div class="flex items-center justify-between max-[767px]:flex-col">
                                <span class="text-[14px] font-[500] text-[#1D242B]">{{ __("1 Admin Charges") }}</span>
                                <span class="text-[14px] font-[500] text-[#1D242B]">{{ __("$:price/month", ['price' => $pricing['monthly']['admin']['price']]) }}</span>
                            </div>
                            <div class="flex items-center justify-between max-[767px]:flex-col">
                                <span class="text-[14px] font-[500] text-[#1D242B]">{{ __("1 Technician Charges") }}</span>
                                <span class="text-[14px] font-[500] text-[#1D242B]">{{ __("$:price/month", ['price' => $pricing['monthly']['technician']['price']]) }}</span>
                            </div>
                    </div>
                    @if(!$pastDue)
                        <div class="px-6 py-3 flex items-center justify-center mt-8">
                            <x-form.link :link="route('account.pricing.create')" class="btn btn-primary !font-[400]">{{ __("Proceed") }}</x-form.link>
                        </div>
                    @endif
                </div>
            @else
                <div class="mb-4 rounded-md bg-red-50 p-4 mt-5">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <x-icons name="alert" class="h-5 w-5 text-red-400" />
                        </div>
                        <div class="ml-3">
                            <p class="message text-xs text-red-600"> {{ trans('payments.pricing_not_set') }}</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
