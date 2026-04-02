<?php

namespace App\Filament\Resources\CashTransactionCategories\Pages;

use App\Filament\Resources\CashTransactionCategories\CashTransactionCategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCashTransactionCategory extends CreateRecord
{
    protected static string $resource = CashTransactionCategoryResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
