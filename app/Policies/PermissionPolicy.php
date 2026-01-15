<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Permission;
use Illuminate\Auth\Access\HandlesAuthorization;

class PermissionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view any permissions.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('role_permissions.read');
    }

    /**
     * Determine if the user can view the permission.
     */
    public function view(User $user, Permission $permission): bool
    {
        return $user->hasPermission('role_permissions.view-details');
    }

    /**
     * Determine if the user can create permissions.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('role_permissions.create');
    }

    /**
     * Determine if the user can update the permission.
     */
    public function update(User $user, Permission $permission): bool
    {
        // Ne peut pas modifier une permission système (sauf Super Admin)
        if ($permission->is_system && !$user->isSuperAdmin()) {
            return false;
        }

        return $user->hasPermission('role_permissions.update');
    }

    /**
     * Determine if the user can delete the permission.
     */
    public function delete(User $user, Permission $permission): bool
    {
        // Ne peut jamais supprimer une permission système
        if ($permission->is_system) {
            return false;
        }

        return $user->hasPermission('role_permissions.delete');
    }

    /**
     * Determine if the user can activate/deactivate the permission.
     */
    public function toggleStatus(User $user, Permission $permission): bool
    {
        return $user->hasPermission('role_permissions.update');
    }
}
