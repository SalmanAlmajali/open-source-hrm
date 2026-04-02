<?php

namespace App\Filament\Resources\CashReceivables\Tables;

use App\Filament\Resources\CashReceivables\Schemas\CashReceivableForm;
use App\Models\CashAccount;
use App\Models\CashReceivable;
use App\Models\CashTransaction;
use App\Models\CashTransactionCategory;
use App\Models\Project;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Notifications\Notification;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CashReceivableTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(
                CashReceivable::query()
                    ->with(['project', 'cashAccount', 'cashTransaction'])
                    ->latest()
            )
            ->columns([
                TextColumn::make('project.name')
                    ->label('Proyek')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::SemiBold)
                    ->icon('heroicon-o-briefcase'),

                TextColumn::make('project.spk_number')
                    ->label('No. SPK')
                    ->searchable()
                    ->badge()
                    ->color('info')
                    ->placeholder('-'),

                TextColumn::make('receivable_amount')
                    ->label('Nilai Piutang')
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format((float) $state, 0, ',', '.'))
                    ->weight(FontWeight::Bold),

                TextColumn::make('received_amount')
                    ->label('Jumlah Diterima')
                    ->formatStateUsing(fn($state) => $state ? 'Rp ' . number_format((float) $state, 0, ',', '.') : '-')
                    ->color('success'),

                TextColumn::make('due_date')
                    ->label('Jatuh Tempo')
                    ->date('d/m/Y')
                    ->placeholder('-')
                    ->color(fn(CashReceivable $record) => (
                        $record->status === 'pending' &&
                        $record->due_date &&
                        $record->due_date->isPast()
                    ) ? 'danger' : null),

                TextColumn::make('received_date')
                    ->label('Tgl. Diterima')
                    ->date('d/m/Y')
                    ->placeholder('-'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending'   => 'warning',
                        'received'  => 'success',
                        'cancelled' => 'danger',
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'pending'   => 'Menunggu',
                        'received'  => 'Diterima',
                        'cancelled' => 'Dibatalkan',
                        default     => $state,
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending'   => 'Menunggu',
                        'received'  => 'Diterima',
                        'cancelled' => 'Dibatalkan',
                    ]),
            ])
            ->recordActions([
                // Q3 Detailed: "Mark as Received" records actual cash + creates CashTransaction
                Action::make('mark_received')
                    ->label('Tandai Diterima')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn(CashReceivable $record) => $record->status === 'pending')
                    ->form(CashReceivableForm::receiveForm())
                    ->fillForm(fn(CashReceivable $record) => [
                        'received_amount' => number_format((float) $record->receivable_amount, 0, ',', ''),
                        'cash_account_id' => CashAccount::getDefaultForIncome()?->id,
                    ])
                    ->action(function (CashReceivable $record, array $data): void {
                        $category = CashTransactionCategory::findByName('Penerimaan Piutang')
                            ?? CashTransactionCategory::findByName('Pendapatan Proyek');

                        $transaction = CashTransaction::create([
                            'cash_account_id'   => $data['cash_account_id'],
                            'type'              => 'inflow',
                            'amount'            => $data['received_amount'],
                            'transaction_date'  => $data['received_date'],
                            'category_id'       => $category?->id,
                            'description'       => 'Penerimaan piutang: ' . $record->project->name
                                . ' — SPK ' . $record->project->spk_number,
                            'reference_number'  => 'RCV-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4)),
                            'transactionable_type' => Project::class,
                            'transactionable_id'   => $record->project_id,
                            'is_auto_generated' => true,
                        ]);

                        $record->update([
                            'received_amount'    => $data['received_amount'],
                            'received_date'      => $data['received_date'],
                            'status'             => 'received',
                            'notes'              => $data['notes'] ?? $record->notes,
                            'cash_transaction_id'=> $transaction->id,
                            'cash_account_id'    => $data['cash_account_id'],
                        ]);

                        Notification::make()
                            ->title('Piutang berhasil dicatat sebagai diterima')
                            ->success()
                            ->send();
                    }),

                Action::make('cancel')
                    ->label('Batalkan')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn(CashReceivable $record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(fn(CashReceivable $record) => $record->update(['status' => 'cancelled'])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
