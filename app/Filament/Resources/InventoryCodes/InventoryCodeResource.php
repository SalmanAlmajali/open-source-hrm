<?php

namespace App\Filament\Resources\InventoryCodes;

use App\Filament\Resources\InventoryCodes\Pages\CreateInventoryCode;
use App\Filament\Resources\InventoryCodes\Pages\EditInventoryCode;
use App\Filament\Resources\InventoryCodes\Pages\ListInventoryCodes;
use App\Filament\Resources\InventoryCodes\Schemas\InventoryCodeForm;
use App\Filament\Resources\InventoryCodes\Tables\InventoryCodesTable;
use App\Models\InventoryCode;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class InventoryCodeResource extends Resource
{
    protected static ?string $model = InventoryCode::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNumberedList;

    protected static ?string $recordTitleAttribute = 'code';
    
    protected static ?string $label = 'Kode Barang';

    protected static string|UnitEnum|null $navigationGroup = 'Sarana dan Prasarana';

    public static function form(Schema $schema): Schema
    {
        return InventoryCodeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InventoryCodesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInventoryCodes::route('/'),
            // 'create' => CreateInventoryCode::route('/create'),
            // 'edit' => EditInventoryCode::route('/{record}/edit'),
        ];
    }
}
