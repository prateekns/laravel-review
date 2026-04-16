<div x-data="{
    confirmDelete: false,
    modelId: null
}"
    @cancelled.window="confirmDelete = false"
    @confirmed.window="$wire.deleteTemplate(modelId); confirmDelete = false">
    {{-- Add Template Card --}}
    <div class="white-box">
        <div class="manage-template-heading">
            <p class="m-0">{{ __('business.templates.add') }}</p>
        </div>
        @if ($notification)
        <div wire:key="notification-{{ now() }}">
            <x-notification-alert
                type="{{ $notificationType }}"
                :message="$notification"
                wire:click="clearNotification" />
        </div>
        @endif

        @if (session()->has('notificationMessage'))
        <div class="mb-4">
            <x-notification-alert
                type="{{ session('notificationType', 'success') }}"
                :message="session('notificationMessage')" />
        </div>
        @endif
        <form
            x-data="templateForm()"
            x-ref="form"
            @submit.prevent="submitForm($wire)"
            class="template-box">
            <div class="mb-[24px]">
                <x-form.label for="type" required>{{ __('business.templates.template_type') }}</x-form.label>
                <div class="flex items-center gap-[17px] mt-3">
                    <div class="custom-radio-box">
                        <input type="radio" wire:model.defer="type" x-model="type" value="WO" class="mr-2" id="type_work_order" :checked="true">
                        <label for="type_work_order"
                            class="mr-2 text-gray-500">{{ __('business.templates.work_order') }}</label>
                    </div>
                    <div class="custom-radio-box">
                        <input type="radio" wire:model.defer="type" x-model="type" value="MO" class="mr-2" id="type_maintenance">
                        <label for="type_maintenance"
                            class="mr-2 text-gray-500">{{ __('business.templates.maintenance') }}</label>
                    </div>
                </div>
            </div>
            <div class="w-full min-[991px]:w-[550px]">
                <x-form.label for="name" required>{{ __('business.templates.new_name') }}</x-form.label>
                <x-form.textarea
                    rows="2"
                    id="name"
                    x-ref="name"
                    x-model="name"
                    class="mt-[6px] text-box"
                    wire:model.defer="name"
                    placeholder="{{ __('business.templates.enter_name') }}"
                    maxlength="100" />
                <template x-if="errors.name">
                    <span class="error-message-box" x-text="errors.name"></span>
                </template>
                @error('name') <span class="error-message-box">{{ $message }}</span> @enderror
            </div>
            <div class="w-full min-[991px]:w-[550px]">
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
                @error('description') <span class="error-message-box">{{ $message }}</span> @enderror
            </div>
            <div class="flex gap-[24px] max-[767px]:flex-col max-[767px]:gap-[16px]">
                <x-form.button type="submit" class="btn-box btn">{{ __('business.templates.add_btn') }}</x-form.button>
                <x-form.button type="button" class="btn-box outlined" @click="clearForm">{{ __('business.templates.clear_btn') }}</x-form.button>
            </div>
        </form>
    </div>

    {{-- Existing Templates Card --}}
    <div class="white-box mt-[24px] accordian-box" x-data="{ open: true }">
        <div
            @click="open = !open"
            class="accordian-header !py-[12px]"
            :class="open ? 'manage-template-heading cursor-pointer show' : 'manage-template-heading cursor-pointer'">
            <p class="m-0">{{ __('business.templates.existing') }}</p>
            <div class="icon" :class="{ 'show': open }" style="transition: transform 0.3s ease">
                <svg width="16" height="10" viewBox="0 0 16 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M2 2L8 8L14 2" stroke="#0D44EA" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
        </div>
        <div x-show="open" x-transition class="accordian-contant table-box table-list-box w-full !mt-[7px] !p-[0]">
            <div class="table-box !px-0 !pb-0 !mt-[0px] !pt-0 whitespace-nowrap">
            <table class="min-w-full !border-separate border-spacing-y-[23px]" aria-describedby="Business Template Listing">
                <thead class="!border-b-0">
                    <tr>
                        <th scope="col" class="w-[8%] max-[767px]:w-[10%] max-[767px]:!text-left !py-0">{{ __('business.templates.template_type') }}</th>
                        <th scope="col" class="w-[28%] max-[767px]:w-[28%] max-[767px]:!text-left !py-0">{{ __('business.templates.name') }}</th>
                        <th scope="col" class="w-[40%] max-[767px]:w-[40%] max-[767px]:!text-left !py-0">{{ __('business.templates.template_description') }}</th>
                        <th scope="col" class="w-[10%] max-[767px]:w-[10%] max-[767px]:!text-left !py-0">{{ __('business.customer.status.label') }}</th>
                        <th scope="col" class="!text-center min-[1600px]:!text-right py-0">{{ __('business.templates.action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($templates as $template)
                    <tr class="{{ Str::kebab($template['type_label']) }}">
                        <td data-label="Type" class="min-[641px]:!text-left !text-[#4C4C4C]">
                            <span class="capitalize break-auto-phrase">{{ str_replace('_', ' ', $template['type_label']) }}</span>
                        </td>
                        <td data-label="Template Name" class="min-[641px]:!text-left break-words !text-[#4C4C4C]"> <span class="max-[640px]:w-[50%] max-[640px]:text-left max-[640px]:inline-flex">{{ $template['name'] }}</span></td>
                        <td data-label="Template Description" class="min-[641px]:!text-left !text-[#4C4C4C]">
                            <div x-data="{ expanded: false }" class="relative max-[640px]:max-w-[50%] max-[640px]:float-right max-[640px]:text-left">
                                <div class="relative break-words template-description" x-bind:class="expanded ? 'break-words' : 'description-truncate'">
                                    {{ $template['description'] }}
                                </div>
                                @if (strlen($template['description']) > 120)
                                    <button
                                        @click="expanded = !expanded"
                                        x-text="expanded ? '{{ __('business.templates.view_less') }}' : '{{ __('business.templates.view_more') }}'"
                                        class=" block mt-[6px] !underline font-[400] text-[#2563EB] text-[14px] leading-[18px] cursor-pointer">
                                    </button> @endif
                            </div>
                        </td>
                        <td data-label="Status" class="min-[641px]:!text-left status-td !text-[#4C4C4C]">
                            <span @class([
                                'max-[640px]:justify-end font-[400] !text-[16px] max-[640px]:!text-[14px]',
                                'badge',
                                'bg-success' => $template['is_active'],
                                'bg-warning' => !$template['is_active'],
                            ])>
                                {{ $template['is_active'] ? __('business.customer.status.active') : __('business.customer.status.inactive') }}
                            </span>
                        </td>
                        <td data-label="Actions" class="min-[1600px]:!text-right !text-[#4C4C4C]">
                            <div class="inline-flex gap-[24px] max-[380px]:gap-[14px]">
                                <x-form.link link="{{ route('business.checklists.index', ['template' => $template['id']]) }}"
                                    class="!text-[14px] !font-[400] !text-[#000000] !underline whitespace-nowrap">{{ __('business.templates.manage_checklist') }}</x-form.link>
                                <a href="{{ route('templates.edit', $template['id']) }}" class="text-[#0d44ea]"
                                    title="{{ __('business.templates.edit') }}">
                                    <svg width="22" height="22" viewBox="0 0 22 22" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M20 19.9998H12M1.5 20.4998L7.04927 18.3655C7.40421 18.229 7.58168 18.1607 7.74772 18.0716C7.8952 17.9924 8.0358 17.901 8.16804 17.7984C8.31692 17.6829 8.45137 17.5484 8.72028 17.2795L20 5.99982C21.1046 4.89525 21.1046 3.10438 20 1.99981C18.8955 0.895245 17.1046 0.895244 16 1.99981L4.72028 13.2795C4.45138 13.5484 4.31692 13.6829 4.20139 13.8318C4.09877 13.964 4.0074 14.1046 3.92823 14.2521C3.83911 14.4181 3.77085 14.5956 3.63433 14.9506L1.5 20.4998ZM1.5 20.4998L3.55812 15.1488C3.7054 14.7659 3.77903 14.5744 3.90534 14.4867C4.01572 14.4101 4.1523 14.3811 4.2843 14.4063C4.43533 14.4351 4.58038 14.5802 4.87048 14.8703L7.12957 17.1294C7.41967 17.4195 7.56472 17.5645 7.59356 17.7155C7.61877 17.8475 7.58979 17.9841 7.51314 18.0945C7.42545 18.2208 7.23399 18.2944 6.85107 18.4417L1.5 20.4998Z"
                                            stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>

                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="py-6 !text-center text-gray-400">{{ __('business.templates.no_found') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div x-cloak x-show="confirmDelete">
        <x-confirm.confirm-delete wire:key="delete-modal" :message="__('business.templates.delete_confirm_message')"
            btnConfirm="{{ __('business.templates.delete_btn') }}"
            btnCancel="{{ __('business.templates.cancel_btn') }}" />
    </div>
</div>
