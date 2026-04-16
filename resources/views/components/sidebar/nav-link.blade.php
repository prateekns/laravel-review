@props(['active' => false])

@php
$classes = $active
    ? 'active'
    : '';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    @if (isset($icon))
        <span class="">
            {{ $icon }}
        </span>
    @endif
    {{ $slot }}
</a>
