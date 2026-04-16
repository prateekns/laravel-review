<!-- Maintenance Order Form -->
<div
    x-data="maintenanceOrderForm"
    x-init="
        selectedCustomerId = '{{ old('customer_id', isset($maintenance) ? $maintenance->customer_id : (isset($customer) ? $customer->id : 'null')) }}';
        selectedTemplateId = '{{ old('template_id', isset($maintenance) ? $maintenance->template_id : 'null') }}';
        customerType = '{{ old('customer_type', 'existing') }}';
        isCustomerFixed = {{ isset($customer) ? 'true' : 'false' }};
        $nextTick(() => init());
    "
    @close-cancel.window="cancelInactive"
    @close-confirm.window="confirmInactiveStatus"
>
    <script>
        window.oldWorkOrder = @json(isset($maintenance) ? $maintenance : null);
        window.oldInput = @json(old());
    </script>
    <form
        action="{{ isset($maintenance) ? route('business.work-orders.maintenance.update', $maintenance) : route('business.work-orders.maintenance.store') }}"
        method="POST"
        enctype="multipart/form-data"
        x-ref="form"
    >
        @csrf
        @if(isset($maintenance))
            @method('PUT')
        @endif

        <!-- Customer Selection Section -->
        <div x-data="{ open: true }" class="white-box shadow-sm py-[24px] px-[20px] rounded-[16px]">
            <div class="accordian-header"
                :class="open ? 'manage-template-heading cursor-pointer show' : 'manage-template-heading cursor-pointer'"
                @click="open = !open"
            >
                <div class="flex gap-[10px] items-center">
                    <p class="m-0">{{ __('business.work_orders.select_customer') }}*</p>
                </div>
                <div class="icon" :class="{ 'show': open }" style="transition: transform 0.3s ease">
                    <svg width="16" height="10" viewBox="0 0 16 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2 2L8 8L14 2" stroke="#0D44EA" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>
            </div>

            <div x-show="open" x-transition class="accordian-contant w-full !mt-[24px] max-[640px]:!p-0">
                <div class="flex flex-col w-full">
                    <!-- Customer Type Selection -->
                    <div x-show="{{ $maintenance ? 'false' : '!isCustomerFixed' }}" x-cloak>
                        <div class="flex items-center gap-[17px] mt-3">
                            <div class="custom-radio-box">
                                <input type="radio" name="customer_type" value="existing" class="mr-2" id="customer_type_existing" x-model="customerType">
                                <label for="customer_type_existing" class="mr-2 text-gray-500">{{ __('business.work_orders.existing_customer') }}</label>
                            </div>
                            <div class="custom-radio-box">
                                <input type="radio" name="customer_type" value="new" class="mr-2" id="customer_type_new" x-model="customerType">
                                <label for="customer_type_new" class="mr-2 text-gray-500">{{ __('business.work_orders.new_customer') }}</label>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Selection Dropdown or Fixed Customer Display -->
                    <div x-show="customerType === 'existing'" x-cloak>
                        @if(isset($customer) || isset($maintenance))
                            <h3 class="font-[400] text-[14px] mb-[4px]">{{ __('business.work_orders.sections.name') }}</h3>
                            <div class="p-[0] mt-[4px]">
                                <p class="font-[400] text-[#000000] text-[16px] customer-info">
                                    {{ isset($maintenance) ? $maintenance->customer->customer_name :
                                        (isset($customer) ? $customer->customer_name : '') }}
                                </p>
                                <input type="hidden" name="customer_id" value="{{ isset($maintenance) ? $maintenance->customer_id : (isset($customer) ? $customer->id : '') }}">
                                <input type="hidden" name="customer_type" value="existing">
                            </div>
                        @else
                            <select
                                name="customer_id"
                                id="customer_id"
                                class="form-select border-[2px] border-[#E5E7EB] rounded-[10px] px-[16px] py-[12px] text-[16px] font-[400] text-[#000000] mt-[24px] min-[768px]:max-w-[452px] max-[767px]:max-w-[100%] w-full cursor-pointer"
                                x-bind:class="{ 'error-message-border': errors.customer_id }"
                                x-model="selectedCustomerId"
                                @change="handleCustomerChange($event)"
                            >
                                <option value="">{{ __('business.work_orders.select_customer_placeholder') }}</option>
                                @foreach($customers as $c)
                                    <option value="{{ $c->id }}" {{ old('customer_id', isset($maintenance) ? $maintenance->customer_id : null) == $c->id ? 'selected' : '' }}>
                                        {{ $c->customer_name }}
                                    </option>
                                @endforeach
                            </select>
                            <div x-show="errors.customer_id" x-text="errors.customer_id" class="error-message-box customer_id"></div>
                            @error('customer_id') <div class="error-message-box customer_id">{{ $message }}</div> @enderror
                        @endif
                    </div>

                    <!-- Add New Customer Button -->
                    <div x-show="customerType === 'new' && !isCustomerFixed" class="mt-[24px] col-span-2 text-left" x-cloak>
                        <x-form.link link="{{ route('business.customers.create') }}" class="btn-box btn">
                            {{ __('business.customers.add_new') }}
                        </x-form.link>
                    </div>
                </div>

                <!-- Customer Details Display -->
                <div class="mt-[24px] col-span-2"
                    x-show="customerType === 'existing' && (selectedCustomerId || isCustomerFixed)"
                    wire:key="customer-pool-details"
                >
                    @livewire('business.customers.customer-pool-details', [
                        'customerId' => isset($maintenance) ? $maintenance->customer_id : (isset($customer) ? $customer->id : null),
                        'isViewMode' => isset($maintenance) || isset($customer)
                    ], key('customer-pool-details-' . (old('customer_id', isset($maintenance) ? $maintenance->customer_id : (isset($customer) ? $customer->id : 'new')))))
                </div>
            </div>
        </div>

        <!-- Service Details Section -->
        <div x-data="{ open: true }" class="white-box shadow-sm py-[24px] px-[20px] rounded-[16px] mt-6" x-show="customerType === 'existing'">
            <div class="accordian-header"
                :class="open ? 'manage-template-heading cursor-pointer show' : 'manage-template-heading cursor-pointer'"
                @click="open = !open"
            >
                <div class="flex gap-[10px] items-center">
                    <p class="m-0">{{ __('business.work_orders.service_type') }}*</p>
                </div>
                <div class="icon" :class="{ 'show': open }" style="transition: transform 0.3s ease">
                    <svg width="16" height="10" viewBox="0 0 16 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2 2L8 8L14 2" stroke="#0D44EA" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>
            </div>

            <div x-show="open" x-transition class="accordian-contant w-full !mt-[30px] !p-[0]">
                <div class="service-type-box">
                    <div class="flex flex-col mb-[24px]">
                        <!-- Service Type Selection -->
                        <div>
                            <x-form.label for="template_id" required>{{ __('business.work_orders.sections.select_service_type') }}</x-form.label>
                            <div class="grid grid-cols-2 gap-[24px] max-[600px]:grid-cols-1 mt-2">
                                <div class="flex flex-col">
                                    <select
                                        name="template_id"
                                        id="template_id"
                                        class="form-select mt-[0px] w-full !max-w-full"
                                        x-bind:class="{ 'error-message-border': errors.template_id }"
                                        x-model="selectedTemplateId"
                                        @change="handleTemplateChange($event)"
                                    >
                                        <option value="">{{ __('business.work_orders.sections.select_service_type') }}</option>
                                        @foreach($templates as $template)
                                            <option
                                                value="{{ $template->id }}"
                                                data-description="{{ $template->description }}"
                                                {{ old('template_id', isset($maintenance) ? $maintenance->template_id : null) == $template->id ? 'selected' : '' }}
                                            >
                                                {{ $template->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div x-show="errors.template_id" x-text="errors.template_id" class="error-message-box template_id"></div>
                                    @error('template_id') <div class="error-message-box template_id">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-[24px] max-[600px]:grid-cols-1">
                        <div class="task-details-box">
                            <div>
                                <div class="flex gap-[10px] items-center">
                                    <h4 class="text-[20px] font-[600] text-[#086DF1]">{{ __('business.work_orders.sections.task_details') }}</h4>
                                </div>
                            </div>
                
                            <!-- Job Name -->
                            <div class="mt-6">
                                <x-form.label for="name" required>{{ __('business.work_orders.job_name') }}</x-form.label>
                                <x-form.text class="mt-2"
                                    name="name"
                                    id="name"
                                    x-model="jobName"
                                    x-bind:class="{ 'error-message-border': errors.name }"
                                    :value="old('name', isset($maintenance) ? $maintenance->name : '')"
                                    :placeholder="__('business.work_orders.job_name_placeholder')"
                                />
                                <div x-show="errors.name" x-text="errors.name" class="error-message-box name"></div>
                                @error('name') <div class="error-message-box name">{{ $message }}</div> @enderror
                            </div>
                
                            <!-- Job Description -->
                            <div class="mt-6">
                                <x-form.label for="description">{{ __('business.work_orders.job_description') }}</x-form.label>
                                <x-form.textarea class="mt-2"
                                    name="description"
                                    id="description"
                                    x-model="jobDescription"
                                    x-bind:class="{ 'error-message-border': errors.description }"
                                    rows="5"
                                    :value="old('description', isset($maintenance) ? $maintenance->description : '')"
                                    :placeholder="__('business.work_orders.job_description_placeholder')"
                                />
                                <div x-show="errors.description" x-text="errors.description" class="error-message-box description"></div>
                                @error('description') <div class="error-message-box description">{{ $message }}</div> @enderror
                            </div>
                
                            <!-- Additional Task -->
                            <div class="mt-6">
                                <x-form.label for="additional_task">{{ __('business.work_orders.additional_task') }}</x-form.label>
                                <x-form.text class="mt-2"
                                    name="additional_task"
                                    id="additional_task"
                                    x-model="additionalTask"
                                    x-bind:class="{ 'error-message-border': errors.additional_task }"
                                    :value="old('additional_task', isset($maintenance) ? $maintenance->additional_task : '')"
                                    :placeholder="__('business.work_orders.additional_task_placeholder')"
                                />
                                <div x-show="errors.additional_task" x-text="errors.additional_task" class="error-message-box additional_task"></div>
                                @error('additional_task') <div class="error-message-box additional_task">{{ $message }}</div> @enderror
                            </div>
                
                            <!-- Preferred Start Date & Time -->
                            <div class="mt-6">
                                <x-form.label for="preferred_start_date" required>{{ __('business.work_orders.preferred_date_time') }}</x-form.label>
                                <div class="grid grid-cols-2 gap-[24px] max-[1199px]:grid-cols-1">
                                    <!-- Preferred Start Date -->
                                    <div class="flex flex-col">
                                        <div class="date-box date-box-calendar">
                                            <input
                                                type="date"
                                                name="preferred_start_date"
                                                id="preferred_start_date"
                                                class="input-box custom-date-icon mt-2"
                                                value="{{ old('preferred_start_date', isset($maintenance) ? $maintenance->preferred_start_date : ($defaultDate ?? '')) }}"
                                                x-bind:class="{ 'error-message-border': errors.preferred_start_date }"
                                                x-model="preferredStartDate"
                                                x-bind:min="minAllowedDate"
                                                x-bind:max="maxAllowedDate"
                                                onkeydown="return false" onpaste="return false" ondrop="return false" inputmode="none" autocomplete="off"
                                            />
                                        </div>
                                        <div x-show="errors.preferred_start_date" x-text="errors.preferred_start_date" class="error-message-box preferred_start_date"></div>
                                        @error('preferred_start_date') <div class="error-message-box preferred_start_date">{{ $message }}</div> @enderror
                                    </div>
                                    <!-- Preferred Start Time -->
                                    <div class="flex flex-col">
                                        <div class="date-box mt-[6px]">
                                            <x-form.time-picker
                                                type="time"
                                                name="preferred_start_time"
                                                id="preferred_start_time"
                                                value="{{ old('preferred_start_time', isset($maintenance) ? $maintenance->preferred_start_time : ($defaultTime ?? '')) }}"
                                                x-model="preferredStartTime"
                                                class="input-box custom-time-icon"
                                                step="900"
                                                x-bind:class="{ 'error-message-border': errors.preferred_start_time }"
                                            />
                                        </div>
                                        <div x-show="errors.preferred_start_time" x-text="errors.preferred_start_time" class="error-message-box preferred_start_time"></div>
                                        @error('preferred_start_time') <div class="error-message-box preferred_start_time">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>
                
                            <!-- Work Order Photo -->
                            <div class="mt-6" x-data="photoUpload">
                                <div class="flex items-center justify-between">
                                    <x-form.label for="photo">{{ __('business.work_orders.upload_photo') }}</x-form.label>
                                    <button
                                        type="button"
                                        class="text-[12px] font-[400] text-[#2563EB] italic underline"
                                        x-show="file || existingPhoto"
                                        @click="$refs.photo.click()"
                                    >
                                        {{ __('business.work_orders.sections.change_photo') }}
                                    </button>
                                </div>
                
                                <!-- Initialize oldWorkOrder data for JavaScript -->
                                @if(isset($maintenance) && $maintenance->photo)
                                    <script>
                                        window.oldWorkOrder = window.oldWorkOrder || {};
                                        window.oldWorkOrder.photo = @json($maintenance->photo);
                                        window.oldWorkOrder.photoUrl = @json($maintenance->photo_url);
                                        window.oldWorkOrder.photoThumbUrl = @json($maintenance->photo_thumb_url);
                                    </script>
                                @endif
                
                                <!-- File Upload Area -->
                                <div class="mt-2" id="wo-image-upload-area">
                                    <!-- Show this when there's an existing or selected photo -->
                                    <div
                                        x-show="file || existingPhoto"
                                        class="relative border-[2px] border-dashed border-[#E5E7EB] p-[34px] rounded-[12px]"
                                    >
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-12 h-12 bg-gray-100 rounded-lg overflow-hidden cursor-pointer"
                                                @click="$dispatch('open-modal', {
                                                    id: 'image-preview-modal',
                                                    url: file ? file.preview : existingPhotoUrl
                                                })"
                                            >
                                                <img
                                                    :src="file ? file.preview : (existingPhotoThumbUrl || existingPhotoUrl)"
                                                    class="w-full h-full object-cover"
                                                    alt="Work Order"
                                                    id="wo-image-preview"
                                                >
                                            </div>
                                            <div class="flex-1">
                                                <p class="text-[12px] font-[600] text-[#0B0B0B]" x-text="file ? file.name : existingPhotoName"></p>
                                                <p class="text-[14px] font-[400] text-[#767676]" x-text="file ? formatFileSize(file.file.size) : ''"></p>
                                            </div>
                                            <button
                                                type="button"
                                                class="cursor-pointer"
                                                @click="file ? removeFile() : markPhotoForDeletion()"
                                            >
                                                <svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M15.5 9L9.5 15M9.5 9L15.5 15M22.5 12C22.5 17.5228 18.0228 22 12.5 22C6.97715 22 2.5 17.5228 2.5 12C2.5 6.47715 6.97715 2 12.5 2C18.0228 2 22.5 6.47715 22.5 12Z" stroke="#0D44EA" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                
                                    <!-- Show this when no photo is selected -->
                                    <div
                                        x-show="!file && !existingPhoto"
                                        class="flex items-center justify-center w-full"
                                    >
                                        <label
                                            for="photo"
                                            class="flex flex-col items-center justify-center w-full h-[136px] p-[34px] border-2 border-dashed rounded-[12px] cursor-pointer transition-colors duration-200"
                                            :class="dragover ? 'border-blue-500 bg-blue-50' : 'border-gray-300'"
                                            @dragover.prevent="handleDragOver($event)"
                                            @dragleave.prevent="handleDragLeave($event)"
                                            @drop.prevent="handleDrop($event)"
                                        >
                                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                                <svg width="41" height="52" viewBox="0 0 41 52" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <g clip-path="url(#clip0_1490_28846)">
                                                        <path d="M35.5 25V31.6667C35.5 32.5507 35.1488 33.3986 34.5237 34.0237C33.8986 34.6488 33.0507 35 32.1667 35H8.83333C7.94928 35 7.10143 34.6488 6.47631 34.0237C5.85119 33.3986 5.5 32.5507 5.5 31.6667V25" stroke="#6B7280" stroke-width="3.33333" stroke-linecap="round" stroke-linejoin="round"/>
                                                        <path d="M28.8307 13.3333L20.4974 5L12.1641 13.3333" stroke="#6B7280" stroke-width="3.33333" stroke-linecap="round" stroke-linejoin="round"/>
                                                        <path d="M20.5 5V25" stroke="#6B7280" stroke-width="3.33333" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </g>
                                                    <defs>
                                                        <clipPath id="clip0_1490_28846">
                                                            <rect width="40" height="40" fill="white" transform="translate(0.5)"/>
                                                        </clipPath>
                                                    </defs>
                                                </svg>
                
                                                <p class="mt-[0px] text-[#212529] text-[14px] font-[600]">
                                                    {{ __('business.work_orders.upload_photo_drag_and_drop') }}
                                                </p>
                                                <p class="mt-[0px] text-[#212529] text-[14px] font-[400]">
                                                    {{ __('business.work_orders.image_requirements') }}
                                                </p>
                                            </div>
                                        </label>
                                    </div>
                
                                    <!-- Hidden file input -->
                                    <input
                                        id="photo"
                                        name="photo"
                                        type="file"
                                        class="hidden"
                                        accept=".jpg,.jpeg,.png"
                                        x-ref="photo"
                                        @change="handleFileSelect($event)"
                                    >
                
                                    <!-- Error Message -->
                                    <div
                                        x-show="errorMessage"
                                        x-text="errorMessage"
                                        class="error-message-box"
                                        id="wo-image-error-message"
                                    ></div>
                                </div>
                            </div>
                        </div>

                        <!-- Checklist Section -->
                        <div class="task-details-box checklist-section">
                            <div>
                                @livewire('business.work-orders.checklist-section', [
                                    'templateId' => old('template_id', isset($maintenance) ? $maintenance->template_id : null),
                                    'workOrder' => isset($maintenance) ? $maintenance : null
                                ])
                                
                                <!-- Error Message -->
                                <div x-show="errors.checklist_items" x-text="errors.checklist_items" class="error-message-box checklist_items"></div>
                                @error('checklist_items') <div class="error-message-box checklist_items">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Setup Maintenance Service Frequency Section -->
        <div class="white-box shadow-sm py-[24px] px-[20px] rounded-[16px] mt-6" x-show="customerType === 'existing'">
            <div class="accordian-header"
                :class="open ? 'manage-template-heading cursor-pointer show' : 'manage-template-heading cursor-pointer'"
                @click="open = !open"
            >
                <div class="flex gap-[10px] items-center">
                    <p class="m-0">{{ __('business.work_orders.sections.setup_recurring_service') }}</p>
                </div>
                <div class="icon" :class="{ 'show': open }" style="transition: transform 0.3s ease">
                    <svg width="16" height="10" viewBox="0 0 16 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2 2L8 8L14 2" stroke="#0D44EA" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>
            </div>

            <div x-show="open" x-transition class="accordian-contant w-full !mt-[0px] !p-[0]">
                <div class="flex flex-col py-[20px] max-[640px]:px-[0px]">
                    <!-- Service Re-occurrence Question -->
                    <div>
                        <x-form.label>{{ __('business.work_orders.recurring_service_question') }}</x-form.label>
                        <div class="flex items-center gap-4 mt-[12px]">
                            <div class="custom-radio-box">
                                <input
                                    type="radio"
                                    name="is_recurring"
                                    value="1"
                                    id="recurring_yes"
                                    :checked="isRecurring === true || isRecurring === '1'"
                                    @change="isRecurring = true; handleRecurringToggle()"
                                    {{ old('is_recurring', isset($maintenance) ? $maintenance->is_recurring : false) ? 'checked' : '' }}
                                >
                                <label for="recurring_yes" class="label-box">{{ __('common.form.yes') }}</label>
                            </div>
                            <div class="custom-radio-box">
                                <input
                                    type="radio"
                                    name="is_recurring"
                                    value="0"
                                    id="recurring_no"
                                    :checked="isRecurring === false || isRecurring === '0'"
                                    @change="isRecurring = false; handleRecurringToggle()"
                                    {{ old('is_recurring', isset($maintenance) ? $maintenance->is_recurring : false) ? '' : 'checked' }}
                                >
                                <label for="recurring_no" class="label-box">{{ __('common.form.no') }}</label>
                            </div>
                        </div>
                        @error('is_recurring')
                            <div class="error-message-box">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Custom Frequency Section -->
                    <div x-show="isRecurring" x-transition class="mt-6">
                        <h3 class="text-[20px] font-[600] text-[#212529] mb-[12px]">{{ __('business.work_orders.choose_custom_frequency') }}</h3>
                        
                        <!-- Frequency and Repeat After -->
                        <div class="flex min-[992px]:max-w-[508px]">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4 w-full">
                                <div>
                                    <x-form.label for="frequency">{{ __('business.work_orders.frequency') }}</x-form.label>
                                    <select
                                        name="frequency"
                                        id="frequency"
                                        x-model="frequency"
                                        @change="handleFrequencyChange()"
                                        class="form-select border-[2px] border-[#E5E7EB] rounded-[10px] px-[16px] py-[12px] text-[16px] font-[400] text-[#000000] mt-[12px] w-full cursor-pointer"
                                    >
                                        <option value="daily" {{ old('frequency', isset($maintenance) ? $maintenance->frequency : '') == 'daily' ? 'selected' : '' }}>{{ __('business.work_orders.frequency_daily') }}</option>
                                        <option value="weekly" {{ old('frequency', isset($maintenance) ? $maintenance->frequency : '') == 'weekly' ? 'selected' : '' }}>{{ __('business.work_orders.frequency_weekly') }}</option>
                                        <option value="semi_monthly" {{ old('frequency', isset($maintenance) ? $maintenance->frequency : '') == 'semi_monthly' ? 'selected' : '' }}>{{ __('business.work_orders.frequency_semi_monthly') }}</option>
                                        <option value="monthly" {{ old('frequency', isset($maintenance) ? $maintenance->frequency : '') == 'monthly' ? 'selected' : '' }}>{{ __('business.work_orders.frequency_monthly') }}</option>
                                    </select>
                                    <div x-show="errors.frequency" x-text="errors.frequency" class="error-message-box frequency"></div>
                                    @error('frequency')
                                        <div class="error-message-box">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <!-- Daily Frequency - All Days (Readonly) -->
                                <div x-show="frequency === 'daily'">
                                    <x-form.label for="repeat_after">{{ __('business.work_orders.repeat_every') }}</x-form.label>
                                    <input
                                        type="text"
                                        name="repeat_after"
                                        id="repeat_after"
                                        value="All Days"
                                        readonly
                                        class="form-input border-[2px] border-[#E5E7EB] rounded-[10px] px-[16px] py-[12px] text-[16px] font-[400] text-[#6B7280] mt-[12px] w-full bg-gray-100"
                                    />
                                    <input type="hidden" name="repeat_after" value="1" />
                                    <div x-show="errors.repeat_after" x-text="errors.repeat_after" class="error-message-box repeat_after"></div>
                                    @error('repeat_after')
                                        <div class="error-message-box">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Semi-Monthly Frequency - Hidden Repeat After -->
                                <div x-show="frequency === 'semi_monthly'" style="display: none;">
                                    <input type="hidden" name="repeat_after" value="1" />
                                </div>

                                <!-- Weekly Frequency - Repeat After Dropdown -->
                                <div x-show="frequency === 'weekly'">
                                    <x-form.label for="repeat_after_weekly">{{ __('business.work_orders.repeat_after') }}</x-form.label>
                                    <select
                                        name="repeat_after"
                                        id="repeat_after_weekly"
                                        x-model="repeatAfter"
                                        class="form-select border-[2px] border-[#E5E7EB] rounded-[10px] px-[16px] py-[12px] text-[16px] font-[400] text-[#000000] mt-[12px] w-full cursor-pointer"
                                    >
                                        <option value="1" {{ old('repeat_after', isset($maintenance) ? $maintenance->repeat_after : '') == '1' ? 'selected' : '' }}>{{ __('business.work_orders.repeat_after_1_week') }}</option>
                                        <option value="2" {{ old('repeat_after', isset($maintenance) ? $maintenance->repeat_after : '') == '2' ? 'selected' : '' }}>{{ __('business.work_orders.repeat_after_2_weeks') }}</option>
                                        <option value="3" {{ old('repeat_after', isset($maintenance) ? $maintenance->repeat_after : '') == '3' ? 'selected' : '' }}>{{ __('business.work_orders.repeat_after_3_weeks') }}</option>
                                        <option value="4" {{ old('repeat_after', isset($maintenance) ? $maintenance->repeat_after : '') == '4' ? 'selected' : '' }}>{{ __('business.work_orders.repeat_after_4_weeks') }}</option>
                                    </select>
                                    <div x-show="errors.repeat_after" x-text="errors.repeat_after" class="error-message-box repeat_after"></div>
                                    @error('repeat_after')
                                        <div class="error-message-box">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Monthly Frequency - Repeat After Dropdown -->
                                <div x-show="frequency === 'monthly'">
                                    <x-form.label for="repeat_after_monthly">{{ __('business.work_orders.repeat_after') }}</x-form.label>
                                    <select
                                        name="repeat_after"
                                        id="repeat_after_monthly"
                                        x-model="repeatAfter"
                                        class="form-select border-[2px] border-[#E5E7EB] rounded-[10px] px-[16px] py-[12px] text-[16px] font-[400] text-[#000000] mt-[12px] w-full cursor-pointer"
                                    >
                                        <option value="1" {{ old('repeat_after', isset($maintenance) ? $maintenance->repeat_after : '') == '1' ? 'selected' : '' }}>{{ __('business.work_orders.repeat_after_1_month') }}</option>
                                        <option value="2" {{ old('repeat_after', isset($maintenance) ? $maintenance->repeat_after : '') == '2' ? 'selected' : '' }}>{{ __('business.work_orders.repeat_after_2_months') }}</option>
                                        <option value="3" {{ old('repeat_after', isset($maintenance) ? $maintenance->repeat_after : '') == '3' ? 'selected' : '' }}>{{ __('business.work_orders.repeat_after_3_months') }}</option>
                                        <option value="6" {{ old('repeat_after', isset($maintenance) ? $maintenance->repeat_after : '') == '6' ? 'selected' : '' }}>{{ __('business.work_orders.repeat_after_6_months') }}</option>
                                        <option value="12" {{ old('repeat_after', isset($maintenance) ? $maintenance->repeat_after : '') == '12' ? 'selected' : '' }}>{{ __('business.work_orders.repeat_after_12_months') }}</option>
                                    </select>
                                    <div x-show="errors.repeat_after" x-text="errors.repeat_after" class="error-message-box repeat_after"></div>
                                    @error('repeat_after')
                                        <div class="error-message-box">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Select Days Section -->
                       
                        <div x-show="frequency === 'weekly' || frequency === 'semi_monthly'" class="mb-4">
                            <x-form.label>{{ __('business.work_orders.select_days') }}</x-form.label>
                            <div class="flex flex-wrap w-full mt-[20px] gap-[20px]">
                                @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                                    <label for="selected_days_{{ $day }}" class="flex items-center label-box gap-[15px] input-checkbox">
                                        <input
                                            type="checkbox"
                                            name="selected_days[]"
                                            value="{{ $day }}"
                                            x-model="selectedDays"
                                            id="selected_days_{{ $day }}"
                                            class="form-checkbox h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                        >
                                        <span class="text-[16px] font-[600] text-[#374151]">{{ ucfirst($day) }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <div x-show="errors.selected_days" x-text="errors.selected_days" class="error-message-box selected_days"></div>
                            @error('selected_days')
                                <div class="error-message-box">{{ $message }}</div>
                            @enderror
                        </div>
                     

                        <!-- Monthly Day Selection -->
                          <div class="flex min-[992px]:max-w-[508px]">
                        <div x-show="frequency === 'monthly'" class="mb-4 w-full">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <x-form.label for="monthly_day_type">{{ __('business.work_orders.on_the') }}</x-form.label>
                                    <select
                                        name="monthly_day_type"
                                        id="monthly_day_type"
                                        x-model="monthlyDayType"
                                        class="form-select border-[2px] border-[#E5E7EB] rounded-[10px] px-[16px] py-[12px] text-[16px] font-[400] text-[#000000] mt-[12px] w-full cursor-pointer"
                                    >
                                        <option value="first" {{ old('monthly_day_type', isset($maintenance) ? $maintenance->monthly_day_type : '') == 'first' ? 'selected' : '' }}>{{ __('business.work_orders.monthly_day_type_first') }}</option>
                                        <option value="second" {{ old('monthly_day_type', isset($maintenance) ? $maintenance->monthly_day_type : '') == 'second' ? 'selected' : '' }}>{{ __('business.work_orders.monthly_day_type_second') }}</option>
                                        <option value="third" {{ old('monthly_day_type', isset($maintenance) ? $maintenance->monthly_day_type : '') == 'third' ? 'selected' : '' }}>{{ __('business.work_orders.monthly_day_type_third') }}</option>
                                        <option value="fourth" {{ old('monthly_day_type', isset($maintenance) ? $maintenance->monthly_day_type : '') == 'fourth' ? 'selected' : '' }}>{{ __('business.work_orders.monthly_day_type_fourth') }}</option>
                                    </select>
                                </div>
                                <div class="mt-auto">
                                    <select
                                        name="monthly_day_of_week"
                                        id="monthly_day_of_week"
                                        x-model="monthlyDayOfWeek"
                                        class="align-bottom form-select border-[2px] border-[#E5E7EB] rounded-[10px] px-[16px] py-[12px] text-[16px] font-[400] text-[#000000] mt-[12px] w-full cursor-pointer"
                                    >
                                        <option value="monday" {{ old('monthly_day_of_week', isset($maintenance) ? $maintenance->monthly_day_of_week : '') == 'monday' ? 'selected' : '' }}>{{ __('business.work_orders.day_monday') }}</option>
                                        <option value="tuesday" {{ old('monthly_day_of_week', isset($maintenance) ? $maintenance->monthly_day_of_week : '') == 'tuesday' ? 'selected' : '' }}>{{ __('business.work_orders.day_tuesday') }}</option>
                                        <option value="wednesday" {{ old('monthly_day_of_week', isset($maintenance) ? $maintenance->monthly_day_of_week : '') == 'wednesday' ? 'selected' : '' }}>{{ __('business.work_orders.day_wednesday') }}</option>
                                        <option value="thursday" {{ old('monthly_day_of_week', isset($maintenance) ? $maintenance->monthly_day_of_week : '') == 'thursday' ? 'selected' : '' }}>{{ __('business.work_orders.day_thursday') }}</option>
                                        <option value="friday" {{ old('monthly_day_of_week', isset($maintenance) ? $maintenance->monthly_day_of_week : '') == 'friday' ? 'selected' : '' }}>{{ __('business.work_orders.day_friday') }}</option>
                                        <option value="saturday" {{ old('monthly_day_of_week', isset($maintenance) ? $maintenance->monthly_day_of_week : '') == 'saturday' ? 'selected' : '' }}>{{ __('business.work_orders.day_saturday') }}</option>
                                        <option value="sunday" {{ old('monthly_day_of_week', isset($maintenance) ? $maintenance->monthly_day_of_week : '') == 'sunday' ? 'selected' : '' }}>{{ __('business.work_orders.day_sunday') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div x-show="errors.monthly_day_type" x-text="errors.monthly_day_type" class="error-message-box monthly_day_type"></div>
                            @error('monthly_day_type')
                                <div class="error-message-box">{{ $message }}</div>
                            @enderror
                            <div x-show="errors.monthly_day_of_week" x-text="errors.monthly_day_of_week" class="error-message-box monthly_day_of_week"></div>
                            @error('monthly_day_of_week')
                                <div class="error-message-box">{{ $message }}</div>
                            @enderror
                        </div>
                        </div>

                        {{-- End Date --}}
                        <div class="mt-6">
                            <x-form.label for="end_date">{{ __('business.work_orders.end_date') }}</x-form.label>
                            <div class="grid grid-cols-2 gap-[24px] max-[1199px]:grid-cols-1">
                                <!-- End Date -->
                                <div class="flex flex-col relative">
                                    <div class="date-box date-box-calendar">
                                        <input
                                            type="date"
                                            name="end_date"
                                            id="end_date"
                                            class="input-box custom-date-icon mt-2"
                                            value=""
                                            x-bind:class="{ 'error-message-border': errors.end_date }"
                                            x-model="endDate"
                                            x-bind:min="minAllowedDate"
                                            x-bind:max="maxAllowedDate"
                                            onkeydown="return false" onpaste="return false" ondrop="return false" inputmode="none" autocomplete="off"
                                            />
                                    </div>
                                    <div x-show="errors.end_date" x-text="errors.end_date" class="error-message-box end_date"></div>
                                    @error('end_date') <div class="error-message-box end_date">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div x-show="customerType === 'existing'" x-cloak>
            <!-- Communication Notes Section -->
            <div class="mt-[17px]">
                <x-form.label for="description">{{ __('business.work_orders.sections.communication') }}</x-form.label>
                <p class="mt-[12px] text-[14px] font-[500] text-[#4B5563]">{{ __('business.work_orders.sections.communication_description') }}</p>
                <p class="text-[14px] font-[500] text-[#4B5563] italic">{{ __('business.work_orders.sections.communication_notes') }}</p>
                <div class="flex items-center gap-[17px] mt-[12px]">
                    <div class="custom-radio-box">
                        <input type="radio" name="technician_customer_coordination" value="1" class="mr-2" id="communication_yes" {{ old('technician_customer_coordination', isset($maintenance) ? $maintenance->technician_customer_coordination : true) ? 'checked' : '' }}>
                        <label for="communication_yes" class="mr-2 text-gray-500">{{ __('business.work_orders.yes') }}</label>
                    </div>
                    <div class="custom-radio-box">
                        <input type="radio" name="technician_customer_coordination" value="0" class="mr-2" id="communication_no" {{ old('technician_customer_coordination', isset($maintenance) ? $maintenance->technician_customer_coordination : true) ? '' : 'checked' }}>
                        <label for="communication_no" class="mr-2 text-gray-500">{{ __('business.work_orders.no') }}</label>
                    </div>
                    
                    @error('technician_customer_coordination')
                        <div class="error-message-box">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Active/Inactive Toggle (Edit Only) -->
            @if(isset($maintenance))
                <div class="mt-6">
                    <x-form.label>{{ __('business.customer.status.label') }}</x-form.label>
                    <div class="flex items-center gap-4 mt-[12px]">
                        <div class="custom-radio-box ">
                            <input
                                type="radio"
                                name="is_active"
                                value="1"
                                id="status_active"
                                x-model="isActive"
                                @change="showConfirm=false"
                            >
                            <label for="status_active" class="mr-2 text-gray-500">{{ __('business.customer.status.active') }}</label>
                        </div>
                        <div class="custom-radio-box">
                            <input
                                type="radio"
                                name="is_active"
                                value="0"
                                id="status_inactive"
                                x-model="isActive"
                                @change="showConfirm=true"
                            >
                            <label for="status_inactive" class="mr-2 text-gray-500">{{ __('business.customer.status.inactive') }}</label>
                        </div>
                    </div>
                </div>
            @else
                <!-- Add hidden input for is_active with default value 1 -->
                <input type="hidden" name="is_active" value="1">
            @endif
        </div>

        <div class="mt-[24px]">
            <x-form.label>{{ __('business.maintenance_order.chemical_note_heading') }}</x-form.label>
            <p class="mt-[12px] text-[14px] font-[400] text-[#606060]">{{ __('business.maintenance_order.chemical_note_description') }}</p>
        </div>

        <!-- Form Actions -->
        <div class="flex gap-4 mt-[17px] max-[640px]:flex-col" x-show="customerType === 'existing'">
            <button type="button" value="save" class="btn-box btn" @click="$event.preventDefault(); submitWithAction('save')">
                {{ isset($maintenance) ? __('business.customer.update') : __('business.work_orders.create_order') }}
            </button>
            @if(!isset($maintenance))
                <button type="button" value="save_and_assign" class="btn-box btn" @click="$event.preventDefault(); submitWithAction('save_and_assign')">
                    {{ __('business.work_orders.create_and_assign') }}
                </button>
            @endif
            <input type="hidden" name="action" x-ref="action" x-model="action">
            <x-form.link link="{{ route('business.work-orders.maintenance.index') }}" class="btn-box outlined">
                {{ __('business.work_orders.cancel') }}
            </x-form.link>
        </div>

        <!-- Add the Confirmation Modal for Inactive Status -->
        @include('components.confirm.confirm-modal', [
            'title' => __('business.customer.confirm_deactivate'),
            'description' => __('business.work_orders.messages.inactive_confirm_message'),
            'modalType' => 'inactive'
        ])

        <!-- Add the Confirmation Modal for Updates -->
        @if(isset($maintenance))
            @include('components.confirm.update-confirm-modal', [
                'title' => __('business.maintenance.messages.update_confirm_title'),
                'description' => __('business.maintenance.messages.update_confirm_message'),
                'btnConfirm' => __('business.maintenance.messages.update_confirm_button'),
                'btnCancel' => __('business.maintenance.messages.update_cancel_button'),
                'modalType' => 'update'
            ])
        @endif

        <!-- Add the Image Preview Modal Component (Single Instance) -->
        <x-modal.image-preview />
    </form>
</div>
