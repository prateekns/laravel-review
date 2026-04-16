@props([
    'name' => '',
    'placeholder' => '',
    'options' => [],
    'selected' => '',
    'wireChange' => null,
    'wireModel' => null,
])

<div >
    <select
        @if($wireChange) wire:change="{{ $wireChange }}($event.target.value)" @endif
        @if($wireModel) wire:model="{{ $wireModel }}" @endif
        name="{{ $name }}"
        id="{{ $name }}"
        class="input-box"
    >
        <option value="">{{ $placeholder }}</option>
        @foreach ($options as $key => $value)
            <option value="{{ $key }}" {{ $selected == $key ? 'selected' : '' }}>{{ $value }}</option>
        @endforeach
    </select>
</div>
