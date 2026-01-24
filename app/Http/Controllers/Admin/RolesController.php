<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;

class RolesController extends Controller
{
   
    /**
 * Display a listing of the resource.
 */
public function index(Request $request)
{
    $query = Role::withCount(['users', 'permissions']);

    /**
         * @var User ùuser
         */
        $user = auth()->user();

    // Exclure le rôle super admin si l'utilisateur connecté n'est pas super admin
    if (!$user->isSuperAdmin()) {
        $query->where('slug', '!=', 'super-administrateur');
        // ou selon votre structure :
        // $query->where('is_super_admin', false);
        // $query->where('level', '<', 100);
    }

    // Recherche
    if ($search = $request->search) {
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('slug', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        });
    }

    // Filtre par type
    if ($request->type === 'system') {
        $query->where('is_system_role', true);
    } elseif ($request->type === 'custom') {
        $query->where('is_system_role', false);
    }

    $roles = $query->orderBy('level', 'desc')->paginate(20);

    return view('admin.roles.index', compact('roles'));
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permissions = Permission::active()
            ->ordered()
            ->get()
            ->groupBy('module');

        return view('admin.roles.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoleRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();

            // Les rôles créés manuellement ne sont jamais des rôles système
            $data['is_system_role'] = false;

            $role = Role::create($data);

            // Attribuer les permissions si fournies
            if ($request->has('permissions')) {
                $role->syncPermissions($request->permissions);
            }

            DB::commit();

            return redirect()->route('admin.roles.show', $role)
                ->with('success', 'Rôle créé avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la création du rôle : ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        $role->load(['permissions', 'users']);

        $permissionsByModule = $role->permissions
            ->sortBy('display_order')
            ->groupBy('module');

        return view('admin.roles.show', compact('role', 'permissionsByModule'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        if (!$role->canBeEdited()) {
            return back()->with('error', 'Ce rôle système ne peut pas être modifié.');
        }

        $permissions = Permission::active()
            ->ordered()
            ->get()
            ->groupBy('module');

        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoleRequest $request, Role $role)
    {
        if (!$role->canBeEdited()) {
            return back()->with('error', 'Ce rôle système ne peut pas être modifié.');
        }

        try {
            DB::beginTransaction();

            $data = $request->validated();

            $role->update($data);

            // Mettre à jour les permissions
            if ($request->has('permissions')) {
                $role->syncPermissions($request->permissions);
            } else {
                $role->permissions()->detach();
            }

            DB::commit();

            return redirect()->route('admin.roles.show', $role)
                ->with('success', 'Rôle modifié avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la modification du rôle : ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        if (!$role->canBeDeleted()) {
            $message = $role->is_system_role
                ? 'Ce rôle système ne peut pas être supprimé.'
                : 'Ce rôle ne peut pas être supprimé car il est attribué à des utilisateurs.';

            return back()->with('error', $message);
        }

        try {
            $role->delete();

            return redirect()->route('admin.roles.index')
                ->with('success', 'Rôle supprimé avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la suppression : ' . $e->getMessage());
        }
    }

    /**
     * Affiche la page de gestion des permissions d'un rôle.
     */
    public function permissions(Role $role)
    {
        $permissions = Permission::active()
            ->ordered()
            ->get();

        // Grouper par catégorie ET par module
        $permissionsByCategory = $permissions->groupBy('category');
        $permissionsByModule = $permissions->groupBy('module');

        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('admin.roles.permissions', compact('role', 'permissionsByCategory', 'permissionsByModule', 'rolePermissions'));
    }

    /**
     * Met à jour les permissions d'un rôle.
     */
    public function updatePermissions(Request $request, Role $role)
    {
        $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'uuid|exists:permissions,id',
        ]);

        try {
            DB::beginTransaction();

            $role->syncPermissions($request->permissions ?? []);

            DB::commit();

            return back()->with('success', 'Permissions mises à jour avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la mise à jour des permissions : ' . $e->getMessage());
        }
    }

    /**
     * Duplique un rôle avec ses permissions.
     */
    public function duplicate(Role $role)
    {
        try {
            DB::beginTransaction();

            $newRole = $role->replicate();
            $newRole->name = $role->name . ' (Copie)';
            $newRole->slug = $role->slug . '-copie-' . time();
            $newRole->is_system_role = false;
            $newRole->save();

            // Copier les permissions
            $permissionsData = [];
            foreach ($role->permissions as $permission) {
                $permissionsData[$permission->id] = [
                    'attribue_par' => auth()->id(),
                    'attribue_le' => now(),
                    'actif' => true,
                    'created_by' => auth()->id(),
                    'created_at' => now(),
                ];
            }
            $newRole->permissions()->attach($permissionsData);

            DB::commit();

            return redirect()->route('admin.roles.show', $newRole)
                ->with('success', 'Rôle dupliqué avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la duplication : ' . $e->getMessage());
        }
    }
}
