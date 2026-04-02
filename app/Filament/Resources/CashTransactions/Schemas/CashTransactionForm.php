<?php

namespace App\Filament\Resources\CashTransactions\Schemas;

use App\Models\CashAccount;
use App\Models\CashTransactionCategory;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Illuminate\Support\Str;

class CashTransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Detail Transaksi')
                ->description('Catat pemasukan, pengeluaran, atau transfer antar rekening.')
                ->icon('heroicon-o-arrows-right-left')
                ->schema([
                    Grid::make(3)->schema([
                        Select::make('type')
                            ->label('Jenis Transaksi')
                            ->options([
                                'inflow'   => '⬆ Pemasukan',
                                'outflow'  => '⬇ Pengeluaran',
                                'transfer' => '⇄ Transfer',
                            ])
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn(Set $set) => $set('category_id', null)),

                        DatePicker::make('transaction_date')
                            ->label('Tanggal Transaksi')
                            ->required()
                            ->default(now()),

                        TextInput::make('reference_number')
                            ->label('No. Referensi')
                            ->default(fn() => 'TRX-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4)))
                            ->maxLength(50)
                            ->prefixIcon('heroicon-m-document-text'),
                    ]),

                    Grid::make(2)->schema([
                        Select::make('cash_account_id')
                            ->label(fn(Get $get) => $get('type') === 'transfer' ? 'Rekening Asal' : 'Rekening')
                            ->options(CashAccount::where('is_active', true)->pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->prefixIcon('heroicon-m-banknotes'),

                        Select::make('transfer_to_account_id')
                            ->label('Rekening Tujuan')
                            ->options(CashAccount::where('is_active', true)->pluck('name', 'id'))
                            ->searchable()
                            ->prefixIcon('heroicon-m-arrow-right')
                            ->visible(fn(Get $get) => $get('type') === 'transfer')
                            ->required(fn(Get $get) => $get('type') === 'transfer'),

                        Select::make('category_id')
                            ->label('Kategori')
                            ->options(function (Get $get) {
                                $type = $get('type');
                                if (!$type || $type === 'transfer') {
                                    return [];
                                }
                                return CashTransactionCategory::where('type', $type)
                                    ->orderBy('name')
                                    ->pluck('name', 'id');
                            })
                            ->searchable()
                            ->prefixIcon('heroicon-m-tag')
                            ->visible(fn(Get $get) => $get('type') !== 'transfer')
                            ->placeholder('Pilih kategori...'),
                    ]),

                    TextInput::make('amount')
                        ->label('Jumlah (Rp)')
                        ->required()
                        ->prefix('Rp')
                        ->mask(RawJs::make('$money($input)'))
                        ->dehydrateStateUsing(fn($state) => (float) str_replace(',', '', (string) $state))
                        ->columnSpanFull(),

                    Textarea::make('description')
                        ->label('Keterangan')
                        ->rows(2)
                        ->placeholder('Deskripsi singkat transaksi ini...')
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
