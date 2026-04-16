<div class="container-fluid mx-auto" x-data="{ search: '' }">
    <div x-show="search.length > 2" x-cloak>
        <x-loading :target="'search'" />
    </div>
    <x-loading :target="'previousPage,nextPage'" />

    <div class="flex justify-between items-center flex-row max-[1200px]:flex-col add-sub-mobile-wrapper ">
        <div class="gap-[16px] flex flex-col">
            <h1 class="main-heading">{{ __('Manage Sub-Admins') }}</h1>
            <p class="sub-heading">{{ __('Create, update, or update status of Sub-Admin accounts as per your subscription plan') }}</p>
        </div>

        @if(!$subAdmins->isEmpty())
        <x-form.link
            :link="$canAddSubAdmin ? route('business.sub-admins.create') : ''"
            class="btn-box gap-2 outlined {{$hasReachedLimit || !$hasActivePlan ? 'disabled' : '' }}">
            <svg width="20" height="21" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M9.1665 11.3334H4.1665V9.66669H9.1665V4.66669H10.8332V9.66669H15.8332V11.3334H10.8332V16.3334H9.1665V11.3334Z" class="icon-color" />
            </svg>
            {{ __('Add New Sub-admin') }}
        </x-form.link>
        @endif
    </div>
    @if($warningMessage)
        @if($pastDue)
            <x-limit-warning :buttonText="__('My Plan')" :warningText="$warningMessage" :link="route('account.my-plan')" />
        @else
            <x-limit-warning :warningText="$warningMessage" :link="route('account.index')" />
        @endif
    @endif

    @if($subAdmins->isEmpty() && !$search)
    <x-empty-state
        title="{{ __('No Sub-Admins Added') }}"
        description="{{ __('No Sub-Admins added yet. Start by creating your first Sub-Admin.') }}"
        :buttonLink="($hasReachedLimit || !$hasActivePlan) ? '' : route('business.sub-admins.create')"
        :buttonText="__('Add New Sub-Admin')"
        :disabled="$hasReachedLimit || !$hasActivePlan" />
    @else
    <div class="white-box">
        <div class="top-box">
            <div class="sm:flex-auto">
                <h2 class="white-box-heading">{{ __('Existing Sub-admins') }}</h2>
            </div>

            <!-- Search Input -->
            <div class="grid grid-cols-1 relative">
                <input
                    x-model="search"
                    type="text"
                    wire:model.live="search"
                    class="col-start-1 row-start-1 block w-full rounded-md bg-white py-2.5 pr-10 pl-10 text-base border-2 text-[#000000] border-[#767676] placeholder:text-[#767676] focus:border-blue-500 sm:pl-9 text-[16px] font-[400] appearance-none"
                    placeholder="{{ __('Search') }}">
             
                <svg class="pointer-events-none col-start-1 row-start-1 ml-2 size-5 self-center text-[#767676]" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M17.5 17.5L12.5001 12.5M14.1667 8.33333C14.1667 11.555 11.555 14.1667 8.33333 14.1667C5.11167 14.1667 2.5 11.555 2.5 8.33333C2.5 5.11167 5.11167 2.5 8.33333 2.5C11.555 2.5 14.1667 5.11167 14.1667 8.33333Z" stroke="#767676" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>

                @if($search)
                <button type="button"
                    wire:click="$set('search', '')"
                    class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none cursor-pointer"
                    aria-label="{{ __('Clear search') }}">
                    <svg class="size-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 8.586l4.95-4.95a1 1 0 1 1 1.414 1.414L11.414 10l4.95 4.95a1 1 0 0 1-1.414 1.414L10 11.414l-4.95 4.95a1 1 0 0 1-1.414-1.414L8.586 10l-4.95-4.95A1 1 0 1 1 5.05 3.636L10 8.586z" clip-rule="evenodd" />
                    </svg>
                </button>
                @endif
            </div>
            <!-- Search Input Ends -->
        </div>
        <div class="table-box">
            <table class="min-w-full divide-y divide-gray-300" aria-describedby="Business Sub Admin List">
                <thead>
                    <tr>
                        <th scope="col" class="min-[641px]:w-[200px] min-[641px]:whitespace-wrap">
                            {{ __('Name') }}
                        </th>
                        <th scope="col">
                            {{ __('E-mail') }}
                        </th>
                        <th scope="col">
                            {{ __('Status') }}
                        </th>
                        <th scope="col" class="!text-center">
                            {{ __('Actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subAdmins as $subAdmin)
                    <tr>
                        <td data-label="Name">
                            {{ $subAdmin->adminName }}
                        </td>
                        <td data-label="E-mail" class="email-td">
                            {{ $subAdmin->email }}
                        </td>
                        <td data-label="Status" class="status-td">
                            <span class="{{ $subAdmin->status ? 'badge bg-success' : 'badge bg-warning' }}">
                                {{ $subAdmin->status ? __('Active') : __('Inactive') }}
                            </span>
                        </td>
                        <td  class="flex gap-4 max-[767px]:justify-end justify-center table-actions">
                            <a href="{{ route('business.sub-admins.edit', $subAdmin->id) }}" class="text-[#0d44ea] edit-sub-admin">
                                <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M15.25 15.7502H9.25002M1.375 16.1252L5.53695 14.5245C5.80316 14.4221 5.93626 14.3709 6.06079 14.3041C6.1714 14.2447 6.27685 14.1761 6.37603 14.0992C6.48769 14.0125 6.58853 13.9117 6.79021 13.71L15.25 5.25023C16.0784 4.4218 16.0784 3.07865 15.25 2.25023C14.4216 1.4218 13.0784 1.4218 12.25 2.25022L3.79021 10.71C3.58853 10.9117 3.48769 11.0125 3.40104 11.1242C3.32408 11.2234 3.25555 11.3288 3.19618 11.4394C3.12933 11.564 3.07814 11.6971 2.97575 11.9633L1.375 16.1252ZM1.375 16.1252L2.91859 12.1119C3.02905 11.8248 3.08428 11.6812 3.17901 11.6154C3.26179 11.5579 3.36423 11.5362 3.46322 11.5551C3.5765 11.5767 3.68529 11.6855 3.90286 11.9031L5.59718 13.5974C5.81475 13.815 5.92354 13.9237 5.94517 14.037C5.96408 14.136 5.94234 14.2385 5.88486 14.3212C5.81908 14.416 5.67549 14.4712 5.3883 14.5817L1.375 16.1252Z" stroke="#212529" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>

                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-4 text-center text-gray-500">{{ __('No sub-admins found matching your search.') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="pagination-box">
                {{-- $subAdmins->links() --}}
                <x-table-pagination :list="$subAdmins" />
            </div>
        </div>
    </div>
    @endif

    @if(session()->has('success'))
    <x-notification-alert type="success" :message="session('success')" />
    @endif

    @if (session()->has('error'))
    <x-notification-alert type="error" :message="session('error')" />
    @endif
</div>
