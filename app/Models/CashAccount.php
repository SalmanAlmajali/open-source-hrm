<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashAccount extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'type',
        'opening_balance',
        'currency',
        'is_active',
        'default_for_payroll',
        'default_for_income',
        'notes',
    ];

    protected $casts = [
        'opening_balance'      => 'decimal:2',
        'is_active'            => 'boolean',
        'default_for_payroll'  => 'boolean',
        'default_for_income'   => 'boolean',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function transactions(): HasMany
    {
        return $this->hasMany(CashTransaction::class);
    }

    public function incomingTransfers(): HasMany
    {
        return $this->hasMany(CashTransaction::class, 'transfer_to_account_id');
    }

    public function receivables(): HasMany
    {
        return $this->hasMany(CashReceivable::class);
    }

    // ─── Computed Balance ─────────────────────────────────────────────────────

    public function currentBalance(): float
    {
        $inflows      = (float) $this->transactions()->where('type', 'inflow')->sum('amount');
        $outflows     = (float) $this->transactions()->where('type', 'outflow')->sum('amount');
        $transfersOut = (float) $this->transactions()->where('type', 'transfer')->sum('amount');
        $transfersIn  = (float) CashTransaction::where('transfer_to_account_id', $this->id)
            ->where('type', 'transfer')
            ->sum('amount');

        return (float) $this->opening_balance + $inflows - $outflows - $transfersOut + $transfersIn;
    }

    public function balanceForPeriod(int $year, ?int $month = null): array
    {
        $query = $this->transactions()->whereYear('transaction_date', $year);
        if ($month) {
            $query->whereMonth('transaction_date', $month);
        }

        $periodInflows  = (float) (clone $query)->where('type', 'inflow')->sum('amount');
        $periodOutflows = (float) (clone $query)->where('type', 'outflow')->sum('amount');

        // Opening = everything before this period
        $beforeQuery = $this->transactions()
            ->where('transaction_date', '<', \Carbon\Carbon::create($year, $month ?? 1, 1));
        $beforeIn  = (float) (clone $beforeQuery)->where('type', 'inflow')->sum('amount');
        $beforeOut = (float) (clone $beforeQuery)->where('type', 'outflow')->sum('amount');

        $opening = (float) $this->opening_balance + $beforeIn - $beforeOut;
        $closing = $opening + $periodInflows - $periodOutflows;

        return compact('opening', 'periodInflows', 'periodOutflows', 'closing');
    }

    // ─── Static Lookups ───────────────────────────────────────────────────────

    public static function getDefaultForPayroll(): ?self
    {
        return static::where('default_for_payroll', true)->where('is_active', true)->first();
    }

    public static function getDefaultForIncome(): ?self
    {
        return static::where('default_for_income', true)->where('is_active', true)->first();
    }
}
