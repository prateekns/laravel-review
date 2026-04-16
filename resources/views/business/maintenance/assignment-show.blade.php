@extends('layouts.business.app')

@section('title', __('business.maintenance.view_details'))

@section('content')
    <div class="container-fluid mx-auto">
        <!-- Header Section -->
        <x-page-heading title="{{ __('business.maintenance.view_details') }}"
            description="{{ __('business.maintenance.view_description') }}"
            link="{{ route('business.work-orders.maintenance.index') }}">
            <!-- Technician Assignment Section -->
            <div class="flex gap-2 flex-col text-left items-start max-[767px]:w-full max-[767px]:mt-[12px]">
                <span
                    class="text-[16px] font-[500] whitespace-nowrap">{{ __('business.work_orders.assigned_technician') }}</span>
                <span class="text-[16px]">{{ $assignment->technician->full_name }}</span>
            </div>
        </x-page-heading>

        <!-- Customer Details Section -->
        <div class="white-box shadow-sm py-[24px] px-[20px] rounded-[16px] mb-4">
            <div x-data="{ open: false }">
                <div @click="open = !open" class="accordian-header" id="customer-details"
                    :class="open ? 'manage-template-heading cursor-pointer show' : 'manage-template-heading cursor-pointer'">
                    <div class="flex gap-[10px] items-center">
                        <p class="m-0">{{ __('business.work_orders.customer_details') }}</p>
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
                    <div class="px-0 pb-6 max-[767px]:px-0">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-0">
                            <!-- Customer Name -->
                            <div>
                                <x-form.label
                                    class="!text-[14px] !font-[400] !text-[#000000]">{{ __('business.work_orders.sections.name') }}</x-form.label>
                                <p class="mt-[4px] text-[16px] font-[400] text-[#000000]" id="customer-name">
                                    {{ $assignment->isCompleted() ? $assignment->completedJobCustomer->name ?? '-' : $workOrder->customer->customer_name ?? '-' }}
                                </p>
                            </div>

                            <!-- Pool Details -->
                            <div class="col-span-2">
                                @if ($assignment->isCompleted())
                                    @include(
                                        'livewire.business.customers.completed-job-customer-pool-details',
                                        [
                                            'completedJobCustomer' => $assignment->completed_job_customer,
                                            'hasNoPoolDetails' => $assignment->has_no_pool_details,
                                        ]
                                    )
                                @else
                                    @livewire('business.customers.customer-pool-details', [
                                        'customerId' => $workOrder->customer_id,
                                        'isViewMode' => true,
                                    ])
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Service Type Section -->
        <div class="white-box shadow-sm py-[24px] px-[20px] rounded-[16px] mb-4">
            <div x-data="{ open: true }">
                <div @click="open = !open" class="accordian-header"
                    :class="open ? 'manage-template-heading cursor-pointer show' : 'manage-template-heading cursor-pointer'">
                    <div class="flex gap-[10px] items-center">
                        <p class="m-0">{{ __('business.work_orders.service_type') }}</p>
                    </div>
                    <div class="icon" :class="{ 'show': open }" style="transition: transform 0.3s ease">
                        <svg width="16" height="10" viewBox="0 0 16 10" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M2 2L8 8L14 2" stroke="#0D44EA" stroke-width="2.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </div>
                </div>
                <div x-show="open" x-transition class="accordian-contant w-full">
                    <div class="px-0 pb-6 max-[767px]:px-0">
                        <div class="mt-6">
                            <x-form.label
                                class="!text-[14px] !font-[400] !text-[#000000]">{{ __('business.work_orders.service_type') }}</x-form.label>
                            <p class="mt-[4px] text-[16px] font-[400] text-[#000000]" id="service-type">
                                {{ $assignment->template->name ?? '-' }}</p>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-[24px]">
                            <!-- Service Type Details -->
                            <div class="task-details-box relative">
                                <h2 class="text-[20px] font-[600] text-[#0D6EFD] mb-[24px]">
                                    {{ __('business.work_orders.sections.task_details') }}</h2>

                                <div class="mt-0">
                                    <x-form.label
                                        class="!text-[14px] !font-[400] !text-[#000000]">{{ __('business.work_orders.job_name') }}</x-form.label>
                                    <p class="mt-[4px] text-[16px] font-[400] text-[#000000]" id="job-name">
                                        {{ $assignment->name }}</p>
                                </div>

                                @if ($assignment->additional_task)
                                    <div class="mt-[24px]">
                                        <x-form.label
                                            class="!text-[14px] !font-[400] !text-[#000000]">{{ __('business.work_orders.additional_task') }}</x-form.label>
                                        <p class="mt-[4px] text-[16px] font-[400] text-[#000000]" id="additional-task">
                                            {{ $assignment->additional_task }}</p>
                                    </div>
                                @endif

                                <div class="mt-[24px]">
                                    <x-form.label
                                        class="!text-[14px] !font-[400] !text-[#000000]">{{ __('business.work_orders.sections.description') }}</x-form.label>
                                    <p class="mt-[4px] text-[16px] font-[400] text-[#000000]" id="description">
                                        {{ $assignment->description ?: '-' }}</p>
                                </div>

                                <div class="mt-[24px]">
                                    <x-form.label
                                        class="!text-[14px] !font-[400] !text-[#000000]">{{ __('business.work_orders.preferred_start_date') }}</x-form.label>
                                    <p class="mt-[4px] text-[16px] font-[400] text-[#000000]" id="preferred-start-date">
                                        {{ $assignment->scheduled_date_formatted ? Carbon\Carbon::parse($assignment->scheduled_date_formatted)->format('M d, Y') : '-' }}
                                    </p>
                                </div>

                                <div class="mt-[24px]">
                                    <x-form.label
                                        class="!text-[14px] !font-[400] !text-[#000000]">{{ __('business.work_orders.preferred_start_time') }}</x-form.label>
                                    <p class="mt-[4px] text-[16px] font-[400] text-[#000000]" id="preferred-start-time">
                                        {{ $assignment->scheduled_time_formatted ?: '-' }}
                                    </p>
                                </div>

                                <!-- Uploaded Photos -->
                                @if ($assignment->photo)
                                    <div class="my-[24px] flex w-full  h-[1px] bg-[#E5E7EB]"></div>
                                    <div class="mt-0">
                                        <x-form.label
                                            class="!text-[14px] !font-[400] !text-[#000000]">{{ __('business.work_orders.uploaded_photos') }}</x-form.label>
                                        <div
                                            class="mt-2 flex items-center py-[12px] px-[16px] bg-white border border-[#DBEAFE] rounded-[10px]">
                                            <div class="flex items-center gap-3 photo-preview">
                                                <div class="w-12 h-12 rounded-lg overflow-hidden">
                                                    <img src="{{ $assignment->photo_thumb_url }}" id="uploaded-photos"
                                                        alt="Work Order"
                                                        class="w-full h-full object-cover cursor-pointer" x-data
                                                        @click="$dispatch('open-modal', {
                                                        id: 'image-preview-modal',
                                                        url: '{{ $assignment->photo_url }}'
                                                    })">
                                                </div>
                                                <div>
                                                    <p class="text-[#0B0B0B] font-[600] text-[12px] filename">
                                                        {{ basename($assignment->photo) }}</p>
                                                    {{-- <p class="text-[14px] text-[#767676] font-[400]">{{ number_format(Storage::size('public/work_order/' . $workOrder->photo) / 1024, 1) }}kb</p> --}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-[24px] flex w-full  h-[1px] bg-[#E5E7EB]"></div>
                                @endif
                            </div>

                            {{-- Checklists Section --}}
                            <div class="task-details-box relative">
                                <div class="bg-white rounded-lg">
                                    <h2 class="text-[20px] font-[600] text-[#0D6EFD] mb-[24px]">
                                        {{ __('business.work_orders.checklist_for') }}
                                        {{ $assignment->template->name ?? 'Template' }}
                                    </h2>
                                    <h3 class="text-[16px] font-[600] text-[#000000] mb-[24px]">
                                        {{ __('business.work_orders.mandatory_technician_checklist') }}</h3>

                                    @if ($checklistItems && count($checklistItems) > 0)
                                        <div class="space-y-[12px]">
                                            @foreach ($checklistItems as $item)
                                                <div class="text-[16px] font-[400] text-[#212529] checklist-item">
                                                    {{ $item->description }}</div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-[16px] font-[400] text-[#212529] no-checklist-item">
                                            {{ __('business.work_orders.no_checklist_items') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Maintenance Frequency Details Section -->
        @if ($assignment->is_recurring)
            <div class="white-box shadow-sm py-[24px] px-[20px] rounded-[16px] mb-4">
                <div x-data="{ open: false }">
                    <div @click="open = !open" class="accordian-header"
                        :class="open ? 'manage-template-heading cursor-pointer show' : 'manage-template-heading cursor-pointer'">
                        <div class="flex gap-[10px] items-center">
                            <p class="m-0">{{ __('business.maintenance.maintenance_service_frequency_details') }}</p>
                        </div>
                        <div class="icon" :class="{ 'show': open }" style="transition: transform 0.3s ease">
                            <svg width="16" height="10" viewBox="0 0 16 10" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path d="M2 2L8 8L14 2" stroke="#0D44EA" stroke-width="2.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </div>
                    </div>
                    <div x-show="open" x-transition class="accordian-contant w-full">
                        <div class="px-0 pb-6 max-[767px]:px-0">
                            <div
                                class="grid grid-cols-3 md:grid-cols-3 gap-[33px] max-[767px]:gap-[12px] mt-[40px] w-full">
                                <div class="frequency-box">
                                    <x-form.label
                                        class="!text-[14px] !font-[400] !text-[#1D242B] max-[767px]:!text-[12px]">{{ __('business.work_orders.maintenance_type') }}</x-form.label>
                                    <p
                                        class="mt-[12px] text-[16px] max-[767px]:text-[14px] font-[500] text-[#1C1D1D] text-center">
                                        {{ $assignment->frequency_label }}
                                        {{ $assignment->repeat_after_label ? '(' . $assignment->repeat_after_label . ')' : '' }}
                                    </p>
                                </div>

                                <div class="frequency-box">
                                    <x-form.label
                                        class="!text-[14px] !font-[400] !text-[#1D242B] max-[767px]:!text-[12px]">{{ __('business.work_orders.maintenance_days') }}</x-form.label>
                                    <p
                                        class="mt-[12px] text-[16px] font-[500] max-[767px]:text-[14px] text-[#1C1D1D] text-center">
                                        @if ($assignment->frequency === 'daily')
                                            {{ __('business.maintenance.all_days') }}
                                        @elseif(
                                            $assignment->frequency === 'monthly' &&
                                                $assignment->monthly_day_type_label &&
                                                $assignment->monthly_day_of_week_label)
                                            {{ $assignment->monthly_day_type_label }}
                                            {{ $assignment->monthly_day_of_week_label }}
                                        @elseif($assignment->selected_days_label)
                                            {{ $workOrder->selected_days_label }}
                                        @else
                                            -
                                        @endif
                                    </p>
                                </div>

                                <div class="frequency-box">
                                    <x-form.label
                                        class="!text-[14px] !font-[400] !text-[#1D242B] max-[767px]:!text-[12px]">{{ __('business.work_orders.end_date') }}</x-form.label>
                                    <p
                                        class="mt-[12px] text-[16px] font-[500] max-[767px]:text-[14px] min-[375px]:whitespace-nowrap text-[#1C1D1D] text-center">
                                        {{ $assignment->end_date ? Carbon\Carbon::parse($assignment->end_date_formatted)->format('M d, Y') : '-' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Chemical Usage Section -->
        <div class="white-box shadow-sm py-[24px] px-[20px] rounded-[16px] mb-4">
            <div x-data="{ open: false }" id="chemical-usage-section">
                <div @click="open = !open" class="accordian-header"
                    :class="open ? 'manage-template-heading cursor-pointer show' : 'manage-template-heading cursor-pointer'">
                    <div class="flex gap-[10px] items-center">
                        <p class="m-0">{{ __('business.work_orders.chemical_usage') }}</p>
                    </div>
                    <div class="icon" :class="{ 'show': open }" style="transition: transform 0.3s ease">
                        <svg width="16" height="10" viewBox="0 0 16 10" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M2 2L8 8L14 2" stroke="#0D44EA" stroke-width="2.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </div>
                </div>
                <div x-show="open" x-transition class="accordian-contant w-full !mt-[0px] !p-[0]">
                    <div class="px-0 pb-0">
                        @if ($regularChemicalLogs->count() > 0 || $additionalItemLogs->count() > 0)
                            <!-- Chemical Usage Table -->
                            @if ($regularChemicalLogs->count() > 0)
                                <p class="text-[16px] font-[400] text-[#0C1421] mb-[40px] mt-[12px]"><span
                                        class="text-[16px] font-[600] text-[#0C1421]">{{ __('business.maintenance.note') }}:</span>
                                    {{ __('business.maintenance.chemical_usage_note') }}</p>
                                <div class="mb-0">
                                    <x-form.label
                                        class="pl-[8px] max-[640px]:pl-0 max-[640px]:border-b-[1px] max-[640px]:border-b-[#1D242B] max-[640px]:pb-[8px]">{{ __('business.work_orders.chemical_usage') }}</x-form.label>
                                    <div class="table-box !px-0 !pb-0 !mt-[16px] !pt-0">
                                        <table class="min-w-full divide-y divide-gray-300" aria-describedby="Work Order Data">
                                            <thead>
                                                <tr>
                                                    <th scope="col">{{ __('business.work_orders.chemical_name_value') }}</th>
                                                    <th scope="col">{{ __('business.work_orders.range') }}</th>
                                                    <th scope="col">{{ __('business.work_orders.ideal_target') }}</th>
                                                    <th scope="col">{{ __('business.work_orders.pool_reading') }}</th>
                                                    <th scope="col">{{ __('business.work_orders.added_qty') }}</th>
                                                    <th scope="col">{{ __('business.work_orders.tabs') }}</th>
                                                    <th scope="col">{{ __('business.work_orders.chemical_added') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($regularChemicalLogs as $chemicalLog)
                                                    <tr>
                                                        <td data-label="Chemical Name">
                                                            {{ $chemicalLog->chemical_name ?? '-' }}
                                                        </td>
                                                        <td data-label="Range">
                                                            {{ $chemicalLog->range ?? '-' }}
                                                        </td>
                                                        <td data-label="Ideal Target">
                                                            {{ $chemicalLog->ideal_target }}
                                                            {{ $chemicalLog->unit }}
                                                        </td>
                                                        <td data-label="Pool Reading">
                                                            {{ $chemicalLog->reading ? $chemicalLog->reading . ' ' . $chemicalLog->unit : '-' }}
                                                        </td>
                                                        <td data-label="Added Qty">
                                                            {{ $chemicalLog->formatted_qty_added }}
                                                        </td>
                                                        <td data-label="Tabs">
                                                            {{ $chemicalLog->tabs ?? '-' }}
                                                        </td>
                                                        <td data-label="Chemical Added">
                                                            {{ $chemicalLog->chemical_used }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif
                            <!-- Additional Maintenance Items Table -->
                            @if ($additionalItemLogs->count() > 0)
                                <div class="mt-6">
                                    <x-form.label
                                        class="max-[640px]:border-b-[1px] max-[640px]:border-b-[#1D242B] max-[640px]:pb-[8px]">{{ __('business.work_orders.additional_maintenance_items') }}</x-form.label>
                                    <div class="table-box !px-0 !pb-0 !pt-0 !mt-[16px]">
                                        <table class="min-w-full divide-y divide-gray-300" aria-describedby="Work Order Items">
                                            <thead>
                                                <tr>
                                                    <th scope="col">{{ __('business.work_orders.item_name') }}</th>
                                                    <th scope="col">{{ __('business.work_orders.quantity_added') }}
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($additionalItemLogs as $additionalItem)
                                                    <tr>
                                                        <td data-label="Item Name">{{ $additionalItem->item ?? '-' }}</td>
                                                        <td data-label="Quantity Added">
                                                            {{ $additionalItem->quantity . ' ' . $additionalItem->unit}}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif
                        @else
                            <p class="text-[16px] text-gray-500 text-center pb-0 mt-[30px]">
                                {{ __('business.work_orders.no_chemical_usage_data') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Items Sold and Extra Work Section -->
        <div class="white-box shadow-sm py-[24px] px-[20px] rounded-[16px] mb-4">
            <div x-data="{ open: false }">
                <div @click="open = !open" class="accordian-header"
                    :class="open ? 'manage-template-heading cursor-pointer show' : 'manage-template-heading cursor-pointer'">
                    <div class="flex gap-[10px] items-center">
                        <p class="m-0">{{ __('business.work_orders.additional_items_sold_extra_work') }}</p>
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
                    <div class="px-0 pb-0">
                        @if (($itemsSoldForInstance && $itemsSoldForInstance->count() > 0) || $assignment->extra_work_done)
                            <!-- Items Sold Table -->
                            @if ($itemsSoldForInstance && $itemsSoldForInstance->count() > 0)
                                <div class="mb-0" id="items-sold-table">
                                    <x-form.label
                                        class="max-[640px]:border-b-[1px] max-[640px]:border-b-[#1D242B] max-[640px]:pb-[8px]">{{ __('business.items_sold.title') }}</x-form.label>
                                    <div class="table-box !px-0 !mt-16px !pt-0">
                                        <table class="min-w-full divide-y divide-gray-300" aria-describedby="Work Order Item Sold">
                                            <thead>
                                                <tr>
                                                    <th scope="col">{{ __('business.work_orders.item_name') }}</th>
                                                    <th scope="col">{{ __('business.work_orders.quantity') }}</th>
                                                    <th scope="col">{{ __('business.work_orders.date') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($itemsSoldForInstance as $item)
                                                    <tr>
                                                        <td data-label="Item Name">{{ $item->item }}</td>
                                                        <td data-label="Quantity Sold">{{ $item->quantity }}</td>
                                                        <td data-label="Date">
                                                            {{ $item->created_at_formatted }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif

                            <!-- Additional Work -->
                            @if ($assignment->extra_work_done)
                                <div class="px-[0px] py-[8px] pt-0 !pb-0 max-[767px]:!mt-[16px]" id="extra-work-section">
                                    <x-form.label
                                        class="!text-[12px] !font-[600] !text-[#0C1421] px-[12px]">{{ __('business.work_orders.sections.extra_work') }}</x-form.label>

                                    <div class="mt-[12px] mb-[8px] flex w-full bg-[#E5E7EB] h-[1px]"></div>
                                    <div class="mt-[4px] text-[12px] font-[400] text-[#000000] px-[12px]"
                                        id="extra-work-done">{!! nl2br(e($assignment->extra_work_done)) !!}</div>
                                </div>
                            @endif
                        @else
                            <p class="text-gray-500 text-center">
                                {{ __('business.work_orders.no_items_sold_or_extra_work') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Communication Notes Section -->
        <div class="px-0 pb-6" id="communication-notes">
            <p class="text-[16px] font-[600] text-[#1D242B] mb-[12px]">
                {{ __('business.work_orders.communication_notes') }}</p>
            <p class="text-[400] text-[#606060] text-[14px] mb-[10px] communication-notes-label">
                ({{ __('business.work_orders.tech_to_customer_note') }})</p>
            <p class="text-[14px] font-[500] text-[#1C1D1D]">{{ $assignment->communication_notes ?: '-' }}</p>
            @include('business.work-orders.parts.customer-attachments', ['customerAttachments' => $customerAttachments])
        </div>
        <!-- Add the Image Preview Modal Component (Single Instance) -->
        <x-modal.image-preview />
    </div>
@endsection
