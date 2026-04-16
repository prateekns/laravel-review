@extends('layouts.business.app')

@section('content')
    <div class="container-fluid mx-auto" x-data="maintenanceOrderForm">
        <div class="w-full">
            <!-- Header Section -->
            <x-page-heading title="{{ __('business.maintenance.create_title') }}"
                description="{{ __('business.maintenance.create_description') }}"
                link="{{ route('business.work-orders.maintenance.index') }}" />

            @if (session('notification'))
                <x-notification-alert type="{{ session('notification.type') }}"
                    message="{{ session('notification.message') }}" />
            @endif

            @if (session('error'))
                <x-notification-alert type="error"
                    message="{{ session('error') }}" />
            @endif

            <div>
                @include('business.maintenance.form', [
                    'maintenance' => $maintenance,
                    'customers' => $customers,
                    'templates' => $templates,
                ])
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/maintenance.js') }}"></script>
@endpush
