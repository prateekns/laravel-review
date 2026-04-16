<div class="bg-white shadow-sm sm:rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg font-medium leading-6 text-gray-900">{{ __('admin.business.customers') }}</h3>
        <div class="mt-5">
            <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" aria-describedby="Customer List">
                <thead >
                    <tr>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">{{ __('admin.business.customer_name') }}</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">{{ __('admin.business.customer_email') }}</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">{{ __('admin.business.customer_status') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($customers as $customer)
                        <tr>
                            <td class="px-3 py-3.5 break-words max-w-[100px] whitespace-wrap text-sm font-medium text-gray-500">{{ $customer->first_name }} {{ $customer->last_name }}</td>
                            <td class="email-td py-3.5 text-left text-sm break-words max-w-[100px] whitespace-wrap">{{ $customer->email_1 }}</td>
                            <td class="status-td py-3.5 text-left text-sm">
                            <span class="inline-flex items-center rounded-md bg-{{$customer->status?'green':'red'}}-50 px-2 py-1 text-xs font-medium text-{{$customer->status?'green':'red'}}-700 ring-1 ring-inset ring-{{$customer->status?'green':'red'}}-600/20">
                                {{ $customer->status ? __('admin.active') : __('admin.inactive')}}
                            </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-4 text-center text-gray-500">
                                {{ $error ? __('admin.message.customers_load_fail') : __('admin.business.no_customers_found') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Footer -->
             @if($customers)
                <div class="pagination-box">
                    <x-table-pagination :list="$customers"/>
                </div>
            @endif
            <!-- Footer Ends -->
        </div>
        </div>
    </div>
</div>
