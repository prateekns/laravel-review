@extends('layouts.admin.app')

@section('title', 'Technician Details')

@section('content')
<div class="py-8" x-data="{ activeTab: 'profile' }">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
            <!-- Technician Header -->
            <div class="border-b border-gray-200 bg-white px-6 py-5">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-semibold leading-6 text-gray-900">{{ $technician->name }}</h3>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500">Technician ID: {{ $technician->id }}</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('admin.business.show', $technician->business_id) }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                            <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
                            </svg>
                            Back to Business
                        </a>
                    </div>
                </div>
            </div>

            <!-- Tabs Navigation -->
            <nav class="isolate flex divide-x divide-gray-200 rounded-lg shadow-sm" aria-label="Tabs">
                <button @click="activeTab = 'profile'"
                    :class="{ 'text-gray-900': activeTab === 'profile', 'text-gray-500 hover:text-gray-700': activeTab !== 'profile' }"
                    class="group relative min-w-0 flex-1 overflow-hidden rounded-l-lg bg-white px-4 py-4 text-center text-sm font-medium hover:bg-gray-50 focus:z-10">
                    <span>Profile</span>
                    <span aria-hidden="true"
                        :class="{ 'bg-indigo-500': activeTab === 'profile', 'bg-transparent': activeTab !== 'profile' }"
                        class="absolute inset-x-0 bottom-0 h-0.5"></span>
                </button>
                <button @click="activeTab = 'business'"
                    :class="{ 'text-gray-900': activeTab === 'business', 'text-gray-500 hover:text-gray-700': activeTab !== 'business' }"
                    class="group relative min-w-0 flex-1 overflow-hidden bg-white px-4 py-4 text-center text-sm font-medium hover:bg-gray-50 focus:z-10">
                    <span>Business</span>
                    <span aria-hidden="true"
                        :class="{ 'bg-indigo-500': activeTab === 'business', 'bg-transparent': activeTab !== 'business' }"
                        class="absolute inset-x-0 bottom-0 h-0.5"></span>
                </button>
                <button @click="activeTab = 'activity'"
                    :class="{ 'text-gray-900': activeTab === 'activity', 'text-gray-500 hover:text-gray-700': activeTab !== 'activity' }"
                    class="group relative min-w-0 flex-1 overflow-hidden bg-white px-4 py-4 text-center text-sm font-medium hover:bg-gray-50 focus:z-10">
                    <span>Activity</span>
                    <span aria-hidden="true"
                        :class="{ 'bg-indigo-500': activeTab === 'activity', 'bg-transparent': activeTab !== 'activity' }"
                        class="absolute inset-x-0 bottom-0 h-0.5"></span>
                </button>
                <button @click="activeTab = 'settings'"
                    :class="{ 'text-gray-900': activeTab === 'settings', 'text-gray-500 hover:text-gray-700': activeTab !== 'settings' }"
                    class="group relative min-w-0 flex-1 overflow-hidden rounded-r-lg bg-white px-4 py-4 text-center text-sm font-medium hover:bg-gray-50 focus:z-10">
                    <span>Settings</span>
                    <span aria-hidden="true"
                        :class="{ 'bg-indigo-500': activeTab === 'settings', 'bg-transparent': activeTab !== 'settings' }"
                        class="absolute inset-x-0 bottom-0 h-0.5"></span>
                </button>
            </nav>

            <!-- Tab Content -->
            <div class="px-6 py-5">
                <!-- Profile Tab -->
                <div x-show="activeTab === 'profile'" x-transition>
                    <div class="bg-gray-50 p-6 rounded-lg shadow-sm">
                        <h3 class="text-base font-semibold leading-7 text-gray-900">Contact Information</h3>
                        <dl class="mt-3 space-y-5">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Name</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $technician->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Email</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $technician->email }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Phone</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $technician->phone ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                <dd class="mt-1 text-sm">
                                    @if($technician->is_active)
                                    <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">Active</span>
                                    @else
                                    <span class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/20">Inactive</span>
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Business Tab -->
                <div x-show="activeTab === 'business'" x-transition>
                    <div class="bg-gray-50 p-6 rounded-lg shadow-sm">
                        <h3 class="text-base font-semibold leading-7 text-gray-900">Business Information</h3>
                        <dl class="mt-3 space-y-5">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Business Name</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $technician->business->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Business Email</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $technician->business->email }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Business Phone</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $technician->business->phone }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Activity Tab -->
                <div x-show="activeTab === 'activity'" x-transition>
                    <div class="bg-gray-50 p-6 rounded-lg shadow-sm">
                        <h3 class="text-base font-semibold leading-7 text-gray-900">Recent Activity</h3>
                        <div class="mt-3">
                            <p class="text-sm text-gray-500">No recent activity to display.</p>
                        </div>
                    </div>
                </div>

                <!-- Settings Tab -->
                <div x-show="activeTab === 'settings'" x-transition>
                    <div class="bg-gray-50 p-6 rounded-lg shadow-sm">
                        <h3 class="text-base font-semibold leading-7 text-gray-900">Account Settings</h3>
                        <div class="mt-3 space-y-5">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900">Account Status</h4>
                                    <p class="text-sm text-gray-500">Manage technician account status</p>
                                </div>
                                <button type="button" class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                                    Change Status
                                </button>
                            </div>
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900">Password</h4>
                                    <p class="text-sm text-gray-500">Update account password</p>
                                </div>
                                <button type="button" class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                                    Change Password
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
