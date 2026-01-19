<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class IncomingLetter extends Model
{
    use HasUuids;

    protected $fillable = [
        'reference_number',
        'letter_date',
        'received_date',
        'sender',
        'recipient',
        'subject',
        'description',
        'file_path',
        'status',
    ];

    protected $casts = [
        'letter_date' => 'date',
        'received_date' => 'date',
    ];
}
