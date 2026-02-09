<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Resources\Pages\ListRecords;

class ListOrders extends ListRecords
{
    public static $resource = OrderResource::class;
    public static $view = 'filament.resources.orders.list';

    public $startDate;
    public $endDate;

    public function updatedStartDate()
    {
        $this->resetPage();
    }

    public function updatedEndDate()
    {
        $this->resetPage();
    }

    protected function applyFilters($query)
    {
        // Custom Date Filter
        if ($this->startDate && $this->endDate) {
            $query->whereDate('order_date', '>=', $this->startDate)
                  ->whereDate('order_date', '<=', $this->endDate);
        }

        // Standard Filter Logic
        if (
            ! $this->isFilterable() ||
            $this->filter === '' ||
            $this->filter === null
        ) {
            return $query;
        }

        collect($this->getTable()->getFilters())
            ->filter(fn ($filter) => $filter->getName() === $this->filter)
            ->each(function ($filter) use (&$query) {
                $query = $filter->apply($query);
            });

        return $query;
    }

    public function export()
    {
        if (!$this->startDate || !$this->endDate) {
            $this->notify('Please select both Start Date and End Date.');
            return;
        }

        $filename = "Laporan_Bulanan_{$this->startDate}_{$this->endDate}.xlsx";
        
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\MonthlyReportExport($this->startDate, $this->endDate), 
            $filename
        );
    }

    public function print()
    {
        if (!$this->startDate || !$this->endDate) {
            $this->notify('Please select both Start Date and End Date.');
            return;
        }

        return redirect()->route('orders.print', [
            'start' => $this->startDate, 
            'end' => $this->endDate
        ]);
    }
}
