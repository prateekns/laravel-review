<script>
    window.oldCustomer = @json($customer);
    window.oldInput = @json(old());
    @if (!$customer->id)
        window.businessCountry = @json($businessCountry);
        window.businessState = @json($businessState);
        window.businessCity = @json($businessCity);
    @endif
</script>
<form action="{{ $customer->id ? route('business.customers.update', $customer) : route('business.customers.store') }}"
    method="POST" @submit.prevent="submitForm" enctype="multipart/form-data" x-ref="form" x-data="customerForm"
    @close-cancel.window="handleCancel" @close-confirm.window="handleConfirm">
    @csrf
    @method($customer->id ? 'PUT' : 'POST')
    @php
        $textPrefix = $customer->id ? __('business.templates.edit') : __('business.checklist.add');
    @endphp
    <!-- Add Customer Information Section -->
    <div class="white-box shadow-sm py-[24px] px-[20px] rounded-[16px]">

        <div x-data="{ open: true }">
            <div @click="open = !open" class="accordian-header"
                :class="open ? 'manage-template-heading cursor-pointer show' : 'manage-template-heading cursor-pointer'">

                <div class="flex gap-[10px] items-center">
                    <span
                        class="m-0 min-w-[28px] w-[28px] h-[28px] rounded-[50%] bg-[#0D44EA] text-white text-[14px] font-[500] flex items-center justify-center">1</span>
                    <p class="m-0">{{ $textPrefix . ' ' . __('business.customer.sections.customer_info') }}</p>
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
                @if (!$customer->id)
                    <div class="mb-[24px]">
                        <x-form.label for="pool_type" required>{{ __('business.customers.pool_type') }}</x-form.label>
                        <div class="flex items-center gap-[17px] mt-3">
                            <div class="custom-radio-box">
                                <input type="radio" name="pool_type" x-model="pool_type" value="1" class="mr-2"
                                    id="pool_type_residential" :checked="pool_type === 1"
                                    @change="handlePoolTypeChange">
                                <label for="pool_type_residential"
                                    class="mr-2 text-gray-500">{{ __('business.customer.residential_pool') }}</label>
                            </div>
                            <div class="custom-radio-box">
                                <input type="radio" name="pool_type" x-model="pool_type" value="2" class="mr-2"
                                    id="pool_type_commercial" :checked="pool_type === 2" @change="handlePoolTypeChange">
                                <label for="pool_type_commercial"
                                    class="mr-2 text-gray-500">{{ __('business.customers.commercial_pool') }}</label>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="grid grid-cols-2 gap-[24px] max-[600px]:grid-cols-1">
                    <!-- First Name -->
                    <div>
                        <x-form.label for="first_name">
                            {{ __('business.customer.first_name') }}
                            <span x-show="pool_type == 1">*</span>
                        </x-form.label>
                        <x-form.text class="mt-2" name="first_name" id="first_name" x-model="first_name"
                            x-ref="first_name" :value="old('first_name', $customer->first_name)"
                            placeholder="{{ __('business.customer.placeholders.first_name') }}"
                            x-bind:required="pool_type == 1"
                            x-bind:class="{ 'error-message-border': errors.first_name }" />
                        <div x-show="errors.first_name" x-text="errors.first_name" class="error-message-box first_name">
                        </div>
                        @error('first_name')
                            <div class="error-message-box first_name">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Last Name -->
                    <div>
                        <x-form.label for="last_name">
                            {{ __('business.customer.last_name') }}
                            <span x-show="pool_type == 1">*</span>
                        </x-form.label>
                        <x-form.text class="mt-2" name="last_name" id="last_name" x-model="last_name"
                            x-ref="last_name" :value="old('last_name', $customer->last_name)"
                            placeholder="{{ __('business.customer.placeholders.last_name') }}"
                            x-bind:required="pool_type == 1"
                            x-bind:class="{ 'error-message-border': errors.last_name }" />
                        <div x-show="errors.last_name" x-text="errors.last_name" class="error-message-box last_name">
                        </div>
                        @error('last_name')
                            <div class="error-message-box last_name">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Commercial Pool Details -->
                <div class="col-span-2 mt-[30px]" x-show="pool_type == 2" x-cloak>
                    <div class="flex min-[768px]:w-[calc(50%-12px)] max-[767px]:w-full flex-col">
                        <x-form.label for="customer_commercial_pool_details"
                            required>{{ __('business.customer.commercial_company_name') }}</x-form.label>
                        <x-form.text class="mt-2" name="commercial_pool_details" id="customer_commercial_pool_details"
                            x-model="commercial_pool_details" x-ref="commercial_pool_details" :value="old('commercial_pool_details', $customer->commercial_pool_details)"
                            rows="3" x-bind:class="{ 'error-message-border': errors.commercial_pool_details }"
                            placeholder="{{ __('business.customer.placeholders.commercial_company_name') }}" />
                        <div x-show="errors.commercial_pool_details" x-text="errors.commercial_pool_details"
                            class="error-message-box commercial_pool_details"></div>
                        @error('commercial_pool_details')
                            <div class="error-message-box commercial_pool_details">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-[24px] max-[600px]:grid-cols-1 mt-[30px]">
                    <!-- Email Address 1 -->
                    <div>
                        <x-form.label for="customer_email_1"
                            required>{{ __('business.customer.email_1') }}</x-form.label>
                        <x-form.text class="mt-2" name="email_1" id="customer_email_1" x-model="email_1"
                            x-ref="email_1" :value="old('email_1', $customer->email_1)"
                            placeholder="{{ __('business.customer.placeholders.email_1') }}"
                            x-bind:class="{ 'error-message-border': errors.email_1 }" />
                        <div x-show="errors.email_1" x-text="errors.email_1" class="error-message-box email_1"></div>
                        @error('email_1')
                            <div class="error-message-box email_1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email Address 2 -->
                    <div>
                        <x-form.label for="customer_email_2">{{ __('business.customer.email_2') }}</x-form.label>
                        <x-form.text class="mt-2" name="email_2" id="customer_email_2" x-model="email_2"
                            x-ref="email_2" :value="old('email_2', $customer->email_2)"
                            placeholder="{{ __('business.customer.placeholders.email_2') }}"
                            x-bind:class="{ 'error-message-border': errors.email_2 }" />
                        <div x-show="errors.email_2" x-text="errors.email_2" class="error-message-box email_2"></div>
                        @error('email_2')
                            <div class="error-message-box email_2">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-[24px] max-[600px]:grid-cols-1 mt-[30px]">
                    <!-- Phone Number 1 -->
                    <div>
                        <x-form.label for="customer_phone_1"
                            required>{{ __('business.customer.phone_1') }}</x-form.label>
                        <div class="flex items-center gap-2 mt-2">
                            <div class="code-inputs">
                                <x-form.text name="isd_code"
                                    value="{{ old('isd_code', $customer->isd_code ?? $businessCountry->isd_code) }}"
                                    readonly />
                            </div>

                            <x-form.text class="mt-0 w-full" name="phone_1" id="customer_phone_1" x-model="phone_1"
                                x-ref="phone_1" :value="old('phone_1', $customer->phone_1)"
                                placeholder="{{ __('business.customer.placeholders.phone_1') }}"
                                x-bind:class="{ 'error-message-border': errors.phone_1 }" />
                        </div>
                        <div x-show="errors.phone_1" x-text="errors.phone_1" class="error-message-box phone_1"></div>
                        @error('phone_1')
                            <div class="error-message-box phone_1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Phone Number 2 -->
                    <div>
                        <x-form.label for="customer_phone_2">{{ __('business.customer.phone_2') }}</x-form.label>
                        <div class="flex items-center gap-2 mt-2">
                            <div class="code-inputs">
                                <x-form.text name="isd_code"
                                    value="{{ old('isd_code', $customer->isd_code ?? $businessCountry->isd_code) }}"
                                    readonly />
                            </div>

                            <x-form.text class="mt-0 w-full" name="phone_2" id="customer_phone_2" x-model="phone_2"
                                x-ref="phone_2" :value="old('phone_2', $customer->phone_2)"
                                placeholder="{{ __('business.customer.placeholders.phone_2') }}" />
                        </div>
                        <div x-show="errors.phone_2" x-text="errors.phone_2" class="error-message-box phone_2"></div>
                        @error('phone_2')
                            <div class="error-message-box phone_2">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Address Details Section -->
    <div class="white-box shadow-sm py-[24px] px-[20px] rounded-[16px]">
        <div x-data="{ open: true }">
            <div @click="open = !open" class="accordian-header"
                :class="open ? 'manage-template-heading cursor-pointer show' : 'manage-template-heading cursor-pointer'">
                <div class="flex gap-[10px] items-center">
                    <span
                        class="m-0 min-w-[28px] w-[28px] h-[28px] rounded-[50%] bg-[#0D44EA] text-white text-[14px] font-[500] flex items-center justify-center">2</span>
                    <p class="m-0">{{ $textPrefix . ' ' . __('business.customer.sections.address_details') }}</p>

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
                <div class="grid grid-cols-2 gap-[30px] max-[600px]:grid-cols-1">
                    <!-- Address -->
                    <div>
                        <x-form.label for="customer_address"
                            required>{{ __('business.customer.address') }}</x-form.label>
                        <x-form.text class="mt-2" name="address" id="customer_address" x-model="address"
                            x-ref="address" :value="old('address', $customer->address)"
                            placeholder="{{ __('business.customer.placeholders.address') }}"
                            x-bind:class="{ 'error-message-border': errors.address }" />
                        <div x-show="errors.address" x-text="errors.address" class="error-message-box address"></div>
                        @error('address')
                            <div class="error-message-box address">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Additional address -->
                    <div>
                        <x-form.label for="customer_street" >{{ __('business.customer.additional_address') }}</x-form.label>
                        <x-form.text class="mt-2" name="street" id="customer_street" x-model="street"
                            x-ref="street" :value="old('street', $customer->street)"
                            placeholder="{{ __('business.customer.placeholders.street') }}"
                            x-bind:class="{ 'error-message-border': errors.street }" />
                        <div x-show="errors.street" x-text="errors.street" class="error-message-box street"></div>
                        @error('street')
                            <div class="error-message-box street">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Zip Code -->
                    <div>
                        <x-form.label for="customer_zip_code"
                            required>{{ __('business.customer.zip_code') }}</x-form.label>
                        <x-form.text class="mt-2" name="zip_code" id="customer_zip_code" x-model="zip_code"
                            x-ref="zip_code" :value="old('zip_code', $customer->zip_code)"
                            placeholder="{{ __('business.customer.placeholders.zip_code') }}"
                            x-bind:class="{ 'error-message-border': errors.zip_code }" />
                        <div x-show="errors.zip_code" x-text="errors.zip_code" class="error-message-box zip_code">
                        </div>
                        @error('zip_code')
                            <div class="error-message-box zip_code">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Country -->
                    <div>
                        <x-form.label for="country"
                            required>{{ __('business.customer.select_country') }}</x-form.label>
                        <select name="country_id" id="country" class="form-select mt-2 w-full"
                            x-model="country_id" @change="handleCountryChange"
                            x-bind:class="{ 'error-message-border': errors.country_id }">
                            <option value="">{{ __('business.customer.select_country') }}</option>
                            @foreach ($countries as $country)
                                <option value="{{ $country->id }}" data-isd-code="{{ $country->isd_code }}"
                                    {{ old('country_id', $customer->country_id ?? $businessCountry->id) == $country->id ? 'selected' : '' }}>
                                    {{ $country->name }}
                                </option>
                            @endforeach
                        </select>
                        <div x-show="errors.country_id" x-text="errors.country_id"
                            class="error-message-box country_id">
                        </div>
                        @error('country_id')
                            <div class="error-message-box country_id">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- State -->
                    <div>
                        <x-form.label for="customer_state" required>{{ __('business.customer.state') }}</x-form.label>
                        <x-form.text class="mt-2" name="state" id="customer_state" x-model="state"
                            x-ref="state" :value="old('state', $customer->state)"
                            placeholder="{{ __('business.customer.placeholders.state') }}"
                            x-bind:class="{ 'error-message-border': errors.state }" />
                        <div x-show="errors.state" x-text="errors.state" class="error-message-box state"></div>
                        @error('state')
                            <div class="error-message-box state">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- City -->
                    <div>
                        <x-form.label for="customer_city" required>{{ __('business.customer.city') }}</x-form.label>
                        <x-form.text class="mt-2" name="city" id="customer_city" x-model="city" x-ref="city"
                            :value="old('city', $customer->city)" placeholder="{{ __('business.customer.placeholders.city') }}"
                            x-bind:class="{ 'error-message-border': errors.city }" />
                        <div x-show="errors.city" x-text="errors.city" class="error-message-box city"></div>
                        @error('city')
                            <div class="error-message-box city">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Technician Notes Section -->

    </div>
    <div class="white-box shadow-sm py-[24px] px-[20px] rounded-[16px]">
        <div x-data="{ open: true }">
            <div @click="open = !open" class="accordian-header"
                :class="open ? 'manage-template-heading cursor-pointer show' : 'manage-template-heading cursor-pointer'">
                <div class="flex gap-[10px] items-center">
                    <span
                        class="m-0 min-w-[28px] w-[28px] h-[28px] rounded-[50%] bg-[#0D44EA] text-white text-[14px] font-[500] flex items-center justify-center">3</span>
                    <p class="m-0">
                        {{ $textPrefix !== 'Add' ? $textPrefix . ' ' . __('business.customer.technician_notes') : __('business.customer.technician_notes') }}
                    </p>
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
                <!-- Pool Size -->
                <div class="mb-6">
                    <div class="grid grid-cols-2 gap-[24px] max-[600px]:grid-cols-1 max-[767px]:gap-[12px]">
                        <div>
                            <x-form.label>{{ __('business.customer.pool_size_gallons') }}</x-form.label>
                            <x-form.text class="mt-2" name="pool_size_gallons" id="pool_size"
                                x-model.number="pool_size_gallons" x-ref="pool_size_gallons" :value="old('pool_size_gallons', $customer->pool_size_gallons)"
                                placeholder="{{ __('business.customer.placeholders.pool_size') }}" />
                            <div x-show="errors.pool_size_gallons" x-text="errors.pool_size_gallons"
                                class="error-message-box pool_size_gallons"></div>
                            @error('pool_size_gallons')
                                <div class="error-message-box pool_size_gallons">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="text-[14px] font-[400] italic text-[#474747] max-[767px]:flex min-[768px]:hidden">
                            {{ __('business.customer.or') }}</div>
                        <div>
                            <x-form.label>{{ __('business.customer.pool_size_area') }}</x-form.label>
                            <div class="flex items-center gap-4">
                                <span
                                    class="text-[14px] font-[400] italic text-[#474747] min-[767px]:flex max-[768px]:hidden mt-2">{{ __('business.customer.or') }}</span>
                                <div class="flex-1 grid grid-cols-3 gap-4">
                                    <div>
                                        <x-form.text class="mt-2" name="pool_length" id="pool_length"
                                            x-model.number="pool_length" x-ref="pool_length" :value="old('pool_length', $customer->pool_length)"
                                            placeholder="{{ __('business.customer.pool_length') }}"
                                            @input="calculatePoolSize" />
                                        <div x-show="errors.pool_length" x-text="errors.pool_length"
                                            class="error-message-box pool_length"></div>
                                        @error('pool_length')
                                            <div class="error-message-box pool_length">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div>
                                        <x-form.text class="mt-2" name="pool_width" id="pool_width"
                                            x-model.number="pool_width" x-ref="pool_width" :value="old('pool_width', $customer->pool_width)"
                                            placeholder="{{ __('business.customer.pool_width') }}"
                                            @input="calculatePoolSize" />
                                        <div x-show="errors.pool_width" x-text="errors.pool_width"
                                            class="error-message-box pool_width"></div>
                                        @error('pool_width')
                                            <div class="error-message-box pool_width">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div>
                                        <x-form.text class="mt-2" name="pool_depth" id="pool_depth"
                                            x-model.number="pool_depth" x-ref="pool_depth" :value="old('pool_depth', $customer->pool_depth)"
                                            placeholder="{{ __('business.customer.pool_depth') }}"
                                            @input="calculatePoolSize" />
                                        <div x-show="errors.pool_depth" x-text="errors.pool_depth"
                                            class="error-message-box pool_depth"></div>
                                        @error('pool_depth')
                                            <div class="error-message-box pool_depth">{{ $message }}</div>
                                        @enderror

                                    </div>
                                </div>
                            </div>
                            <p class="flex items-center text-[14px] italic text-[#5A5A5A] font-[400] mt-2 min-[768px]:pl-[36px]">
                                {{ __('business.customer.pool_size_formula') }}</p>

                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-[24px] mt-[30px] max-[767px]:grid-cols-1">
                    <div>
                        <x-form.label for="clean_psi">{{ __('business.customer.clean_psi') }}</x-form.label>
                        <div class="space-y-2">
                            <x-form.text class="mt-2" name="clean_psi" id="clean_psi" x-model="clean_psi"
                                :value="old('clean_psi', $customer->clean_psi)" placeholder="{{ __('business.customer.clean_psi_details') }}"
                                x-bind:class="{ 'error-message-border': errors.clean_psi }" />
                            <div x-show="errors.clean_psi" x-text="errors.clean_psi"
                                class="error-message-box clean_psi"></div>
                            @error('clean_psi')
                                <div class="error-message-box clean_psi">{{ $message }}</div>
                            @enderror

                            <div class="relative" x-data="customerImageUpload" data-field="clean_psi_image"
                                data-thumb-field="clean_psi_image_thumb">
                                <div x-show="file || existingPhoto" class="flex items-center justify-between">
                                    <x-form.label>{{ __('business.customer.uploaded_photo') }}</x-form.label>
                                    <button type="button"
                                        class="text-[12px] font-[400] text-[#2563EB] italic underline"
                                        @click="$refs[fieldName].click()">
                                        {{ __('business.work_orders.sections.change_photo') }}
                                    </button>
                                </div>

                                <!-- Show this when there's an existing or selected photo -->
                                <div x-show="file || existingPhoto"
                                    class="relative border-[2px] border-dashed border-[#E5E7EB] py-[14px] px-[24px] rounded-[12px] mt-2">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 bg-gray-100 rounded-lg overflow-hidden cursor-pointer"
                                            @click="$dispatch('open-modal', {
                                                id: 'image-preview-modal',
                                                url: file ? file.preview : ( existingPhotoUrl || existingPhotoThumbUrl)
                                            })">
                                            <img :src="file ? file.preview : (existingPhotoUrl || existingPhotoThumbUrl)"
                                                class="w-full h-full object-cover" alt="Clean PSI">
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-[12px] font-[600] text-[#0B0B0B]"
                                                x-text="file ? file.name : existingPhotoName"></p>
                                            <p class="text-[14px] font-[400] text-[#767676]"
                                                x-text="file ? formatFileSize(file.file.size) : ''"></p>
                                        </div>
                                        <button type="button" class="cursor-pointer"
                                            @click="removeFile(); markPhotoForDeletion();">
                                            <svg width="25" height="24" viewBox="0 0 25 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M15.5 9L9.5 15M9.5 9L15.5 15M22.5 12C22.5 17.5228 18.0228 22 12.5 22C6.97715 22 2.5 17.5228 2.5 12C2.5 6.47715 6.97715 2 12.5 2C18.0228 2 22.5 6.47715 22.5 12Z"
                                                    stroke="#0D44EA" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- Show this when no photo is selected -->
                                <div x-show="!file && !existingPhoto">
                                    <input type="file" name="clean_psi_image" id="clean_psi_image"
                                        x-ref="clean_psi_image" accept="image/*" class="hidden"
                                        @change="handleFileSelect($event)"
                                        x-bind:class="{ 'error-message-border': errors.clean_psi_image }">
                                    <button type="button"
                                        class="flex items-center text-[14px] italic text-[#5A5A5A] font-[400]"
                                        @click="$refs[fieldName].click()">
                                        <span class="mr-1">+</span>
                                        {{ __('business.customer.attach_photo') }}
                                    </button>
                                </div>

                                <div x-show="errorMessage" x-text="errorMessage" class="error-message-box"></div>
                                <div x-show="errors.clean_psi_image" x-text="errors.clean_psi_image"
                                    class="error-message-box clean_psi_image"></div>
                                @error('clean_psi_image')
                                    <div class="error-message-box clean_psi_image">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Entry Instruction -->
                <div class="grid grid-cols-2 gap-[24px] mt-[30px] max-[767px]:grid-cols-1">
                    <div>
                        <x-form.label
                            for="customer_entry_instruction">{{ __('business.customer.entry_instruction') }}</x-form.label>
                        <x-form.text class="mt-2" name="entry_instruction" id="customer_entry_instruction"
                            x-model="entry_instruction" x-ref="entry_instruction" :value="old('entry_instruction', $customer->entry_instruction)" rows="4"
                            placeholder="{{ __('business.customer.placeholders.entry_instruction') }}" />
                        <div x-show="errors.entry_instruction" x-text="errors.entry_instruction"
                            class="error-message-box entry_instruction"></div>
                        @error('entry_instruction')
                            <div class="error-message-box entry_instruction">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-[24px] mt-[30px] max-[767px]:grid-cols-1">
                    <div>
                        <x-form.label
                            for="customer_tech_notes">{{ __('business.customer.tech_notes') }}</x-form.label>
                        <x-form.textarea class="mt-2" name="tech_notes" id="customer_tech_notes"
                            x-model="tech_notes" x-ref="tech_notes" :value="old('tech_notes', $customer->tech_notes)" rows="4"
                            placeholder="{{ __('business.customer.placeholders.tech_notes') }}" />
                        <div x-show="errors.tech_notes" x-text="errors.tech_notes"
                            class="error-message-box tech_notes"></div>
                        @error('tech_notes')
                            <div class="error-message-box tech_notes">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <x-form.label
                            for="customer_admin_notes">{{ __('business.customer.admin_notes') }}</x-form.label>
                        <x-form.textarea class="mt-2" name="admin_notes" id="customer_admin_notes"
                            x-model="admin_notes" x-ref="admin_notes" :value="old('admin_notes', $customer->admin_notes)" rows="4"
                            placeholder="{{ __('business.customer.placeholders.admin_notes') }}" />
                        <div x-show="errors.admin_notes" x-text="errors.admin_notes"
                            class="error-message-box admin_notes"></div>
                        @error('admin_notes')
                            <div class="error-message-box admin_notes">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Status -->

                </div>
                <div class="grid grid-cols-2 gap-[24px] max-[600px]:grid-cols-1 mt-[30px]">
                    <h3 class="text-[16px] font-[600] text-[#212529] leading-[21px] mb-[0px]">Pool Item List</h3>
                </div>

                <!-- Pool Equipment -->
                <div class="grid grid-cols-2 gap-[24px] max-[600px]:grid-cols-1 mt-[16px]">
                    @foreach ([
        'filter' => ['label' => __('business.customer.filter'), 'name' => 'filter_details', 'image' => 'filter_image', 'image_thumb' => 'filter_image_thumb'],
        'pump' => ['label' => __('business.customer.pump'), 'name' => 'pump_details', 'image' => 'pump_image', 'image_thumb' => 'pump_image_thumb'],
        'cleaner' => ['label' => __('business.customer.cleaner'), 'name' => 'cleaner_details', 'image' => 'cleaner_image', 'image_thumb' => 'cleaner_image_thumb'],
        'heater' => ['label' => __('business.customer.heater'), 'name' => 'heater_details', 'image' => 'heater_image', 'image_thumb' => 'heater_image_thumb'],
        'heat_pump' => ['label' => __('business.customer.heat_pump'), 'name' => 'heat_pump_details', 'image' => 'heat_pump_image', 'image_thumb' => 'heat_pump_image_thumb'],
        'aux' => ['label' => __('business.customer.aux'), 'name' => 'aux_details', 'image' => 'aux_image', 'image_thumb' => 'aux_image_thumb'],
        'aux2' => ['label' => __('business.customer.aux2'), 'name' => 'aux2_details', 'image' => 'aux2_image', 'image_thumb' => 'aux2_image_thumb'],
        'salt_system' => ['label' => __('business.customer.salt_system'), 'name' => 'salt_system_details', 'image' => 'salt_system_image', 'image_thumb' => 'salt_system_image_thumb'],
    ] as $key => $item)
                        <div>
                            <x-form.label for="{{ $item['name'] }}">{{ $item['label'] }}</x-form.label>
                            <div class="space-y-2">
                                <x-form.text class="mt-2" name="{{ $item['name'] }}" id="{{ $item['name'] }}"
                                    x-model="{{ $item['name'] }}" x-ref="{{ $item['name'] }}" :value="old($item['name'], $customer->{$item['name']})"
                                    placeholder="{{ __($item['label'] . ' Details') }}"
                                    x-bind:class="{ 'error-message-border': errors.{{ $item['name'] }} }" />
                                <div x-show="errors.{{ $item['name'] }}" x-text="errors.{{ $item['name'] }}"
                                    class="error-message-box {{ $item['name'] }}"></div>
                                @error($item['name'])
                                    <div class="error-message-box {{ $item['name'] }}">{{ $message }}</div>
                                @enderror

                                <div class="relative" x-data="customerImageUpload" data-field="{{ $item['image'] }}"
                                    data-thumb-field="{{ $item['image_thumb'] }}">
                                    <div x-show="file || existingPhoto" class="flex items-center justify-between">
                                        <x-form.label>{{ __('business.customer.uploaded_photo') }}</x-form.label>
                                        <button type="button"
                                            class="text-[12px] font-[400] text-[#2563EB] italic underline"
                                            @click="$refs[fieldName].click()">
                                            {{ __('business.work_orders.sections.change_photo') }}
                                        </button>
                                    </div>

                                    <!-- Show this when there's an existing or selected photo -->
                                    <div x-show="file || existingPhoto"
                                        class="relative border-[2px] border-dashed border-[#E5E7EB] py-[14px] px-[24px] rounded-[12px] mt-2">
                                        <div class="flex items-center gap-3">
                                            <div class="w-12 h-12 bg-gray-100 rounded-lg overflow-hidden cursor-pointer"
                                                @click="$dispatch('open-modal', {
                                                    id: 'image-preview-modal',
                                                    url: file ? file.preview : (existingPhotoThumbUrl || existingPhotoUrl)
                                                })">
                                                <img :src="file ? file.preview : (existingPhotoThumbUrl || existingPhotoUrl)"
                                                    class="w-full h-full object-cover" :alt="'{{ $item['label'] }}'">
                                            </div>
                                            <div class="flex-1">
                                                <p class="text-[12px] font-[600] text-[#0B0B0B]"
                                                    x-text="file ? file.name : existingPhotoName"></p>
                                                <p class="text-[14px] font-[400] text-[#767676]"
                                                    x-text="file ? formatFileSize(file.file.size) : ''"></p>
                                            </div>
                                            <button type="button" class="cursor-pointer"
                                                @click="removeFile(); markPhotoForDeletion();">
                                                <svg width="25" height="24" viewBox="0 0 25 24"
                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M15.5 9L9.5 15M9.5 9L15.5 15M22.5 12C22.5 17.5228 18.0228 22 12.5 22C6.97715 22 2.5 17.5228 2.5 12C2.5 6.47715 6.97715 2 12.5 2C18.0228 2 22.5 6.47715 22.5 12Z"
                                                        stroke="#0D44EA" stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Show this when no photo is selected -->
                                    <div x-show="!file && !existingPhoto">
                                        <input type="file" name="{{ $item['image'] }}"
                                            id="{{ $item['image'] }}" x-ref="{{ $item['image'] }}"
                                            accept="image/*" class="hidden mt-2" @change="handleFileSelect($event)"
                                            x-bind:class="{ 'error-message-border': errors.{{ $item['image'] }} }">
                                        <button type="button"
                                            class="flex items-center text-[14px] italic text-[#5A5A5A] font-[400]"
                                            @click="$refs[fieldName].click()">
                                            <span class="mr-1">+</span>
                                            {{ __('business.customer.attach_photo') }}
                                        </button>
                                    </div>

                                    <div x-show="errorMessage" x-text="errorMessage" class="error-message-box"></div>
                                    <div x-show="errors.{{ $item['image'] }}" x-text="errors.{{ $item['image'] }}"
                                        class="error-message-box {{ $item['image'] }}"></div>
                                    @error($item['image'])
                                        <div class="error-message-box {{ $item['image'] }}">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @if ($customer->id)
        <div class="col-span-2 mt-[36px]">
            <x-form.label for="status" required>{{ __('business.customer.status.label') }}</x-form.label>
            <div class="flex items-center gap-[17px] mt-3">
                <div class="custom-radio-box">
                    <input type="radio" name="status" x-model.number="status" value="1" class="mr-2"
                        id="status_active" :checked="status === 1" @change="showConfirm=false;">
                    <label for="status_active"
                        class="mr-2 text-gray-500">{{ __('business.customer.status.active') }}</label>
                </div>
                <div class="custom-radio-box">
                    <input type="radio" name="status" x-model.number="status" value="0" class="mr-2"
                        id="status_inactive" :checked="status === 0" @change="showConfirm=true">
                    <label for="status_inactive"
                        class="mr-2 text-gray-500">{{ __('business.customer.status.inactive') }}</label>
                </div>
            </div>
        </div>
    @endif
    <!-- Form Actions -->
    <div class="flex gap-4 mt-[36px] max-[640px]:flex-col">
        @if ($customer->id)
             <button type="button" value="save" class="btn-box btn"
                x-bind:disabled="processing"
                x-bind:class="{'opacity-75 cursor-not-allowed': processing}"
                x-data="{ processing: false }"
                @click="$event.preventDefault(); action = 'save'; submitWithAction('save')">
                {{ __('business.customer.update') }}
                <x-form.loading/>
             </button>
        @else
            <button type="button" value="save" class="btn-box btn"
                x-bind:disabled="processing"
                x-bind:class="{'opacity-75 cursor-not-allowed': processing}"
                x-data="{ processing: false }"
                @click="$event.preventDefault(); action = 'save'; submitWithAction('save')">
                {{ __('business.customer.save_customer') }}
                <x-form.loading/>
            </button>
            <button type="button" value="save_and_create_work_order" class="btn-box btn"
                x-bind:disabled="processing"
                x-bind:class="{'opacity-75 cursor-not-allowed': processing}"
                x-data="{ processing: false }"
                @click="$event.preventDefault(); action = 'save_and_create_work_order'; submitWithAction('save_and_create_work_order')">
                {{ __('business.customer.save_and_create_work_order') }}
                <x-form.loading/>
            </button>
        @endif
        <input type="hidden" name="action" x-ref="action" x-model="action">
        <a href="{{ route('business.customers.index') }}"
            class="btn-box outlined">{{ __('business.customer.cancel') }}</a>
    </div>

    <!-- Inactive confirmation modal (teleported to body but stays in this Alpine scope) -->
    <template x-teleport="body">
        @include('components.confirm.confirm-modal', [
            'title' => __('business.customer.confirm_deactivate'),
            'description' => __('business.customer.confirm_deactivate_message'),
        ])
    </template>
</form>

<!-- Add the Image Preview Modal Component -->
<x-modal.image-preview />
