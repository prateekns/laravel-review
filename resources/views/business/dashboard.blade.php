@extends('layouts.business.app')
@section('content')
<div class="dashboard-wrapper" x-data={showBanner:true}>
    <div class="w-full mx-auto">
        <div class="flex justify-between items-center flex-row max-[1200px]:flex-col add-sub-mobile-wrapper">
            <div class="gap-[16px] flex flex-col">
                <h1 class="main-heading">Welcome Back, {{$adminName}}!</h1>
            </div>
        </div>

        @if($massMessages && $massMessages->status)
            <div class="flex flex-col p-[24px] bg-[#FFFAE8] border-[1px] border-[#DFB400] rounded-[18px] gap-[12px] mt-[24px] relative" x-show="showBanner">
                <p class="font-[600] text-[24px] text-[#000000]">{{ __('Important message') }}</p>
                <p class="text-[20px] font-[400] text-[#374151]">
                    {{$massMessages->message}}
                </p>
            </div>
        @endif

        <div class="flex flex-col mt-[24px]">
            <div class="white-box !p-[24px] !mt-[0] mb-[24px]">
                <div class="sheduler-search-bar bg-waves">
                    <p class="font-[600] text-[20px] text-[#ffffff]">{{ __('Overall Stats') }}</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mt-[30px] pt-[0px] px-[16px] pb-[12px]">
                    <div class="bg-gradient-to-r from-[#E0E0E0] to-[#ACACAC] rounded-[8px] p-[17px] flex-inline items-center justify-center text-center">
                        <p class="text-[24px] font-[600] text-[#111827] leading-[32px]">{{$customers}}</p>
                        <p class="text-[14px] font-[500] text-[#111827] leading-[18px]">{{ __('Total Customers') }}</p>
                    </div>
                    <div class="bg-gradient-to-r from-[#E0E0E0] to-[#ACACAC]  rounded-[8px] p-[17px] flex-inline items-center justify-center text-center">
                        <p class="text-[24px] font-[600] text-[#111827] leading-[32px]">{{$technicians}}</p>
                        <p class="text-[14px] font-[500] text-[#111827] leading-[18px]">{{ __('Total Technicians') }}</p>
                    </div>
                    <div class="bg-gradient-to-r from-[#E0E0E0] to-[#ACACAC]  rounded-[8px] p-[17px] flex-inline items-center justify-center text-center">
                        <p class="text-[24px] font-[600] text-[#111827] leading-[32px]">{{$subadmins}}</p>
                        <p class="text-[14px] font-[500] text-[#111827] leading-[18px]">{{ __('Total Admins') }}</p>
                    </div>
                    <div class="bg-gradient-to-r from-[#E0E0E0] to-[#ACACAC] rounded-[8px] p-[17px] flex-inline items-center justify-center text-center">
                        <p class="text-[24px] font-[600] text-[#111827] leading-[32px]">{{$totalJobs}}</p>
                        <p class="text-[14px] font-[500] text-[#111827] leading-[18px]">{{ __('Total Jobs') }}</p>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Left Column: Quick Links -->
                <div class="lg:col-span-1 white-box !p-[24px] !mt-[0px]" id="quick-link-section">
                    <div class="bg-[#DBEAFE] rounded-[8px] p-[12px] mb-[30px]">
                        <h2 class="text-[20px] font-[600] text-[#0D44EA] leading-[40px]">{{ __('Quick Links') }}</h2>
                    </div>
                    <div class="gap-[32px] inline-flex flex-col w-full" id="quick-links">

                    <a href="{{route('business.work-orders.index')}}">
                        <div class="bg-white border-[2px] border-[#DBEAFE] rounded-[10px] py-[27px] px-[20px] flex items-center cursor-pointer shadow-[4px_4px_8px_0_rgba(11,37,72,0.1)]">
                            <div class="inline-flex mr-[16px]">
                                <svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect width="36" height="36" rx="18" fill="#DBEAFE" />
                                    <rect x="3" y="3" width="30" height="30" rx="15" fill="#2563EB" />
                                    <path d="M20.4209 15.0882C20.1569 14.8242 20.0248 14.6922 19.9754 14.54C19.9319 14.4061 19.9319 14.2619 19.9754 14.128C20.0248 13.9758 20.1569 13.8437 20.4209 13.5797L22.3131 11.6875C21.811 11.4604 21.2536 11.334 20.6666 11.334C18.4575 11.334 16.6666 13.1248 16.6666 15.334C16.6666 15.6613 16.7059 15.9795 16.7801 16.284C16.8596 16.6101 16.8993 16.7732 16.8922 16.8762C16.8848 16.984 16.8688 17.0414 16.819 17.1374C16.7715 17.2291 16.6805 17.3201 16.4985 17.5021L12.3333 21.6673C11.781 22.2196 11.781 23.115 12.3333 23.6673C12.8856 24.2196 13.781 24.2196 14.3333 23.6673L18.4985 19.5021C18.6805 19.3201 18.7715 19.2291 18.8632 19.1816C18.9592 19.1318 19.0166 19.1158 19.1244 19.1084C19.2274 19.1013 19.3905 19.141 19.7166 19.2205C20.0211 19.2947 20.3393 19.334 20.6666 19.334C22.8758 19.334 24.6666 17.5431 24.6666 15.334C24.6666 14.747 24.5402 14.1896 24.3131 13.6875L22.4209 15.5797C22.1569 15.8437 22.0248 15.9758 21.8726 16.0252C21.7387 16.0687 21.5945 16.0687 21.4606 16.0252C21.3084 15.9758 21.1764 15.8437 20.9124 15.5797L20.4209 15.0882Z" stroke="white" stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>

                            </div>
                            <span class="font-[400] text-[#212529] text-[16px]">{{ __('Work Orders') }}</span>
                        </div>
                    </a>

                    <a href="{{route('business.work-orders.maintenance.index')}}">
                        <div class="bg-white border-[2px] border-[#DBEAFE] rounded-[10px] py-[27px] px-[20px] flex items-center cursor-pointer shadow-[4px_4px_8px_0_rgba(11,37,72,0.1)]">
                            <div class="inline-flex mr-[16px]">
                                <svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect width="36" height="36" rx="18" fill="#DBEAFE" />
                                    <rect x="3" y="3" width="30" height="30" rx="15" fill="#2563EB" />
                                    <path d="M18.3733 14.0527C18.8318 13.3201 19.1569 12.512 19.3333 11.666C19.6667 13.3327 20.6667 14.9327 22 15.9993C23.3333 17.066 24 18.3327 24 19.666C24.0038 20.5875 23.7339 21.4895 23.2245 22.2574C22.7151 23.0254 21.9892 23.6248 21.1388 23.9797C20.2883 24.3346 19.3516 24.429 18.4474 24.2509C17.5433 24.0728 16.7124 23.6302 16.06 22.9793M14.6667 20.5193C16.1333 20.5193 17.3333 19.2993 17.3333 17.8193C17.3333 17.046 16.9533 16.3127 16.1933 15.6927C15.4333 15.0727 14.86 14.1527 14.6667 13.186C14.4733 14.1527 13.9067 15.0793 13.14 15.6927C12.3733 16.306 12 17.0527 12 17.8193C12 19.2993 13.2 20.5193 14.6667 20.5193Z" stroke="white" stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>

                            </div>
                            <span class="font-[400] text-[#212529] text-[16px]">{{ __('Maintenance Orders') }}</span>
                        </div>
                    </a>

                    <a href="{{route('reports.index')}}">
                        <div class="bg-white border-[2px] border-[#DBEAFE] rounded-[10px] py-[27px] px-[20px] flex items-center cursor-pointer shadow-[4px_4px_8px_0_rgba(11,37,72,0.1)]">
                            <div class="inline-flex mr-[16px]">
                                <svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect width="36" height="36" rx="18" fill="#DBEAFE" />
                                    <rect x="3" y="3" width="30" height="30" rx="15" fill="#2563EB" />
                                    <path d="M19.3333 11.5137V14.2674C19.3333 14.6407 19.3333 14.8274 19.406 14.97C19.4699 15.0955 19.5719 15.1975 19.6973 15.2614C19.8399 15.334 20.0266 15.334 20.4 15.334H23.1537M20.6666 18.6673H15.3333M20.6666 21.334H15.3333M16.6666 16.0007H15.3333M19.3333 11.334H15.8666C14.7465 11.334 14.1865 11.334 13.7586 11.552C13.3823 11.7437 13.0764 12.0497 12.8846 12.426C12.6666 12.8538 12.6666 13.4139 12.6666 14.534V21.4673C12.6666 22.5874 12.6666 23.1475 12.8846 23.5753C13.0764 23.9516 13.3823 24.2576 13.7586 24.4493C14.1865 24.6673 14.7465 24.6673 15.8666 24.6673H20.1333C21.2534 24.6673 21.8135 24.6673 22.2413 24.4493C22.6176 24.2576 22.9236 23.9516 23.1153 23.5753C23.3333 23.1475 23.3333 22.5874 23.3333 21.4673V15.334L19.3333 11.334Z" stroke="white" stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>

                            </div>
                            <span class="font-[400] text-[#212529] text-[16px]">{{ __('Reports') }}</span>
                        </div>
                    </a>

                    <a href="{{route('business.checklists.index')}}">
                        <div class="bg-white border-[2px] border-[#DBEAFE] rounded-[10px] py-[27px] px-[20px] flex items-center cursor-pointer shadow-[4px_4px_8px_0_rgba(11,37,72,0.1)]">
                            <div class="inline-flex mr-[16px]">
                                <svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect width="36" height="36" rx="18" fill="#DBEAFE" />
                                    <rect x="3" y="3" width="30" height="30" rx="15" fill="#2563EB" />
                                    <path d="M20.6666 12.6673C21.2866 12.6673 21.5966 12.6673 21.8509 12.7355C22.5411 12.9204 23.0802 13.4595 23.2651 14.1497C23.3333 14.404 23.3333 14.714 23.3333 15.334V21.4673C23.3333 22.5874 23.3333 23.1475 23.1153 23.5753C22.9236 23.9516 22.6176 24.2576 22.2413 24.4493C21.8135 24.6673 21.2534 24.6673 20.1333 24.6673H15.8666C14.7465 24.6673 14.1865 24.6673 13.7586 24.4493C13.3823 24.2576 13.0764 23.9516 12.8846 23.5753C12.6666 23.1475 12.6666 22.5874 12.6666 21.4673V15.334C12.6666 14.714 12.6666 14.404 12.7348 14.1497C12.9197 13.4595 13.4588 12.9204 14.149 12.7355C14.4033 12.6673 14.7133 12.6673 15.3333 12.6673M16 20.0007L17.3333 21.334L20.3333 18.334M16.4 14.0007H19.6C19.9733 14.0007 20.16 14.0007 20.3026 13.928C20.4281 13.8641 20.53 13.7621 20.594 13.6366C20.6666 13.494 20.6666 13.3074 20.6666 12.934V12.4007C20.6666 12.0273 20.6666 11.8406 20.594 11.698C20.53 11.5725 20.4281 11.4706 20.3026 11.4066C20.16 11.334 19.9733 11.334 19.6 11.334H16.4C16.0266 11.334 15.8399 11.334 15.6973 11.4066C15.5719 11.4706 15.4699 11.5725 15.406 11.698C15.3333 11.8406 15.3333 12.0273 15.3333 12.4007V12.934C15.3333 13.3074 15.3333 13.494 15.406 13.6366C15.4699 13.7621 15.5719 13.8641 15.6973 13.928C15.8399 14.0007 16.0266 14.0007 16.4 14.0007Z" stroke="white" stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>

                            </div>
                            <span class="font-[400] text-[#212529] text-[16px]">{{ __('Checklist') }}</span>
                        </div>
                    </a>

                    <a href="{{route('templates.index')}}">
                        <div class="bg-white border-[2px] border-[#DBEAFE] rounded-[10px] py-[27px] px-[20px] flex items-center cursor-pointer shadow-[4px_4px_8px_0_rgba(11,37,72,0.1)]">
                            <div class="inline-flex mr-[16px]">
                                <svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect width="36" height="36" rx="18" fill="#DBEAFE" />
                                    <rect x="3" y="3" width="30" height="30" rx="15" fill="#2563EB" />
                                    <path d="M20.6666 12.6673C21.2866 12.6673 21.5966 12.6673 21.8509 12.7355C22.5411 12.9204 23.0802 13.4595 23.2651 14.1497C23.3333 14.404 23.3333 14.714 23.3333 15.334V21.4673C23.3333 22.5874 23.3333 23.1475 23.1153 23.5753C22.9236 23.9516 22.6176 24.2576 22.2413 24.4493C21.8135 24.6673 21.2534 24.6673 20.1333 24.6673H15.8666C14.7465 24.6673 14.1865 24.6673 13.7586 24.4493C13.3823 24.2576 13.0764 23.9516 12.8846 23.5753C12.6666 23.1475 12.6666 22.5874 12.6666 21.4673V15.334C12.6666 14.714 12.6666 14.404 12.7348 14.1497C12.9197 13.4595 13.4588 12.9204 14.149 12.7355C14.4033 12.6673 14.7133 12.6673 15.3333 12.6673M16 20.0007L17.3333 21.334L20.3333 18.334M16.4 14.0007H19.6C19.9733 14.0007 20.16 14.0007 20.3026 13.928C20.4281 13.8641 20.53 13.7621 20.594 13.6366C20.6666 13.494 20.6666 13.3074 20.6666 12.934V12.4007C20.6666 12.0273 20.6666 11.8406 20.594 11.698C20.53 11.5725 20.4281 11.4706 20.3026 11.4066C20.16 11.334 19.9733 11.334 19.6 11.334H16.4C16.0266 11.334 15.8399 11.334 15.6973 11.4066C15.5719 11.4706 15.4699 11.5725 15.406 11.698C15.3333 11.8406 15.3333 12.0273 15.3333 12.4007V12.934C15.3333 13.3074 15.3333 13.494 15.406 13.6366C15.4699 13.7621 15.5719 13.8641 15.6973 13.928C15.8399 14.0007 16.0266 14.0007 16.4 14.0007Z" stroke="white" stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>

                            </div>
                            <span class="font-[400] text-[#212529] text-[16px]">{{ __('Templates') }}</span>
                        </div>
                    </a>

                    <a href="{{route('business.scheduler.index')}}">
                        <div class="bg-white border-[2px] border-[#DBEAFE] rounded-[10px] py-[27px] px-[20px] flex items-center cursor-pointer shadow-[4px_4px_8px_0_rgba(11,37,72,0.1)]">
                            <div class="inline-flex mr-[16px]">
                                <svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect width="36" height="36" rx="18" fill="#DBEAFE" />
                                    <rect x="3" y="3" width="30" height="30" rx="15" fill="#2563EB" />
                                    <path d="M24 16.6673H12M20.6667 11.334V14.0007M15.3333 11.334V14.0007M15.2 24.6673H20.8C21.9201 24.6673 22.4802 24.6673 22.908 24.4493C23.2843 24.2576 23.5903 23.9516 23.782 23.5753C24 23.1475 24 22.5874 24 21.4673V15.8673C24 14.7472 24 14.1872 23.782 13.7593C23.5903 13.383 23.2843 13.0771 22.908 12.8853C22.4802 12.6673 21.9201 12.6673 20.8 12.6673H15.2C14.0799 12.6673 13.5198 12.6673 13.092 12.8853C12.7157 13.0771 12.4097 13.383 12.218 13.7593C12 14.1872 12 14.7472 12 15.8673V21.4673C12 22.5874 12 23.1475 12.218 23.5753C12.4097 23.9516 12.7157 24.2576 13.092 24.4493C13.5198 24.6673 14.0799 24.6673 15.2 24.6673Z" stroke="white" stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>

                            </div>
                            <span class="font-[400] text-[#212529] text-[16px]">{{ __('Job Scheduler') }}</span>
                        </div>
                    </a>
                    </div>
                </div>

                <!-- Right Column: Unassigned Jobs -->
                <div class="lg:col-span-2 white-box !p-[24px] !mt-[0px]">
                    <div class="bg-[#DBEAFE] rounded-[8px] p-[12px] mb-[30px] flex justify-between items-center max-[540px]:flex-col  max-[540px]:items-start" id="unassigned-jobs-section">
                        <h2 class="text-[20px] font-[600] text-[#0D44EA] leading-[40px]">{{ __('Unassigned Jobs') }} ({{$unassignedJobsCount}})</h2>
                        <a href="{{route('business.scheduler.index')}}">
                            <button class="bg-[#0D44EA] rounded-[12px] text-[12px] font-[600] text-[#ffffff] p-[9.5px] min-w-[180px] leading-[21px] cursor-pointer">
                                {{ __('Assign Job') }}
                            </button>
                        </a>
                    </div>
                    <div class="space-y-4">
                        @forelse ($unassignedJobs as $job)
                            <div class="dashboard-cards  {{$job->type == 'WO' ? 'dash-work-bg' : 'dash-maintenance-bg'}}">
                                <div class="dash-card-title">
                                    @if($job->type == 'WO')
                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M10.4184 5.08823C10.1544 4.82422 10.0224 4.69221 9.97294 4.54C9.92944 4.4061 9.92944 4.26187 9.97294 4.12797C10.0224 3.97575 10.1544 3.84375 10.4184 3.57974L12.3107 1.68749C11.8085 1.4604 11.2511 1.33398 10.6642 1.33398C8.45503 1.33398 6.66417 3.12485 6.66417 5.33398C6.66417 5.66134 6.7035 5.97952 6.77768 6.28404C6.85712 6.61014 6.89684 6.7732 6.88979 6.8762C6.88241 6.98404 6.86633 7.04142 6.81659 7.13739C6.76909 7.22907 6.67808 7.32008 6.49605 7.50211L2.33084 11.6673C1.77855 12.2196 1.77855 13.115 2.33084 13.6673C2.88312 14.2196 3.77856 14.2196 4.33084 13.6673L8.49605 9.50211C8.67808 9.32008 8.76909 9.22907 8.86076 9.18156C8.95674 9.13183 9.01411 9.11575 9.12195 9.10837C9.22496 9.10132 9.38801 9.14104 9.71411 9.22048C10.0186 9.29466 10.3368 9.33398 10.6642 9.33398C12.8733 9.33398 14.6642 7.54312 14.6642 5.33398C14.6642 4.74705 14.5378 4.18963 14.3107 3.68749L12.4184 5.57974C12.1544 5.84375 12.0224 5.97575 11.8702 6.02521C11.7363 6.06872 11.5921 6.06872 11.4582 6.02521C11.3059 5.97575 11.1739 5.84375 10.9099 5.57974L10.4184 5.08823Z" stroke="black" stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    @else
                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M8.37333 4.05366C8.83184 3.32108 9.15687 2.51302 9.33333 1.66699C9.66667 3.33366 10.6667 4.93366 12 6.00033C13.3333 7.06699 14 8.33366 14 9.66699C14.0038 10.5885 13.7339 11.4904 13.2245 12.2584C12.7151 13.0263 11.9892 13.6258 11.1388 13.9807C10.2883 14.3355 9.35161 14.4299 8.44745 14.2518C7.54328 14.0738 6.71236 13.6312 6.06 12.9803M4.66667 10.5203C6.13333 10.5203 7.33333 9.30033 7.33333 7.82033C7.33333 7.04699 6.95333 6.31366 6.19333 5.69366C5.43333 5.07366 4.86 4.15366 4.66667 3.18699C4.47333 4.15366 3.90667 5.08033 3.14 5.69366C2.37333 6.30699 2 7.05366 2 7.82033C2 9.30033 3.2 10.5203 4.66667 10.5203Z" stroke="black" stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    @endif

                                    {{$job->type == 'WO' ? 'WORK ORDER' : 'MAINTINANCE ORDER'}}
                                </div>
                                <div class="dash-status">
                                    <span class="dash-status-dot bg-status-upcoming"></span>
                                    <p> {{$job->job_status}}</p>
                                </div>
                                <p class="dash-card-date">{{ $job->preferred_date}} | {{ $job->preferred_time}}</p>
                                <h3 class="dash-card-desc">{{$job->name}}</h3>
                                <p class="dash-card-date">{{$job->customer->name}}</p>
                            </div>
                        @empty
                            <div class="dashboard-cards dash-work-bg" id="no-jobs">
                                <p>{{ __('No unassigned jobs available.')}}</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
