<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Order #{{ $order->id }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }
        .receipt {
            width: 380px;
            margin: 20px auto;
            padding: 10px;
            border: 1px solid #ddd;
            background: #fff;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        .mb-1 { margin-bottom: 4px; }
        .mb-2 { margin-bottom: 8px; }
        .mb-4 { margin-bottom: 16px; }
        .pb-2 { padding-bottom: 8px; }
        .border-b { border-bottom: 1px dashed #000; }
        .flex { display: flex; justify-content: space-between; }
        .text-xs { font-size: 10px; }
        .italic { font-style: italic; }
        
        @media print {
            body { margin: 0; padding: 0; }
            .receipt {
                width: 100%;
                max-width: 80mm; /* Standard receipt width */
                margin: 0;
                border: none;
            }
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="receipt">
        <div class="text-center mb-4">
            <h2 class="font-bold uppercase mb-1">Laundry App</h2>
            <p>Jl. Contoh Laundry No. 123</p>
            <p>Telp: 0812-3456-7890</p>
        </div>

        <div class="mb-2 border-b pb-2">
            <p>No Order: #{{ $order->id }}</p>
            <p>Tanggal: {{ $order->order_date ? $order->order_date->format('d/m/Y H:i') : '-' }}</p>
            <p>Pelanggan: {{ optional($order->customer)->name ?? '-' }}</p>
            <p>Tipe: {{ ucfirst($order->order_type) }}</p>
        </div>

        <div class="mb-2 border-b pb-2">
            @if($order->order_type === 'kiloan')
                @php
                    $pricePerKg = optional($order->serviceType)->price_per_kg ?? 0;
                    $kiloanSubtotal = $order->weight * $pricePerKg;
                @endphp
                <div class="mb-1">
                    <p class="font-bold">{{ optional($order->serviceType)->name ?? 'Service' }}</p>
                    <div class="flex">
                        <span>{{ number_format((float)$order->weight, 2) }} kg x {{ number_format($pricePerKg, 0) }}</span>
                        <span>{{ number_format($kiloanSubtotal, 0) }}</span>
                    </div>
                    
                    @if($order->serviceDuration)
                        <div class="flex text-xs italic">
                            <span>+ {{ $order->serviceDuration->name }}</span>
                            @if($order->serviceDuration->surcharge > 0)
                                <span>{{ number_format($order->serviceDuration->surcharge, 0) }}</span>
                            @endif
                        </div>
                    @endif

                    @if($order->fragrance)
                        <div class="flex text-xs italic">
                            <span>+ {{ $order->fragrance->name }}</span>
                            @if($order->fragrance->price > 0)
                                <span>{{ number_format($order->fragrance->price, 0) }}</span>
                            @endif
                        </div>
                    @endif
                </div>
            @endif

            @if($order->orderItems && $order->orderItems->isNotEmpty())
                @foreach($order->orderItems as $item)
                    <div class="mb-1 flex">
                        <span>{{ optional($item->unitType)->name ?? 'Item' }} x{{ $item->quantity }}</span>
                        <span>{{ number_format($item->subtotal, 0) }}</span>
                    </div>
                @endforeach
            @endif
        </div>

        <div class="mb-2 border-b pb-2">
            {{-- Recalculate Logic to display breakdown correctly --}}
            {{-- We know Total, but explicitly showing Subtotal/Discount/Surcharge is nice --}}
            @php
                 // Approximation for display
                 $total = $order->total_amount;
            @endphp

            @if($order->discount)
                 <div class="flex text-xs">
                    <span>Diskon ({{ $order->discount->name }})</span>
                    {{-- We don't store discount amount in DB, but we can try to infer or just show "Included" if hard --}}
                    {{-- But let's try to calculate if we can --}}
                    @php
                        // If percentage
                        $discountVal = 0;
                        // It's hard to reverse engineer exact amounts without stored fields. 
                        // But we can show the Total as primary.
                    @endphp
                    <span>-</span>
                 </div>
            @endif
            
            <div class="flex font-bold" style="margin-top: 5px; font-size: 14px;">
                <span>TOTAL</span>
                <span>Rp {{ number_format($order->total_amount, 0) }}</span>
            </div>
        </div>

        <div class="text-center mb-4">
            <p>Status: <span class="uppercase font-bold">{{ $order->paid_amount >= $order->total_amount ? 'LUNAS' : 'BELUM LUNAS' }}</span></p>
            @if($order->paid_amount >= $order->total_amount)
                <p>Dibayar: {{ number_format($order->paid_amount, 0) }}</p>
            @elseif($order->paid_amount > 0)
                 <p>Dibayar Sebagian: {{ number_format($order->paid_amount, 0) }}</p>
                 <p>Sisa: {{ number_format($order->total_amount - $order->paid_amount, 0) }}</p>
            @endif
        </div>

        <div class="text-center text-xs">
            <p>Terima kasih atas kepercayaan Anda!</p>
        </div>
        
        <div class="no-print text-center" style="margin-top: 20px;">
             <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer;">Cetak Lagi</button>
             <button onclick="window.close()" style="padding: 10px 20px; cursor: pointer;">Tutup</button>
        </div>
    </div>

</body>
</html>
