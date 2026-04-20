<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeProjectRating extends Model
{
    protected $fillable = [
        'project_id',
        'employee_id',
        'rated_by',
        'rating',
        'comments',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function ratedBy()
    {
        return $this->belongsTo(Employee::class, 'rated_by');
    }
}
