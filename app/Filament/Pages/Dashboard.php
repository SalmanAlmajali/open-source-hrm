<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard as PagesDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Pages\Page;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class Dashboard extends PagesDashboard
{
    use HasFiltersForm;

    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Filter Grafik')
                    ->description('Atur parameter data yang ingin ditampilkan.')
                    ->schema([
                        // Filter Tahun
                        Select::make('year')
                            ->label('Tahun')
                            ->options(function () {
                                $years = range(date('Y'), date('Y') - 5);
                                return array_combine($years, $years);
                            })
                            ->default(date('Y'))
                            ->required(),

                        // Filter Bulan (Opsional)
                        Select::make('month')
                            ->label('Bulan')
                            ->options([
                                '01' => 'Januari',
                                '02' => 'Februari',
                                '03' => 'Maret',
                                '04' => 'April',
                                '05' => 'Mei',
                                '06' => 'Juni',
                                '07' => 'Juli',
                                '08' => 'Agustus',
                                '09' => 'September',
                                '10' => 'Oktober',
                                '11' => 'November',
                                '12' => 'Desember',
                            ])
                            ->placeholder('Semua Bulan (Tahunan)')
                            ->native(false),

                        // Pilihan Kolom Data
                        Select::make('data_column')
                            ->label('Data Ditampilkan')
                            ->options([
                                'contract_value' => 'Nilai Kontrak (Bruto)',
                                'net_income' => 'Net Income (Bersih)',
                            ])
                            ->default('contract_value')
                            ->required(),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),
            ]);
    }
}
