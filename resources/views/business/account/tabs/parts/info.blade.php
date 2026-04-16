<div class="subscription-info-banner-box" x-data="{ showDowngradeInfo: false }">
    @if($isSubscribed && $canSubscribe)
        <div class="subscription-plan-info relative">
            <div class="info">
                <div class="icon">
                    <svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect width="25" height="24" rx="12" fill="#DFB400" />
                        <path d="M12.5 11.1871V13.2362M7.41636 15.0808C6.94348 15.9005 7.53504 16.9247 8.48132 16.9247H16.5187C17.465 16.9247 18.0565 15.9005 17.5836 15.0808L13.565 8.11508C13.0918 7.29497 11.9082 7.29497 11.435 8.11508L7.41636 15.0808ZM12.5 14.8755H12.5041V14.8796H12.5V14.8755Z" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>

                </div>
                <p class="message">
                    {{ __("You have an subscription plan for :admin Admin, :technician Technician", ['admin' => $user->business->num_admin, 'technician' => $user->business->num_technician]) }}
                </p>
            </div>
            <p class="plan-message inline-flex items-center gap-[4px]">
                 @if($subscription->ends_at)
                    {{__("Subscription ends on : ") }} 
                    <span class="date">{{ App\Helpers\Helper::getFormattedDate($subscription->ends_at) }}</span>
                @else
                    {{ __("Next payment:") }}
                    <span class="amount">{{ $amountDue }}</span>
                    <span>{{ __("on") }}</span>
                    <span class="date">{{ $nextPaymentDate }}</span>
                    @if($user->business->credit_balance > 0)
                        <span class="cursor-pointer" @click="showDowngradeInfo = true"><x-icons.info /></span>
                    @endif
                @endif
            </p>

            @if($user->business->credit_balance > 0)
                <div class="downgrade-info" x-show="showDowngradeInfo" x-cloak>
                    <p class="text-[12px] text-[#374151] font-[400] break-auto-phrase">
                        {{ __("Your plan has been downgraded. :balance remains as surplus and will be auto-adjusted in the next cycle, so you will only pay :pay instead of :next_payment.",
                            ['balance' => $creditBalance, 'pay' => $amountDue, 'next_payment' => $nextPayment]) }}
                    </p>
                    <span class="cursor-pointer absolute top-[16px] right-[16px]" @click="showDowngradeInfo = false"><x-icons.close /></span>
                </div>
            @endif
        </div>

    @elseif($user->business->onTrial())

        <div class="trail-subscription-info">
            <div class="info">
                <div class="icon">
                    <svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="0.5" width="24" height="24" rx="12" fill="#16A34A" />
                        <mask id="mask0_1116_184690" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="3" y="3" width="19" height="18">
                            <rect x="3.5" y="3" width="18" height="18" fill="#D9D9D9" />
                        </mask>
                        <g mask="url(#mask0_1116_184690)">
                            <path d="M6.0731 17.5232L8.52935 10.6941C8.5871 10.546 8.67435 10.433 8.7911 10.3551C8.90798 10.2772 9.03423 10.2383 9.16985 10.2383C9.26023 10.2383 9.34435 10.2534 9.42223 10.2836C9.5001 10.314 9.5741 10.3643 9.64423 10.4344L14.0434 14.8333C14.1135 14.9036 14.1637 14.9776 14.1939 15.0555C14.2243 15.1334 14.2395 15.2175 14.2395 15.3079C14.2395 15.4435 14.2005 15.5697 14.1227 15.6865C14.0448 15.8033 13.9318 15.8906 13.7837 15.9482L6.95435 18.4045C6.81885 18.4573 6.68835 18.4655 6.56285 18.429C6.43735 18.3925 6.32941 18.3291 6.23904 18.2387C6.14866 18.1483 6.08523 18.0404 6.04873 17.9149C6.0121 17.7894 6.02023 17.6588 6.0731 17.5232ZM20.2567 8.45552C20.1682 8.54877 20.0646 8.59539 19.9459 8.59539C19.8271 8.59539 19.7235 8.5512 19.6352 8.46283L19.586 8.4137C19.3822 8.20983 19.1346 8.10789 18.8434 8.10789C18.552 8.10789 18.3044 8.20983 18.1005 8.4137L14.3014 12.2128C14.213 12.3012 14.1094 12.3466 13.9907 12.349C13.8719 12.3515 13.7659 12.3061 13.6727 12.2128C13.5794 12.1195 13.5328 12.0146 13.5328 11.8984C13.5328 11.782 13.5794 11.6772 13.6727 11.584L17.4716 7.78483C17.8476 7.40895 18.3024 7.22102 18.836 7.22102C19.3697 7.22102 19.8245 7.40895 20.2005 7.78483L20.2567 7.84108C20.3452 7.92958 20.3895 8.03202 20.3895 8.14839C20.3895 8.26464 20.3452 8.36702 20.2567 8.45552ZM10.9091 6.29927C11.0024 6.20602 11.1072 6.15939 11.2235 6.15939C11.3399 6.15939 11.4447 6.20602 11.538 6.29927L11.6966 6.45789C12.0966 6.85789 12.2966 7.3377 12.2966 7.89733C12.2966 8.45695 12.0966 8.93677 11.6966 9.33677L11.561 9.47233C11.4727 9.56083 11.3691 9.60627 11.2504 9.60864C11.1316 9.61102 11.0255 9.56558 10.9322 9.47233C10.8389 9.37908 10.7923 9.27427 10.7923 9.15789C10.7923 9.04164 10.8389 8.93683 10.9322 8.84345L11.0679 8.70789C11.2909 8.48489 11.4024 8.2147 11.4024 7.89733C11.4024 7.58008 11.2909 7.30989 11.0679 7.08677L10.9091 6.92814C10.8207 6.83964 10.7753 6.73602 10.7728 6.61727C10.7704 6.49852 10.8159 6.39252 10.9091 6.29927ZM13.8024 5.0012C13.8957 4.90795 14.0005 4.86133 14.1169 4.86133C14.2332 4.86133 14.338 4.90795 14.4313 5.0012L15.2302 5.80033C15.6111 6.18108 15.8015 6.63827 15.8015 7.17189C15.8015 7.70552 15.6111 8.1627 15.2302 8.54345L12.9313 10.8426C12.8428 10.9311 12.7392 10.9765 12.6204 10.9789C12.5017 10.9813 12.3957 10.9358 12.3024 10.8426C12.2092 10.7493 12.1625 10.6445 12.1625 10.5281C12.1625 10.4118 12.2092 10.307 12.3024 10.2137L14.6015 7.91477C14.8053 7.71089 14.9072 7.46327 14.9072 7.17189C14.9072 6.88052 14.8053 6.63289 14.6015 6.42902L13.8024 5.63008C13.7139 5.54158 13.6685 5.43795 13.6661 5.3192C13.6637 5.20045 13.7092 5.09445 13.8024 5.0012ZM19.1389 13.5829C19.0456 13.6761 18.9408 13.7228 18.8244 13.7228C18.7082 13.7228 18.6034 13.6761 18.5102 13.5829L17.9274 13.0003C17.6995 12.7723 17.4207 12.6583 17.0908 12.6583C16.761 12.6583 16.4822 12.7723 16.2544 13.0003L15.6716 13.5829C15.5831 13.6714 15.4795 13.7168 15.3607 13.7192C15.242 13.7216 15.136 13.6761 15.0427 13.5829C14.9495 13.4896 14.9029 13.3848 14.9029 13.2685C14.9029 13.1522 14.9495 13.0474 15.0427 12.954L15.6255 12.3715C16.0302 11.9666 16.5187 11.7641 17.0908 11.7641C17.6629 11.7641 18.1514 11.9666 18.5563 12.3715L19.1389 12.954C19.2274 13.0425 19.2728 13.1461 19.2752 13.2649C19.2775 13.3836 19.2321 13.4896 19.1389 13.5829Z" fill="white" />
                        </g>
                    </svg>

                </div>
                <p class="text-[20px] text-[#000000] font-[500] break-auto-phrase">{{ __("You're currently on a Trial Plan!", ['period' => $trial_period]) }}</p>
            </div>
    

            <div class="trail-plan-description">
                <p class="text-[16px] text-[#374151] font-[500] break-auto-phrase">{{ __("You have access to :admin Admin and :technician Technician slots during this trial.", ['admin' => $trial_admin, 'technician' => $trial_technician]) }}</p>
                <p class="text-[16px] text-[#374151] font-[500]  w-xl mx-auto max-[767px]:w-full break-auto-phrase">{{ __("Upgrade anytime to add more users or continue using the service with our Pay-As-You-Go plan.") }}</p>
            </div>
        </div>

        @elseif($pastDue)
        <div class="flex items-center gap-2 justify-center text-center p-[24px] text-sm rounded-[4px] bg-yellow-100 border border-[#D7CFAD]">
            <x-icons name="alert-round" fill="#DFB400"/>
            <p class="text-[15px] text-[#212529] font-[500] break-auto-phrase">{{ __("Payment attempt unsuccessful. If you need help, contact our website support team.") }}</p>
        </div>
       

    @elseif(!$isSubscribed && !$user->business->onTrial() && $subscription)
        <div class="flex items-center gap-2 justify-center text-center p-[24px] text-sm rounded-[4px] bg-yellow-100 border border-[#D7CFAD]">
            <x-icons name="alert-round" fill="#DFB400"/>
            <p class="text-[15px] text-[#212529] font-[500] break-auto-phrase">{{ __("You currently do not have an active plan. Please purchase to continue.") }}</p>
        </div>

    @elseif(!$isSubscribed && !$user->business->onTrial() && !$subscription)
        <div class="trial-ended-info">
            <div class="flex items-center gap-2 justify-center text-center py-[10px] px-[16px] mb-4 text-sm rounded-[4px] bg-white border border-[#DFB400]">
                <x-icons name="alert-round" fill="#DFB400"/>
                <p class="text-[20px] text-[#000000] font-[500] break-auto-phrase">{{ ucwords(__("Your trial has ended")) }}</p>
            </div>

            <div class="flex flex-col gap-2 text-center mt-4 px-12">
                <p class="text-[16px] text-[#374151] font-[500]  w-xl mx-auto max-[767px]:w-full break-auto-phrase">
                    {{ __("Your free trial is over.", ['period' => $trial_period]) }}
                </p>
                    
                <p class="text-[16px] text-[#374151] font-[500] break-auto-phrase">
                    {{ __("You currently had access to :admin Admin and :technician Technician to keep managing your work orders and technician schedules.", ['admin' => $trial_admin,'technician' => $trial_technician]) }}
                </p>

                <p class="text-[16px] text-[#374151] font-[500] w-xl mx-auto max-[767px]:w-full break-auto-phrase">
                    {{ __("To continue using this service, please select your Pay-As-You-Go plan.") }}
                </p>
            </div>
        </div>
    @endif
</div>
