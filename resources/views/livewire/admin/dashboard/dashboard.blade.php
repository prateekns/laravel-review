<div class="relative"
    x-data="{ showToast:false}"
    x-on:date-range-error="showToast=true;setTimeout(() => { showToast = false; }, 5000);"
>

    <div x-show="showToast" x-cloak>
        <x-toast type="error" :message="__('admin.validation.date_range_error')" />
    </div>

    <!-- Loading Overlay for Filter Changes - Covers Entire Dashboard -->
    @if($filter != 'custom')
        <x-loading :target="'filter'" />
    @endif

    <x-loading :target="'fromDate,toDate'" />

    <div class="mt-4">
        <div >
            <!-- Time Filter -->
            <div class="flex justify-end">
                <x-time-filter :filter="$filter" :all="false"/>
            </div>
            <!-- End Time Filter -->
            
            <!-- Dashboard Content -->
            <h2 class="text-lg/6 font-medium text-gray-900"> {{ __('admin.dashboard.overview') }}</h2>
            <div class="mt-2 grid grid-cols-4 gap-5" wire:loading.class="opacity-50" wire:target="filter,fromDate,toDate">
                <!-- Total number of Businesses -->
                <x-card
                    :route="route('admin.business.index')"
                    :title="__('admin.dashboard.total_businesses')"
                    :value="$dashboardData['businesses']"
                />

                <!-- Total Number of Business Sub Admins -->
                <x-card
                    :title="__('admin.dashboard.active_business_users')"
                    :value="$dashboardData['activeBusinessUsers']"
                />

                <!-- Total Number of Super Sub Admins -->
                <x-card
                    :route="route('admin.sub-admin')"
                    :title="__('admin.dashboard.active_users')"
                    :value="$dashboardData['activeUsers']"
                />

                <!-- Total Revenue (Business Admin Plans) -->
                <x-card
                    :route="route('admin.earnings.index')"
                    :title="__('admin.dashboard.total_revenue')"
                    :value="$dashboardData['revenue']"
                    :currency="true"
                />
            </div>
        </div>

        <!-- Earnings Bar Chart -->
        <div class="w-full mt-8 relative">
            @livewire('admin.dashboard.earnings-bar-chart', key('earnings-bar-chart'))
        </div>
        <!-- ... rest of dashboard ... -->
    </div>
</div>
