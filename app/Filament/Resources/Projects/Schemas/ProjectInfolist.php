<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Models\Project;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;

class ProjectInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // HEADER: Nama Proyek & Status
                Group::make()
                    ->columns(2)
                    ->schema([
                        Section::make('Identitas Proyek')
                            ->description('Informasi dasar mengenai pekerjaan.')
                            ->icon('heroicon-o-briefcase')
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Nama Proyek')
                                    ->weight(FontWeight::Bold)
                                    ->size(TextSize::Large),

                                TextEntry::make('offer_number')
                                    ->label('No. Penawaran')
                                    ->icon('heroicon-m-document-text')
                                    ->copyable(),

                                TextEntry::make('plan_date')
                                    ->label('Tanggal Rencana')
                                    ->date('d F Y')
                                    ->icon('heroicon-m-calendar'),

                                TextEntry::make('offer_file_path')
                                    ->label('Dokumen Penawaran')
                                    ->formatStateUsing(fn() => 'Unduh Dokumen')
                                    ->icon('heroicon-o-arrow-down-tray')
                                    ->color('info')
                                    ->url(fn(Project $record) => $record->offer_file_path ? asset('storage/' . $record->offer_file_path) : null)
                                    ->openUrlInNewTab()
                                    ->visible(fn(Project $record) => !empty($record->offer_file_path)),
                            ])->columns(2),
                        Section::make('Legalitas (SPK)')
                            ->description('Isi jika proyek sudah Deal/Realisasi.')
                            ->icon('heroicon-o-check-badge')
                            ->schema([
                                TextEntry::make('status')
                                    ->label('Status')
                                    ->badge()
                                    ->color(fn(string $state): string => match ($state) {
                                        'Realisasi' => 'success',
                                        'Rencana' => 'gray',
                                    }),

                                TextEntry::make('spk_number')
                                    ->label('Nomor SPK')
                                    ->placeholder('-')
                                    ->fontFamily(FontFamily::Mono)
                                    ->copyable(),

                                TextEntry::make('spk_date')
                                    ->label('Tanggal SPK')
                                    ->date('d F Y')
                                    ->placeholder('-'),

                                TextEntry::make('spk_file_path')
                                    ->label('Dokumen SPK')
                                    ->formatStateUsing(fn() => 'Lihat SPK')
                                    ->icon('heroicon-o-document-check')
                                    ->color('success')
                                    ->url(fn(Project $record) => $record->spk_file_path ? asset('storage/' . $record->spk_file_path) : null)
                                    ->openUrlInNewTab()
                                    ->visible(fn(Project $record) => !empty($record->spk_file_path)),
                            ])->columns(2),
                    ])
                    ->columnSpanFull(),

                // BODY: Detail Keuangan (Grid 3 Kolom)
                Section::make('Kalkulasi Pendapatan')
                    ->icon('heroicon-o-banknotes')
                    ->schema([
                        Grid::make(3)->schema([
                            // KOLOM 1: Nilai Kontrak
                            Group::make([
                                TextEntry::make('contract_value')
                                    ->label('Nilai Kontrak (Bruto)')
                                    ->money('IDR', locale: 'id', decimalPlaces: 2)
                                    ->weight(FontWeight::Bold)
                                    ->size(TextSize::Large)
                                    ->color('primary'),

                                TextEntry::make('tax_base')
                                    ->label('DPP (Dasar Pengenaan Pajak)')
                                    ->money('IDR', locale: 'id')
                                    ->size(TextSize::Small)
                                    ->color('gray'),
                            ]),

                            // KOLOM 2: Potongan Pajak
                            Group::make([
                                TextEntry::make('vat')
                                    ->label('PPN (Keluaran)')
                                    ->money('IDR', locale: 'id')
                                    ->icon('heroicon-m-arrow-trending-up')
                                    ->color('danger'),

                                TextEntry::make('income_tax')
                                    ->label('PPh (Potongan)')
                                    ->money('IDR', locale: 'id')
                                    ->icon('heroicon-m-scissors')
                                    ->color('warning'),
                            ]),

                            // KOLOM 3: Hasil Bersih
                            Group::make([
                                TextEntry::make('flag_fee')
                                    ->label('Fee Bendera')
                                    ->money('IDR', locale: 'id')
                                    ->color('gray'),

                                TextEntry::make('net_income')
                                    ->label('NET INCOME (Masuk Kas)')
                                    ->money('IDR', locale: 'id')
                                    ->weight(FontWeight::Black) // Sangat Tebal
                                    ->size(TextSize::Large)
                                    ->color('success') // Warna Hijau
                                    ->icon('heroicon-o-check-circle'),
                            ]),
                        ]),
                    ])
                    ->columnSpanFull(),

                Section::make('Tim & Catatan Tambahan')
                    ->icon('heroicon-o-user-group')
                    ->schema([
                        Grid::make(2)->schema([
                            // Kolom Kiri: Penanggung Jawab (PIC)
                            Group::make([
                                TextEntry::make('employees.name')
                                    ->label('Penanggung Jawab (PIC)')
                                    ->badge() // Menampilkan daftar nama dalam bentuk badge
                                    ->color('info')
                                    ->icon('heroicon-m-user')
                                    ->listWithLineBreaks() // Jika banyak, akan disusun ke bawah
                                    ->placeholder('Belum ada penanggung jawab yang ditunjuk.'),
                            ]),

                            // Kolom Kanan: Catatan
                            Group::make([
                                TextEntry::make('notes')
                                    ->label('Catatan Proyek')
                                    ->markdown() // Mendukung format teks tebal/miring jika diperlukan
                                    ->prose() // Membuat teks panjang lebih nyaman dibaca
                                    ->placeholder('Tidak ada catatan khusus untuk proyek ini.'),
                            ]),
                        ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
