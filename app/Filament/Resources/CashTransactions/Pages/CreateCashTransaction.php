<?php

namespace App\Filament\Resources\CashTransactions\Pages;

use App\Filament\Resources\CashTransactions\CashTransactionResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CreateCashTransaction extends CreateRecord
{
    protected static string $resource = CashTransactionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['recorded_by']      = Auth::id();
        $data['reference_number'] ??= 'TRX-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4));
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
