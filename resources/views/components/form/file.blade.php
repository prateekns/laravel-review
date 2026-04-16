@props([
    'name',
    'id' => null,
    'accept' => '',
    'multiple' => false,
    'disabled' => false
])

<div class="flex items-center justify-center w-full">
    <label
        for="{{ $id ?? $name }}"
        class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed rounded-lg cursor-pointer transition-colors duration-200 {{ $disabled ? 'bg-gray-100 border-gray-400' : 'border-gray-300 bg-gray-50 hover:bg-gray-100' }}"
    >
        <div class="flex flex-col items-center justify-center pt-5 pb-6">
            <svg class="w-8 h-8 mb-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
            </svg>
            <p class="mb-2 text-sm text-gray-500">
                <span class="font-semibold">{{ __('business.work_orders.upload_photo_drag_and_drop') }}</span>
            </p>
            <p class="text-xs text-gray-500">
                {{ __('business.work_orders.image_requirements') }}
            </p>
        </div>
        <input
            type="file"
            name="{{ $name }}"
            id="{{ $id ?? $name }}"
            {{ $multiple ? 'multiple' : '' }}
            {{ $disabled ? 'disabled' : '' }}
            {{ $accept ? "accept=$accept" : '' }}
            {{ $attributes->merge(['class' => 'hidden']) }}
        >
    </label>
</div>
