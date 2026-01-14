<?php

namespace App\Policies;

use App\Models\Employee;
use Illuminate\Auth\Access\Response;

class EmployeePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Employee $employee): bool
    {
        return $employee->can('view_any_employees');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Employee $employee): bool
    {
        return $employee->can('view_employees');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Employee $employee): bool
    {
        return $employee->can('create_employees');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Employee $employee): bool
    {
        return $employee->can('update_employees');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(Employee $employee): bool
    {
        return $employee->can('delete_any_employees');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Employee $employee): bool
    {
        return $employee->can('delete_employees');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Employee $employee): bool
    {
        return $employee->can('restore_employees');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Employee $employee): bool
    {
        return $employee->can('force_delete_employees');
    }
}
