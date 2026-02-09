<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Filament\Roles;
use Filament\Resources\Forms\Components;
use Filament\Resources\Forms\Form;
use Filament\Resources\Resource;
use Filament\Resources\Tables\Columns;
use Filament\Resources\Tables\Filter;
use Filament\Resources\Tables\Table;
use Filament\Resources\Tables\RecordActions\Link;

class OrderResource extends Resource
{
    public static $icon = 'heroicon-o-collection';
    public static $label = 'Pesanan';
    public static $pluralLabel = 'Pesanan';

    public static function form(Form $form)
    {
        return $form
            ->schema([
                Components\Section::make('Detail Pesanan')
                    ->schema([
                        Components\Select::make('customer_id')
                            ->label('Pelanggan')
                            ->options(\App\Models\Customer::all()->pluck('name', 'id'))
                            ->required(),
                        Components\Select::make('order_type')
                            ->label('Tipe Pesanan')
                            ->options([
                                'kiloan' => 'Kiloan',
                                'satuan' => 'Satuan',
                            ])
                            ->default('kiloan')
                            ->required(),
                        Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'pending' => 'Menunggu',
                                'processing' => 'Proses',
                                'ready' => 'Selesai',
                                'completed' => 'Diambil',
                            ])
                            ->default('pending'),
                        Components\Select::make('payment_status')
                            ->label('Status Pembayaran')
                            ->options([
                                'unpaid' => 'Belum Lunas',
                                'paid' => 'Lunas',
                            ])
                            ->default('unpaid')
                            ->disabled(),
                    ]),

                Components\Section::make('Detail Kiloan (Isi jika Tipe Pesanan Kiloan)')
                    ->schema([
                        Components\Select::make('service_duration_id')
                            ->label('Waktu Layanan')
                            ->options(\App\Models\ServiceDuration::pluck('name', 'id'))
                            ->placeholder('Pilih Durasi (mis. Reguler, Ekspres)'),
                        
                        Components\Select::make('service_type_id')
                            ->label('Tipe Layanan')
                            ->options(\App\Models\ServiceType::pluck('name', 'id'))
                            ->placeholder('Pilih Tipe (mis. Cuci Komplit)'),

                        Components\Select::make('fragrance_id')
                            ->label('Pewangi')
                            ->options(\App\Models\Fragrance::pluck('name', 'id'))
                            ->placeholder('Pilih Pewangi'),

                        Components\TextInput::make('weight')
                            ->numeric()
                            ->label('Berat (kg)'),
                    ]),

                Components\Section::make('Keuangan')
                    ->schema([
                        Components\Select::make('discount_id')
                            ->label('Diskon')
                            ->options(\App\Models\Discount::pluck('name', 'id')),
                        
                        Components\TextInput::make('total_amount')
                            ->label('Total Biaya (Rp)')
                            ->numeric()
                            ->required(),

                        Components\TextInput::make('paid_amount')
                             ->label('Jumlah Bayar (Rp)')
                             ->numeric()
                             ->default(0),

                        Components\Textarea::make('notes')
                             ->label('Catatan')
                             ->rows(3),
                    ]),
            ]);
    }

    public static function table(Table $table)
    {
        return $table
            ->columns([
                Columns\Text::make('id')->label('ID')->sortable()->searchable(),
                Columns\Text::make('customer.name')->searchable()->label('Pelanggan'),
                Columns\Text::make('customer.phone')->label('Telepon'),
                Columns\Text::make('customer.address')->label('Alamat')->limit(30),
                Columns\Text::make('items_summary')->label('Item')->limit(50),
                Columns\Text::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Menunggu',
                        'processing' => 'Proses',
                        'ready' => 'Selesai',
                        'completed' => 'Diambil',
                    ]),
                Columns\Text::make('payment_status')
                    ->label('Status Pembayaran')
                    ->options([
                        'unpaid' => 'Belum Lunas',
                        'paid' => 'Lunas',
                    ]),
                Columns\Text::make('notes')->label('Catatan')->limit(20),
                Columns\Text::make('discount.name')->label('Diskon'),
                Columns\Text::make('total_amount_formatted')->label('Total'),
                Columns\Text::make('paid_amount_formatted')->label('Dibayar'),
                Columns\Text::make('order_date')->label('Tanggal')->date(),
            ])
            ->filters([
                Filter::make('active', fn ($query) => $query->where('status', '!=', 'completed')),
            ])
            ->recordActions([
                // Update to Selesai
                Link::make('mark_ready')
                    ->label('Selesai')
                    ->url(fn ($record) => route('filament.resources.orders.mark_ready', $record))
                    ->when(fn ($record) => in_array($record->status, ['pending', 'processing'])),
                
                // Update to Completed
                Link::make('mark_completed')
                    ->label('Ambil')
                    ->url(fn ($record) => route('filament.resources.orders.mark_completed', $record))
                    ->when(fn ($record) => $record->status === 'ready'),

                // Mark Paid
                Link::make('mark_paid')
                    ->label('Bayar Lunas')
                    ->url(fn ($record) => route('filament.resources.orders.mark_paid', $record))
                    ->when(fn ($record) => $record->payment_status === 'unpaid'),

                Link::make('edit')
                    ->label('Lihat/Edit')
                    ->url(fn ($record) => static::generateUrl('edit', ['record' => $record])),

                Link::make('print_receipt')
                    ->label('Cetak Struk')
                    ->url(fn ($record) => route('orders.receipt', $record))
                    ->icon('heroicon-o-printer'),
            ]);
    }

    public static function relations()
    {
        return [
            RelationManagers\ItemsRelationManager::class,
        ];
    }

    public static function routes()
    {
        return [
            Pages\ListOrders::routeTo('/', 'index'),
            Pages\CreateOrder::routeTo('/create', 'create'),
            Pages\EditOrder::routeTo('/{record}/edit', 'edit'),
        ];
    }
}
