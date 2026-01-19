<?php

namespace App\Filament\Resources\IncomingLetters\Pages;

use App\Filament\Resources\IncomingLetters\IncomingLetterResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewIncomingLetter extends ViewRecord
{
    protected static string $resource = IncomingLetterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
