<?php

namespace App\Filament\Resources\InventoryLocations\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class InventoryLocationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Info Lokasi')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Lokasi')
                            ->placeholder('Contoh: Lemari A - Rak 1')
                            ->required()
                            ->maxLength(255),

                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->placeholder('Keterangan detail lokasi...')
                            ->rows(3),
                    ])->columnSpanFull(),
            ]);
    }
}
