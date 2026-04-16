<div class="bg-white  w-full px-[10px]">
    <div>
        <div class="flex items-center gap-2">
            <h2 class="text-[16px] font-[600] text-[#000000]">Current billing cycle</h2>
            <span id="billing-frequency" class="bg-[#0D44EA] inlin-flex py-[2px] px-[7px] text-white text-[12px] font-[600] rounded-[4px]">
                {{ $recentOrder?->billing_frequency == 'half-yearly' ? 'Semi-Annually' : ucfirst($recentOrder?->billing_frequency) }}
            </span>
        </div>
        <p id="billing-cycle-text" class="text-[16px] font-[400] text-[#000000] whitespace-normal leading-relaxed mt-[16px] mb-[24px]">
           {{ __('Your billing cycle cannot be changed during an active subscription downgrade.') }}
        </p>
    </div>

    <hr class="border-[#ECECEC] mb-[24px]"  />

   
    <div>
        <h3 class="text-[16px] font-[600] text-[#000000]">{{ __('Upcoming Billing Update') }}</h3>
        <div class="mt-[16px]">
            <p class="text-[18px] font-[500] text-[#000000]">{{ __('Credit Balance') }}: $<span id="proration-amount">{{ abs(number_format($invoicePreview['proration_amount'], 2)) }}</span></p>
            <p class="text-[18px] font-[500] text-[#000000]">{{ __('Billing Period') }}: <span id="billing-period">{{ $invoicePreview['start_date'] }} - {{ $invoicePreview['end_date'] }}</span></p>
        </div>
        <p class="text-[16px] font-[400] text-[#000000] mt-[16px]" id="proration-info-text">
            {{ __('A credit for the unused portion of admins and technicians for the remainder of your current billing cycle will be applied to your account. This results in a credit balance of $:balance, which will be adjusted in your next invoice.', ['balance' => abs(number_format($invoicePreview['proration_amount'], 2))]) }}
        </p>
    </div>

    <hr class="border-[#ECECEC] mb-[24px] mt-[24px]" />

    
    <div>
        <h3 class="text-[16px] font-[600] text-[#000000]">{{ __('Going forward') }}</h3>
        <p class="text-[16px] font-[400] text-[#000000] mt-[16px]">
             {{ __("Your new monthly subscription total will be $:amount per month starting :date, including:", ['amount' => number_format($invoicePreview['next_cycle_amount'], 2) , 'date' => $invoicePreview['next_cycle_start_date']]) }}
        </p>
        <div class="flex items-center gap-3 mt-4">
            <span class="border-[1px] border-[#16A34A] inline-flex items-center px-[10px] py-[6px] rounded-[12px] text-[14px] font-[500] bg-white text-[#16A34A]">
                {{ $subscriptionPricing['team_data']->num_admin }} Admins
            </span>
            <span class="border-[1px] border-[#16A34A] inline-flex items-center px-[10px] py-[6px] rounded-[12px] text-[14px] font-[500] bg-white text-[#16A34A]">
                {{ $subscriptionPricing['team_data']->num_technician }} Technician
            </span>
        </div>
    </div>

</div>
