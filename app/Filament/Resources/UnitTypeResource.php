<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UnitTypeResource\Pages;
use App\Filament\Resources\UnitTypeResource\RelationManagers;
use App\Filament\Roles;
use Filament\Resources\Forms\Components;
use Filament\Resources\Forms\Form;
use Filament\Resources\Resource;
use Filament\Resources\Tables\Columns;
use Filament\Resources\Tables\Filter;
use Filament\Resources\Tables\Table;

class UnitTypeResource extends Resource
{
    public static $label = 'Tipe Satuan';
    public static $pluralLabel = 'Tipe Satuan';
    public static $icon = 'heroicon-o-cube';

    public static function form(Form $form)
    {
        return $form
            ->schema([
                Components\TextInput::make('name')
                    ->label('Nama')
                    ->required()
                    ->placeholder('mis. Sepatu, Karpet'),
                Components\Select::make('measure_mode')
                    ->label('Mode Pengukuran')
                    ->options([
                        'quantity' => 'Per Fisik',
                        'meter' => 'Per Meter',
                    ])
                    ->required(),
                Components\TextInput::make('price')
                    ->label('Harga')
                    ->numeric()
                    ->required(),
            ]);
    }

    public static function table(Table $table)
    {
        return $table
            ->columns([
                Columns\Text::make('name')->label('Nama')->sortable()->searchable(),
                Columns\Text::make('measure_mode')->label('Mode'),
                Columns\Text::make('price_formatted')->label('Harga'),
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
            Pages\ListUnitTypes::routeTo('/', 'index'),
            Pages\CreateUnitType::routeTo('/create', 'create'),
            Pages\EditUnitType::routeTo('/{record}/edit', 'edit'),
        ];
    }
}
