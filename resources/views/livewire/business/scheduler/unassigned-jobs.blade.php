<div class="mt-[24px] p-0 rounded-[8px] bg-scheduler-main-purple bg-white" x-data="scheduler"
    @scheduler-unassign-job.window="$wire.unassignJob($event.detail.jobId)"
    @clearVisualStates.window="clearDragVisualStates()" @setPendingAssignment.window="setPendingAssignment()"
    @resetPendingAssignment.window="resetPendingAssignment()" @touchmove.prevent="handleTouchMove($event)"
    @touchend="handleTouchEnd($event)">
    <h2 class="flex items-center text-[16px] font-[600] text-white py-[25px] px-[20px] rounded-[8px] job-unassigned-bg">
        <svg class="mr-[8px]" width="20" height="20" viewBox="0 0 20 20" fill="none"
            xmlns="http://www.w3.org/2000/svg">
            <path d="M6.66406 1.66797V5.0013" stroke="white" stroke-width="1.66667" stroke-linecap="round"
                stroke-linejoin="round" />
            <path d="M13.3359 1.66797V5.0013" stroke="white" stroke-width="1.66667" stroke-linecap="round"
                stroke-linejoin="round" />
            <path
                d="M15.8333 3.33203H4.16667C3.24619 3.33203 2.5 4.07822 2.5 4.9987V16.6654C2.5 17.5858 3.24619 18.332 4.16667 18.332H15.8333C16.7538 18.332 17.5 17.5858 17.5 16.6654V4.9987C17.5 4.07822 16.7538 3.33203 15.8333 3.33203Z"
                stroke="white" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M2.5 8.33203H17.5" stroke="white" stroke-width="1.66667" stroke-linecap="round"
                stroke-linejoin="round" />
        </svg>
        {{ __('business.scheduler.unassigned_jobs') }} ({{ count($unassignedJobs) }})
    </h2>
    <div class="flex mt-[24px] px-[16px] pb-[12px]">
        <div class="flex overflow-x-auto custom-scrollbar space-x-4 pb-[11px]" x-ref="unassignedContainer"
            @mousedown="panStart($event, 'unassignedContainer')"
            @touchstart.passive="panStart($event, 'unassignedContainer')">
            @foreach ($unassignedJobs as $job)
                <div data-job-id="{{ $job['id'] }}" data-instance-id="{{ $job['instance_id'] ?? '' }}"
                    data-assignment-id="{{ $job['assignment_id'] ?? '' }}"
                    class="flex-shrink-0 w-64 p-4 rounded-xl shadow-md text-white cursor-grab schedule-cards job-touch {{ $jobTypes[$job['type']]['cardClass'] ?? $jobTypes['default']['cardClass'] }}"
                    draggable="true"
                    x-on:dragstart.self="handleDragStart({event: $event, jobId: {{ $job['id'] }}, source: 'unassigned', instanceId: '{{ $job['instance_id'] ?? '' }}', originalTechId: null, originalDateIso: null, time: '{{ $job['time'] ?? '' }}', assignAllFuture: {{ isset($job['assignment_id']) && $job['is_recurring'] ? 'true' : 'false' }}})"
                    x-on:dragend.self="handleDragEnd($event)"
                    @touchstart="handleTouchStart($event, {{ $job['id'] }}, 'unassigned')">
                    <div class="schedule-card-title">
                        {!! $jobTypes[$job['type']]['icon'] ?? $jobTypes['default']['icon'] !!}
                        <span>{{ strtoupper($job['type']) }}</span>
                    </div>
                    <div class="mt-3 text-sm">
                        <p class="flex items-center capitalize text-[12px] font-[400] text-white">
                            <span
                                class="h-[8px] w-[8px] rounded-full mr-[4px] bg-status-{{ strtolower($job['status']) }}"></span>
                            <span class="job-status-label">{{ $job['status'] }}</span>
                            <span
                                class="ml-1 job-recurring-label">{{ $job['is_recurring'] ? '(Recurring)' : '' }}</span>
                        </p>
                        <p class="text-[12px] font-[400] text-white mt-[7px] job-datetime"
                            id="job-datetime-{{ $job['id'] }}">
                            {{ \Carbon\Carbon::parse($job['datetime'])->format('F j, Y | g:i A') }}</p>
                        <p class="text-[14px] font-[500] text-white mt-[7px] job-name"
                            id="job-name-{{ $job['id'] }}">{{ $job['name'] }}</p>
                        @if (isset($job['customer']))
                            <p class="text-[11px] font-[400] text-white mt-[4px]"
                                id="job-customer-{{ $job['id'] }}">
                                {{ $job['customer']['name'] ?? 'Unknown Customer' }}</p>
                        @endif
                        <x-dropdown.kebab-menu>
                            <x-slot name="title">Quick Actions</x-slot>
                            <a href="{{ $job['type'] === 'Maintenance Order' ? route('business.work-orders.maintenance.edit', $job['id']) : route('business.work-orders.edit', $job['id']) }}"
                                class="block">
                                {{ __('business.scheduler.edit_job') }}
                            </a>
                            <button x-data
                                x-on:click="$dispatch('assign-modal-open', { jobId: {{ $job['id'] }}, techId: null, instanceId: '{{ $job['instance_id'] ?? '' }}', dateIso: '{{ \Carbon\Carbon::parse($job['datetime'])->format('Y-m-d') }}', time: '{{ \Carbon\Carbon::parse($job['datetime'])->format('H:i:s') }}', assignAllFuture: {{ isset($job['assignment_id']) && $job['is_recurring'] ? 'true' : 'false' }} })"
                                class="block">
                                {{ __('business.scheduler.assign_only_this_job') }}
                            </button>
                        </x-dropdown.kebab-menu>
                    </div>
                </div>
            @endforeach
        </div>

        @if (count($unassignedJobs) == 0)
            <div class="flex items-center justify-center w-full h-full">
                <p>{{ __('business.scheduler.no_unassigned_jobs') }}</p>
            </div>
        @endif
    </div>
</div>
