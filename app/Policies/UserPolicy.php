<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view any users.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('users.read');
    }

    /**
     * Determine if the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        return $user->hasPermission('users.view-details') && $user->canViewUser($model);
    }

    /**
     * Determine if the user can create users.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('users.create');
    }

    /**
     * Determine if the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        return $user->hasPermission('users.update') && $user->canManageUser($model);
    }

    /**
     * Determine if the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        return $user->hasPermission('users.delete') && 
               $user->canManageUser($model) && 
               $model->canBeDeleted();
    }

    /**
     * Determine if the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return $user->hasPermission('users.restore') && $user->canManageUser($model);
    }

    /**
     * Determine if the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return $user->hasPermission('users.force-delete') && $user->isSuperAdmin();
    }

    /**
     * Determine if the user can toggle user status.
     */
    public function toggleStatus(User $user, User $model): bool
    {
        return $user->hasPermission('users.toggle-status') && 
               $user->canManageUser($model) &&
               $user->id !== $model->id; // Cannot toggle own status
    }

    /**
     * Determine if the user can view trash.
     */
    public function viewTrash(User $user): bool
    {
        return $user->hasPermission('users.view-trash');
    }
}
