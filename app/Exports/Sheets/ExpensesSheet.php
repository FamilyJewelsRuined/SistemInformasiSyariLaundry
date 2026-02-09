<?php

namespace App\Exports\Sheets;

use App\Models\Expense;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExpensesSheet implements FromCollection, WithHeadings, ShouldAutoSize, WithTitle, WithStyles
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
        $expenses = Expense::whereDate('entry_date', '>=', $this->startDate)
            ->whereDate('entry_date', '<=', $this->endDate)
            ->orderBy('entry_date')
            ->get();

        $data = $expenses->map(function ($expense) {
            return [
                'Tanggal' => $expense->entry_date ? $expense->entry_date->format('Y-m-d') : '',
                'Item' => $expense->item_name,
                'Qty' => $expense->quantity,
                'Harga' => $expense->price,
                'Total' => $expense->total_amount,
            ];
        });

        // Add Total Row
        $totalSum = $expenses->sum('total_amount');
        
        $data->push([
            'Tanggal' => '',
            'Item' => '',
            'Qty' => '',
            'Harga' => 'Total Pengeluaran:',
            'Total' => $totalSum,
        ]);

        return $data;
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Item',
            'Qty',
            'Harga',
            'Total'
        ];
    }

    public function title(): string
    {
        return 'Pengeluaran';
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
