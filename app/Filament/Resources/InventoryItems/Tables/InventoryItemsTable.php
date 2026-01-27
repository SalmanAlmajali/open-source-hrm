<?php

namespace App\Filament\Resources\InventoryItems\Tables;

use App\Models\InventoryItem;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InventoryItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no')
                    ->label('No.')
                    ->rowIndex()
                    ->color('gray')
                    ->width(50),

                // Tampilkan QR Code kecil di tabel
                ImageColumn::make('qr_url')
                    ->label('QR')
                    ->square()
                    ->state(fn($record) => $record->qr_url), // Panggil accessor model

                // Tampilkan Full Code (Gabungan)
                TextColumn::make('full_code')
                    ->label('Kode Barang')
                    ->weight('bold')
                    ->searchable(['unique_id', 'inventoryCode.code']),

                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->description(fn(InventoryItem $record) => $record->brand),

                TextColumn::make('condition')
                    ->label('Kondisi')
                    ->badge()
                    ->colors([
                        'success' => 'good',
                        'danger' => 'broken',
                        'warning' => 'repair',
                    ]),

                TextColumn::make('inventoryLocation.name')
                    ->label('Lokasi Penyimpanan')
                    ->searchable()
                    ->toggleable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('print_qr')
                    ->label('QR')
                    ->icon('heroicon-o-qr-code')
                    ->url(fn(InventoryItem $record) => $record->qr_url)
                    ->openUrlInNewTab(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
