<div class="bg-white shadow-sm sm:rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg font-medium leading-6 text-gray-900">{{ __('Sub Admins') }}</h3>
        <div class="mt-5">
            <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" aria-describedby="SubAdmin List">
                <thead>
                    <tr>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">{{ __('admin.business.sub_admin_name') }}</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">{{ __('admin.business.sub_admin_email') }}</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">{{ __('admin.business.created_on') }}</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">{{ __('admin.status') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                  @forelse($subAdmins as $subAdmin)
                    <tr>
                        <td class="px-3 py-3.5 text-left break-words max-w-[100px] whitespace-wrap text-sm font-medium text-gray-500">{{ $subAdmin->first_name }} {{ $subAdmin->last_name }}</td>
                        <td class="px-3 py-3.5 text-left break-words max-w-[100px] whitespace-wrap text-sm font-medium text-gray-500">{{ $subAdmin->email }}</td>
                        <td class="px-3 py-3.5 text-left break-words max-w-[100px] whitespace-wrap text-sm font-medium text-gray-500">{{ $subAdmin->created_date}}</td>
                        <td class="px-3 py-3.5 text-left break-words max-w-[100px] whitespace-wrap text-sm font-medium text-gray-500">
                            <span class="inline-flex items-center rounded-md bg-{{$subAdmin->status?'green':'red'}}-50 px-2 py-1 text-xs font-medium text-{{$subAdmin->status?'green':'red'}}-700 ring-1 ring-inset ring-{{$subAdmin->status?'green':'red'}}-600/20">
                                {{ $subAdmin->status ? __('admin.active') : __('admin.inactive')}}
                            </span>
                        </td>
                    </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-4 text-center text-gray-500">
                                {{$error ?  __('admin.message.business_sub_admin_load_fail') :  __('admin.business.no_sub_admins_found') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Footer -->
             @if($subAdmins)
                <div class="pagination-box">
                    <x-table-pagination :list="$subAdmins"/>
                </div>
            @endif
            <!-- Footer Ends -->

        </div>
        </div>
    </div>
</div>
