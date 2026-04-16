@extends('layouts.business.app')

@section('content')
    <div class="w-full mx-auto">
        <div class="w-full">
            <!-- Header Section -->
            <div class="flex justify-between items-center flex-row max-[1200px]:flex-col add-sub-mobile-wrapper">
                <div class="gap-[16px] flex flex-col">
                 
                        <h1 class="main-heading">{{ __('business.templates.manage') }}</h1>
                        <p class="sub-heading">{{ __('business.templates.manage_subtitle') }}</p>
                  
                </div>
            </div>
            <!-- Header Section -->

            <livewire:business.templates.manage-templates />
        </div>
    </div>
@endsection
