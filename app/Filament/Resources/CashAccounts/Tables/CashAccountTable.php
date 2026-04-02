<?php

namespace App\Filament\Resources\CashAccounts\Tables;

use App\Models\CashAccount;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CashAccountTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(CashAccount::query()->latest())
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Rekening')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->icon('heroicon-o-banknotes'),

                TextColumn::make('type')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'cash'     => 'success',
                        'bank'     => 'info',
                        'e-wallet' => 'warning',
                        default    => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'cash'     => 'Kas Tunai',
                        'bank'     => 'Rekening Bank',
                        'e-wallet' => 'Dompet Digital',
                        default    => $state,
                    }),

                TextColumn::make('current_balance')
                    ->label('Saldo Saat Ini')
                    ->getStateUsing(fn(CashAccount $record): float => $record->currentBalance())
                    ->formatStateUsing(fn(float $state): string => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->color(fn(CashAccount $record): string => $record->currentBalance() >= 0 ? 'success' : 'danger')
                    ->weight(FontWeight::Bold),

                IconColumn::make('default_for_payroll')
                    ->label('Default Gaji')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-minus-circle')
                    ->trueColor('warning')
                    ->falseColor('gray'),

                IconColumn::make('default_for_income')
                    ->label('Default Pendapatan')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-minus-circle')
                    ->trueColor('info')
                    ->falseColor('gray'),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
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
