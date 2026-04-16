<!-- Desktop sidebar partial will go here -->   <!-- Off-canvas menu for mobile, show/hide based on off-canvas menu state. -->
<div class="relative z-50 lg:hidden" role="dialog" aria-modal="true">
    <!--
      Off-canvas menu backdrop, show/hide based on off-canvas menu state.

      Entering: "transition-opacity ease-linear duration-300"
        From: "opacity-0"
        To: "opacity-100"
      Leaving: "transition-opacity ease-linear duration-300"
        From: "opacity-100"
        To: "opacity-0"
    -->
    <div class="fixed inset-0 bg-gray-900/80" aria-hidden="true"></div>

    <div class="fixed inset-0 flex">
      <div class="relative mr-16 flex w-full max-w-xs flex-1">
        <div class="absolute top-0 left-full flex w-16 justify-center pt-5">
          <button type="button" class="-m-2.5 p-2.5">
            <span class="sr-only">Close sidebar</span>
            <svg class="size-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Sidebar component, swap this element with another sidebar if you like -->
        <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-gray-900 px-6 pb-4 ring-1 ring-white/10">
          <div class="flex h-16 shrink-0 items-center">
            <img class="h-8 w-auto" src="{{ asset('images/poolroute-logo.png') }}" alt="{{ config('app.name') }}">
          </div>
          <nav class="flex flex-1 flex-col" aria-label="Sidebar menu">
            <ul role="list" class="flex flex-1 flex-col gap-y-7">
              <li>
                <ul role="list" class="-mx-2 space-y-1">
                  <li>
                    <!-- Current: "bg-gray-800 text-white", Default: "text-gray-400 hover:text-white hover:bg-gray-800" -->
                    <a href="{{ route('admin.dashboard') }}" class="group flex gap-x-3 rounded-md {{ request()->routeIs('admin.dashboard') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} p-2 text-sm/6 font-semibold">
                      <svg class="size-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                      </svg>
                      {{ __('admin.navigation.dashboard') }}
                    </a>
                  </li>
                  <li>
                    <a href="{{ route('admin.business.index') }}" class="group flex gap-x-3 rounded-md {{ request()->routeIs('admin.business*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} p-2 text-sm/6 font-semibold">
                      <svg class="size-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                      </svg>
                      {{ __('admin.navigation.manage_business') }}
                    </a>
                  </li>
                  <li>
                    <a href="{{ route('admin.earnings.index') }}" class="group flex gap-x-3 rounded-md {{ request()->routeIs('admin.earnings*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} p-2 text-sm/6 font-semibold">
                      <svg class="size-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z" />
                      </svg>
                      {{ __('admin.navigation.earnings') }}
                    </a>
                  </li>
                  <li>
                    <a href="{{ route('admin.content') }}" class="group flex gap-x-3 rounded-md {{ request()->routeIs('admin.content*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} p-2 text-sm/6 font-semibold">
                      <svg class="size-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                      </svg>
                      {{ __('admin.navigation.training_video') }}
                    </a>
                  </li>
                  <li>
                    <a href="{{ route('admin.account') }}" class="group flex gap-x-3 rounded-md {{ request()->routeIs('admin.account*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} p-2 text-sm/6 font-semibold">
                      <svg class="size-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H9.75" />
                      </svg>
                      {{ __('admin.navigation.manage_admins') }}
                    </a>
                  </li>
                  <li>
                    <a href="{{ route('admin.mass-message') }}" class="group flex gap-x-3 rounded-md {{ request()->routeIs('admin.account*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} p-2 text-sm/6 font-semibold">
                      <svg class="size-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H9.75" />
                      </svg>
                      {{ __('admin.navigation.mass_message') }}
                    </a>
                  </li>
                  <!-- Settings Menu Item with Theme Toggle -->
                  <li>
                    <a href="{{ route('admin.setting') }}" class="group flex gap-x-3 rounded-md {{ request()->routeIs('admin.setting') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} p-2 text-sm/6 font-semibold">
                      <svg class="size-6 shrink-0" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9.72656 0.666504H6.27344L5.90625 2.2793C5.42969 2.45964 5.0013 2.70964 4.64062 3.01318L3.04688 2.38623L1.38672 4.04639L2.01367 5.64014C1.7103 6.00082 1.46094 6.4292 1.28027 6.90601L0.666016 7.27319V9.72632L2.2793 10.0935C2.45964 10.5701 2.70964 10.9985 3.01318 11.3592L2.38623 12.953L4.04639 14.6131L5.64014 13.9862C6.00082 14.2896 6.4292 14.539 6.90601 14.7197L7.27319 15.3335H9.72656L10.0938 13.7207C10.5703 13.5404 10.9987 13.2904 11.3594 12.9868L12.9531 13.6138L14.6133 11.9536L13.9863 10.3599C14.2897 9.99921 14.5391 9.57083 14.7197 9.09402L15.333 8.72685V6.27372L13.7207 5.90654C13.5404 5.42994 13.2904 5.00156 12.9868 4.64087L13.6138 3.04712L11.9536 1.38696L10.3599 2.01391C9.99921 1.71055 9.57083 1.46119 9.09402 1.28052L8.72685 0.666504H9.72656ZM8.00004 10.6665C6.53337 10.6665 5.33337 9.4665 5.33337 7.99984C5.33337 6.53317 6.53337 5.33317 8.00004 5.33317C9.46671 5.33317 10.6667 6.53317 10.6667 7.99984C10.6667 9.4665 9.46671 10.6665 8.00004 10.6665Z" fill="white"/>
                      </svg>
                      {{ __('admin.navigation.setting') }}
                    </a>
                  </li>
                    
                    <!-- Theme Toggle - Mobile -->
            
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
  <div class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-72 lg:flex-col myred">
    <!-- Sidebar component, swap this element with another sidebar if you like -->
    <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-gray-900 px-6 pb-4">
      <div class="flex h-16 shrink-0 items-center">
        <img class="w-50 mt-6" src="{{ asset('images/poolroute-logo-white.png') }}" alt="{{ config('app.name') }}">
      </div>
      <nav class="flex flex-1 flex-col" aria-label="menu">
        <ul role="list" class="flex flex-1 flex-col gap-y-7">
          <li>
            <ul role="list" class="-mx-2 space-y-1">
              <li>
                <a href="{{ route('admin.dashboard') }}" class="group flex gap-x-3 rounded-md {{ request()->routeIs('admin.dashboard') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} p-2 text-sm/6 font-normal">
                 <svg class="size-6 shrink-0" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M8.20817 3.99935V0.416016H13.5832V3.99935H8.20817ZM0.416504 7.58268V0.416016H5.7915V7.58268H0.416504ZM8.20817 13.5827V6.41602H13.5832V13.5827H8.20817ZM0.416504 13.5827V9.99935H5.7915V13.5827H0.416504Z" fill="white"/>
                </svg>

                  {{ __('admin.navigation.dashboard') }}
                </a>
              </li>
              <li class="mt-4">
                <a href="{{ route('admin.business.index') }}" class="group flex gap-x-3 rounded-md {{ request()->routeIs('admin.business*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} p-2 text-sm/6 font-normal">
                <svg class="size-6 shrink-0" width="16" height="12" viewBox="0 0 16 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M0.665039 11.4228V9.98672C0.665039 9.66839 0.74115 9.38929 0.893372 9.14943C1.04559 8.90957 1.2583 8.70984 1.5315 8.55026C2.21233 8.14901 2.92879 7.83297 3.68087 7.60214C4.43309 7.37144 5.28886 7.25609 6.24816 7.25609C7.20761 7.25609 8.06337 7.37144 8.81546 7.60214C9.56768 7.83297 10.2842 8.14901 10.965 8.55026C11.2382 8.70984 11.4509 8.90957 11.6032 9.14943C11.7554 9.38929 11.8315 9.66839 11.8315 9.98672V11.4228H0.665039ZM13.3315 11.4228V10.0509C13.3315 9.53172 13.2323 9.05554 13.034 8.62234C12.8355 8.18929 12.5736 7.837 12.2482 7.56547C12.6402 7.67658 13.0284 7.81248 13.4125 7.97318C13.7966 8.13401 14.175 8.32658 14.548 8.55089C14.7841 8.68339 14.9742 8.88471 15.1184 9.15484C15.2625 9.42484 15.3346 9.72353 15.3346 10.0509V11.4228H13.3315ZM6.24816 5.74318C5.52941 5.74318 4.91921 5.49234 4.41754 4.99068C3.91587 4.48887 3.66504 3.87859 3.66504 3.15984C3.66504 2.44109 3.91587 1.83089 4.41754 1.32922C4.91921 0.827413 5.52941 0.576511 6.24816 0.576511C6.96691 0.576511 7.57719 0.827413 8.079 1.32922C8.58066 1.83089 8.8315 2.44109 8.8315 3.15984C8.8315 3.87859 8.58066 4.48887 8.079 4.99068C7.57719 5.49234 6.96691 5.74318 6.24816 5.74318ZM12.1102 3.15984C12.1102 3.87859 11.8594 4.48887 11.3577 4.99068C10.8561 5.49234 10.2459 5.74318 9.52712 5.74318C9.47476 5.74318 9.44539 5.74637 9.439 5.75276C9.43247 5.75915 9.40309 5.75651 9.35087 5.74484C9.65171 5.39137 9.89039 4.99818 10.0669 4.56526C10.2433 4.13234 10.3315 3.66359 10.3315 3.15901C10.3315 2.65429 10.2415 2.18741 10.0615 1.75839C9.8815 1.3295 9.64462 0.935052 9.35087 0.575052C9.38712 0.573941 9.4165 0.573941 9.439 0.575052C9.46136 0.576024 9.49073 0.576511 9.52712 0.576511C10.2459 0.576511 10.8561 0.827413 11.3577 1.32922C11.8594 1.83089 12.1102 2.44109 12.1102 3.15984Z" fill="white"/>
                </svg>


                  {{ __('admin.navigation.manage_business') }}
                </a>
              </li>
              <li class="mt-4">
                <a href="{{ route('admin.earnings.index') }}" class="group flex gap-x-3 rounded-md {{ request()->routeIs('admin.earnings*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} p-2 text-sm/6 font-normal">
                <svg  class="size-6 shrink-0" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M13.1457 13.8375L13.7898 13.1933L12.4517 11.8552V10.0956H11.548V12.2398L13.1457 13.8375ZM11.995 15.6308C10.9994 15.6308 10.154 15.2817 9.459 14.5835C8.764 13.8855 8.4165 13.0385 8.4165 12.0427C8.4165 11.047 8.7656 10.2016 9.4638 9.50664C10.1619 8.81164 11.0088 8.46414 12.0046 8.46414C13.0003 8.46414 13.8457 8.81317 14.5407 9.51122C15.2357 10.2094 15.5832 11.0564 15.5832 12.0521C15.5832 13.0479 15.2341 13.8933 14.5359 14.5883C13.8378 15.2833 12.9909 15.6308 11.995 15.6308ZM3.20817 4.38706H10.7915V3.30393H3.20817V4.38706ZM6.78984 13.5827H1.7563C1.38657 13.5827 1.07081 13.4518 0.809004 13.1902C0.547337 12.9284 0.416504 12.6126 0.416504 12.2429V1.75581C0.416504 1.38609 0.547337 1.07032 0.809004 0.808516C1.07081 0.54685 1.38657 0.416016 1.7563 0.416016H12.2434C12.6131 0.416016 12.9289 0.54685 13.1907 0.808516C13.4523 1.07032 13.5832 1.38609 13.5832 1.75581V6.81831C13.3178 6.76053 13.0508 6.71588 12.7823 6.68435C12.5139 6.65296 12.253 6.63727 11.9998 6.63727C11.769 6.63727 11.5369 6.65352 11.3036 6.68602C11.0701 6.71865 10.8407 6.7622 10.6153 6.81664V6.45768H3.20817V7.54102H8.94213C8.55116 7.80379 8.2013 8.10907 7.89255 8.45685C7.5838 8.80463 7.32574 9.18956 7.11838 9.61164H3.20817V10.6948H6.70171C6.65796 10.9096 6.62248 11.1271 6.59525 11.3471C6.56803 11.5672 6.56137 11.7846 6.57525 11.9993C6.58914 12.2173 6.61393 12.4806 6.64963 12.7893C6.68546 13.0981 6.7322 13.3625 6.78984 13.5827Z" fill="white"/>
              </svg>

                  {{ __('admin.navigation.earnings') }}
                </a>
              </li>
              <li class="mt-4">
                <a href="{{ route('admin.content') }}" class="group flex gap-x-3 rounded-md {{ request()->routeIs('admin.content*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} p-2 text-sm/6 font-normal">
                <svg class="size-6 shrink-0" width="16" height="14" viewBox="0 0 16 14" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M2.41621 13.5829V4.41626L0.913086 1.22231L1.8985 0.75293L3.61809 4.41626H12.381L14.1006 0.75293L15.086 1.20147L13.5829 4.41626V13.5829H2.41621ZM6.74954 8.29126H9.24954C9.40302 8.29126 9.53163 8.23974 9.63538 8.13668C9.73927 8.03362 9.79121 7.90599 9.79121 7.75376C9.79121 7.6014 9.73927 7.47237 9.63538 7.36668C9.53163 7.26085 9.40302 7.20793 9.24954 7.20793H6.74954C6.59607 7.20793 6.46746 7.25946 6.36371 7.36251C6.25982 7.46557 6.20788 7.59321 6.20788 7.74543C6.20788 7.89779 6.25982 8.02682 6.36371 8.13251C6.46746 8.23835 6.59607 8.29126 6.74954 8.29126Z" fill="white"/>
              </svg>

                  {{ __('admin.navigation.training_video') }}
                </a>
              </li>
              <li class="mt-4">
                <a href="{{ route('admin.sub-admin') }}" class="group flex gap-x-3 rounded-md {{ request()->routeIs('admin.sub-admin*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} p-2 text-sm/6 font-normal">
                <svg class="size-6 shrink-0" width="16" height="12" viewBox="0 0 16 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M0.665039 11.4228V9.98672C0.665039 9.66839 0.74115 9.38929 0.893372 9.14943C1.04559 8.90957 1.2583 8.70984 1.5315 8.55026C2.21233 8.14901 2.92879 7.83297 3.68087 7.60214C4.43309 7.37144 5.28886 7.25609 6.24816 7.25609C7.20761 7.25609 8.06337 7.37144 8.81546 7.60214C9.56768 7.83297 10.2842 8.14901 10.965 8.55026C11.2382 8.70984 11.4509 8.90957 11.6032 9.14943C11.7554 9.38929 11.8315 9.66839 11.8315 9.98672V11.4228H0.665039ZM13.3315 11.4228V10.0509C13.3315 9.53172 13.2323 9.05554 13.034 8.62234C12.8355 8.18929 12.5736 7.837 12.2482 7.56547C12.6402 7.67658 13.0284 7.81248 13.4125 7.97318C13.7966 8.13401 14.175 8.32658 14.548 8.55089C14.7841 8.68339 14.9742 8.88471 15.1184 9.15484C15.2625 9.42484 15.3346 9.72353 15.3346 10.0509V11.4228H13.3315ZM6.24816 5.74318C5.52941 5.74318 4.91921 5.49234 4.41754 4.99068C3.91587 4.48887 3.66504 3.87859 3.66504 3.15984C3.66504 2.44109 3.91587 1.83089 4.41754 1.32922C4.91921 0.827413 5.52941 0.576511 6.24816 0.576511C6.96691 0.576511 7.57719 0.827413 8.079 1.32922C8.58066 1.83089 8.8315 2.44109 8.8315 3.15984C8.8315 3.87859 8.58066 4.48887 8.079 4.99068C7.57719 5.49234 6.96691 5.74318 6.24816 5.74318ZM12.1102 3.15984C12.1102 3.87859 11.8594 4.48887 11.3577 4.99068C10.8561 5.49234 10.2459 5.74318 9.52712 5.74318C9.47476 5.74318 9.44539 5.74637 9.439 5.75276C9.43247 5.75915 9.40309 5.75651 9.35087 5.74484C9.65171 5.39137 9.89039 4.99818 10.0669 4.56526C10.2433 4.13234 10.3315 3.66359 10.3315 3.15901C10.3315 2.65429 10.2415 2.18741 10.0615 1.75839C9.8815 1.3295 9.64462 0.935052 9.35087 0.575052C9.38712 0.573941 9.4165 0.573941 9.439 0.575052C9.46136 0.576024 9.49073 0.576511 9.52712 0.576511C10.2459 0.576511 10.8561 0.827413 11.3577 1.32922C11.8594 1.83089 12.1102 2.44109 12.1102 3.15984Z" fill="white"/>
                </svg>
                  {{ __('admin.navigation.manage_admins') }}
                </a>
              </li>
              <li class="mt-4">
                <a href="{{ route('admin.mass-message') }}" class="group flex gap-x-3 rounded-md {{ request()->routeIs('admin.sub-admin*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} p-2 text-sm/6 font-normal">
                <svg class="size-6 shrink-0" width="16" height="12" viewBox="0 0 16 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M0.665039 11.4228V9.98672C0.665039 9.66839 0.74115 9.38929 0.893372 9.14943C1.04559 8.90957 1.2583 8.70984 1.5315 8.55026C2.21233 8.14901 2.92879 7.83297 3.68087 7.60214C4.43309 7.37144 5.28886 7.25609 6.24816 7.25609C7.20761 7.25609 8.06337 7.37144 8.81546 7.60214C9.56768 7.83297 10.2842 8.14901 10.965 8.55026C11.2382 8.70984 11.4509 8.90957 11.6032 9.14943C11.7554 9.38929 11.8315 9.66839 11.8315 9.98672V11.4228H0.665039ZM13.3315 11.4228V10.0509C13.3315 9.53172 13.2323 9.05554 13.034 8.62234C12.8355 8.18929 12.5736 7.837 12.2482 7.56547C12.6402 7.67658 13.0284 7.81248 13.4125 7.97318C13.7966 8.13401 14.175 8.32658 14.548 8.55089C14.7841 8.68339 14.9742 8.88471 15.1184 9.15484C15.2625 9.42484 15.3346 9.72353 15.3346 10.0509V11.4228H13.3315ZM6.24816 5.74318C5.52941 5.74318 4.91921 5.49234 4.41754 4.99068C3.91587 4.48887 3.66504 3.87859 3.66504 3.15984C3.66504 2.44109 3.91587 1.83089 4.41754 1.32922C4.91921 0.827413 5.52941 0.576511 6.24816 0.576511C6.96691 0.576511 7.57719 0.827413 8.079 1.32922C8.58066 1.83089 8.8315 2.44109 8.8315 3.15984C8.8315 3.87859 8.58066 4.48887 8.079 4.99068C7.57719 5.49234 6.96691 5.74318 6.24816 5.74318ZM12.1102 3.15984C12.1102 3.87859 11.8594 4.48887 11.3577 4.99068C10.8561 5.49234 10.2459 5.74318 9.52712 5.74318C9.47476 5.74318 9.44539 5.74637 9.439 5.75276C9.43247 5.75915 9.40309 5.75651 9.35087 5.74484C9.65171 5.39137 9.89039 4.99818 10.0669 4.56526C10.2433 4.13234 10.3315 3.66359 10.3315 3.15901C10.3315 2.65429 10.2415 2.18741 10.0615 1.75839C9.8815 1.3295 9.64462 0.935052 9.35087 0.575052C9.38712 0.573941 9.4165 0.573941 9.439 0.575052C9.46136 0.576024 9.49073 0.576511 9.52712 0.576511C10.2459 0.576511 10.8561 0.827413 11.3577 1.32922C11.8594 1.83089 12.1102 2.44109 12.1102 3.15984Z" fill="white"/>
                </svg>
                  {{ __('admin.navigation.mass_message') }}
                </a>
              </li>
              <!-- Settings Menu Item with Theme Toggle -->
              <li>
                    <a href="{{ route('admin.setting') }}" class="group flex gap-x-3 rounded-md {{ request()->routeIs('admin.setting') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} p-2 text-sm/6 font-normal">
                      <svg class="size-6 shrink-0" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9.72656 0.666504H6.27344L5.90625 2.2793C5.42969 2.45964 5.0013 2.70964 4.64062 3.01318L3.04688 2.38623L1.38672 4.04639L2.01367 5.64014C1.7103 6.00082 1.46094 6.4292 1.28027 6.90601L0.666016 7.27319V9.72632L2.2793 10.0935C2.45964 10.5701 2.70964 10.9985 3.01318 11.3592L2.38623 12.953L4.04639 14.6131L5.64014 13.9862C6.00082 14.2896 6.4292 14.539 6.90601 14.7197L7.27319 15.3335H9.72656L10.0938 13.7207C10.5703 13.5404 10.9987 13.2904 11.3594 12.9868L12.9531 13.6138L14.6133 11.9536L13.9863 10.3599C14.2897 9.99921 14.5391 9.57083 14.7197 9.09402L15.333 8.72685V6.27372L13.7207 5.90654C13.5404 5.42994 13.2904 5.00156 12.9868 4.64087L13.6138 3.04712L11.9536 1.38696L10.3599 2.01391C9.99921 1.71055 9.57083 1.46119 9.09402 1.28052L8.72685 0.666504H9.72656ZM8.00004 10.6665C6.53337 10.6665 5.33337 9.4665 5.33337 7.99984C5.33337 6.53317 6.53337 5.33317 8.00004 5.33317C9.46671 5.33317 10.6667 6.53317 10.6667 7.99984C10.6667 9.4665 9.46671 10.6665 8.00004 10.6665Z" fill="white"/>
                      </svg>
                      {{ __('admin.navigation.setting') }}
                    </a>
                  </li>
            </ul>
          </li>
        </ul>
      </nav>
    </div>
</div>
