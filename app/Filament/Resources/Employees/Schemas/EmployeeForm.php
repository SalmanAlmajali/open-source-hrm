<?php

namespace App\Filament\Resources\Employees\Schemas;

use Filament\Forms\Components\{TextInput, DatePicker, FileUpload, Select, Toggle, TextArea};
use Filament\Schemas\Schema;
use App\Models\{Position, Department, Employee};
use Filament\Schemas\Components\{Section, Grid};
use Filament\Support\RawJs;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class EmployeeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // GROUP 1: DATA PRIBADI
                Section::make('Informasi Pribadi')
                    ->description('Data utama identitas pegawai.')
                    ->icon('heroicon-o-user') // Ikon User
                    ->iconColor('primary')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        TextInput::make('employee_code')
                            ->label('NIP (Nomor Induk Pegawai)')
                            ->required()
                            ->maxLength(50)
                            ->placeholder('Contoh: 2024001')
                            ->prefixIcon('heroicon-m-identification')
                            ->columnSpan(2), // NIP dibuat lebar penuh atau bisa disesuaikan

                        TextInput::make('first_name')
                            ->label('Nama Depan')
                            ->required()
                            ->placeholder('Masukan nama depan')
                            ->maxLength(255),

                        TextInput::make('last_name')
                            ->label('Nama Belakang')
                            ->required()
                            ->placeholder('Masukan nama belakang')
                            ->maxLength(255),

                        Grid::make(2)->schema([
                            DatePicker::make('date_of_birth')
                                ->label('Tanggal Lahir')
                                ->prefixIcon('heroicon-m-calendar')
                                ->format('Y-m-d') // Format database standar
                                ->displayFormat('d/m/Y')
                                ->maxDate(now()->subYears(17)) // Validasi minimal umur
                                ->required(),

                            Select::make('gender')
                                ->label('Jenis Kelamin')
                                ->native(false)
                                ->options([
                                    'Male' => 'Laki-Laki',
                                    'Female' => 'Perempuan',
                                ])
                                ->required(),

                            Select::make('marital_status')
                                ->label('Status Pernikahan')
                                ->native(false)
                                ->prefixIcon('heroicon-m-heart')
                                ->options([
                                    'Single' => 'Belum Menikah',
                                    'Married' => 'Menikah',
                                    'Divorced' => 'Cerai Hidup',
                                    'Widowed' => 'Cerai Mati',
                                ]),

                            TextInput::make('national_id')
                                ->label('Nomor KTP / NIK')
                                ->required()
                                ->numeric()
                                ->unique(ignoreRecord: true)
                                ->prefixIcon('heroicon-m-credit-card')
                                ->maxLength(16)
                                ->mask('9999999999999999'),
                        ])
                            ->columnSpanFull(),
                        FileUpload::make('profile_photo_path')
                            ->label('Foto Profil')
                            ->avatar() // Mode avatar (bulat & kecil)
                            ->image()
                            ->imageEditor() // Fitur crop/rotate
                            ->columnSpanFull()
                            ->maxSize(2048)
                            ->helperText('Format: JPG, PNG. Maks: 2MB')
                            ->saveUploadedFileUsing(function (TemporaryUploadedFile $file): string {
                                return (new Employee)->uploadFile($file, 'employee-photos');
                            }),
                    ]),

                // GROUP 4: DATA PEKERJAAN
                Section::make('Detail Kepegawaian')
                    ->description('Informasi terkait posisi dan departemen.')
                    ->icon('heroicon-o-briefcase')
                    ->collapsible()
                    ->schema([
                        Select::make('department_id')
                            ->label('Departemen')
                            ->relationship(
                                name: 'department',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn(Builder $query) => $query->select('id', 'name')->orderBy('name', 'asc')
                            )
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->prefixIcon('heroicon-m-building-office')
                            ->placeholder('Pilih Departemen')
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
                            ]),

                        Select::make('position_id')
                            ->label('Jabatan / Posisi')
                            ->options(Position::all()->pluck('title', 'id'))
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->prefixIcon('heroicon-m-briefcase')
                            ->placeholder('Pilih Jabatan'),

                        Select::make('employment_type')
                            ->label('Status Kepegawaian')
                            ->options([
                                'Permanent' => 'Tetap (Permanent)',
                                'Contract' => 'Kontrak (PKWT)',
                                'Casual' => 'Harian / Freelance',
                                'Internship' => 'Magang',
                            ])
                            ->native(false)
                            ->required(),

                        Grid::make(2)->schema([
                            DatePicker::make('hire_date')
                                ->label('Tanggal Bergabung')
                                ->prefixIcon('heroicon-m-calendar-days')
                                ->required(),

                            DatePicker::make('termination_date')
                                ->label('Tanggal Berhenti')
                                ->prefixIcon('heroicon-m-x-circle')
                                ->helperText('Isi jika pegawai sudah tidak bekerja.'),
                        ]),

                        Toggle::make('is_active')
                            ->label('Status Akun Aktif')
                            ->onColor('success')
                            ->offColor('danger')
                            ->default(true)
                            ->inline(false),
                    ]),

                // GROUP 2: KONTAK & PAJAK
                Section::make('Kontak & Identitas Pajak')
                    ->icon('heroicon-o-phone')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        TextInput::make('email')
                            ->label('Alamat Email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->prefixIcon('heroicon-m-envelope')
                            ->helperText('Email ini akan digunakan sebagai password default.'),

                        TextInput::make('phone')
                            ->label('Nomor Telepon / WhatsApp')
                            ->tel()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->prefixIcon('heroicon-m-device-phone-mobile'),

                        TextInput::make('kra_pin') // Sesuaikan nama kolom jika ingin diubah jadi 'npwp'
                            ->label('NPWP (Nomor Pajak)')
                            ->placeholder('Masukan nomor NPWP')
                            ->prefixIcon('heroicon-m-banknotes')
                            ->mask('99.999.999.9-999.999')
                            ->placeholder('XX.XXX.XXX.X-XXX.XXX')
                            ->columnSpanFull(),
                    ]),

                // GROUP 3: KONTAK DARURAT & KELUARGA (Digabung agar lebih hemat tempat)
                Section::make('Kontak Darurat & Keluarga')
                    ->icon('heroicon-o-lifebuoy')
                    ->collapsible()
                    ->collapsed() // Default tertutup agar tidak terlalu panjang
                    ->schema([
                        // Bagian Kiri: Kontak Darurat
                        Section::make('Kontak Darurat')
                            ->icon('heroicon-s-exclamation-triangle')
                            ->compact() // Tampilan lebih padat
                            ->schema([
                                TextInput::make('emergency_contact_name')
                                    ->label('Nama Kontak')
                                    ->prefixIcon('heroicon-m-user'),
                                TextInput::make('emergency_contact_phone')
                                    ->label('Nomor Telepon')
                                    ->tel()
                                    ->prefixIcon('heroicon-m-phone'),
                            ])->columnSpan(1),

                        // Bagian Kanan: Ahli Waris / Keluarga
                        Section::make('Keluarga Terdekat (Ahli Waris)')
                            ->icon('heroicon-s-user-group')
                            ->compact()
                            ->schema([
                                TextInput::make('next_of_kin_name')
                                    ->label('Nama Lengkap'),
                                TextInput::make('next_of_kin_relationship')
                                    ->label('Hubungan')
                                    ->placeholder('Istri, Suami, Ayah, dll'),
                                TextInput::make('next_of_kin_phone')
                                    ->label('Nomor Telepon')
                                    ->tel(),
                                TextInput::make('next_of_kin_email')
                                    ->label('Email (Opsional)')
                                    ->email(),
                            ])->columnSpan(1),
                    ]),
            ]);
    }
}
