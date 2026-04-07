<?php

namespace App\Filament\Resources\Employees\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\Size;
use Filament\Support\Enums\TextSize;

class EmployeeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // BAGIAN ATAS: HEADER UTAMA (Nama & Status)
                Section::make()
                    ->schema([
                        Flex::make([
                            // Kiri: Identitas Utama
                            Grid::make(1)->schema([
                                TextEntry::make('name')
                                    ->label('Nama Lengkap')
                                    ->weight(FontWeight::Bold)
                                    ->size(TextSize::Large),

                                TextEntry::make('employee_code')
                                    ->label('NIP')
                                    ->fontFamily(FontFamily::Mono)
                                    ->copyable()
                                    ->icon('heroicon-m-identification'),
                            ]),

                            // Kanan: Status & Jabatan
                            Grid::make(2)->schema([
                                TextEntry::make('department.name')
                                    ->label('Departemen')
                                    ->icon('heroicon-m-building-office'),

                                TextEntry::make('position.title')
                                    ->label('Jabatan')
                                    ->icon('heroicon-m-briefcase'),

                                TextEntry::make('employment_type')
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
                                    }),

                                TextEntry::make('is_active')
                                    ->label('Akun')
                                    ->badge()
                                    ->formatStateUsing(fn(bool $state): string => $state ? 'Aktif' : 'Non-Aktif')
                                    ->color(fn(bool $state): string => $state ? 'success' : 'danger'),
                            ]),
                        ])->from('md'),
                    ])
                    ->columnSpanFull(),

                // BAGIAN TENGAH: DETAIL DATA (Grid 2 Kolom)
                Grid::make(2)->schema([
                    // KOLOM KIRI: Data Pribadi & Kontak
                    Group::make([
                        Section::make('Informasi Pribadi')
                            ->icon('heroicon-o-user')
                            ->columns(2)
                            ->schema([
                                TextEntry::make('date_of_birth')
                                    ->label('Tanggal Lahir')
                                    ->date('d F Y')
                                    ->icon('heroicon-m-calendar'),

                                TextEntry::make('gender')
                                    ->label('Jenis Kelamin')
                                    ->formatStateUsing(fn(string $state): string => match ($state) {
                                        'Male' => 'Laki-Laki',
                                        'Female' => 'Perempuan',
                                        default => $state,
                                    }),

                                TextEntry::make('marital_status')
                                    ->label('Status Pernikahan')
                                    ->formatStateUsing(fn(string $state): string => match ($state) {
                                        'Single' => 'Belum Menikah',
                                        'Married' => 'Menikah',
                                        'Divorced' => 'Cerai',
                                        'Widowed' => 'Cerai Mati',
                                        default => $state,
                                    }),

                                TextEntry::make('national_id')
                                    ->label('NIK / KTP')
                                    ->fontFamily(FontFamily::Mono)
                                    ->copyable(),

                                TextEntry::make('kra_pin')
                                    ->label('NPWP')
                                    ->fontFamily(FontFamily::Mono)
                                    ->copyable()
                                    ->columnSpanFull(),
                            ]),

                        Section::make('Kontak')
                            ->icon('heroicon-o-phone')
                            ->schema([
                                TextEntry::make('email')
                                    ->label('Email')
                                    ->icon('heroicon-m-envelope')
                                    ->copyable(),

                                TextEntry::make('phone')
                                    ->label('Nomor Telepon')
                                    ->icon('heroicon-m-device-phone-mobile')
                                    ->url(fn($state) => "tel:{$state}")
                                    ->color('primary'),
                            ])->columns(2),
                    ]),

                    // KOLOM KANAN: Tanggal Penting & Kontak Darurat
                    Group::make([
                        Section::make('Masa Kerja')
                            ->icon('heroicon-o-clock')
                            ->schema([
                                TextEntry::make('hire_date')
                                    ->label('Tanggal Bergabung')
                                    ->date('d F Y')
                                    ->icon('heroicon-m-check-circle'),

                                TextEntry::make('termination_date')
                                    ->label('Tanggal Berhenti')
                                    ->date('d F Y')
                                    ->icon('heroicon-m-x-circle')
                                    ->placeholder('Masih Bekerja')
                                    ->color('danger'),
                            ])->columns(2),

                        Section::make('Kontak Darurat & Keluarga')
                            ->icon('heroicon-o-lifebuoy')
                            ->schema([
                                // Kontak Darurat
                                TextEntry::make('emergency_contact_name')
                                    ->label('Kontak Darurat')
                                    ->icon('heroicon-m-user'),

                                TextEntry::make('emergency_contact_phone')
                                    ->label('No. Darurat')
                                    ->icon('heroicon-m-phone')
                                    ->url(fn($state) => "tel:{$state}"),

                                // Garis Pembatas
                                TextEntry::make('separator')
                                    ->label('')
                                    ->columnSpanFull()
                                    ->extraAttributes(['class' => 'border-b border-gray-200 dark:border-gray-700 my-2']),

                                // Ahli Waris
                                TextEntry::make('next_of_kin_name')
                                    ->label('Nama Ahli Waris')
                                    ->helperText(fn($record) => "Hubungan: " . $record->next_of_kin_relationship),

                                TextEntry::make('next_of_kin_phone')
                                    ->label('No. Ahli Waris')
                                    ->icon('heroicon-m-phone'),
                            ])->columns(2),
                    ]),
                ])
                    ->columnSpanFull(),
            ]);
    }
}
