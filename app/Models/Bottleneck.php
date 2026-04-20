<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bottleneck extends Model
{
    protected $fillable = [
        'project_id',
        'reported_by',
        'subject',
        'description',
        'attachment',
        'status',
        'acknowledged_by',
        'acknowledged_at',
        'resolution_notes',
        'resolved_at',
        'resolved_by',
    ];

    protected $casts = [
        'status' => 'string',
        'acknowledged_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function reportedBy()
    {
        return $this->belongsTo(Employee::class, 'reported_by');
    }

    public function acknowledgedBy()
    {
        return $this->belongsTo(Employee::class, 'acknowledged_by');
    }

    public function resolvedBy()
    {
        return $this->belongsTo(Employee::class, 'resolved_by');
    }
}
