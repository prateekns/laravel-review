@extends('layouts.business.app')

@section('content')
    <div class="w-full mx-auto">
        <div class="w-full">
            <!-- Header Section -->
            <div class="flex flex-row items-end justify-between w-full gap-[163px] mb-8">
                <div class="flex flex-col gap-4 w-full">
                    <!-- Back Button -->
                    <a href="{{ route('templates.index') }}" class="back-btn">
                        <div class="flex items-center gap-2 cursor-pointer">
                            <div class="flex items-center justify-start">
                                <svg width="8" height="13" viewBox="0 0 8 13" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6.5 11.5L1.50001 6.49999L6.5 1.5" stroke="black" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <span class="text-[#0C1421] text-xs leading-4 ml-2">{{ __('Back') }}</span>
                            </div>
                        </div>
                    </a>

                    <!-- Title and Subtitle -->
                    <div class="flex justify-between items-center flex-row max-[1200px]:flex-col add-sub-mobile-wrapper">
                        <div class="gap-[16px] flex flex-col">

                            <h1 class="main-heading">{{ __('business.templates.edit_title') }}</h1>
                            <p class="sub-heading">{{ __('business.templates.edit_subtitle') }}</p>

                        </div>
                    </div>

                </div>
            </div>
            <!-- Header Section -->

            <livewire:business.templates.edit-template :template-id="$templateId" />
        </div>
    </div>
@endsection
