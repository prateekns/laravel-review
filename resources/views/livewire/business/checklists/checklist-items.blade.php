<div
    x-data="{
        confirmDelete: false,
        modelId: null
    }"
    @cancelled="confirmDelete = false"
    @confirmed="$wire.deleteItem(modelId); confirmDelete = false"
    @refresh.window="$wire.$refresh()">
    <div class="checklist-list-box">
        @forelse($checklists as $item)
        <div class="flex">
            <div class="flex-1 flex  space-x-[8px] input-checkbox items-center">
                <input
                    type="checkbox"
                    wire:model="tempVisibility.{{ $item->id }}"
                    title="{{ __('business.checklist.toggle_visibility') }}"
                >

                @if($editingItemId === $item->id)
                <div class="checklist-edit-box">
                    <div class="flex-1 m-w-[446px] max-[767px]:w-full max-[767px]:pr-2">
                        <x-form.textarea
                            wire:model.live="editItemText"
                            wire:keydown.enter="saveEdit"
                            class="w-full text-box"
                            rows="3"
                            :placeholder="__('business.checklist.edit_item_placeholder')"
                            maxlength="255" />
                        @error('editItemText')
                        <div class="error-message-box editItemText">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="flex items-center space-x-2 max-[767px]:w-full">
                        <x-form.button
                            type="button"
                            wire:click="saveEdit"
                            variant="primary"
                            class="btn-box btn">
                            {{ __('common.form.save') }}
                        </x-form.button>
                        <x-form.button
                            type="button"
                            wire:click="cancelEdit"
                            variant="outline"
                            class="btn-box outlined">
                            {{ __('common.form.cancel') }}
                        </x-form.button>
                    </div>
                </div>
                @else
                <span class="font-[400] text-[16px] text-[#212529] whitespace-normal break-words leading-[21px] pr-2 max-w-[526px]">{{ $item->item_text }}</span>
                @endif
            </div>

            <div class="flex items-center space-x-2">

                <div class="flex gap-[24px]">
                    @if($editingItemId !== $item->id)
                    <button
                        type="button"
                        wire:click="startEditing({{ $item->id }}, '{{ $item->item_text }}')"
                        class="cursor-pointer"
                        title="{{ __('common.form.edit') }}">
                        <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20 20.0003H12M1.5 20.5003L7.04927 18.366C7.40421 18.2295 7.58168 18.1612 7.74772 18.0721C7.8952 17.9929 8.0358 17.9015 8.16804 17.7989C8.31692 17.6834 8.45137 17.5489 8.72028 17.28L20 6.0003C21.1046 4.89574 21.1046 3.10487 20 2.0003C18.8955 0.895734 17.1046 0.895732 16 2.0003L4.72028 13.28C4.45138 13.5489 4.31692 13.6834 4.20139 13.8323C4.09877 13.9645 4.0074 14.1051 3.92823 14.2526C3.83911 14.4186 3.77085 14.5961 3.63433 14.951L1.5 20.5003ZM1.5 20.5003L3.55812 15.1493C3.7054 14.7663 3.77903 14.5749 3.90534 14.4872C4.01572 14.4105 4.1523 14.3816 4.2843 14.4068C4.43533 14.4356 4.58038 14.5807 4.87048 14.8708L7.12957 17.1299C7.41967 17.4199 7.56472 17.565 7.59356 17.716C7.61877 17.848 7.58979 17.9846 7.51314 18.095C7.42545 18.2213 7.23399 18.2949 6.85107 18.4422L1.5 20.5003Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>

                    </button>
                    @endif
                    <button
                        type="button"
                        @click="modelId = {{ $item->id }}; confirmDelete = true"
                        class="cursor-pointer"
                        title="{{ __('common.form.delete') }}">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M7 1H13M1 4H19M17 4L16.2987 14.5193C16.1935 16.0975 16.1409 16.8867 15.8 17.485C15.4999 18.0118 15.0472 18.4353 14.5017 18.6997C13.882 19 13.0911 19 11.5093 19H8.49065C6.90891 19 6.11803 19 5.49834 18.6997C4.95276 18.4353 4.50009 18.0118 4.19998 17.485C3.85911 16.8867 3.8065 16.0975 3.70129 14.5193L3 4M8 8.5V13.5M12 8.5V13.5" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>

                    </button>
                </div>

            </div>
        </div>
        @empty
        <div class="px-6 py-4 text-center text-gray-500">
            {{ __('business.checklist.no_items') }}
        </div>
        @endforelse
    </div>

    <!-- Save/Cancel Buttons -->
    @if($checklists->count() > 0)
        <div class="mt-[30px] flex justify-start gap-[24px] max-[767px]:flex-col">
            <x-form.button
                type="button"
                wire:click="saveVisibilityChanges"
                variant="primary"
                class="btn-box btn">
                {{ __('common.form.save') }}
            </x-form.button>
            <x-form.button
                type="button"
                wire:click="cancelVisibilityChanges"
                variant="outline"
                class="btn-box outlined">
                {{ __('common.form.cancel') }}
            </x-form.button>
        </div>
    @endif
    <!-- Delete Confirmation Modal -->
    <div x-cloak x-show="confirmDelete">
        <x-confirm.confirm-delete
            wire:key="delete-modal"
            :message="__('business.checklist.confirm_delete_message')"
            btnConfirm="{{ __('business.checklist.delete_btn') }}"
            btnCancel="{{ __('business.checklist.cancel_btn') }}" />
    </div>

    <!-- Notifications -->
    @if(session()->has('success'))
    <div wire:key="notification-{{ now() }}">
        <x-notification-alert type="success" :message="session('success')" />
    </div>
    @endif

    @if(session()->has('error'))
    <div wire:key="notification-{{ now() }}">
        <x-notification-alert type="error" :message="session('error')" />
    </div>
    @endif
</div>
