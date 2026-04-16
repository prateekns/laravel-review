<div
    x-data="{
        confirmDelete: @entangle('confirmDelete'),
        confirmStatus: @entangle('confirmStatus'),
        showToast: @entangle('showToast'),
        modelId:null,
        hideToast: function() {
                setTimeout(() => { this.showToast = false;}, 5000);
        },
    }"
    @cancelled="confirmDelete=false"
    @cancel="confirmDelete=false;confirmStatus=false"
    @confirm-delete="$wire.call('delete', modelId)"
    @hide-toast="hideToast()"
    >

    <!--Loading Indicator-->
    <x-loading :target="'search,statusFilter,previousPage,nextPage'"/>

    <div class="px-4 sm:px-6 lg:px-4">
        
        <div class="divide-y divide-gray-200 overflow-hidden rounded-lg bg-white shadow-sm">
            <div class="px-4 py-5 sm:px-6 sm:flex sm:items-center">
                <div class="sm:flex-auto">
                    <h2 class="white-box-heading">{{ __('admin.sub-admin.manage_admins') }}</h1>
                </div>

                <div class="mt-3 flex sm:mt-0 sm:mr-4">
                    <a href="{{ route('admin.sub-admin.create')}}" class="cursor-pointer block rounded-md bg-indigo-500 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500"> {{ __('admin.sub-admin.add_admin') }}</a>
                </div>

                <!-- Status Filter -->
                <div class="mr-4">
                    <x-status-filter :status-filter="$statusFilter"/>
                </div>
                <!-- Status Filter Ends -->

                <!-- Search Input -->
                <x-search :search="$search" :placeholder="__('admin.sub-admin.search_admins')"/>
                <!-- Search Input Ends -->
            </div>
            <!-- Table -->
            <table class="min-w-full divide-y divide-gray-300" aria-describedby="Sub Admin List">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">{{ __('admin.sub-admin.name') }}</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">{{ __('admin.sub-admin.email') }}</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">{{ __('admin.status') }}</th>
                        <th scope="col" class="px-3 py-3.5 text-center text-sm font-semibold text-gray-900">{{ __('admin.sub-admin.action') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($admins as $admin)
                        <tr>
                            <td class="px-3 py-4 text-sm  text-gray-500">
                                <div class="flex items-center">
                                    @if($admin->avatar)
                                        <img class="size-8 rounded-full" src="{{ $admin->user_avatar }}" alt="{{ $admin->name }}">
                                    @else
                                        <div class="size-8 rounded-full bg-yellow-500 flex items-center justify-center">
                                            <span class="text-sm/6 font-semibold text-gray-900">{{ $admin->user_initials }}</span>
                                        </div>
                                    @endif
                                    <span class="ml-3 whitespace-wrap text-gray-900 break-words max-w-[100px]">{{ $admin->name }}</span>
                                </div>
                            </td>
                            <td class="px-3 py-4 text-sm whitespace-nowrap text-gray-500">{{ $admin->email }}</td>
                            <td class="px-3 py-4 text-sm whitespace-nowrap">
                                @if(!$admin->status)
                                    <span class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/10">
                                        {{ __('admin.sub-admin.inactive') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/10">
                                        {{ __('admin.sub-admin.active') }}
                                    </span>
                                @endif
                            </td>
                            <td class="relative py-4 pr-4 pl-3 text-right text-sm font-medium whitespace-nowrap sm:pr-6">
                                <div class="flex flex-col-reverse gap-2 justify-center sm:flex-row">
                                   <a href="{{ route('admin.sub-admin.edit', $admin->id) }}"> <x-icons name="edit"/></a>
            
                                   @if($user_id != $admin->id)
                                        <span class="mt-1" @click="modelId = {{$admin->id}}; confirmDelete=true;"> <x-icons name="delete"/></span>
                                        @if(!$admin->is_primary)
                                            <x-toggle-btn
                                                :model="$admin"
                                                :id="$admin->id"
                                                :on-toggle="$admin->status ? 'showDeactivateModal' : 'showActivateModal'"
                                                class="ml-2 mt-1"
                                                @click="confirmStatus=true"
                                            />
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-4 text-center text-gray-500">
                                @if($error)
                                    {{ $search ? __('admin.message.sub_admin_search_failed') : __('admin.sub-admin.load_failed') }}
                                 @else
                                    {{ $search ? __('admin.message.no_sub_admin_found_search') : __('admin.sub-admin.no_admins_found') }}
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <!-- Table Ends -->

            <!-- Footer -->
            @if($admins)
                <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                    <x-table-pagination :list="$admins"/>
                </div>
            @endif
            <!-- Footer Ends -->
        </div>
        <!-- Team Members Card -->
    </div>

    <!-- Sub Admin Deactivate/Activate Confirmation Modal -->
    @if($selectedAdmin)
         <x-confirm.confirm-status
            :model="$selectedAdmin"
            message="{{ $selectedAdmin->status ? __('admin.alert.confirm_deactivate_sub_admin') :  __('admin.alert.activate_sub_admin') }}"
        />
    @endif

    <!-- Sub Admin Delete COnfirmation Modal -->
    <x-admin.confirm-delete
        message="{{ __('admin.alert.delete_sub_admin') }}"
        btnConfirm="{{ __('admin.button.yes') }}"
    />

    <div x-show="showToast">
        <x-toast :message="$message" type="success"/>
    </div>

    @if (session()->has('success'))
        <x-notification-alert type="success" :message="session('success')" />
    @endif

    @if (session()->has('error'))
        <x-notification-alert type="error" :message="session('error')" />
    @endif
</div>
