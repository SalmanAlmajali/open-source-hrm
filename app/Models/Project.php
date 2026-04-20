<?php

namespace App\Models;

use App\Traits\DeletesUploadedFile;
use App\Traits\FileUploadTrait;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Project extends Model
{
    use HasUuids, DeletesUploadedFile, FileUploadTrait;

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

    protected function uploadAttributes(): array
    {
        return [
            'offer_file_path',
            'spk_file_path',
        ];
    }

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'employee_project');
    }

    public function receivable(): HasOne
    {
        return $this->hasOne(CashReceivable::class);
    }

    public function progressLogs()
    {
        return $this->hasMany(ProjectProgress::class);
    }

    public function bottlenecks()
    {
        return $this->hasMany(Bottleneck::class);
    }

    // Ratings from employees about this project
    public function ratingsFromEmployees()
    {
        return $this->hasMany(ProjectEmployeeRating::class);
    }

    // Stakeholder ratings of employees on this project
    public function employeeRatings()
    {
        return $this->hasMany(EmployeeProjectRating::class);
    }
}
