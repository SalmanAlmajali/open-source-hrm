<?php

namespace App\Filament\Resources\Projects\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Zvizvi\UserFields\Components\UserSelect;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // BAGIAN 1: DETAIL PROYEK (Kiri)
                Group::make()
                    ->schema([
                        Section::make('Identitas Proyek')
                            ->description('Informasi dasar mengenai pekerjaan.')
                            ->icon('heroicon-o-briefcase')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nama Proyek')
                                    ->required()
                                    ->placeholder('Contoh: Renovasi Gedung A')
                                    ->columnSpanFull(),

                                TextInput::make('offer_number')
                                    ->label('No. Surat Penawaran')
                                    ->prefixIcon('heroicon-m-document-text'),

                                DatePicker::make('plan_date')
                                    ->label('Tanggal Rencana')
                                    ->default(now())
                                    ->required(),

                                // FILE UPLOAD PENAWARAN
                                FileUpload::make('offer_file_path')
                                    ->label('Upload Dokumen Penawaran')
                                    ->directory('project-offers') // Folder penyimpanan
                                    ->acceptedFileTypes(['application/pdf', 'image/*']) // Hanya PDF & Gambar
                                    ->maxSize(10240) // Maks 5MB
                                    ->downloadable()
                                    ->openable()
                                    ->columnSpanFull()
                                    ->helperText('Format: PDF, JPG, PNG. Maks: 10MB'),

                                UserSelect::make('employees')
                                    ->label('Penanggung Jawab')
                                    ->relationship(
                                        name: 'employees',
                                        // modifyQueryUsing: fn (Builder $query) => $query->orderBy('first_name')->orderBy('last_name'),
                                    ) // Mengambil nama dari tabel employees
                                    ->multiple() // Memungkinkan pilih lebih dari satu
                                    ->preload() // Memuat data di awal agar user mudah mencari
                                    // ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->first_name} {$record->last_name}")
                                    ->searchable(['first_name', 'last_name']),

                                RichEditor::make('notes')
                                    ->label('Catatan Proyek')
                                    ->placeholder('Tambahkan catatan khusus terkait proyek ini...')
                                    ->columnSpanFull()
                            ])->columns(2),

                        Section::make('Legalitas (SPK)')
                            ->description('Isi jika proyek sudah Deal/Realisasi.')
                            ->icon('heroicon-o-check-badge')
                            ->schema([
                                TextInput::make('spk_number')
                                    ->label('Nomor SPK')
                                    ->placeholder('Kosongkan jika masih Rencana'),

                                DatePicker::make('spk_date')
                                    ->label('Tanggal SPK'),

                                // FILE UPLOAD SPK
                                FileUpload::make('spk_file_path')
                                    ->label('Upload Dokumen SPK')
                                    ->directory('project-spk') // Folder penyimpanan
                                    ->acceptedFileTypes(['application/pdf', 'image/*'])
                                    ->maxSize(10240) // Maks 10MB
                                    ->downloadable()
                                    ->openable()
                                    ->columnSpanFull()
                                    ->helperText('Upload scan SPK yang sudah ditandatangani. Format: PDF, JPG, PNG. Maks: 5MB'),
                            ])->columns(2),
                    ])->columnSpan(2),

                // BAGIAN 2: KALKULATOR KEUANGAN (Kanan)
                Group::make()
                    ->schema([
                        Section::make('Kalkulasi Pendapatan')
                            ->icon('heroicon-o-calculator')
                            ->schema([
                                // INPUT NILAI KONTRAK (Masking Rupiah)
                                TextInput::make('contract_value')
                                    ->label('Nilai Kontrak (Bruto)')
                                    ->required()
                                    ->prefix('Rp')
                                    ->default(0)
                                    // 1. MASKING: Ubah input jadi format uang (Titik ribuan, Koma desimal)
                                    ->mask(RawJs::make('$money($input)'))
                                    // 2. CLEANSING: Saat tombol Save ditekan, bersihkan formatnya agar masuk DB
                                    ->dehydrateStateUsing(fn($state) => self::parseMoney($state))
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        self::calculateFinancials($get, $set);
                                    }),

                                // INPUT PERSENTASE
                                Grid::make(3)->schema([
                                    TextInput::make('vat_rate')
                                        ->label('PPN')
                                        ->numeric()
                                        ->default(11)
                                        ->suffix('%')
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(fn(Get $get, Set $set) => self::calculateFinancials($get, $set)),

                                    TextInput::make('income_tax_rate')
                                        ->label('PPh')
                                        ->numeric()
                                        ->default(2)
                                        ->suffix('%')
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(fn(Get $get, Set $set) => self::calculateFinancials($get, $set)),

                                    TextInput::make('flag_fee_rate')
                                        ->label('Fee')
                                        ->numeric()
                                        ->default(0)
                                        ->suffix('%')
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(fn(Get $get, Set $set) => self::calculateFinancials($get, $set)),
                                ]),

                                // HASIL (Read Only & Masked)
                                Group::make()
                                    ->schema([
                                        TextInput::make('tax_base') // DPP
                                            ->label('DPP')
                                            ->readOnly()
                                            ->prefix('Rp')
                                            ->mask(RawJs::make('$money($input)'))
                                            ->dehydrateStateUsing(fn($state) => self::parseMoney($state)),

                                        TextInput::make('vat')
                                            ->label('PPN (Bayar Negara)')
                                            ->readOnly()
                                            ->prefix('Rp')
                                            ->mask(RawJs::make('$money($input)'))
                                            ->dehydrateStateUsing(fn($state) => self::parseMoney($state)),

                                        TextInput::make('income_tax')
                                            ->label('PPh (Potongan)')
                                            ->readOnly()
                                            ->prefix('Rp')
                                            ->mask(RawJs::make('$money($input)'))
                                            ->dehydrateStateUsing(fn($state) => self::parseMoney($state)),

                                        TextInput::make('flag_fee')
                                            ->label('Fee Bendera')
                                            ->readOnly()
                                            ->prefix('Rp')
                                            ->mask(RawJs::make('$money($input)'))
                                            ->dehydrateStateUsing(fn($state) => self::parseMoney($state)),

                                        TextInput::make('net_income')
                                            ->label('Net Income (Masuk Kas)')
                                            ->readOnly()
                                            ->prefix('Rp')
                                            ->mask(RawJs::make('$money($input)'))
                                            ->dehydrateStateUsing(fn($state) => self::parseMoney($state))
                                            ->extraInputAttributes(['class' => 'font-bold text-success-600 text-lg']),
                                    ]),
                            ]),
                    ])->columnSpan(1),
            ])->columns(3);
    }

    // 1. Helper untuk mengubah "1.000.000,00" menjadi float "1000000.00"
    public static function parseMoney($value): float
    {
        if (!$value) return 0;

        // Hapus titik (.) ribuan, ganti koma (,) jadi titik (.) desimal
        return (float) str_replace([',', ','], ['', '.'], (string) $value);
    }

    // 2. Kalkulasi ulang menggunakan data bersih
    public static function calculateFinancials(Get $get, Set $set): void
    {
        // 1. Ambil nilai & bersihkan format
        $contractValue = self::parseMoney($get('contract_value'));

        $vatRate = floatval($get('vat_rate') ?? 0);
        $incomeTaxRate = floatval($get('income_tax_rate') ?? 0);
        $flagRate = floatval($get('flag_fee_rate') ?? 0);

        if ($contractValue > 0) {
            // --- LOGIKA PERHITUNGAN ---

            // Rumus: DPP = Nilai / (1 + PPN%)
            // Contoh: 1.110.000 / 1.11 = 1.000.000
            $taxBase = $contractValue / (1 + ($vatRate / 100));

            $vat = $contractValue - $taxBase;
            $incomeTax = $taxBase * ($incomeTaxRate / 100);
            $flagFee = $contractValue * ($flagRate / 100);

            $net = $contractValue - $vat - $incomeTax - $flagFee;

            // --- PERBAIKAN DI SINI (ROUNDING) ---
            // Kita bulatkan hasil perhitungan ke 2 desimal agar rapi
            // Masking JS akan otomatis menambahkan titik ribuan lagi

            $set('tax_base', round($taxBase, 2));
            $set('vat', round($vat, 2));
            $set('income_tax', round($incomeTax, 2));
            $set('flag_fee', round($flagFee, 2));
            $set('net_income', round($net, 2));
        } else {
            // Reset jika input 0 atau kosong
            $set('tax_base', 0);
            $set('vat', 0);
            $set('income_tax', 0);
            $set('flag_fee', 0);
            $set('net_income', 0);
        }
    }
}
