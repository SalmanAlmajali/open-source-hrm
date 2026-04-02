<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashTransactionCategory extends Model
{
    use HasUuids;

    protected $fillable = ['name', 'type', 'color'];

    public function transactions(): HasMany
    {
        return $this->hasMany(CashTransaction::class, 'category_id');
    }

    public static function findByName(string $name): ?self
    {
        return static::where('name', $name)->first();
    }
}
