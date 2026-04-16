@extends('layouts.business.app')

@section('content')
    <div class="container-fluid mx-auto" x-data="workOrderForm">
        <div class="w-full">
            <!-- Header Section -->
            <x-page-heading title="{{ __('business.customer.create_work_order') }}"
                description="{{ __('business.work_orders.create_description') }}"
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
                @include('business.work-orders.form', [
                    'workOrder' => null,
                    'customers' => $customers,
                    'templates' => $templates,
                ])
            </div>
        </div>
    </div>
@endsection
