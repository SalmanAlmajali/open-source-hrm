<?php

namespace App\Filament\Resources\Departments\Tables;

use App\Models\Department;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DepartmentTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(
                Department::query()
                    ->with('manager')
                    ->latest()
            )
            ->columns([
                TextColumn::make('no')
                    ->label('No.')
                    ->rowIndex()
                    ->color('gray')
                    ->width(50),

                TextColumn::make('name')
                    ->label('Departemen')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->icon('heroicon-o-building-office'),

                TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->fontFamily(FontFamily::Mono)
                    ->badge()
                    ->color('info'),

                TextColumn::make('manager.name') // Mengambil nama dari relasi manager
                    ->label('Kepala Departemen')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-m-user')
                    ->placeholder('Belum ditentukan')
                    ->description(fn(Department $record) => $record->manager ? 'Manager Aktif' : 'Posisi Kosong'),

                TextColumn::make('description')
                    ->label('Deskripsi')
                    ->limit(40)
                    ->tooltip(fn(TextColumn $column): ?string => $column->getState())
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    ViewAction::make(),
                    DeleteAction::make(),
                ])
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
