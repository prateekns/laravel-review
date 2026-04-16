<div>
    <h3 class="text-[20px] font-[600] text-[#2D3748] leading-[25px] mb-[10px]">
        {{ __('business.reports.items_sold_records') }} ({{ $records->total() }}
        {{ __('business.reports.entries') }})</h3>
    <!-- Toolbar -->
    <div class="flex flex-col sm:flex-row justify-between items-center mb-4 gap-4">
        <div class="grid grid-cols-1 relative">
            <input type="text" wire:model.live="search"
                class="col-start-1 row-start-1 block w-full rounded-md bg-white py-2.5 pr-10 pl-10 text-base text-[#000000] border-2 border-[#767676] placeholder:text-[#767676] focus:border-blue-500 sm:pl-9 text-[16px] font-[400] appearance-none"
                placeholder="{{ __('business.customers.search') }}">
            <svg class="pointer-events-none col-start-1 row-start-1 ml-2 size-5 self-center text-[#767676]"
                viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd"
                    d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z"
                    clip-rule="evenodd" />
            </svg>
        </div>

        <div class="flex items-center gap-2">
            <div class="flex items-center gap-2 max-[767px]:w-full max-[767px]:justify-start max-[767px]:mt-[16px]">
                <button wire:click="downloadItemsSoldReport"
                    class="cursor-pointer inline-flex border-[1px] border-[#DBEAFE] bg-[#ffffff] py-[11px] px-[12px] rounded-[6px] text-[14px] font-[500] text-[#374151] leading-[18px] items-center gap-2">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M3.89933 9.88743L6.56101 12.1686C6.68528 12.2776 6.84216 12.3333 6.99987 12.3334C7.10777 12.3334 7.21607 12.3074 7.31456 12.2546C7.35846 12.2311 7.40016 12.2024 7.43872 12.1686L10.1004 9.88743C10.38 9.64783 10.4124 9.22697 10.1728 8.9474C9.93317 8.66784 9.5123 8.63544 9.23274 8.87504L7.66658 10.2173L7.66658 5.05422C7.66658 4.68603 7.3681 4.38756 6.99991 4.38756C6.63172 4.38756 6.33324 4.68603 6.33324 5.05422L6.33324 10.2174L4.76699 8.87504C4.48743 8.63544 4.06656 8.66784 3.82696 8.9474C3.58736 9.22697 3.61976 9.64783 3.89933 9.88743ZM10.9999 5.66671C10.9999 6.0349 10.7014 6.33337 10.3333 6.33337L9.33325 6.33337C8.96506 6.33337 8.66659 6.63185 8.66659 7.00004C8.66659 7.36823 8.96506 7.66671 9.33325 7.66671L10.3333 7.66671C11.4378 7.66671 12.3333 6.77128 12.3333 5.66671L12.3333 3.66671C12.3333 2.56214 11.4378 1.66671 10.3333 1.66671L3.66659 1.66671C2.56202 1.66671 1.66659 2.56214 1.66659 3.66671L1.66659 5.66671C1.66659 6.77128 2.56202 7.66671 3.66659 7.66671L4.66659 7.66671C5.03478 7.66671 5.33325 7.36823 5.33325 7.00004C5.33325 6.63185 5.03478 6.33337 4.66659 6.33337L3.66659 6.33337C3.2984 6.33337 2.99992 6.0349 2.99992 5.66671L2.99992 3.66671C2.99992 3.29852 3.2984 3.00004 3.66659 3.00004L10.3333 3.00004C10.7014 3.00004 10.9999 3.29852 10.9999 3.66671L10.9999 5.66671Z"
                            fill="#374151" />
                    </svg>

                    <span class="whitespace-nowrap">{{ __('business.reports.download_csv') }}</span>
                </button>
            </div>
            <div x-data="{
                open: false,
                maxRange: 60,
                tempStartDate: null,
                tempEndDate: null,
                formatDate(dateString) {
                    if (!dateString) return '';
                    const date = new Date(dateString);
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const day = String(date.getDate()).padStart(2, '0');
                    const year = date.getFullYear();
                    return `${month}/${day}/${year}`;
                },
                validateDates() {
                    if (!this.tempStartDate || !this.tempEndDate) {
                        $wire.showError('{{ __('business.reports.validation.start_end_dates_required') }}');
                        return false;
                    }
            
                    const start = new Date(this.tempStartDate);
                    const end = new Date(this.tempEndDate);
            
                    if (end < start) {
                        $wire.showError('{{ __('business.reports.validation.end_date_after_start') }}');
                        return false;
                    }
            
                    const diffDays = Math.ceil((end - start) / (1000 * 60 * 60 * 24));
                    if (diffDays > this.maxRange) {
                        $wire.showDateRangeError();
                        return false;
                    }
            
                    return true;
                },
                init() {
                    this.tempStartDate = $wire.startDateLocal;
                    this.tempEndDate = $wire.endDateLocal;
                }
            }" class="relative date-range-filter">
                <button @click="open = !open" type="button"
                    class="bg-[#EFF6FF] text-[#212529] text-[12px] font-[600] px-[9px] py-[4px] rounded-[12px] flex items-center gap-2 cursor-pointer">
                    <span>
                        {{ $startDateLocal ? \Carbon\Carbon::parse($startDateLocal, $businessTimezone)->format('m/d/Y') : '-' }}
                        -
                        {{ $endDateLocal ? \Carbon\Carbon::parse($endDateLocal, $businessTimezone)->format('m/d/Y') : '-' }}
                    </span>
                </button>
                <div x-show="open" @click.away="open = false"
                    class="absolute left-0 mt-2 bg-white rounded-lg shadow-lg p-4 z-10">
                    <div class="flex flex-col gap-2">
                        <div>
                            <label for="startDate"
                                class="block text-[12px] font-[600] text-[#212529] mb-1">{{ __('business.reports.start_date') }}</label>
                            <input type="date" id="startDate" x-model="tempStartDate" :min="$wire.minDate"
                                :max="$wire.maxDate"
                                class="block w-full rounded-md bg-white py-1.5 px-2 text-[12px] text-[#212529] border border-[#E5E7EB] cursor-pointer">
                        </div>
                        <div>
                            <label for="endDate"
                                class="block text-[12px] font-[600] text-[#212529] mb-1">{{ __('business.work_orders.end_date') }}</label>
                            <input type="date" id="endDate" x-model="tempEndDate" :min="$wire.minDate"
                                :max="$wire.maxDate"
                                class="block w-full rounded-md bg-white py-1.5 px-2 text-[12px] text-[#212529] border border-[#E5E7EB] cursor-pointer">
                        </div>
                        <button type="button"
                            @click="
                                if (validateDates()) {
                                    $wire.applyDateFilter(tempStartDate, tempEndDate);
                                    open = false;
                                }
                            "
                            class="bg-[#EFF6FF] text-[#212529] text-[12px] font-[600] px-[9px] py-[4px] rounded-[12px] items-center gap-2 cursor-pointer hover:bg-blue-100 text-center">
                            {{ __('business.reports.ok') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="table-box">
        <table class="min-w-full divide-y divide-gray-300" aria-describedby="Item Sold Report">
            <thead>
                <tr>
                    <th scope="col" class="cursor-pointer" wire:click="sortBy('item')">
                        <div class="flex items-center gap-2">
                            {{ __('business.reports.item_sold') }}
                            <x-icons.sort-arrows />
                        </div>
                    </th>
                    <th scope="col" class="cursor-pointer" wire:click="sortBy('customer')">
                        <div class="flex items-center gap-2">
                            {{ __('business.maintenance.customer') }}
                            <x-icons.sort-arrows />
                        </div>
                    </th>
                    <th scope="col" class="cursor-pointer" wire:click="sortBy('technician')">
                        <div class="flex items-center gap-2">
                            {{ __('business.reports.technician') }}
                            <x-icons.sort-arrows />
                        </div>
                    </th>
                    <th scope="col" class="cursor-pointer" wire:click="sortBy('quantity')">
                        <div class="flex items-center gap-2">
                            {{ __('business.work_orders.quantity_sold') }}
                            <x-icons.sort-arrows />
                        </div>
                    </th>
                    <th scope="col">
                        {{ __('business.reports.additional_items_sold') }}
                    </th>
                    <th scope="col" class="cursor-pointer" wire:click="sortBy('created_at')">
                        <div class="flex items-center gap-2">
                            {{ __('business.work_orders.table.date') }}
                            <x-icons.sort-arrows />
                        </div>
                    </th>
                    <th scope="col">{{ __('business.work_orders.table.action') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $row)
                    <tr>
                        <td data-label="Item Sold"><span
                                class="min-[641px]:max-w-[200px] min-[641px]:inline-flex">{{ $row->item ?? '-' }}</span>
                        </td>
                        <td data-label="Customer">
                            @php
                                $customer = null;
                                if ($row->workOrder) {
                                    $customer = $row->workOrder->completedJobCustomers->first();
                                } elseif ($row->workOrderAssignment) {
                                    $customer = $row->workOrderAssignment->completedJobCustomers->first();
                                }
                            @endphp
                            <span class="min-[641px]:max-w-[220px] min-[641px]:inline-flex break-words">
                                {{ $customer?->customer_name ?: '-' }} </span>
                        </td>
                        <td data-label="technician">
                            @if ($row->workOrder->is_recurring && $row->workOrderAssignment)
                                {{ $row->workOrderAssignment?->technician?->full_name }}
                            @else
                                {{ $row->workOrder->technician?->full_name }}
                            @endif
                        </td>
                        <td data-label="Quantity Sold" class="email-td !text-center max-[640px]:!text-right ">
                            <span
                                class="inline-flex rounded-[12px] bg-[#DBEAFE] items-center justify-center text-[12px] font-[500] leading-[16px] text-[#1E40AF] w-full max-w-[max-content] py-[4px] px-[20px] mr-[40px] max-[640px]:mr-[0]">{{ rtrim(rtrim(number_format((float) ($row->quantity ?? 0), 2, '.', ''), '0'), '.') }}
                            </span>
                        </td>
                        <td data-label="{{ __('business.reports.additional_items_sold') }}" style="max-width: 400px;">
                            <span class="">
                                {{ $row->workOrder?->extra_work_done ?: $row->workOrderAssignment?->extra_work_done ?: '-' }}</span>
                        </td>
                        <td data-label="{{ __('business.reports.date') }}">
                            @php
                                $completedAt = $row->created_at;
                            @endphp
                            {{ $completedAt ? $completedAt->setTimezone($businessTimezone ?? config('datetime.timezones.default'))->format('m/d/Y') : '-' }}
                        </td>
                        <td class="table-actions">
                            <div class="flex gap-[10px] max-[767px]:justify-end ">
                                @php
                                    $ViewUrl = '#';
                                    $viewText = '-';

                                    if ($row->workOrderAssignment) {
                                        if ($row->workOrderAssignment->type == 'WO') {
                                            $ViewUrl = route(
                                                'business.work-orders.show_assignment_completed',
                                                $row->workOrderAssignment->instance_id,
                                            );
                                            $viewText = 'WO';
                                        } elseif ($row->workOrderAssignment->type == 'MO') {
                                            $ViewUrl = route(
                                                'business.work-orders.maintenance.show_assignment_completed',
                                                $row->workOrderAssignment->instance_id,
                                            );
                                            $viewText = 'MO';
                                        }
                                    } elseif ($row->workOrder) {
                                        if ($row->workOrder->type == 'WO') {
                                            $ViewUrl = route('business.work-orders.show', $row->workOrder);
                                            $viewText = 'WO';
                                        } elseif ($row->workOrder->type == 'MO') {
                                            $ViewUrl = route('business.work-orders.maintenance.show', $row->workOrder);
                                            $viewText = 'MO';
                                        }
                                    }
                                    $viewText = __('business.scheduler.view') . ' ' . $viewText;
                                @endphp
                                @if ($ViewUrl != '#')
                                    <a href="{{ $ViewUrl }}"
                                        class="text-[14px] font-[600] leading-[19px] text-[#2563EB] underline"
                                        title="{{ $viewText }}">{{ $viewText }}</a>
                                @else
                                    -
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-4 text-center text-gray-500 !w-full !p-0">
                            <span
                                class="w-full inline-block text-center py-[12px]">{{ __('business.reports.no_records_available') }}</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @isset($records)
            @if ($records->hasPages())
                <div class="pagination-box">
                    <x-pagination :paginator="$records" />
                </div>
            @endif
        @endisset
    </div>
    <x-loading target="downloadItemsSoldReport,sortBy,search" />
</div>
