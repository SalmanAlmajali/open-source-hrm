<?php

namespace App\Filament\Resources\InventoryCodes\Schemas;

use App\Models\InventoryCode;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class InventoryCodeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->label('Kode')
                    ->required()
                    ->unique(InventoryCode::class, 'code', ignoreRecord: true)
                    ->maxLength(255)
                    ->columnSpanFull(),
            ]);
    }
}
