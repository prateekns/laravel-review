<!-- Start of the Reporting Component -->
<div class="reporting" x-data="{ activeTab: 'chemicals' }">

    <!-- Summary Report Section -->
    <div class="white-box !mt-0">
        <div class="sheduler-search-bar bg-waves !justify-between gap-[8px] mb-[30px]">
            <div class="flex items-center gap-[8px]">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M14 6.66671H2M10.6667 1.33337V4.00004M5.33333 1.33337V4.00004M5.2 14.6667H10.8C11.9201 14.6667 12.4802 14.6667 12.908 14.4487C13.2843 14.257 13.5903 13.951 13.782 13.5747C14 13.1469 14 12.5868 14 11.4667V5.86671C14 4.7466 14 4.18655 13.782 3.75873C13.5903 3.3824 13.2843 3.07644 12.908 2.88469C12.4802 2.66671 11.9201 2.66671 10.8 2.66671H5.2C4.0799 2.66671 3.51984 2.66671 3.09202 2.88469C2.71569 3.07644 2.40973 3.3824 2.21799 3.75873C2 4.18655 2 4.7466 2 5.86671V11.4667C2 12.5868 2 13.1469 2.21799 13.5747C2.40973 13.951 2.71569 14.257 3.09202 14.4487C3.51984 14.6667 4.0799 14.6667 5.2 14.6667Z"
                        stroke="white" stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <p class="font-[600] text-[20px] text-[#ffffff]">{{ __('business.reports.summary_report') }} ({{ $days }} {{ __('business.reports.days') }})</p>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 px-[16px] summary-report-cards">
            <!-- Card 1: Total Chemicals -->
            <div
                class="inline-flex flex-col bg-gradient-to-r from-[#E0E0E0] to-[#ACACAC] p-[17px] rounded-[8px] text-center total-chemicals-card"
>
                <div class="flex justify-center items-center mx-auto mb-2">
                   <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M13.3333 2.66406V12.7001C13.3337 13.1147 13.2373 13.5238 13.0519 13.8947L6.29328 27.3974C6.19049 27.6014 6.14182 27.8283 6.15195 28.0565C6.16207 28.2847 6.23066 28.5065 6.35112 28.7006C6.47158 28.8947 6.63987 29.0545 6.83987 29.1649C7.03987 29.2752 7.26487 29.3323 7.49328 29.3307H24.5066C24.735 29.3323 24.96 29.2752 25.16 29.1649C25.36 29.0545 25.5283 28.8947 25.6488 28.7006C25.7692 28.5065 25.8378 28.2847 25.8479 28.0565C25.8581 27.8283 25.8094 27.6014 25.7066 27.3974L18.9479 13.8947C18.7626 13.5238 18.6662 13.1147 18.6666 12.7001V2.66406" stroke="#2563EB" stroke-width="2.66667" stroke-linecap="round" stroke-linejoin="round"/>
<path d="M11.3333 2.66406H20.6666" stroke="#2563EB" stroke-width="2.66667" stroke-linecap="round" stroke-linejoin="round"/>
<path d="M9.33325 21.3359H22.6666" stroke="#2563EB" stroke-width="2.66667" stroke-linecap="round" stroke-linejoin="round"/>
</svg>


                </div>
                <p class="text-[24px] leading-[32px] font-[600] text-[#111827] chemicals-card-total total">
                    @if(!empty($summary['chemicals_by_unit']))
                        <div class="space-y-1">
                            @foreach($summary['chemicals_by_unit'] as $unit => $total)
                                <div class="text-[24px] leading-[32px] font-[600] text-[#111827">
                                    {{ $total }} {{ $unit }}
                                </div>
                            @endforeach
                        </div>
                    @else
                        {{0}}
                    @endif

                </p>
                <p class="text-[14px] text-[#111827] font-[500] leading-[18px]">{{ __('business.reports.total_chemicals') }}</p>
            </div>
            <!-- Card 2: Items Sold -->
            <div
                class="inline-flex flex-col bg-gradient-to-r from-[#E0E0E0] to-[#ACACAC] p-[17px] rounded-[8px] text-center summary-card items-sold-card">
                <div class="flex justify-center items-center mx-auto mb-2">
                    <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M14.6667 28.9758C15.0721 29.2099 15.5319 29.3331 16 29.3331C16.4681 29.3331 16.9279 29.2099 17.3333 28.9758L26.6667 23.6425C27.0717 23.4087 27.408 23.0725 27.6421 22.6676C27.8761 22.2627 27.9995 21.8034 28 21.3358V10.6691C27.9995 10.2015 27.8761 9.74222 27.6421 9.33736C27.408 8.93249 27.0717 8.59629 26.6667 8.36247L17.3333 3.02914C16.9279 2.79509 16.4681 2.67188 16 2.67188C15.5319 2.67188 15.0721 2.79509 14.6667 3.02914L5.33333 8.36247C4.92835 8.59629 4.59197 8.93249 4.35795 9.33736C4.12392 9.74222 4.00048 10.2015 4 10.6691V21.3358C4.00048 21.8034 4.12392 22.2627 4.35795 22.6676C4.59197 23.0725 4.92835 23.4087 5.33333 23.6425L14.6667 28.9758Z" stroke="#2563EB" stroke-width="2.66667" stroke-linecap="round" stroke-linejoin="round"/>
