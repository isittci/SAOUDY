<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use App\Models\RolePermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RolePermissionController extends Controller
{
    /**
     * Afficher la matrice des attributions de permissions.
     */
    public function index(Request $request)
    {
        $roles = Role::withCount('permissions')
            ->byLevel('desc')
            ->get();

        $permissions = Permission::active()
            ->ordered()
            ->get()
            ->groupBy('category');

        // Construire la matrice
        $matrix = [];
        foreach ($roles as $role) {
            $rolePermissionIds = $role->permissions()->pluck('permissions.id')->toArray();
            $matrix[$role->id] = $rolePermissionIds;
        }

        return view('role-permissions.index', compact('roles', 'permissions', 'matrix'));
    }

    /**
     * Afficher les détails d'une attribution.
     */
    public function show(string $roleId, string $permissionId)
    {
        $rolePermission = RolePermission::where('role_id', $roleId)
            ->where('permission_id', $permissionId)
            ->with(['role', 'permission', 'attributeur'])
            ->firstOrFail();

        return view('role-permissions.show', compact('rolePermission'));
    }

    /**
     * Afficher le formulaire d'édition d'une attribution.
     */
    public function edit(string $roleId, string $permissionId)
    {
        $rolePermission = RolePermission::where('role_id', $roleId)
            ->where('permission_id', $permissionId)
            ->with(['role', 'permission'])
            ->firstOrFail();

        return view('role-permissions.edit', compact('rolePermission'));
    }

    /**
     * Mettre à jour une attribution.
     */
    public function update(Request $request, string $roleId, string $permissionId)
    {
        $request->validate([
            'expire_le' => 'nullable|date|after:today',
            'actif' => 'boolean',
            'notes' => 'nullable|string|max:1000',
            'conditions' => 'nullable|array',
        ]);

        try {
            DB::table('role_permissions')
                ->where('role_id', $roleId)
                ->where('permission_id', $permissionId)
                ->update([
                    'expire_le' => $request->expire_le,
                    'actif' => $request->boolean('actif', true),
                    'notes' => $request->notes,
                    'conditions' => $request->conditions ? json_encode($request->conditions) : null,
                    'updated_by' => auth()->id(),
                    'updated_at' => now(),
                ]);

            return back()->with('success', 'L\'attribution a été mise à jour avec succès.');

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    /**
     * Supprimer une attribution.
     */
    public function destroy(string $roleId, string $permissionId)
    {
        try {
            $role = Role::findOrFail($roleId);
            $permission = Permission::findOrFail($permissionId);

            $role->revokePermission($permission);

            return back()->with('success', 'L\'attribution a été supprimée avec succès.');

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    /**
     * Activer/Désactiver une attribution.
     */
    public function toggleStatus(string $roleId, string $permissionId)
    {
        try {
            $rolePermission = RolePermission::where('role_id', $roleId)
                ->where('permission_id', $permissionId)
                ->firstOrFail();

            $rolePermission->toggleStatus();

            $status = $rolePermission->actif ? 'activée' : 'désactivée';

            return back()->with('success', "L'attribution a été {$status} avec succès.");

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors du changement de statut: ' . $e->getMessage());
        }
    }

    /**
     * Attribuer plusieurs permissions à un rôle.
     */
    public function bulkAssign(Request $request)
    {
        $request->validate([
            'role_id' => 'required|uuid|exists:roles,id',
            'permission_ids' => 'required|array',
            'permission_ids.*' => 'uuid|exists:permissions,id',
            'expire_le' => 'nullable|date|after:today',
        ]);

        try {
            DB::beginTransaction();

            $role = Role::findOrFail($request->role_id);

            foreach ($request->permission_ids as $permissionId) {
                // Vérifier si l'attribution existe déjà
                $exists = $role->permissions()->where('permissions.id', $permissionId)->exists();

                if (!$exists) {
                    $permission = Permission::findOrFail($permissionId);
                    $role->givePermission($permission, auth()->id(), [
                        'expire_le' => $request->expire_le,
                    ]);
                }
            }

            DB::commit();

            return back()->with('success', count($request->permission_ids) . ' permission(s) attribuée(s) avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de l\'attribution: ' . $e->getMessage());
        }
    }

    /**
     * Retirer plusieurs permissions d'un rôle.
     */
    public function bulkRevoke(Request $request)
    {
        $request->validate([
            'role_id' => 'required|uuid|exists:roles,id',
            'permission_ids' => 'required|array',
            'permission_ids.*' => 'uuid|exists:permissions,id',
        ]);

        try {
            DB::beginTransaction();

            $role = Role::findOrFail($request->role_id);

            foreach ($request->permission_ids as $permissionId) {
                $permission = Permission::find($permissionId);
                if ($permission) {
                    $role->revokePermission($permission);
                }
            }

            DB::commit();

            return back()->with('success', count($request->permission_ids) . ' permission(s) retirée(s) avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors du retrait: ' . $e->getMessage());
        }
    }

    /**
     * Prolonger l'expiration d'une attribution.
     */
    public function extendExpiration(Request $request, string $roleId, string $permissionId)
    {
        $request->validate([
            'days' => 'required|integer|min:1|max:365',
        ]);

        try {
            $rolePermission = RolePermission::where('role_id', $roleId)
                ->where('permission_id', $permissionId)
                ->firstOrFail();

            $rolePermission->extendExpiration($request->days);

            return back()->with('success', "L'expiration a été prolongée de {$request->days} jour(s).");

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la prolongation: ' . $e->getMessage());
        }
    }

    /**
     * Afficher les attributions qui expirent bientôt.
     */
    public function expiringSoon(Request $request)
    {
        $days = $request->get('days', 7);

        $expiringPermissions = RolePermission::with(['role', 'permission'])
            ->active()
            ->expiringSoon($days)
            ->orderBy('expire_le')
            ->get();

        return view('role-permissions.expiring-soon', compact('expiringPermissions', 'days'));
    }

    /**
     * API: Matrice des permissions.
     */
    public function apiMatrix()
    {
        $roles = Role::byLevel('desc')->get(['id', 'name', 'slug', 'level']);
        $permissions = Permission::active()->ordered()->get(['id', 'name', 'slug', 'category', 'module']);

        $matrix = [];
        foreach ($roles as $role) {
            $rolePermissionIds = $role->permissions()->pluck('permissions.id')->toArray();
            $matrix[$role->id] = $rolePermissionIds;
        }

        return response()->json([
            'roles' => $roles,
            'permissions' => $permissions->groupBy('category'),
            'matrix' => $matrix,
        ]);
    }

    /**
     * Synchroniser les permissions d'un rôle (AJAX).
     */
    public function syncPermissions(Request $request, Role $role)
    {
        $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'uuid|exists:permissions,id',
        ]);

        if (!$role->canBeEdited()) {
            return response()->json(['error' => 'Ce rôle système ne peut pas être modifié.'], 403);
        }

        try {
            $role->syncPermissions($request->permissions ?? []);

            return response()->json([
                'success' => true,
                'message' => 'Les permissions ont été synchronisées avec succès.',
                'count' => count($request->permissions ?? []),
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
