@props([
    'link' => '',
    'label' => '',
])

@if($link)
    <a href="{{ $link }}" {{ $attributes->merge(['class' => 'route-link']) }}>
        {{ $label ?: $slot }}
    </a>
@else
    <button {{ $attributes->merge(['class' => 'route-link']) }}>
        {{ $label ?: $slot }}
    </button>
@endif
