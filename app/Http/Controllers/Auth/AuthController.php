<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Mail\ResetPasswordMail;
use App\Mail\VerificationCodeMail;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Afficher le formulaire de connexion
     */
    public function index(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Formulaire de connexion',
            ]);
        }

        return view("auth.login");
    }

    /**
     * Traiter la demande de connexion (étape 1: envoi du code)
     */
    public function login(Request $request)
    {

        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        // Rate limiting
        $key = 'login-attempt:' . $request->ip();

        // if (RateLimiter::tooManyAttempts($key, 5)) {

        //     $seconds = RateLimiter::availableIn($key);

        //     if ($request->expectsJson()) {
        //         return response()->json([
        //             'success' => false,
        //             'message' => "Trop de tentatives. Réessayez dans {$seconds} secondes.",
        //         ], 429);
        //     }

        //     throw ValidationException::withMessages([
        //         'email' => "Trop de tentatives. Réessayez dans {$seconds} secondes.",
        //     ]);
        // }


        // Rechercher l'utilisateur
        $user = User::where('email', $validated['email'])->first();

        // Vérifier les identifiants
        if (!$user || !Hash::check($validated['password'], $user->password)) {
            RateLimiter::hit($key, 300); // 5 minutes

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Identifiants incorrects.',
                ], 401);
            }

            throw ValidationException::withMessages([
                'email' => 'Les identifiants fournis sont incorrects.',
            ]);
        }


        // Vérifier si l'utilisateur est actif
        if (!$user->statut) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Votre compte est désactivé. Contactez l\'administrateur.',
                ], 403);
            }

            throw ValidationException::withMessages([
                'email' => 'Votre compte est désactivé. Contactez l\'administrateur.',
            ]);
        }

        DB::beginTransaction();
        try {
            // Générer un code de vérification à 6 chiffres
            $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            // Stocker le code avec expiration (10 minutes)
            $token = Str::random(60);
            $user->update([
                'password_reset_token' => $token,
                'email_verified_at' => now()->addMinutes(10), // Utilisé temporairement pour l'expiration
            ]);


            // Sauvegarder le code en cache (10 minutes)
            cache()->put("verification_code:{$token}", [
                'code' => $verificationCode,
                'user_id' => $user->id,
                'email' => $user->email,
            ], now()->addMinutes(10));

            // Envoyer le code par email
            Mail::to($user->email)->send(new VerificationCodeMail($user, $verificationCode));

            DB::commit();

            RateLimiter::clear($key);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Code de vérification envoyé par email.',
                    'token' => $token,
                    'expires_in' => 600, // 10 minutes en secondes
                    // En développement seulement - à retirer en production
                    'debug_code' => config('app.debug') ? $verificationCode : null,
                ]);
            }

            return redirect()->route('auth.verify.show', ['token' => $token])
                ->with('success', 'Un code de vérification a été envoyé à votre email.')
                ->with('debug_code', config('app.debug') ? $verificationCode : null);

        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de l\'envoi du code.',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return back()->withInput()->with('error', 'Erreur lors de l\'envoi du code.');
        }
    }

    /**
     * Afficher le formulaire de vérification du code
     */
    public function showVerifyForm(Request $request, string $token)
    {

        // Vérifier si le token existe en cache
        if (!cache()->has("verification_code:{$token}")) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Code expiré ou invalide.',
                ], 404);
            }

            return redirect()->route('auth.login')->with('error', 'Code expiré ou invalide. Veuillez vous reconnecter.');
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Veuillez entrer le code de vérification.',
                'token' => $token,
            ]);
        }

        return view('auth.verify', compact('token'));
    }

    /**
     * Vérifier le code et finaliser la connexion
     */
    public function verifyCode(Request $request, string $token)
    {
        $validated = $request->validate([
            'code' => 'required|string|size:6',
        ]);

        // Récupérer les données du cache
        $cacheData = cache()->get("verification_code:{$token}");

        if (!$cacheData) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Code expiré ou invalide.',
                ], 404);
            }

            return redirect()->route('users.login')->with('error', 'Code expiré ou invalide. Veuillez vous reconnecter.');
        }

        // Vérifier le code
        if ($cacheData['code'] !== $validated['code']) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Code de vérification incorrect.',
                ], 401);
            }

            return back()->with('error', 'Code de vérification incorrect.');
        }

        // Récupérer l'utilisateur
        $user = User::find($cacheData['user_id']);

        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur introuvable.',
                ], 404);
            }

            return redirect()->route('users.login')->with('error', 'Utilisateur introuvable.');
        }

        DB::beginTransaction();
        try {
            // Nettoyer le token

            $user->update([
                'password_reset_token' => null,
                'email_verified_at' => now(),
            ]);

            // Supprimer le code du cache
            cache()->forget("verification_code:{$token}");

            // Connecter l'utilisateur
            Auth::guard('web')->login($user, $request->boolean('remember'));

            // Régénérer la session pour éviter la fixation de session
            $request->session()->regenerate();

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Connexion réussie.',
                    'user' => [
                        'id' => $user->id,
                        'nom_complet' => $user->nom_complet,
                        'email' => $user->email,
                        'role' => $user->role->name,
                    ],
                    'redirect' => route('dashboard'),
                ]);
            }

            return redirect()->intended(route('dashboard'))
                ->with('success', 'Connexion réussie. Bienvenue ' . $user->nom_complet);

        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la connexion.',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Erreur lors de la connexion.');
        }
    }

    /**
     * Renvoyer un nouveau code de vérification
     */
    public function resendCode(Request $request, string $token)
    {
        // Récupérer les données du cache
        $cacheData = cache()->get("verification_code:{$token}");

        if (!$cacheData) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session expirée. Veuillez vous reconnecter.',
                ], 404);
            }

            return redirect()->route('users.login')->with('error', 'Session expirée. Veuillez vous reconnecter.');
        }

        $user = User::find($cacheData['user_id']);

        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur introuvable.',
                ], 404);
            }

            return redirect()->route('users.login')
                ->with('error', 'Utilisateur introuvable.');
        }

        // Rate limiting pour le renvoi de code
        $key = 'resend-code:' . $user->id;
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "Veuillez attendre {$seconds} secondes avant de demander un nouveau code.",
                ], 429);
            }

            return back()->with('error', "Veuillez attendre {$seconds} secondes avant de demander un nouveau code.");
        }

        DB::beginTransaction();
        try {
            // Générer un nouveau code
            $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            // Mettre à jour le cache
            cache()->put("verification_code:{$token}", [
                'code' => $verificationCode,
                'user_id' => $user->id,
                'email' => $user->email,
            ], now()->addMinutes(10));

            // Envoyer le nouveau code par email
            Mail::to($user->email)->send(new VerificationCodeMail($user, $verificationCode));

            DB::commit();

            RateLimiter::hit($key, 60); // 1 minute

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Un nouveau code a été envoyé.',
                    'expires_in' => 600,
                    // En développement seulement
                    'debug_code' => config('app.debug') ? $verificationCode : null,
                ]);
            }

            return back()
                ->with('success', 'Un nouveau code a été envoyé à votre email.')
                ->with('debug_code', config('app.debug') ? $verificationCode : null);

        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de l\'envoi du code.',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Erreur lors de l\'envoi du code.');
        }
    }

    /**
     * Déconnexion
     */
    public function logout(Request $request)
    {
        $userName = Auth::user()->nom_complet;

        // dd($userName);

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Déconnexion réussie.',
            ]);
        }

        return redirect()->route('auth.index')->with('success', "Au revoir {$userName} !");
    }

    /**
     * Afficher le formulaire de demande de réinitialisation
     */
    public function showForgotPasswordForm(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Formulaire de réinitialisation',
            ]);
        }

        return view('auth.forgot-password');
    }

    /**
     * Envoyer le lien de réinitialisation
     */
    public function sendResetLink(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        // Rate limiting
        $key = 'reset-link:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "Veuillez attendre {$seconds} secondes.",
                ], 429);
            }

            throw ValidationException::withMessages([
                'email' => "Veuillez attendre {$seconds} secondes.",
            ]);
        }

        $user = User::where('email', $validated['email'])->first();

        DB::beginTransaction();
        try {
            $token = Str::random(60);
            $user->update(['password_reset_token' => $token]);

            Mail::to($user->email)->send(new ResetPasswordMail($user, $token));

            DB::commit();

            RateLimiter::hit($key, 300); // 5 minutes

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Lien de réinitialisation envoyé.',
                    // En développement seulement
                    'debug_link' => config('app.debug') ? route('users.password.reset', ['token' => $token]) : null,
                ]);
            }

            return back()->with('success', 'Lien de réinitialisation envoyé à votre email.');

        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de l\'envoi.',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Erreur lors de l\'envoi du lien.');
        }
    }

    /**
     * Afficher le formulaire de réinitialisation
     */
    public function showResetForm(Request $request, string $token)
    {
        $user = User::where('password_reset_token', $token)->first();

        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lien invalide ou expiré.',
                ], 404);
            }

            return redirect()->route('users.login')->with('error', 'Lien invalide ou expiré.');
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'email' => $user->email,
            ]);
        }

        return view('auth.reset-password', compact('token'));
    }

    /**
     * Réinitialiser le mot de passe
     */
    public function resetPassword(Request $request, string $token)
    {
        $validated = $request->validate([
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = User::where('password_reset_token', $token)->first();

        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lien invalide ou expiré.',
                ], 404);
            }

            return redirect()->route('users.login')
                ->with('error', 'Lien invalide ou expiré.');
        }

        DB::beginTransaction();
        try {
            $user->update([
                'password' => Hash::make($validated['password']),
                'password_reset_token' => null,
            ]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Mot de passe réinitialisé avec succès.',
                ]);
            }

            return redirect()->route('users.login')->with('success', 'Mot de passe réinitialisé. Vous pouvez vous connecter.');

        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la réinitialisation.',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Erreur lors de la réinitialisation.');
        }
    }
}
