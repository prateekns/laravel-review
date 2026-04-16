@extends('layouts.business.app')

@section('content')
<div class="container-fluid mx-auto">
    <div class="w-full">
        <!-- Header Section -->
        <x-page-heading
            title="{{ __('Edit Sub-Admin') }}"
            description="{{ __('Fill in the details to edit an account.') }}"
            link="{{ route('business.sub-admins.index') }}" />
        <!-- Header Section -->

        @if (session('error'))
            <x-alert type="error" :message="session('error')" />
        @endif

        @if($limitReached && !$subAdmin->status)
            <x-limit-warning :warningText="__('business.no_more_profiles')" :link="route('account.index')" />
        @endif

        <div class="white-box">
            <livewire:business.sub-admin.create :subAdmin="$subAdmin" />
        </div>
    </div>
</div>
@endsection
