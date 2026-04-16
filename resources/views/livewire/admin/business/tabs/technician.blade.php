<div class="bg-white shadow-sm sm:rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <div class=" sm:flex sm:items-center">
            <div class="sm:flex-auto">
                <h3 class="text-lg font-semibold text-gray-900">{{ __('admin.business.technicians') }}</h3>
            </div>
        </div>
    
        <div class="mt-5">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200" aria-describedby="Technician List">
                    <thead >
                        <tr>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">{{ __('admin.technician.name') }}</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">{{ __('admin.technician.email') }}</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">{{ __('admin.technician.skill_type') }}</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">{{ __('admin.status') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($technicians as $technician)
                        <tr>
                            <td class="px-3 py-3.5 text-left break-words max-w-[100px] whitespace-wrap text-sm font-medium text-gray-500">{{ $technician->first_name }} {{ $technician->last_name }}</td>
                            <td class="px-3 py-3.5 text-left break-words max-w-[100px] whitespace-wrap">{{ $technician->email }}</td>
                            <td class="px-3 py-3.5 text-left break-words max-w-[100px] whitespace-wrap">
                                {{ $technician->skills->pluck('skill_type')->implode(', ') ?: __('admin.technician.no_skill') }}
                            </td>
                            <td class="px-3 py-3.5 text-left break-words max-w-[100px] whitespace-wrap">
                                <span class="inline-flex items-center rounded-md bg-{{$technician->status?'green':'red'}}-50 px-2 py-1 text-xs font-medium text-{{$technician->status?'green':'red'}}-700 ring-1 ring-inset ring-{{$technician->status?'green':'red'}}-600/20">
                                    {{ $technician->status ? __('admin.active') : __('admin.inactive')}}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-4 text-center text-gray-500">
                                {{$error ?  __('admin.message.technician_load_fail') :  __('admin.table.no_technicians_found') }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- Footer -->
                 @if($technicians)
                <div class="pagination-box">
                    <x-table-pagination :list="$technicians"/>
                </div>
                @endif
                <!-- Footer Ends -->
            </div>
        </div>
    </div>
</div>
