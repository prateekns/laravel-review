@extends('layouts.business.app')

@section('content')
<div class="container-fluid mx-auto">
    <div class="w-full">
        <!-- Header Section -->
        <x-page-heading
            title="{{ __('Create New Sub-Admin') }}"
            description="{{ __('Fill in the details to create an account') }}"
            link="{{ route('business.sub-admins.index') }}" />
        <!-- Header Section -->

        @if (session('error'))
            <x-alert type="error" :message="session('error')" />
        @endif

        <div class="white-box">
            <livewire:business.sub-admin.create />
        </div>
    </div>
</div>
@endsection
