<div
    x-data="{
        confirmDelete: false,
        modelId: null,
        expanded: false,
        selectedId: @entangle('selectedTemplate')
    }"
    @cancel="confirmDelete = false"
    @confirm-delete="$wire.deleteItem(modelId); confirmDelete = false"
    @refresh.window="$wire.$refresh()"
>
    <!-- Manage Checklist Box -->
    <div class="manage-checklist-box">
        @forelse($templates as $template)
            <div class="accordian-box">
                
                <!-- Accordian Header -->
                <div class="accordian-header">
                    <h2 class="title">
                        {{ $template->name }}
                    </h2>

                    <div class="flex items-center space-x-4">
                        <template x-if="selectedId === {{ $template->id }}">
                            <x-form.button
                                type="button"
                                wire:click="openAddModal({{ $template->id }})"
                                class="inline-flex items-center !px-[10px] !py-[0] !m-[0] !mr-[24px] !text-[16px] !font-[600] !text-[#242424]"
                                variant="link"
                            >
                                <span class="mr-1">+</span>
                                {{ __('business.checklist.add') }}
                            </x-form.button>
                        </template>

                        <button
                            x-on:click="selectedId = selectedId === {{ $template->id }} ? null : {{ $template->id }}"
                            class="cursor-pointer"
                        >
                            <div class="icon" :class="{ 'show': selectedId === {{ $template->id }} }">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6 9L12 15L18 9"
                                        stroke="#1E1E1E" stroke-width="2.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- Accordian Content -->
                <div
                    x-show="selectedId === {{ $template->id }}"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="accordian-contant"
                >
                    <livewire:business.checklists.checklist-items
                        :key="'checklist-'.$template->id"
                        :template-id="$template->id"
                        wire:init="loadChecklists"
                    />
                </div>

            </div> <!-- /.accordian-box -->
            @empty
                <div class="accordian-contant">
                    <div class="text-center text-gray-500 bg-white p-[20px] rounded-[4px]">
                        {{ __('business.checklist.no_template_types') }}
                    </div>
                </div>
        @endforelse
    </div> <!-- /.manage-checklist-box -->

     <!-- Add Item Modal -->
    <div x-data="{ showAddModal: @entangle('showAddModal') }" @keydown.escape.window="$wire.closeAddModal()">
        <div
            x-show="showAddModal"
            x-cloak
            class="relative z-10"
            aria-labelledby="modal-title"
            role="dialog"
            aria-modal="true">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-gray-500/75 transition-opacity"></div>

            <!-- Modal Dialog -->
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                        <div class="px-6 py-4">
                            <!-- Modal Header -->
                            <div class="text-center mb-6">
                                <div class="mx-auto flex items-center justify-center mb-[20px]">
                                    <svg width="75" height="75" viewBox="0 0 75 75" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect x="0.308105" y="0.604492" width="74" height="74" rx="37" fill="#EFF6FF" />
                                        <path d="M33.948 22.1993V29.3298C33.948 29.6621 33.948 29.8282 33.8924 29.9604C33.84 30.085 33.7705 30.1755 33.66 30.2633C33.5429 30.3563 33.3532 30.4196 32.9737 30.5462C28.1502 32.1555 24.708 36.3737 24.708 41.3242C24.708 47.6617 30.3492 52.7992 37.308 52.7992C44.2668 52.7992 49.908 47.6617 49.908 41.3242C49.908 36.3737 46.4658 32.1555 41.6423 30.5462C41.2628 30.4196 41.0731 30.3563 40.956 30.2633C40.8455 30.1755 40.7761 30.085 40.7236 29.9604C40.668 29.8282 40.668 29.6621 40.668 29.3298V22.1993M31.428 22.1992H43.188" stroke="#0D44EA" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>

                                </div>
                                <h3 class="font-[700] text-[#4C4C4C] text-[32px]">
                                    {{ __('business.checklist.add_checklist') }}
                                </h3>
                                <p class="font-[400] text-[#4B5563] text-[16px] mt-[8px]">
                                    {{ __('business.checklist.add_tasks_subtitle') }}
                                </p>
                            </div>

                            <!-- Input Field -->
                            <div class="mt-[20px] flex flex-col max-w-[446px] mb-[16px] justify-center mx-auto">
                                <label for="newItemText" class="label-box text-left">
                                    {{ __('business.checklist.enter_task') }}
                                </label>
                                <x-form.textarea
                                    class="mt-2 text-box"
                                    name="newItemText"
                                    wire:model="newItemText"
                                    :placeholder="__('business.checklist.task_placeholder')"
                                    maxlength="255"
                                    row="2"
                                />
                                @error('newItemText')
                                    <div class="error-message-box newItemText">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Modal Buttons -->
                            <div class="flex justify-center justify-center gap-[24px]">
                                <x-form.button
                                    type="button"
                                    wire:click="closeAddModal"
                                    variant="outline"
                                    class="btnt-box outlined">
                                    {{ __('common.form.cancel') }}
                                </x-form.button>
                                <x-form.button
                                    type="button"
                                    wire:click="saveNewItem"
                                    variant="primary"
                                    class="btn-box btn">
                                    {{ __('business.checklist.add_task') }}
                                </x-form.button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div x-cloak x-show="confirmDelete">
        <x-confirm.confirm-delete
            wire:key="delete-modal"
            :message="__('business.checklist.confirm_delete_message')"
            btnConfirm="{{ __('business.checklist.delete_btn') }}"
            btnCancel="{{ __('business.checklist.cancel_btn') }}"
        />
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
