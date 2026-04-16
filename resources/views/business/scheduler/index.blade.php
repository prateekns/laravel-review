@extends('layouts.business.app')

@section('content')
    <div class="p-0 max-w-full mx-auto" x-data="scheduler()">
        <!-- Notification Alert Component -->
        <x-notification-alert />

        <div class="flex justify-between items-center flex-row max-[1200px]:flex-col add-sub-mobile-wrapper">
            <div class="gap-[16px] flex flex-col">
                <h1 class="main-heading">{{ __('business.scheduler.title') }}</h1>
                <p class="sub-heading">{{ __('business.scheduler.subtitle') }}</p>
            </div>
        </div>

        @livewire('business.scheduler.unassigned-jobs', ['jobId' => $jobId ?? null, 'job' => $job ?? null])
        @livewire('business.scheduler.calendar-component', ['jobId' => $jobId ?? null])
    </div>
@endsection
