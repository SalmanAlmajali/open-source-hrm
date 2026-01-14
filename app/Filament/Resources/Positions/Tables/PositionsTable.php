<?php

namespace App\Filament\Resources\Positions\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PositionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('title', 'asc')
            ->columns([
                TextColumn::make('no')
                    ->label('No.')
                    ->rowIndex()
                    ->color('gray')
                    ->width(50),

                TextColumn::make('title')
                    ->label('Nama Jabatan')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->icon('heroicon-o-briefcase'),

                TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable()
                    ->fontFamily(FontFamily::Mono)
                    ->copyable()
                    ->badge()
                    ->color('gray')
                    ->placeholder('-'),

                TextColumn::make('department.name')
                    ->label('Departemen')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-building-office-2')
                    ->badge()
                    ->color('info')
                    ->placeholder('Umum / Tanpa Dept'),

                TextColumn::make('salary')
                    ->label('Gaji Pokok')
                    ->money('IDR', locale: 'id') // Format Rupiah otomatis
                    ->sortable()
                    ->alignment('right'), // Angka rata kanan agar rapi

                TextColumn::make('description')
                    ->label('Deskripsi')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('department_id')
                    ->label('Filter Departemen')
                    ->relationship('department', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ])
                    ->tooltip('Aksi')
                    ->icon('heroicon-m-ellipsis-vertical'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Tidak ada jabatan')
            ->emptyStateDescription('Buat jabatan baru untuk menetapkan struktur gaji dan posisi.');;
    }
}
