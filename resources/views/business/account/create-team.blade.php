@extends('layouts.business.app')

@section('title', __('Create Team Size'))

@section('content')
<div class="w-full" x-data="createTeamSize()">
    <div class="flex justify-between items-center">
        <div class="flex items-center">
            <div>
                <a href="{{ route('account.index') }}" class="back-btn">
                    <p>
                        <x-icons name="back" />
                        <span>{{ __('Back') }}</span>
                    </p>
                </a>
                <h1 class="main-heading mt-4">{{ __('Choose Your Team Size') }}</h1>
                <p class="sub-heading">{{ __('Select how many Admins and Technicians you want to include in your plan') }}</p>
                
            </div>
        </div>
    </div>
    @include('business.account.partials.plan-info-banner')
    <p class="error-message-box" x-show="errors.team_size" x-text="errors.team_size"></p>

    <div class="mt-8">
        @if (session('error'))
            <x-alert type="error" :message="session('error')" />
        @endif
        <form
            action="{{ route('account.team.pricing.store') }}"
            method="POST"
            @submit.prevent="submitForm"
            x-ref="form">
            @csrf
            <div class="grid grid-cols-2 max-[767px]:grid-cols-1 gap-x-[32px] gap-y-[28px]">

                <div class="choose-tech-box">

                    <div class="flex">
                        <label for="admin" class="flex text-[20px] font-[600] text-[#212529]">{{ __('How many admins do you need?') }}</label>

                    </div>
                    <div class="counter-box">
                        <button type="button" id="decrementAdmin" @click="decrementAdmin()" class="btn-decrease"><svg width="14" height="2" viewBox="0 0 14 2" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M1.16675 1H12.8334" stroke="#1F2937" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>

                        <input type="number" x-model="admin" x-ref="admin" value="{{ $adminSize }}" name="admin" max="100" />

                        <button type="button" id="incrementAdmin" @click="incrementAdmin()" class="btn-increase"><svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6 1V11M11 6L1 6" stroke="#1F2937" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                    </div>
                    <p class="error-message-box" x-show="errors.admin" x-text="errors.admin"></p>
                </div>

                <div class="choose-tech-box">
                    <div class="flex">
                        <label for="technician" class="flex text-[20px] font-[600] text-[#212529]">{{ __('How many technicians do you need?') }}</label>

                    </div>
                    <div class=" counter-box">
                        <button type="button" id="decrementTechnician" @click="decrementTechnician()" class="btn-decrease"><svg width="14" height="2" viewBox="0 0 14 2" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M1.16675 1H12.8334" stroke="#1F2937" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg></button>

                        <input type="number" x-model="technician" x-ref="technician" name="technician" value="{{ $technicianSize }}" max="100"  />

                        <button type="button" id="incrementTechnician" @click="incrementTechnician()" class="btn-increase"><svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6 1V11M11 6L1 6" stroke="#1F2937" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg></button>
                    </div>
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
                                <span x-text="technician"></span> technician
                            </div>
                            <div class="text-right text-[20px] font-[400] text-[#1D242B]">$<span x-text="getTechnicianTotal()"></span></div>
                        </div>
                        <div class="grid grid-cols-2" id="total-amount">
                            <div class="text-[20px] font-[400] text-blue">Total Amount</div>
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
                            {{ __('Confirm & Proceed for Payment')}}
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
        isTrialActive: {{$isTrialActive ? 'true' : 'false'}},
        adminCount: {{$adminSize}},
        technicianCount: {{$technicianSize}},
        isSubscribed: {{$isSubscribed ? 'true' : 'false'}},

    };
    window.max_team_qty = {{$maxTeamQty}};
</script>
@endsection
