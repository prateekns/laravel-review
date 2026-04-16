@extends('layouts.business.app')

@section('title', __('business.work_orders.edit_title'))

@section('content')
    <div class="container-fluid mx-auto" x-data="workOrderForm">
        <div class="w-full">
            <!-- Header Section -->
            <x-page-heading title="{{ __('business.work_orders.edit_title') }}"
            description="{{ __('business.work_orders.edit_subtitle') }}"
            link="{{ route('business.work-orders.index') }}" />

            @if (session('notification'))
                <x-notification-alert type="{{ session('notification.type') }}"
                    message="{{ session('notification.message') }}" />
            @endif

            @if (session('error'))
                <x-notification-alert type="error"
                    message="{{ session('error') }}" />
            @endif

            <div>
                <!-- Include the common form with the work order data -->
                @include('business.work-orders.form', [
                    'workOrder' => $workOrder,
                    'customers' => $customers,
                    'templates' => $templates,
                    'action' => route('business.work-orders.update', $workOrder),
                    'method' => 'PUT'
                ])
            </div>
        </div>
    </div>
@endsection
