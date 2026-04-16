<div class="container-fluid mx-auto"
    x-data="selectAllTechnicians()"
    @close.window="cancelMessageModal()"
    @sms-success.window="
        successMessage = $event.detail[0].message;
        showSuccess = true;
        showToast = true;
        setTimeout(() => {
            showToast = false;
            showSuccess = false;
        }, 5000);
        cancelMessageModal();
    "
    >
    <div x-show="search.length > 2" x-cloak>
        <x-loading :target="'search'" />
    </div>
    <x-loading :target="'previousPage,nextPage'" />
    <div class="flex justify-between items-center flex-row max-[1200px]:flex-col add-sub-mobile-wrapper">
        <div class="gap-[16px] flex flex-col">
            <h1 class="main-heading">{{ __('Manage Technicians') }}</h1>
            <p class="sub-heading"> {{ __('Enable admins to add, edit, view, and archive technician profiles efficiently.') }}</p>
        </div>
        @if(!$technicians->isEmpty())
        <x-form.link
            :link="!$warningMessage ? route('business.technicians.create') : ''"
            class="btn-box gap-2 outlined {{$warningMessage ? 'disabled' : '' }}">
            <svg width="20" height="21" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M9.1665 11.3334H4.1665V9.66669H9.1665V4.66669H10.8332V9.66669H15.8332V11.3334H10.8332V16.3334H9.1665V11.3334Z" fill="#0D44EA" />
            </svg>
            {{ __('Add New Technician') }}
        </x-form.link>
        @endif
    </div>

    <div x-show="showSuccess" x-cloak>
        <x-toast type="success" message="successMessage" x-show="successMessage"/>
    </div>


    @if(session()->has('success'))
    <x-notification-alert type="success" :message="session('success')" />
    @endif

    @if (session()->has('error'))
    <x-notification-alert type="error" :message="session('error')" />
    @endif

    @if($warningMessage)
        @if($pastDue)
            <x-limit-warning :buttonText="__('My Plan')" :warningText="$warningMessage" :link="route('account.my-plan')" />
        @else
            <x-limit-warning :warningText="$warningMessage" :link="route('account.index')" />
        @endif
    @endif


    @if($technicians->isEmpty() && !$search)
    <x-empty-state
        title="{{ __('No Technicians Added') }}"
        description="{{ __('No technicians added yet. Start by creating your first technician.') }}"
        :buttonLink="$warningMessage ? '' : route('business.technicians.create')"
        :buttonText="__('Add New Technician')"
        :disabled="!empty($warningMessage)" />
    @else

    <div>
        <!-- Businesses Card -->
        <div class="white-box">
            <div class="top-box">
                <div class="sm:flex-auto">
                    <h2 class="white-box-heading"> {{ __('Existing Technicians') }}</h1>
                </div>

                <!-- Search Input -->
                 
                <div class=" grid grid-cols-1 relative">
                    <input
                        type="text"
                        wire:model.live="search"
                        class="col-start-1 row-start-1 block w-full rounded-md bg-white py-2.5 pr-10 pl-10 text-base text-[#000000] border-2 border-[#767676] placeholder:text-[#767676] focus:border-blue-500 sm:pl-9 text-[16px] font-[400] appearance-none"
                        placeholder="Search">
                    <svg class="pointer-events-none col-start-1 row-start-1 ml-2 size-5 self-center" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M17.5 17.5L12.5001 12.5M14.1667 8.33333C14.1667 11.555 11.555 14.1667 8.33333 14.1667C5.11167 14.1667 2.5 11.555 2.5 8.33333C2.5 5.11167 5.11167 2.5 8.33333 2.5C11.555 2.5 14.1667 5.11167 14.1667 8.33333Z" stroke="#767676" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>


                    @if($search)
                        <button type="button"
                            wire:click="$set('search', '')"
                            class="absolute right-2 top-1/2 -translate-y-1/2 text-[#212529] hover:text-gray-600 focus:outline-none cursor-pointer"
                            aria-label="Clear search">
                            <svg class="size-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 8.586l4.95-4.95a1 1 0 1 1 1.414 1.414L11.414 10l4.95 4.95a1 1 0 0 1-1.414 1.414L10 11.414l-4.95 4.95a1 1 0 0 1-1.414-1.414L8.586 10l-4.95-4.95A1 1 0 1 1 5.05 3.636L10 8.586z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    @endif
                </div>

                <div>
                    <button
                        type="button"
                        class="btn-box btn"
                        @click="showMessageModal = true"
                        x-show="selectedTechnicians.length > 1"
                        x-cloak
                    >
                        {{ __('Send Message') }}
                    </button>
                </div>
            
                <!-- Search Input Ends -->
            </div>

            <div class="table-box">
                <form wire:submit="sendMessage">
                    <!-- Table -->
                    <table class="min-w-full divide-y divide-gray-300" aria-describedby="Technician List">
                        <thead>
                            <tr>
                                <th class="input-checkbox m-w-[20px] w-[20px]">
                                    <input type="checkbox" x-model="selectAll" class="technician-checkbox" @click="toggleAll()">
                                </th>
                                <th scope="col" class="min-[641px]:w-[200px] min-[641px]:whitespace-wrap">{{ __('Staff ID') }}</th>
                                <th scope="col">{{ __('Technician Name') }}</th>
                                <th scope="col">{{ __('E-mail') }}</th>
                                <th scope="col">{{ __('Phone') }}</th>
                                <th scope="col">{{ __('Skill Type') }}</th>
                                <th scope="col">{{ __('Status') }}</th>
                                <th scope="col">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($technicians as $technician)
                            <tr>
                                <td class="input-checkbox">
                                    <input type="checkbox" class="technician-checkbox" wire:model="selectedTechnicians" value="{{$technician->id}}" @change="updateSelected()">
                                </td>
                                <td data-label="Staff ID">{{ $technician->staff_id }}</td>
                                <td data-label="Technician Name">{{ $technician->first_name }} {{ $technician->last_name }}</td>
                                <td data-label="E-mail" class="email-td">{{ $technician->email }}</td>
                                <td data-label="Phone">{{ $technician->isd_code }}-{{ $technician->phone }}</td>
                                <td data-label="Skill Type">
                                    {{ $technician->skills?->pluck('skill_type')->take(2)->implode(', ') ?: __('admin.technician.no_skill') }}

                                    <div x-data="{ open: false }" class="relative inline-block">

                                        <span @click="open = !open" class="text-blue text-[12px] font-[400] underline italic  cursor-pointer pl-[4px]">
                                            @if($technician->skills?->count() > 2)
                                            +{{ $technician->skills?->count() - 2 }} more
                                            @endif
                                        </span>

                                        <div x-show="open" @click.away="open = false"
                                            class="absolute left-0 mt-2 bg-white  rounded p-2 text-[12px] shadow-sm z-10">
                                            {{$technician->skills?->pluck('skill_type')->slice(2)->implode(', ')}}
                                        </div>

                                    </div>

                                </td>

                                <td data-label="Status" class="status-td">
                                    @if($technician->status)
                                    <span class="badge bg-success">{{__('Active')}}</span>
                                    @else
                                    <span class="badge bg-warning">{{__('Inactive')}}</span>
                                    @endif
                                </td>
                                <td class="table-actions">
                                    <div class="flex gap-[10px] max-[767px]:justify-end ">
                                        <a href="{{ route('business.technicians.edit', $technician) }}" class="text-[#0d44ea]">
                                            <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M15.25 15.7502H9.25002M1.375 16.1252L5.53695 14.5245C5.80316 14.4221 5.93626 14.3709 6.06079 14.3041C6.1714 14.2447 6.27685 14.1761 6.37603 14.0992C6.48769 14.0125 6.58853 13.9117 6.79021 13.71L15.25 5.25023C16.0784 4.4218 16.0784 3.07865 15.25 2.25023C14.4216 1.4218 13.0784 1.4218 12.25 2.25022L3.79021 10.71C3.58853 10.9117 3.48769 11.0125 3.40104 11.1242C3.32408 11.2234 3.25555 11.3288 3.19618 11.4394C3.12933 11.564 3.07814 11.6971 2.97575 11.9633L1.375 16.1252ZM1.375 16.1252L2.91859 12.1119C3.02905 11.8248 3.08428 11.6812 3.17901 11.6154C3.26179 11.5579 3.36423 11.5362 3.46322 11.5551C3.5765 11.5767 3.68529 11.6855 3.90286 11.9031L5.59718 13.5974C5.81475 13.815 5.92354 13.9237 5.94517 14.037C5.96408 14.136 5.94234 14.2385 5.88486 14.3212C5.81908 14.416 5.67549 14.4712 5.3883 14.5817L1.375 16.1252Z" stroke="#212529" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </a>
                                        <div class="cursor-pointer send-message" @click="selectTechnician()">
                                            <svg width="17" height="14" viewBox="0 0 17 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M1 3.25L7.12369 7.53658C7.61957 7.8837 7.86751 8.05726 8.1372 8.12448C8.37542 8.18386 8.62458 8.18386 8.8628 8.12448C9.13249 8.05726 9.38043 7.8837 9.87631 7.53658L16 3.25M4.6 13H12.4C13.6601 13 14.2902 13 14.7715 12.7548C15.1948 12.539 15.539 12.1948 15.7548 11.7715C16 11.2902 16 10.6601 16 9.4V4.6C16 3.33988 16 2.70982 15.7548 2.22852C15.539 1.80516 15.1948 1.46095 14.7715 1.24524C14.2902 1 13.6601 1 12.4 1H4.6C3.33988 1 2.70982 1 2.22852 1.24524C1.80516 1.46095 1.46095 1.80516 1.24524 2.22852C1 2.70982 1 3.33988 1 4.6V9.4C1 10.6601 1 11.2902 1.24524 11.7715C1.46095 12.1948 1.80516 12.539 2.22852 12.7548C2.70982 13 3.33988 13 4.6 13Z" stroke="#212529" stroke-width="1.2525" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="py-4 text-center text-gray-500">{{ __('No technician users found for your search.') }}</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <!-- Table Ends -->
                    <x-modal.select-message :messages="$messages" />

                </form>

                <!-- Footer -->
                <div class="pagination-box">
                    <x-table-pagination :list="$technicians" />
                </div>
                <!-- Footer Ends -->
            </div>

        </div>
        <!-- Businesses Card -->
    </div>
    @endif
</div>
