<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'offer_number',
        'offer_file_path',
        'spk_number',
        'spk_file_path',
        'spk_date',
        'plan_date',
        'contract_value',
        'tax_base',
        'vat',
        'vat_rate',
        'income_tax',
        'income_tax_rate',
        'flag_fee',
        'flag_fee_rate',
        'net_income',
        'profit',
    ];

    protected $appends = [
        'status',
    ];

    protected $casts = [
        'spk_date' => 'date',
        'plan_date' => 'date',
        'contract_value' => 'decimal:2',
        'tax_base' => 'decimal:2',
        'vat' => 'decimal:2',
        'net_income' => 'decimal:2',
    ];

    // Virtual Attribute untuk Status
    public function getStatusAttribute(): string
    {
        return $this->spk_number ? 'Realisasi' : 'Rencana';
    }
}
