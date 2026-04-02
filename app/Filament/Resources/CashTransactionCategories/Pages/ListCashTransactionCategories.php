<?php

namespace App\Filament\Resources\CashTransactionCategories\Pages;

use App\Filament\Resources\CashTransactionCategories\CashTransactionCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCashTransactionCategories extends ListRecords
{
    protected static string $resource = CashTransactionCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
