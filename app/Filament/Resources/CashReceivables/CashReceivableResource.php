<?php

namespace App\Filament\Resources\CashReceivables;

use App\Filament\Resources\CashReceivables\Pages\ListCashReceivables;
use App\Filament\Resources\CashReceivables\Schemas\CashReceivableForm;
use App\Filament\Resources\CashReceivables\Tables\CashReceivableTable;
use App\Models\CashReceivable;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class CashReceivableResource extends Resource
{
    protected static ?string $model = CashReceivable::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-currency-dollar';

    protected static string|UnitEnum|null $navigationGroup = 'Keuangan';

    protected static ?string $label = 'Piutang Proyek';

    protected static ?string $pluralLabel = 'Piutang Proyek';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return CashReceivableForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CashReceivableTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCashReceivables::route('/'),
        ];
    }
}
