<div x-data="{ ...templateForm(),
        showConfirm: false,
        handleConfirm() {
            $wire.handleConfirm();
            this.showConfirm = false;
        },
        handleCancel() {
            $wire.handleCancel();
            this.showConfirm = false;
        }
    }"
    @close-cancel.window="handleCancel"
    @close-confirm.window="handleConfirm">
    <div class="white-box">
        <div class="manage-template-heading">
            <p class="m-0">{{ __('business.templates.edit_title') }}</p>
        </div>

        @if (session()->has('notification'))
            <div class="mb-4">
                <x-notification-alert type="{{ session('notificationType', 'success') }}" :message="session('notification')" />
            </div>
        @endif
        <form
            x-ref="form"
            @submit.prevent="submitEditForm($wire)"
            class="template-box"
            x-on:submit="if(showConfirm) $event.preventDefault();">
            <div class="w-full min-[991px]:w-[324px]">
                <x-form.label for="name" required>{{ __('business.templates.new_name') }}</x-form.label>
                <x-form.textarea
                    rows="2"
                    class="mt-[6px] text-box"
                    id="name"
                    x-ref="name"
                    x-model="name"
                    wire:model.defer="name"
                    placeholder="{{ __('business.templates.enter_name') }}"
                    maxlength="100" />
                <template x-if="errors.name">
                    <span class="error-message-box" x-text="errors.name"></span>
                </template>
                @error('name')
                    <span class="error-message-box">{{ $message }}</span>
                @enderror
            </div>
            <div class="w-full min-[991px]:w-[324px]">
                <x-form.label for="description" required>{{ __('business.templates.description') }}</x-form.label>
                <x-form.textarea
                    id="description"
                    x-ref="description"
                    x-model="description"
                    wire:model.defer="description"
                    placeholder="{{ __('business.templates.enter_description') }}"
                    maxlength="1200"
                    rows="4"
                    class="textarea manage-input-box mt-[6px] text-box !w-full" />
                <template x-if="errors.description">
                    <span class="error-message-box" x-text="errors.description"></span>
                </template>
                @error('description')
                    <span class="error-message-box">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-[24px]">
                <x-form.label>{{ __('business.customer.status.label') }}</x-form.label>
                <div class="flex items-center gap-[17px] mt-3">
                    <div class="custom-radio-box">
                        <input type="radio" wire:model.defer="is_active" value="1" class="mr-2" id="status_active" @change="showConfirm=false">
                        <label for="status_active"
                            class="mr-2 text-gray-500">{{ __('business.customer.status.active') }}</label>
                    </div>
                    <div class="custom-radio-box">
                        <input type="radio" wire:model.defer="is_active" value="0" class="mr-2" id="status_inactive" @change="showConfirm=true">
                        <label for="status_inactive"
                            class="mr-2 text-gray-500">{{ __('business.customer.status.inactive') }}</label>
                    </div>
                </div>
                @error('is_active') <span class="error-message-box">{{ $message }}</span> @enderror
            </div>
            
            <div class="flex gap-[24px] max-[767px]:flex-col max-[767px]:gap-[16px]">
                <x-form.button type="submit"
                    class="btn-box btn">{{ __('business.templates.update_btn') }}</x-form.button>
                <x-form.button type="button" class="btn-box outlined"
                    wire:click="cancelEdit">{{ __('business.templates.cancel_btn') }}</x-form.button>
            </div>
        </form>
      @include('components.confirm.confirm-modal', [
          'title' => __('business.customer.confirm_deactivate'),
          'description' => __('business.templates.confirm_deactivate_message'),
      ])
    </div>
</div>