<path d="M16 29.3333V16" stroke="#2563EB" stroke-width="2.66667" stroke-linecap="round" stroke-linejoin="round"/>
<path d="M4.40015 9.33594L14.6708 15.6479C15.0752 15.8805 15.5336 16.0029 16.0001 16.0029C16.4667 16.0029 16.925 15.8805 17.3295 15.6479L27.6001 9.33594" stroke="#2563EB" stroke-width="2.66667" stroke-linecap="round" stroke-linejoin="round"/>
<path d="M10 5.69531L22 12.562" stroke="#2563EB" stroke-width="2.66667" stroke-linecap="round" stroke-linejoin="round"/>
</svg>



                </div>
                <p class="text-[24px] leading-[32px] font-[600] text-[#111827] items-sold-card-total total">{{ $summary['items_sold'] }}</p>
                <p class="text-[14px] text-[#111827] font-[500] leading-[18px]">{{ __('business.items_sold.title') }}</p>
            </div>
            <!-- Card 3: Clients Serviced -->
            <div
                class="inline-flex flex-col bg-gradient-to-r from-[#E0E0E0] to-[#ACACAC] p-[17px] rounded-[8px] text-center summary-card clients-serviced-card">
                <div class="flex justify-center items-center mx-auto mb-2">
                <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M21.3334 28V25.3333C21.3334 23.9188 20.7715 22.5623 19.7713 21.5621C18.7711 20.5619 17.4146 20 16.0001 20H8.00008C6.58559 20 5.22904 20.5619 4.22885 21.5621C3.22865 22.5623 2.66675 23.9188 2.66675 25.3333V28" stroke="#2563EB" stroke-width="2.66667" stroke-linecap="round" stroke-linejoin="round"/>
