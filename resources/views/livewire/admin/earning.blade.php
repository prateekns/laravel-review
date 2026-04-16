<div
    x-data="{ showToast:false}"
    x-on:date-range-error="showToast=true;setTimeout(() => { showToast = false; }, 5000);"
>
    <div x-show="showToast" x-cloak>
        <x-toast type="error" :message="__('admin.validation.date_range_error')" />
    </div>

<div class="mt-4 px-4 sm:px-6 lg:px-4">

    <!--Loading Indicator-->
    @if($filter != 'custom')
        <x-loading :target="'filter'"/>
    @endif

    <x-loading :target="'search,fromDate,toDate,previousPage,nextPage'"/>
        
    <!-- Earnings Card -->
    <div class="divide-y divide-gray-200 overflow-hidden rounded-lg bg-white shadow-sm">
        <div class="px-4 py-5 sm:px-6 sm:flex sm:items-center">
            <div class="sm:flex-auto">
                <h2 class="white-box-heading"> {{ __('admin.earning.earnings') }}</h1>
                 <p class="mt-2 text-sm text-gray-700">{{ __('admin.table.total') .": ".  $total }}</p>
            </div>

             <!-- Search Input -->
             <div class="mr-4">
                <x-search :search="$search"/>
             </div>
            <!-- Search Input Ends -->

            <!-- Time Filter -->
            <x-time-filter :filter="$filter" :all="false"/>
            <!-- Filter Ends -->
        </div>
        
        <!-- Table -->
        <table class="min-w-full divide-y divide-gray-300" aria-describedby="Earnings">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="py-3.5 pr-3 pl-4 text-left text-sm font-semibold text-gray-900 sm:pl-6">{{ __('admin.earning.business_name') }}</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">{{ __('admin.earning.plan') }}</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">{{ __('admin.earning.amount') }}</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">{{ __('admin.earning.type') }}</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">{{ __('admin.earning.transaction_date') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($earnings as $earning)
                    <tr>
                        <td class="py-4 pr-3 pl-4 text-sm font-medium whitespace-nowrap text-gray-900 sm:pl-6">{{ $earning->business?->name }}</td>
                        <td class="px-3 py-4 text-sm whitespace-nowrap text-gray-500">
                            <div class="flex flex-col">
                                <span>{{ __('admin.business.admin') }}: {{ $earning->invoice_type==='recurring' ? $earning->order?->total_admin : $earning->order?->admin_qty_change}}</span>
                                <span>{{ __('admin.business.technician') }}: {{ $earning->invoice_type==='recurring' ? $earning->order?->total_technician : $earning->order?->technician_qty_change }}</span>
                            </div>
                        </td>
                        <td class="px-3 py-4 text-sm whitespace-nowrap text-gray-500">{{ $earning->invoice_amount }}</td>
                        <td class="px-3 py-4 text-sm whitespace-nowrap text-gray-500">{{ ucfirst($earning->invoice_type) }}</td>
                        <td class="px-3 py-4 text-sm whitespace-nowrap text-gray-500">
                            <time datetime="{{ $earning->created_at }}">{{ $earning->created_at }}</time>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-14 text-center text-gray-500">
                            @if($error)
                                {{ __('admin.table.search_failed') }}
                            @else
                                {{ $search ? __('admin.message.no_result_matches') : __('admin.earning.no_earnings_found') }}
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <!-- Table Ends -->

        <!-- Footer -->
        @if($earnings)
            <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                <x-table-pagination :list="$earnings"/>
            </div>
        @endif
        <!-- Footer Ends -->
    </div>
</div>
     <!-- Earnings Card -->

@error('fromDate')
    <x-notification-alert type="error" :message="$message" :timestamp="now()->timestamp" />
@enderror

@error('toDate')
    <x-notification-alert type="error" :message="$message" :timestamp="now()->timestamp" />
@enderror
</div>
