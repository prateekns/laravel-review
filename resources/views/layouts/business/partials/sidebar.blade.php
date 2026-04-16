<!-- Desktop sidebar partial will go here -->
<!-- Off-canvas menu for mobile, show/hide based on off-canvas menu state. -->
<div class="fixed inset-0 bg-gray-900/80 mobile-menu-overlay hidden" aria-hidden="true"></div>
<div class="relative z-50 lg:hidden mobile-menu" role="dialog" aria-modal="true">
    <div class="fixed inset-0 flex">
        <div class="relative flex w-full max-w-xs flex-1 bg-blue">

            <!-- Sidebar component, swap this element with another sidebar if you like -->
            <div class="flex grow flex-col gap-y-8 overflow-y-auto px-5 py-[41px] ring-1 ring-white/10">
                <div class="flex h-16 shrink-0 items-center">
                    <img class="w-[224px] h-[57px]" src="{{ asset('images/dashboard-logo.svg') }}" alt="Pool Route">
                </div>
                <nav class="flex flex-1 flex-col" aria-label="Mainmenu">
                    <ul role="list" class="flex flex-1 flex-col gap-y-7">
                        <li>
                            <ul role="list" class="sidebar-list">
                                
                                <li>
                                <a href="{{ route('dashboard') }}" class=" {{ request()->routeIs('dashboard') ? ' active' : '' }}">
                                <svg width="20" height="21" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M10.8186 3.1035C10.5258 2.87579 10.3794 2.76194 10.2178 2.71817C10.0752 2.67955 9.92484 2.67955 9.78221 2.71817C9.62057 2.76194 9.47418 2.87579 9.18141 3.1035L3.52949 7.49944C3.15168 7.79329 2.96278 7.94022 2.82669 8.12422C2.70614 8.28721 2.61633 8.47082 2.56169 8.66605C2.5 8.88644 2.5 9.12575 2.5 9.60438V15.6335C2.5 16.5669 2.5 17.0336 2.68166 17.3901C2.84144 17.7037 3.09641 17.9587 3.41002 18.1185C3.76654 18.3002 4.23325 18.3002 5.16667 18.3002H6.83333C7.06669 18.3002 7.18337 18.3002 7.2725 18.2547C7.3509 18.2148 7.41464 18.1511 7.45459 18.0727C7.5 17.9835 7.5 17.8668 7.5 17.6335V12.1335C7.5 11.6668 7.5 11.4334 7.59083 11.2552C7.67072 11.0984 7.79821 10.9709 7.95501 10.891C8.13327 10.8002 8.36662 10.8002 8.83333 10.8002H11.1667C11.6334 10.8002 11.8667 10.8002 12.045 10.891C12.2018 10.9709 12.3293 11.0984 12.4092 11.2552C12.5 11.4334 12.5 11.6668 12.5 12.1335V17.6335C12.5 17.8668 12.5 17.9835 12.5454 18.0727C12.5854 18.1511 12.6491 18.2148 12.7275 18.2547C12.8166 18.3002 12.9333 18.3002 13.1667 18.3002H14.8333C15.7668 18.3002 16.2335 18.3002 16.59 18.1185C16.9036 17.9587 17.1586 17.7037 17.3183 17.3901C17.5 17.0336 17.5 16.5669 17.5 15.6335V9.60438C17.5 9.12575 17.5 8.88644 17.4383 8.66605C17.3837 8.47082 17.2939 8.28721 17.1733 8.12422C17.0372 7.94022 16.8483 7.79329 16.4705 7.49944L10.8186 3.1035Z" stroke="white" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                {{ __('Dashboard') }}
                                </a>
                        </li>
                        

                                {{-- Customers --}}
                                <li>
                                    <x-sidebar.nav-link :href="route('business.customers.index')" :active="request()->routeIs('business.customers.*')">
                                        <x-slot name="icon">
                                            <svg width="20" height="21" viewBox="0 0 20 21" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M7.50008 13.5173H6.25009C5.08711 13.5173 4.50563 13.5173 4.03246 13.6608C2.96713 13.984 2.13345 14.8176 1.81028 15.883C1.66675 16.3561 1.66675 16.9376 1.66675 18.1006M12.0834 6.85059C12.0834 8.92165 10.4045 10.6006 8.33342 10.6006C6.26235 10.6006 4.58341 8.92165 4.58341 6.85059C4.58341 4.77952 6.26235 3.10059 8.33342 3.10059C10.4045 3.10059 12.0834 4.77952 12.0834 6.85059ZM9.16675 18.1006L11.7512 17.3622C11.875 17.3268 11.9369 17.3091 11.9946 17.2826C12.0458 17.2591 12.0945 17.2304 12.14 17.1971C12.1912 17.1595 12.2367 17.114 12.3277 17.0229L17.7085 11.6423C18.2838 11.067 18.2838 10.1342 17.7084 9.5589C17.1331 8.98361 16.2004 8.98362 15.6251 9.55891L10.2444 14.9396C10.1534 15.0306 10.1079 15.0761 10.0703 15.1273C10.0369 15.1728 10.0082 15.2215 9.98471 15.2728C9.95821 15.3305 9.94053 15.3924 9.90516 15.5161L9.16675 18.1006Z"
                                                    stroke="white" stroke-width="1.66667" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                            </svg>

                                        </x-slot>
                                        {{ __('business.sidebar.customers') }}
                                    </x-sidebar.nav-link>
                                </li>

                                {{-- Work Orders dropdown menu - Mobile --}}
                                <li x-data="{ open: {{ request()->routeIs('business.work-orders*') || request()->routeIs('business.checklists*') || request()->routeIs('templates.*') ? 'true' : 'false' }} }">

                                    {{-- This button is only visible when the menu is CLOSED --}}
                                    <button x-show="!open" @click="open = true" type="button"
                                        class="group flex w-full items-center justify-between rounded-[8px] p-[12px] text-white hover:text-[#0D44EA]">
                                        <div class="flex items-center gap-x-[8px] ">
                                            <svg width="20" height="21" viewBox="0 0 20 21" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M16.6666 11.2168V6.4668C16.6666 5.06667 16.6666 4.3666 16.3942 3.83182C16.1545 3.36142 15.772 2.97896 15.3016 2.73928C14.7668 2.4668 14.0668 2.4668 12.6666 2.4668H7.33331C5.93318 2.4668 5.23312 2.4668 4.69834 2.73928C4.22793 2.97896 3.84548 3.36142 3.6058 3.83182C3.33331 4.3666 3.33331 5.06667 3.33331 6.4668V15.1335C3.33331 16.5336 3.33331 17.2337 3.6058 17.7684C3.84548 18.2388 4.22793 18.6213 4.69834 18.861C5.23312 19.1335 5.93318 19.1335 7.33331 19.1335H9.99998M11.6666 9.9668H6.66665M8.33331 13.3001H6.66665M13.3333 6.63346H6.66665M12.0833 16.6335L13.75 18.3001L17.5 14.5501"
                                                    stroke="white" stroke-width="1.66667" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                            </svg>

                                            <span class="text-[16px] font-[400]"> {{ __('Manage Work Orders') }} </span>
                                        </div>
                                        <svg class="h-5 w-5 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </button>

                                    {{-- This container appears as a single white box when the menu is OPEN --}}
                                    <div x-show="open" class="rounded-md bg-white" style="display: none;" x-transition>

                                        {{-- Header of the open menu (also the "close" button) --}}
                                        <button @click="open = false" type="button"
                                            class="flex w-full items-center  !px-[12px] !py-[12px] !gap-0">
                                            <div class="flex items-center gap-x-[8px] flex-1">
                                                <svg class="h-5 w-5 text-[#0D44EA]" width="20" height="21"
                                                    viewBox="0 0 20 21" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M16.6666 11.0176V6.26758C16.6666 4.86745 16.6666 4.16738 16.3941 3.6326C16.1544 3.1622 15.772 2.77975 15.3016 2.54006C14.7668 2.26758 14.0667 2.26758 12.6666 2.26758H7.33325C5.93312 2.26758 5.23305 2.26758 4.69828 2.54006C4.22787 2.77975 3.84542 3.1622 3.60574 3.6326C3.33325 4.16738 3.33325 4.86745 3.33325 6.26758V14.9342C3.33325 16.3344 3.33325 17.0344 3.60574 17.5692C3.84542 18.0396 4.22787 18.4221 4.69828 18.6618C5.23305 18.9342 5.93312 18.9342 7.33325 18.9342H9.99992M11.6666 9.76758H6.66659M8.33325 13.1009H6.66659M13.3333 6.43424H6.66659M12.0833 16.4342L13.7499 18.1009L17.4999 14.3509"
                                                        stroke="#0D44EA" stroke-width="1.66667" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                </svg>
                                                <span class="text-[16px] font-[400] text-[#0D44EA] whitespace-nowrap">
                                                    {{ __('business.sidebar.manage_work_orders') }} </span>
                                            </div>
                                            <svg class="h-5 w-5 shrink-0 rotate-90 ml-[8px]" viewBox="0 0 20 20"
                                                fill="#0D44EA">
                                                <path fill-rule="evenodd"
                                                    d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </button>

                                        {{-- List of Links --}}
                                        <ul class="mt-0 space-y-1 bg-[#F5F9FF] flex flex-col rounded-b-[8px] pb-[12px]">
                                            <li class="py-[12px] px-[10px] !mb-[0px]">
                                                <a href="{{ route('business.work-orders.index') }}"
                                                    class="inline-flex items-center font-[500] text-[16px] text-[#1C1D1D] hover:text-[#0D44EA] hover:!bg-[#F5F9FF] !py-[0px]
                                                    {{ request()->routeIs('business.work-orders.*') && !request()->routeIs('business.work-orders.maintenance.*') ? '!text-[#0D44EA] ' : '!text-[#1C1D1D]' }}">
                                                    <span
                                                        class="hover:text-[#0D44EA]">{{ __('business.sidebar.work_orders') }}</span>
                                                </a>
                                            </li>

                                            <li class="py-[12px] px-[10px] !mb-[0px]">
                                                <a href="{{ route('business.work-orders.maintenance.index') }}"
                                                    class="inline-flex items-center font-[500] text-[16px] text-[#1C1D1D] hover:text-[#0D44EA] hover:!bg-[#F5F9FF] !py-[0px]
                                                    {{ request()->routeIs('business.work-orders.maintenance.*') ? '!text-[#0D44EA] ' : '!text-[#1C1D1D]' }}">
                                                    <span
                                                        class="hover:text-[#0D44EA]">{{ __('business.sidebar.maintenance_orders') }}</span>
                                                </a>
                                            </li>

                                            {{-- Checklist --}}
                                            <li class="py-[12px] px-[10px] !mb-[0px]">
                                                <a href="{{ route('business.checklists.index') }}"
                                                    class="inline-flex items-center font-[500] text-[16px] text-[#1C1D1D] hover:text-[#0D44EA] hover:!bg-[#F5F9FF] !py-[0px]
                                                    {{ request()->routeIs('business.checklists.*') ? '!text-[#0D44EA] ' : '!text-[#1C1D1D]' }}">
                                                    <span
                                                        class="hover:text-[#0D44EA]">{{ __('business.sidebar.checklist') }}</span>
                                                </a>
                                            </li>

                                            {{-- Templates --}}
                                            <li class="py-[12px] px-[10px] !mb-[0px]">
                                                <a href="{{ route('templates.index') }}"
                                                    class="inline-flex items-center font-[500] text-[16px] text-[#1C1D1D] hover:text-[#0D44EA] hover:!bg-[#F5F9FF] !py-[0px]
                                                    {{ request()->routeIs('templates.*') ? '!text-[#0D44EA] ' : '!text-[#1C1D1D]' }}">
                                                    <span
                                                        class="hover:text-[#0D44EA]">{{ __('business.sidebar.templates') }}</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>

                                {{-- Manage Work Schedule --}}
                                <li>
                                    <x-sidebar.nav-link :href="route('business.scheduler.index')" :active="request()->routeIs('business.scheduler.index')">
                                        <x-slot name="icon">
                                            <x-icons.schedule class="w-6 h-6" />
                                        </x-slot>
                                        {{ __('business.sidebar.manage_work_schedule') }}
                                    </x-sidebar.nav-link>
                                </li>

                                {{-- Chemical List --}}
                                <li>
                                    <x-sidebar.nav-link :href="route('business.chemical-list')" :active="request()->routeIs('business.chemical-list')">
                                        <x-slot name="icon">
                                            <x-icons.chemical class="w-6 h-6" />
                                        </x-slot>
                                        {{ __('business.sidebar.chemical_list') }}
                                    </x-sidebar.nav-link>
                                </li>

                                {{-- Items Sold --}}
                                <li>
                                    <x-sidebar.nav-link :href="route('items-sold.index')" :active="request()->routeIs('items-sold.*')">
                                        <x-slot name="icon">
                                            <svg width="20" height="21" viewBox="0 0 20 21" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M13.3337 8.10091V5.60091C13.3337 3.75996 11.8413 2.26758 10.0004 2.26758C8.15944 2.26758 6.66706 3.75996 6.66706 5.60091V8.10091M2.99373 9.22755L2.49373 14.5609C2.35156 16.0773 2.28048 16.8355 2.5321 17.4211C2.75314 17.9356 3.14049 18.361 3.63207 18.6291C4.19166 18.9342 4.95319 18.9342 6.47626 18.9342H13.5245C15.0476 18.9342 15.8091 18.9342 16.3687 18.6291C16.8603 18.361 17.2476 17.9356 17.4687 17.4211C17.7203 16.8355 17.6492 16.0773 17.5071 14.5609L17.0071 9.22755C16.887 7.94703 16.827 7.30678 16.539 6.82271C16.2854 6.3964 15.9107 6.05517 15.4625 5.84245C14.9537 5.60091 14.3106 5.60091 13.0245 5.60091L6.97626 5.60091C5.69013 5.60091 5.04707 5.60091 4.53824 5.84245C4.09011 6.05517 3.7154 6.3964 3.46177 6.82271C3.1738 7.30677 3.11377 7.94703 2.99373 9.22755Z"
                                                    stroke="white" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                            </svg>

                                        </x-slot>
                                        {{ __('business.sidebar.items_sold') }}
                                    </x-sidebar.nav-link>
                                </li>

                                {{-- Reports --}}
                                <li>
                                    <x-sidebar.nav-link :href="route('reports.index')" :active="request()->routeIs('reports.*')">
                                        <x-slot name="icon">
                                            <svg width="20" height="21" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M11.6668 2.69128V6.1334C11.6668 6.60011 11.6668 6.83347 11.7577 7.01173C11.8376 7.16853 11.965 7.29601 12.1218 7.37591C12.3001 7.46673 12.5335 7.46673 13.0002 7.46673H16.4423M13.3335 11.6333H6.66683M13.3335 14.9667H6.66683M8.3335 8.30001H6.66683M11.6668 2.46667H7.3335C5.93337 2.46667 5.2333 2.46667 4.69852 2.73916C4.22811 2.97884 3.84566 3.36129 3.60598 3.8317C3.3335 4.36648 3.3335 5.06654 3.3335 6.46668V15.1333C3.3335 16.5335 3.3335 17.2335 3.60598 17.7683C3.84566 18.2387 4.22811 18.6212 4.69852 18.8609C5.2333 19.1333 5.93337 19.1333 7.3335 19.1333H12.6668C14.067 19.1333 14.767 19.1333 15.3018 18.8609C15.7722 18.6212 16.1547 18.2387 16.3943 17.7683C16.6668 17.2335 16.6668 16.5335 16.6668 15.1333V7.46668L11.6668 2.46667Z" stroke="white" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </x-slot>
                                        {{ __('business.sidebar.reports') }}
                                    </x-sidebar.nav-link>
                                </li>

                                {{-- Account --}}
                                <li x-data="{ open: {{ request()->routeIs('account*') || request()->routeIs('business.sub-admins*') || request()->routeIs('business.technicians.*') ? 'true' : 'false' }} }">
                                    <!-- This button is only visible when the menu is CLOSED -->
                                    <button x-show="!open" @click="open = true" type="button"
                                        class="group flex w-full items-center justify-between rounded-[8px] p-[12px] text-white hover:text-[#0D44EA]">
                                        <div class="flex items-center gap-x-[8px] ">
                                            <svg width="20" height="21" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M4.43033 16.9987C4.93727 15.8043 6.12085 14.9667 7.50008 14.9667H12.5001C13.8793 14.9667 15.0629 15.8043 15.5698 16.9987M13.3334 8.71668C13.3334 10.5576 11.841 12.05 10.0001 12.05C8.15913 12.05 6.66675 10.5576 6.66675 8.71668C6.66675 6.87573 8.15913 5.38334 10.0001 5.38334C11.841 5.38334 13.3334 6.87573 13.3334 8.71668ZM18.3334 10.8C18.3334 15.4024 14.6025 19.1333 10.0001 19.1333C5.39771 19.1333 1.66675 15.4024 1.66675 10.8C1.66675 6.19764 5.39771 2.46667 10.0001 2.46667C14.6025 2.46667 18.3334 6.19764 18.3334 10.8Z" stroke="currentColor" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <span class="text-[16px] font-[400]"> {{ __('business.sidebar.account') }} </span>
                                        </div>
                                        <svg class="h-5 w-5 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </button>

                                    <!-- This container appears as a single white box when the menu is OPEN -->
                                    <div x-show="open" class="rounded-md bg-white" style="display: none;" x-transition>

                                        <!-- Header of the open menu (also the "close" button) -->
                                        <button @click="open = false" type="button"
                                            class="flex w-full items-center  !px-[12px] !py-[12px] !gap-0">
                                            <div class="flex items-center gap-x-[8px] flex-1">
                                                <svg class="h-5 w-5 text-[#0D44EA]" width="20" height="21"
                                                    viewBox="0 0 20 21" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M4.43033 16.9987C4.93727 15.8043 6.12085 14.9667 7.50008 14.9667H12.5001C13.8793 14.9667 15.0629 15.8043 15.5698 16.9987M13.3334 8.71668C13.3334 10.5576 11.841 12.05 10.0001 12.05C8.15913 12.05 6.66675 10.5576 6.66675 8.71668C6.66675 6.87573 8.15913 5.38334 10.0001 5.38334C11.841 5.38334 13.3334 6.87573 13.3334 8.71668ZM18.3334 10.8C18.3334 15.4024 14.6025 19.1333 10.0001 19.1333C5.39771 19.1333 1.66675 15.4024 1.66675 10.8C1.66675 6.19764 5.39771 2.46667 10.0001 2.46667C14.6025 2.46667 18.3334 6.19764 18.3334 10.8Z" stroke="currentColor" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                                <span class="text-[16px] font-[400] text-[#0D44EA] whitespace-nowrap">
                                                    {{ __('business.sidebar.account') }} </span>
                                            </div>
                                            <svg class="h-5 w-5 shrink-0 rotate-90 ml-[8px]" viewBox="0 0 20 20"
                                                fill="#0D44EA">
                                                <path fill-rule="evenodd"
                                                    d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </button>

                                        <!-- List of Links -->
                                        <ul class="mt-0 space-y-1 bg-[#F5F9FF] flex flex-col rounded-b-[8px] pb-[12px]">
                                            <li class="py-[12px] px-[10px] !mb-[0px]">
                                                <a href="{{ route('account.profile') }}"
                                                    class="inline-flex items-center font-[500] text-[16px] text-[#1C1D1D] hover:text-[#0D44EA] hover:!bg-[#F5F9FF] !py-[0px]
                                                    {{ request()->routeIs('account*') ? '!text-[#0D44EA] ' : '!text-[#1C1D1D]' }}">
                                                    <span
                                                        class="hover:text-[#0D44EA]">{{ __('business.sidebar.my_profile') }}</span>
                                                </a>
                                            </li>

                                            <li class="py-[12px] px-[10px] !mb-[0px]">
                                                <a href="{{ route('business.sub-admins.index') }}"
                                                    class="inline-flex items-center font-[500] text-[16px] text-[#1C1D1D] hover:text-[#0D44EA] hover:!bg-[#F5F9FF] !py-[0px]
                                                    {{ request()->routeIs('business.sub-admins*') ? '!text-[#0D44EA] ' : '!text-[#1C1D1D]' }}">
                                                    <span
                                                        class="hover:text-[#0D44EA]">{{ __('business.sidebar.sub_admins') }}</span>
                                                </a>
                                            </li>

                                            <li class="py-[12px] px-[10px] !mb-[0px]">
                                                <a href="{{ route('business.technicians.index') }}"
                                                    class="inline-flex items-center font-[500] text-[16px] text-[#1C1D1D] hover:text-[#0D44EA] hover:!bg-[#F5F9FF] !py-[0px]
                                                    {{ request()->routeIs('business.technicians.*') ? '!text-[#0D44EA] ' : '!text-[#1C1D1D]' }}">
                                                    <span
                                                        class="hover:text-[#0D44EA]">{{ __('business.sidebar.technicians') }}</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>

                                {{-- Help --}}
                                <li>
                                    <x-sidebar.nav-link :href="route('help')" :active="request()->routeIs('help')">
                                        <x-slot name="icon">
                                            <x-icons.help class="w-6 h-6" />
                                        </x-slot>
                                        {{ __('business.sidebar.help') }}
                                    </x-sidebar.nav-link>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- Static sidebar for desktop -->
<div class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-[288px] lg:flex-col">
    <!-- Sidebar component, swap this element with another sidebar if you like -->
    <div class="aside-bar">
        <div class="aside-bar-overlay">
            <div class="flex h-16 shrink-0 items-center">
                <img class="w-[224px] h-[57px]" src="{{ asset('images/dashboard-logo.svg') }}" alt="Pool Route">
            </div>
            <nav class="flex flex-1 flex-col mt-[51px]" aria-label="menu">
                <ul role="list" class="flex flex-1 flex-col gap-y-7">
                    <li>
                        <ul role="list" class="sidebar-list">
                           
                            <li>
                            <x-sidebar.nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                            <x-slot name="icon">
                            <svg width="20" height="21" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10.8186 3.1035C10.5258 2.87579 10.3794 2.76194 10.2178 2.71817C10.0752 2.67955 9.92484 2.67955 9.78221 2.71817C9.62057 2.76194 9.47418 2.87579 9.18141 3.1035L3.52949 7.49944C3.15168 7.79329 2.96278 7.94022 2.82669 8.12422C2.70614 8.28721 2.61633 8.47082 2.56169 8.66605C2.5 8.88644 2.5 9.12575 2.5 9.60438V15.6335C2.5 16.5669 2.5 17.0336 2.68166 17.3901C2.84144 17.7037 3.09641 17.9587 3.41002 18.1185C3.76654 18.3002 4.23325 18.3002 5.16667 18.3002H6.83333C7.06669 18.3002 7.18337 18.3002 7.2725 18.2547C7.3509 18.2148 7.41464 18.1511 7.45459 18.0727C7.5 17.9835 7.5 17.8668 7.5 17.6335V12.1335C7.5 11.6668 7.5 11.4334 7.59083 11.2552C7.67072 11.0984 7.79821 10.9709 7.95501 10.891C8.13327 10.8002 8.36662 10.8002 8.83333 10.8002H11.1667C11.6334 10.8002 11.8667 10.8002 12.045 10.891C12.2018 10.9709 12.3293 11.0984 12.4092 11.2552C12.5 11.4334 12.5 11.6668 12.5 12.1335V17.6335C12.5 17.8668 12.5 17.9835 12.5454 18.0727C12.5854 18.1511 12.6491 18.2148 12.7275 18.2547C12.8166 18.3002 12.9333 18.3002 13.1667 18.3002H14.8333C15.7668 18.3002 16.2335 18.3002 16.59 18.1185C16.9036 17.9587 17.1586 17.7037 17.3183 17.3901C17.5 17.0336 17.5 16.5669 17.5 15.6335V9.60438C17.5 9.12575 17.5 8.88644 17.4383 8.66605C17.3837 8.47082 17.2939 8.28721 17.1733 8.12422C17.0372 7.94022 16.8483 7.79329 16.4705 7.49944L10.8186 3.1035Z"
                      stroke="white" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            </x-slot>
                        {{ __('Dashboard') }}
                            </x-sidebar.nav-link>
                            </li>
                                               
                            {{-- Customers --}}
                            <li>
                                <x-sidebar.nav-link :href="route('business.customers.index')" :active="request()->routeIs('business.customers.*')">
                                    <x-slot name="icon">
                                        <svg width="20" height="21" viewBox="0 0 20 21" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M7.50008 13.5173H6.25009C5.08711 13.5173 4.50563 13.5173 4.03246 13.6608C2.96713 13.984 2.13345 14.8176 1.81028 15.883C1.66675 16.3561 1.66675 16.9376 1.66675 18.1006M12.0834 6.85059C12.0834 8.92165 10.4045 10.6006 8.33342 10.6006C6.26235 10.6006 4.58341 8.92165 4.58341 6.85059C4.58341 4.77952 6.26235 3.10059 8.33342 3.10059C10.4045 3.10059 12.0834 4.77952 12.0834 6.85059ZM9.16675 18.1006L11.7512 17.3622C11.875 17.3268 11.9369 17.3091 11.9946 17.2826C12.0458 17.2591 12.0945 17.2304 12.14 17.1971C12.1912 17.1595 12.2367 17.114 12.3277 17.0229L17.7085 11.6423C18.2838 11.067 18.2838 10.1342 17.7084 9.5589C17.1331 8.98361 16.2004 8.98362 15.6251 9.55891L10.2444 14.9396C10.1534 15.0306 10.1079 15.0761 10.0703 15.1273C10.0369 15.1728 10.0082 15.2215 9.98471 15.2728C9.95821 15.3305 9.94053 15.3924 9.90516 15.5161L9.16675 18.1006Z"
                                                stroke="white" stroke-width="1.66667" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </svg>

                                    </x-slot>
                                    {{ __('business.sidebar.customers') }}
                                </x-sidebar.nav-link>
                            </li>

                            {{-- Work Orders dropdown menu - Desktop --}}
                            <li x-data="{ open: {{ request()->routeIs('business.work-orders*') || request()->routeIs('business.checklists*') || request()->routeIs('templates.*') ? 'true' : 'false' }} }">

                                <!-- This button is only visible when the menu is CLOSED -->
                                <button x-show="!open" @click="open = true" type="button"
                                    class="group flex w-full items-center justify-between rounded-[8px] p-[12px] text-white hover:text-[#0D44EA]">
                                    <div class="flex items-center gap-x-[8px]">

                                        <svg width="20" height="21" viewBox="0 0 20 21" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M16.6666 11.2168V6.4668C16.6666 5.06667 16.6666 4.3666 16.3942 3.83182C16.1545 3.36142 15.772 2.97896 15.3016 2.73928C14.7668 2.4668 14.0668 2.4668 12.6666 2.4668H7.33331C5.93318 2.4668 5.23312 2.4668 4.69834 2.73928C4.22793 2.97896 3.84548 3.36142 3.6058 3.83182C3.33331 4.3666 3.33331 5.06667 3.33331 6.4668V15.1335C3.33331 16.5336 3.33331 17.2337 3.6058 17.7684C3.84548 18.2388 4.22793 18.6213 4.69834 18.861C5.23312 19.1335 5.93318 19.1335 7.33331 19.1335H9.99998M11.6666 9.9668H6.66665M8.33331 13.3001H6.66665M13.3333 6.63346H6.66665M12.0833 16.6335L13.75 18.3001L17.5 14.5501"
                                                stroke="white" stroke-width="1.66667" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </svg>


                                        <span class="text-[16px] font-[400]"> {{ __('Manage Work Orders') }} </span>
                                    </div>
                                    <svg class="h-5 w-5 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>

                                <!-- This container appears as a single white box when the menu is OPEN -->
                                <div x-show="open" class="rounded-md bg-white" style="display: none;" x-transition>

                                    <!-- Header of the open menu (also the "close" button) -->
                                    <button @click="open = false" type="button"
                                        class="flex w-full items-center  !px-[12px] !py-[12px] !gap-0">
                                        <div class="flex items-center gap-x-[8px]">

                                            <svg class="h-5 w-5 text-[#0D44EA]" width="20" height="21"
                                                viewBox="0 0 20 21" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M16.6666 11.0176V6.26758C16.6666 4.86745 16.6666 4.16738 16.3941 3.6326C16.1544 3.1622 15.772 2.77975 15.3016 2.54006C14.7668 2.26758 14.0667 2.26758 12.6666 2.26758H7.33325C5.93312 2.26758 5.23305 2.26758 4.69828 2.54006C4.22787 2.77975 3.84542 3.1622 3.60574 3.6326C3.33325 4.16738 3.33325 4.86745 3.33325 6.26758V14.9342C3.33325 16.3344 3.33325 17.0344 3.60574 17.5692C3.84542 18.0396 4.22787 18.4221 4.69828 18.6618C5.23305 18.9342 5.93312 18.9342 7.33325 18.9342H9.99992M11.6666 9.76758H6.66659M8.33325 13.1009H6.66659M13.3333 6.43424H6.66659M12.0833 16.4342L13.7499 18.1009L17.4999 14.3509"
                                                    stroke="#0D44EA" stroke-width="1.66667" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                            </svg>

                                            <span class="text-[16px] font-[400] text-[#0D44EA] whitespace-nowrap">
                                                {{ __('business.sidebar.manage_work_orders') }} </span>
                                        </div>
                                        <svg class="h-5 w-5 shrink-0 rotate-90 ml-[8px]" viewBox="0 0 20 20"
                                            fill="#0D44EA">
                                            <path fill-rule="evenodd"
                                                d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </button>

                                    <!-- List of Links -->
                                    <ul class="mt-0 space-y-1 bg-[#F5F9FF] flex flex-col rounded-b-[8px] pb-[12px]">
                                        <li class="py-[12px] px-[10px] !mb-[0px]">
                                            <a href="{{ route('business.work-orders.index') }}"
                                                class="inline-flex items-center font-[500] text-[16px] text-[#1C1D1D] hover:text-[#0D44EA] hover:!bg-[#F5F9FF] !py-[0px]
                                                    {{ request()->routeIs('business.work-orders.*') && !request()->routeIs('business.work-orders.maintenance.*') ? '!text-[#0D44EA] ' : '!text-[#1C1D1D]' }}">
                                                <span
                                                    class="hover:text-[#0D44EA]">{{ __('business.sidebar.work_orders') }}</span>
                                            </a>
                                        </li>

                                        <!-- Maintenance Work Orders -->
                                        <li class="py-[12px] px-[10px] !mb-[0px]">
                                            <a href="{{ route('business.work-orders.maintenance.index') }}"
                                                class="inline-flex items-center font-[500] text-[16px] text-[#1C1D1D] hover:text-[#0D44EA] hover:!bg-[#F5F9FF] !py-[0px]
                                                {{ request()->routeIs('business.work-orders.maintenance.*') ? '!text-[#0D44EA] ' : '!text-[#1C1D1D]' }}">
                                                <span
                                                    class="hover:text-[#0D44EA]">{{ __('business.sidebar.maintenance_orders') }}</span>
                                            </a>
                                        </li>

                                        <li class="py-[12px] px-[10px] !mb-[0px]">
                                            <a href="{{ route('business.checklists.index') }}"
                                                class="inline-flex items-center font-[500] text-[16px] text-[#1C1D1D] hover:text-[#0D44EA] hover:!bg-[#F5F9FF] !py-[0px]
                                                {{ request()->routeIs('business.checklists.*') ? '!text-[#0D44EA] ' : '!text-[#1C1D1D]' }}">
                                                <span
                                                    class="hover:text-[#0D44EA]">{{ __('business.sidebar.checklist') }}</span>
                                            </a>
                                        </li>

                                        <!-- Templates -->
                                        <li class="py-[12px] px-[10px] !mb-[0px]">
                                            <a href="{{ route('templates.index') }}"
                                                class="inline-flex items-center font-[500] text-[16px] text-[#1C1D1D] hover:text-[#0D44EA] hover:!bg-[#F5F9FF] !py-[0px]
                                                {{ request()->routeIs('templates.*') ? '!text-[#0D44EA] ' : '!text-[#1C1D1D]' }}">
                                                <span
                                                    class="hover:text-[#0D44EA]">{{ __('business.sidebar.templates') }}</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>

                            {{-- Manage Work Schedule --}}
                            <li>
                                <x-sidebar.nav-link :href="route('business.scheduler.index')" :active="request()->routeIs('business.scheduler.index')">
                                    <x-slot name="icon">
                                        <x-icons.schedule class="w-6 h-6" />
                                    </x-slot>
                                    {{ __('business.sidebar.manage_work_schedule') }}
                                </x-sidebar.nav-link>
                            </li>

                            {{-- Chemical List --}}
                            <li>
                                <x-sidebar.nav-link :href="route('business.chemical-list')" :active="request()->routeIs('business.chemical-list')">
                                    <x-slot name="icon">
                                        <x-icons.chemical class="w-6 h-6" />
                                    </x-slot>
                                    {{ __('business.sidebar.chemical_list') }}
                                </x-sidebar.nav-link>
                            </li>

                            {{-- Items Sold --}}
                            <li>
                                <x-sidebar.nav-link :href="route('items-sold.index')" :active="request()->routeIs('items-sold.*')">
                                    <x-slot name="icon">
                                        <svg width="20" height="21" viewBox="0 0 20 21" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M13.3337 8.10091V5.60091C13.3337 3.75996 11.8413 2.26758 10.0004 2.26758C8.15944 2.26758 6.66706 3.75996 6.66706 5.60091V8.10091M2.99373 9.22755L2.49373 14.5609C2.35156 16.0773 2.28048 16.8355 2.5321 17.4211C2.75314 17.9356 3.14049 18.361 3.63207 18.6291C4.19166 18.9342 4.95319 18.9342 6.47626 18.9342H13.5245C15.0476 18.9342 15.8091 18.9342 16.3687 18.6291C16.8603 18.361 17.2476 17.9356 17.4687 17.4211C17.7203 16.8355 17.6492 16.0773 17.5071 14.5609L17.0071 9.22755C16.887 7.94703 16.827 7.30678 16.539 6.82271C16.2854 6.3964 15.9107 6.05517 15.4625 5.84245C14.9537 5.60091 14.3106 5.60091 13.0245 5.60091L6.97626 5.60091C5.69013 5.60091 5.04707 5.60091 4.53824 5.84245C4.09011 6.05517 3.7154 6.3964 3.46177 6.82271C3.1738 7.30677 3.11377 7.94703 2.99373 9.22755Z"
                                                stroke="white" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </svg>

                                    </x-slot>
                                    {{ __('business.sidebar.items_sold') }}
                                </x-sidebar.nav-link>
                            </li>

                            {{-- Reports --}}
                            <li>
                                <x-sidebar.nav-link :href="route('reports.index')" :active="request()->routeIs('reports.*')">
                                    <x-slot name="icon">
                                        <svg width="20" height="21" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M11.6668 2.69128V6.1334C11.6668 6.60011 11.6668 6.83347 11.7577 7.01173C11.8376 7.16853 11.965 7.29601 12.1218 7.37591C12.3001 7.46673 12.5335 7.46673 13.0002 7.46673H16.4423M13.3335 11.6333H6.66683M13.3335 14.9667H6.66683M8.3335 8.30001H6.66683M11.6668 2.46667H7.3335C5.93337 2.46667 5.2333 2.46667 4.69852 2.73916C4.22811 2.97884 3.84566 3.36129 3.60598 3.8317C3.3335 4.36648 3.3335 5.06654 3.3335 6.46668V15.1333C3.3335 16.5335 3.3335 17.2335 3.60598 17.7683C3.84566 18.2387 4.22811 18.6212 4.69852 18.8609C5.2333 19.1333 5.93337 19.1333 7.3335 19.1333H12.6668C14.067 19.1333 14.767 19.1333 15.3018 18.8609C15.7722 18.6212 16.1547 18.2387 16.3943 17.7683C16.6668 17.2335 16.6668 16.5335 16.6668 15.1333V7.46668L11.6668 2.46667Z" stroke="white" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </x-slot>
                                    {{ __('business.sidebar.reports') }}
                                </x-sidebar.nav-link>
                            </li>

                            {{-- Account --}}
                            <li x-data="{ open: {{ request()->routeIs('account*') || request()->routeIs('business.sub-admins*') || request()->routeIs('business.technicians.*') ? 'true' : 'false' }} }">
                                <!-- This button is only visible when the menu is CLOSED -->
                                <button x-show="!open" @click="open = true" type="button"
                                    class="group flex w-full items-center justify-between rounded-[8px] p-[12px] text-white hover:text-[#0D44EA]">
                                    <div class="flex items-center gap-x-[8px] ">
                                        <svg width="20" height="21" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M4.43033 16.9987C4.93727 15.8043 6.12085 14.9667 7.50008 14.9667H12.5001C13.8793 14.9667 15.0629 15.8043 15.5698 16.9987M13.3334 8.71668C13.3334 10.5576 11.841 12.05 10.0001 12.05C8.15913 12.05 6.66675 10.5576 6.66675 8.71668C6.66675 6.87573 8.15913 5.38334 10.0001 5.38334C11.841 5.38334 13.3334 6.87573 13.3334 8.71668ZM18.3334 10.8C18.3334 15.4024 14.6025 19.1333 10.0001 19.1333C5.39771 19.1333 1.66675 15.4024 1.66675 10.8C1.66675 6.19764 5.39771 2.46667 10.0001 2.46667C14.6025 2.46667 18.3334 6.19764 18.3334 10.8Z" stroke="currentColor" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <span class="text-[16px] font-[400]"> {{ __('business.sidebar.account') }} </span>
                                    </div>
                                    <svg class="h-5 w-5 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>

                                <!-- This container appears as a single white box when the menu is OPEN -->
                                <div x-show="open" class="rounded-md bg-white" style="display: none;" x-transition>

                                    <!-- Header of the open menu (also the "close" button) -->
                                    <button @click="open = false" type="button"
                                        class="flex w-full items-center  !px-[12px] !py-[12px] !gap-0">
                                        <div class="flex items-center gap-x-[8px] flex-1">
                                            <svg class="h-5 w-5 text-[#0D44EA]" width="20" height="21"
                                                viewBox="0 0 20 21" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path d="M4.43033 16.9987C4.93727 15.8043 6.12085 14.9667 7.50008 14.9667H12.5001C13.8793 14.9667 15.0629 15.8043 15.5698 16.9987M13.3334 8.71668C13.3334 10.5576 11.841 12.05 10.0001 12.05C8.15913 12.05 6.66675 10.5576 6.66675 8.71668C6.66675 6.87573 8.15913 5.38334 10.0001 5.38334C11.841 5.38334 13.3334 6.87573 13.3334 8.71668ZM18.3334 10.8C18.3334 15.4024 14.6025 19.1333 10.0001 19.1333C5.39771 19.1333 1.66675 15.4024 1.66675 10.8C1.66675 6.19764 5.39771 2.46667 10.0001 2.46667C14.6025 2.46667 18.3334 6.19764 18.3334 10.8Z" stroke="currentColor" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <span class="text-[16px] font-[400] text-[#0D44EA] whitespace-nowrap">
                                                {{ __('business.sidebar.account') }} </span>
                                        </div>
                                        <svg class="h-5 w-5 shrink-0 rotate-90 ml-[8px]" viewBox="0 0 20 20"
                                            fill="#0D44EA">
                                            <path fill-rule="evenodd"
                                                d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </button>

                                    {{-- List of Links --}}
                                    <ul class="mt-0 space-y-1 bg-[#F5F9FF] flex flex-col rounded-b-[8px] pb-[12px]">
                                        <li class="py-[12px] px-[10px] !mb-[0px]">
                                            <a href="{{ route('account.profile') }}"
                                                class="inline-flex items-center font-[500] text-[16px] text-[#1C1D1D] hover:text-[#0D44EA] hover:!bg-[#F5F9FF] !py-[0px]
                                                {{ request()->routeIs('account*') ? '!text-[#0D44EA] ' : '!text-[#1C1D1D]' }}">
                                                <span
                                                    class="hover:text-[#0D44EA]">{{ __('business.sidebar.my_profile') }}</span>
                                            </a>
                                        </li>

                                        <li class="py-[12px] px-[10px] !mb-[0px]">
                                            <a href="{{ route('business.sub-admins.index') }}"
                                                class="inline-flex items-center font-[500] text-[16px] text-[#1C1D1D] hover:text-[#0D44EA] hover:!bg-[#F5F9FF] !py-[0px]
                                                {{ request()->routeIs('business.sub-admins*') ? '!text-[#0D44EA] ' : '!text-[#1C1D1D]' }}">
                                                <span
                                                    class="hover:text-[#0D44EA]">{{ __('business.sidebar.sub_admins') }}</span>
                                            </a>
                                        </li>

                                        <li class="py-[12px] px-[10px] !mb-[0px]">
                                            <a href="{{ route('business.technicians.index') }}"
                                                class="inline-flex items-center font-[500] text-[16px] text-[#1C1D1D] hover:text-[#0D44EA] hover:!bg-[#F5F9FF] !py-[0px]
                                                {{ request()->routeIs('business.technicians.*') ? '!text-[#0D44EA] ' : '!text-[#1C1D1D]' }}">
                                                <span
                                                    class="hover:text-[#0D44EA]">{{ __('business.sidebar.technicians') }}</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>

                            {{-- Help --}}
                            <li>
                                <x-sidebar.nav-link :href="route('help')" :active="request()->routeIs('help')">
                                    <x-slot name="icon">
                                        <x-icons.help class="w-6 h-6" />
                                    </x-slot>
                                    {{ __('business.sidebar.help') }}
                                </x-sidebar.nav-link>
                            </li>
                        </ul>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>

@section('scripts')
    <script>
        function setupAsideMenuToggle() {
            const hamburgerIcon = document.querySelector(".hamburger-icon");
            const mobileMenu = document.querySelector(".mobile-menu");
            const mobileMenuOverlay = document.querySelector(".mobile-menu-overlay");

            if (!hamburgerIcon || !mobileMenu || !mobileMenuOverlay) return;

            function toggleMenu() {
                if (window.innerWidth <= 1023) {
                    mobileMenu.classList.toggle("translate-x-0");
                    mobileMenuOverlay.classList.toggle("hidden");
                }
            }

            function hideMenu() {
                if (window.innerWidth <= 1023) {
                    mobileMenu.classList.remove("translate-x-0");
                    mobileMenuOverlay.classList.add("hidden");
                }
            }

            hamburgerIcon.addEventListener("click", toggleMenu);
            mobileMenuOverlay.addEventListener("click", hideMenu);
        }

        document.addEventListener("DOMContentLoaded", function() {
            setupAsideMenuToggle();
        });
    </script>
@endsection
