<?php

namespace App\Filament\Resources\Projects\Tables;

use App\Models\Project;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no')
                    ->label('No.')
                    ->rowIndex()
                    ->color('gray')
                    ->width(50),

                TextColumn::make('name')
                    ->label('Proyek')
                    ->searchable()
                    ->description(fn(Project $record) => $record->offer_number)
                    ->weight(FontWeight::Bold),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'gray' => 'Rencana',
                        'success' => 'Realisasi',
                    ])
                    ->getStateUsing(fn(Project $record) => $record->status),

                TextColumn::make('contract_value')
                    ->label('Nilai Kontrak')
                    ->money('IDR', locale: 'id')
                    ->sortable()
                    ->summarize(Sum::make()->money('IDR', locale: 'id')),

                TextColumn::make('net_income')
                    ->label('Pemasukan Net')
                    ->money('IDR', locale: 'id')
                    ->color('success')
                    ->sortable()
                    ->summarize(Sum::make()->money('IDR', locale: 'id')),

                TextColumn::make('spk_date')
                    ->label('Tgl SPK')
                    ->date('d M Y')
                    ->toggleable(),

                TextColumn::make('employees.name')
                    ->label('Penanggung Jawab')
                    ->badge() // Ditampilkan dalam bentuk tag/badge
                    ->separator(',') // Pemisah data
                    ->color('info')
                    ->searchable(),

                TextColumn::make('notes')
                    ->label('Catatan')
                    ->limit(50) // Membatasi teks agar tabel tidak terlalu panjang
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->html(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'Rencana' => 'Rencana (Belum SPK)',
                        'Realisasi' => 'Realisasi (Sudah SPK)',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if ($data['value'] === 'Rencana') {
                            return $query->whereNull('spk_number');
                        }
                        if ($data['value'] === 'Realisasi') {
                            return $query->whereNotNull('spk_number');
                        }
                    }),
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
