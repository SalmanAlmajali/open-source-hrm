<?php

namespace App\Filament\Resources\CashTransactionCategories;

use App\Filament\Resources\CashTransactionCategories\Pages\CreateCashTransactionCategory;
use App\Filament\Resources\CashTransactionCategories\Pages\EditCashTransactionCategory;
use App\Filament\Resources\CashTransactionCategories\Pages\ListCashTransactionCategories;
use App\Filament\Resources\CashTransactionCategories\Schemas\CashTransactionCategoryForm;
use App\Filament\Resources\CashTransactionCategories\Tables\CashTransactionCategoryTable;
use App\Models\CashTransactionCategory;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class CashTransactionCategoryResource extends Resource
{
    protected static ?string $model = CashTransactionCategory::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    protected static string|UnitEnum|null $navigationGroup = 'Keuangan';

    protected static ?string $label = 'Kategori Transaksi';

    protected static ?string $pluralLabel = 'Kategori Transaksi';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return CashTransactionCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CashTransactionCategoryTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListCashTransactionCategories::route('/'),
            'create' => CreateCashTransactionCategory::route('/create'),
            'edit'   => EditCashTransactionCategory::route('/{record}/edit'),
        ];
    }
}
