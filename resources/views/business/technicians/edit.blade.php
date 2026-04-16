@extends('layouts.business.app')

@section('content')
<div class="container-fluid mx-auto">
    <div class="w-full">

        <!-- Header Section -->
        <x-page-heading
            title="{{ __('Edit Technician') }}"
            description="{{ __('Fill in the details to edit Technician.') }}"
            link="{{ route('business.technicians.index') }}" />
        <!-- Header Section -->

        @if (session('error'))
            <x-notification-alert type="error"
                message="{{ session('error') }}" />
        @endif
        
        @if($limitReached && !$technician->status)
            <x-limit-warning :warningText="__('business.no_more_profiles')" :link="route('account.index')" />
        @endif

        <div class="white-box">
            <div x-data="technicianForm()" x-init="isEdit = true" @multi-selected-value="setSkillType($event.detail.value)">
                @include('business.technicians.form', ['technician' => $technician])
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
    window.oldTechnician = {
        status: "{{!$technician->status ? '0' :  $technician->status}}"
    };
</script>
@endsection
