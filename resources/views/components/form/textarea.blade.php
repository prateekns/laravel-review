@props([
    'name' => '',
    'id' => '',
    'label' => null,
    'value' => null,
    'rows' => 3
])

@if ($label)
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700">{{ $label }}</label>
@endif

<textarea
    name="{{ $name }}"
    id="{{ $id ? $id : $name }}"
    rows="{{ $rows }}"
    {{ $attributes->merge(['class' => 'input-box']) }}
>{{ old($name, $value) }}</textarea>
