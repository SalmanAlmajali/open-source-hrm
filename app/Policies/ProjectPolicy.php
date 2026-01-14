<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\Project;
use Illuminate\Auth\Access\Response;

class ProjectPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Employee $employee): bool
    {
        return $employee->can('view_any_projects');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Employee $employee, Project $project): bool
    {
        return $employee->can('view_projects');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Employee $employee): bool
    {
        return $employee->can('create_projects');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Employee $employee, Project $project): bool
    {
        return $employee->can('update_projects');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Employee $employee, Project $project): bool
    {
        return $employee->can('delete_projects');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Employee $employee, Project $project): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Employee $employee, Project $project): bool
    {
        return false;
    }
}
