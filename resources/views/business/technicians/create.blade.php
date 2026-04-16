@extends('layouts.business.app')

@section('content')
<div class="container-fluid mx-auto">
    <div class="w-full">

        <!-- Header Section -->
        <x-page-heading
            title="{{ __('Add New Technician') }}"
            description="{{ __('Provide technician details to create a new account.') }}"
            link="{{ route('business.technicians.index') }}" />
        <!-- Header Section -->

        @if (session('error'))
            <x-notification-alert type="error"
                message="{{ session('error') }}" />
        @endif

        <div class="white-box">
            <div x-data="technicianForm()" x-init="isEdit = false" @multi-selected-value="setSkillType($event.detail.value)">
                @include('business.technicians.form', ['technician' => $technician])
            </div>
        </div>
    </div>
</div>
@endsection
