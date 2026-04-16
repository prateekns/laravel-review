<div class="relative">
    <!-- Loading Overlay for Child Component -->
    <x-loading :target="'filter'" />

    <!-- Time Filter -->
    <div class="flex justify-end mb-4">
        <x-time-filter :filter="$filter" :all="false" :custom="false" :daily="false"/>
    </div>
    <!-- End Time Filter -->

    <!-- Chart Container -->
    <div
        wire:ignore
        x-data="initEarningsBarChart(@js($labels), @js($totals), @js($filter))"
        class="bg-white rounded-lg shadow-sm p-4"
        wire:loading.class="opacity-50"
        wire:target="filter"
    >
        <h2 class="mx-auto mb-6 text-lg/6 font-medium text-gray-900">{{ __('admin.dashboard.earnings') }}</h2>
        <canvas x-ref="canvas" height="120"></canvas>
    </div>
</div>
