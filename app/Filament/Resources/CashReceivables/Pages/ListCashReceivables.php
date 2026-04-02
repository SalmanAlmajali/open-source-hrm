<?php

namespace App\Filament\Resources\CashReceivables\Pages;

use App\Filament\Resources\CashReceivables\CashReceivableResource;
use Filament\Resources\Pages\ListRecords;

class ListCashReceivables extends ListRecords
{
    protected static string $resource = CashReceivableResource::class;

    protected function getHeaderActions(): array
    {
        return [];
        // Receivables are created automatically by ProjectObserver when SPK is set.
        // Manual creation is intentionally disabled.
    }
}
