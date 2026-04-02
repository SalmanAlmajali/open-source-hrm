<?php

namespace App\Filament\Resources\CashTransactionCategories\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CashTransactionCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Detail Kategori')
                ->icon('heroicon-o-tag')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('name')
                            ->label('Nama Kategori')
                            ->required()
                            ->maxLength(100)
                            ->placeholder('Contoh: Gaji Karyawan'),

                        Select::make('type')
                            ->label('Jenis')
                            ->options([
                                'inflow'  => 'Pemasukan',
                                'outflow' => 'Pengeluaran',
                            ])
                            ->required()
                            ->prefixIcon('heroicon-m-arrows-right-left'),

                        ColorPicker::make('color')
                            ->label('Warna Badge')
                            ->helperText('Warna yang tampil pada badge kategori di tabel transaksi.'),
                    ]),
                ]),
        ]);
    }
}
