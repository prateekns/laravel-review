@extends('layouts.business.app')

@section('content')
    <div class="w-full mx-auto">
        <div class="w-full">
            <!-- Header Section -->
            <div class="flex justify-between items-center flex-row max-[1200px]:flex-col add-sub-mobile-wrapper ">
                
                <!-- Title and Subtitle -->
                <div class="gap-[16px] flex flex-col">
                    <h1 class="main-heading">
                        {{ __('business.checklist.manage_checklist') }}
                    </h1>
                    <p class="sub-heading">
                        {{ __('business.checklist.subtitle') }}
                    </p>
                </div>
                <x-form.link
                    type="link"
                    link="{{ route('templates.index') }}"
                    class="inline-flex btn-box btn"
                    variant="link"
                >
                    {{ __('business.checklist.add_template') }}
                </x-form.link>
               
            </div>
            <!-- Header Section -->

            <livewire:business.checklists.manage-checklist />
        </div>
    </div>
@endsection
