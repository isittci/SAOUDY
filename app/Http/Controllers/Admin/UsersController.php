<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Mail\PasswordResetByAdmin;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::with('role')
            ->viewable();

        // Recherche
        if ($search = $request->search) {
            $query->search($search);
        }

        // Filtre par rôle
        if ($roleId = $request->role_id) {
            $query->byRole($roleId);
        }

        // Filtre par statut
        if ($statut = $request->statut) {
            if ($statut === 'actif') {
                $query->actif();
            } elseif ($statut === 'inactif') {
                $query->inactif();
            }
        }

        $users = $query->orderedByName()->paginate(20);
        $roles = Role::orderBy('level', 'desc')->get();

        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', User::class);

        /**
         * @var User ùuser
         */
        $user = auth()->user();

        // Récupérer les rôles que l'utilisateur peut attribuer
        if ($user->isSuperAdmin()) {
            $roles = Role::orderBy('level', 'desc')->get();
        } else {
            // On peut attribuer des rôles de niveau strictement inférieur
            $roles = Role::where('level', '<', $user->role->level)
                ->orderBy('level', 'desc')
                ->get();
        }

        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        try {
            DB::beginTransaction();

             /**
         * @var User ùuser
         */
            $data = $request->validated();

            // Vérifier que l'utilisateur peut attribuer ce rôle
            $role = Role::findOrFail($data['role_id']);
            if (!auth()->user()->isSuperAdmin() && $role->level >= auth()->user()->role->level) {
                return back()->with('error', 'Vous ne pouvez pas attribuer un rôle de niveau égal ou supérieur au vôtre.')
                    ->withInput();
            }

            // Gérer la vérification de l'email
            if ($request->has('email_verified') && $request->email_verified) {
                $data['email_verified_at'] = now();
            }

            $user = User::create($data);

            DB::commit();

            return redirect()->route('admin.users.show', $user)
                ->with('success', 'Utilisateur créé avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la création de l\'utilisateur : ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        // Vérifier que l'utilisateur connecté peut voir cet utilisateur
        if (!auth()->user()->canViewUser($user)) {
            abort(403, 'Vous n\'avez pas l\'autorisation de voir cet utilisateur.');
        }

        $user->load('role', 'creator', 'updater');

        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        // Vérifier que l'utilisateur connecté peut gérer cet utilisateur
        if (!auth()->user()->canManageUser($user)) {
            abort(403, 'Vous n\'avez pas l\'autorisation de modifier cet utilisateur.');
        }

        $authUser = auth()->user();

        // Récupérer les rôles que l'utilisateur peut attribuer
        if ($authUser->isSuperAdmin()) {
            $roles = Role::orderBy('level', 'desc')->get();
        } else {
            $roles = Role::where('level', '<', $authUser->role->level)
                ->orderBy('level', 'desc')
                ->get();
        }

        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();

            // Vérifier que l'utilisateur peut attribuer ce rôle
            $role = Role::findOrFail($data['role_id']);
            if (!auth()->user()->isSuperAdmin() && $role->level >= auth()->user()->role->level) {
                return back()->with('error', 'Vous ne pouvez pas attribuer un rôle de niveau égal ou supérieur au vôtre.')
                    ->withInput();
            }

            // Ne mettre à jour le mot de passe que s'il est fourni
            if (empty($data['password'])) {
                unset($data['password']);
            }

            // Gérer la vérification de l'email
            if ($request->has('email_verified')) {
                $data['email_verified_at'] = $request->email_verified ? now() : null;
            }

            $user->update($data);

            DB::commit();

            return redirect()->route('admin.users.show', $user)
                ->with('success', 'Utilisateur modifié avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la modification de l\'utilisateur : ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if (!$user->canBeDeleted()) {
            return back()->with('error', 'Cet utilisateur ne peut pas être supprimé.');
        }

        if (!auth()->user()->canManageUser($user)) {
            abort(403, 'Vous n\'avez pas l\'autorisation de supprimer cet utilisateur.');
        }

        try {
            $user->delete();

            return redirect()->route('admin.users.index')
                ->with('success', 'Utilisateur déplacé dans la corbeille.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la suppression : ' . $e->getMessage());
        }
    }

    /**
     * Bascule le statut de l'utilisateur.
     */
    public function toggleStatus(User $user)
    {
        if (!auth()->user()->canManageUser($user)) {
            abort(403, 'Vous n\'avez pas l\'autorisation de modifier le statut de cet utilisateur.');
        }

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas modifier votre propre statut.');
        }

        try {
            $user->toggleStatus();

            $message = $user->isActive()
                ? 'Utilisateur activé avec succès.'
                : 'Utilisateur désactivé avec succès.';

            return back()->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors du changement de statut : ' . $e->getMessage());
        }
    }

    /**
     * Réinitialise le mot de passe de l'utilisateur.
     */
    // public function resetPassword(Request $request, User $user)
    // {
    //     if (!auth()->user()->canManageUser($user)) {
    //         abort(403, 'Vous n\'avez pas l\'autorisation de réinitialiser le mot de passe de cet utilisateur.');
    //     }

    //     if ($user->id === auth()->id()) {
    //         return back()->with('error', 'Utilisez la page de profil pour modifier votre propre mot de passe.');
    //     }

    //     $request->validate([
    //         'password' => 'required|string|min:8|confirmed',
    //     ], [
    //         'password.required' => 'Le mot de passe est obligatoire.',
    //         'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
    //         'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
    //     ]);

    //     try {
    //         $user->update([
    //             'password' => Hash::make($request->password),
    //         ]);

    //         return back()->with('success', 'Mot de passe réinitialisé avec succès.');
    //     } catch (\Exception $e) {
    //         return back()->with('error', 'Erreur lors de la réinitialisation : ' . $e->getMessage());
    //     }
    // }

        public function resetPassword(Request $request, User $user)
    {
        if (!auth()->user()->canManageUser($user)) {
            abort(403, 'Vous n\'avez pas l\'autorisation de réinitialiser le mot de passe de cet utilisateur.');
        }

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Utilisez la page de profil pour modifier votre propre mot de passe.');
        }

        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ], [
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
        ]);

        try {
            DB::beginTransaction();

            // Stocker le mot de passe en clair temporairement pour l'email
            $plainPassword = $request->password;

            // Mettre à jour le mot de passe hashé
            $user->update([
                'password' => Hash::make($plainPassword),
                'updated_by' => auth()->id(),
            ]);

            // Générer un token de réinitialisation valable 1 heure
            $token = Str::random(64);

            // Supprimer les anciens tokens de cet utilisateur
            DB::table('password_reset_tokens')
                ->where('email', $user->email)
                ->delete();

            // Créer un nouveau token
            DB::table('password_reset_tokens')->insert([
                'email' => $user->email,
                'token' => Hash::make($token),
                'created_at' => now(),
            ]);

            // Générer l'URL de réinitialisation 'password.reset'
            $resetUrl = url(route('auth.password.reset', [
                'token' => $token,
                'email' => $user->email,
            ], false));

            // Envoyer l'email
            Mail::to($user->email)->send(
                new PasswordResetByAdmin($user, $plainPassword, $token, $resetUrl)
            );

            DB::commit();

            return back()->with('success', 'Mot de passe réinitialisé avec succès. Un email a été envoyé à l\'utilisateur.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la réinitialisation : ' . $e->getMessage());
        }
    }

    /**
     * Affiche la corbeille.
     */
    public function trash(Request $request)
    {
        $query = User::onlyTrashed()
            ->with('role')
            ->viewable();

        if ($search = $request->search) {
            $query->search($search);
        }

        $users = $query->orderedByName()->paginate(20);
        $roles = Role::orderBy('level', 'desc')->get();

        return view('admin.users.trash', compact('users', 'roles'));
    }

    /**
     * Restaure un utilisateur supprimé.
     */
    public function restore(string $id)
    {

        $user = User::onlyTrashed()->findOrFail($id);

        if (!auth()->user()->canManageUser($user)) {
            abort(403, 'Vous n\'avez pas l\'autorisation de restaurer cet utilisateur.');
        }

        try {
            $user->restore();

            return redirect()->route('admin.users.index')
                ->with('success', 'Utilisateur restauré avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la restauration : ' . $e->getMessage());
        }
    }

    /**
     * Supprime définitivement un utilisateur.
     */
    public function forceDestroy(string $id)
    {
        $user = User::onlyTrashed()->findOrFail($id);

        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Seul un Super Administrateur peut supprimer définitivement un utilisateur.');
        }

        try {
            $user->forceDelete();

            return back()->with('success', 'Utilisateur supprimé définitivement.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la suppression : ' . $e->getMessage());
        }
    }
}
