<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExpenseResource\Pages;
use App\Filament\Resources\ExpenseResource\RelationManagers;
use App\Filament\Roles;
use Filament\Resources\Forms\Components;
use Filament\Resources\Forms\Form;
use Filament\Resources\Resource;
use Filament\Resources\Tables\Columns;
use Filament\Resources\Tables\Filter;
use Filament\Resources\Tables\Table;

class ExpenseResource extends Resource
{
    public static $icon = 'heroicon-o-collection';

    public static $label = 'Pengeluaran';
    public static $pluralLabel = 'Pengeluaran';

    public static function form(Form $form)
    {
        return $form
            ->schema([
                Components\DatePicker::make('entry_date')
                    ->label('Tanggal')
                    ->required(),
                Components\TextInput::make('item_name')
                    ->label('Nama Item')
                    ->required(),
                Components\TextInput::make('quantity')
                    ->label('Jumlah (Qty)')
                    ->numeric()
                    ->required(),
                Components\TextInput::make('price')
                    ->label('Harga Satuan')
                    ->numeric()
                    ->required(),
                Components\TextInput::make('total_amount')
                    ->label('Total')
                    ->numeric()
                    ->disabled()
                    ->helpMessage('Akan dihitung otomatis saat disimpan'),
            ]);
    }

    public static function table(Table $table)
    {
        return $table
            ->columns([
                Columns\Text::make('entry_date')->label('Tanggal')->date()->sortable(),
                Columns\Text::make('item_name')->label('Item')->searchable(),
                Columns\Text::make('quantity')->label('Qty'),
                Columns\Text::make('price')->label('Harga')
                    ->formatUsing(fn ($value) => 'Rp ' . number_format($value, 0, ',', '.')),
                Columns\Text::make('total_amount')->label('Total')
                     ->formatUsing(fn ($value) => 'Rp ' . number_format($value, 0, ',', '.')),
            ])
            ->filters([
                //
            ]);
    }

    public static function relations()
    {
        return [
            //
        ];
    }

    public static function routes()
    {
        return [
            Pages\ListExpenses::routeTo('/', 'index'),
            Pages\CreateExpense::routeTo('/create', 'create'),
            Pages\EditExpense::routeTo('/{record}/edit', 'edit'),
        ];
    }
}
