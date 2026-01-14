<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\Position;
use Illuminate\Auth\Access\Response;

class PositionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Employee $employee): bool
    {
        return $employee->can('view_any_positions');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Employee $employee, Position $position): bool
    {
        return $employee->can('view_positions');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Employee $employee): bool
    {
        return $employee->can('create_positions');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Employee $employee, Position $position): bool
    {
        return $employee->can('update_positions');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(Employee $employee): bool
    {
        return $employee->can('delete_any_positions');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Employee $employee, Position $position): bool
    {
        return $employee->can('delete_positions');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Employee $employee, Position $position): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Employee $employee, Position $position): bool
    {
        return false;
    }
}
