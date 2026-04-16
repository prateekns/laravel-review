<div>
    @if ($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto assign-job-modal" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen text-center max-[767px]:p-[10px]">
                <!-- Background overlay -->
                <div class="fixed inset-0 transition-opacity bg-[#000000] opacity-[40%]" aria-hidden="true"></div>

                <!-- Modal panel -->
                <div
                    class="relative  inline-flex flex-col w-full max-w-[710px] min-[767px]:min-w-[710px] py-[24px] overflow-hidden text-left align-bottom transition-all transform bg-white rounded-[12px] px-[24px]">
                    <div class="inline-flex flex-col max-w-[475px] mx-auto">
                        <h2 class="text-[#1D242B] font-[700] text-[32px] leading-[42px] text-center mb-[23px]">
                            @if ($assignAllFuture)
                                {{ __('business.scheduler.assign_all_future_jobs') }}
                            @else
                                {{ __('business.scheduler.assign_only_this_job') }}
                            @endif
                        </h2>

                        <!-- Job Details Box -->
                        <div
                            class="px-[12px] py-[10px] mb-[23px] rounded-[12px]
                            @if (strtoupper($job->type_label) === 'MAINTENANCE ORDER') maintenance-detail-box
                            @else
                            work-orderDetail-box @endif">
                            <div class="flex items-center mb-1">
                                @if ($job->type === 'MO')
                                    <div class="m-detailBox">
                                        <svg width="15" height="15" viewBox="0 0 15 15" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M7.59141 3.78999C7.9926 3.14898 8.277 2.44194 8.43141 1.70166C8.72307 3.15999 9.59807 4.55999 10.7647 5.49333C11.9314 6.42666 12.5147 7.53499 12.5147 8.70166C12.5181 9.508 12.2819 10.2972 11.8362 10.9691C11.3905 11.6411 10.7553 12.1656 10.0112 12.4761C9.267 12.7866 8.4474 12.8692 7.65626 12.7134C6.86511 12.5576 6.13805 12.1704 5.56724 11.6008M4.34807 9.44833C5.63141 9.44833 6.68141 8.38083 6.68141 7.08583C6.68141 6.40916 6.34891 5.76749 5.68391 5.22499C5.01891 4.68249 4.51724 3.87749 4.34807 3.03166C4.17891 3.87749 3.68307 4.68833 3.01224 5.22499C2.34141 5.76166 2.01474 6.41499 2.01474 7.08583C2.01474 8.38083 3.06474 9.44833 4.34807 9.44833Z"
                                                stroke="#1E40AF" stroke-width="1.16667" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </svg>

                                        <span>{{ strtoupper($job->type_label) }}</span>
                                    </div>
                                @else
                                    <div class="wo-detailBox">
                                        <svg width="15" height="15" viewBox="0 0 15 15" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <g clip-path="url(#clip0_4529_35988)">
                                                <path
                                                    d="M9.24657 4.69463C9.01556 4.46362 8.90005 4.34812 8.85677 4.21493C8.81871 4.09777 8.81871 3.97157 8.85677 3.85441C8.90005 3.72122 9.01556 3.60571 9.24657 3.3747L10.9023 1.71898C10.4629 1.52028 9.97517 1.40967 9.4616 1.40967C7.5286 1.40967 5.9616 2.97667 5.9616 4.90967C5.9616 5.19611 5.99601 5.47451 6.06092 5.74097C6.13043 6.02631 6.16518 6.16898 6.15901 6.25911C6.15255 6.35347 6.13848 6.40367 6.09497 6.48765C6.0534 6.56786 5.97377 6.6475 5.81449 6.80677L2.16993 10.4513C1.68668 10.9346 1.68668 11.7181 2.16993 12.2013C2.65318 12.6846 3.43668 12.6846 3.91993 12.2013L7.56449 8.55677C7.72377 8.3975 7.8034 8.31786 7.88362 8.2763C7.96759 8.23278 8.0178 8.21871 8.11216 8.21225C8.20229 8.20608 8.34496 8.24084 8.6303 8.31035C8.89676 8.37526 9.17516 8.40967 9.4616 8.40967C11.3946 8.40967 12.9616 6.84266 12.9616 4.90967C12.9616 4.3961 12.851 3.90836 12.6523 3.46898L10.9966 5.1247C10.7656 5.35571 10.6501 5.47122 10.5169 5.51449C10.3997 5.55256 10.2735 5.55256 10.1563 5.51449C10.0231 5.47122 9.90764 5.35571 9.67663 5.1247L9.24657 4.69463Z"
                                                    stroke="#07A3B9" stroke-width="1.16667" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                            </g>
                                            <defs>
                                                <clipPath id="clip0_4529_35988">
                                                    <rect width="14" height="14" fill="white"
                                                        transform="translate(0.130371 0.243164)" />
                                                </clipPath>
                                            </defs>
                                        </svg>

                                        <span>{{ strtoupper($job->type_label) }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="inline-block w-full">
                                <span
                                    class="text-[12px] font-[600] text-[#212529] leading-[16px]">{{ __('business.work_orders.table.name') }}:
                                </span>
                                <span
                                    class="text-[12px] font-[400] text-[#212529] leading-[16px]">{{ $job->name ?? '' }}</span>
                            </div>
                            <div class="inline-block w-full">
                                <span
                                    class="text-[12px] font-[600] text-[#212529] leading-[16px]">{{ __('business.work_orders.maintenance_type') }}:
                                </span>
                                <span
                                    class="text-[12px] font-[400] text-[#212529] leading-[16px]">{{ $job->work_type_text }}</span>
                            </div>
                            <div class="inline-block w-full">
                                <span
                                    class="text-[12px] font-[600] text-[#212529] leading-[16px]">{{ __('business.work_orders.table.customer_name') }}:
                                </span>
                                <span
                                    class="text-[12px] font-[400] text-[#212529] leading-[16px]">{{ $job->customer->customer_name ?? '' }}</span>
                            </div>

                        </div>

                        <!-- Technician Selection -->
                        <div class="mb-6">
                            <label for="selectTechnician" class="block text-[16px] font-medium text-[#111827] mb-2">
                                {{ __('business.scheduler.select_technician') }}
                            </label>
                            <select wire:model.live="selectedTechnicianId"
                                id="selectTechnician"
                                class="w-full form-select"
                                wire:loading.class="opacity-50"
                                wire:target="selectedTechnicianId">
                                <option value="">{{ __('business.scheduler.select_technician_placeholder') }}
                                </option>
                                @foreach ($availableTechnicians as $technician)
                                    <option value="{{ $technician['id'] }}">
                                        {{ $technician['name'] }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($selectedTechnicianId && $selectedTechnician && !$selectedTechnician['available']['status'])
                                <div class="error-message-box technician_id">
                                    {{ $selectedTechnician['available']['message'] }}
                                </div>
                            @endif
                        </div>

                        <!-- Action Buttons -->
                        <div
                            class="flex gap-[20px] max-[600px]:flex-col max-[600px]:w-full items-center justify-center">
                            <button wire:click="close" type="button" class="btn-box outlined max-[600px]:w-full">
                                {{ __('business.scheduler.cancel') }}
                            </button>
                            <button wire:click="assignJob" type="button"
                                class="btn-box btn max-[600px]:w-full disabled:opacity-50 disabled:cursor-not-allowed"
                                wire:loading.attr="disabled" wire:target="assignJob"
                                @if (
                                    !$selectedTechnicianId ||
                                        ($selectedTechnicianId &&
                                            $selectedTechnician &&
                                            isset($selectedTechnician['available']) &&
                                            isset($selectedTechnician['available']['status']) &&
                                            !$selectedTechnician['available']['status'])) disabled @endif>
                                {{ __('business.scheduler.confirm') }}
                            </button>
                        </div>

                        <!-- Modal-scoped loading overlay (matches shared @loading) -->
                        <x-loading target="assignJob" />
                        <!-- Loading overlay for technician selection -->
                        <div wire:loading wire:target="selectedTechnicianId"
                            class="loader fixed top-0 left-0 opacity-50 inset-0 z-50 flex items-center justify-center bg-gray-50 bg-opacity-90 rounded-lg w-full h-full">
                            <div class="flex absolute top-[50%] left-[50%] transform -translate-x-1/2 -translate-y-1/2">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-3 border-indigo-800"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
