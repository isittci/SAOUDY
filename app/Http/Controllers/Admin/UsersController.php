<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Mail\WelcomeNewUser;
use App\Mail\PasswordResetByAdmin;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
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

        /**
         * @var User $user
         */
        $user = auth()->user();

        // Exclure les super admins si l'utilisateur connecté n'est pas super admin
        if (!$user->isSuperAdmin()) {
            $query->whereHas('role', function ($q) {
                $q->where('slug', '!=', 'super-administrateur');
            });
        }

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

        // Également filtrer les rôles affichés
        $roles = Role::when(!$user->isSuperAdmin(), function ($q) {
            $q->where('slug', '!=', 'super-administrateur');
        })->orderBy('level', 'desc')->get();

        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', User::class);

        /**
         * @var User $user
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
     *
     * IMPORTANT: L'envoi de l'email de bienvenue est OBLIGATOIRE.
     * Si l'email échoue, l'utilisateur n'est PAS créé (rollback).
     */
    public function store(StoreUserRequest $request)
    {
        try {
            DB::beginTransaction();

            /**
             * @var User $authUser
             */
            $authUser = auth()->user();
            $data = $request->validated();

            // Vérifier que l'utilisateur peut attribuer ce rôle
            $role = Role::findOrFail($data['role_id']);
            if (!$authUser->isSuperAdmin() && $role->level >= $authUser->role->level) {
                return back()->with('error', 'Vous ne pouvez pas attribuer un rôle de niveau égal ou supérieur au vôtre.')
                    ->withInput();
            }

            // Stocker le mot de passe en clair pour l'email
            $plainPassword = $data['password'];

            // Gérer la vérification de l'email
            if ($request->has('email_verified') && $request->email_verified) {
                $data['email_verified_at'] = now();
            }

            // Ajouter le créateur
            $data['created_by'] = $authUser->id;

            // Créer l'utilisateur
            $user = User::create($data);

            // Charger la relation role pour l'email
            $user->load('role');

            // ============================================================
            // ENVOI DE L'EMAIL DE BIENVENUE (OBLIGATOIRE)
            // Si l'envoi échoue, on annule la création de l'utilisateur
            // ============================================================
            Mail::to($user->email)->send(
                new WelcomeNewUser($user, $plainPassword, $authUser)
            );

            Log::info('Utilisateur créé et email de bienvenue envoyé', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $role->name,
                'created_by' => $authUser->id,
            ]);

            DB::commit();

            return redirect()->route('admin.users.show', $user)
                ->with('success', 'Utilisateur créé avec succès. Un email contenant les identifiants de connexion a été envoyé à ' . $user->email);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Échec de la création de l\'utilisateur', [
                'email' => $request->email ?? 'N/A',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Message d'erreur personnalisé selon le type d'erreur
            $errorMessage = $this->getCreateUserErrorMessage($e);

            return back()->with('error', $errorMessage)->withInput();
        }
    }

    /**
     * Retourne un message d'erreur personnalisé selon le type d'exception.
     */
    private function getCreateUserErrorMessage(\Exception $e): string
    {
        $message = $e->getMessage();

        // Détecter les erreurs d'envoi d'email
        if (
            str_contains($message, 'mail') ||
            str_contains($message, 'SMTP') ||
            str_contains($message, 'smtp') ||
            str_contains($message, 'Connection') ||
            str_contains($message, 'Swift_') ||
            str_contains($message, 'Symfony\Component\Mailer') ||
            $e instanceof \Symfony\Component\Mailer\Exception\TransportExceptionInterface
        ) {
            return 'Impossible de créer l\'utilisateur : l\'envoi de l\'email de bienvenue a échoué. ' .
                   'Veuillez vérifier la configuration du serveur de messagerie et réessayer. ' .
                   'Détail : ' . $message;
        }

        // Erreur de base de données (email déjà existant, etc.)
        if (str_contains($message, 'Duplicate') || str_contains($message, 'unique')) {
            return 'Cette adresse email est déjà utilisée par un autre compte.';
        }

        // Erreur générique
        return 'Erreur lors de la création de l\'utilisateur : ' . $message;
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

            // Ajouter l'updater
            $data['updated_by'] = auth()->id();

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
            $user->update(['deleted_by' => auth()->id()]);
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
     *
     * IMPORTANT: L'envoi de l'email est OBLIGATOIRE.
     * Si l'email échoue, le mot de passe n'est PAS modifié (rollback).
     */
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

            // Générer l'URL de réinitialisation
            $resetUrl = url(route('auth.password.reset', [
                'token' => $token,
                'email' => $user->email,
            ], false));

            // Envoyer l'email (OBLIGATOIRE - si échec, rollback)
            Mail::to($user->email)->send(
                new PasswordResetByAdmin($user, $plainPassword, $token, $resetUrl)
            );

            DB::commit();

            return back()->with('success', 'Mot de passe réinitialisé avec succès. Un email a été envoyé à l\'utilisateur.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Échec de la réinitialisation du mot de passe', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Erreur lors de la réinitialisation du mot de passe. L\'email n\'a pas pu être envoyé : ' . $e->getMessage());
        }
    }

    /**
     * Renvoie l'email de bienvenue à un utilisateur avec un nouveau mot de passe.
     *
     * IMPORTANT: L'envoi de l'email est OBLIGATOIRE.
     * Si l'email échoue, le mot de passe n'est PAS modifié (rollback).
     */
    public function resendWelcomeEmail(User $user)
    {
        if (!auth()->user()->canManageUser($user)) {
            abort(403, 'Vous n\'avez pas l\'autorisation d\'envoyer un email à cet utilisateur.');
        }

        try {
            DB::beginTransaction();

            // Générer un nouveau mot de passe temporaire
            $plainPassword = Str::random(12);

            // Mettre à jour le mot de passe
            $user->update([
                'password' => Hash::make($plainPassword),
                'updated_by' => auth()->id(),
            ]);

            // Charger la relation role
            $user->load('role');

            // Envoyer l'email (OBLIGATOIRE - si échec, rollback)
            Mail::to($user->email)->send(
                new WelcomeNewUser($user, $plainPassword, auth()->user())
            );

            Log::info('Email de bienvenue renvoyé', [
                'user_id' => $user->id,
                'email' => $user->email,
                'sent_by' => auth()->id(),
            ]);

            DB::commit();

            return back()->with('success', 'Email de bienvenue renvoyé avec un nouveau mot de passe à ' . $user->email);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Échec du renvoi de l\'email de bienvenue', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Erreur lors de l\'envoi de l\'email. Le mot de passe n\'a pas été modifié : ' . $e->getMessage());
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
            $user->update([
                'deleted_by' => null,
                'updated_by' => auth()->id(),
            ]);

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
