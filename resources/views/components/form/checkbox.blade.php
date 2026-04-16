@props([
    'id' => '',
    'name' => '',
    'value' => '',
    'checked' => false,
    'label' => null,
    'xModel' => null,
    'wireModel' => null,
    'errorBag' => 'default'
])

@php
    $inputClasses = '';
@endphp

<div class="flex h-6 shrink-0 items-center">
    <div class="group grid size-4 grid-cols-1 input-checkbox">
        <input
            id="{{ $id }}"
            name="{{$name}}"
            type="checkbox"
            value="{{$value ?? ''}}"
            class="{{$inputClasses}}"
            {{ $xModel ? "x-model=$xModel" : '' }}
            {{ $wireModel ? "wire:model=$wireModel" : '' }}
            {{ $checked ? 'checked' : '' }}
            {{ $attributes }}
            >
    </div>
</div>
