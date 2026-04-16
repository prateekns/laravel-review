@props(['label' => null,'for' => null, 'required' => false])
<label for="{{ $for ?? $label }}" {{ $attributes->merge(['class' => 'label-box']) }}>
    {{ $label ?? $slot }}
    @if ($required)
        <span class="">*</span>
    @endif
</label>
