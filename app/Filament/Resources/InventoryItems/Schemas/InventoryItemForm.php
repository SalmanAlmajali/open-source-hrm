<?php

namespace App\Filament\Resources\InventoryItems\Schemas;

use App\Filament\Resources\InventoryItems\Pages\CreateInventoryItem;
use App\Models\InventoryItem;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;

class InventoryItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identitas Barang & Kode')
                    ->description('Tentukan kode kategori dan nomor unik barang.')
                    ->schema([
                        Grid::make(2)->schema([
                            // 1. Pilih Kode Induk
                            Select::make('inventory_code_id')
                                ->label('Kategori Kode')
                                ->relationship('inventoryCode', 'code')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->createOptionForm([
                                    TextInput::make('code')
                                        ->required()
                                        ->unique('inventory_codes', 'code')
                                        ->label('Buat Kode Baru'),
                                ])
                                ->afterStateUpdated(function ($state, Set $set) {
                                    self::setUniqueID($state, $set);
                                })
                                ->live(debounce: '2s'),

                            // 2. Input Unique ID
                            TextInput::make('unique_id')
                                ->label('Nomor Unik')
                                ->required()
                                ->maxLength(50)
                                ->readOnly()
                                ->unique(InventoryItem::class, 'unique_id', ignoreRecord: true),

                            // 3. Nama Barang
                            TextInput::make('name')
                                ->label('Nama Barang')
                                ->required()
                                ->columnSpanFull(),

                            // 4. Tipe
                            Select::make('type')
                                ->label('Tipe')
                                ->options([
                                    'asset' => 'Aset Tetap',
                                    'consumable' => 'Habis Pakai',
                                ])
                                ->required(),

                            // 5. Kategori
                            Select::make('category')
                                ->label('Kategori')
                                ->options([
                                    'laptop' => 'Laptop',
                                    'proyektor' => 'Proyektor',
                                    'printer' => 'Printer',
                                    'alat uji' => 'Alat Uji',
                                    'lainnya' => 'Lainnya',
                                ])
                                ->placeholder('Pilih Kategori')
                                ->reactive()
                                ->afterStateUpdated(function ($state, Set $set) {
                                    if ($state !== 'lainnya') {
                                        $set('other_items', null);
                                    }
                                })
                                ->afterStateHydrated(function (Set $set, $state, $record) {
                                    if (!$record) return;

                                    $preset = ['laptop', 'proyektor', 'printer', 'alat uji'];
                                    $set('category', in_array($record->category, $preset) ? $record->category : 'lainnya');
                                })
                                ->required(),

                            TextInput::make('other_items')
                                ->label('Perlengkapan Kerja Lainnya')
                                ->placeholder('Tulis kategori manual...')
                                ->reactive()
                                ->hidden(fn($get) => $get('category') !== 'lainnya')
                                ->required(fn($get) => $get('category') === 'lainnya')
                                ->afterStateHydrated(function (Set $set, $record) {
                                    $preset = ['laptop', 'proyektor', 'printer', 'alat uji'];
                                    if ($record && !in_array($record->category, $preset)) {
                                        $set('other_items', $record->category);
                                    }
                                })
                                ->columnSpanFull(),
                        ]),
                    ]),

                Section::make('Detail')
                    ->description('Tuliskan informasi detail barang.')
                    ->schema([
                        TextInput::make('brand')
                            ->label('Merk'),
                        Select::make('inventory_location_id')
                            ->label('Lokasi Penyimpanan')
                            ->relationship('inventoryLocation', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Grid::make(3)->schema([
                            TextInput::make('stock')
                                ->numeric()
                                ->default(1)
                                ->label('Stok'),
                            TextInput::make('unit')
                                ->label('Satuan'),
                            Select::make('condition')
                                ->label('Kondisi')
                                ->options([
                                    'good' => 'Baik',
                                    'broken' => 'Rusak',
                                    'repair' => 'Perbaikan',
                                ])->default('good'),
                        ]),

                        Grid::make(2)->schema([
                            DatePicker::make('purchase_date')
                                ->label('Tanggal Pembelian'),
                            TextInput::make('price')
                                ->label('Harga')
                                ->mask(RawJs::make('$money($input)'))
                                ->stripCharacters([','])
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(999999999)
                                ->prefix('Rp. ')
                                ->required(),
                        ]),
                    ]),

                Section::make()
                    ->schema([
                        FileUpload::make('image_path')
                            ->label('Foto Barang')
                            ->image()
                            ->disk('public')
                            ->maxSize(5120) // 5MB
                            ->acceptedFileTypes(['image/jpeg', 'image/png'])
                            ->columnSpanFull()
                            ->required()
                            ->helperText('Format: JPG, PNG. Maks: 5MB')
                            ->saveUploadedFileUsing(function (TemporaryUploadedFile $file): string {
                                return (new InventoryItem)->uploadFile($file, 'inventory/items');
                            }),

                        RichEditor::make('description')
                            ->label('Catatan')
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull()
            ]);
    }

    public static function setUniqueID(string $inventoryCodeId, Set $set): void
    {
        // ambil nomor terbesar untuk inventory_code_id ini
        $lastNumber = InventoryItem::where('inventory_code_id', $inventoryCodeId)
            ->selectRaw('MAX(CAST(unique_id AS UNSIGNED)) as max_number')
            ->value('max_number');

        // menentukan nomor berikutnya
        $nextNumber = $lastNumber ? $lastNumber + 1 : 1;

        // nomro diawali dari 0 jadi 001
        $nomor = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        // menyimpan unique_id
        $set('unique_id', $nomor);
    }
}
