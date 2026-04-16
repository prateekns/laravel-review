@extends('layouts.business.app')

@section('content')
    <div class="container-fluid mx-auto">
        <div class="w-full">
            <!-- Header Section -->
            <x-page-heading title="{{ __('business.customer.edit_title') }}"
                description="{{ __('business.customer.edit_description') }}" link="{{ route('business.customers.index') }}" />
            <!-- Header Section -->

            @if (session('notification'))
                <x-notification-alert type="{{ session('notification.type') }}"
                    message="{{ session('notification.message') }}" />
            @endif

            <div x-data="customerForm">
                @include('business.customers.form', ['customer' => $customer])
            </div>

        </div>
    </div>
@endsection
