<?php

namespace App\Filament\Resources\IncomingLetters;

use App\Filament\Resources\IncomingLetters\Pages\CreateIncomingLetter;
use App\Filament\Resources\IncomingLetters\Pages\EditIncomingLetter;
use App\Filament\Resources\IncomingLetters\Pages\ListIncomingLetters;
use App\Filament\Resources\IncomingLetters\Pages\ViewIncomingLetter;
use App\Filament\Resources\IncomingLetters\Schemas\IncomingLetterForm;
use App\Filament\Resources\IncomingLetters\Schemas\IncomingLetterInfolist;
use App\Filament\Resources\IncomingLetters\Tables\IncomingLettersTable;
use App\Models\IncomingLetter;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class IncomingLetterResource extends Resource
{
    protected static ?string $model = IncomingLetter::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInboxArrowDown;

    protected static ?string $label = 'Surat Masuk';

    protected static string|UnitEnum|null $navigationGroup = 'Tata Laksana Surat';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'reference_number';

    public static function form(Schema $schema): Schema
    {
        return IncomingLetterForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return IncomingLetterInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return IncomingLettersTable::configure($table);
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
            'index' => ListIncomingLetters::route('/'),
            'create' => CreateIncomingLetter::route('/create'),
            'view' => ViewIncomingLetter::route('/{record}'),
            'edit' => EditIncomingLetter::route('/{record}/edit'),
        ];
    }
}
