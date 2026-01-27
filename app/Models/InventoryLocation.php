<?php

namespace App\Models;

use App\DeletesUploadedFile;
use App\Filament\Resources\InventoryLocations\InventoryLocationResource;
use App\QrCodeHelper;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class InventoryLocation extends Model
{
    use HasUuids, DeletesUploadedFile;

    protected $fillable = [
        'name',
        'description',
        'qr_path',
    ];

    protected $appends = [
        'qr_url'
    ];

    public function getQrUrlAttribute(): ?string
    {
        return $this->qr_path ? asset('storage/' . $this->qr_path) : null;
    }

    protected static function booted()
    {
        // Generate QR saat dibuat
        static::created(function ($location) {
            self::generateQr($location);
            $location->saveQuietly();
        });

        // Hapus file QR saat data dihapus
        static::deleting(function ($location) {
            if ($location->qr_path && Storage::exists($location->qr_path)) {
                Storage::delete($location->qr_path);
            }
        });
    }

    public static function generateQr($location)
    {
        // URL yang akan dibuka saat scan (Halaman Public/View)
        $url = InventoryLocationResource::getUrl('view', [
            'record' => $location
        ]);

        $filename = "/inventory/qr_locations/{$location->name}.png";

        QrCodeHelper::generate($url, $filename);

        $location->qr_path = $filename;
    }

    public function items(): HasMany
    {
        return $this->hasMany(InventoryItem::class);
    }

    protected function uploadAttributes(): array
    {
        return [
            'qr_path'
        ];
    }
}
