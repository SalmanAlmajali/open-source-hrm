<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectEmployeeRating extends Model
{
    protected $fillable = [
        'project_id',
        'employee_id',
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
}
