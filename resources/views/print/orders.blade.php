<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Orders {{ $start }} - {{ $end }}</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .header { text-align: center; margin-bottom: 30px; }
        .no-print { display: none; }
        @media print {
            .no-print { display: none; }
            button { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h1>Order Report</h1>
        <p>Period: {{ $start }} - {{ $end }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Type</th>
                <th>Status</th>
                <th>Total</th>
                <th>Payment</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
                <tr>
                    <td>{{ $order->id }}</td>
                    <td>{{ optional($order->customer)->name }}</td>
                    <td>{{ ucfirst($order->order_type) }}</td>
                    <td>{{ ucfirst($order->status) }}</td>
                    <td>{{ $order->total_amount_formatted }}</td>
                    <td>{{ ucfirst($order->payment_status) }}</td>
                    <td>{{ $order->order_date->format('d M Y') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            @php
                $omset = $orders->where('payment_status', 'paid')->sum('total_amount');
            @endphp
            <tr>
                <td colspan="4" style="text-align: right; font-weight: bold;">Total Omset (Revenue):</td>
                <td colspan="3" style="font-weight: bold;">Rp {{ number_format($omset, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
