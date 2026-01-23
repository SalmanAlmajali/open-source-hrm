<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryLoan extends Model
{
    use HasUuids;

    protected $fillable = [
        'employee_id',
        'item_id',
        'amount',
        'loan_date',
        'return_date',
        'status',
        'reason',
        'admin_notes',
        'approved_by',
        'return_picture',
    ];

    protected $appends = [
        'return_picture_url',
    ];

    public function getReturnPictureUrlAttribute()
    {
        return $this->return_picture ? asset('storage/'.$this->return_picture) : null;
    }

    public function employee(): BelongsTo {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function item(): BelongsTo {
        return $this->belongsTo(InventoryItem::class, 'item_id');
    }

    public function approvedBy(): BelongsTo {
        return $this->belongsTo(Employee::class, 'approved_by');
    }
}
