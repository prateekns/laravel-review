@props(['filter' => 'monthly', 'all' => false, 'custom' => true, 'daily' => true])
<div class="relative inline-block text-left"
     x-data="{
        open: false,
        localFilter: @entangle('filter'),
        showCustomDates: @js($filter === 'custom'),
        localFromDate: '',
        localToDate: '',
        selectFilter(){
            if(this.localFilter != 'custom'){
                this.localFromDate = '';
                this.localToDate = '';
                $wire.set('filter', this.localFilter);
            }
        },
        validateAndUpdate() {
            if (this.localFromDate && this.localToDate) {
                if (this.localFromDate <= this.localToDate) {
                    // Valid date range
                    $wire.set('fromDate', this.localFromDate);
                    $wire.set('toDate', this.localToDate);
                    $wire.set('filter', 'custom');
                } else {
                    // Invalid date range - use timestamp to make each error unique
                    $wire.dispatch('date-range-error');
                }
            }
        }
     }">
    <div class="flex items-center space-x-4 justify-end">

    @if($custom)
        
        <div x-show="showCustomDates" x-transition class="relative flex items-center space-x-4 mt-0 top-[-12px]" x-cloak>
        
            <div class="relative">
                <label for="fromDate" class="block text-sm font-medium leading-6 text-gray-900">{{ __('admin.label.date_from') }}</label>
                <input
                    type="date"
                    id="fromDate"
                    x-ref="fromDate"
                    x-model="localFromDate"
                    max="{{ date('Y-m-d') }}"
                    @change="validateAndUpdate()"
                    @keypress.prevent
                    @keyup.prevent
                    @paste.prevent
                    @click="$refs.fromDate.showPicker && $refs.fromDate.showPicker()"
                    class="block w-full h-[45px] rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" required>
            </div>
            <span class="text-gray-500 mt-[25px]">- </span>
            <div class="relative">
                <label for="toDate" class="block text-sm font-medium leading-6 text-gray-900">{{ __('admin.label.date_to') }}</label>
                <input
                    type="date"
                    id="toDate"
                    x-ref="toDate"
                    x-model="localToDate"
                    @change="validateAndUpdate()"
                    @keypress.prevent
                    @keyup.prevent
                    @paste.prevent
                    max="{{ date('Y-m-d') }}"
                    @click="$refs.toDate.showPicker && $refs.toDate.showPicker()"
                    class="block h-[45px] w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" required>
            </div>
            <div class="absolute bottom-[-20px] flex w-full text-sm text-yellow-800 mt-2 text-[12px]">{{ __('admin.filter.date_instruction')}}</div>
        </div>
        @endif

        <div class="relative inline-block text-left"
             @click.outside="open = false" x-cloak>
            <div>
                <button type="button"
                    @click="open = !open"
                    class="inline-flex w-full justify-center gap-x-1.5 rounded-md bg-white px-3 py-2.5 text-sm text-base text-gray-900 shadow-xs outline-1 -outline-offset-1 outline-gray-300 hover:bg-gray-50 sm:text-sm/6"
                    :aria-expanded="open"
                    aria-haspopup="true">
                    <span x-text="localFilter === 'custom' ? '{{ __('admin.filter.custom') }}' : (localFilter ? localFilter.charAt(0).toUpperCase() + localFilter.slice(1) : '{{ __(ucfirst($filter)) }}')"></span>
                    <svg class="-mr-1 size-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                        <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>

            <div x-show="open"
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="transform opacity-0 scale-95"
                x-transition:enter-end="transform opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="transform opacity-100 scale-100"
                x-transition:leave-end="transform opacity-0 scale-95"
                class="absolute right-0 z-10 mt-2 w-56 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black/5 focus:outline-hidden"
                role="menu"
                aria-orientation="vertical"
                aria-labelledby="menu-button"
                tabindex="-1"
                @click.stop>
                <div class="py-1" role="none">
                    @if($all)
                        <button type="button"
                                wire:click="$set('filter', 'all')"
                                @click="open = false; localFilter = 'all'; showCustomDates = false; selectFilter();"
                                class="block w-full px-4 py-2 text-left text-sm {{ $filter === 'all' ? 'bg-gray-100 text-gray-900' : 'text-gray-700' }} hover:bg-gray-100"
                                role="menuitem">
                                    {{ __('admin.filter.all') }}
                        </button>
                    @endif
                    @if($daily)
                        <button type="button"
                            wire:click="$set('filter', 'daily')"
                            @click="open = false; localFilter = 'daily'; showCustomDates = false; selectFilter();"
                            class="block w-full px-4 py-2 text-left text-sm {{ $filter === 'daily' ? 'bg-gray-100 text-gray-900' : 'text-gray-700' }} hover:bg-gray-100"
                            role="menuitem">
                            {{ __('admin.filter.daily') }}
                        </button>
                    @endif
                    <button type="button"
                        wire:click="$set('filter', 'weekly')"
                        @click="open = false; localFilter = 'weekly'; showCustomDates = false; selectFilter();"
                        class="block w-full px-4 py-2 text-left text-sm {{ $filter === 'weekly' ? 'bg-gray-100 text-gray-900' : 'text-gray-700' }} hover:bg-gray-100"
                        role="menuitem">
                        {{ __('admin.filter.weekly') }}
                    </button>
                    <button type="button"
                        wire:click="$set('filter', 'monthly')"
                        @click="open = false; localFilter = 'monthly'; showCustomDates = false; selectFilter();"
                        class="block w-full px-4 py-2 text-left text-sm {{ $filter === 'monthly' ? 'bg-gray-100 text-gray-900' : 'text-gray-700' }} hover:bg-gray-100"
                        role="menuitem">
                        {{ __('admin.filter.monthly') }}
                    </button>
                    <button type="button"
                        wire:click="$set('filter', 'yearly')"
                        @click="open = false; localFilter = 'yearly'; showCustomDates = false; selectFilter();"
                        class="block w-full px-4 py-2 text-left text-sm {{ $filter === 'yearly' ? 'bg-gray-100 text-gray-900' : 'text-gray-700' }} hover:bg-gray-100"
                        role="menuitem">
                        {{ __('admin.filter.yearly') }}
                    </button>

                    @if($custom)
                    <button type="button"
                        @click="open = false; localFilter = 'custom'; showCustomDates = true;"
                        :class="localFilter === 'custom' ? 'bg-gray-100 text-gray-900' : 'text-gray-700'"
                        class="block w-full px-4 py-2 text-left text-sm hover:bg-gray-100"
                        role="menuitem">
                        {{ __('admin.filter.custom') }}
                    </button>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>
