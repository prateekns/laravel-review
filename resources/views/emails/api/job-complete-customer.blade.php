@extends('emails.layouts.job')

@section('content')
<table border="0" style="width:100%" aria-describedby="JobCompletionCustomerEmail">
    <thead>
            <tr><th></th></tr>
    </thead>
    <tr><td style="width: 100%;height:24px"></td></tr>
    <tr>
        <td style="background-color: #F3F4F6; padding: 32px; font-family: Arial, sans-serif; color: #000000; border-radius: 8px;">

            <p style="line-height:20px; font-size: 16px;font-weight:700">Hi {{ $workOrder->customer->customer_name }}</p>
            <div style="font-size: 16px;font-weight:400">{{ $workOrder->customer->address }}</div>
            @if($workOrder->customer->street)
                <div style="line-height:20px; font-size: 16px;font-weight:400">{{ $workOrder->customer->street }}</div>
            @endif
            <div style="line-height:20px; font-size: 16px;font-weight:400">{{ $workOrder->customer->city_name . ', ' . $workOrder->customer->state_name . ' ' . $workOrder->customer->zip_code }}</div>
            <p style="font-size: 16px;font-weight:400">This is to inform you that your pool service has been successfully completed.</p>
            <p style="font-size: 16px;font-weight:400"><strong style="line-height:20px; font-size: 16px;font-weight:700">Technician:</strong> {{ $workOrder->technician->fullName }}</p>
            <p style="font-size: 16px;font-weight:400"><strong style="line-height:20px; font-size: 16px;font-weight:700">Completion Date:</strong> {{ $jobCompletedAt }}</p>
           
            <!-- Communication Notes -->
            @if($customer_message)
                <h4 style="margin: 20px 0 10px 0; line-height:20px; font-size: 16px;font-weight:700; font-family: Arial, sans-serif;">Job Communication Notes</h4>
                <p style="margin: 0 0 20px 0; font-size: 14px;">{!! nl2br(e($customer_message)) !!}</p>
            @endif
            
            <!-- Job Images -->
            @if($attachment)
                <h4 style="margin: 20px 0 10px 0; line-height:20px; font-size: 16px;font-weight:700; font-family: Arial, sans-serif;">Job Images</h4>
                
                @if(isset($attachment['customer_image_1']))
                    <a href="{{ $attachment['customer_image_1'] }}" target="_blank" style="display: inline-block; margin-right: 10px;">
                        <img src="{{ $attachment['customer_image_1_thumb'] }}" alt="Completed workorder uploaded by technician" style="border-radius: 50%; width: 60px; height: 60px;">
                    </a>
                @endif
                
                @if(isset($attachment['customer_image_2']))
                    <a href="{{ $attachment['customer_image_2'] }}" target="_blank" style="display: inline-block;">
                        <img src="{{ $attachment['customer_image_2_thumb'] }}" alt="Completed workorder uploaded by technician" style="border-radius: 50%; width: 60px; height: 60px;">
                    </a>
                @endif
            @endif

            @if($workOrder->type == 'MO' && $workOrder->chemicalLogs->isNotEmpty() && $chemicalLogs->isNotEmpty())
       
                <h4 style="margin: 20px 0 10px 0; line-height:20px; font-size: 16px;font-weight:700; font-family: Arial, sans-serif;">Chemicals Used</h4>
                <!-- Chemicals Used -->
                <table style="width: 100%;border-collapse: collapse;" aria-describedby="Checmical List">
                    <thead>
                        <tr>
                            <th style="border: 1px solid #333;padding: 8px;text-align: left;background-color: #f2f2f2;">Chemical Name</th>
                            <th style="border: 1px solid #333;padding: 8px;text-align: left;background-color: #f2f2f2;">Pool Reading</th>
                            <th style="border: 1px solid #333;padding: 8px;text-align: left;background-color: #f2f2f2;">Added Qty</th>
                            <th style="border: 1px solid #333;padding: 8px;text-align: left;background-color: #f2f2f2;">Chemical Added</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($chemicalLogs as $chemical)
                        @if($chemical->qty_added > 0)
                            <tr>
                                <td style="border: 1px solid #333;padding: 8px;text-align: left;">{{ $chemical->chemical_name }}</td>
                                <td style="border: 1px solid #333;padding: 8px;text-align: left;">{{ $chemical->reading }} {{ ($chemical->chemical_name != 'pH') ? $chemical->unit : '' }}</td>
                                <td style="border: 1px solid #333;padding: 8px;text-align: left;">{{ $chemical->qty_added }}  {{ $chemical->chemical_used_unit }}</td>
                                <td style="border: 1px solid #333;padding: 8px;text-align: left;">
                                    {{ $chemical->chemical_used }}
                                    @if($chemical->tabs > 0)
                                        <p> Tab-{{$chemical->tabs}}</p>
                                    @endif
                                </td>
                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
            @endif
        </td>
    </tr>
</table>
@endsection
