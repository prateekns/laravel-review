@extends('layouts.business.app')

@section('title', __('Help'))

@section('content')
<div
    x-data="{
        showToast: false,
        showSuccess: false,
        showError: false,
        successMessage: '',
        errorMessage: ''
    }"
    @notify-success.window="
        successMessage = $event.detail[0].message;
        showSuccess = true;
        showToast = true;
        setTimeout(() => {
            showToast = false;
            showSuccess = false;
        }, 5000);
    "
    @notify-error.window="
        errorMessage = $event.detail[0].message;
        showError = true;
        showToast = true;
        setTimeout(() => {
            showToast = false;
            showError = false;
        }, 5000);
    "
>
    <div class="container-fluid mx-auto">
        <div class="flex justify-between  flex-col  mb-[24px]">
            <h1 class="main-heading">{{ __('Chemical List') }}</h1>
            <p class="sub-heading">{{ __('Grab control of your chemicals, your pool service perfected!') }}</p>
        </div>
        <livewire:business.chemical />
    </div>
    <div x-show="showSuccess" x-cloak>
        <x-toast type="success" message="successMessage" x-show="successMessage"/>
    </div>
    <div x-show="showError" x-cloak>
        <x-toast type="error"  message="errorMessage" x-show="errorMessage"/>
    </div>
</div>
@endsection
