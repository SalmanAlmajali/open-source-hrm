<?php

namespace App\Filament\Resources\InventoryCodes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InventoryCodesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query) => $query->withCount([
                'items as total',
                'items as total_damaged' => fn($q) => $q->whereIn('condition', ['broken', 'repair']),
                'items as total_borrowed' => fn($q) => $q->whereHas('inventoryLoans', fn($l) => $l->whereIn('status', ['borrowed', 'approved'])),
            ]))
            ->columns([
                TextColumn::make('no')
                    ->label('No.')
                    ->rowIndex()
                    ->color('gray')
                    ->width(50),

                TextColumn::make('code')
                    ->searchable()
                    ->label('Kode'),

                TextColumn::make('total')
                    ->label('Jumlah barang')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('total_borrowed')
                    ->label('Jumlah dipinjam')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('total_damaged')
                    ->label('Jumlah rusak')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                    ->modalWidth('md'),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
