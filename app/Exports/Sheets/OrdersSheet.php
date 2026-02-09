<?php

namespace App\Exports\Sheets;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OrdersSheet implements FromCollection, WithHeadings, ShouldAutoSize, WithTitle, WithStyles
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        $orders = Order::with(['customer', 'serviceType', 'serviceDuration', 'orderItems.unitType'])
            ->whereDate('order_date', '>=', $this->startDate)
            ->whereDate('order_date', '<=', $this->endDate)
            ->orderBy('order_date')
            ->get();

        // Calculate Total Omset (Revenue from Paid Orders only)
        $totalOmset = $orders->filter(function ($order) {
            // Check if paid
            return $order->paid_amount >= $order->total_amount && $order->total_amount > 0;
        })->sum('total_amount');

        $data = $orders->map(function ($order) {
            // 1. Tanggal
            $date = $order->order_date ? $order->order_date->format('Y-m-d') : '';

            // 2. Nama
            $name = $order->customer ? $order->customer->name : '-';

            // 3. Item (Match DataTables items_summary)
            $item = $order->items_summary;

            // 4. Layanan
            $service = '-';
            if ($order->serviceType) {
                $service = $order->serviceType->name;
                if ($order->serviceDuration) {
                    $service .= ' (' . $order->serviceDuration->name . ')';
                }
            } elseif ($order->orderItems->isNotEmpty()) {
                // For Satuan, list distinct unit names if needed, or just rely on items_summary for details
                // The prompt says "Rename Kg/Jumlah to Item". 
                // "Layanan" (Service) was previously requested.
                // We keep Layanan logic as generic service description.
                 $service = $order->orderItems->map(function($item) {
                     return $item->unitType ? $item->unitType->name : '';
                 })->filter()->unique()->implode(', ');
            }

            // 5. Status Pembayaran
            $paymentStatus = 'Belum Lunas';
            if ($order->paid_amount >= $order->total_amount && $order->total_amount > 0) {
                $paymentStatus = 'Lunas';
            }

            // 6. Total
            $total = $order->total_amount;

            return [
                'Tanggal' => $date,
                'Nama' => $name,
                'Item' => $item,
                'Layanan' => $service,
                'Status Pembayaran' => $paymentStatus,
                'Total' => $total,
            ];
        });

        // Append Total Omset Row
        $data->push([
            'Tanggal' => '',
            'Nama' => '',
            'Item' => '',
            'Layanan' => '',
            'Status Pembayaran' => 'Total Omset',
            'Total' => $totalOmset,
        ]);

        return $data;
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Nama',
            'Item',
            'Layanan',
            'Status Pembayaran',
            'Total'
        ];
    }

    public function title(): string
    {
        return 'Orders';
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        return [
            1 => ['font' => ['bold' => true]],
            $lastRow => ['font' => ['bold' => true]],
        ];
    }
}
