@props([
    'name',
    'field',
    'range',
    'value',
    'unit' => '',
    'editField',
    'editValues'
])

<tr>
    <td data-label="Chemical Name/ Value">
        {{ $name }}
    </td>
    <td data-label="Range">{{ $range }}</td>
    <td data-label="Ideal/ Target" class="max-[767px]:!px-[0px] max-[767px]:!pb-[0px]">
        @if($editField === $field)
            <div class="flex min-[641px]:items-start max-[640px]:items-end flex-col ">
            <div class="relative w-[202px] max-[640px]:w-[150px] max-[640px]:float-right">
                <input
                    type="text"
                    class="w-full h-[36px] rounded-[4px] border border-[#C5C5C5] px-[12px] py-[8px] pr-[55px] bg-white @error('editValues.' . $field) border-red-500 @enderror"
                    wire:model.live="editValues.{{ $field }}"
                    step="0.01"
                    min="0"
                    max="9999.99"
                    inputmode="decimal"
                    placeholder="0.00"
                >
                <div class="absolute inset-y-0 right-5 flex items-center  pointer-events-none">
                    <span class="text-[#4C4C4C] text-[16px] font-[400]">{{ $unit }}</span>
                </div>
               
            </div>
             @error("editValues.$field")
                <p class="error-message-box max-w-[202px] max-[640px]:max-w-[100%]  text-right inline-block">{{ $message }}</p>
            @enderror
            </div>
            
        @else
            <span class="pr-[12px]">{{ is_numeric($value) ? rtrim(rtrim(number_format($value, 2, '.', ''), '0'), '.') : $value }} {{ $unit }}</span>
        @endif
    </td>
    <td class="table-actions">
        @if($editField === $field)
            <div class="flex gap-2">
                <span wire:click="updateChemical({{ $field }})" class="cursor-pointer save-chemical">
                    <x-icons name="check-icon" />
                </span>
                <span wire:click="cancelEdit" class="cursor-pointer cancel-chemical">
                    <x-icons name="close-icon" />
                </span>
            </div>
        @else
            <span wire:click="startEdit({{ $field }}, {{ $value }})" class="edit-chemical cursor-pointer inline-block ml-[16px]">
                <x-icons name="edit-icon" />
            </span>
        @endif
    </td>
</tr>
