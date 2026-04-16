@extends('layouts.business.app')

@section('title', __('business.items_sold.title'))

@section('content')
    <div class="w-full mx-auto">
        <div class="w-full">
            <!-- Header Section -->
            <div class="flex justify-between items-center flex-row max-[1200px]:flex-col add-sub-mobile-wrapper">
                <div class="gap-[16px] flex flex-col">
                 
                        <h1 class="main-heading">{{ __('business.items_sold.title') }}</h1>
                        <p class="sub-heading">{{ __('business.items_sold.subtitle') }}</p>
                  
                </div>
            </div>
            <!-- Header Section -->

            <div class="mx-auto">
                <div class="py-4">
                    <livewire:business.items-sold />
                </div>
            </div>
        </div>
    </div>
@endsection
