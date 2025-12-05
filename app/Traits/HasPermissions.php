<?php

namespace App\Traits;

use App\Models\Permission;
use App\Models\Role;

/**
 * Trait HasPermissions
 *
 * Ajoute des fonctionnalités de gestion des permissions aux modèles.
 */
trait HasPermissions
{
    /**
     * Check if the user has permission through their role.
     */
    public function hasPermissionTo(string $permission): bool
    {
        if (!$this->role) {
            return false;
        }

        return $this->role->hasPermission($permission);
    }

    /**
     * Check if user can perform action on resource.
     */
    public function canDo(string $action, string $resource): bool
    {
        $permissionSlug = "{$resource}-{$action}";
        return $this->hasPermissionTo($permissionSlug);
    }

    /**
     * Get all permission slugs for the user.
     */
    public function getPermissionSlugs(): array
    {
        if (!$this->role) {
            return [];
        }

        return $this->role->activePermissions()
            ->pluck('permissions.slug')
            ->toArray();
    }

    /**
     * Check multiple permissions with AND logic.
     */
    public function hasAllPermissions(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermissionTo($permission)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Check multiple permissions with OR logic.
     */
    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermissionTo($permission)) {
                return true;
            }
        }
        return false;
    }
}



