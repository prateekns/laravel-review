@extends('layouts.business.app')

@section('content')
    <div class="container-fluid mx-auto">
        <div class="w-full">

            <!-- Header Section -->
            <x-page-heading
                title="{{ __('business.customers.add_new') }}"
                description="{{ __('business.customers.add_description') }}"
                link="{{ route('business.customers.index') }}" />
            <!-- Header Section -->

            @if (session('error'))
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-md">
                    <div class="text-sm text-red-600">
                        <ul>
                            <li>{{ session('error') }}</li>
                        </ul>
                    </div>
                </div>
            @endif

            <div x-data="customerForm">
                @include('business.customers.form', ['customer' => $customer])
            </div>
        </div>
    </div>
@endsection
