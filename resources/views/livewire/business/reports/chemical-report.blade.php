<div>
    <h3 class="text-[20px] font-[600] text-[#2D3748] leading-[25px] mb-[10px]">
        {{ __('business.reports.chemical_usage_records') }}
        ({{ $chemicalLogs->total() }} {{ __('business.reports.entries') }})</h3>

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
        <div class="flex items-center gap-[16px]">
            <div class="flex items-center gap-2 max-[767px]:w-full max-[767px]:justify-start max-[767px]:mt-[16px]">
                <button wire:click="downloadChemicalReport"
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
                    <span>{{ \Carbon\Carbon::parse($startDateLocal, $businessTimezone)->format('m/d/Y') . ' - ' . \Carbon\Carbon::parse($endDateLocal, $businessTimezone)->format('m/d/Y') }}
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
                                class="block text-[12px] font-[600] text-[#212529] mb-1">{{ __('business.reports.end_date') }}</label>
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
            <div class="relative">
                <button @click="filtersOpen = !filtersOpen"
                    class="p-[9px] border bg-[#FFFFFF] border-[#E5E7EB] rounded-[10px] cursor-pointer">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <mask id="mask0_4563_140020" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0"
                            width="24" height="24">
                            <rect width="24" height="24" fill="#D9D9D9" />
                        </mask>
                        <g mask="url(#mask0_4563_140020)">
                            <path
                                d="M3.16667 19.7773C2.852 19.7773 2.57867 19.6618 2.34667 19.4307C2.11556 19.1987 2 18.9253 2 18.6107C2 18.296 2.11556 18.0231 2.34667 17.792C2.57867 17.56 2.852 17.444 3.16667 17.444H7.72267C8.03733 17.444 8.31022 17.56 8.54133 17.792C8.77333 18.0231 8.88933 18.296 8.88933 18.6107C8.88933 18.9253 8.77333 19.1987 8.54133 19.4307C8.31022 19.6618 8.03733 19.7773 7.72267 19.7773H3.16667ZM3.16667 6.556C2.852 6.556 2.57867 6.44 2.34667 6.208C2.11556 5.97689 2 5.704 2 5.38933C2 5.07467 2.11556 4.80133 2.34667 4.56933C2.57867 4.33822 2.852 4.22267 3.16667 4.22267H11.8613C12.176 4.22267 12.4489 4.33822 12.68 4.56933C12.912 4.80133 13.028 5.07467 13.028 5.38933C13.028 5.704 12.912 5.97689 12.68 6.208C12.4489 6.44 12.176 6.556 11.8613 6.556H3.16667ZM12.1107 22C11.796 22 11.5231 21.8844 11.292 21.6533C11.06 21.4213 10.944 21.148 10.944 20.8333V16.416C10.944 16.1013 11.06 15.8284 11.292 15.5973C11.5231 15.3662 11.796 15.2507 12.1107 15.2507C12.4262 15.2507 12.6996 15.3662 12.9307 15.5973C13.1618 15.8284 13.2773 16.1013 13.2773 16.416V17.444H20.8333C21.148 17.444 21.4213 17.56 21.6533 17.792C21.8844 18.0231 22 18.296 22 18.6107C22 18.9253 21.8844 19.1987 21.6533 19.4307C21.4213 19.6618 21.148 19.7773 20.8333 19.7773H13.2773V20.8333C13.2773 21.148 13.1618 21.4213 12.9307 21.6533C12.6996 21.8844 12.4262 22 12.1107 22ZM7.72267 15.3893C7.408 15.3893 7.13467 15.2733 6.90267 15.0413C6.67156 14.8102 6.556 14.5373 6.556 14.2227V13.1667H3.16667C2.852 13.1667 2.57867 13.0511 2.34667 12.82C2.11556 12.588 2 12.3147 2 12C2 11.6853 2.11556 11.412 2.34667 11.18C2.57867 10.9489 2.852 10.8333 3.16667 10.8333H6.556V9.80533C6.556 9.49067 6.67156 9.21778 6.90267 8.98667C7.13467 8.75467 7.408 8.63867 7.72267 8.63867C8.03733 8.63867 8.31022 8.75467 8.54133 8.98667C8.77333 9.21778 8.88933 9.49067 8.88933 9.80533V14.2227C8.88933 14.5373 8.77333 14.8102 8.54133 15.0413C8.31022 15.2733 8.03733 15.3893 7.72267 15.3893ZM12.1107 13.1667C11.796 13.1667 11.5231 13.0511 11.292 12.82C11.06 12.588 10.944 12.3147 10.944 12C10.944 11.6853 11.06 11.412 11.292 11.18C11.5231 10.9489 11.796 10.8333 12.1107 10.8333H20.8333C21.148 10.8333 21.4213 10.9489 21.6533 11.18C21.8844 11.412 22 11.6853 22 12C22 12.3147 21.8844 12.588 21.6533 12.82C21.4213 13.0511 21.148 13.1667 20.8333 13.1667H12.1107ZM16.2773 8.74933C15.9627 8.74933 15.6898 8.63378 15.4587 8.40267C15.2267 8.17156 15.1107 7.89867 15.1107 7.584V3.16667C15.1107 2.852 15.2267 2.57867 15.4587 2.34667C15.6898 2.11556 15.9627 2 16.2773 2C16.592 2 16.8653 2.11556 17.0973 2.34667C17.3284 2.57867 17.444 2.852 17.444 3.16667V4.22267H20.8333C21.148 4.22267 21.4213 4.33822 21.6533 4.56933C21.8844 4.80133 22 5.07467 22 5.38933C22 5.704 21.8844 5.97689 21.6533 6.208C21.4213 6.44 21.148 6.556 20.8333 6.556H17.444V7.584C17.444 7.89867 17.3284 8.17156 17.0973 8.40267C16.8653 8.63378 16.592 8.74933 16.2773 8.74933Z"
                                fill="#212529" />
                        </g>
                    </svg>

                </button>
                <!-- Filter Dropdown -->
                <div x-show="filtersOpen" @click.away="filtersOpen = false" x-transition
                    class="absolute right-0 mt-2 w-56 bg-[#FFFFFF] rounded-[8px] shadow-sm border-[1px] border-[#F3F4F6] z-10 p-4">
                    <ul class="space-y-3">
                        @foreach ($availableChemicals as $chemical)
                            <li class="flex items-center input-checkbox">
                                <input id="{{ $chemical }}" type="checkbox" wire:model.live="selectedChemicals"
                                    checked value="{{ $chemical }}" class="">
                                <label for="{{ $chemical }}"
                                    class="ml-[10px] font-[400] text-[14px] text-[#374151] leading-[18px]">{{ $chemical }}
                                </label>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Chemicals Table -->
    <div class="table-box">
        <table class="min-w-full divide-y divide-gray-300" aria-describedby="Chemical Report">
            <thead>
                <tr>
                    <th scope="col" class="cursor-pointer" wire:click="sortBy('chemical_name')">
                        <div class="flex items-center gap-2">
                            {{ __('business.reports.chemical_name') }}
                            <x-icons.sort-arrows />
                        </div>
                    </th>
                    <th scope="col" class="cursor-pointer" wire:click="sortBy('technician')">
                        <div class="flex items-center gap-2">
                            {{ __('business.reports.technician') }}
                            <x-icons.sort-arrows />
                        </div>
                    </th>
                    <th scope="col" class="cursor-pointer" wire:click="sortBy('customer')">
                        <div class="flex items-center gap-2">
                            {{ __('business.maintenance.customer') }}
                            <x-icons.sort-arrows />
                        </div>
                    </th>
                    <th scope="col" class="cursor-pointer" wire:click="sortBy('quantity')">
                        <div class="flex items-center gap-2">
                            {{ __('business.work_orders.quantity_sold') }}
                            <x-icons.sort-arrows />
                        </div>
                    </th>
                    <th scope="col" class="cursor-pointer" wire:click="sortBy('chemical_used')">
                        <div class="flex items-center gap-2">
                            {{ __('business.reports.chemical_used') }}
                            <x-icons.sort-arrows />
                        </div>
                    </th>
                    <th scope="col">{{ __('business.work_orders.additional_maintenance_items') }}</th>
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
                @forelse($chemicalLogs as $log)
                    <tr>
                        <td data-label="Chemical Name">{{ $log->chemical_name ?? ($log->chemical->name ?? '-') }}</td>
                        <td data-label="{{ __('business.reports.technician') }}">
                            @if ($log->workOrder->is_recurring && $log->workOrderAssignment)
                                {{ $log->workOrderAssignment?->technician?->full_name }}
                            @else
                                {{ $log->workOrder->technician?->full_name }}
                            @endif
                        </td>
                        <td data-label="{{ __('business.work_orders.customer') }}">
                            @php
                                $customer = null;
                                if ($log->workOrder) {
                                    $customer = $log->workOrder->completedJobCustomers->first();
                                } elseif ($log->workOrderAssignment) {
                                    $customer = $log->workOrderAssignment->completedJobCustomers->first();
                                }
                            @endphp
                            {{ $customer?->customer_name ?: '-' }}
                        </td>
                        <td data-label="{{ __('business.work_orders.quantity_sold') }}" class="email-td">
                            <span
                                class="inline-flex rounded-[12px] bg-[#DBEAFE] items-center justify-center text-[12px] font-[500] leading-[16px] text-[#1E40AF] w-full max-w-[max-content] py-[4px] px-[20px]">{{ $log->formatted_qty_added }}
                            </span>
                            @if ($log->tabs)
                                <span
                                    class="inline-flex rounded-[12px] bg-[#DBEAFE] items-center justify-center text-[12px] font-[500] leading-[16px] text-[#1E40AF] w-full max-w-[max-content] py-[4px] px-[20px]">{{ $log->tabs }}
                                    Tabs
                                </span>
                            @endif
                        </td>
                        <td data-label="{{ __('business.reports.used_chemical') }}">
                            {{ $log->chemical_used }}
                        </td>
                        <td data-label="{{ __('business.work_orders.additional_maintenance_items') }}">
                            @if ($log->maintenance_items)
                                @php
                                    $items = explode(', ', $log->maintenance_items);
                                    $itemCount = count($items);
                                    $maxToShow = 1;
                                @endphp
                                <div x-data="{ expanded: false }"
                                    class="relative max-[640px]:max-w-[50%] max-[640px]:float-right max-[640px]:text-left">
                                    <div class="relative break-words"
                                        x-bind:class="expanded ? 'break-words' : 'description-truncate'">
                                        <ul class="text-[#000000] list-none">
                                            @foreach ($items as $index => $item)
                                                <li class="text-[12px] font-[400] leading-[16px] text-[#000000]"
                                                    x-show="expanded || {{ $index }} < {{ $maxToShow }}"
                                                    x-cloak>{{ $item }}</li>
                                            @endforeach
                                        </ul>
                                        @if ($itemCount > $maxToShow)
                                            <button @click="expanded = !expanded"
                                                x-text="expanded ? '{{ __('business.templates.view_less') }}' : '{{ __('business.templates.view_more') }}'"
                                                class="block mt-[6px] !underline italic text-[#2563EB] text-[10px] leading-[15px] cursor-pointer">
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @else
                                -
                            @endif
                        </td>
                        <td data-label="{{ __('business.work_orders.table.date') }}">
                            @php
                                $order = $log->workOrder ?? $log->workOrderAssignment;
                                $completedAt = $order?->completed_at ?? $log->created_at;
                            @endphp
                            {{ $completedAt ? $completedAt->setTimezone(auth()->guard('business')->user()?->business?->timezone ?? config('datetime.timezones.default'))->format('m/d/Y') : '-' }}
                        </td>
                        <td class="table-actions">
                            <div class="flex gap-[10px] max-[767px]:justify-end ">
                                @php
                                    $ViewUrl = '#';
                                    $viewText = '-';

                                    if ($log->workOrderAssignment) {
                                        if ($log->workOrderAssignment->type == 'WO') {
                                            $ViewUrl = route(
                                                'business.work-orders.show_assignment_completed',
                                                $log->workOrderAssignment->instance_id,
                                            );
                                            $viewText = 'WO';
                                        } elseif ($log->workOrderAssignment->type == 'MO') {
                                            $ViewUrl = route(
                                                'business.work-orders.maintenance.show_assignment_completed',
                                                $log->workOrderAssignment->instance_id,
                                            );
                                            $viewText = 'MO';
                                        }
                                    } elseif ($log->workOrder) {
                                        if ($log->workOrder->type == 'WO') {
                                            $ViewUrl = route('business.work-orders.show', $log->workOrder);
                                            $viewText = 'WO';
                                        } elseif ($log->workOrder->type == 'MO') {
                                            $ViewUrl = route('business.work-orders.maintenance.show', $log->workOrder);
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

        @isset($chemicalLogs)
            @if ($chemicalLogs->hasPages())
                <div class="pagination-box">
                    <x-pagination :paginator="$chemicalLogs" />
                </div>
            @endif
        @endisset
    </div>
    <x-loading target="downloadChemicalReport,sortBy,search" />
</div>
