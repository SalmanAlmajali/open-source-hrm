<?php

namespace App\Filament\Resources\OutgoingLetters\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use App\Models\OutgoingLetter;

class OutgoingLetterForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Surat')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('reference_number')
                                ->label('Nomor Surat')
                                ->required()
                                ->unique(ignoreRecord: true),

                            DatePicker::make('letter_date')
                                ->label('Tanggal Surat')
                                ->default(now())
                                ->required(),
                        ]),

                        TextInput::make('recipient')
                            ->label('Tujuan (Instansi/Penerima)')
                            ->required()
                            ->columnSpanFull(),

                        TextInput::make('subject')
                            ->label('Perihal')
                            ->required()
                            ->columnSpanFull(),

                        Textarea::make('description')
                            ->label('Ringkasan Isi')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Section::make('Otorisasi & File')
                    ->schema([
                        Grid::make(2)->schema([
                            // Relasi ke Pegawai untuk Tanda Tangan
                            Select::make('signed_by')
                                ->label('Penandatangan')
                                ->relationship('signatory', 'name') // Pastikan Employee punya get 'name' atau gunakan kolom 'first_name'
                                ->searchable()
                                ->preload(),

                            Select::make('status')
                                ->label('Status Surat')
                                ->options([
                                    'draft' => 'Konsep (Draft)',
                                    'sent' => 'Terkirim',
                                    'archived' => 'Diarsipkan',
                                ])
                                ->default('draft')
                                ->required(),
                        ]),

                        FileUpload::make('file_path')
                            ->label('Arsip Surat Keluar')
                            ->acceptedFileTypes(['application/pdf', 'image/*'])
                            ->maxSize(10240)
                            ->downloadable()
                            ->columnSpanFull()
                            ->helperText('Format: PDF, JPG, PNG. Maks: 10MB')
                            ->saveUploadedFileUsing(function (TemporaryUploadedFile $file): string {
                                return (new OutgoingLetter)->uploadFile($file, 'letters/outgoing');
                            }),
                    ]),
            ]);
    }
}
