<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectProgress extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'project_id',
        'employee_id',
        'subject',
        'description',
        'attachment',
        'hours_spent',
        'progress_date',
        'acknowledged_by',
        'acknowledged_at',
        'deleted_by',
    ];

    protected $casts = [
        'progress_date' => 'date',
        'hours_spent' => 'integer',
        'acknowledged_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function acknowledgedBy()
    {
        return $this->belongsTo(Employee::class, 'acknowledged_by');
    }

    public function deletedBy()
    {
        return $this->belongsTo(Employee::class, 'deleted_by');
    }
}
