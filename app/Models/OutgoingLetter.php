<?php

namespace App\Models;

use App\DeletesUploadedFile;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OutgoingLetter extends Model
{
    use HasUuids, DeletesUploadedFile;

    protected $fillable = [
        'reference_number',
        'letter_date',
        'recipient',
        'subject',
        'description',
        'signed_by', // ID Pegawai
        'file_path',
        'status',
    ];

    protected $casts = [
        'letter_date' => 'date',
    ];

    // Relasi ke Penandatangan (Pegawai)
    public function signatory(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'signed_by');
    }

    protected function uploadAttributes(): array
    {
        return [
            'file_path'
        ];
    }
}
