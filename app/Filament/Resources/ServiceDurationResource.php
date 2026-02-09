<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceDurationResource\Pages;
use App\Filament\Resources\ServiceDurationResource\RelationManagers;
use App\Filament\Roles;
use Filament\Resources\Forms\Components;
use Filament\Resources\Forms\Form;
use Filament\Resources\Resource;
use Filament\Resources\Tables\Columns;
use Filament\Resources\Tables\Filter;
use Filament\Resources\Tables\Table;

class ServiceDurationResource extends Resource
{
    public static $label = 'Waktu Layanan';
    public static $pluralLabel = 'Waktu Layanan';
    public static $icon = 'heroicon-o-clock';

    public static function form(Form $form)
    {
        return $form
            ->schema([
                Components\TextInput::make('name')
                    ->label('Nama')
                    ->required()
                    ->placeholder('mis. Reguler, Ekspres'),
                Components\TextInput::make('description')
                    ->label('Deskripsi')
                    ->placeholder('mis. Maks 72 jam'),
                Components\TextInput::make('surcharge')
                    ->label('Biaya Tambahan')
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table)
    {
        return $table
            ->columns([
                Columns\Text::make('name')->label('Nama')->sortable()->searchable(),
                Columns\Text::make('description')->label('Deskripsi'),
                Columns\Text::make('surcharge_formatted')->label('Biaya Tambahan'),
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
            Pages\ListServiceDurations::routeTo('/', 'index'),
            Pages\CreateServiceDuration::routeTo('/create', 'create'),
            Pages\EditServiceDuration::routeTo('/{record}/edit', 'edit'),
        ];
    }
}
