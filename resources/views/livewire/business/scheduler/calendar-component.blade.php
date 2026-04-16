<div class="sheduler-calendar" x-data="scheduler" @dragover.throttle.100ms="handleDragScroll($event)"
    @touchmove.prevent="handleTouchMove($event)" @touchend="handleTouchEnd($event)"
    @setPendingAssignment.window="setPendingAssignment()" @resetPendingAssignment.window="resetPendingAssignment()"
    @clearVisualStates.window="clearDragVisualStates()" @begin-assign-processing.window="isSubmittingAssignment = true"
    @end-assign-processing.window="isSubmittingAssignment = false">
    <div class="sheduler-search-bar">
        <div
            class="relative flex-grow max-w-xs bg-white rounded-[10px] max-[769px]:border-[1px] max-[769px]:border-[#EFF6FF] max-[769px]:w-full max-[769px]:max-w-full">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg width="19" height="20" viewBox="0 0 19 20" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M16.6416 16.8071L12.0583 12.2238M13.586 8.40435C13.586 11.3575 11.192 13.7516 8.23882 13.7516C5.28563 13.7516 2.8916 11.3575 2.8916 8.40435C2.8916 5.45116 5.28563 3.05713 8.23882 3.05713C11.192 3.05713 13.586 5.45116 13.586 8.40435Z"
                        stroke="#212529" stroke-width="1.83333" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
            <input type="text" placeholder="{{ __('business.work_orders.search') }}"
                wire:model.live.debounce.300ms="searchTerm"
                class="w-full pl-10 pr-4 py-2 rounded-[10px] text-[14px] font-[400] text-[#000000] placeholder-black">
        </div>
        <div class="flex items-center space-x-2 text-gray-700 max-[769px]:justify-center max-[769px]:w-full"
            id="week-navigation">
            <button wire:click="prevWeek"
                class="bg-white cursor-pointer p-[13px] rounded-[10px] border-[1px] max-[365px]:mr-[0] border-[#E5E7EB] max-[769px]:border-0"
                id="prev-week-button">
                <svg width="17" height="17" viewBox="0 0 17 17" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path d="M10.6426 12.9321L6.64258 8.93213L10.6426 4.93213" stroke="#020817" stroke-width="1.33333"
                        stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>
            <div class="text-[17px] font-[600] text-white text-center min-w-48 max-[365px]:min-w-auto max-[365px]:m-[0] max-[769px]:text-[#111B45] max-[769px]:text-[14px] max-[769px]:font-[500]"
                id="week-range">
                @php
                    $weekRange = $this->getCurrentWeekRangeDisplay();
                @endphp
                {{ $weekRange['start'] }} - {{ $weekRange['end'] }}
            </div>
            <button wire:click="nextWeek"
                class="bg-white cursor-pointer p-[13px] rounded-[10px] border-[1px] max-[365px]:m-[0] border-[#E5E7EB] max-[769px]:border-0"
                id="next-week-button">
                <svg width="17" height="17" viewBox="0 0 17 17" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path d="M6.93262 12.9321L10.9326 8.93213L6.93262 4.93213" stroke="#020817" stroke-width="1.33333"
                        stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>
        </div>
    </div>

    <div class="overflow-x-auto custom-scrollbar space-x-4" x-ref="gridContainer"
        @mousedown="panStart($event, 'gridContainer')" @touchstart.passive="panStart($event, 'gridContainer')">
        <div class="inline-block min-w-full align-middle">
            <div class="grid bg-[#F9FAFB] border-b-[#E5E7EB] border-b-[1px]"
                style="grid-template-columns: 105px repeat(7, minmax(105px, 1fr));" id="day-headers">
                <div class="sticky-tech-info bg-[#F9FAFB]"></div>
                @foreach ($weekDates as $day)
                    <div class="text-center py-[14px] day-header" id="day-header-{{ $day['iso'] }}">
                        <p class="font-[500] text-[13px] text-[#374151] leading-[19px]">{{ $day['dayName'] }}</p>
                        <p class="font-[500] text-[11px] text-[#6B7280] leading-[15px]">{{ $day['dateFormatted'] }}</p>
                    </div>
                @endforeach
            </div>
            <div class="space-y-4">
                @php
                    $weekStart = \Carbon\Carbon::parse($weekDates[0]['iso']);
                    $weekEnd = \Carbon\Carbon::parse($weekDates[6]['iso']);
                @endphp
                @foreach ($filteredTechnicians as $tech)
                    <div wire:key="tech-{{ $tech['id'] }}"
                        class="grid border-b-[#E5E7EB] bg-white border-b-[1px] gap-[8px] items-start py-[16px] pr-[10px]"
                        style="grid-template-columns: 121px repeat(7, minmax(121px, 1fr));">
                        <div class="sticky-tech-info bg-white pl-[20px] self-stretch">
                            <div class="flex items-start flex-col">
                                <span
                                    class="flex-shrink-0 h-10 w-10 rounded-full bg-[#0D44EA] text-white flex items-center justify-center text-[14px] font-[500] mb-[12px]">{{ $tech['initials'] }}</span>
                                <div class="flex flex-col">
                                    <p class="text-[14px] font-[500] text-[#111827] technician-name">{{ $tech['name'] }}
                                    </p>
                                    @php
                                        $totalJobs = 0;
                                        if (isset($tech['schedule'])) {
                                            foreach ($tech['schedule'] as $day => $daySchedule) {
                                                if (
                                                    !isset($daySchedule['isNotAvailable']) &&
                                                    is_array($daySchedule) &&
                                                    \Carbon\Carbon::parse($day)->between($weekStart, $weekEnd)
                                                ) {
                                                    $totalJobs += count($daySchedule);
                                                }
                                            }
                                        }
                                    @endphp
                                    @if ($totalJobs > 0)
                                        <button
                                            x-on:click="expandedTechs[{{ $tech['id'] }}] = !expandedTechs[{{ $tech['id'] }}]"
                                            class="flex items-center text-[12px] font-[400] text-[#0D44EA] text-left hover:underline cursor-pointer">
                                            <span
                                                x-text="(expandedTechs[{{ $tech['id'] }}] || false) ? '{{ __('business.scheduler.see_less') }}' : '{{ __('business.scheduler.see_all') }}'"></span>
                                            <svg width="15" height="15" viewBox="0 0 15 15" fill="none"
                                                xmlns="http://www.w3.org/2000/svg"
                                                x-show="expandedTechs[{{ $tech['id'] }}] || false">
                                                <path d="M4.43262 8.9679L7.93262 5.4679L11.4326 8.9679" stroke="#0D44EA"
                                                    stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <svg width="15" height="15" viewBox="0 0 15 15" fill="none"
                                                xmlns="http://www.w3.org/2000/svg"
                                                x-show="!(expandedTechs[{{ $tech['id'] }}] || false)">
                                                <path d="M11.4326 5.70361L7.93262 9.20361L4.43262 5.70361"
                                                    stroke="#0D44EA" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @foreach ($weekDates as $day)
                            <div wire:key="slot-{{ $tech['id'] }}-{{ $day['iso'] }}"
                                class="daily-slot min-h-[120px] transition-colors duration-200 flex flex-col gap-[8px]"
                                data-tech-id="{{ $tech['id'] }}" data-date-iso="{{ $day['iso'] }}"
                                :class="{
                                    'day-not-available': {{ isset($tech['schedule'][$day['iso']]['isNotAvailable']) ? 'true' : 'false' }},
                                    'drag-over-slot': dragoverTechId === {{ $tech['id'] }} &&
                                        dragoverDateIso === '{{ $day['iso'] }}' && !
                                        {{ isset($tech['schedule'][$day['iso']]['isNotAvailable']) ? 'true' : 'false' }}
                                }"
                                @dragover.prevent="handleDragOverSlot({{ $tech['id'] }}, '{{ $day['iso'] }}', {{ isset($tech['schedule'][$day['iso']]['isNotAvailable']) ? 'true' : 'false' }})"
                                @dragleave.prevent="handleDragLeaveSlot()"
                                @drop.prevent="handleDropInSlot($event, {{ $tech['id'] }}, '{{ $day['iso'] }}', {{ isset($tech['schedule'][$day['iso']]['isNotAvailable']) ? 'true' : 'false' }})">

                                @if (isset($tech['schedule'][$day['iso']]['isNotAvailable']))
                                    <div class="day-not-available-text">{{ __('business.scheduler.not_available') }}
                                    </div>
                                @else
                                    @foreach ($tech['schedule'][$day['iso']] ?? [] as $index => $job)
                                        <div wire:key="job-{{ $job['id'] }}-{{ $job['instance_id'] ?? $index }}"
                                            x-show="(expandedTechs[{{ $tech['id'] }}] || false) || {{ $index }} === 0"
                                            x-transition>
                                            <div data-job-id="{{ $job['id'] }}"
                                                data-instance-id="{{ $job['instance_id'] ?? '' }}"
                                                data-time="{{ \Carbon\Carbon::parse($job['datetime'])->format('H:i:s') }}"
                                                x-on:dragstart.self="handleDragStart({event: $event, jobId: {{ $job['id'] }}, source: 'assigned', instanceId: '{{ $job['instance_id'] ?? '' }}', originalTechId: {{ $tech['id'] }}, originalDateIso: '{{ $day['iso'] }}'})"
                                                x-on:dragend.self="handleDragEnd($event)"
                                                @touchstart="handleTouchStart($event, {{ $job['id'] }}, 'assigned')"
                                                draggable="false" class="job-touch transition-all duration-200"
                                                :class="{
                                                    'border-2 border-blue-500': dragoverTechId ===
                                                        {{ $tech['id'] }} &&
                                                        dragoverDateIso === '{{ $day['iso'] }}' && dragOrigin
                                                        ?.source === 'assigned',
                                                    'transform translate-y-1': dragoverTechId ===
                                                        {{ $tech['id'] }} &&
                                                        dragoverDateIso === '{{ $day['iso'] }}'
                                                }"
                                                @dragover.prevent @drop.prevent>
                                                <div
                                                    class="job-boxes {{ $job['type'] === 'Maintenance Order' ? 'theme-maintenance-box' : 'theme-work-box' }}">
                                                    <div class="job-box-title">
                                                        @if ($job['type'] === 'Maintenance Order')
                                                            <svg width="16" height="16" viewBox="0 0 16 16"
                                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path
                                                                    d="M8.37333 4.05329C8.83184 3.32071 9.15687 2.51266 9.33333 1.66663C9.66667 3.33329 10.6667 4.93329 12 5.99996C13.3333 7.06663 14 8.33329 14 9.66663C14.0038 10.5882 13.7339 11.4901 13.2245 12.258C12.7151 13.026 11.9892 13.6254 11.1388 13.9803C10.2883 14.3352 9.35161 14.4296 8.44745 14.2515C7.54328 14.0734 6.71236 13.6309 6.06 12.98M4.66667 10.52C6.13333 10.52 7.33333 9.29996 7.33333 7.81996C7.33333 7.04663 6.95333 6.31329 6.19333 5.69329C5.43333 5.07329 4.86 4.15329 4.66667 3.18663C4.47333 4.15329 3.90667 5.07996 3.14 5.69329C2.37333 6.30663 2 7.05329 2 7.81996C2 9.29996 3.2 10.52 4.66667 10.52Z"
                                                                    stroke="black" stroke-width="1.33333"
                                                                    stroke-linecap="round" stroke-linejoin="round" />
                                                            </svg>
                                                        @else
                                                            <svg width="16" height="16" viewBox="0 0 16 16"
                                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path
                                                                    d="M10.4188 5.08762C10.1548 4.82361 10.0228 4.6916 9.97331 4.53939C9.92981 4.40549 9.92981 4.26126 9.97331 4.12736C10.0228 3.97514 10.1548 3.84314 10.4188 3.57913L12.311 1.68688C11.8089 1.45979 11.2515 1.33337 10.6645 1.33337C8.4554 1.33337 6.66454 3.12424 6.66454 5.33337C6.66454 5.66073 6.70386 5.97891 6.77805 6.28343C6.85749 6.60953 6.89721 6.77259 6.89015 6.87559C6.88277 6.98343 6.86669 7.04081 6.81696 7.13678C6.76946 7.22846 6.67844 7.31947 6.49642 7.5015L2.33121 11.6667C1.77892 12.219 1.77892 13.1144 2.33121 13.6667C2.88349 14.219 3.77892 14.219 4.33121 13.6667L8.49642 9.5015C8.67844 9.31947 8.76946 9.22846 8.86113 9.18095C8.9571 9.13122 9.01448 9.11514 9.12232 9.10776C9.22533 9.10071 9.38838 9.14043 9.71448 9.21987C10.019 9.29405 10.3372 9.33337 10.6645 9.33337C12.8737 9.33337 14.6645 7.54251 14.6645 5.33337C14.6645 4.74644 14.5381 4.18902 14.311 3.68688L12.4188 5.57913C12.1548 5.84314 12.0228 5.97514 11.8706 6.0246C11.7367 6.06811 11.5924 6.06811 11.4585 6.0246C11.3063 5.97514 11.1743 5.84314 10.9103 5.57913L10.4188 5.08762Z"
                                                                    stroke="black" stroke-width="1.33333"
                                                                    stroke-linecap="round" stroke-linejoin="round" />
                                                            </svg>
                                                        @endif
                                                        <span>{{ strtoupper($job['type']) }}</span>
                                                    </div>
                                                    <!-- Show datetime only when expanded -->
                                                    <p class="time-box-discp job-datetime"
                                                        x-show="expandedTechs[{{ $tech['id'] }}] || false">
                                                        {{ Carbon\Carbon::parse($job['datetime'])->format('F j, Y | g:i A') }}
                                                    </p>
                                                    <!-- Show job name only when expanded -->
                                                    <p class="job-box-discp truncate"
                                                        x-show="expandedTechs[{{ $tech['id'] }}] || false">
                                                        {{ $job['name'] }}</p>
                                                    <div class="job-box-status">
                                                        <span
                                                            class="h-2 w-2 bg-current rounded-full mr-1.5 bg-status-{{ $job['status_class'] ?? 'status-pending' }}"></span>
                                                        <p>{{ $job['status'] }}</p>
                                                    </div>
                                                    @if ($job['status'] == 'Upcoming' || $job['status'] == 'Pending' || $job['status'] == 'Completed')
                                                        <x-dropdown.kebab-menu :show="'expandedTechs[' . $tech['id'] . '] || false'">
                                                            <x-slot name="title">Quick Actions</x-slot>
                                                            @if ($job['status'] === 'Completed')
                                                                @php
                                                                    // Determine the correct view route based on job type and assignment
                                                                    if (isset($job['assignment_id'])) {
                                                                        $view_route =
                                                                            $job['type'] === 'Maintenance Order'
                                                                                ? route(
                                                                                    'business.work-orders.maintenance.show_assignment_completed',
                                                                                    $job['instance_id'],
                                                                                )
                                                                                : route(
                                                                                    'business.work-orders.show_assignment_completed',
                                                                                    $job['instance_id'],
                                                                                );
                                                                    } else {
                                                                        $view_route =
                                                                            $job['type'] === 'Maintenance Order'
                                                                                ? route(
                                                                                    'business.work-orders.maintenance.show',
                                                                                    $job['id'],
                                                                                )
                                                                                : route(
                                                                                    'business.work-orders.show',
                                                                                    $job['id'],
                                                                                );
                                                                    }
                                                                @endphp
                                                                <a href="{{ $view_route }}" class="block">
                                                                    {{ __('business.scheduler.view') }}
                                                                </a>
                                                            @elseif($job['status'] == 'Upcoming' || !$job['is_recurring'])
                                                                <a href="{{ $job['type'] === 'Maintenance Order' ? route('business.work-orders.maintenance.edit', $job['id']) : route('business.work-orders.edit', $job['id']) }}"
                                                                    class="block">
                                                                    {{ __('business.scheduler.edit_job') }}
                                                                </a>
                                                            @endif
                                                            @if ($job['status'] == 'Upcoming')
                                                                <button x-data
                                                                    x-on:click="$dispatch('assign-modal-open', { jobId: {{ $job['id'] }}, techId: {{ $tech['id'] }}, instanceId: '{{ $job['instance_id'] ?? '' }}', dateIso: '{{ $day['iso'] }}', time: '{{ $job['time'] ?? '' }}', assignAllFuture: false })"
                                                                    class="block">
                                                                    {{ __('business.scheduler.assign_only_this_job') }}
                                                                </button>
                                                                @if ($job['is_recurring'] && (empty($job['assignment_id']) || !empty($job['recurrence_rule'])))
                                                                    <button x-data
                                                                        x-on:click="$dispatch('assign-modal-open', { jobId: {{ $job['id'] }}, techId: {{ $tech['id'] }}, instanceId: '{{ $job['instance_id'] ?? '' }}', dateIso: '{{ $day['iso'] }}', time: '{{ $job['time'] ?? '' }}', assignAllFuture: true })"
                                                                        class="block">
                                                                        {{ __('business.scheduler.assign_all_future_jobs') }}
                                                                    </button>
                                                                @endif
                                                            @elseif ($job['status'] == 'Pending')
                                                                <button
                                                                    wire:click="cancelJob({{ $job['id'] }}, {{ $job['instance_id'] ?? '' }}, '{{ $job['datetime'] ?? '' }}')"
                                                                    class="block">
                                                                    {{ __('business.scheduler.cancel') }}
                                                                </button>
                                                            @endif
                                                        </x-dropdown.kebab-menu>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    @if (isset($tech['schedule'][$day['iso']]) && count($tech['schedule'][$day['iso']]) > 0)
                                        <div class="mt-[0px]">
                                            <div x-show="!(expandedTechs[{{ $tech['id'] }}] || false)"
                                                class="getjobsforday flex items-center font-[400] text-[10px] text-[#2563EB] justify-center">
                                                {{ __('business.scheduler.total_jobs') }}:
                                                {{ count($tech['schedule'][$day['iso']]) }}
                                            </div>
                                            <div x-show="expandedTechs[{{ $tech['id'] }}] || false"
                                                class="flex items-center justify-center" x-cloak>
                                                <button type="button"
                                                    wire:click="exportDayJobs({{ $tech['id'] }}, '{{ $day['iso'] }}')"
                                                    wire:loading.attr="disabled"
                                                    wire:loading.class="opacity-75 cursor-not-allowed"
                                                    wire:target="exportDayJobs"
                                                    class="font-[500] text-[14px] text-[#374151] border-[1px] border-[#DBEAFE] bg-[#FFFFFF] rounded-[6px] py-[5px] px-[8px] inline-flex gap-[8px] items-center w-full cursor-pointer">
                                                    <svg width="17" height="17" viewBox="0 0 17 17"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                            d="M5.8317 11.6835L8.49338 13.9646C8.61765 14.0736 8.77453 14.1293 8.93224 14.1294C9.04015 14.1295 9.14844 14.1035 9.24694 14.0506C9.29083 14.0271 9.33253 13.9984 9.3711 13.9646L12.0328 11.6835C12.3123 11.4439 12.3447 11.023 12.1051 10.7434C11.8655 10.4639 11.4447 10.4315 11.1651 10.6711L9.59895 12.0133L9.59895 6.85025C9.59895 6.48206 9.30047 6.18358 8.93228 6.18358C8.56409 6.18358 8.26562 6.48206 8.26562 6.85024L8.26562 12.0134L6.69936 10.6711C6.4198 10.4315 5.99894 10.4639 5.75934 10.7434C5.51974 11.023 5.55213 11.4439 5.8317 11.6835ZM12.9323 7.46273C12.9323 7.83092 12.6338 8.12939 12.2656 8.12939L11.2656 8.12939C10.8974 8.12939 10.599 8.42787 10.599 8.79606C10.599 9.16425 10.8974 9.46273 11.2656 9.46273L12.2656 9.46273C13.3702 9.46273 14.2656 8.5673 14.2656 7.46273L14.2656 5.46273C14.2656 4.35816 13.3702 3.46273 12.2656 3.46273L5.59896 3.46273C4.49439 3.46273 3.59896 4.35816 3.59896 5.46273L3.59896 7.46273C3.59896 8.5673 4.49439 9.46273 5.59896 9.46273L6.59896 9.46273C6.96715 9.46273 7.26563 9.16425 7.26563 8.79606C7.26563 8.42787 6.96715 8.12939 6.59896 8.12939L5.59896 8.12939C5.23077 8.12939 4.93229 7.83092 4.93229 7.46273L4.93229 5.46273C4.93229 5.09454 5.23077 4.79606 5.59896 4.79606L12.2656 4.79606C12.6338 4.79606 12.9323 5.09454 12.9323 5.46273L12.9323 7.46273Z"
                                                            fill="#374151" />
                                                    </svg>
                                                    <span> {{ __('business.scheduler.export_csv') }}</span>
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Assignment Modal -->
    <livewire:business.scheduler.assign-job-modal :jobId="$jobId" />

    <!-- Job Assignment Confirmation Modal -->
    @if ($showConfirmModal && !empty($pendingAssignment))
        <div x-data="{
            showConfirm: @entangle('showConfirmModal'),
            showUpdateConfirm: @entangle('showConfirmModal'),
            handleUpdateConfirm() {
                $wire.confirmAssignment();
            },
            handleUpdateCancel() {
                // Clear visual states first
                this.clearDragVisualStates();
                // Hide modal by setting Alpine.js state directly
                this.showConfirm = false;
                this.showUpdateConfirm = false;
                // Reset pending assignment state
                this.isPendingAssignment = false;
            }
        }" @close-confirm.window="$wire.confirmAssignment()"
            @close-cancel.window="handleUpdateCancel()">
            <x-confirm.confirm-modal title="{{ __('business.scheduler.confirm_assignment_title') }}"
                description="{{ __('business.scheduler.confirm_assignment_message', ['job_name' => $pendingAssignment['jobName'], 'technician_name' => $pendingAssignment['technicianName']]) }}"
                :btnCancel="__('business.scheduler.cancel')" :btnConfirm="__('business.scheduler.confirm')" />
        </div>
    @endif

    <!-- Job Cancellation Confirmation Modal -->
    @if ($showCancelConfirmModal && !empty($pendingCancellation))
        <div x-data="{
            showConfirm: @entangle('showCancelConfirmModal'),
            handleConfirm() {
                $wire.confirmCancellation();
            },
            handleCancel() {
                $wire.cancelCancellation();
            }
        }" @close-confirm.window="handleConfirm()" @close-cancel.window="handleCancel()">
            <x-confirm.confirm-modal title="{{ __('business.scheduler.confirm_cancellation_title') }}"
                description="{{ __('business.scheduler.confirm_cancellation_message', ['job_name' => $pendingCancellation['jobName']]) }}"
                :btnCancel="__('business.scheduler.cancel')" :btnConfirm="__('business.scheduler.confirm')" />
        </div>
    @endif

    <!-- Use shared loading component styles -->
    <x-loading target="confirmAssignment" />
    <x-loading target="assignJob" />
    <x-loading target="handleAssignJobEvent" />
    <x-loading target="prevWeek" />
    <x-loading target="nextWeek" />
    <x-loading target="updateJobOrder" />
    <x-loading target="exportDayJobs" />

    <!-- Alpine-driven overlay for immediate feedback between drop and confirm popup -->
    <div x-cloak x-show="isSubmittingAssignment"
        class="loader fixed top-0 left-0 opacity-50 inset-0 z-50 flex items-center justify-center bg-gray-50 bg-opacity-90 rounded-lg w-full h-full">
        <div class="flex absolute top-[50%] left-[50%] transform -translate-x-1/2 -translate-y-1/2">
            <div class="animate-spin rounded-full h-8 w-8 border-b-3 border-indigo-800"></div>
        </div>
    </div>

</div>
