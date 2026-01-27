<?php

namespace App;

use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeHelper
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Generate dan simpan QR Code.
     *
     * @param string $content Isi data QR Code (URL/Text)
     * @param string $path Path penyimpanan (contoh: qrcodes/item-001.png)
     * @param string $disk Disk storage yang digunakan (default: public)
     * @return string Path file yang disimpan
     */
    public static function generate(string $content, string $path, string $disk = 'public'): string
    {
        // 1. Pastikan folder tujuan ada
        $directory = dirname($path);
        if (!Storage::disk($disk)->exists($directory)) {
            Storage::disk($disk)->makeDirectory($directory);
        }

        // 2. Generate Image QR (Format PNG, Size 300px)
        $qrImage = QrCode::format('png')
            ->size(300)
            ->margin(1) // Margin tipis biar rapi
            ->generate($content);

        // 3. Simpan ke Storage
        Storage::disk($disk)->put($path, $qrImage);

        return $path;
    }
}
