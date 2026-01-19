<?php

namespace App\Filament\Resources\OutgoingLetters\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OutgoingLettersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('letter_date', 'desc')
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
                    ->copyable(),

                TextColumn::make('letter_date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('recipient')
                    ->label('Tujuan')
                    ->searchable()
                    ->limit(25),

                TextColumn::make('subject')
                    ->label('Perihal')
                    ->limit(30),

                TextColumn::make('signatory.name') // Ambil nama pegawai
                    ->label('TTD')
                    ->placeholder('-'),

                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'gray' => 'draft',
                        'info' => 'sent',
                        'success' => 'archived',
                    ]),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'sent' => 'Terkirim',
                        'archived' => 'Diarsipkan',
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
