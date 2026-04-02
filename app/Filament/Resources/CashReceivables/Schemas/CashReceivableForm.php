<?php

namespace App\Filament\Resources\CashReceivables\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use App\Models\CashAccount;

class CashReceivableForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Detail Piutang')->schema([
                TextInput::make('receivable_amount')
                    ->label('Nilai Piutang (Rp)')
                    ->prefix('Rp')
                    ->mask(RawJs::make('$money($input)'))
                    ->dehydrateStateUsing(fn($state) => (float) str_replace(',', '', (string) $state))
                    ->readOnly(),

                DatePicker::make('due_date')
                    ->label('Jatuh Tempo'),

                Textarea::make('notes')
                    ->label('Catatan')
                    ->rows(2)
                    ->columnSpanFull(),
            ]),
        ]);
    }

    /**
     * Form used inside the "Mark as Received" table action modal.
     */
    public static function receiveForm(): array
    {
        return [
            TextInput::make('received_amount')
                ->label('Jumlah yang Diterima (Rp)')
                ->prefix('Rp')
                ->required()
                ->mask(RawJs::make('$money($input)'))
                ->dehydrateStateUsing(fn($state) => (float) str_replace(',', '', (string) $state)),

            DatePicker::make('received_date')
                ->label('Tanggal Penerimaan')
                ->required()
                ->default(now()),

            Select::make('cash_account_id')
                ->label('Rekening Tujuan')
                ->options(CashAccount::where('is_active', true)->pluck('name', 'id'))
                ->required()
                ->searchable()
                ->prefixIcon('heroicon-m-banknotes'),

            \Filament\Forms\Components\Textarea::make('notes')
                ->label('Catatan Penerimaan')
                ->rows(2),
        ];
    }
}
