<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class InventoryItem extends Model
{
    use HasUuids;

    protected $fillable = [
        'inventory_code_id',
        'unique_id',
        'name',
        'brand',
        'type', // asset / consumable
        'category',
        'stock',
        'unit',
        'condition',
        'purchase_date',
        'price',
        'location',
        'image_path',
        'description',
        'qr_path',
    ];

    protected $appends = [
        'full_code',
        'qr_url',
        'image_url'
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'price' => 'decimal:2',
    ];

    public function getImageUrlAttribute()
    {
        if (!$this->image_path) {
            return null;
        }

        // Pastikan path 'storage/' hanya sekali
        $path = str_starts_with($this->image_path, 'items/')
            ? $this->image_path
            : "items/{$this->image_path}";

        // Gunakan url() supaya sesuai dengan host (APP_URL)
        return url("storage/{$path}");
    }

    public function getFullCodeAttribute()
    {
        // Ambil kode dari relasi
        $kodeUtama = $this->inventoryCode?->code ?? 'NON';
        // Gabungkan: KODE-001
        return $kodeUtama . '-' . $this->unique_id;
    }

    public function getQrUrlAttribute()
    {
        return $this->qr_path ? asset('storage/' . $this->qr_path) : null;
    }

    protected static function booted()
    {
        static::created(function ($item) {
            self::generateQr($item);
            $item->saveQuietly();
        });

        static::deleting(function ($item) {
            $isNotAvailable = $item->inventoryLoans()
                ->whereIn('status', ['approved', 'borrowed'])
                ->exists();

            if ($isNotAvailable) {
                throw new Exception('Item ini sedang dipinjam dan tidak dapat dihapus.');
            }

            if ($item->qr_path && Storage::exists($item->qr_path)) {
                Storage::delete($item->qr_path);
            }

            if ($item->image_path && Storage::exists($item->image_path)) {
                Storage::delete($item->image_path);
            }
        });
    }

    /**
     * Generate QR code
     */
    public static function generateQr($item)
    {
        $qrContent = env('APP_URL') . '/inventory/' . $item->id;

        $kodeUtama = optional($item->inventoryCode)->code ?? 'UNKNOWN';
        $filename  = "qr_codes/{$kodeUtama}-{$item->unique_id}.png";

        $qrImage = QrCode::format('png')->size(300)->generate($qrContent);
        Storage::put($filename, $qrImage);

        $item->qr_path = $filename;
    }

    public function inventoryCode(): BelongsTo
    {
        // Perbaikan typo foreign key
        return $this->belongsTo(InventoryCode::class, 'inventory_code_id');
    }

    public function inventoryLoans()
    {
        return $this->hasMany(InventoryLoan::class, 'item_id');
    }
}
