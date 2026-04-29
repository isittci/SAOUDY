<?php

namespace App\Traits;

use App\Models\Permission;
use Illuminate\Support\Collection;

trait HasPermissions
{
    /**
     * Vérifie si l'utilisateur possède une permission spécifique.
     *
     * @param string $permission Slug de la permission
     * @return bool
     */
    public function hasPermission(string $permission): bool
    {
        // Le Super Admin a toutes les permissions automatiquement
        if ($this->isSuperAdmin()) {
            return true;
        }

        // Vérifier si le rôle possède cette permission
        return $this->role
            && $this->role->permissions()
                ->where('permissions.slug', $permission)
                ->where('permissions.is_active', true)
                ->where('role_permissions.actif', true)
                ->where(function ($query) {
                    $query->whereNull('role_permissions.expire_le')
                        ->orWhere('role_permissions.expire_le', '>', now());
                })
                ->exists();
    }

    /**
     * Vérifie si l'utilisateur possède au moins une des permissions.
     *
     * @param array $permissions Liste des slugs de permissions
     * @return bool
     */
    public function hasAnyPermission(array $permissions): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Vérifie si l'utilisateur possède toutes les permissions.
     *
     * @param array $permissions Liste des slugs de permissions
     * @return bool
     */
    public function hasAllPermissions(array $permissions): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Vérifie si l'utilisateur est Super Administrateur.
     *
     * @return bool
     */
    public function isSuperAdmin(): bool
    {
        return $this->role && $this->role->level === 100;
    }

    /**
     * Vérifie si l'utilisateur est Administrateur (niveau >= 80).
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->role && $this->role->level >= 80;
    }

    /**
     * Vérifie si l'utilisateur peut gérer un autre utilisateur.
     * Règle : on peut gérer les utilisateurs de niveau strictement inférieur.
     *
     * @param \App\Models\User $user
     * @return bool
     */
    public function canManageUser($user): bool
    {
        // Super Admin peut tout gérer
        if ($this->isSuperAdmin()) {
            return true;
        }

        // On ne peut pas gérer soi-même via cette méthode
        if ($this->id === $user->id) {
            return false;
        }

        // On peut gérer les utilisateurs de niveau strictement inférieur
        return $this->role && $user->role && $this->role->level > $user->role->level;
    }

    /**
     * Vérifie si l'utilisateur peut voir un autre utilisateur.
     * Règle : on peut voir les utilisateurs de niveau inférieur ou égal.
     *
     * @param \App\Models\User $user
     * @return bool
     */
    public function canViewUser($user): bool
    {
        // Super Admin peut tout voir
        if ($this->isSuperAdmin()) {
            return true;
        }

        // On peut toujours se voir soi-même
        if ($this->id === $user->id) {
            return true;
        }

        // On peut voir les utilisateurs de niveau inférieur ou égal
        return $this->role && $user->role && $this->role->level >= $user->role->level;
    }

    /**
     * Récupère toutes les permissions actives de l'utilisateur via son rôle.
     *
     * @return Collection
     */
    public function getActivePermissions(): Collection
    {
        if ($this->isSuperAdmin()) {
            return Permission::where('is_active', true)->get();
        }

        if (!$this->role) {
            return collect();
        }

        return $this->role->permissions()
            ->where('permissions.is_active', true)
            ->where('role_permissions.actif', true)
            ->where(function ($query) {
                $query->whereNull('role_permissions.expire_le')
                    ->orWhere('role_permissions.expire_le', '>', now());
            })
            ->get();
    }

    /**
     * Récupère les permissions groupées par module.
     *
     * @return Collection
     */
    public function getPermissionsByModule(): Collection
    {
        return $this->getActivePermissions()
            ->groupBy('module')
            ->sortKeys();
    }
}
