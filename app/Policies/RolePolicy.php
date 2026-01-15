<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Role;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view any roles.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('roles.read');
    }

    /**
     * Determine if the user can view the role.
     */
    public function view(User $user, Role $role): bool
    {
        return $user->hasPermission('roles.view-details');
    }

    /**
     * Determine if the user can create roles.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('roles.create');
    }

    /**
     * Determine if the user can update the role.
     */
    public function update(User $user, Role $role): bool
    {
        // Ne peut pas modifier un rôle de niveau égal ou supérieur
        if (!$user->isSuperAdmin() && $role->level >= $user->role->level) {
            return false;
        }

        return $user->hasPermission('roles.update');
    }

    /**
     * Determine if the user can delete the role.
     */
    public function delete(User $user, Role $role): bool
    {
        // Ne peut pas supprimer un rôle système
        if ($role->is_system) {
            return false;
        }

        // Ne peut pas supprimer un rôle de niveau égal ou supérieur
        if (!$user->isSuperAdmin() && $role->level >= $user->role->level) {
            return false;
        }

        return $user->hasPermission('roles.delete');
    }

    /**
     * Determine if the user can manage permissions for the role.
     */
    public function managePermissions(User $user, Role $role): bool
    {
        // Ne peut pas gérer les permissions d'un rôle de niveau égal ou supérieur
        if (!$user->isSuperAdmin() && $role->level >= $user->role->level) {
            return false;
        }

        return $user->hasPermission('roles.manage');
    }

    /**
     * Determine if the user can duplicate the role.
     */
    public function duplicate(User $user, Role $role): bool
    {
        return $user->hasPermission('roles.duplicate');
    }
}