<path d="M12.0001 14.6667C14.9456 14.6667 17.3334 12.2789 17.3334 9.33333C17.3334 6.38781 14.9456 4 12.0001 4C9.05456 4 6.66675 6.38781 6.66675 9.33333C6.66675 12.2789 9.05456 14.6667 12.0001 14.6667Z" stroke="#2563EB" stroke-width="2.66667" stroke-linecap="round" stroke-linejoin="round"/>
<path d="M29.3335 27.9985V25.3319C29.3326 24.1502 28.9393 23.0022 28.2153 22.0683C27.4913 21.1344 26.4777 20.4673 25.3335 20.1719" stroke="#2563EB" stroke-width="2.66667" stroke-linecap="round" stroke-linejoin="round"/>
<path d="M21.3335 4.17188C22.4807 4.46561 23.4975 5.13281 24.2237 6.06829C24.9498 7.00377 25.344 8.15431 25.344 9.33854C25.344 10.5228 24.9498 11.6733 24.2237 12.6088C23.4975 13.5443 22.4807 14.2115 21.3335 14.5052" stroke="#2563EB" stroke-width="2.66667" stroke-linecap="round" stroke-linejoin="round"/>
</svg>

                </div>
                <p class="text-[24px] leading-[32px] font-[600] text-[#111827] clients-serviced-card-total total">{{ $summary['clients_serviced'] }}</p>
                <p class="text-[14px] text-[#111827] font-[500] leading-[18px]">{{ __('business.reports.clients_serviced') }}</p>
            </div>
            <!-- Card 4: Work Order -->
            <div
                class="inline-flex flex-col bg-gradient-to-r from-[#E0E0E0] to-[#ACACAC] p-[17px] rounded-[8px] text-center summary-card work-orders-card">
                <div class="flex justify-center items-center mx-auto mb-2">
                  <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M19.6001 8.40225C19.3557 8.65149 19.2189 8.98658 19.2189 9.33559C19.2189 9.68459 19.3557 10.0197 19.6001 10.2689L21.7334 12.4023C21.9826 12.6466 22.3177 12.7834 22.6667 12.7834C23.0157 12.7834 23.3508 12.6466 23.6001 12.4023L28.6267 7.37559C29.2972 8.85717 29.5002 10.5079 29.2087 12.1078C28.9172 13.7077 28.145 15.1807 26.9951 16.3306C25.8452 17.4805 24.3721 18.2527 22.7722 18.5442C21.1724 18.8357 19.5216 18.6327 18.0401 17.9623L8.82672 27.1756C8.29629 27.706 7.57686 28.004 6.82672 28.004C6.07657 28.004 5.35715 27.706 4.82672 27.1756C4.29629 26.6452 3.99829 25.9257 3.99829 25.1756C3.99829 24.4254 4.29629 23.706 4.82672 23.1756L14.0401 13.9623C13.3696 12.4807 13.1666 10.8299 13.4581 9.23006C13.7496 7.63018 14.5218 6.15713 15.6717 5.00721C16.8216 3.8573 18.2946 3.08514 19.8945 2.79364C21.4944 2.50214 23.1451 2.70514 24.6267 3.37558L19.6134 8.38892L19.6001 8.40225Z" stroke="#2563EB" stroke-width="2.66667" stroke-linecap="round" stroke-linejoin="round"/>
</svg>

                </div>
                <p class="text-[24px] leading-[32px] font-[600] text-[#111827] work-orders-card-total total">{{ $summary['work_orders'] }}</p>
                <p class="text-[14px] text-[#111827] font-[500] leading-[18px]">{{ __('business.templates.work_order') }}</p>
            </div>
            <!-- Card 5: Maintenance Order -->
            <div
                class="inline-flex flex-col bg-gradient-to-r from-[#E0E0E0] to-[#ACACAC] p-[17px] rounded-[8px]  text-center summary-card maintenance-orders-card">
                <div class="flex justify-center items-center mx-auto mb-2">
                   <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M16.7467 8.10927C17.6637 6.6441 18.3137 5.028 18.6667 3.33594C19.3333 6.66927 21.3333 9.86927 24 12.0026C26.6667 14.1359 28 16.6693 28 19.3359C28.0076 21.179 27.4678 22.9828 26.4491 24.5187C25.4303 26.0546 23.9784 27.2535 22.2775 27.9633C20.5766 28.673 18.7032 28.8618 16.8949 28.5056C15.0866 28.1495 13.4247 27.2644 12.12 25.9626M9.33333 21.0426C12.2667 21.0426 14.6667 18.6026 14.6667 15.6426C14.6667 14.0959 13.9067 12.6293 12.3867 11.3893C10.8667 10.1493 9.72 8.30927 9.33333 6.37594C8.94667 8.30927 7.81333 10.1626 6.28 11.3893C4.74667 12.6159 4 14.1093 4 15.6426C4 18.6026 6.4 21.0426 9.33333 21.0426Z" stroke="#2563EB" stroke-width="2.66667" stroke-linecap="round" stroke-linejoin="round"/>
