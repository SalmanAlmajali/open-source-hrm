<?php

namespace App\Filament\Resources\IncomingLetters\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;

class IncomingLetterInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Group::make([
                            // Bagian Kiri: Identitas Utama
                            Grid::make(1)->schema([
                                TextEntry::make('reference_number')
                                    ->label('Nomor Surat (Eksternal)')
                                    ->weight(FontWeight::Bold)
                                    ->copyable()
                                    ->icon('heroicon-m-document-text'),

                                TextEntry::make('sender')
                                    ->label('Pengirim')
                                    ->size(TextSize::Large)
                                    ->weight(FontWeight::SemiBold),

                                TextEntry::make('subject')
                                    ->label('Perihal')
                                    ->color('gray'),
                            ]),

                            // Bagian Kanan: Tanggal & Agenda
                            Grid::make(2)->schema([
                                TextEntry::make('classification_code')
                                    ->label('Kode Arsip')
                                    ->placeholder('-')
                                    ->fontFamily(FontFamily::Mono),

                                TextEntry::make('letter_date')
                                    ->label('Tgl. Surat')
                                    ->date('d F Y')
                                    ->icon('heroicon-m-calendar'),

                                TextEntry::make('received_date')
                                    ->label('Tgl. Diterima')
                                    ->date('d F Y')
                                    ->icon('heroicon-m-inbox-arrow-down'),
                            ]),
                        ])->from('md'),
                    ]),

                Section::make('Detail Isi & Disposisi')
                    ->schema([
                        Grid::make(3)->schema([
                            // Kolom 1: Penerima & Status
                            Grid::make(1)->schema([
                                TextEntry::make('recipient')
                                    ->label('Ditujukan Kepada')
                                    ->icon('heroicon-m-user'),

                                TextEntry::make('status')
                                    ->label('Status Disposisi')
                                    ->badge()
                                    ->color(fn(string $state): string => match ($state) {
                                        'received' => 'gray',
                                        'processed' => 'warning',
                                        'archived' => 'success',
                                    })
                                    ->formatStateUsing(fn(string $state): string => match ($state) {
                                        'received' => 'Diterima (Baru)',
                                        'processed' => 'Sedang Diproses',
                                        'archived' => 'Diarsipkan',
                                    }),
                            ])->columnSpan(1),

                            // Kolom 2: Ringkasan Isi (Lebar)
                            TextEntry::make('description')
                                ->label('Ringkasan Isi Surat')
                                ->markdown()
                                ->prose()
                                ->placeholder('Tidak ada ringkasan.')
                                ->columnSpan(2),
                        ]),
                    ]),

                // Bagian File Lampiran
                Section::make('Berkas Lampiran')
                    ->schema([
                        TextEntry::make('file_path')
                            ->label('Scan Surat Masuk')
                            ->formatStateUsing(fn() => 'Unduh / Lihat Dokumen')
                            ->icon('heroicon-o-paper-clip')
                            ->color('primary')
                            ->url(fn($record) => $record->file_path ? asset('storage/' . $record->file_path) : null)
                            ->openUrlInNewTab()
                            ->visible(fn($record) => !empty($record->file_path)),
                    ])
                    ->hidden(fn($record) => empty($record->file_path)),
            ]);
    }
}
