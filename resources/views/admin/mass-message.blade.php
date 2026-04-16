@extends('layouts.admin.app')

@section('title', 'Mass Message')

@section('content')
    <div class="container mx-auto">
        <div class="max-w-7xl mx-auto">
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <div>
                        <a href="{{ route('admin.sub-admin') }}"  class="back-btn">
                            <p>
                            <x-icons name="back"/>
                                <span>Back</span>
                            </p>
                        </a>
                        <h1 class="text-lg font-medium main-heading mt-4">
                        Mass Message
                        </h1>
                        <p class="sub-heading">{{ __('Send messages to business admin and sub-admin users.')}}</p>
                    </div>
                </div>
            </div>

            <div class="white-box">
                <livewire:admin.mass-message />
            </div>
        </div>
    </div>
@endsection
