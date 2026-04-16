<div x-data="{
    confirmDelete: false,
    modelId: null,
    init() {
        Livewire.on('item-ready-for-delete', () => {
            this.confirmDelete = true;
        });

        Livewire.on('refresh-items', () => {
            this.confirmDelete = false;
            this.modelId = null;
        });
    }
}" @cancelled="confirmDelete = false"
    @confirmed="$wire.deleteItem(modelId); confirmDelete = false">
    <!-- Add Items Sold Section -->
    <div class="white-box shadow-sm py-[24px] px-[20px] rounded-[16px] !mt-0">
        <div x-data="{ open: true }">
            <div @click="open = !open" class="accordian-header"
                :class="open ? 'manage-template-heading cursor-pointer show' : 'manage-template-heading cursor-pointer'">
                <div class="gap-[10px] items-center">
                    <p class="m-0">{{ __('business.items_sold.add_section_title') }}</p>
                    <span class="">{{ __('business.items_sold.add_section_title_description')}}</span>
                </div>
                <div class="icon" :class="{ 'show': open }" style="transition: transform 0.3s ease">
                    <svg width="16" height="10" viewBox="0 0 16 10" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M2 2L8 8L14 2" stroke="#0D44EA" stroke-width="2.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </div>
            </div>

            <div x-show="open" x-transition class="accordian-contant w-full !mt-[30px] !p-[0]">
                <div>
                    <form wire:submit="save">
                        <div class="mb-[30px]">
                            <label for="itemName" class="label-box">
                                {{ __('business.work_orders.item_name') }}<span class="text-red-500">*</span>
                            </label>
                            <input type="text" wire:model="itemName" id="itemName"
                                class="input-box min-[641px]:max-w-[324px] max-[640px]:max-w-full mt-2"
                                placeholder="{{ __('business.items_sold.enter_item_name') }}">
                            @error('itemName')
                                <div class="error-message-box itemName">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="flex gap-4 max-[767px]:flex-col">
                            <button type="submit"
                                class="px-6 py-2 bg-[#0D44EA] text-white rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                {{ __('business.items_sold.save_item') }}
                            </button>
                            <button type="button" wire:click="clear"
                                class="px-6 py-2 border border-[#0D44EA] text-[#0D44EA] rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                {{ __('business.templates.clear_btn') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Item List Table Section -->
    <div class="white-box shadow-sm py-[24px] px-[20px] rounded-[16px] mt-6">
        <div x-data="{ open: true }">
            <div @click="open = !open" class="accordian-header"
                :class="open ? 'manage-template-heading cursor-pointer show' : 'manage-template-heading cursor-pointer'">
                <div class="flex gap-[10px] items-center">
                    <p class="m-0">{{ __('business.items_sold.list_section_title') }}</p>
                </div>
                <div class="icon" :class="{ 'show': open }" style="transition: transform 0.3s ease">
                    <svg width="16" height="10" viewBox="0 0 16 10" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M2 2L8 8L14 2" stroke="#0D44EA" stroke-width="2.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </div>
            </div>

            <div x-show="open" x-transition class="accordian-contant w-full !mt-[30px] !p-[0]">
                <div>
                    @if ($items->isEmpty())
                        <div class="text-center py-8 text-gray-500">
                            {{ __('business.items_sold.no_items') }}
                        </div>
                    @else
                        <div class="table-box !p-0 !mt-[30px]">
                            <table class="min-w-full" aria-describedby="Items Sold">
                                <thead class="!border-0">
                                    <tr>
                                        <th scope="col" class="!text-[16px]">
                                            {{ __('business.work_orders.item_name') }}
                                        </th>
                                        <th scope="col" class="!text-right !text-[16px]">
                                            {{ __('business.work_orders.table.action') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($items as $item)
                                        <tr>
                                            <td data-label="Item Name" class="!text-[#4B5563] !text-[16px] !font-[500]">
                                                @if ($editingItemId === $item->id)
                                                    <div
                                                        class="flex flex-col gap-2 min-[768px]:w-[394px] max-[767px]:w-full">
                                                        <div
                                                            class="flex items-center gap-[22px] w-full max-[767px]:flex-col max-[767px]:items-start">
                                                            <div class="flex-1">
                                                                <input type="text" wire:model.live="editingItemName"
                                                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                                                @error('editingItemName')
                                                                    <div class="error-message-box mt-1">{{ $message }}
                                                                    </div>
                                                                @enderror
                                                            </div>
                                                            <div class="flex items-center gap-[16px]">
                                                                <button wire:click="updateItem"
                                                                    class="text-[16px] font-[600] text-[#242424] underline">
                                                                    {{ __('common.form.save') }}
                                                                </button>
                                                                <button wire:click="cancelEdit"
                                                                    class="text-[16px] font-[600] text-[#7e7e7e] underline">
                                                                    {{ __('common.form.cancel') }}
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    {{ $item->name }}
                                                @endif
                                            </td>
                                            <td class="flex gap-[24px] justify-end table-actions !text-right">
                                                @if ($editingItemId !== $item->id)
                                                    <button
                                                        wire:click="startEdit({{ $item->id }})"
                                                        title="{{ __('common.form.edit') }}"
                                                        class="cursor-pointer">
                                                        <svg width="22" height="22" viewBox="0 0 22 22"
                                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path
                                                                d="M20 20.0003H12M1.5 20.5003L7.04927 18.366C7.40421 18.2295 7.58168 18.1612 7.74772 18.0721C7.8952 17.9929 8.0358 17.9015 8.16804 17.7989C8.31692 17.6834 8.45137 17.5489 8.72028 17.28L20 6.0003C21.1046 4.89574 21.1046 3.10487 20 2.0003C18.8955 0.895734 17.1046 0.895732 16 2.0003L4.72028 13.28C4.45138 13.5489 4.31692 13.6834 4.20139 13.8323C4.09877 13.9645 4.0074 14.1051 3.92823 14.2526C3.83911 14.4186 3.77085 14.5961 3.63433 14.951L1.5 20.5003ZM1.5 20.5003L3.55812 15.1493C3.7054 14.7663 3.77903 14.5749 3.90534 14.4872C4.01572 14.4105 4.1523 14.3816 4.2843 14.4068C4.43533 14.4356 4.58038 14.5807 4.87048 14.8708L7.12957 17.1299C7.41967 17.4199 7.56472 17.565 7.59356 17.716C7.61877 17.848 7.58979 17.9846 7.51314 18.095C7.42545 18.2213 7.23399 18.2949 6.85107 18.4422L1.5 20.5003Z"
                                                                stroke="black" stroke-width="2" stroke-linecap="round"
                                                                stroke-linejoin="round" />
                                                        </svg>


                                                    </button>
                                                @endif
                                                <button type="button" wire:click="confirmDelete({{ $item->id }})"
                                                    wire:loading.attr="disabled"
                                                    x-on:click="modelId = {{ $item->id }}" class="cursor-pointer"
                                                    title="{{ __('common.form.delete') }}">
                                                    <svg width="20" height="20" viewBox="0 0 20 20"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M7 1H13M1 4H19M17 4L16.2987 14.5193C16.1935 16.0975 16.1409 16.8867 15.8 17.485C15.4999 18.0118 15.0472 18.4353 14.5017 18.6997C13.882 19 13.0911 19 11.5093 19H8.49065C6.90891 19 6.11803 19 5.49834 18.6997C4.95276 18.4353 4.50009 18.0118 4.19998 17.485C3.85911 16.8867 3.8065 16.0975 3.70129 14.5193L3 4M8 8.5V13.5M12 8.5V13.5"
                                                            stroke="black" stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </svg>

                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div x-cloak x-show="confirmDelete">
        <x-confirm.confirm-delete
            wire:key="delete-modal"
            :message="__('business.items_sold.confirm_delete', ['name' => $itemToDelete ? $itemToDelete->name : ''])"
            @confirmed="$wire.deleteItem(modelId)"
            @cancelled="confirmDelete = false"
        />
    </div>

    <!-- Notifications -->
    @if (session()->has('success'))
        <div class="fixed bottom-4 right-4 z-50" x-data="{ show: true }" x-init="setTimeout(() => show = false, 8000)" x-show="show">
            <x-notification-alert type="success" :message="session('success')" />
        </div>
    @endif

    @if (session()->has('error'))
        <div class="fixed bottom-4 right-4 z-50" x-data="{ show: true }" x-init="setTimeout(() => show = false, 8000)" x-show="show">
            <x-notification-alert type="error" :message="session('error')" />
        </div>
    @endif
</div>
