<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Sheets\OrdersSheet;
use App\Exports\Sheets\ExpensesSheet;

class MonthlyReportExport implements WithMultipleSheets
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function sheets(): array
    {
        return [
            new OrdersSheet($this->startDate, $this->endDate),
            new ExpensesSheet($this->startDate, $this->endDate),
        ];
    }
}
