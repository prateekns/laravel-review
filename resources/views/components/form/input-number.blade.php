@props([
    'name' => '',
    'id' => '',
    'value' => null,
    'xModel' => null,
    'xRef' => null,
    'wireModel' => null,
    'errorBag' => 'default'
])

<div class="relative w-full">
    <input
        type="number"
        name="{{ $name }}"
        id="{{ $name }}"
        value="{{ old($name, $value) }}"
        {{ $attributes->merge(['class' => 'input-box']) }}
        {{ $xRef ? "x-ref=$xRef" : ''}}
        {{ $xModel ? "x-model=$xModel" : '' }}
        {{ $wireModel ? "wire:model=$wireModel" : '' }}
    />
</div>
