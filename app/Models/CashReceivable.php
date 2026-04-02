<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashReceivable extends Model
{
    use HasUuids;

    protected $fillable = [
        'project_id',
        'receivable_amount',
        'received_amount',
        'due_date',
        'received_date',
        'status',
        'notes',
        'cash_transaction_id',
        'cash_account_id',
    ];

    protected $casts = [
        'receivable_amount' => 'decimal:2',
        'received_amount'   => 'decimal:2',
        'due_date'          => 'date',
        'received_date'     => 'date',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function cashTransaction(): BelongsTo
    {
        return $this->belongsTo(CashTransaction::class);
    }

    public function cashAccount(): BelongsTo
    {
        return $this->belongsTo(CashAccount::class);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isReceived(): bool
    {
        return $this->status === 'received';
    }
}
