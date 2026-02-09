<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Resources\Forms\Components;
use Filament\Resources\Forms\Form;
use Filament\Resources\RelationManager;
use Filament\Resources\Tables\Columns;
use Filament\Resources\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    public static $relationship = 'orderItems';

    public static $icon = 'heroicon-o-collection';

    public static function form(Form $form)
    {
        return $form
            ->schema([
                Components\Select::make('unit_type_id')
                    ->options(\App\Models\UnitType::pluck('name', 'id'))
                    ->required(),
                Components\TextInput::make('quantity')
                    ->numeric()
                    ->required(),
                Components\TextInput::make('unit_price')
                    ->numeric()
                    ->required(),
                Components\TextInput::make('subtotal')
                    ->numeric()
                    ->required(),
            ]);
    }

    public static function table(Table $table)
    {
        return $table
            ->columns([
                Columns\Text::make('unitType.name')->label('Item'),
                Columns\Text::make('quantity'),
                Columns\Text::make('unit_price_formatted')->label('Unit Price'),
                Columns\Text::make('subtotal_formatted')->label('Subtotal'),
            ])
            ->filters([
                //
            ]);
    }
}
