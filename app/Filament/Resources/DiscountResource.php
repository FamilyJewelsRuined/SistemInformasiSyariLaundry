<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DiscountResource\Pages;
use App\Filament\Resources\DiscountResource\RelationManagers;
use App\Filament\Roles;
use Filament\Resources\Forms\Components;
use Filament\Resources\Forms\Form;
use Filament\Resources\Resource;
use Filament\Resources\Tables\Columns;
use Filament\Resources\Tables\Filter;
use Filament\Resources\Tables\Table;

class DiscountResource extends Resource
{
    public static $icon = 'heroicon-o-currency-dollar';
    public static $label = 'Diskon';
    public static $pluralLabel = 'Diskon';

    public static function form(Form $form)
    {
        return $form
            ->schema([
                Components\TextInput::make('name')->label('Nama')->required(),
                Components\Select::make('type')
                    ->label('Tipe')
                    ->options([
                        'fixed' => 'Tetap (Rp)',
                        'percent' => 'Persentase (%)',
                    ])
                    ->required(),
                Components\TextInput::make('value')->label('Nilai')->numeric()->required(),
            ]);
    }

    public static function table(Table $table)
    {
        return $table
            ->columns([
                Columns\Text::make('name')->label('Nama'),
                Columns\Text::make('type')->label('Tipe'),
                Columns\Text::make('value')->label('Nilai'),
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
            Pages\ListDiscounts::routeTo('/', 'index'),
            Pages\CreateDiscount::routeTo('/create', 'create'),
            Pages\EditDiscount::routeTo('/{record}/edit', 'edit'),
        ];
    }
}
