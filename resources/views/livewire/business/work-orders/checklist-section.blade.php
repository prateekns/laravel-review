<div
    x-data="{
        open: true,
        showAddModal: false,
        newItem: '',
        errorMessage: '',
        clearModalData() {
            this.newItem = '';
            this.errorMessage = '';
        },
        closeModal() {
            this.showAddModal = false;
            this.clearModalData();
        },
        addChecklistItem() {
            if (!this.newItem.trim()) {
                this.errorMessage = '{{ __("business.customer.validation.required") }}';
                return;
            }

            if (this.newItem.trim().length > 255) {
                this.errorMessage = '{{ __("business.work_orders.validation.max_length") }}';
                return;
            }

            // Check for duplicates
            const existingItems = document.querySelectorAll('.template-item');
            for (let item of existingItems) {
                if (item.querySelector('.flex-grow').textContent.trim().toLowerCase() === this.newItem.trim().toLowerCase()) {
                    this.errorMessage = '{{ __("business.work_orders.validation.checklist_item_unique") }}';
                    return;
                }
            }

            $wire.addChecklistItem({ text: this.newItem.trim() });
            this.clearModalData();
            this.showAddModal = false;
        }
    }"
>
    <div>
        <div class="flex gap-[10px] items-center">
            <h4 class="text-[20px] font-[600] text-[#ffffff] w-full bg-[#2563EB] rounded-[8px] p-[12px] mb-[20px]">{{ __('business.checklist.checklist') }}</h4>
        </div>
    </div>

    <div class="w-full !p-[0]">
        <!-- Template Checklist Items -->
        <div id="template-checklist-items" class="mb-6" wire:loading.class="opacity-50">
            <div class="flex justify-between items-center mb-[6px]">
                <h4 class="font-[600] text-[16px] text-[#000000] break-auto-phrase">{{ __('business.work_orders.mandatory_checklist') }}</h4>
               
                @if($templateId)
                    <x-form.button
                        type="button"
                        @click="showAddModal = true"
                        class="inline-flex items-center !p-[0] !m-[0] !text-[16px] !font-[600] !text-[#086DF1] whitespace-nowrap"
                        variant="link"
                    >
                        <span class="mr-1 text-[16px] font-[600] text-[#086DF1]">+</span>
                        {{ __('business.checklist.add') }}
                    </x-form.button>
                @endif
                
            </div>
            <div class="mb-[24px]">
                <p class="text-[12px] font-[600] text-[#6B7280] max-w-[390px] break-auto-phrase">{{ __('business.work_orders.mandatory_checklist_description') }}</p>
            </div>
                
            <!-- Loading State -->
            <div wire:loading wire:target="loadTemplateChecklist" class="text-center py-4">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-500"></div>
            </div>
                        
            <!-- Checklist Items Container -->
            <div class="space-y-2" wire:loading.remove wire:target="loadTemplateChecklist">
                @if($checklistItems && count($checklistItems) > 0)
                    @foreach($checklistItems as $index => $item)
                        <div class="flex items-center space-x-[13px] template-item" wire:key="checklist-item-{{ $index }}">
                            <div class="flex items-center space-x-[8px] input-checkbox">
                                <input type="checkbox"
                                    name="checklist_items[{{ $index }}][is_visible]"
                                    class=""
                                    {{ $item['is_visible'] ? 'checked' : '' }}>
                            </div>
                            <input type="hidden" name="checklist_items[{{ $index }}][is_custom]" value="{{ $item['is_custom'] ? '1' : '0' }}">
                            <input type="hidden" name="checklist_items[{{ $index }}][is_default]" value="{{ $item['is_default'] ? '1' : '0' }}">
                            <input type="hidden" name="checklist_items[{{ $index }}][sort_order]" value="{{ $index }}">
                            <input type="hidden" name="checklist_items[{{ $index }}][item_text]" value="{{ $item['item_text'] }}">
                            <div class="flex-grow text-[16px] font-[400] text-[#212529] break-words">
                                {{ $item['item_text'] }}
                            </div>
                            @if($item['is_custom'])
                                {{-- removeCustomItem --}}
                            @endif
                        </div>
                    @endforeach
                @else
                    <p class="text-gray-500">{{ __('business.work_orders.no_template_selected') }}</p>
                @endif
            </div>
        </div>
       
        <div
            x-show="showAddModal"
            class="relative z-10"
            @click.self="closeModal()"
            class="relative z-10"
            aria-labelledby="modal-title"
            role="dialog"
            aria-modal="true"
            x-cloak>
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
                                <x-form.text class="mt-2"
                                        x-model="newItem"
                                        name="newItemText"
                                        @keydown.enter.prevent="addChecklistItem()"
                                        :placeholder="__('business.checklist.task_placeholder')"
                                        :error="$errors->first('newItemText')" />
                                        <div x-show="errorMessage" x-text="errorMessage" class="error-message-box"></div>
                            </div>

                            <!-- Modal Buttons -->
                            <div class="flex justify-center justify-center gap-[24px]">
                                <x-form.button
                                    type="button"
                                    @click="closeModal()"
                                    variant="outline"
                                    class="btnt-box outlined">
                                    {{ __('common.form.cancel') }}
                                </x-form.button>
                                <x-form.button
                                    type="button"
                                    @click="addChecklistItem()"
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
</div>
