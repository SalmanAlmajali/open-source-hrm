<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Builder;

class CashTransaction extends Model
{
    use HasUuids;

    protected $fillable = [
        'cash_account_id',
        'type',
        'amount',
        'transaction_date',
        'category_id',
        'description',
        'reference_number',
        'transactionable_type',
        'transactionable_id',
        'transfer_to_account_id',
        'is_auto_generated',
        'recorded_by',
    ];

    protected $casts = [
        'amount'           => 'decimal:2',
        'transaction_date' => 'date',
        'is_auto_generated'=> 'boolean',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function cashAccount(): BelongsTo
    {
        return $this->belongsTo(CashAccount::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(CashTransactionCategory::class, 'category_id');
    }

    public function transactionable(): MorphTo
    {
        return $this->morphTo();
    }

    public function transferToAccount(): BelongsTo
    {
        return $this->belongsTo(CashAccount::class, 'transfer_to_account_id');
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'recorded_by');
    }

    // ─── Query Scopes ─────────────────────────────────────────────────────────

    public function scopeForPeriod(Builder $query, int $year, ?int $month = null): Builder
    {
        $query->whereYear('transaction_date', $year);
        if ($month) {
            $query->whereMonth('transaction_date', $month);
        }
        return $query;
    }

    public function scopeInflows(Builder $query): Builder
    {
        return $query->where('type', 'inflow');
    }

    public function scopeOutflows(Builder $query): Builder
    {
        return $query->where('type', 'outflow');
    }
}
