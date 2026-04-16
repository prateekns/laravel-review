@extends('layouts.business.app')

@section('title', __('business.maintenance.edit_title'))

@section('content')
<div class="container-fluid mx-auto" x-data="maintenanceOrderForm">
    <div class="w-full">
        <!-- Header Section -->
        <x-page-heading title="{{ __('business.maintenance.edit_title') }}"
        description="{{ __('business.maintenance.edit_subtitle') }}"
        link="{{ route('business.work-orders.maintenance.index') }}" />
    </div>

    @if (session('notification'))
        <x-notification-alert type="{{ session('notification.type') }}"
            message="{{ session('notification.message') }}" />
    @endif

    @if (session('error'))
        <x-notification-alert type="error"
            message="{{ session('error') }}" />
    @endif

    @include('business.maintenance.form', [
        'maintenance' => $maintenance,
        'customers' => $customers,
        'templates' => $templates,
    ])
</div>
@endsection
