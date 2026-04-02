<?php

namespace App\Filament\Resources\CashTransactionCategories\Tables;

use App\Models\CashTransactionCategory;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CashTransactionCategoryTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(CashTransactionCategory::query()->orderBy('type')->orderBy('name'))
            ->columns([
                ColorColumn::make('color')
                    ->label('')
                    ->width(40),

                TextColumn::make('name')
                    ->label('Nama Kategori')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::SemiBold),

                TextColumn::make('type')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn(string $state): string => $state === 'inflow' ? 'success' : 'danger')
                    ->formatStateUsing(fn(string $state): string => $state === 'inflow' ? 'Pemasukan' : 'Pengeluaran'),

                TextColumn::make('transactions_count')
                    ->label('Digunakan')
                    ->counts('transactions')
                    ->badge()
                    ->color('gray'),
            ])
            ->filters([])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
