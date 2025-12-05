<?php

namespace App\Http\Controllers\Web\Private\Isitt;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Mail\PasswordResetMail;
use App\Mail\UserCredentialsMail;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Afficher la liste des utilisateurs
     */
    public function index(Request $request)
    {
        /**
         * @var User $user
         */
        $user = auth()->user();
        if (!$user->hasPermission('users.read')) {
            return $this->respondUnauthorized('Accès non autorisé', $request);
        }

        $query = User::with(['role', 'creator', 'updater']);

        // Recherche
        if ($request->has('search') && $request->search) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('nom_complet', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('telephone_1', 'LIKE', "%{$search}%");
            });
        }

        // Filtrer par rôle
        if ($request->has('role_id') && $request->role_id) {

            $query->where('role_id', $request->role_id);
        }

        // Filtrer par statut
        if ($request->has('status') && $request->status != null ) {
            $query->where('status', $request->status);
        }

        // Tri
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $perPage = $request->get('per_page', 10);
        $users = $query->paginate($perPage);
        $roles = Role::orderBy('level')->get();



        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $users->items(),
                'pagination' => [
                    'total' => $users->total(),
                    'per_page' => $users->perPage(),
                    'current_page' => $users->currentPage(),
                    'last_page' => $users->lastPage(),
                    'from' => $users->firstItem(),
                    'to' => $users->lastItem(),
                ],
                'roles' => $roles,
            ]);
        }

        return view('users.index', compact('users', 'roles'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create(Request $request)
    {
        /**
         * @var User $user
         */
        $user = auth()->user();
        if (!$user->hasPermission('users.create')) {
            return $this->respondUnauthorized('Accès non autorisé', $request);
        }

        $roles = Role::orderBy('level')->get();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'roles' => $roles,
            ]);
        }

        return view('users.create', compact('roles'));
    }

    /**
     * Enregistrer un nouveau utilisateur
     */
    public function store(Request $request)
    {
        /**
         * @var User $user
         */
        $user = auth()->user();
        if (!$user->hasPermission('users.create')) {
            return $this->respondUnauthorized('Accès non autorisé', $request);
        }

        $validated = $request->validate([
            'nom_complet' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'telephone_1' => 'required|string|max:20',
            'telephone_2' => 'nullable|string|max:20',
            'role_id' => 'required|exists:roles,id',
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
            'status' => 'boolean',
            'send_credentials' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'nom_complet' => $validated['nom_complet'],
                'email' => $validated['email'],
                'telephone_1' => $validated['telephone_1'],
                'telephone_2' => $validated['telephone_2'] ?? null,
                'role_id' => $validated['role_id'],
                'password' => Hash::make($validated['password']),
                'status' => $validated['status'] ?? true,
                'email_verified_at' => now(),
                'created_by' => auth()->id(),
            ]);

            // Charger les relations
            $user->load(['role', 'creator']);

            // Envoyer les identifiants par email si demandé
            if ($request->send_credentials) {
                Mail::to($user->email)->send(new UserCredentialsMail($user, $validated['password']));
            }

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Utilisateur créé avec succès',
                    'data' => $user,
                ], 201);
            }

            return redirect()
                ->route('users.show', $user)
                ->with('success', 'Utilisateur créé avec succès');

        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la création',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la création: ' . $e->getMessage());
        }
    }

    /**
     * Afficher les détails d'un utilisateur
     */
    public function show(Request $request, $user)
    {
         /**
         * @var User $admin
         */
        $admin = auth()->user();

        if (!$admin->hasPermission('users.read')) {
            return $this->respondUnauthorized('Accès non autorisé', $request);
        }

        // Charger l'utilisateur et vérifier son existence
        $user = User::findOrFail($user);
        if( !$user ) {
            return $this->respondUnauthorized('Utilisateur non trouvé', $request);
        }

        // Changer le chargement des relations
        $user->load(['role', 'creator', 'updater']);

        // Retourner la réponse selon le type de requête
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $user,
            ]);
        }

        // Afficher la vue
        return view('users.show', compact('user'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Request $request, User $user)
    {
        /**
         * @var User $admin
         */
        $admin = auth()->user();

        if (!$admin->hasPermission('users.update')) {
            return $this->respondUnauthorized('Accès non autorisé', $request);
        }

        // Empêcher la modification d'un super admin par un non super admin
        if ($user->isSuperAdmin()) {
            return $this->respondUnauthorized('Vous ne pouvez pas modifier un super administrateur', $request);
        }

        $roles = Role::orderBy('level')->get();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $user,
                'roles' => $roles,
            ]);
        }

        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Mettre à jour un utilisateur
     */
    public function update(Request $request, User $user)
    {
        /**
         * @var User $superAdmin
         */
        $superAdmin = auth()->user();

        if (!$superAdmin->hasPermission('users.update')) {
            return $this->respondUnauthorized('Accès non autorisé', $request);
        }

        // Empêcher la modification d'un super admin par un non super admin
        if ($user->isSuperAdmin()) {
            return $this->respondUnauthorized('Vous ne pouvez pas modifier un super administrateur', $request);
        }

        $validated = $request->validate([
            'nom_complet' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,

            'telephone_1' => 'required|string|max:20',
            'telephone_2' => 'nullable|string|max:20',
            'role_id' => 'required|exists:roles,id',
            'status' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            $user->update([
                'nom_complet' => $validated['nom_complet'],
                'email' => $validated['email'],
                'telephone_1' => $validated['telephone_1'],
                'telephone_2' => $validated['telephone_2'] ?? null,
                'role_id' => $validated['role_id'],
                'status' => $validated['status'] ?? $user->status,
                'updated_by' => auth()->id(),
            ]);

            $user->load(['role', 'creator', 'updater']);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Utilisateur mis à jour avec succès',
                    'data' => $user,
                ]);
            }

            return redirect()
                ->route('users.show', $user)
                ->with('success', 'Utilisateur mis à jour avec succès');

        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la mise à jour',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    /**
     * Supprimer un utilisateur (soft delete)
     */
    public function destroy(Request $request, User $user)
    {
        /**
         * @var User $superAdmin
         */
        $superAdmin = auth()->user();

        if (!$superAdmin->hasPermission('users.delete')) {
            return $this->respondUnauthorized('Accès non autorisé', $request);
        }

        // Empêcher la suppression d'un super admin
        if ($user->isSuperAdmin()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de supprimer un super administrateur',
                ], 403);
            }

            return back()->with('error', 'Impossible de supprimer un super administrateur');
        }

        // Empêcher l'auto-suppression
        if ($user->id === auth()->id()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous ne pouvez pas vous supprimer vous-même',
                ], 403);
            }

            return back()->with('error', 'Vous ne pouvez pas vous supprimer vous-même');
        }

        DB::beginTransaction();
        try {
            $user->update(['deleted_by' => auth()->id()]);
            $user->delete();

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Utilisateur supprimé avec succès',
                ]);
            }

            return redirect()
                ->route('users.index')
                ->with('success', 'Utilisateur supprimé avec succès');

        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la suppression',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    /**
     * Restaurer un utilisateur supprimé
     */
    public function restore(Request $request, $id)
    {
        /**
         * @var User $user
         */
        $user = auth()->user();

        if (!$user->hasPermission('users.restore')) {
            return $this->respondUnauthorized('Accès non autorisé', $request);
        }

        $user = User::withTrashed()->findOrFail($id);

        DB::beginTransaction();
        try {
            $user->restore();
            $user->update(['deleted_by' => null]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Utilisateur restauré avec succès',
                    'data' => $user->fresh(['role', 'creator']),
                ]);
            }

            return redirect()
                ->route('users.show', $user)
                ->with('success', 'Utilisateur restauré avec succès');

        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la restauration',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Erreur lors de la restauration: ' . $e->getMessage());
        }
    }

    /**
     * Changer le mot de passe d'un utilisateur
     */
    public function updatePassword(Request $request, User $user)
    {
        /**
         * @var User $superAdmin
         */
        $superAdmin = auth()->user();

        if (!$superAdmin->hasPermission('users.reset-password') && auth()->id() !== $user->id) {
            return $this->respondUnauthorized('Accès non autorisé', $request);
        }

        $validated = $request->validate([
            'current_password' => auth()->id() === $user->id ? 'required' : 'nullable',
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
        ]);

        // Vérifier le mot de passe actuel si c'est l'utilisateur lui-même
        if (auth()->id() === $user->id && !Hash::check($validated['current_password'], $user->password)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le mot de passe actuel est incorrect',
                ], 422);
            }

            return back()->with('error', 'Le mot de passe actuel est incorrect');
        }

        DB::beginTransaction();
        try {
            $user->update([
                'password' => Hash::make($validated['password']),
                'updated_by' => auth()->id(),
            ]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Mot de passe mis à jour avec succès',
                ]);
            }

            return back()->with('success', 'Mot de passe mis à jour avec succès');

        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la mise à jour du mot de passe',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    /**
     * Activer/Désactiver un utilisateur
     */
    public function toggleStatus(Request $request, User $user)
    {
        /**
         * @var User $superAdmin
         */
        $superAdmin = auth()->user();

        if (!$superAdmin->hasPermission('users.update')) {
            return $this->respondUnauthorized('Accès non autorisé', $request);
        }

        // Empêcher la désactivation d'un super admin
        if ($user->isSuperAdmin()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de désactiver un super administrateur',
                ], 403);
            }

            return back()->with('error', 'Impossible de désactiver un super administrateur');
        }

        DB::beginTransaction();
        try {
            $user->update([
                'status' => !$user->status,
                'updated_by' => auth()->id(),
            ]);

            DB::commit();

            $message = $user->status ? 'Utilisateur activé avec succès' : 'Utilisateur désactivé avec succès';

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => $user,
                ]);
            }

            return back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors du changement de statut',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Réinitialiser le mot de passe et envoyer par email
     */
    public function resetPassword(Request $request, User $user)
    {
        /**
         * @var User $superAdmin
         */
        $superAdmin = auth()->user();

        if (!$superAdmin->hasPermission('users.reset-password')) {
            return $this->respondUnauthorized('Accès non autorisé', $request);
        }

        DB::beginTransaction();
        try {
            // Générer un nouveau mot de passe
            $newPassword = Str::random(12);

            $user->update([
                'password' => Hash::make($newPassword),
                'password_reset_token' => Str::random(60),
                'updated_by' => auth()->id(),
            ]);

            // Envoyer le nouveau mot de passe par email
            Mail::to($user->email)->send(new PasswordResetMail($user, $newPassword));

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Mot de passe réinitialisé et envoyé par email',
                    'temp_password' => $newPassword, // À retirer en production
                ]);
            }

            return back()->with('success', 'Mot de passe réinitialisé et envoyé par email');

        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la réinitialisation',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Obtenir les statistiques des utilisateurs
     */
    public function statistics(Request $request)
    {
        /**
         * @var User $user
         */
        $user = auth()->user();

        if (!$user->hasPermission('users.list')) {
            return $this->respondUnauthorized('Accès non autorisé', $request);
        }

        $stats = [
            'total' => User::count(),
            'active' => User::where('status', true)->count(),
            'inactive' => User::where('status', false)->count(),
            'verified' => User::whereNotNull('email_verified_at')->count(),
            'by_role' => User::select('role_id', DB::raw('count(*) as count'))
                ->with('role:id,libelle')
                ->groupBy('role_id')
                ->get()
                ->map(function ($item) {
                    return [
                        'role' => $item->role->name,
                        'count' => $item->count,
                    ];
                }),
        ];

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $stats,
            ]);
        }

        return view('users.statistics', compact('stats'));
    }

    /**
     * Réponse non autorisée selon le type de requête
     */
    private function respondUnauthorized(string $message, Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message,
            ], 403);
        }

        abort(403, $message);
    }
}
