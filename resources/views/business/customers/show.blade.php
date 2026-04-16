@extends('layouts.business.app')

@section('title', __('business.customer.view_details'))

@section('content')
    <div class="container-fluid mx-auto">
        <!-- Header Section -->
        <x-page-heading title="{{ __('business.customer.view_details') }}"
            description="{{ __('business.customer.view_description') }}" link="{{ route('business.customers.index') }}" />

        <!-- Status Badge -->
        <div class="inline-flex absolute top-[28px] right-[36px] min-w-[71px]">
            <span
                class="customer-status-badge w-full px-[7px] py-[2px] rounded-[4px] text-[12px] font-[600] text-center {{ $customer->status ? 'bg-[#0C9B2B] text-white' : 'bg-[#E43232] text-white' }}">
                {{ $customer->status ? __('business.customer.status.active') : __('business.customer.status.inactive') }}
            </span>
        </div>

        <!-- Personal Details & Address Section -->
        <div class="white-box shadow-sm py-[24px] px-[20px] rounded-[16px]">
            <div x-data="{ open: true }">
                <div @click="open = !open" class="accordian-header"
                    :class="open ? 'manage-template-heading cursor-pointer show' : 'manage-template-heading cursor-pointer'">
                    <div class="flex gap-[10px] items-center">
                        <span
                            class="m-0 min-w-[28px] w-[28px] h-[28px] rounded-[50%] bg-[#0D44EA] text-white text-[14px] font-[500] flex items-center justify-center">1</span>
                        <p class="m-0">{{ __('business.customer.personal_details') }}</p>
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
                    <div class="px-6 pb-6 max-[767px]:px-0">
                        <div class="grid grid-cols-1  md:grid-cols-2 gap-6">
                            <!-- Name -->
                            <div>
                                <x-form.label
                                    class="!text-[14px] !font-[400] !text-[#000000]">{{ __('business.customer.first_name') }}</x-form.label>
                                <p class="mt-[4px] text-[16px] font-[400] text-[#000000]">{{ $customer->first_name }}</p>
                            </div>
                            <div>
                                <x-form.label
                                    class="!text-[14px] !font-[400] !text-[#000000]">{{ __('business.customer.last_name') }}</x-form.label>
                                <p class="mt-[4px] text-[16px] font-[400] text-[#000000]">{{ $customer->last_name }}</p>
                            </div>
                        </div>
                        @if ($customer->pool_type == 2)
                            <!-- Commercial Pool -->
                            <div class="grid grid-cols-1  md:grid-cols-2 gap-6 mt-[24px]">
                                <div class="col-span-2">
                                    <x-form.label
                                        class="!text-[14px] !font-[400] !text-[#000000]">{{ __('business.customer.commercial_company_name') }}</x-form.label>
                                    <p class="mt-[4px] text-[16px] font-[400] text-[#000000]">
                                        {{ $customer->commercial_pool_details ?: '-' }}</p>
                                </div>
                            </div>
                        @endif
                        <!-- Email Addresses -->
                        <div class="grid grid-cols-1  md:grid-cols-2 gap-6 mt-[24px]">
                            <div>
                                <x-form.label
                                    class="!text-[14px] !font-[400] !text-[#000000]">{{ __('business.customer.email_1') }}</x-form.label>
                                <p class="mt-[4px] text-[16px] font-[400] text-[#000000]">{{ $customer->email_1 }}</p>
                            </div>
                            <div>
                                <x-form.label
                                    class="!text-[14px] !font-[400] !text-[#000000]">{{ __('business.customer.email_2') }}</x-form.label>
                                <p class="mt-[4px] text-[16px] font-[400] text-[#000000]">{{ $customer->email_2 ?: '-' }}
                                </p>
                            </div>
                        </div>

                        <!-- Phone Numbers -->
                        <div class="grid grid-cols-1  md:grid-cols-2 gap-6 mt-[24px]">
                            <div>
                                <x-form.label
                                    class="!text-[14px] !font-[400] !text-[#000000]">{{ __('business.customer.phone_1') }}</x-form.label>
                                <p class="mt-[4px] text-[16px] font-[400] text-[#000000]">
                                    {{ $customer->isd_code }}-{{ $customer->phone_1 }}</p>
                            </div>
                            <div>
                                <x-form.label
                                    class="!text-[14px] !font-[400] !text-[#000000]">{{ __('business.customer.phone_2') }}</x-form.label>
                                <p class="mt-[4px] text-[16px] font-[400] text-[#000000]">{{ $customer->phone_two }}
                                </p>
                            </div>
                        </div>

                        <!-- Address -->
                        <div class="grid grid-cols-1  md:grid-cols-2 gap-6 mt-[24px]">
                            <div class="col-span-2">
                                <x-form.label
                                    class="!text-[14px] !font-[400] !text-[#000000]">{{ __('business.customer.address') }}</x-form.label>
                                <p class="mt-[1px] text-[16px] font-[400] text-[#000000]">
                                    {{ $customer->address }}{{ $customer->street ? ', ' . $customer->street : '' }}</p>
                                <p class="mt-[1px] text-[16px] font-[400] text-[#000000]">
                                    {{ __('business.customer.zip_code') }}: {{ $customer->zip_code }}</p>
                                <p class="mt-[1px] text-[16px] font-[400] text-[#000000]">{{ $customer->city_name }},
                                    {{ $customer->state_name }}, {{ $customer->country_name }}</p>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Technician Notes Section -->
        <div class="white-box shadow-sm py-[24px] px-[20px] rounded-[16px]">
            <div x-data="{ open: true }">
                <div @click="open = !open" class="accordian-header"
                    :class="open ? 'manage-template-heading cursor-pointer show' : 'manage-template-heading cursor-pointer'">
                    <div class="flex gap-[10px] items-center">
                        <span
                            class="m-0 min-w-[28px] w-[28px] h-[28px] rounded-[50%] bg-[#0D44EA] text-white text-[14px] font-[500] flex items-center justify-center">2</span>
                        <p class="m-0">{{ __('business.customer.technician_notes') }}</p>
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
                    <div class="px-6 pb-6 max-[767px]:px-0">
                        <!-- Pool Size -->
                        <div class="mb-6">
                            <x-form.label>{{ __('business.customer.pool_details') }}</x-form.label>
                            @if ($customer->pool_size_gallons)
                                <p class="text-[14px] font-[600] text-[#212529]">
                                    {{ number_format($customer->pool_size_gallons, 2) }}
                                    {{ ucfirst(__('business.work_orders.gallons')) }}
                                </p>
                            @else
                                <p class="text-[14px] font-[600] text-gray-500">{{ __('business.customer.no_pool_size') }}
                                </p>
                            @endif
                        </div>

                        <!-- Past Visits -->
                        <div>
                            <x-form.label class="!text-blue">{{ __('business.customer.past_visits') }}</x-form.label>
                            @if ($pastVisits->isNotEmpty())
                                <div class="table-box !p-0">
                                    <table class="min-w-full divide-y divide-gray-300 table-nowrap" aria-describedby="Customer List">
                                        <thead>
                                            <tr>
                                                <th scope="col">
                                                    {{ __('business.customer.visit_date') }}
                                                </th>
                                                <th scope="col">
                                                    {{ __('business.customer.technician_name') }}
                                                </th>
                                                <th scope="col">
                                                    {{ __('business.work_orders.table.name') }}
                                                </th>
                                                <th scope="col">
                                                    {{ __('business.customer.status.label') }}
                                                </th>
                                                <th scope="col">
                                                    {{ __('business.customer.notes') }}
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($pastVisits as $visit)
                                                @php
                                                    $preferredStartDate = Carbon\Carbon::parse(
                                                        $visit->preferred_start_date->format('Y-m-d') .
                                                            ' ' .
                                                            $visit->preferred_start_time,
                                                        'UTC',
                                                    );
                                                    // Convert to business timezone and return only the date
                                                    $visitDate = $preferredStartDate
                                                        ->setTimezone($customer->business->timezone)
                                                        ->format('M d Y');
                                                    $status = $visit->isCompleted()
                                                        ? $visit->status->label()
                                                        : $visit->calculateJobStatus();
                                                    $statusClass = $visit->isCompleted()
                                                        ? $visit->status->color()
                                                        : 'status-pending';

                                                    if ($visit->type == 'WO') {
                                                        $viewLink = $visit->instance_id
                                                            ? route('business.work-orders.show_assignment_completed', [
                                                                $visit->instance_id,
                                                            ])
                                                            : route('business.work-orders.show', [
                                                                'workOrder' => $visit->id,
                                                            ]);
                                                    } else {
                                                        $viewLink = $visit->instance_id
                                                            ? route(
                                                                'business.work-orders.maintenance.show_assignment_completed',
                                                                [$visit->instance_id],
                                                            )
                                                            : route('business.work-orders.maintenance.show', [
                                                                'maintenance' => $visit->id,
                                                            ]);
                                                    }
                                                @endphp
                                                <tr>
                                                    <td data-label="Visit Date">
                                                        {{ $visitDate }}
                                                    </td>
                                                    <td data-label="Technician Name">
                                                        {{ $visit->technician?->fullName ?? '-' }}
                                                    </td>
                                                    <td data-label="{{ __('business.work_orders.table.name') }}">
                                                        <a class="min-[641px]:max-w-[280px] min-[641px]:inline-flex underline"
                                                            href="{{ $viewLink }}"
                                                            target="_blank">{{ $visit->name }}</a>
                                                    </td>
                                                    <td data-label="Status" class="status-td max-[640px]:justify-end">
                                                        <span class="badge {{ $statusClass }} max-[640px]:!inline-flex">
                                                            {{ $status ?? '-' }}
                                                        </span>
                                                    </td>
                                                    <td data-label="Notes">
                                                        <span class="min-[641px]:max-w-[350px] min-[641px]:inline-flex">
                                                            {{ $visit->communication_notes ?: '-' }}</span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-gray-500 text-center py-4">{{ __('business.customer.no_past_visits') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @if ($customer->status)
            <div class="flex gap-4 mt-[36px] max-[640px]:flex-col">
                <x-form.link link="{{ route('business.work-orders.customer.create', ['customer' => $customer->id]) }}"
                    class="btn-box btn max-[600px]:w-full">
                    {{ __('business.customer.create_work_order') }}
                </x-form.link>
                <x-form.link
                    link="{{ route('business.work-orders.maintenance.customer.create', ['customer' => $customer->id]) }}"
                    class="btn-box btn max-[600px]:w-full">
                    {{ __('business.customer.create_maintenance_order') }}
                </x-form.link>
            </div>
        @endif
    </div>
@endsection
