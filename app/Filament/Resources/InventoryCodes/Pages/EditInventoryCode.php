<?php

namespace App\Filament\Resources\InventoryCodes\Pages;

use App\Filament\Resources\InventoryCodes\InventoryCodeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditInventoryCode extends EditRecord
{
    protected static string $resource = InventoryCodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
