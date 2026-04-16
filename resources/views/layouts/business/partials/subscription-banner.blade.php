@if($isTrialActive)
    <!-- Free Trial Button -->
    <button type="button" class="flex justify-center items-center gap-2 px-4 py-2.5 trail-banners hover:bg-gray-50">
        <!-- Zap Icon -->
        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0">
            <path d="M10.8333 1.66675L2.5 11.6667H10L9.16667 18.3334L17.5 8.33341H10L10.8333 1.66675Z" stroke="#344054" stroke-width="1.67" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <!-- Button Text -->
        <p class="text-[#344054] font-[400] text-[14px]">
            Your free trial ends in {{$trialEndsIn == 0 ? 'less than' : ''}} <span class="text-[#E43232]">
                {{ $trialEndsIn > 0 ? $trialEndsIn : '1' }} days.</span>
        </p>
    </button>
@elseif(!is_null($planEndsIn))
    <button type="button" class="flex justify-center items-center gap-2 px-4 py-2.5 rounded-lg border border-[#D0D5DD] bg-white shadow-[0px_1px_2px_0px_rgba(16,24,40,0.05)] hover:bg-gray-50">
        <!-- Zap Icon -->
        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0">
            <path d="M10.8333 1.66675L2.5 11.6667H10L9.16667 18.3334L17.5 8.33341H10L10.8333 1.66675Z" stroke="#344054" stroke-width="1.67" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <!-- Button Text -->
        <span class="text-[#344054] text-sm font-semibold leading-5">
            Your plan ends in {{$planEndsIn == 0 ? 'less than' : ''}} <span class="text-red-500">
                {{ $planEndsIn > 0 ? $planEndsIn : '1' }} days.</span>
        </span>
    </button>
@else
    <!-- No subscription banner to show -->
@endif
