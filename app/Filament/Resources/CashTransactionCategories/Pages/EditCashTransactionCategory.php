<?php

namespace App\Filament\Resources\CashTransactionCategories\Pages;

use App\Filament\Resources\CashTransactionCategories\CashTransactionCategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCashTransactionCategory extends EditRecord
{
    protected static string $resource = CashTransactionCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }

    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? $this->getResource()::getUrl('index');
    }
}
