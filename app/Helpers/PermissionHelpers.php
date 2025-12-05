<?php

use App\Models\Role;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;


// ============================================================================
// HELPER FUNCTIONS
// ============================================================================

if (!function_exists('permission_exists')) {
    /**
     * Check if a permission exists by slug.
     */
    function permission_exists(string $slug, string $guard = 'web'): bool
    {
        return Permission::where('slug', $slug)
            ->where('guard_name', $guard)
            ->exists();
    }
}

if (!function_exists('create_permission')) {
    /**
     * Create a new permission.
     */
    function create_permission(
        string $name,
        string $resource,
        string $action,
        array $options = []
    ): Permission {
        $slug = "{$resource}-{$action}";

        return Permission::create([
            'name' => $name,
            'slug' => $slug,
            'resource' => $resource,
            'action' => $action,
            'description' => $options['description'] ?? null,
            'guard_name' => $options['guard_name'] ?? 'web',
            'category' => $options['category'] ?? null,
            'priority' => $options['priority'] ?? 0,
            'is_active' => $options['is_active'] ?? true,
            'is_system' => $options['is_system'] ?? false,
            'conditions' => $options['conditions'] ?? null,
        ]);
    }
}

if (!function_exists('assign_permission_to_role')) {
    /**
     * Assign a permission to a role.
     */
    function assign_permission_to_role(
        string $permissionSlug,
        string $roleSlug,
        array $pivotData = []
    ): void {
        $permission = Permission::where('slug', $permissionSlug)->firstOrFail();
        $role = Role::where('slug', $roleSlug)->firstOrFail();

        $role->givePermissionTo($permission, $pivotData);
    }
}

if (!function_exists('create_crud_permissions')) {
    /**
     * Create CRUD permissions for a resource.
     */
    function create_crud_permissions(string $resource, string $category = null): array
    {
        $actions = [
            'create' => 'Créer',
            'read' => 'Lire',
            'update' => 'Modifier',
            'delete' => 'Supprimer',
        ];

        $permissions = [];

        foreach ($actions as $action => $label) {
            $permissions[] = create_permission(
                "{$label} {$resource}",
                $resource,
                $action,
                ['category' => $category]
            );
        }

        return $permissions;
    }
}

if (!function_exists('user_can')) {
    /**
     * Check if the authenticated user has a permission.
     */
    function user_can(string $permission): bool
    {
        if (!auth()->check()) {
            return false;
        }

        /**
         * @var User $user
         */
        $user = auth()->user();

        return $user->hasPermissionTo($permission);
    }
}

if (!function_exists('role_has_permission')) {
    /**
     * Check if a role has a specific permission.
     */
    function role_has_permission(string $roleSlug, string $permissionSlug): bool
    {
        $role = Role::where('slug', $roleSlug)->first();

        if (!$role) {
            return false;
        }

        return $role->hasPermission($permissionSlug);
    }
}

if (!function_exists('get_permissions_by_category')) {
    /**
     * Get all permissions grouped by category.
     */
    function get_permissions_by_category(string $guard = 'web'): array
    {
        return Permission::active()
            ->where('guard_name', $guard)
            ->get()
            ->groupBy('category')
            ->toArray();
    }
}

if (!function_exists('sync_role_permissions')) {
    /**
     * Sync permissions for a role.
     */
    function sync_role_permissions(string $roleSlug, array $permissionSlugs): void
    {
        $role = Role::where('slug', $roleSlug)->firstOrFail();
        $permissions = Permission::whereIn('slug', $permissionSlugs)->pluck('id')->toArray();

        $role->syncPermissions($permissions);
    }
}

if (!function_exists('cleanup_expired_permissions')) {
    /**
     * Deactivate all expired permissions in role_permissions.
     */
    function cleanup_expired_permissions(): int
    {
        return DB::table('role_permissions')
            ->whereNotNull('expire_le')
            ->where('expire_le', '<=', now())
            ->where('actif', true)
            ->update([
                'actif' => false,
                'updated_at' => now(),
            ]);
    }
}


