@extends('layouts.business.app')

@section('title', 'Business Dashboard')

@section('content')

    <x-page-heading title="Review & Complete Payment"
        description="{{ __('Confirm your selections and enter payment details to activate your custom plan.') }}"
        link="{{ route('account.pricing.create') }}" />

    <div class="flex mt-6 w-full justify-between max-[1200px]:flex-col max-[1200px]:gap-[24px]" x-data="processDowngrade()">
        <div x-show="processing">
            <x-loading />
        </div>
        <div class=" w-[calc(22%-20px)] max-[1200px]:w-full">
            <div class="bg-white rounded-[12px] shadow-sm p-[24px]">
                <h2
                    class="text-[44px] font-[600] text-blue border-b-[1px] border-grey-100 pb-[21px] flex flex-wrap items-center">
                    @if ($invoicePreview)
                        <span>${{ $invoicePreview['proration_amount'] }}</span>
                    @endif
                </h2>
                <div class="mt-[21px]">
                    <div
                        class="flex flex-col justify-between text-base font-medium text-gray-500 gap-[8px] max-[1200px]:flex-row max-[1200px]:justify-start max-[1200px]:flex-wrap ">
                        <p id="badge-admin"
                            class="text-[14px] font-[500] text-[#16A34A] py-[6px] px-[10px] flex max-w-max items-center justify-center items border-[1px] border-[#16A34A] rounded-[12px]">
                            {{ $order->admin_qty_change }} Admins</p>
                        <p id="badge-technician"
                            class="text-[14px] font-[500] text-[#16A34A] py-[6px] px-[10px] flex max-w-max items-center justify-center items border-[1px] border-[#16A34A] rounded-[12px]">
                            {{ $order->technician_qty_change }} Technicians</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex w-[calc(78%-20px)] max-[1200px]:w-full">
            <div class="flex w-full">
                <form id="payment-form" action="{{ route('account.process.downgrade') }}" method="POST" class="w-full"
                    @submit.prevent="actionDowngrade">
                    @csrf
                    <input type="hidden" name="payment_uuid" x-ref="payment_uuid" value="{{ $payment_uuid }}">
                    <div class="bg-white rounded-[12px] shadow-sm p-[24px]">
                        <div class="flex justify-center items-center gap-4 max-[767px]:flex-wrap">
                            @if ($invoicePreview)
                                @include('business.account.partials.downgrade-proration')
                            @endif
                        </div>

                        @if ($errors->has('subscription'))
                            <x-alert type="error" :message="$errors->first('subscription')" class="mt-5" />
                        @endif

                        <x-confirm.payment-error title="Payment Failed" message="{{ __('payments.payment_error') }}"
                            btnConfirm="{{ __('	Retry') }}" />
                    </div>

                    <div class="bg-white rounded-[12px] shadow-sm p-[24px] mt-[20px]">
                        <div class="form-box">
                            <div class="col-span-full">
                                <h3 id="payment-heading" class="font-[600] text-[16px] text-blue">
                                    {{ __('Credit Information') }}</h3>
                            </div>
                            <div class="bg-[#ECECEC] flex w-[100%] h-[1px]"></div>

                            <div class="col-span-full">
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between">
                                        <span
                                            class="text-[#212529] text-[20px] font-[400]">{{ __('Credit Balance:') }}</span>
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
                                        <a href="{{ route('account.plan.downgrade') }}"
                                            class="btn-box outlined items-center justify-center max-[767px]:w-full max-[767px]:flex">{{ __('Cancel') }}
                                        </a>
                                        <x-form.button x-bind:disabled="processing"
                                            x-bind:class="{ 'opacity-75 cursor-not-allowed': processing }"
                                            class="btn-box btn min-w-[200px]">
                                            {{ __('Confirm Downgrade') }}
                                        </x-form.button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
