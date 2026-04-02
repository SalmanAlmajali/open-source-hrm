<?php

namespace App\Filament\Resources\CashAccounts\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;

class CashAccountForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi Rekening')
                ->description('Detail rekening kas atau bank yang digunakan perusahaan.')
                ->icon('heroicon-o-banknotes')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('name')
                            ->label('Nama Rekening')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Kas Utama, BNI 1234-5678')
                            ->prefixIcon('heroicon-m-banknotes'),

                        Select::make('type')
                            ->label('Jenis Rekening')
                            ->options([
                                'cash'     => 'Kas Tunai',
                                'bank'     => 'Rekening Bank',
                                'e-wallet' => 'Dompet Digital',
                            ])
                            ->required()
                            ->prefixIcon('heroicon-m-credit-card'),

                        TextInput::make('opening_balance')
                            ->label('Saldo Awal (Rp)')
                            ->required()
                            ->default(0)
                            ->numeric()
                            ->prefix('Rp')
                            ->mask(RawJs::make('$money($input)'))
                            ->dehydrateStateUsing(fn($state) => (float) str_replace(',', '', (string) $state)),

                        TextInput::make('currency')
                            ->label('Mata Uang')
                            ->default('IDR')
                            ->maxLength(3)
                            ->readOnly(),
                    ]),

                    Textarea::make('notes')
                        ->label('Catatan')
                        ->rows(2)
                        ->columnSpanFull(),
                ]),

            Section::make('Konfigurasi Integrasi HRM')
                ->description('Tentukan rekening default untuk transaksi otomatis dari modul HRM.')
                ->icon('heroicon-o-cog-6-tooth')
                ->schema([
                    Grid::make(3)->schema([
                        Toggle::make('is_active')
                            ->label('Rekening Aktif')
                            ->default(true)
                            ->onColor('success')
                            ->offColor('danger')
                            ->inline(false),

                        Toggle::make('default_for_payroll')
                            ->label('Default Penggajian')
                            ->helperText('Outflow otomatis saat payroll selesai.')
                            ->onColor('warning')
                            ->inline(false),

                        Toggle::make('default_for_income')
                            ->label('Default Pendapatan')
                            ->helperText('Piutang proyek akan diposting ke sini.')
                            ->onColor('info')
                            ->inline(false),
                    ]),
                ]),
        ]);
    }
}
