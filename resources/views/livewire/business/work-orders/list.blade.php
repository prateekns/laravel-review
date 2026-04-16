<div class="container-fluid mx-auto" x-data="{ search: '' }">
    <div x-show="search.length > 2" x-cloak>
        <x-loading :target="'search'" />
    </div>
    <x-loading :target="'previousPage,nextPage'" />

    <div class="flex justify-between items-center flex-row max-[1200px]:flex-col add-sub-mobile-wrapper">
        <div class="gap-[16px] flex flex-col">
            <h1 class="main-heading">{{ __('business.work_orders.title') }}</h1>
            <p class="sub-heading">{{ __('business.work_orders.description') }}</p>
        </div>
        @if (!$workOrders->isEmpty())
            <div class="flex gap-[20px] max-[600px]:flex-col max-[600px]:w-full">
                <x-form.link link="{{ route('business.work-orders.create') }}" class="btn-box btn max-[600px]:w-full">
                    {{ __('business.customer.create_work_order') }}
                </x-form.link>
            </div>
        @endif
    </div>

    @if (session()->has('success'))
        <x-notification-alert type="success" :message="session('success')" />
    @endif

    @if (session()->has('error'))
        <x-notification-alert type="error" :message="session('error')" />
    @endif

    @if ($workOrders->isEmpty() && !$search)
        <x-empty-state :title="__('business.work_orders.empty_state.title')" :description="__('business.work_orders.empty_state.description')" :buttonText="__('business.work_orders.create_order')" :buttonLink="route('business.work-orders.create')"
            buttonClass="btn-box gap-2" icon="clipboard-list" />
    @else
        <div>
            <div class="white-box">
                <div class="top-box">
                    <div class="sm:flex-auto">
                        <h2 class="white-box-heading">{{ __('business.work_orders.existing') }}</h2>
                    </div>

                    <!-- Search Input -->
                    <div class="grid grid-cols-1 relative">
                        <input type="text" wire:model.live="search"
                            class="col-start-1 row-start-1 block w-full rounded-md bg-white py-2.5 pr-10 pl-10 text-base text-[#000000] border-2 border-[#767676] placeholder:text-[#767676] focus:border-blue-500 sm:pl-9 text-[16px] font-[400] appearance-none"
                            placeholder="{{ __('business.work_orders.search') }}">
                        <svg class="pointer-events-none col-start-1 row-start-1 ml-2 size-5 self-center text-[#767676]"
                            viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z"
                                clip-rule="evenodd" />
                        </svg>
                        @if ($search)
                            <button type="button" wire:click="$set('search', '')"
                                class="absolute right-2 top-1/2 -translate-y-1/2 text-[#212529] hover:text-gray-600 focus:outline-none cursor-pointer"
                                aria-label="{{ __('business.work_orders.clear_search') }}">
                                <svg class="size-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M10 8.586l4.95-4.95a1 1 0 1 1 1.414 1.414L11.414 10l4.95 4.95a1 1 0 0 1-1.414 1.414L10 11.414l-4.95 4.95a1 1 0 0 1-1.414-1.414L8.586 10l-4.95-4.95A1 1 0 1 1 5.05 3.636L10 8.586z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        @endif
                    </div>
                </div>

                <div class="table-box">
                    <table class="min-w-full divide-y divide-gray-300" aria-describedby="Work Orders Listing">
                        <thead>
                            <tr>
                                <th scope="col">{{ __('business.work_orders.table.id') }}</th>
                                <th scope="col">{{ __('business.work_orders.table.customer_name') }}</th>
                                <th scope="col">{{ __('business.work_orders.table.commercial_company_name') }}</th>
                                <th scope="col">{{ __('business.work_orders.table.name') }}</th>
                                <th scope="col">{{ __('business.maintenance.table.next_occurrence') }}</th>
                                <th scope="col">{{ __('business.work_orders.table.work_type') }}</th>
                                <th scope="col">{{ __('business.work_orders.table.status') }}</th>
                                <th scope="col">{{ __('business.work_orders.table.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($workOrders as $workOrder)
                                <tr>
                                    <td data-label="Work Order ID">{{ $workOrder->id }}</td>
                                    <td data-label="Customer Name">{{ !empty($workOrder->customer->full_name) ? $workOrder->customer->full_name : '-' }}</td>
                                    <td data-label="Commercial Name">{{ $workOrder->customer->commercial_pool_details ?? '-' }}</td>
                                    <td data-label="Name"
                                        class="text-wrap truncate overflow-ellipsis max-w-[200px] max-[640px]:max-w-[100%] !break-auto-phrase"
                                        title="{{ $workOrder->name }}">
                                        <a href="{{ route('business.work-orders.show', $workOrder) }}"
                                            title="{{ __('business.work_orders.view') }}">
                                            {{ $workOrder->name }}
                                        </a>
                                    </td>
                                    <td data-label="Date">
                                        {{ $workOrder->next_occurrence ?? 'N/A' }}
                                    </td>
                                    <td data-label="Work Type">{{ $workOrder->work_type_text }}</td>
                                    <td data-label="Status" class="status-td max-[767px]:!text-right">
                                        @php
                                            $statusLabel = null;
                                            if ($workOrder->isCompleted()) {
                                                $statusLabel = __('business.work_orders.status.completed');
                                                $badgeClass = $workOrder->status->color();
                                            }
                                            elseif ($workOrder->isInProgress()) {
                                                $statusLabel = __('business.work_orders.status.in_progress');
                                                $badgeClass = $workOrder->status->color();
                                            } elseif (!is_null($workOrder->technician_id)) {
                                                $statusLabel = 'Assigned';
                                                $badgeClass = 'badge-assigned';
                                            } else {
                                                $statusLabel = $workOrder->is_active
                                                    ? __('business.customer.status.active')
                                                    : __('business.customer.status.inactive');
                                                $badgeClass = $workOrder->is_active ? 'bg-success' : 'bg-warning';
                                            }
                                        @endphp
                                        <span
                                            class="badge max-[640px]:!inline-flex {{ $badgeClass }}">{{ $statusLabel }}</span>
                                    </td>
                                    <td class="table-actions">
                                        <div class="flex gap-[10px] max-[767px]:justify-end ">
                                        <a href="{{ route('business.work-orders.show', $workOrder) }}"
                                            class="text-[#0d44ea]"
                                            title="{{ __('business.work_orders.view') }}">
                                            <svg width="17" height="14" viewBox="0 0 17 14" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M8.5 9.5C9.60457 9.5 10.5 8.60457 10.5 7.5C10.5 6.39543 9.60457 5.5 8.5 5.5C7.39543 5.5 6.5 6.39543 6.5 7.5C6.5 8.60457 7.39543 9.5 8.5 9.5Z"
                                                    stroke="#212529" stroke-width="1.5" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                                <path
                                                    d="M15.5 7.5C13.7359 10.5119 11.2359 12.5 8.5 12.5C5.76414 12.5 3.26414 10.5119 1.5 7.5C3.26414 4.48813 5.76414 2.5 8.5 2.5C11.2359 2.5 13.7359 4.48813 15.5 7.5Z"
                                                    stroke="#212529" stroke-width="1.5" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                            </svg>
                                        </a>
                                        @if ($workOrder->canBeEdited())
                                            <a href="{{ route('business.work-orders.edit', $workOrder) }}"
                                                class="text-[#0d44ea]"
                                                title="{{ __('business.work_orders.edit') }}">
                                                <svg width="17" height="17" viewBox="0 0 17 17" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M15.25 15.7502H9.25002M1.375 16.1252L5.53695 14.5245C5.80316 14.4221 5.93626 14.3709 6.06079 14.3041C6.1714 14.2447 6.27685 14.1761 6.37603 14.0992C6.48769 14.0125 6.58853 13.9117 6.79021 13.71L15.25 5.25023C16.0784 4.4218 16.0784 3.07865 15.25 2.25023C14.4216 1.4218 13.0784 1.4218 12.25 2.25022L3.79021 10.71C3.58853 10.9117 3.48769 11.0125 3.40104 11.1242C3.32408 11.2234 3.25555 11.3288 3.19618 11.4394C3.12933 11.564 3.07814 11.6971 2.97575 11.9633L1.375 16.1252ZM1.375 16.1252L2.91859 12.1119C3.02905 11.8248 3.08428 11.6812 3.17901 11.6154C3.26179 11.5579 3.36423 11.5362 3.46322 11.5551C3.5765 11.5767 3.68529 11.6855 3.90286 11.9031L5.59718 13.5974C5.81475 13.815 5.92354 13.9237 5.94517 14.037C5.96408 14.136 5.94234 14.2385 5.88486 14.3212C5.81908 14.416 5.67549 14.4712 5.3883 14.5817L1.375 16.1252Z"
                                                        stroke="#212529" stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                </svg>
                                            </a>
                                        @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-4 text-center text-gray-500 !w-full !p-0">
                                        <span class="w-full inline-block text-center py-[12px]">
                                            {{ __('business.work_orders.no_records') }} </span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    @if ($workOrders->total() > 10)
                        <div class="pagination-box">
                            <x-pagination :paginator="$workOrders" />
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
