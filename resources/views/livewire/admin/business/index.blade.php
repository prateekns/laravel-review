<div class="mt-4 1px-4 sm:px-6 lg:px-4"
    x-data="{show:true,errorMessage:'',showError:false,showToast:false,confirmStatus: @entangle('confirmStatus')}"
    @cancel="confirmStatus=false"
    @business-export-failed.window="
        errorMessage = $event.detail[0].message;
        showError = true;
        showToast=true;
        setTimeout(() => {
            showError = false;
            showToast=false;
        }, 5000);
    "
    >

    <x-loading :target="'search,statusFilter,previousPage,nextPage,sortBy,downloadBusinessList'"/>

    <div x-show="showError" x-cloak>
        <x-toast type="error"  message="errorMessage" x-show="errorMessage"/>
    </div>

    <!-- Businesses Card -->
    <div class="divide-y divide-gray-200 overflow-hidden rounded-lg bg-white shadow-sm">
        <div class="px-4 py-5 sm:px-6 sm:flex sm:items-center">
            <div class="sm:flex-auto">
                <h1 class="text-base font-semibold text-gray-900"> {{ __('admin.business.manage_business') }}</h1>
            </div>

            <!-- Export -->
            <button wire:click="downloadBusinessList" type="button" class="rounded-md bg-blue-600 px-3 py-3 mr-3 text-sm font-semibold text-white shadow-xs hover:bg-blue-500  cursor-pointer">{{ __("Export Businesses") }}</button>

            <!-- Status Filter -->
            <div class="mr-4">
                <x-status-filter :status-filter="$statusFilter"/>
            </div>
            <!-- Status Filter Ends -->

            <!-- Search Input -->
            <x-search :search="$search" :placeholder="__('admin.business.search_placeholder')"/>
            <!-- Search Input Ends -->
        </div>
        <!-- Table -->
        <table class="min-w-full divide-y divide-gray-300" aria-describedby="Business Admin List">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="py-3.5 pr-3 pl-4 text-left text-sm font-semibold text-gray-900 sm:pl-6">{{ __('admin.table.business_name') }}</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900" >{{ __('admin.table.business_admin_name') }}</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900" >{{ __('admin.table.business_email') }}</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900" >{{ __('admin.table.business_phone') }}</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900" >{{ __('admin.table.status') }}</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 cursor-pointer hover:bg-gray-100" wire:click="sortBy('created_at')">
                        <div class="flex items-center">
                            {{ __('admin.table.created_at') }}
                            @if($sortField === 'created_at')
                                @if($sortDirection === 'asc')
                                    <svg class="ml-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                @else
                                    <svg class="ml-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                @endif
                            @else
                                <svg class="ml-1 h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            @endif
                        </div>
                    </th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900" >{{ __('admin.table.action') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($businesses as $business)
                    <tr>
                        <td class="py-4 pr-3 pl-4 text-sm font-medium whitespace-wrap text-gray-900 sm:pl-6 break-words max-w-[100px]">{{ $business->name }}</td>
                        <td class="px-3 py-4 text-sm whitespace-nowrap text-gray-500">{{ $business?->primaryUser?->first_name }} {{ $business?->primaryUser?->last_name }}</td>
                        <td class="px-3 py-4 text-sm whitespace-nowrap text-gray-500">
                            <a href="mailto:{{ $business->email }}" class="hover:text-blue-400">{{ $business->email }}</a>
                        </td>
                        <td class="px-3 py-4 text-sm whitespace-nowrap text-gray-500">{{ $business->phone }}</td>
                        <td class="px-3 py-4 text-sm whitespace-nowrap">
                            @if(!$business->status)
                                <span class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/10">
                                    {{ __('admin.table.inactive') }}
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/10">
                                    {{ __('admin.table.active') }}
                                </span>
                            @endif
                        </td>
                        <td class="px-3 py-4 text-sm whitespace-nowrap text-gray-500">{{ $business->created_at->format('m/d/Y') }}</td>
                        <td class="py-4 pr-4 text-sm  whitespace-nowrap">
                            <a href="{{ route('admin.business.show', $business->id) }}" class="cursor-pointer inline-flex items-center justify-center" title="{{ __('View') }}">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" >
                                        <mask id="mask0_235_1643" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="24" height="24">
                                            <rect width="24" height="24" fill="#D9D9D9"/>
                                        </mask>
                                        <g mask="url(#mask0_235_1643)">
                                            <path d="M11.9999 16.025C13.2663 16.025 14.3415 15.5834 15.2255 14.7002C16.1087 13.817 16.5503 12.7418 16.5503 11.4746C16.5503 10.2082 16.1087 9.13341 15.2255 8.25021C14.3415 7.36701 13.2663 6.92541 11.9999 6.92541C10.7335 6.92541 9.65831 7.36701 8.77431 8.25021C7.89111 9.13341 7.44951 10.2082 7.44951 11.4746C7.44951 12.7586 7.89111 13.8378 8.77431 14.7122C9.65831 15.5874 10.7335 16.025 11.9999 16.025ZM11.9999 14.1746C11.2495 14.1746 10.6119 13.9122 10.0871 13.3874C9.56231 12.8626 9.29991 12.225 9.29991 11.4746C9.29991 10.725 9.56231 10.0878 10.0871 9.56301C10.6119 9.03741 11.2495 8.77461 11.9999 8.77461C12.7503 8.77461 13.3879 9.03741 13.9127 9.56301C14.4375 10.0878 14.6999 10.725 14.6999 11.4746C14.6999 12.225 14.4375 12.8626 13.9127 13.3874C13.3879 13.9122 12.7503 14.1746 11.9999 14.1746ZM11.9999 18.9998C9.55031 18.9998 7.32951 18.3166 5.33751 16.9502C3.34551 15.5838 1.89951 13.7586 0.999512 11.4746C1.89951 9.19141 3.34951 7.37061 5.34951 6.01221C7.34951 4.65381 9.56631 3.97461 11.9999 3.97461C14.4335 3.97461 16.6503 4.65381 18.6503 6.01221C20.6503 7.37061 22.1003 9.19141 23.0003 11.4746C22.1003 13.7586 20.6543 15.5838 18.6623 16.9502C16.6703 18.3166 14.4495 18.9998 11.9999 18.9998Z" fill="#8691a0"/>
                                        </g>
                                    </svg>
                            </a>
                            <x-toggle-btn
                                :model="$business"
                                :id="$business->id"
                                :on-toggle="$business->status ? 'showDeactivateModal' : 'showActivateModal'"
                                class="ml-2"
                                @click="confirmStatus=true"
                            />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-4 text-center text-gray-500">
                            @if($error)
                                {{ __('admin.table.search_failed') }}
                            @else
                                {{ $search ? __('admin.table.no_businesses_found_search') : __('admin.table.no_businesses_found') }}
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <!-- Table Ends -->

        <!-- Footer -->
        @if($businesses)
        <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
            <x-table-pagination :list="$businesses"/>
        </div>
        @endif
        <!-- Footer Ends -->
         </div>
     <!-- Businesses Card -->

    <!-- Business Deactivate/Activate Modal -->
    @if($selectedBusiness)
         <x-confirm.confirm-status
            :model="$selectedBusiness"
            message="{{ $selectedBusiness->status ? __('admin.alert.deactivate_business') :  __('admin.alert.activate_business') }}"
        />
    @endif

    @if (session()->has('success'))
        <x-notification-alert type="success" :message="session('success')" />
    @endif

    @if (session()->has('error'))
        <x-notification-alert type="error" :message="session('error')" />
    @endif
</div>
