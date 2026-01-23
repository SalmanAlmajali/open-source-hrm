<?php

namespace App\Filament\Resources\InventoryCodes\Pages;

use App\Filament\Resources\InventoryCodes\InventoryCodeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListInventoryCodes extends ListRecords
{
    protected static string $resource = InventoryCodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->modalWidth('md'),
        ];
    }
}
