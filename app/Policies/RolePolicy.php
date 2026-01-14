<?php

namespace App\Policies;

use App\Models\Employee;
use Illuminate\Auth\Access\Response;
use Spatie\Permission\Models\Role;

class RolePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Employee $employee): bool
    {
        return $employee->can('view_any_roles');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Employee $employee, Role $role): bool
    {
        return $employee->can('view_roles');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Employee $employee): bool
    {
        return $employee->can('create_roles');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Employee $employee, Role $role): bool
    {
        return $employee->can('update_roles');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(Employee $employee): bool
    {
        return $employee->can('delete_any_roles');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Employee $employee, Role $role): bool
    {
        return $employee->can('delete_roles');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Employee $employee, Role $role): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Employee $employee, Role $role): bool
    {
        return false;
    }
}
