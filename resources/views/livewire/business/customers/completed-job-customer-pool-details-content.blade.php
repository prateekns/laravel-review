@php
    use Illuminate\Support\Facades\Storage;
@endphp

<!-- Pool Details Section -->
<h3 class="font-[400] text-[14px] mb-[4px]">{{ __('business.customer.pool_details') }}</h3>


<div class="pool-details-wrapper grid grid-cols-2 max-[1199px]:grid-cols-1">
    <!-- Left Column -->

    <div class="pool-detail-boxes flex-col">
        <div class="flex  w-[100%]" id="pool-size">
            <span
                class="text-[16px] font-[400] text-[#000000] max-[767px]:text-[14px] max-[767px]:font-[600] w-[50%] max-[769px]:text-[14px]">{{ __('business.work_orders.pool_size') }}:</span>
            <span
                class="text-[16px] font-[400] text-[#000000] max-[767px]:text-[14px] max-[767px]:font-[600] w-[50%] max-[769px]:text-[14px] max-[769px]:text-right max-[767px]:font-[600]">{{ $completedJobCustomer['pool_size_gallons'] ? $completedJobCustomer['pool_size_gallons'] . ' ' . __('business.work_orders.gallons') : '' }}</span>
        </div>
        <div class="flex  w-[100%]" id="pool-type">
            <span
                class="text-[16px] font-[400] text-[#000000] max-[767px]:text-[14px] max-[767px]:font-[600] w-[50%] max-[769px]:text-[14px]">{{ __('business.work_orders.pool_type') }}:</span>
            <span
                class="text-[16px] font-[400] text-[#000000] max-[767px]:text-[14px] max-[767px]:font-[600] w-[50%] max-[769px]:text-[14px] max-[769px]:text-right max-[767px]:font-[600]">{{ $completedJobCustomer['pool_type'] }}</span>
        </div>

        <div class="flex  w-[100%]" id="clean-psi">
            <span
                class="text-[16px] font-[400] text-[#000000] max-[767px]:text-[14px] max-[767px]:font-[600] w-[50%] max-[769px]:text-[14px]">{{ __('business.work_orders.clean_psi') }}:</span>
            <span
                class="text-[16px] font-[400] text-[#000000] max-[767px]:text-[14px] max-[767px]:font-[600] w-[50%] max-[769px]:text-[14px] max-[769px]:text-right max-[767px]:font-[600]">{{ $completedJobCustomer['clean_psi'] }}</span>
        </div>

        <div class="flex  w-[100%]" id="entry-instructions">
            <span
                class="text-[16px] font-[400] text-[#000000] max-[767px]:text-[14px] max-[767px]:font-[600] w-[50%] max-[769px]:text-[14px]">{{ __('business.customer.entry_instruction') }}:</span>
            <span
                class="text-[16px] font-[400] text-[#000000] max-[767px]:text-[14px] max-[767px]:font-[600] w-[50%] max-[769px]:text-[14px] max-[769px]:text-right max-[767px]:font-[600]">{{ $completedJobCustomer['entry_instruction'] }}</span>
        </div>

        <div class="flex  w-[100%]" id="tech-note">
            <span
                class="text-[16px] font-[400] text-[#000000] max-[767px]:text-[14px] max-[767px]:font-[600] w-[50%] max-[769px]:text-[14px]">{{ __('business.work_orders.tech_notes') }}:</span>
            <span
                class="text-[16px] font-[400] text-[#000000] max-[767px]:text-[14px] max-[767px]:font-[600] w-[50%] max-[769px]:text-[14px] max-[769px]:text-right max-[767px]:font-[600]">{{ $completedJobCustomer['tech_notes'] }}</span>
        </div>

        <div class="flex  w-[100%]" id="admin-note">
            <span
                class="text-[16px] font-[400] text-[#000000] max-[767px]:text-[14px] max-[767px]:font-[600] w-[50%] max-[769px]:text-[14px]">{{ __('business.work_orders.admin_notes') }}:</span>
            <span
                class="text-[16px] font-[400] text-[#000000] max-[767px]:text-[14px] max-[767px]:font-[600] w-[50%] max-[769px]:text-[14px] max-[769px]:text-right max-[767px]:font-[600]">{{ $completedJobCustomer['admin_notes'] }}</span>
        </div>
    </div>

    <!-- Right Column - Equipment Details -->
    <div class="pool-detail-boxes flex-col">
        <div class="flex  w-[100%]" id="equipment-filter">
            <span
                class="text-[16px] font-[400] text-[#000000] max-[767px]:text-[14px] max-[767px]:font-[600] w-[30%] max-[767px]:text-[14px] max-[767px]:pr-[5px] break-auto-phrase">{{ __('business.customer.filter') }}:</span>
            <div class="flex items-center gap-2 w-[70%] justify-between">
                <span
                    class="text-[16px] font-[400] text-[#000000] max-[767px]:text-[14px] max-[767px]:font-[600] ">{{ $completedJobCustomer['filter_details'] }}</span>
                @if (!empty($completedJobCustomer['filter_image']))
                    <button type="button"
                        class="text-[12px] font-[400] text-[#086DF1] cursor-pointer whitespace-nowrap view-image-btn"
                        x-data
                        @click="$dispatch('open-modal', {
                                id: 'image-preview-modal',
                                url: '{{ $completedJobCustomer->filter_image }}'
                            })">
                        <span class="min-[768px]:inline-block max-[768px]:hidden">
                            {{ __('business.work_orders.view_image') }} </span>
                        <span class="max-[767px]:inline-block min-[768px]:hidden"><svg width="24" height="24"
                                viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M16.2 21H6.93137C6.32555 21 6.02265 21 5.88238 20.8802C5.76068 20.7763 5.69609 20.6203 5.70865 20.4608C5.72312 20.2769 5.93731 20.0627 6.36569 19.6343L14.8686 11.1314C15.2646 10.7354 15.4627 10.5373 15.691 10.4632C15.8918 10.3979 16.1082 10.3979 16.309 10.4632C16.5373 10.5373 16.7354 10.7354 17.1314 11.1314L21 15V16.2M16.2 21C17.8802 21 18.7202 21 19.362 20.673C19.9265 20.3854 20.3854 19.9265 20.673 19.362C21 18.7202 21 17.8802 21 16.2M16.2 21H7.8C6.11984 21 5.27976 21 4.63803 20.673C4.07354 20.3854 3.6146 19.9265 3.32698 19.362C3 18.7202 3 17.8802 3 16.2V7.8C3 6.11984 3 5.27976 3.32698 4.63803C3.6146 4.07354 4.07354 3.6146 4.63803 3.32698C5.27976 3 6.11984 3 7.8 3H16.2C17.8802 3 18.7202 3 19.362 3.32698C19.9265 3.6146 20.3854 4.07354 20.673 4.63803C21 5.27976 21 6.11984 21 7.8V16.2M10.5 8.5C10.5 9.60457 9.60457 10.5 8.5 10.5C7.39543 10.5 6.5 9.60457 6.5 8.5C6.5 7.39543 7.39543 6.5 8.5 6.5C9.60457 6.5 10.5 7.39543 10.5 8.5Z"
                                    stroke="#0D44EA" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                    </button>
                @endif
            </div>
        </div>
        <div class="flex  w-[100%]" id="equipment-pump">
            <span
                class="text-[16px] font-[400] text-[#000000] max-[767px]:text-[14px] max-[767px]:font-[600] w-[30%] max-[767px]:text-[14px] max-[767px]:pr-[5px] break-auto-phrase">{{ __('business.customer.pump') }}:</span>
            <div class="flex items-center gap-2 w-[70%] justify-between">
                <span
                    class="text-[16px] font-[400] text-[#000000] max-[767px]:text-[14px] max-[767px]:font-[600]">{{ $completedJobCustomer['pump_details'] }}</span>
                @if (!empty($completedJobCustomer['pump_image']))
                    <button type="button"
                        class="text-[12px] font-[400] text-[#086DF1] cursor-pointer whitespace-nowrap view-image-btn"
                        x-data
                        @click="$dispatch('open-modal', {
                                id: 'image-preview-modal',
                                url: '{{ $completedJobCustomer->pump_image }}'
                            })">
                        <span class="min-[768px]:inline-block max-[768px]:hidden">
                            {{ __('business.work_orders.view_image') }} </span>
                        <span class="max-[767px]:inline-block min-[768px]:hidden"><svg width="24" height="24"
                                viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M16.2 21H6.93137C6.32555 21 6.02265 21 5.88238 20.8802C5.76068 20.7763 5.69609 20.6203 5.70865 20.4608C5.72312 20.2769 5.93731 20.0627 6.36569 19.6343L14.8686 11.1314C15.2646 10.7354 15.4627 10.5373 15.691 10.4632C15.8918 10.3979 16.1082 10.3979 16.309 10.4632C16.5373 10.5373 16.7354 10.7354 17.1314 11.1314L21 15V16.2M16.2 21C17.8802 21 18.7202 21 19.362 20.673C19.9265 20.3854 20.3854 19.9265 20.673 19.362C21 18.7202 21 17.8802 21 16.2M16.2 21H7.8C6.11984 21 5.27976 21 4.63803 20.673C4.07354 20.3854 3.6146 19.9265 3.32698 19.362C3 18.7202 3 17.8802 3 16.2V7.8C3 6.11984 3 5.27976 3.32698 4.63803C3.6146 4.07354 4.07354 3.6146 4.63803 3.32698C5.27976 3 6.11984 3 7.8 3H16.2C17.8802 3 18.7202 3 19.362 3.32698C19.9265 3.6146 20.3854 4.07354 20.673 4.63803C21 5.27976 21 6.11984 21 7.8V16.2M10.5 8.5C10.5 9.60457 9.60457 10.5 8.5 10.5C7.39543 10.5 6.5 9.60457 6.5 8.5C6.5 7.39543 7.39543 6.5 8.5 6.5C9.60457 6.5 10.5 7.39543 10.5 8.5Z"
                                    stroke="#0D44EA" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                    </button>
                @endif
            </div>
        </div>
        <div class="flex  w-[100%]" id="equipment-heater">
            <span
                class="text-[16px] font-[400] text-[#000000] max-[767px]:text-[14px] max-[767px]:font-[600] w-[30%] max-[767px]:text-[14px] max-[767px]:pr-[5px] break-auto-phrase">{{ __('business.customer.heater') }}:</span>
            <div class="flex items-center gap-2 w-[70%] justify-between">
                <span
                    class="text-[16px] font-[400] text-[#000000] max-[767px]:text-[14px] max-[767px]:font-[600]">{{ $completedJobCustomer['heater_details'] }}</span>
                @if (!empty($completedJobCustomer['heater_image']))
                    <button type="button"
                        class="text-[12px] font-[400] text-[#086DF1] cursor-pointer whitespace-nowrap view-image-btn"
                        x-data
                        @click="$dispatch('open-modal', {
                                id: 'image-preview-modal',
                                url: '{{ $completedJobCustomer->heater_image }}'
                            })">
                        <span class="min-[768px]:inline-block max-[768px]:hidden">
                            {{ __('business.work_orders.view_image') }} </span>
                        <span class="max-[767px]:inline-block min-[768px]:hidden"><svg width="24" height="24"
                                viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M16.2 21H6.93137C6.32555 21 6.02265 21 5.88238 20.8802C5.76068 20.7763 5.69609 20.6203 5.70865 20.4608C5.72312 20.2769 5.93731 20.0627 6.36569 19.6343L14.8686 11.1314C15.2646 10.7354 15.4627 10.5373 15.691 10.4632C15.8918 10.3979 16.1082 10.3979 16.309 10.4632C16.5373 10.5373 16.7354 10.7354 17.1314 11.1314L21 15V16.2M16.2 21C17.8802 21 18.7202 21 19.362 20.673C19.9265 20.3854 20.3854 19.9265 20.673 19.362C21 18.7202 21 17.8802 21 16.2M16.2 21H7.8C6.11984 21 5.27976 21 4.63803 20.673C4.07354 20.3854 3.6146 19.9265 3.32698 19.362C3 18.7202 3 17.8802 3 16.2V7.8C3 6.11984 3 5.27976 3.32698 4.63803C3.6146 4.07354 4.07354 3.6146 4.63803 3.32698C5.27976 3 6.11984 3 7.8 3H16.2C17.8802 3 18.7202 3 19.362 3.32698C19.9265 3.6146 20.3854 4.07354 20.673 4.63803C21 5.27976 21 6.11984 21 7.8V16.2M10.5 8.5C10.5 9.60457 9.60457 10.5 8.5 10.5C7.39543 10.5 6.5 9.60457 6.5 8.5C6.5 7.39543 7.39543 6.5 8.5 6.5C9.60457 6.5 10.5 7.39543 10.5 8.5Z"
                                    stroke="#0D44EA" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                    </button>
                @endif
            </div>
        </div>
        <div class="flex  w-[100%]" id="equipment-cleaner">
            <span
                class="text-[16px] font-[400] text-[#000000] max-[767px]:text-[14px] max-[767px]:font-[600] w-[30%] max-[767px]:text-[14px] max-[767px]:pr-[5px] break-auto-phrase">{{ __('business.customer.cleaner') }}:</span>
            <div class="flex items-center gap-2 w-[70%] justify-between">
                <span
                    class="text-[16px] font-[400] text-[#000000] max-[767px]:text-[14px] max-[767px]:font-[600]">{{ $completedJobCustomer['cleaner_details'] }}</span>
                @if (!empty($completedJobCustomer['cleaner_image']))
                    <button type="button"
                        class="text-[12px] font-[400] text-[#086DF1] cursor-pointer whitespace-nowrap view-image-btn"
                        x-data
                        @click="$dispatch('open-modal', {
                                id: 'image-preview-modal',
                                url: '{{ $completedJobCustomer->cleaner_image }}'
                            })">
                        <span class="min-[768px]:inline-block max-[768px]:hidden">
                            {{ __('business.work_orders.view_image') }} </span>
                        <span class="max-[767px]:inline-block min-[768px]:hidden"><svg width="24" height="24"
                                viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M16.2 21H6.93137C6.32555 21 6.02265 21 5.88238 20.8802C5.76068 20.7763 5.69609 20.6203 5.70865 20.4608C5.72312 20.2769 5.93731 20.0627 6.36569 19.6343L14.8686 11.1314C15.2646 10.7354 15.4627 10.5373 15.691 10.4632C15.8918 10.3979 16.1082 10.3979 16.309 10.4632C16.5373 10.5373 16.7354 10.7354 17.1314 11.1314L21 15V16.2M16.2 21C17.8802 21 18.7202 21 19.362 20.673C19.9265 20.3854 20.3854 19.9265 20.673 19.362C21 18.7202 21 17.8802 21 16.2M16.2 21H7.8C6.11984 21 5.27976 21 4.63803 20.673C4.07354 20.3854 3.6146 19.9265 3.32698 19.362C3 18.7202 3 17.8802 3 16.2V7.8C3 6.11984 3 5.27976 3.32698 4.63803C3.6146 4.07354 4.07354 3.6146 4.63803 3.32698C5.27976 3 6.11984 3 7.8 3H16.2C17.8802 3 18.7202 3 19.362 3.32698C19.9265 3.6146 20.3854 4.07354 20.673 4.63803C21 5.27976 21 6.11984 21 7.8V16.2M10.5 8.5C10.5 9.60457 9.60457 10.5 8.5 10.5C7.39543 10.5 6.5 9.60457 6.5 8.5C6.5 7.39543 7.39543 6.5 8.5 6.5C9.60457 6.5 10.5 7.39543 10.5 8.5Z"
                                    stroke="#0D44EA" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </span>
                    </button>
                @endif
            </div>
        </div>
        <div class="flex w-[100%]" id="equipment-salt-system">
            <span
                class="text-[16px] font-[400] text-[#000000] max-[767px]:text-[14px] max-[767px]:font-[600] w-[30%] max-[767px]:text-[14px] max-[767px]:pr-[5px] break-auto-phrase">{{ __('business.work_orders.salt_system') }}:</span>
            <div class="flex items-center gap-2 w-[70%] justify-between">
                <span
                    class="text-[16px] font-[400] text-[#000000] max-[767px]:text-[14px] max-[767px]:font-[600]">{{ $completedJobCustomer['salt_system_details'] }}</span>
                @if (!empty($completedJobCustomer['salt_system_image']))
                    <button type="button"
                        class="text-[12px] font-[400] text-[#086DF1] cursor-pointer whitespace-nowrap view-image-btn"
                        x-data
                        @click="$dispatch('open-modal', {
                                id: 'image-preview-modal',
                                url: '{{ $completedJobCustomer->salt_system_image }}'
                            })">
                        <span class="min-[768px]:inline-block max-[768px]:hidden">
                            {{ __('business.work_orders.view_image') }} </span>
                        <span class="max-[767px]:inline-block min-[768px]:hidden"><svg width="24" height="24"
                                viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M16.2 21H6.93137C6.32555 21 6.02265 21 5.88238 20.8802C5.76068 20.7763 5.69609 20.6203 5.70865 20.4608C5.72312 20.2769 5.93731 20.0627 6.36569 19.6343L14.8686 11.1314C15.2646 10.7354 15.4627 10.5373 15.691 10.4632C15.8918 10.3979 16.1082 10.3979 16.309 10.4632C16.5373 10.5373 16.7354 10.7354 17.1314 11.1314L21 15V16.2M16.2 21C17.8802 21 18.7202 21 19.362 20.673C19.9265 20.3854 20.3854 19.9265 20.673 19.362C21 18.7202 21 17.8802 21 16.2M16.2 21H7.8C6.11984 21 5.27976 21 4.63803 20.673C4.07354 20.3854 3.6146 19.9265 3.32698 19.362C3 18.7202 3 17.8802 3 16.2V7.8C3 6.11984 3 5.27976 3.32698 4.63803C3.6146 4.07354 4.07354 3.6146 4.63803 3.32698C5.27976 3 6.11984 3 7.8 3H16.2C17.8802 3 18.7202 3 19.362 3.32698C19.9265 3.6146 20.3854 4.07354 20.673 4.63803C21 5.27976 21 6.11984 21 7.8V16.2M10.5 8.5C10.5 9.60457 9.60457 10.5 8.5 10.5C7.39543 10.5 6.5 9.60457 6.5 8.5C6.5 7.39543 7.39543 6.5 8.5 6.5C9.60457 6.5 10.5 7.39543 10.5 8.5Z"
                                    stroke="#0D44EA" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </span>
                    </button>
                @endif
            </div>
        </div>
        <div class="flex w-[100%]" id="equipment-heat-pump">
            <span
                class="text-[16px] font-[400] text-[#000000] max-[767px]:text-[14px] max-[767px]:font-[600] w-[30%] max-[767px]:text-[14px] max-[767px]:pr-[5px] break-auto-phrase">{{ __('business.customer.heat_pump') }}:</span>
            <div class="flex items-center gap-2 w-[70%] justify-between">
                <span
                    class="text-[16px] font-[400] text-[#000000] max-[767px]:text-[14px] max-[767px]:font-[600]">{{ $completedJobCustomer['heat_pump_details'] }}</span>
                @if (!empty($completedJobCustomer['heat_pump_image']))
                    <button type="button"
                        class="text-[12px] font-[400] text-[#086DF1] cursor-pointer whitespace-nowrap view-image-btn"
                        x-data
                        @click="$dispatch('open-modal', {
                                id: 'image-preview-modal',
                                url: '{{ $completedJobCustomer->heat_pump_image }}'
                            })">
                        <span class="min-[768px]:inline-block max-[768px]:hidden">
                            {{ __('business.work_orders.view_image') }} </span>
                        <span class="max-[767px]:inline-block min-[768px]:hidden"><svg width="24" height="24"
                                viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M16.2 21H6.93137C6.32555 21 6.02265 21 5.88238 20.8802C5.76068 20.7763 5.69609 20.6203 5.70865 20.4608C5.72312 20.2769 5.93731 20.0627 6.36569 19.6343L14.8686 11.1314C15.2646 10.7354 15.4627 10.5373 15.691 10.4632C15.8918 10.3979 16.1082 10.3979 16.309 10.4632C16.5373 10.5373 16.7354 10.7354 17.1314 11.1314L21 15V16.2M16.2 21C17.8802 21 18.7202 21 19.362 20.673C19.9265 20.3854 20.3854 19.9265 20.673 19.362C21 18.7202 21 17.8802 21 16.2M16.2 21H7.8C6.11984 21 5.27976 21 4.63803 20.673C4.07354 20.3854 3.6146 19.9265 3.32698 19.362C3 18.7202 3 17.8802 3 16.2V7.8C3 6.11984 3 5.27976 3.32698 4.63803C3.6146 4.07354 4.07354 3.6146 4.63803 3.32698C5.27976 3 6.11984 3 7.8 3H16.2C17.8802 3 18.7202 3 19.362 3.32698C19.9265 3.6146 20.3854 4.07354 20.673 4.63803C21 5.27976 21 6.11984 21 7.8V16.2M10.5 8.5C10.5 9.60457 9.60457 10.5 8.5 10.5C7.39543 10.5 6.5 9.60457 6.5 8.5C6.5 7.39543 7.39543 6.5 8.5 6.5C9.60457 6.5 10.5 7.39543 10.5 8.5Z"
                                    stroke="#0D44EA" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </span>
                    </button>
                @endif
            </div>
        </div>
        <div class="flex w-[100%]" id="equipment-aux-systems">
            <span
                class="text-[16px] font-[400] text-[#000000] max-[767px]:text-[14px] max-[767px]:font-[600] w-[30%] max-[767px]:text-[14px] max-[767px]:pr-[5px] break-auto-phrase">{{ __('business.work_orders.aux_systems') }}:</span>
            <div class="flex items-center gap-2 w-[70%] justify-between">
                <span
                    class="text-[16px] font-[400] text-[#000000] max-[767px]:text-[14px] max-[767px]:font-[600]">{{ $completedJobCustomer['aux_details'] }}</span>
                @if (!empty($completedJobCustomer['aux_image']))
                    <button type="button"
                        class="text-[12px] font-[400] text-[#086DF1] cursor-pointer whitespace-nowrap view-image-btn"
                        x-data
                        @click="$dispatch('open-modal', {
                                id: 'image-preview-modal',
                                url: '{{ $completedJobCustomer->aux_image }}'
                            })">
                        <span class="min-[768px]:inline-block max-[768px]:hidden">
                            {{ __('business.work_orders.view_image') }} </span>
                        <span class="max-[767px]:inline-block min-[768px]:hidden"><svg width="24" height="24"
                                viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M16.2 21H6.93137C6.32555 21 6.02265 21 5.88238 20.8802C5.76068 20.7763 5.69609 20.6203 5.70865 20.4608C5.72312 20.2769 5.93731 20.0627 6.36569 19.6343L14.8686 11.1314C15.2646 10.7354 15.4627 10.5373 15.691 10.4632C15.8918 10.3979 16.1082 10.3979 16.309 10.4632C16.5373 10.5373 16.7354 10.7354 17.1314 11.1314L21 15V16.2M16.2 21C17.8802 21 18.7202 21 19.362 20.673C19.9265 20.3854 20.3854 19.9265 20.673 19.362C21 18.7202 21 17.8802 21 16.2M16.2 21H7.8C6.11984 21 5.27976 21 4.63803 20.673C4.07354 20.3854 3.6146 19.9265 3.32698 19.362C3 18.7202 3 17.8802 3 16.2V7.8C3 6.11984 3 5.27976 3.32698 4.63803C3.6146 4.07354 4.07354 3.6146 4.63803 3.32698C5.27976 3 6.11984 3 7.8 3H16.2C17.8802 3 18.7202 3 19.362 3.32698C19.9265 3.6146 20.3854 4.07354 20.673 4.63803C21 5.27976 21 6.11984 21 7.8V16.2M10.5 8.5C10.5 9.60457 9.60457 10.5 8.5 10.5C7.39543 10.5 6.5 9.60457 6.5 8.5C6.5 7.39543 7.39543 6.5 8.5 6.5C9.60457 6.5 10.5 7.39543 10.5 8.5Z"
                                    stroke="#0D44EA" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </span>
                    </button>
                @endif
            </div>
        </div>
        <div class="flex w-[100%]" id="equipment-aux-2-systems">
            <span
                class="text-[16px] font-[400] text-[#000000] max-[767px]:text-[14px] max-[767px]:font-[600] w-[30%] max-[767px]:text-[14px] max-[767px]:pr-[5px] break-auto-phrase">{{ __('business.work_orders.aux2_systems') }}:</span>
            <div class="flex items-center gap-2 w-[70%] justify-between">
                <span
                    class="text-[16px] font-[400] text-[#000000] max-[767px]:text-[14px] max-[767px]:font-[600]">{{ $completedJobCustomer['aux2_details'] }}</span>
                @if (!empty($completedJobCustomer['aux2_image']))
                    <button type="button"
                        class="text-[12px] font-[400] text-[#086DF1] cursor-pointer whitespace-nowrap view-image-btn"
                        x-data
                        @click="$dispatch('open-modal', {
                                id: 'image-preview-modal',
                                url: '{{ $completedJobCustomer->aux_2_image }}'
                            })">
                        <span class="min-[768px]:inline-block max-[768px]:hidden">
                            {{ __('business.work_orders.view_image') }} </span>
                        <span class="max-[767px]:inline-block min-[768px]:hidden"><svg width="24" height="24"
                                viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M16.2 21H6.93137C6.32555 21 6.02265 21 5.88238 20.8802C5.76068 20.7763 5.69609 20.6203 5.70865 20.4608C5.72312 20.2769 5.93731 20.0627 6.36569 19.6343L14.8686 11.1314C15.2646 10.7354 15.4627 10.5373 15.691 10.4632C15.8918 10.3979 16.1082 10.3979 16.309 10.4632C16.5373 10.5373 16.7354 10.7354 17.1314 11.1314L21 15V16.2M16.2 21C17.8802 21 18.7202 21 19.362 20.673C19.9265 20.3854 20.3854 19.9265 20.673 19.362C21 18.7202 21 17.8802 21 16.2M16.2 21H7.8C6.11984 21 5.27976 21 4.63803 20.673C4.07354 20.3854 3.6146 19.9265 3.32698 19.362C3 18.7202 3 17.8802 3 16.2V7.8C3 6.11984 3 5.27976 3.32698 4.63803C3.6146 4.07354 4.07354 3.6146 4.63803 3.32698C5.27976 3 6.11984 3 7.8 3H16.2C17.8802 3 18.7202 3 19.362 3.32698C19.9265 3.6146 20.3854 4.07354 20.673 4.63803C21 5.27976 21 6.11984 21 7.8V16.2M10.5 8.5C10.5 9.60457 9.60457 10.5 8.5 10.5C7.39543 10.5 6.5 9.60457 6.5 8.5C6.5 7.39543 7.39543 6.5 8.5 6.5C9.60457 6.5 10.5 7.39543 10.5 8.5Z"
                                    stroke="#0D44EA" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </span>
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
