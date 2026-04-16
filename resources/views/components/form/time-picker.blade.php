@props([
    'name' => '',
    'id' => '',
    'value' => null,
    'xModel' => null,
    'xRef' => null,
    'wireModel' => null,
    'inputClasses' => '',
    'class' => ''
])
@php
$inputClasses = 'input-box';
@endphp
<div
    x-data="{ hasError: false }"
    x-init="() => {
    const initial = '{{ old($name, $value) }}';
    const fp = flatpickr($refs.input, {
        enableTime: true,
        noCalendar: true,
        enableSeconds: false,
        // Submitted value matches stored format HH:MM:SS
        dateFormat: 'H:i:s',
        time_24hr: false,
        // Display value in 12h using alt input
        altInput: true,
        altFormat: 'h:i K',
        minuteIncrement: 5,
        defaultDate: initial || null,
        altInputType: 'time',
        allowInput: false,
        altInputClass: 'input-box custom-time-icon {{$inputClasses}} {{ $class ?? ''}}',
        onReady: function(selectedDates, dateStr, instance) {
            const timeInputs = instance.timeContainer.querySelectorAll('input');
            timeInputs.forEach(input => {
                input.setAttribute('readonly', 'readonly'); // block typing
            });
            // Watch for Alpine error state changes
            $watch('hasError', value => {
                if (value) {
                    instance.altInput.classList.add('error-message-border');
                } else {
                    instance.altInput.classList.remove('error-message-border');
                }
            });

            // Check for Laravel validation errors
            @error($name)
                instance.altInput.classList.add('error-message-border');
            @enderror
        }
    });
    if (!fp.selectedDates.length && initial) {
        fp.setDate(initial, true, 'H:i:s');
    }
}"
    x-effect="hasError = errors?.{{ $name }} ? true : false"
>
    <input
        x-ref="input"
        type="time"
        name="{{ $name ?? 'time' }}"
        value="{{ old($name, $value) }}"
        id="{{ $id ?? $name ?? 'time' }}"
        class="input-box custom-time-icon {{ $inputClasses ?? '' }} {{ $class ?? ''}}"
        {{ $attributes->merge(['class' => $inputClasses]) }}
        {{ $xRef ? "x-ref=$xRef" : ''}}
        {{ $xModel ? "x-model=$xModel" : '' }}
        {{ $wireModel ? "wire:model=$wireModel" : '' }}
        {{ $attributes }} />
</div>
