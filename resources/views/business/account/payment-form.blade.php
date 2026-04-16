@extends('layouts.business.app')

@section('title', 'Business Dashboard')

@section('content')

    <x-page-heading title="Review & Complete Payment"
        description="{{ __('Confirm your selections and enter payment details to activate your custom plan.') }}"
        link="{{ route('account.pricing.create') }}" />

    <div class="flex mt-6 w-full justify-between max-[1200px]:flex-col max-[1200px]:gap-[24px]" x-data="stripePayment()">
        <div x-show="processing">
            <x-loading />
        </div>
        <div class=" w-[calc(22%-20px)] max-[1200px]:w-full">
            <div class="bg-white rounded-[12px] shadow-sm p-[24px]">
                <h2
                    class="text-[44px] font-[600] text-blue border-b-[1px] border-grey-100 pb-[21px] flex flex-wrap items-center">
                    @if ($invoicePreview)
                        <span>${{ $invoicePreview['proration_amount'] }}</span>
                    @else
                        <span x-text="totalAmount">{{ $totalAmount }}</span><span class="text-[20px]"
                            x-text="totalAmountInterval">/mo</span>
                    @endif
                </h2>
                <div class="mt-[21px]">
                    <div
                        class="flex flex-col justify-between text-base font-medium text-gray-500 gap-[8px] max-[1200px]:flex-row max-[1200px]:justify-start max-[1200px]:flex-wrap ">
                        <p id="badge-admin"
                            class="text-[14px] font-[500] text-[#16A34A] py-[6px] px-[10px] flex max-w-max items-center justify-center items border-[1px] border-[#16A34A] rounded-[12px]">
                            {{ $subscriptionPricing['team_data']->num_admin }} Admins</p>
                        <p id="badge-technician"
                            class="text-[14px] font-[500] text-[#16A34A] py-[6px] px-[10px] flex max-w-max items-center justify-center items border-[1px] border-[#16A34A] rounded-[12px]">
                            {{ $subscriptionPricing['team_data']->num_technician }} Technicians</p>
                    </div>
                </div>
            </div>
        </div>

        <div class=" flex  w-[calc(78%-20px)] max-[1200px]:w-full">
            <div class="flex w-full">
                <form id="payment-form" action="{{ route('payment.process') }}" method="POST" class="w-full">
                    @csrf
                    <input type="hidden" name="payment_uuid" x-ref="payment_uuid" value="{{ $payment_uuid }}">
                    <div class="bg-white rounded-[12px] shadow-sm p-[24px]">
                        <div class="flex justify-center items-center gap-4 max-[767px]:flex-wrap">
                            @if ($invoicePreview)
                                @include('business.account.partials.proration-info')
                            @else
                                @foreach ($subscriptionPricing['pricing'] as $key => $pricing)
                                    <div
                                        class="bg-gray-100 rounded-[8px] p-[10px] flex-1/3 max-[500px]:flex-1/1 text-[#BBBDBF] border-[#BBBDBF] border-[1px] flex items-center gap-4 has-[:checked]:bg-blue-100 has-[:checked]:border-2 has-[:checked]:border-blue-500 has-[:checked]:text-white relative ">
                                        @if ($key != 'monthly' && $key != 'daily' && $pricing['discount'] > 0)
                                            <div class="offer-badge">
                                                Save {{ $pricing['discount'] . '%' }}
                                            </div>
                                        @endif
                                        <input type="radio" name="interval" x-model="interval"
                                            value="{{ $pricing['interval'] }}" id="interval-{{ $pricing['interval'] }}"
                                            @change="cardError.billing_cycle = null"
                                            {{ $pricing['interval'] == 'monthly' ? 'checked' : '' }}
                                            class="cursor-pointer w-[20px] h-[20px]">
                                        <div>
                                            <p class="text-[20px]  font-[600] ">{{ $pricing['label'] }}</p>
                                            <p class="text-[14px]  font-[400] ">
                                                ${{ $pricing['price'] }}/{{ $pricing['interval'] }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        @if ($errors->has('subscription'))
                            <x-alert type="error" :message="$errors->first('subscription')" class="mt-5" />
                        @endif

                        <x-confirm.payment-error title="Payment Failed" message="{{ __('payments.payment_error') }}"
                            btnConfirm="{{ __('	Retry') }}" />
                    </div>

                    <div class="bg-white rounded-[12px] shadow-sm p-[24px] mt-[20px]">
                        <div class="form-box" x-show="!stripeError" x-cloak>
                            <div class="col-span-full">
                                <h3 id="payment-heading" class="font-[600] text-[16px] text-blue">
                                    {{ __('Payment Information') }}</h3>
                            </div>
                            <div class="bg-[#ECECEC] flex w-[100%] h-[1px]"></div>

                            @if ($paymentMethods?->count() > 0)
                                <div class="col-span-full">
                                    <p x-show="paymentMethod" class="text-[14px] font-[400] text-yellow-500">
                                        <span id="existing-card-info">{{ __('payments.card_info_text') }}</span>
                                    </p>
                                    <div class="mt-2">
                                        <label for="card-holder-name"
                                            class="label-box mb-2">{{ __('Select Payment Method') }}:</label>
                                        <!-- Wrapper with relative positioning -->
                                        <div class="relative">
                                            <select name="pm" @change="paymentMethodOnChange" id="sel-payment-method"
                                                class="mt-2 !block w-full appearance-none input-box">
                                                <option value="">{{ __('Select Payment Method') }}</option>
                                                @foreach ($paymentMethods as $paymentMethod)
                                                    <option value="{{ $paymentMethod->id }}">
                                                        {{ ucfirst($paymentMethod->card->brand) }}
                                                        *{{ $paymentMethod->card->last4 }}</option>
                                                @endforeach
                                                <option value="add-card">{{ __('Add New Card') }}</option>
                                            </select>

                                            <!-- Custom arrow icon container -->
                                            <div
                                                class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                                <svg class="h-5 w-5 fill-current" xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20">
                                                    <path
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                                                </svg>
                                            </div>
                                        </div>
                                        <input type="hidden" name="card_holder_name" x-model="cardHolderName"
                                            x-ref="cardHolderName" id="card-holder-name"
                                            value="{{ $user->first_name }} {{ $user->last_name }}">
                                    </div>
                                    <div x-show="errors.payment_method_error" x-text="errors.payment_method_error"
                                        class="error-message-box" x-cloak></div>
                                </div>
                            @endif

                            <div id="card-form-section" x-show="showPaymentForm" class="flex gap-[24px] flex-col" x-cloak>
                                <div class="col-span-full">
                                    <div class="mt-2">
                                        <div class="flex gap-2">
                                            <label for="card-holder-name"
                                                class="label-box">{{ __('Cardholder Name') }}:</label>

                                        </div>
                                        <div class="mt-2">
                                            <input type="text" id="card-holder-name" x-model="cardHolderName"
                                                x-ref="cardHolderName"
                                                value="{{ $user->first_name }} {{ $user->last_name }}"
                                                name="card_holder_name" class="input-box" />
                                        </div>
                                        <div class="error-message-box" x-show="cardError.cardHolderName"
                                            x-text="cardError.cardHolderName" x-cloak></div>
                                    </div>
                                </div>

                                <div class="col-span-full">
                                    <div class="mt2">
                                        <div class="flex gap-2">
                                            <label for="card-number" class="label-box">{{ __('Card number') }}</label>

                                        </div>
                                        <div class="mt-2">
                                            <div id="card-number" class="!block input-box"></div>
                                            <span id="card-brand"></span>
                                        </div>
                                        <div class="error-message-box" x-text="cardError.cardNumber" x-cloak></div>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-[10px]">
                                    <div class="mt-2">
                                        <div class="flex gap-2">
                                            <label for="expiration-date"
                                                class="label-box">{{ __('Expiry Date') }}</label>

                                        </div>
                                        <div class="mt-2">
                                            <div id="card-expiry" class="!block input-box"></div>
                                        </div>
                                        <div class="error-message-box" x-text="cardError.cardExpiry" x-cloak></div>
                                    </div>

                                    <div class="mt-2">
                                        <div class="flex gap-2">
                                            <label for="cvc" class="label-box">{{ __('CVC') }}</label>

                                        </div>
                                        <div class="mt-2">
                                            <div id="card-cvc" class="!block input-box"></div>
                                        </div>

                                        <p class="error-message-box" x-text="cardError.cardCvc" x-cloak></p>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="payment_method" id="payment-method">
                            <input type="hidden" name="client_secret" x-ref="clientSecret" x-model="clientSecret"
                                id="client-secret">

                            <div class="col-span-full">
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between">
                                        <span
                                            class="text-[#212529] text-[20px] font-[400]">{{ __('Total Amount:') }}</span>
                                        @if ($invoicePreview)
                                            <span class="text-[#1E40AF] text-[20px] font-[600]"
                                                id="total-proration-amount">${{ $invoicePreview['proration_amount'] }}</span>
                                        @else
                                            <span class="text-[#1E40AF] text-[20px] font-[600]" id="total-amount"
                                                x-text="totalAmountInDecimal">{{ $totalAmountInDecimal }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-span-full text-right flex  justify-end">
                                    <div class="mt-2 flex gap-[20px] max-[767px]:flex-col-reverse max-[767px]:w-full">
                                        <a href="{{ route('account.pricing.create') }}"
                                            class="btn-box outlined items-center justify-center max-[767px]:w-full max-[767px]:flex">{{ __('Cancel') }}
                                        </a>
                                        <x-form.button @click.prevent="actionPay" x-bind:disabled="processing"
                                            x-bind:class="{ 'opacity-75 cursor-not-allowed': processing }"
                                            class="btn-box btn min-w-[200px]">
                                            {{ __('Pay Now') }}
                                        </x-form.button>
                                    </div>
                                </div>
                            </div>
                        </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        window.pricing = {
            daily: "{{ isset($subscriptionPricing['pricing']['daily']) ? $subscriptionPricing['pricing']['daily']['price']: 0 }}",
            dailyInterval: "/daily",
            monthly: "{{ isset($subscriptionPricing['pricing']['monthly']) ? $subscriptionPricing['pricing']['monthly']['price']: 0 }}",
            monthlyInterval: "/mo",
            halfyearly: "{{ isset($subscriptionPricing['pricing']['half-yearly']) ? $subscriptionPricing['pricing']['half-yearly']['price'] : 0 }}",
            halfyearlyInterval: "/6mo",
            yearly: "{{ isset($subscriptionPricing['pricing']['yearly']) ? $subscriptionPricing['pricing']['yearly']['price'] : 0 }}",
            yearlyInterval: "/yr",
            currency: "$",
        };
    </script>
@endsection
