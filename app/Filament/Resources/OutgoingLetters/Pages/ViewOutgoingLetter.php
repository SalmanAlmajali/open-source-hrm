<?php

namespace App\Filament\Resources\OutgoingLetters\Pages;

use App\Filament\Resources\OutgoingLetters\OutgoingLetterResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewOutgoingLetter extends ViewRecord
{
    protected static string $resource = OutgoingLetterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
