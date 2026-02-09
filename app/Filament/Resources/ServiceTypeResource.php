<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceTypeResource\Pages;
use App\Filament\Resources\ServiceTypeResource\RelationManagers;
use App\Filament\Roles;
use Filament\Resources\Forms\Components;
use Filament\Resources\Forms\Form;
use Filament\Resources\Resource;
use Filament\Resources\Tables\Columns;
use Filament\Resources\Tables\Filter;
use Filament\Resources\Tables\Table;

class ServiceTypeResource extends Resource
{
    public static $label = 'Tipe Layanan';
    public static $pluralLabel = 'Tipe Layanan';
    public static $icon = 'heroicon-o-tag';

    public static function form(Form $form)
    {
        return $form
            ->schema([
                Components\TextInput::make('name')
                    ->label('Nama')
                    ->required()
                    ->placeholder('mis. Cuci, Setrika'),
                Components\TextInput::make('price_per_kg')
                    ->label('Harga per Kg')
                    ->numeric()
                    ->required(),
            ]);
    }

    public static function table(Table $table)
    {
        return $table
            ->columns([
                Columns\Text::make('name')->label('Nama')->sortable()->searchable(),
                Columns\Text::make('price_per_kg_formatted')->label('Harga per Kg'),
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
            Pages\ListServiceTypes::routeTo('/', 'index'),
            Pages\CreateServiceType::routeTo('/create', 'create'),
            Pages\EditServiceType::routeTo('/{record}/edit', 'edit'),
        ];
    }
}
