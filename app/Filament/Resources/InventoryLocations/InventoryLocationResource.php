<?php

namespace App\Filament\Resources\InventoryLocations;

use App\Filament\Resources\InventoryLocations\Pages\CreateInventoryLocation;
use App\Filament\Resources\InventoryLocations\Pages\EditInventoryLocation;
use App\Filament\Resources\InventoryLocations\Pages\ListInventoryLocations;
use App\Filament\Resources\InventoryLocations\Pages\ViewInventoryLocation;
use App\Filament\Resources\InventoryLocations\Schemas\InventoryLocationForm;
use App\Filament\Resources\InventoryLocations\Schemas\InventoryLocationInfolist;
use App\Filament\Resources\InventoryLocations\Tables\InventoryLocationsTable;
use App\Models\InventoryLocation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class InventoryLocationResource extends Resource
{
    protected static ?string $model = InventoryLocation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMapPin;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $label = 'Lokasi Penyimpanan';

    protected static string|UnitEnum|null $navigationGroup = 'Sarana dan Prasarana';

    public static function form(Schema $schema): Schema
    {
        return InventoryLocationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return InventoryLocationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InventoryLocationsTable::configure($table);
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
            'index' => ListInventoryLocations::route('/'),
            'create' => CreateInventoryLocation::route('/create'),
            'view' => ViewInventoryLocation::route('/{record}'),
            'edit' => EditInventoryLocation::route('/{record}/edit'),
        ];
    }
}
