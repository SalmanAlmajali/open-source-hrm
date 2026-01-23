<?php

namespace App\Filament\Resources\InventoryCodes\Pages;

use App\Filament\Resources\InventoryCodes\InventoryCodeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateInventoryCode extends CreateRecord
{
    protected static string $resource = InventoryCodeResource::class;
}