</svg>

                </div>
                <p class="text-[24px] leading-[32px] font-[600] text-[#111827] maintenance-orders-card-total total">{{ $summary['maintenance_orders'] }}</p>
                <p class="text-[14px] text-[#111827] font-[500] leading-[18px]">{{ __('business.templates.maintenance') }}</p>
            </div>
        </div>
    </div>

    <!-- Tabs Section -->
    <div class="white-box p-2 shadow-sm ">
        <div class="flex bg-[#F9FAFB] p-[4px] rounded-[6px]">
            <!-- Chemical List Tab Button -->
            <button @click="activeTab = 'chemicals'; $wire.activeTab = 'chemicals'"
                :class="{ 'bg-[#0D44EA] text-[#ffffff]': activeTab === 'chemicals', 'text-[#212529]': activeTab !== 'chemicals' }"
                class="flex-1 flex items-center justify-center p-[6px] font-[500] text-[14px] leading-[19px] rounded-[4px] transition-colors cursor-pointer">
                <svg class="mr-[8px]" width="16" height="17" viewBox="0 0 16 17" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M6.66652 1.83337V6.85137C6.66671 7.05871 6.61855 7.26324 6.52585 7.44871L3.14652 14.2C3.09512 14.302 3.07079 14.4155 3.07585 14.5296C3.08091 14.6437 3.11521 14.7546 3.17544 14.8516C3.23567 14.9487 3.31981 15.0286 3.41981 15.0838C3.51981 15.1389 3.63231 15.1675 3.74652 15.1667H12.2532C12.3674 15.1675 12.4799 15.1389 12.5799 15.0838C12.6799 15.0286 12.764 14.9487 12.8243 14.8516C12.8845 14.7546 12.9188 14.6437 12.9239 14.5296C12.9289 14.4155 12.9046 14.302 12.8532 14.2L9.47385 7.44871C9.38116 7.26324 9.33299 7.05871 9.33318 6.85137V1.83337"
                        stroke="currentColor" stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M5.6665 1.83337H10.3332" stroke="currentColor" stroke-width="1.33333" stroke-linecap="round"
                        stroke-linejoin="round" />
                    <path d="M4.6665 11.1666H11.3332" stroke="currentColor" stroke-width="1.33333" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>

                {{__('business.reports.chemical_list')}}
            </button>
            <!-- Items Sold Tab Button -->
            <button @click="activeTab = 'items'; $wire.activeTab = 'items'"
                :class="{ 'bg-[#0D44EA] text-[#ffffff]': activeTab === 'items', 'text-[#212529]': activeTab !== 'items' }"
                class="flex-1 flex items-center justify-center p-[6px] font-[500] text-[14px] leading-[19px] rounded-[4px] transition-colors cursor-pointer">
                <svg class="mr-[8px]" width="16" height="17" viewBox="0 0 16 17" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M7.33333 14.9867C7.53603 15.1037 7.76595 15.1653 8 15.1653C8.23405 15.1653 8.46397 15.1037 8.66667 14.9867L13.3333 12.32C13.5358 12.2031 13.704 12.035 13.821 11.8326C13.938 11.6301 13.9998 11.4005 14 11.1667V5.83335C13.9998 5.59953 13.938 5.36989 13.821 5.16746C13.704 4.96503 13.5358 4.79692 13.3333 4.68002L8.66667 2.01335C8.46397 1.89633 8.23405 1.83472 8 1.83472C7.76595 1.83472 7.53603 1.89633 7.33333 2.01335L2.66667 4.68002C2.46418 4.79692 2.29599 4.96503 2.17897 5.16746C2.06196 5.36989 2.00024 5.59953 2 5.83335V11.1667C2.00024 11.4005 2.06196 11.6301 2.17897 11.8326C2.29599 12.035 2.46418 12.2031 2.66667 12.32L7.33333 14.9867Z"
                        stroke="currentColor" stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M8 15.1667V8.5" stroke="currentColor" stroke-width="1.33333" stroke-linecap="round"
                        stroke-linejoin="round" />
                    <path
                        d="M2.19995 5.16663L7.33528 8.32263C7.5375 8.43891 7.76669 8.50011 7.99995 8.50011C8.23322 8.50011 8.4624 8.43891 8.66462 8.32263L13.8 5.16663"
                        stroke="currentColor" stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M5 3.34668L11 6.78001" stroke="currentColor" stroke-width="1.33333" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>

                {{__('business.items_sold.title')}}
            </button>
        </div>

        <!-- Tab Content -->
        <div class="mt-[10px]">
            <!-- Chemical List Content -->
            <div x-show="activeTab === 'chemicals'" x-data="{ filtersOpen: false }">
                <livewire:business.reports.chemical-report />
            </div>

            <!-- Items Sold Content -->
            <div x-show="activeTab === 'items'">
                <livewire:business.reports.items-sold-report />
            </div>
        </div>
    </div>
    <x-notification-alert />
</div>
<!-- End of the Reporting Component -->
