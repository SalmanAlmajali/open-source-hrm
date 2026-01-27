<?php

namespace App\Filament\Resources\InventoryLocations\Tables;

use App\Models\InventoryLocation;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InventoryLocationsTable
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
                ImageColumn::make('qr_url')
                    ->label('QR Code')
                    ->square()
                    ->state(fn($record) => $record->qr_url),

                TextColumn::make('name')
                    ->label('Lokasi')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn(InventoryLocation $record) => $record->description),

                TextColumn::make('items_count')
                    ->label('Jumlah Barang')
                    ->counts('items')
                    ->badge()
                    ->color('info'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('print')
                    ->label('QR')
                    ->icon('heroicon-o-qr-code')
                    ->url(fn(InventoryLocation $record) => $record->qr_url)
                    ->openUrlInNewTab(),
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                ])
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
