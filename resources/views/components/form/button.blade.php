@props([
    'type' => 'submit',
    'label' => '',
    'wireTarget' => null,
    'click' => null,
])

<button type="{{ $type }}" {{ $attributes->merge(['class' => 'btn-box cursor-pointer']) }} @if($click) @click="{{ $click }}" @endif x-cloak>
    @if ($label)
        {{ $label }}
    @else
        {{ $slot }}
    @endif
    @if ($wireTarget)
            <span
                wire:loading
                wire:target="{{ $wireTarget }}"
                class="ml-2 animate-spin rounded-full h-4 w-4 border-b-2 border-white"
            ></span>
        @endif
</button>
