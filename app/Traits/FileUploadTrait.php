<?php

namespace App\Traits;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

trait FileUploadTrait
{
    /**
     * Upload file dan otomatis konversi ke WebP (jika file berupa gambar)
     */
    public function uploadFile($file, string $folder): string
    {
        try {
            $originalExtension = $file->getClientOriginalExtension();
            $baseName = Str::of($file->hashName())->basename(".{$originalExtension}");
            $mimeType = $file->getMimeType();

            // Jika file adalah gambar, konversi ke WebP menggunakan Intervention
            if (str_starts_with($mimeType, 'image/')) {
                $manager = new ImageManager(Driver::class);
                $image = $manager->read($file);
                $encodedFile = $image->encode(new WebpEncoder());
                
                $path = "{$folder}/{$baseName}.webp";
                Storage::disk('public')->put($path, $encodedFile);
            } else {
                // Jika file BUKAN gambar (misal PDF, DOCX), simpan aslinya
                $path = "{$folder}/{$baseName}.{$originalExtension}";
                Storage::disk('public')->put($path, file_get_contents($file));
            }

            return $path;
        } catch (Exception $e) {
            throw new Exception("Gagal mengupload file: " . $e->getMessage());
        }
    }

    /**
     * Hapus file dari storage
     */
    public function deleteFile(?string $path): void
    {
        try {
            if ($path && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        } catch (Exception $e) {
            throw new Exception("Gagal menghapus file: " . $e->getMessage());
        }
    }

    /**
     * Download file dari storage
     */
    public function downloadFile(string $path, string $fileName = null): BinaryFileResponse
    {
        if (Storage::disk('public')->exists($path)) {
            $absolutePath = Storage::disk('public')->path($path);
            return response()->download($absolutePath, $fileName);
        }

        throw new Exception("File tidak ditemukan di server.");
    }
}