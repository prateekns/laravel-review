<div x-data="updateCard()"
    @close-cancel.window="showConfirm=false"
    @close-confirm="$wire.cancelSubscription()"
    @card-updated.window="showToast=true;setTimeout(() => { showToast = false; }, 5000);">
 
    <div x-show="cardError.error" x-cloak>
        <x-toast type="error" message="cardError.error" x-show="cardError.error"/>
    </div>
    <div x-show="successMessage" x-cloak>
        <x-toast type="success" message="successMessage" x-show="successMessage"/>
    </div>
    <div x-show="processing"><x-loading/></div>

    <div class="my-plan-box">
        @if ($subscription && !$subscription->incomplete())
        <div class="my-plan-content" x-cloak>
            <div class="plans-wrapper">
                <div class="plan-content">
                    <div class="btn-box">
                        <button id="subscription-status" type="button" class="rounded-[4px] py-[2px] px-[7px] text-white bg-[#16A34A] text-[12px] font-[600]">
                            {{ $subscription?->stripe_status === 'past_due' ? 'PAYMENT DUE' : strtoupper($subscription?->stripe_status) }}
                        </button>
                    </div>

                    <div class="plan-box">
                        <div class="flex items-center justify-between px-[20px]">
                            <span class="text-[14px] font-[500] text-[#000000]">
                                Supports <span id="admin-slot">{{ $user->business->num_admin }}</span> Admin, <span id="technician-slot">{{ $user->business->num_technician }}</span> Technician
                            </span>
                        </div>

                        <div>
                            <div class="flex items-center justify-between px-[20px]">
                                @if ($isSubscribed && !$subscription->ends_at)
                                <span class="text-[14px] font-[500] text-[#000000] next-payment">
                                    Next payment: {{ $amountDue }} on {{ $nextPaymentDate }}
                                </span>
                                @elseif($pastDue)
                                    <span class="text-[14px] font-[500] text-[#000000] next-payment">
                                        Payment Due: {{ $amountDue }} on {{ $nextPaymentDate }}
                                    </span>
                                @elseif($isSubscribed && $subscription->ends_at)
                                    <span class="text-gray-600 subscription-ends-on">Subscription ends on {{ App\Helpers\Helper::getFormattedDate($subscription->ends_at) }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="plan-actions-btn">
                    @if (!$isSubscribed && $subscription && !$pastDue)
                        <a href="{{ route('account.pricing.create') }}" class="btn-box btn !text-[18px] !font-[400] items-center !justify-center w-[100%] min-[768px]:max-w-[218px] ml-auto" id="renew-subscription-link">Renew Subscription</a>
                    @endif
                    @if ($isSubscribed && ! $user->business->subscription('default')->ends_at)
                        <a href="{{ route('account.pricing.create') }}" class="btn-box btn !text-[18px] !font-[400] items-center !justify-center w-[100%] min-[768px]:max-w-[218px] ml-auto">
                            {{ __('Upgrade') }}
                        </a>
                        <a id="btnDowngrade" href="{{ route('account.plan.downgrade') }}" class="btn-box outlined !text-[18px] !font-[400] items-center !justify-center w-[100%] min-[768px]:max-w-[218px] min-[768px]:!px-[15px] ml-auto">
                            {{ __('Downgrade') }}
                        </a>
                    @endif

                    @if ($isSubscribed && !$subscription->ends_at)
                        <button type="button" class="btn-box outlined !text-[18px] !font-[400] items-center !justify-center w-[100%] min-[768px]:max-w-[218px] min-[768px]:!px-[15px] ml-auto" @click="showConfirm=true;">
                            {{__('Cancel Subscription')}}
                        </button>
                    @endif

                    @if ($isSubscribed && $subscription->ends_at)
                        <button wire:click="resumeSubscription" id="resume-subscription-button" type="button" class="btn-box outlined !text-[18px] !font-[400] items-center !justify-center w-[100%] min-[768px]:max-w-[218px] min-[768px]:!px-[15px] ml-auto">
                            {{__('Resume Subscription')}}
                        </button>
                    @endif
                </div>
            </div>
            
            <div class="plans-wrapper">
                <div class="plan-content justify-center">
                    <div class="plan-box px-[20px]">
                        <div class="flex items-center justify-between">
                            <span class="text-[14px] font-[500] text-[#000000]">
                                {{ __('Payment Information')}}
                            </span>
                        </div>
                        <div class="card-details">
                            <x-icons name="{{ $user->business->pm_type }}" />
                            <div x-show="!lastFour" id="card-details">Credit **{{$user->business->pm_last_four}}</div>
                            <div x-show="lastFour">Credit **<span x-text="lastFour"></span></div>
                            <svg width="14" height="15" viewBox="0 0 14 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3.66667 6.83398V4.16732C3.66667 3.28326 4.01786 2.43542 4.64298 1.8103C5.2681 1.18517 6.11595 0.833984 7 0.833984C7.88406 0.833984 8.7319 1.18517 9.35702 1.8103C9.98214 2.43542 10.3333 3.28326 10.3333 4.16732V6.83398M2.33333 6.83398H11.6667C12.403 6.83398 13 7.43094 13 8.16732V12.834C13 13.5704 12.403 14.1673 11.6667 14.1673H2.33333C1.59695 14.1673 1 13.5704 1 12.834V8.16732C1 7.43094 1.59695 6.83398 2.33333 6.83398Z" stroke="#0D44EA" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="plan-actions-btn">
                    <button @click="toggleUpdateCard"
                        type="button"
                        class="btn-box outlined !text-[18px] !font-[400] items-center !justify-center w-[100%] min-[768px]:max-w-[218px] min-[768px]:!px-[15px] ml-auto" id="change-card-button">
                        {{ __('Change Card')}}
                    </button>
                </div>
            </div>

            <div class="flex w-full change-card-box" x-show="showUpdateCard" x-cloak>
                <h2 class="flex w-full text-[16px] font-[600] text-[#1E40AF] pb-[12px] border-b-[1px] border-[#ECECEC] mb-[24px]">Change Card</h2>
                <form id="payment-form" action="{{ route('stripe.update.card') }}" method="POST">
                    @csrf
                    <input type="hidden" name="payment_method" id="payment-method">
                    <div id="card-form-section" class="col-span-full" x-cloak>
                        <div class="mt-2 col-span-2">
                                <div class="flex gap-2">
                                    <label for="card-holder-name" class="label-box">
                                        {{ __('Cardholder Name:')}}
                                    </label>
                                </div>
                                <div class="mt-2">
                                    <input type="text"
                                        id="card-holder-name"
                                        x-model="cardHolderName"
                                        x-ref="cardHolderName"
                                        name="cardHolderName"
                                        value="{{ $user->first_name }} {{ $user->last_name }}"
                                        class="!block w-full input-box" />
                                </div>
                                <p class="error-message-box" x-show="cardError.cardHolderName" x-text="cardError.cardHolderName"></p>
                            
                        </div>

                        <div class="mt-2 col-span-2">
                            
                                <div class="flex gap-2">
                                    <label for="card-number" class="label-box">
                                        {{ __('Card number')}}
                                    </label>
                                </div>
                                <div class="mt-2">
                                    <div id="card-number"
                                        class="!block w-full input-box">
                                    </div>
                                    <span id="card-brand"></span>
                                    <p class="error-message-box" x-text="cardError.cardNumber"></p>
                                </div>
                            
                        </div>

                        <div class="mt-2 col-span-2">
                            <div class="flex gap-2">
                                <label for="expiration-date" class="label-box">
                                    {{ __('Expiration date')}}
                                </label>
                            </div>
                            <div class="mt-2">
                                <div id="card-expiry"
                                    class="!block w-full input-box">
                                </div>
                                <p class="error-message-box" x-text="cardError.cardExpiry"></p>
                            </div>
                        </div>

                        <div class="mt-2 col-span-2">
                            <div class="flex gap-2">
                                <label for="cvc" class="label-box">
                                    {{ __('CVC')}}
                                </label>
                            </div>
                            <div class="mt-2">
                                <div id="card-cvc"
                                    class="!block w-full input-box">
                                </div>
                                <p class="error-message-box" x-text="cardError.cardCvc"></p>
                            </div>
                        </div>

                        <div class="flex w-full justify-end max-[767px]:flex-col mt-[24px] gap-[24px]">
                            
                            <button @click="toggleUpdateCard"
                                type="button"
                                class="btn-box outlined max-[767px]:w-full"  id="cancel-add-card-button">
                                {{ __('Cancel')}}
                            </button>
                                <x-form.button
                                    @click.prevent="saveCard"
                                    id="card-button"
                                    x-bind:disabled="processing"
                                    x-bind:class="{'opacity-75 cursor-not-allowed': processing}"
                                    class="btn-box btn max-[767px]:w-full">
                                    {{ __('Add Card')}}
                                </x-form.button>
                        
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- Update Card Form-->
        @endif
    </div>


    <!-- Billing History -->
    <div class="billing-history-box mt-[48px]">
        <div class="head-title px-[20px] max-[600px]:px-0">
            <h1 class="text-[28px] font-medium text-gray-900">{{ __('Billing History') }}</h1>
        </div>

        <div class="overflow-x-auto table-box   max-[600px]:!px-0 min-[600px]:!px-[10px]">
            @if ($user->business->invoices->count() > 0)
            <table class="min-w-full divide-y divide-white" aria-describedby="My Plan">
                <thead class=" !border-0">
                    <tr>
                        <th scope="col" class="!text-[16px] !text-blue">
                            {{ __('Date')}}
                        </th>
                        <th scope="col" class="!text-[16px] !text-blue">
                            {{ __('Transaction ID')}}
                        </th>
                        <th scope="col" class="!text-[16px] !text-blue">
                            {{ __('Price')}}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @foreach ($user->business->invoices as $invoice)
                    <tr>
                        <td data-label="Date" class="!text-[16px]">
                            {{ $invoice->created_at }}
                        </td>
                        <td data-label="Transaction ID" class="!text-[16px]">
                            {{ $invoice->invoice_number }}
                        </td>
                        <td data-label="Price" class="!text-[16px]">
                            {{ \App\Helpers\Helper::getFormattedAmountWithCurrency($invoice->amount_paid) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="flex items-center justify-between">
                <span class="text-gray-600 px-3">{{ __('No Billing History Found.') }}</span>
            </div>
            @endif
        </div>
    </div>

    @include('components.confirm.confirm-modal' , [
        'title' => __('Cancel Subscription'),
        'description' => __('business.message.subscription_cancel_confirm', ['date' => App\Helpers\Helper::getFormattedDate($subscription?->next_billing_date)]),
        'btnConfirm' => __('Confirm'),
    ])

    @if (session()->has('success'))
        <x-notification-alert  type="success" :message="session('success')" />
    @endif

    @if (session()->has('error'))
       <x-notification-alert type="error" :message="session('error')" />
    @endif

    <x-loading :target="'cancelSubscription,resumeSubscription'"/>
</div>
