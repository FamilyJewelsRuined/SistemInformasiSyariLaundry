<?php

namespace App\Filament\Resources\ExpenseResource\Pages;

use App\Filament\Resources\ExpenseResource;
use Filament\Resources\Pages\ListRecords;

class ListExpenses extends ListRecords
{
    public static $resource = ExpenseResource::class;
}
