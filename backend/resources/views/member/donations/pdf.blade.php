<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Donations Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 10px;
        }
        .header h1 {
            color: #4CAF50;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .summary {
            background-color: #f5f5f5;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .summary-item {
            display: inline-block;
            margin-right: 30px;
        }
        .summary-label {
            font-weight: bold;
            color: #666;
        }
        .summary-value {
            color: #4CAF50;
            font-size: 16px;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table thead {
            background-color: #4CAF50;
            color: white;
        }
        table th, table td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        .status-pending { background-color: #FFC107; color: #000; }
        .status-succeeded { background-color: #4CAF50; color: white; }
        .status-failed { background-color: #F44336; color: white; }
        .status-refunded { background-color: #9E9E9E; color: white; }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .amount {
            font-weight: bold;
            color: #4CAF50;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Donations Report</h1>
        <p>Tounsi-Vert Platform</p>
        <p>Generated on: {{ date('F d, Y h:i A') }}</p>
    </div>

    <div class="summary">
        <div class="summary-item">
            <span class="summary-label">Total Donations:</span>
            <span class="summary-value">{{ $donations->count() }}</span>
        </div>
        <div class="summary-item">
            <span class="summary-label">Total Amount:</span>
            <span class="summary-value">{{ number_format($totalAmount, 2) }} TND</span>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Date</th>
                <th>Organization</th>
                <th>Event</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Payment Ref</th>
            </tr>
        </thead>
        <tbody>
            @foreach($donations as $donation)
                <tr>
                    <td>#{{ $donation->id }}</td>
                    <td>{{ $donation->created_at->format('M d, Y') }}</td>
                    <td>
                        @if($donation->organization)
                            {{ $donation->organization->name }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td>
                        @if($donation->event)
                            {{ Str::limit($donation->event->title, 30) }}
                        @else
                            General
                        @endif
                    </td>
                    <td class="amount">{{ number_format($donation->amount, 2) }} TND</td>
                    <td>
                        @php
                            $statusClass = 'status-' . $donation->status;
                        @endphp
                        <span class="status-badge {{ $statusClass }}">
                            {{ strtoupper($donation->status) }}
                        </span>
                    </td>
                    <td>
                        @if($donation->payment_ref)
                            {{ $donation->payment_ref }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #e8f5e9;">
                <td colspan="4" style="text-align: right; font-weight: bold;">TOTAL:</td>
                <td colspan="3" class="amount" style="font-size: 14px;">
                    {{ number_format($totalAmount, 2) }} TND
                </td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>This is an automated report generated by Tounsi-Vert Platform.</p>
        <p>&copy; {{ date('Y') }} Tounsi-Vert. All rights reserved.</p>
    </div>
</body>
</html>
