<?php

namespace App\Filament\Resources\InventoryLocations\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\TextSize;

class InventoryLocationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // 1. Informasi Lokasi
                Section::make('Detail Lokasi')
                    ->schema([
                        Grid::make(2)->schema([
                            TextEntry::make('name')
                                ->label('Nama Lokasi')
                                ->weight('bold')
                                ->size(TextSize::Large),

                            TextEntry::make('description')
                                ->label('Keterangan')
                                ->placeholder('Tidak ada keterangan'),
                        ]),
                    ])
                    ->columnSpanFull(),

                // 2. Daftar Barang di Lokasi Tersebut
                Section::make('Isi Lokasi')
                    ->description('Daftar barang yang tercatat berada di lokasi ini.')
                    ->schema([
                        RepeatableEntry::make('items') // Pastikan relasi 'items' ada di model Location
                            ->label('Daftar Barang') // Kosongkan label agar lebih bersih
                            ->schema([
                                Grid::make(3)->schema([
                                    // Kolom 2: Info Barang
                                    TextEntry::make('name')
                                        ->label('Nama Barang')
                                        ->weight('bold'),

                                    TextEntry::make('full_code')
                                        ->label('Kode')
                                        ->size(TextSize::Small)
                                        ->color('gray'),
                                    // Kolom 3: Kondisi
                                    TextEntry::make('condition')
                                        ->label('Kondisi')
                                        ->badge()
                                        ->colors([
                                            'success' => 'good',
                                            'danger' => 'broken',
                                            'warning' => 'repair',
                                        ]),

                                    // Kolom 1: Gambar Barang
                                    ImageEntry::make('image_path')
                                        ->label('Gambar')
                                        ->defaultImageUrl(url('/images/placeholder.png'))
                                        ->columnSpan(2), // Fallback jika tidak ada gambar,
                                    
                                        // Kolom 1: Gambar Barang
                                    ImageEntry::make('qr_path')
                                        ->label('QR')
                                        ->defaultImageUrl(url('/images/placeholder.png')), // Fallback jika tidak ada gambar,
                                ]),

                            ])
                            ->contained(true)
                            ->grid(2), // Memberikan kotak border pada setiap item
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
