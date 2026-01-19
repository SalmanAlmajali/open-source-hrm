<?php

namespace App\Filament\Resources\IncomingLetters\Tables;

use App\Models\IncomingLetter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class IncomingLettersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('received_date', 'desc')
            ->columns([
                TextColumn::make('no')
                    ->label('No.')
                    ->rowIndex()
                    ->color('gray')
                    ->width(50),

                TextColumn::make('reference_number')
                    ->label('No. Surat')
                    ->searchable()
                    ->weight(FontWeight::Bold)
                    ->description(fn(IncomingLetter $record) => 'Agenda: ' . ($record->agenda_number ?? '-')),

                TextColumn::make('letter_date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('sender')
                    ->label('Pengirim')
                    ->searchable()
                    ->limit(25),

                TextColumn::make('subject')
                    ->label('Perihal')
                    ->limit(30)
                    ->tooltip(fn(IncomingLetter $record) => $record->subject),

                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'gray' => 'received',
                        'warning' => 'processed',
                        'success' => 'archived',
                    ]),

                TextColumn::make('file_path')
                    ->label('File')
                    ->icon('heroicon-o-paper-clip')
                    ->color('primary')
                    ->url(fn($record) => $record->file_path ? asset('storage/' . $record->file_path) : null)
                    ->openUrlInNewTab()
                    ->formatStateUsing(fn($state) => $state ? 'Lihat' : '-'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'received' => 'Diterima',
                        'processed' => 'Diproses',
                        'archived' => 'Arsip',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
