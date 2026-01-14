<?php

namespace App\Policies;

use App\Models\Employee;
use Illuminate\Auth\Access\Response;
use Spatie\Permission\Models\Permission;

class PermissionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Employee $employee): bool
    {
        return $employee->can('view_any_permissions');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Employee $employee, Permission $permission): bool
    {
        return $employee->can('view_permissions');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Employee $employee): bool
    {
        return $employee->can('create_permissions');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Employee $employee, Permission $permission): bool
    {
        return $employee->can('update_permissions');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Employee $employee, Permission $permission): bool
    {
        return $employee->can('delete_permissions');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Employee $employee, Permission $permission): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Employee $employee, Permission $permission): bool
    {
        return false;
    }
}
