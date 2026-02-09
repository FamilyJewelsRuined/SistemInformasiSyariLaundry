<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Filament\Roles;
use Filament\Resources\Forms\Components;
use Filament\Resources\Forms\Form;
use Filament\Resources\Resource;
use Filament\Resources\Tables\Columns;
use Filament\Resources\Tables\Filter;
use Filament\Resources\Tables\Table;

class CustomerResource extends Resource
{
    public static $icon = 'heroicon-o-collection';

    public static $label = 'Pelanggan';
    public static $pluralLabel = 'Pelanggan';

    public static function form(Form $form)
    {
        return $form
            ->schema([
                Components\TextInput::make('name')
                    ->label('Nama')
                    ->required(),
                Components\TextInput::make('phone')
                    ->label('Telepon'),
                Components\Textarea::make('address')
                    ->label('Alamat'),
            ]);
    }

    public static function table(Table $table)
    {
        return $table
            ->columns([
                Columns\Text::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                Columns\Text::make('phone')
                    ->label('Telepon')
                    ->searchable(),
                Columns\Text::make('address')
                    ->label('Alamat'),
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
            Pages\ListCustomers::routeTo('/', 'index'),
            Pages\CreateCustomer::routeTo('/create', 'create'),
            Pages\EditCustomer::routeTo('/{record}/edit', 'edit'),
        ];
    }
}
