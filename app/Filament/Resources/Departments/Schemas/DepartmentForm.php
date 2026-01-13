<?php
namespace App\Filament\Resources\Departments\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use App\Models\Employee;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;

class DepartmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
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
                                ->default(fn () => 'DEP-' . strtoupper(uniqid())) // Auto generate optional
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
            ]);
    }
}