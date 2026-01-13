<?php

namespace App\Filament\Resources\Positions\Schemas;

use App\Models\Employee;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;

class PositionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Jabatan')
                    ->description('Atur detail posisi, kode, dan standar gaji.')
                    ->icon('heroicon-o-identification')
                    ->columns(2)
                    ->schema([
                        // Baris 1: Nama & Kode
                        TextInput::make('title')
                            ->label('Nama Jabatan')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Senior Developer')
                            ->prefixIcon('heroicon-m-briefcase')
                            ->columnSpan(1),

                        TextInput::make('code')
                            ->label('Kode Jabatan')
                            ->unique(ignoreRecord: true)
                            ->maxLength(50)
                            ->placeholder('Contoh: DEV-SR-01')
                            ->prefixIcon('heroicon-m-qr-code')
                            ->helperText('Kode unik untuk identifikasi sistem.')
                            ->columnSpan(1),

                        // Baris 2: Departemen (Lebar Penuh di Mobile, 1 Kolom di Desktop)
                        Select::make('department_id')
                            ->label('Departemen')
                            ->relationship('department', 'name')
                            ->searchable()
                            ->preload()
                            ->prefixIcon('heroicon-m-building-office')
                            ->placeholder('Pilih departemen naungan')
                            ->native(false)
                            ->createOptionForm([
                                Section::make('Informasi Departemen')
                                    ->description('Masukan detail utama departemen perusahaan.')
                                    ->icon('heroicon-o-building-office-2')
                                    ->schema([
                                        Grid::make(2)->schema([
                                            TextInput::make('name')
                                                ->label('Nama Departemen')
                                                ->required()
                                                ->maxLength(255)
                                                ->placeholder('Contoh: Human Resources')
                                                ->prefixIcon('heroicon-m-building-office'),

                                            TextInput::make('code')
                                                ->label('Kode Departemen')
                                                ->maxLength(50)
                                                ->default(fn() => 'DEP-' . strtoupper(uniqid())) // Auto generate optional
                                                ->placeholder('Contoh: HRD-001')
                                                ->prefixIcon('heroicon-m-tag'),
                                        ]),

                                        Select::make('manager_id')
                                            ->label('Kepala Departemen')
                                            ->options(Employee::orderBy('first_name', 'asc')->get()->pluck('name', 'id')) // Pastikan ada accessor full_name di model Employee
                                            ->searchable()
                                            ->preload()
                                            ->prefixIcon('heroicon-m-user-circle')
                                            ->placeholder('Pilih manajer saat ini')
                                            ->native(false),

                                        Textarea::make('description')
                                            ->label('Deskripsi')
                                            ->rows(3)
                                            ->maxLength(500)
                                            ->columnSpanFull()
                                            ->placeholder('Jelaskan fungsi dan tanggung jawab departemen ini.'),
                                    ])
                            ])
                            ->columnSpanFull(),

                        // Baris 3: Gaji & Deskripsi
                        Section::make('Kompensasi & Detail')
                            ->icon('heroicon-o-banknotes')
                            ->schema([
                                TextInput::make('salary')
                                    ->label('Standar Gaji Pokok')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->placeholder('0')
                                    ->step(1000)
                                    ->mask(RawJs::make('$money($input)'))
                                    ->stripCharacters(','),

                                Textarea::make('description')
                                    ->label('Deskripsi Tugas')
                                    ->rows(3)
                                    ->placeholder('Jelaskan tanggung jawab utama posisi ini...')
                                    ->columnSpanFull(),
                            ])
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
