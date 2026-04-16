<div class="bg-white shadow-sm sm:rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <div class=" sm:flex sm:items-center">
            <div class="sm:flex-auto">
                <h3 class="text-lg font-medium leading-6 text-gray-900">{{ __('admin.business.billing_history') }}</h3>
            </div>
        </div>

        
        <x-loading :target="'gotoPage,nextPage,previousPage'"/>

        <div class="mt-5">
            <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" aria-describedby="Billing List">
                <thead >
                    <tr>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">{{ __('admin.business.invoice_number') }}</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">{{ __('admin.business.plan') }}</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">{{ __('admin.business.invoice_type') }}</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">{{ __('admin.business.amount_paid') }}</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">{{ __('admin.business.created_on') }}</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">{{ __('admin.business.billing_period') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($invoices as $invoice)
                        <tr>
                            <td class="px-3 py-3.5 text-left break-words max-w-[100px] whitespace-wrap text-sm font-medium text-gray-900">{{ $invoice->invoice_number }}</td>
                            <td  class="px-3 py-3.5 text-left break-words max-w-[100px] whitespace-wrap">
                                <div class="flex flex-col">
                                    <span>{{ __('admin.business.admin') }}: {{ $invoice->invoice_type==='recurring' ? $invoice->order?->total_admin : $invoice->order?->admin_qty_change}}</span>
                                    <span>{{ __('admin.business.technician') }}: {{ $invoice->invoice_type==='recurring' ? $invoice->order?->total_technician : $invoice->order?->technician_qty_change }}</span>
                                
                                </div>
                            </td>
                            <td class="px-3 py-3.5 text-left break-words max-w-[100px] whitespace-wrap">{{ ucfirst($invoice->invoice_type) }}</td>
                            <td  class="px-3 py-3.5 text-left break-words max-w-[100px] whitespace-wrap">{{ $invoice->invoice_amount }}</td>
                            <td class="px-3 py-3.5 text-left break-words max-w-[100px] whitespace-wrap">{{ $invoice->created_at }}</td>
                            <td class="px-3 py-3.5 text-left break-words max-w-[100px] whitespace-wrap">
                                {{ $invoice->billing_period }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-4 text-center text-gray-500">
                                {{ $error ? __('admin.message.invoice_load_fail') : __('admin.message.no_billing_history') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Footer -->
            @if($invoices)
                <div class="pagination-box">
                    <x-table-pagination :list="$invoices"/>
                </div>
            @endif
            <!-- Footer Ends -->
        </div>
        </div>
    </div>
</div>
