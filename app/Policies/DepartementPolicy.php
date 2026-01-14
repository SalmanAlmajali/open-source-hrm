<?php

namespace App\Policies;

use App\Models\Department;
use App\Models\Employee;
use Illuminate\Auth\Access\Response;

class DepartementPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Employee $employee): bool
    {
        return $employee->can('view_any_departments');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Employee $employee, Department $department): bool
    {
        return $employee->can('view_departments');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Employee $employee): bool
    {
        return $employee->can('create_departments');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Employee $employee, Department $department): bool
    {
        return $employee->can('update_departments');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(Employee $employee): bool
    {
        return $employee->can('delete_any_departments');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Employee $employee, Department $department): bool
    {
        return $employee->can('delete_departments');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Employee $employee, Department $department): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Employee $employee, Department $department): bool
    {
        return false;
    }
}
