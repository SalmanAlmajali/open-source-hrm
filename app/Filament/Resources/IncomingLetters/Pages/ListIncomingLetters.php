<?php

namespace App\Filament\Resources\IncomingLetters\Pages;

use App\Filament\Resources\IncomingLetters\IncomingLetterResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListIncomingLetters extends ListRecords
{
    protected static string $resource = IncomingLetterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
