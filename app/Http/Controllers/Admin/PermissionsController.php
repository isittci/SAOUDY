<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PermissionsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $query = Permission::query();

        // Recherche
        if ($search = $request->search) {
            $query->search($search);
        }

        // Filtre par module
        if ($module = $request->module) {
            $query->byModule($module);
        }

        // Filtre par ressource
        if ($resource = $request->resource) {
            $query->byResource($resource);
        }

        // Filtre par action
        if ($action = $request->action) {
            $query->byAction($action);
        }

        // Filtre par catégorie
        if ($category = $request->category) {
            $query->where('category', $category);
        }

        // Filtre par statut
        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $permissions = $query->ordered()->paginate(50);

        // Récupérer les filtres disponibles
        $modules = Permission::distinct()->pluck('module')->sort()->values();
        $resources = Permission::distinct()->pluck('resource')->sort()->values();
        $actions = Permission::distinct()->pluck('action')->sort()->values();
        $ACTIONS = Permission::ACTIONS;

        $categories = Permission::distinct()->pluck('category')->filter()->sort()->values();
        $CATEGORIES = Permission::CATEGORIES;


        // dd($actions);

        return view('admin.permissions.index', compact(
            'permissions',
            'modules',
            'resources',
            'actions',
            'ACTIONS',
            'categories',
            'CATEGORIES',
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:permissions,slug',
            'description' => 'nullable|string',
            'module' => 'nullable|string|max:100',
            'category' => 'nullable|string|max:100',
            'resource' => 'nullable|string|max:100',
            'action' => 'nullable|string|max:50',
            'is_active' => 'boolean',
            'requires_confirmation' => 'boolean',
        ]);

        // Générer le slug si non fourni
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $validated['is_active'] = $validated['is_active'] ?? true;
        $validated['requires_confirmation'] = $validated['requires_confirmation'] ?? false;
        $validated['created_by'] = auth()->id();

        $permission = Permission::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Permission créée avec succès.',
            'permission' => $permission
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Permission $permission)
    {
        $permission->load(['roles' => function ($query) {
            $query->orderBy('level', 'desc');
        }]);

        return view('admin.permissions.show', compact('permission'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:permissions,slug,' . $permission->id,
            'description' => 'nullable|string',
            'module' => 'nullable|string|max:100',
            'category' => 'nullable|string|max:100',
            'resource' => 'nullable|string|max:100',
            'action' => 'nullable|string|max:50',
            'is_active' => 'boolean',
            'requires_confirmation' => 'boolean',
        ]);

        // Générer le slug si non fourni
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $validated['updated_by'] = auth()->id();

        $permission->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Permission mise à jour avec succès.',
            'permission' => $permission->fresh()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission)
    {
        // Vérifier si c'est une permission système
        if ($permission->is_system) {
            return response()->json([
                'success' => false,
                'message' => 'Impossible de supprimer une permission système.'
            ], 403);
        }

        $permission->deleted_by = auth()->id();
        $permission->save();
        $permission->delete();

        return response()->json([
            'success' => true,
            'message' => 'Permission supprimée avec succès.'
        ]);
    }

    /**
     * Activate a permission.
     */
    public function activate(Permission $permission)
    {
        $permission->is_active = true;
        $permission->updated_by = auth()->id();
        $permission->save();

        return back()->with('success', 'Permission activée avec succès.');
    }

    /**
     * Deactivate a permission.
     */
    public function deactivate(Permission $permission)
    {
        $permission->is_active = false;
        $permission->updated_by = auth()->id();
        $permission->save();

        return back()->with('success', 'Permission désactivée avec succès.');
    }

    /**
     * Affiche les permissions groupées par module.
     */
    public function byModule()
    {
        $permissions = Permission::active()
            ->ordered()
            ->get()
            ->groupBy('module');

        return view('admin.permissions.by-module', compact('permissions'));
    }

    /**
     * Affiche la matrice des permissions par rôle.
     */
    public function matrix()
    {
        $permissions = Permission::active()
            ->ordered()
            ->get()
            ->groupBy('module');

        $roles = \App\Models\Role::orderBy('level', 'desc')->get();

        return view('admin.permissions.matrix', compact('permissions', 'roles'));
    }

    /**
     * Génère automatiquement les permissions CRUD pour un module.
     */
    public function generateCrud(Request $request)
    {
        $validated = $request->validate([
            'module' => 'required|string|max:100',
            'resource' => 'required|string|max:100',
            'category' => 'nullable|string|max:100',
            'actions' => 'required|array',
            'actions.*' => 'required|string|in:create,read,update,delete,export,import,validate,reject,restore,manage,view-details,view-trash,toggle-status,force-delete'
        ]);

        $created = [];
        $skipped = [];

        foreach ($validated['actions'] as $action) {
            $slug = $validated['resource'] . '.' . $action;

            // Vérifier si la permission existe déjà
            if (Permission::where('slug', $slug)->exists()) {
                $skipped[] = $slug;
                continue;
            }

            $actionLabels = [
                'create' => 'Créer',
                'read' => 'Lire',
                'update' => 'Modifier',
                'delete' => 'Supprimer',
                'export' => 'Exporter',
                'import' => 'Importer',
                'validate' => 'Valider',
                'reject' => 'Rejeter',
                'restore' => 'Restaurer',
                'manage' => 'Gérer',
                'view-details' => 'Voir détails',
                'view-trash' => 'Voir corbeille',
                'toggle-status' => 'Changer statut',
                'force-delete' => 'Supprimer définitivement',
            ];

            $permission = Permission::create([
                'name' => $actionLabels[$action] . ' ' . Str::plural($validated['resource']),
                'slug' => $slug,
                'description' => 'Permission de ' . strtolower($actionLabels[$action]) . ' les ' . Str::plural($validated['resource']),
                'module' => $validated['module'],
                'category' => $validated['category'] ?? $validated['module'],
                'resource' => $validated['resource'],
                'action' => $action,
                'is_active' => true,
                'is_system' => false,
                'created_by' => auth()->id(),
            ]);

            $created[] = $permission->name;
        }

        return response()->json([
            'success' => true,
            'message' => count($created) . ' permission(s) créée(s) avec succès.',
            'created' => $created,
            'skipped' => $skipped
        ]);
    }
}
