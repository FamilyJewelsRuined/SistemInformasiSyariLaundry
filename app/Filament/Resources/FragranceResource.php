<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FragranceResource\Pages;
use App\Filament\Resources\FragranceResource\RelationManagers;
use App\Filament\Roles;
use Filament\Resources\Forms\Components;
use Filament\Resources\Forms\Form;
use Filament\Resources\Resource;
use Filament\Resources\Tables\Columns;
use Filament\Resources\Tables\Filter;
use Filament\Resources\Tables\Table;

class FragranceResource extends Resource
{
    public static $icon = 'heroicon-o-sparkles';
    public static $label = 'Pewangi';
    public static $pluralLabel = 'Pewangi';

    public static function form(Form $form)
    {
        return $form
            ->schema([
                Components\TextInput::make('name')->label('Nama')->required(),
                Components\TextInput::make('price')->label('Harga')->numeric()->default(0),
            ]);
    }

    public static function table(Table $table)
    {
        return $table
            ->columns([
                Columns\Text::make('name')->label('Nama'),
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
            Pages\ListFragrances::routeTo('/', 'index'),
            Pages\CreateFragrance::routeTo('/create', 'create'),
            Pages\EditFragrance::routeTo('/{record}/edit', 'edit'),
        ];
    }
}
