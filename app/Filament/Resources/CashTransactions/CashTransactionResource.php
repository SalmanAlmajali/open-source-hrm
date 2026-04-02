<?php

namespace App\Filament\Resources\CashTransactions;

use App\Filament\Resources\CashTransactions\Pages\CreateCashTransaction;
use App\Filament\Resources\CashTransactions\Pages\EditCashTransaction;
use App\Filament\Resources\CashTransactions\Pages\ListCashTransactions;
use App\Filament\Resources\CashTransactions\Schemas\CashTransactionForm;
use App\Filament\Resources\CashTransactions\Tables\CashTransactionTable;
use App\Models\CashTransaction;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class CashTransactionResource extends Resource
{
    protected static ?string $model = CashTransaction::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrows-right-left';

    protected static string|UnitEnum|null $navigationGroup = 'Keuangan';

    protected static ?string $label = 'Transaksi Kas';

    protected static ?string $pluralLabel = 'Transaksi Kas';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return CashTransactionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CashTransactionTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListCashTransactions::route('/'),
            'create' => CreateCashTransaction::route('/create'),
            'edit'   => EditCashTransaction::route('/{record}/edit'),
        ];
    }
}
