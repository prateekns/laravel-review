@extends('layouts.business.app')

@section('title', __('Manage Your Current Plan'))

@section('content')
<div class="w-full" x-data="downgradePlan()">
    <div class="flex justify-between items-center">
        <div class="flex items-center">
            <div>
                <a href="{{ route('account.index') }}" class="back-btn">
                    <p>
                        <x-icons name="back" />
                        <span>{{ __('Back') }}</span>
                    </p>
                </a>
                <h1 class="main-heading mt-4">{{ __('Manage Your Current Plan') }}</h1>
                <p class="sub-heading">{{ __('You’re currently on a Pay-As-You-Go plan. You can adjust the number of Admins or Technicians anytime. Downgrades will reflect in your next billing cycle.') }}</p>
                
            </div>
        </div>
    </div>
    @include('business.account.partials.downgrade-banner')
    <p class="error-message-box" x-show="errors.team_size" x-text="errors.team_size"></p>

    <div class="mt-8">
        @if (session('error'))
            <x-alert type="error" :message="session('error')" />
        @endif
        <form
            action="{{ route('account.downgrade.store') }}"
            method="POST"
            @submit.prevent="submitForm"
            x-ref="form">
            @csrf
            <div class="grid grid-cols-2 max-[767px]:grid-cols-1 gap-x-[32px] gap-y-[28px]">

                <div class="choose-tech-box">
                    <table class="table-fixed w-full" aria-describedby="Plan Downgrade">
                        <thead class="border-b-[1px] border-b-[#E5E7EB]">
                            <tr>
                                <th class="text-left font-[500] text-[16px] text-[#4C4C4C] leading-[21px]">{{ __('Role') }}</th>
                                <th class="text-left font-[500] text-[16px] text-[#4C4C4C] leading-[21px]">{{ __('Current') }}</th>
                                <th class="font-[500] text-[16px] text-[#4C4C4C] leading-[21px]">{{ __('Adjust To') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-left font-[600] text-[20px] text-[#212529] leading-[26px] ">
                                    <div class="mt-[24px]">Admin</div></td>
                                <td class="text-left font-[600] text-[20px] text-[#4C4C4C] leading-[26px] pl-1"><div class="mt-[24px]" id="currentAdmin"> {{ $adminLimit }}</div></td>
                                <td>
                                    <div class="counter-box mt-[24px]">
                                        <button type="button" id="decrementAdmin" @click="decrementAdmin()" class="btn-decrease">
                                            <svg width="14" height="2" viewBox="0 0 14 2" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M1.16675 1H12.8334" stroke="#1F2937" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </button>
                                        <input type="number" x-model.number="admin" x-ref="admin" value="1" name="admin" max="{{ $adminLimit }}" />
                                        <button type="button" id="incrementAdmin" @click="incrementAdmin()" class="btn-increase">
                                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M6 1V11M11 6L1 6" stroke="#1F2937" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <p class="error-message-box" x-show="errors.admin" x-text="errors.admin"></p>
                </div>

                <div class="choose-tech-box">
                    <table class="table-fixed w-full" aria-describedby="Downgrade Plan">
                        <thead class="border-b-[1px] border-b-[#E5E7EB]">
                            <tr>
                                <th class="text-left font-[500] text-[16px] text-[#4C4C4C] leading-[21px]">{{ __('Role') }}</th>
                                <th class="text-left font-[500] text-[16px] text-[#4C4C4C] leading-[21px]">{{ __('Current') }}</th>
                                <th class="font-[500] text-[16px] text-[#4C4C4C] leading-[21px]">{{ __('Adjust To') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-left font-[600] text-[20px] text-[#212529] leading-[26px] "><div class="mt-[24px]">Technician</div> </td>
                                <td class="text-left font-[600] text-[20px] text-[#4C4C4C] leading-[26px] pl-1"><div class="mt-[24px]" id="currentTechnician"> {{ $technicianLimit }} </div></td>
                                <td>
                                    <div class="counter-box mt-[24px]">
                                        <button type="button" id="decrementTechnician" @click="decrementTechnician()" class="btn-decrease">
                                            <svg width="14" height="2" viewBox="0 0 14 2" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M1.16675 1H12.8334" stroke="#1F2937" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </button>
                                        <input type="number" x-model.number="technician" x-ref="technician" name="technician" value="1" max="{{ $technicianLimit }}"  />
                                        <button type="button" id="incrementTechnician" @click="incrementTechnician()" class="btn-increase">
                                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M6 1V11M11 6L1 6" stroke="#1F2937" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <p class="error-message-box" x-show="errors.technician" x-text="errors.technician"></p>
                </div>
            </div>

            <div class="choose-pricing-box">
                    <div class="pricing-table">
                        <div class="grid grid-cols-2">
                            <div class="text-[20px] font-[400] text-blue">
                                <span x-text="admin"></span> admin
                            </div>
                            <div class="text-right text-[20px] font-[400] text-[#1D242B]">$<span x-text="getAdminTotal()"></span></div>
                        </div>
                        <div class="grid grid-cols-2">
                            <div class="text-[20px] font-[400] text-blue">
                                <span x-text="technician"></span> {{ __('technician')}}
                            </div>
                            <div class="text-right text-[20px] font-[400] text-[#1D242B]">$<span x-text="getTechnicianTotal()"></span></div>
                        </div>
                        <div class="grid grid-cols-2" id="total-amount">
                            <div class="text-[20px] font-[400] text-blue">{{ __('Total Amount')}}</div>
                            <div class="text-right text-[20px] font-[600] text-blue">$<span x-text="getTotalPrice()"></span></div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end mt-[28px] max-[767px]:flex-col gap-[24px]">
                   
                        <a href="{{ route('account.index') }}" class="btn-box outlined items-center justify-center max-[767px]:w-full max-[767px]:flex">{{ __('Cancel') }}
                        </a>

                        <x-form.button
                            x-bind:disabled="processing"
                            x-bind:class="{'opacity-75 cursor-not-allowed': processing}"
                            class="btn-box btn items-center justify-center max-[767px]:w-full">
                            {{ __('Confirm & Proceed')}}
                            <x-form.loading />
                        </x-form.button>
                </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    window.pricing = {
        admin: {{$pricing['monthly']['admin']['price']}},
        technician: {{$pricing['monthly']['technician']['price']}}
    };

    window.subscription = {
        adminCount: {{$adminLimit}},
        technicianCount: {{$technicianLimit}},
    };
</script>
@endsection
