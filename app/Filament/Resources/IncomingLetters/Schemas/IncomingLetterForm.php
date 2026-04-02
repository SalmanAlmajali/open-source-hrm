<?php

namespace App\Filament\Resources\IncomingLetters\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use App\Models\IncomingLetter;

class IncomingLetterForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identitas Surat')
                    ->description('Detail surat yang diterima dari pihak eksternal.')
                    ->schema([
                        TextInput::make('reference_number')
                            ->label('Nomor Surat (Eksternal)')
                            ->placeholder('No. surat dari pengirim')
                            ->required(),

                        Grid::make(2)->schema([
                            DatePicker::make('letter_date')
                                ->label('Tanggal Surat')
                                ->required(),

                            DatePicker::make('received_date')
                                ->label('Tanggal Diterima')
                                ->default(now())
                                ->required(),
                        ]),

                        Grid::make(2)->schema([
                            TextInput::make('sender')
                                ->label('Pengirim')
                                ->prefixIcon('heroicon-m-building-office-2')
                                ->required(),

                            TextInput::make('recipient')
                                ->label('Ditujukan Kepada')
                                ->placeholder('Contoh: Kepala Dinas / Direktur'),
                        ]),
                    ]),

                Section::make('Isi & Berkas')
                    ->schema([
                        TextInput::make('subject')
                            ->label('Perihal')
                            ->columnSpanFull()
                            ->required(),

                        Textarea::make('description')
                            ->label('Ringkasan Isi')
                            ->rows(3)
                            ->columnSpanFull(),

                        Select::make('status')
                            ->label('Status Disposisi')
                            ->options([
                                'received' => 'Diterima (Baru)',
                                'processed' => 'Sedang Diproses',
                                'archived' => 'Diarsipkan',
                            ])
                            ->default('received')
                            ->required(),

                        FileUpload::make('file_path')
                            ->label('Upload Scan Surat')
                            ->acceptedFileTypes(['application/pdf', 'image/*'])
                            ->maxSize(10240) // 10MB
                            ->downloadable()
                            ->openable()
                            ->columnSpanFull()
                            ->helperText('Format: PDF, JPG, PNG. Maks: 10MB')
                            ->saveUploadedFileUsing(function (TemporaryUploadedFile $file): string {
                                return (new IncomingLetter)->uploadFile($file, 'letters/incoming');
                            }),
                    ]),
            ]);
    }
}
