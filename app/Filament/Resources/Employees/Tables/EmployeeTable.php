<?php

namespace App\Filament\Resources\Employees\Tables;

use Filament\Tables\Table;
use App\Models\{Employee, Position, Department};
use Filament\Tables\Filters\{Filter, SelectFilter};
use Filament\Tables\Columns\{TextColumn, ToggleColumn,};
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\{ActionGroup, EditAction, ViewAction, DeleteAction, BulkActionGroup, DeleteBulkAction};
use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\FontWeight;

class EmployeeTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(
                Employee::query()
                    ->with(['department', 'position']) // Eager load position juga
                    ->latest()
            )
            ->columns([
                TextColumn::make('no')
                    ->label('No.')
                    ->rowIndex()
                    ->color('gray')
                    ->width(50),
                // KOLOM 1: NIP (Dibuat Font Mono agar rapi)
                TextColumn::make('employee_code')
                    ->label('NIP')
                    ->searchable()
                    ->sortable()
                    ->copyable() // Bisa dicopy dengan 1 klik
                    ->fontFamily(FontFamily::Mono)
                    ->weight(FontWeight::Bold),

                // KOLOM 2: NAMA & EMAIL (Digabung agar hemat tempat)
                TextColumn::make('name') // Pastikan ada getFullNameAttribute di Model
                    ->label('Pegawai')
                    ->description(fn(Employee $record): string => $record->email)
                    ->searchable(['first_name', 'last_name', 'email'])
                    ->sortable(['first_name']),

                // KOLOM 3: DEPARTEMEN & JABATAN (Digabung)
                TextColumn::make('department.name')
                    ->label('Divisi & Jabatan')
                    ->description(fn(Employee $record): string => $record->position->title ?? '-')
                    ->searchable()
                    ->sortable(),

                // KOLOM 4: STATUS KEPEGAWAIAN (Pakai Badge Warna)
                TextColumn::make('employment_type')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'success' => 'Permanent',
                        'warning' => 'Contract',
                        'gray' => 'Casual',
                        'info' => 'Internship',
                    ])
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'Permanent' => 'Tetap',
                        'Contract' => 'Kontrak',
                        'Casual' => 'Harian',
                        'Internship' => 'Magang',
                        default => $state,
                    })
                    ->sortable(),

                // KOLOM 5: TANGGAL BERGABUNG
                TextColumn::make('hire_date')
                    ->label('Tgl Bergabung')
                    ->date('d M Y')
                    ->sortable()
                    ->icon('heroicon-m-calendar'),

                // KOLOM 6: TOGGLE AKTIF
                ToggleColumn::make('is_active')
                    ->label('Akun Aktif')
                    ->onColor('success')
                    ->offColor('danger')
                    ->sortable(),

                // KOLOM TERSEMBUNYI (Bisa dimunculkan user jika butuh)
                TextColumn::make('phone')
                    ->label('Telepon')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('national_id')
                    ->label('NIK / KTP')
                    ->searchable()
                    ->fontFamily(FontFamily::Mono)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('kra_pin')
                    ->label('NPWP')
                    ->searchable()
                    ->fontFamily(FontFamily::Mono)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Filter Status Aktif
                Filter::make('status')
                    ->form([
                        \Filament\Forms\Components\Select::make('is_active')
                            ->label('Status Akun')
                            ->options([
                                '1' => 'Aktif',
                                '0' => 'Non-Aktif',
                            ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['is_active'] === '1',
                                fn(Builder $query) => $query->where('is_active', true),
                            )
                            ->when(
                                $data['is_active'] === '0',
                                fn(Builder $query) => $query->where('is_active', false),
                            );
                    }),

                // Filter Departemen
                SelectFilter::make('department_id')
                    ->label('Departemen')
                    ->relationship('department', 'name')
                    ->searchable()
                    ->preload(),

                // Filter Status Kepegawaian
                SelectFilter::make('employment_type')
                    ->label('Status Kepegawaian')
                    ->options([
                        'Permanent' => 'Tetap',
                        'Contract' => 'Kontrak',
                        'Casual' => 'Harian',
                    ]),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()->label('Lihat Detail'),
                    EditAction::make()->label('Ubah Data'),
                    DeleteAction::make()->label('Hapus'),
                ])
                    ->tooltip('Aksi')
                    ->icon('heroicon-m-ellipsis-vertical'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Hapus yang dipilih'),
                ]),
            ])
            ->emptyStateHeading('Belum ada data pegawai')
            ->emptyStateDescription('Silakan buat data pegawai baru melalui tombol di atas.');
    }
}
