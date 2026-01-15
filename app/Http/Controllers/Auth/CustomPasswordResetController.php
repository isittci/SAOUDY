<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CustomPasswordResetController extends Controller
{
    /**
     * Durée de validité du token en minutes (2 heures = 120 minutes)
     */
    private int $tokenExpirationMinutes;

    public function __construct()
    {
        $this->tokenExpirationMinutes = config('password_reset.token_expiration_minutes', 120);
    }

    /**
     * Affiche le formulaire de réinitialisation.
     */
    public function showResetForm(Request $request, $token)
    {
        $email = $request->email;

        // Vérifier si le token existe et n'a pas expiré (2 heures)
        $passwordReset = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (!$passwordReset) {
            return redirect()->route('auth.index')
                ->with('error', 'Ce lien de réinitialisation est invalide.');
        }

        // Vérifier si le token a expiré (2 heures)
        if (now()->diffInMinutes($passwordReset->created_at) > $this->tokenExpirationMinutes) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            return redirect()->route('auth.index')
                ->with('error', 'Ce lien de réinitialisation a expiré (valable 2 heures). Veuillez contacter un administrateur.');
        }

        // Vérifier si le token correspond
        if (!Hash::check($token, $passwordReset->token)) {
            return redirect()->route('auth.index')
                ->with('error', 'Ce lien de réinitialisation est invalide.');
        }

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    /**
     * Traite la réinitialisation du mot de passe.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
        ]);

        // Vérifier le token
        $passwordReset = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$passwordReset || !Hash::check($request->token, $passwordReset->token)) {
            return back()->with('error', 'Ce lien de réinitialisation est invalide.');
        }

        // Vérifier l'expiration (2 heures)
        if (now()->diffInMinutes($passwordReset->created_at) > $this->tokenExpirationMinutes) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->with('error', 'Ce lien de réinitialisation a expiré (valable 2 heures).');
        }

        // Mettre à jour le mot de passe
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->with('error', 'Utilisateur introuvable.');
        }

        $user->update([
            'password' => Hash::make($request->password),
            'updated_by' => $user->id, // L'utilisateur se met à jour lui-même
        ]);

        // Supprimer le token utilisé
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('auth.index')
            ->with('success', 'Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.');
    }
}
