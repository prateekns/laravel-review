@extends('layouts.business.app')

@section('content')
    <div class="container-fluid mx-auto">
        <div class="w-full">
            <!-- Header Section -->
            <div class="flex justify-between items-center flex-row max-[1200px]:flex-col add-sub-mobile-wrapper">
                <div class="gap-[16px] flex flex-col">
                    <h1 class="main-heading">{{ __('business.reports.title') }}</h1>
                    <p class="sub-heading">{{ __('business.reports.subtitle') }}</p>
                </div>
            </div>
            <!-- Header Section -->

            @if (session('error'))
                <x-alert type="error" :message="session('error')" />
            @endif

            <div class="flex flex-col w-full mt-[24px]">
                <livewire:business.reports.reports />
            </div>
        </div>
    </div>
@endsection
