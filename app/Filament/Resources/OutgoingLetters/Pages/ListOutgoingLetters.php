<?php

namespace App\Filament\Resources\OutgoingLetters\Pages;

use App\Filament\Resources\OutgoingLetters\OutgoingLetterResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOutgoingLetters extends ListRecords
{
    protected static string $resource = OutgoingLetterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
