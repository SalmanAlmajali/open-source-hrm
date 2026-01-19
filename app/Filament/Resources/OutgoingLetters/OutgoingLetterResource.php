<?php

namespace App\Filament\Resources\OutgoingLetters;

use App\Filament\Resources\OutgoingLetters\Pages\CreateOutgoingLetter;
use App\Filament\Resources\OutgoingLetters\Pages\EditOutgoingLetter;
use App\Filament\Resources\OutgoingLetters\Pages\ListOutgoingLetters;
use App\Filament\Resources\OutgoingLetters\Pages\ViewOutgoingLetter;
use App\Filament\Resources\OutgoingLetters\Schemas\OutgoingLetterForm;
use App\Filament\Resources\OutgoingLetters\Schemas\OutgoingLetterInfolist;
use App\Filament\Resources\OutgoingLetters\Tables\OutgoingLettersTable;
use App\Models\OutgoingLetter;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class OutgoingLetterResource extends Resource
{
    protected static ?string $model = OutgoingLetter::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPaperAirplane;

    protected static ?string $recordTitleAttribute = 'reference_number';

    protected static ?string $label = 'Surat Keluar';

    protected static string|UnitEnum|null $navigationGroup = 'Tata Laksana Surat';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return OutgoingLetterForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return OutgoingLetterInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OutgoingLettersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOutgoingLetters::route('/'),
            'create' => CreateOutgoingLetter::route('/create'),
            'view' => ViewOutgoingLetter::route('/{record}'),
            'edit' => EditOutgoingLetter::route('/{record}/edit'),
        ];
    }
}
