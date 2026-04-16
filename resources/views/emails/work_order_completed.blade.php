<!DOCTYPE html>
<html lang="en">
<head>
    <title>Work Order Completed</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #0066cc;
            margin-bottom: 10px;
        }
        .content {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
        }
        .section {
            margin-bottom: 20px;
        }
        .section h2 {
            color: #0066cc;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #777;
        }
        .info-row {
            margin-bottom: 8px;
        }
        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 130px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Work Order Completed</h1>
        <p>This email confirms that your service has been completed.</p>
    </div>

    <div class="content">
        <div class="section">
            <h2>Work Order Details</h2>
            <div class="info-row">
                <span class="info-label">Work Order ID:</span> {{ $workOrder->work_order_id }}
            </div>
            <div class="info-row">
                <span class="info-label">Completed Date:</span> {{ now()->format('F j, Y') }}
            </div>
        </div>

        <div class="section">
            <h2>Service Information</h2>
            <div class="info-row">
                <span class="info-label">Task:</span> {{ $workOrder->service_type ?? 'N/A' }}
            </div>
            <div class="info-row">
                <span class="info-label">Description:</span> {{ $workOrder->description ?? 'N/A' }}
            </div>
            @if($notes)
            <div class="info-row">
                <span class="info-label">Completion Notes:</span> {{ $notes }}
            </div>
            @endif
        </div>
        <div class="section">
            <h2>Service Location</h2>
            <div class="info-row">
                <span class="info-label">Address:</span>
                {{ $client ? $client->street . ', ' : '' }}
                {{ $client && $client->city ? $client->city->name . ', ' : '' }}
                {{ $client && $client->state ? $client->state->name . ' ' : '' }}
                {{ $client ? $client->zip_code : '' }}
            </div>
        </div>

        <div class="section">
            <h2>Technician Information</h2>
            <div class="info-row">
                <span class="info-label">Technician:</span> {{ $technician ? $technician->name : 'N/A' }}
            </div>
        </div>

        
    </div>

    <div class="footer">
        <p>Thank you for choosing our services. If you have any questions, please contact us.</p>
    </div>
</body>
</html>
