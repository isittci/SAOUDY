<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;

class RoleController extends Controller
{
    /**
     * Afficher la liste des rôles.
     */
    public function index(Request $request)
    {

        $query = Role::withCount(['users', 'permissions'])
            ->search($request->search);

        // Filtrer par type
        if ($request->filled('type')) {
            if ($request->type === 'system') {
                $query->system();
            } elseif ($request->type === 'custom') {
                $query->custom();
            }
        }

        // Tri
        $sortBy = $request->get('sort', 'level');
        $sortDir = $request->get('direction', 'desc');
        $query->orderBy($sortBy, $sortDir);

        $roles = $query->paginate(15)->withQueryString();

        return view('roles.index', compact('roles'));
    }

    /**
     * Afficher le formulaire de création.
     */
    public function create()
    {
        $permissions = Permission::groupedByCategory();
        $levels = Role::LEVEL_LABELS;

        return view('roles.create', compact('permissions', 'levels'));
    }

    /**
     * Enregistrer un nouveau rôle.
     */
    public function store(StoreRoleRequest $request)
    {
        try {
            DB::beginTransaction();

            $role = Role::create([
                'name' => $request->name,
                'slug' => $request->slug ?: Str::slug($request->name),
                'description' => $request->description,
                'level' => $request->level,
                'is_system_role' => false,
            ]);

            // Attribuer les permissions
            if ($request->filled('permissions')) {
                $role->syncPermissions($request->permissions);
            }

            DB::commit();

            return redirect()
                ->route('admin.roles.show', $role)
                ->with('success', 'Le rôle a été créé avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la création du rôle: ' . $e->getMessage());
        }
    }

    /**
     * Afficher les détails d'un rôle.
     */
    public function show(Role $role)
    {
        $role->load(['users' => function ($q) {
            $q->orderBy('nom_complet')->limit(10);
        }, 'permissions']);

        $permissionsByCategory = $role->permissions()
            ->orderBy('category')
            ->orderBy('display_order')
            ->get()
            ->groupBy('category');

        return view('roles.show', compact('role', 'permissionsByCategory'));
    }

    /**
     * Afficher le formulaire d'édition.
     */
    public function edit(Role $role)
    {
        if (!$role->canBeEdited()) {
            return back()->with('error', 'Ce rôle système ne peut pas être modifié.');
        }

        $permissions = Permission::groupedByCategory();
        $rolePermissionIds = $role->permissions()->pluck('permissions.id')->toArray();
        $levels = Role::LEVEL_LABELS;

        return view('roles.edit', compact('role', 'permissions', 'rolePermissionIds', 'levels'));
    }

    /**
     * Mettre à jour un rôle.
     */
    public function update(UpdateRoleRequest $request, Role $role)
    {
        if (!$role->canBeEdited()) {
            return back()->with('error', 'Ce rôle système ne peut pas être modifié.');
        }

        try {
            DB::beginTransaction();

            $role->update([
                'name' => $request->name,
                'slug' => $request->slug ?: Str::slug($request->name),
                'description' => $request->description,
                'level' => $request->level,
            ]);

            // Synchroniser les permissions
            if ($request->has('permissions')) {
                $role->syncPermissions($request->permissions ?? []);
            }

            DB::commit();

            return redirect()
                ->route('admin.roles.show', $role)
                ->with('success', 'Le rôle a été mis à jour avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la mise à jour du rôle: ' . $e->getMessage());
        }
    }

    /**
     * Supprimer un rôle.
     */
    public function destroy(Role $role)
    {
        if (!$role->canBeDeleted()) {
            return back()->with('error', 'Ce rôle ne peut pas être supprimé.');
        }

        try {
            $role->delete();

            return redirect()
                ->route('admin.roles.index')
                ->with('success', 'Le rôle a été supprimé avec succès.');

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la suppression du rôle: ' . $e->getMessage());
        }
    }

    /**
     * Dupliquer un rôle.
     */
    public function duplicate(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:roles,name',
            'slug' => 'nullable|string|max:100|unique:roles,slug',
        ], [
            'name.required' => 'Le nom du nouveau rôle est requis.',
            'name.unique' => 'Ce nom de rôle existe déjà.',
            'slug.unique' => 'Cet identifiant existe déjà.',
        ]);

        try {
            $newRole = $role->duplicate(
                $request->name,
                $request->slug ?: Str::slug($request->name)
            );

            return redirect()->route('admin.roles.edit', $newRole)->with('success', 'Le rôle a été dupliqué avec succès.');

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la duplication du rôle: ' . $e->getMessage());
        }
    }

    /**
     * Afficher la page de gestion des permissions d'un rôle.
     */
    public function permissions(Role $role)
    {
        $permissionsByCategory = Permission::groupedByCategory();
        $rolePermissionIds = $role->permissions()->pluck('permissions.id')->toArray();

        return view('roles.permissions', compact('role', 'permissionsByCategory', 'rolePermissionIds'));
    }

    /**
     * Mettre à jour les permissions d'un rôle.
     */
    public function updatePermissions(Request $request, Role $role)
    {
        if (!$role->canBeEdited()) {
            return back()->with('error', 'Les permissions de ce rôle système ne peuvent pas être modifiées.');
        }

        try {
            $role->syncPermissions($request->permissions ?? []);

            return redirect()->route('admin.roles.permissions', $role)->with('success', 'Les permissions ont été mises à jour avec succès.');

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la mise à jour des permissions: ' . $e->getMessage());
        }
    }

    /**
     * API: Liste des rôles pour les selects.
     */
    public function apiList(Request $request)
    {
        $query = Role::select('id', 'name', 'slug', 'level')
            ->search($request->search)
            ->byLevel('desc');

        // Filtrer par niveau maximum si spécifié
        if ($request->filled('max_level')) {
            $query->maxLevel($request->max_level);
        }

        $roles = $query->limit(50)->get();

        return response()->json($roles);
    }
}
