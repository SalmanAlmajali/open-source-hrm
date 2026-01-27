<?php

namespace App\Models;

use App\Filament\Resources\InventoryLocations\InventoryLocationResource;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class InventoryLocation extends Model
{
    use HasUuids;

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

        $filename = "/inventory/qr_locations/{$location->id}.png";

        // Pastikan folder ada
        if (!Storage::exists('inventory')) {
            Storage::makeDirectory('inventory');

            if (!Storage::exists('/inventory/qr_locations/')) {
                Storage::makeDirectory('/inventory/qr_locations/');
            }
        }

        $qrImage = QrCode::format('png')->size(300)->margin(1)->generate($url);
        Storage::put($filename, $qrImage);

        $location->qr_path = $filename;
    }

    public function items(): HasMany
    {
        return $this->hasMany(InventoryItem::class);
    }
}
