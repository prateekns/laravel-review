<div class="container-fluid mx-auto" x-data="{ search: '' }">
    <div x-show="search.length > 2" x-cloak>
        <x-loading :target="'search'" />
    </div>
    <x-loading :target="'previousPage,nextPage'" />

    <div class="flex justify-between items-center flex-row max-[1200px]:flex-col add-sub-mobile-wrapper">
        <div class="gap-[16px] flex flex-col">
            <h1 class="main-heading">{{ __('business.customers.title') }}</h1>
            <p class="sub-heading">{{ __('business.customers.description') }}</p>
        </div>
        @if (!$customers->isEmpty())
            <div class="flex gap-[20px] max-[600px]:flex-col max-[600px]:w-full">
                <x-form.link link="{{ route('business.customers.import') }}" class="btn-box outlined max-[600px]:w-full">
                    {{ __('business.customers.import.title') }}
                </x-form.link>
                <x-form.link link="{{ route('business.customers.create') }}" class="btn-box btn max-[600px]:w-full">
                    {{ __('business.customers.add_new') }}
                </x-form.link>
            </div>
        @endif
    </div>

    @if (session()->has('success'))
        <x-notification-alert type="success" :message="session('success')" />
    @endif

    @if (session()->has('error'))
        <x-notification-alert type="error" :message="session('error')" />
    @endif

    @if ($customers->isEmpty() && !$search)
        <x-empty-state :title="__('business.customers.empty_state.title')" :description="__('business.customers.empty_state.description')" :buttonText="__('business.customers.import.title')" :buttonLink="route('business.customers.import')" :secondaryButtonText="__('business.customers.add_new')"
            :secondaryButtonLink="route('business.customers.create')" buttonClass="btn-box gap-2 outlined" secondaryButtonClass="btn-box gap-2"
            icon="users" />
    @else
        <div>
            <div class="white-box">
                <div class="top-box">
                    <div class="sm:flex-auto">
                        <h2 class="white-box-heading">{{ __('business.customers.existing_customers') }}</h2>
                    </div>

                    <!-- Search Input -->
                    <div class="grid grid-cols-1 relative">
                        <input type="text" wire:model.live="search"
                            class="col-start-1 row-start-1 block w-full rounded-md bg-white py-2.5 pr-10 pl-10 text-base text-[#000000] border-2 border-[#767676] placeholder:text-[#767676] focus:border-blue-500 sm:pl-9 text-[16px] font-[400] appearance-none"
                            placeholder="{{ __('business.customers.search') }}">
                        <svg class="pointer-events-none col-start-1 row-start-1 ml-2 size-5 self-center text-[#767676]"
                            viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z"
                                clip-rule="evenodd" />
                        </svg>
                        @if ($search)
                            <button type="button" wire:click="$set('search', '')"
                                class="absolute right-2 top-1/2 -translate-y-1/2 text-[#212529] hover:text-gray-600 focus:outline-none cursor-pointer"
                                aria-label="{{ __('business.customers.clear_search') }}">
                                <svg class="size-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M10 8.586l4.95-4.95a1 1 0 1 1 1.414 1.414L11.414 10l4.95 4.95a1 1 0 0 1-1.414 1.414L10 11.414l-4.95 4.95a1 1 0 0 1-1.414-1.414L8.586 10l-4.95-4.95A1 1 0 1 1 5.05 3.636L10 8.586z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        @endif
                    </div>
                </div>

                <div class="table-box">
                    <table class="min-w-full divide-y divide-gray-300" aria-describedby="Customer List">
                        <thead>
                            <tr>
                                <th scope="col">{{ __('business.customers.name') }}</th>
                                <th scope="col">{{ __('business.customer.commercial_company_name') }}</th>
                                <th scope="col">{{ __('business.customers.email') }}</th>
                                <th scope="col">{{ __('business.customers.status') }}</th>
                                <th scope="col">{{ __('business.customers.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customers as $customer)
                                <tr>
                                    <td data-label="Customer Name">
                                        {{ !empty($customer->name) ? $customer->name : '-' }}</td>
                                    <td data-label="Commercial Pool">{{ $customer->commercial_pool_details ?? '-' }}
                                    </td>
                                    <td data-label="E-mail" class="email-td">
                                        <a href="mailto:{{ $customer->email_1 }}" class="text-[#0d44ea]"
                                            title="{{ __('business.customers.email') }}">{{ $customer->email_1 }}</a>
                                    </td>
                                    <td data-label="Status" class="status-td">
                                        <span @class([
                                            'max-[640px]:justify-end font-[400] text-[12px]',
                                            'badge',
                                            'bg-success' => $customer->status,
                                            'bg-warning' => !$customer->status,
                                        ])>
                                            {{ $customer->status_text }}
                                        </span>
                                    </td>
                                    <td class.old="table-actions" x-data="{ open: false }" @click.away="open = false"
                                        @keydown.escape.window="open = false">
                                        <div class="relative">
                                            <button class="cursor-pointer" x-ref="trigger" @click="open = !open"
                                                class="text-gray-500 hover:text-gray-700 focus:outline-none">
                                                <svg width="19" height="18" viewBox="0 0 19 18" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M9.5 9.75C9.91421 9.75 10.25 9.41421 10.25 9C10.25 8.58579 9.91421 8.25 9.5 8.25C9.08579 8.25 8.75 8.58579 8.75 9C8.75 9.41421 9.08579 9.75 9.5 9.75Z"
                                                        stroke="black" stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                    <path
                                                        d="M9.5 4.5C9.91421 4.5 10.25 4.16421 10.25 3.75C10.25 3.33579 9.91421 3 9.5 3C9.08579 3 8.75 3.33579 8.75 3.75C8.75 4.16421 9.08579 4.5 9.5 4.5Z"
                                                        stroke="black" stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                    <path
                                                        d="M9.5 15C9.91421 15 10.25 14.6642 10.25 14.25C10.25 13.8358 9.91421 13.5 9.5 13.5C9.08579 13.5 8.75 13.8358 8.75 14.25C8.75 14.6642 9.08579 15 9.5 15Z"
                                                        stroke="black" stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                </svg>
                                            </button>
                                            <template x-teleport="body">
                                                <div x-show="open" x-anchor.bottom-end.offset.4="$refs.trigger"
                                                    x-transition:enter="transition ease-out duration-100"
                                                    x-transition:enter-start="transform opacity-0 scale-95"
                                                    x-transition:enter-end="transform opacity-100 scale-100"
                                                    x-transition:leave="transition ease-in duration-75"
                                                    x-transition:leave-start="transform opacity-100 scale-100"
                                                    x-transition:leave-end="transform opacity-0 scale-95"
                                                    class="table-actions-menu" style="display: none;">
                                                    <div class="options-menu" role="menu" aria-orientation="vertical"
                                                        aria-labelledby="options-menu">
                                                        <a class="option-menu-items"
                                                            href="{{ route('business.customers.show', $customer) }}"
                                                            role="menuitem">{{ __('business.customers.view') }}</a>
                                                        <a class="option-menu-items"
                                                            href="{{ route('business.customers.edit', $customer) }}"
                                                            role="menuitem">{{ __('business.customers.edit') }}</a>
                                                        @if ($customer->status)
                                                            <a class="option-menu-items"
                                                                href="{{ route('business.work-orders.customer.create', $customer) }}"
                                                                role="menuitem">{{ __('business.templates.work_order') }}</a>
                                                            <a class="option-menu-items"
                                                                href="{{ route('business.work-orders.maintenance.customer.create', $customer) }}"
                                                                role="menuitem">{{ __('business.templates.maintenance') }}</a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-4 text-center text-gray-500">
                                        <span
                                            class="w-full inline-block text-center">{{ __('business.customers.no_customers') }}</span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    @if ($customers->total() > 10)
                        <div class="pagination-box">
                            <x-pagination :paginator="$customers" />
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
