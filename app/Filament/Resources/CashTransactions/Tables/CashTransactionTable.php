<?php

namespace App\Filament\Resources\CashTransactions\Tables;

use App\Models\CashTransaction;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;

class CashTransactionTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(
                CashTransaction::query()
                    ->with(['cashAccount', 'category', 'transferToAccount'])
                    ->latest('transaction_date')
                    ->latest()
            )
            ->columns([
                TextColumn::make('transaction_date')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('reference_number')
                    ->label('No. Ref')
                    ->searchable()
                    ->copyable()
                    ->fontFamily(\Filament\Support\Enums\FontFamily::Mono)
                    ->color('gray'),

                TextColumn::make('cashAccount.name')
                    ->label('Rekening')
                    ->searchable()
                    ->icon('heroicon-m-banknotes'),

                TextColumn::make('type')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'inflow'   => 'success',
                        'outflow'  => 'danger',
                        'transfer' => 'info',
                        default    => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'inflow'   => '⬆ Pemasukan',
                        'outflow'  => '⬇ Pengeluaran',
                        'transfer' => '⇄ Transfer',
                        default    => $state,
                    }),

                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->badge()
                    ->color('gray')
                    ->placeholder('-'),

                TextColumn::make('amount')
                    ->label('Jumlah')
                    ->formatStateUsing(fn($state): string => 'Rp ' . number_format((float) $state, 0, ',', '.'))
                    ->color(fn(CashTransaction $record): string => match ($record->type) {
                        'inflow'   => 'success',
                        'outflow'  => 'danger',
                        'transfer' => 'info',
                        default    => 'gray',
                    })
                    ->weight(FontWeight::Bold)
                    ->alignRight(),

                TextColumn::make('description')
                    ->label('Keterangan')
                    ->limit(35)
                    ->tooltip(fn(TextColumn $column) => $column->getState())
                    ->placeholder('-'),

                IconColumn::make('is_auto_generated')
                    ->label('Auto')
                    ->boolean()
                    ->trueIcon('heroicon-o-bolt')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('warning')
                    ->falseColor('gray')
                    ->tooltip(fn($record) => $record->is_auto_generated ? 'Dibuat otomatis dari HRM' : 'Input manual'),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Jenis')
                    ->options([
                        'inflow'   => 'Pemasukan',
                        'outflow'  => 'Pengeluaran',
                        'transfer' => 'Transfer',
                    ]),

                SelectFilter::make('cash_account_id')
                    ->label('Rekening')
                    ->relationship('cashAccount', 'name'),

                SelectFilter::make('category_id')
                    ->label('Kategori')
                    ->relationship('category', 'name'),

                Filter::make('date_range')
                    ->label('Rentang Tanggal')
                    ->form([
                        DatePicker::make('from')->label('Dari Tanggal'),
                        DatePicker::make('until')->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn($q, $date) => $q->whereDate('transaction_date', '>=', $date))
                            ->when($data['until'], fn($q, $date) => $q->whereDate('transaction_date', '<=', $date));
                    }),
            ])
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
            ])
            ->defaultSort('transaction_date', 'desc');
    }
}
