@props([
    'name',
    'value',
    'label' => '',
    'checked' => false,
    'disabled' => false,
    'class' => '',
    'id' => null
])

@php
    $id = $id ?? $name . '_' . $value;
@endphp

<label class="flex items-center {{ $class }}">
    <input
        type="radio"
        name="{{ $name }}"
        value="{{ $value }}"
        id="{{ $id }}"
        {{ $checked ? 'checked' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        {{ $attributes->merge(['class' => 'form-radio h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500']) }}
    >
    <span class="ml-2 text-sm font-medium text-gray-700">{{ $label }}</span>
</label>
