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



/**
 * @OA\Tag(
 *     name="Authentification",
 *     description="Endpoints pour la gestion de l'authentification des utilisateurs"
 * )
 */
class AuthController extends Controller
{
        /**
     * Afficher le formulaire de connexion / Endpoint info
     *
     * @OA\Get(
     *     path="/auth",
     *     operationId="authIndex",
     *     tags={"Authentification"},
     *     summary="Informations sur l'endpoint de connexion",
     *     description="Retourne les informations sur l'endpoint de connexion et les champs requis pour s'authentifier.",
     *     @OA\Response(
     *         response=200,
     *         description="Informations de l'endpoint de connexion",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Endpoint de connexion disponible"),
     *             @OA\Property(property="method", type="string", example="POST"),
     *             @OA\Property(property="endpoint", type="string", example="http://example.com/auth"),
     *             @OA\Property(
     *                 property="required_fields",
     *                 type="object",
     *                 @OA\Property(property="email", type="string", example="string|required|email"),
     *                 @OA\Property(property="password", type="string", example="string|required|min:8")
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {

        // Pour les requêtes API (Postman, fetch, axios, etc.)
        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Endpoint de connexion disponible',
                'method' => 'POST',
                'endpoint' => route('login'),
                'required_fields' => [
                    'email' => 'string|required|email',
                    'password' => 'string|required|min:8',
                ],
            ]);
        }

        return view('auth.login');
    }

    /**
     * Traiter la demande de connexion (étape 1: envoi du code de vérification)
     *
     * @OA\Post(
     *     path="/auth",
     *     operationId="authLogin",
     *     tags={"Authentification"},
     *     summary="Connexion utilisateur - Étape 1",
     *     description="Authentifie l'utilisateur avec email/téléphone et mot de passe. Envoie un code de vérification par email pour la double authentification.",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Identifiants de connexion",
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(
     *                 property="email",
     *                 type="string",
     *                 description="Email ou numéro de téléphone de l'utilisateur",
     *                 example="utilisateur@exemple.com"
     *             ),
     *             @OA\Property(
     *                 property="password",
     *                 type="string",
     *                 format="password",
     *                 description="Mot de passe de l'utilisateur",
     *                 example="MonMotDePasse123"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Code de vérification envoyé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Code de vérification envoyé par email."),
     *             @OA\Property(property="token", type="string", example="abc123xyz789..."),
     *             @OA\Property(property="expires_in", type="string", example="600 secondes"),
     *             @OA\Property(
     *                 property="debug_code",
     *                 type="string",
     *                 nullable=true,
     *                 description="Code de vérification (uniquement en mode debug)",
     *                 example="123456"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Identifiants incorrects",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Compte désactivé",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Votre compte est désactivé. Contactez l'administrateur.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erreur de validation",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=429,
     *         description="Trop de tentatives",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Trop de tentatives. Réessayez dans 300 secondes.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur serveur",
     *         @OA\JsonContent(ref="#/components/schemas/ServerErrorResponse")
     *     )
     * )
     */
    public function login(Request $request)
    {

        $validated = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        // Rate limiting
        $key = 'login-attempt:' . $request->ip();

        // if (RateLimiter::tooManyAttempts($key, 5)) {

        //     $seconds = RateLimiter::availableIn($key);

        //     if ($request->expectsJson() || $request->wantsJson()) {
        //         return response()->json([
        //             'success' => false,
        //             'message' => "Trop de tentatives. Réessayez dans {$seconds} secondes.",
        //         ], 429);
        //     }

        //     throw ValidationException::withMessages([
        //         'email' => "Trop de tentatives. Réessayez dans {$seconds} secondes.",
        //     ]);
        // }


        // Rechercher l'utilisateur par email ou téléphone
        $loginField = $validated['email'];
        $user = null;

        // Détecter si c'est un email ou un numéro de téléphone
        if (filter_var($loginField, FILTER_VALIDATE_EMAIL)) {
            // C'est un email
            $user = User::where('email', $loginField)->first();
        } else {
            // C'est probablement un numéro de téléphone
            // Rechercher dans telephone_principal ou telephone_secondaire
            $user = User::where('telephone_principal', $loginField)
                ->orWhere('telephone_secondaire', $loginField)
                ->first();
        }

        // Vérifier les identifiants
        if (!$user || !Hash::check($validated['password'], $user->password)) {
            RateLimiter::hit($key, 300); // 5 minutes

            if ($request->expectsJson() || $request->wantsJson()) {
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
            if ($request->expectsJson() || $request->wantsJson()) {
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

            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Code de vérification envoyé par email.',
                    'token' => $token,
                    'expires_in' => "600 secondes", // 10 minutes en secondes
                    // En développement seulement - à retirer en production
                    'debug_code' => config('app.debug') ? $verificationCode : null,
                ]);
            }

            return redirect()->route('auth.verify.show', ['token' => $token])
                ->with('success', 'Un code de vérification a été envoyé à votre email.')
                ->with('debug_code', config('app.debug') ? $verificationCode : null);
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->expectsJson() || $request->wantsJson()) {
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
     *
     * @OA\Get(
     *     path="/auth/verify/{token}",
     *     operationId="showVerifyForm",
     *     tags={"Authentification"},
     *     summary="Afficher le formulaire de vérification",
     *     description="Vérifie si le token de vérification est valide et affiche le formulaire pour saisir le code.",
     *     @OA\Parameter(
     *         name="token",
     *         in="path",
     *         required=true,
     *         description="Token de vérification reçu lors de la connexion",
     *         @OA\Schema(type="string", example="abc123xyz789...")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Token valide",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Veuillez entrer le code de vérification."),
     *             @OA\Property(property="token", type="string", example="abc123xyz789...")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Code expiré ou invalide",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Code expiré ou invalide.")
     *         )
     *     )
     * )
     */
    public function showVerifyForm(Request $request, string $token)
    {

        // Vérifier si le token existe en cache
        if (!cache()->has("verification_code:{$token}")) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Code expiré ou invalide.',
                ], 404);
            }

            return redirect()->route('auth.login')->with('error', 'Code expiré ou invalide. Veuillez vous reconnecter.');
        }

        if ($request->expectsJson() || $request->wantsJson()) {
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
     *
     * @OA\Post(
     *     path="/auth/verify/{token}",
     *     operationId="verifyCode",
     *     tags={"Authentification"},
     *     summary="Vérifier le code - Étape 2",
     *     description="Vérifie le code de vérification à 6 chiffres et finalise la connexion en générant un token d'accès.",
     *     @OA\Parameter(
     *         name="token",
     *         in="path",
     *         required=true,
     *         description="Token de vérification",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"code"},
     *             @OA\Property(
     *                 property="code",
     *                 type="string",
     *                 minLength=6,
     *                 maxLength=6,
     *                 description="Code de vérification à 6 chiffres",
     *                 example="123456"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Connexion réussie",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Connexion réussie."),
     *             @OA\Property(
     *                 property="user",
     *                 type="object",
     *                 @OA\Property(property="id", type="string", format="uuid", example="550e8400-e29b-41d4-a716-446655440000"),
     *                 @OA\Property(property="nom_complet", type="string", example="Jean Dupont"),
     *                 @OA\Property(property="email", type="string", format="email", example="jean.dupont@exemple.com"),
     *                 @OA\Property(property="role", type="string", example="administrateur")
     *             ),
     *             @OA\Property(property="token", type="string", example="1|abc123xyz789..."),
     *             @OA\Property(property="token_type", type="string", example="Bearer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Code de vérification incorrect",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Code de vérification incorrect.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Token expiré ou utilisateur introuvable",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur serveur",
     *         @OA\JsonContent(ref="#/components/schemas/ServerErrorResponse")
     *     )
     * )
     */


    public function verifyCode(Request $request, string $token)
    {
        $validated = $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $cacheData = cache()->get("verification_code:{$token}");

        if (!$cacheData) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Code expiré ou invalide.',
                ], 404);
            }
            return redirect()->route('auth.index')->with('error', 'Code expiré ou invalide.');
        }

        if ($cacheData['code'] !== $validated['code']) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Code de vérification incorrect.',
                ], 401);
            }
            return back()->with('error', 'Code de vérification incorrect.');
        }

        $user = User::find($cacheData['user_id']);

        if (!$user) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur introuvable.',
                ], 404);
            }
            return redirect()->route('auth.index')->with('error', 'Utilisateur introuvable.');
        }

        DB::beginTransaction();
        try {
            $user->update([
                'password_reset_token' => null,
                'email_verified_at' => now(),
            ]);

            cache()->forget("verification_code:{$token}");

            // ===== DIFFÉRENCIER API vs WEB =====
            if ($request->expectsJson() || $request->wantsJson()) {
                // Pour l'API : créer un token Sanctum
                $apiToken = $user->createToken('auth_token')->plainTextToken;

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Connexion réussie.',
                    'user' => [
                        'id' => $user->id,
                        'nom_complet' => $user->nom_complet,
                        'email' => $user->email,
                        'role' => $user->role->name,
                    ],
                    'token' => $apiToken,
                    'token_type' => 'Bearer',
                ]);
            }

            // Pour le Web : utiliser la session
            Auth::guard('web')->login($user, $request->boolean('remember'));
            $request->session()->regenerate();

            DB::commit();

            return redirect()->intended(route('dashboard'))
                ->with('success', 'Connexion réussie. Bienvenue ' . $user->nom_complet);
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson() || $request->wantsJson()) {
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
     *
     * @OA\Post(
     *     path="/auth/verify/{token}/resend",
     *     operationId="resendCode",
     *     tags={"Authentification"},
     *     summary="Renvoyer le code de vérification",
     *     description="Génère et envoie un nouveau code de vérification par email. Limité à 3 demandes par minute.",
     *     @OA\Parameter(
     *         name="token",
     *         in="path",
     *         required=true,
     *         description="Token de vérification actuel",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Nouveau code envoyé",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Un nouveau code a été envoyé."),
     *             @OA\Property(property="expires_in", type="integer", example=600),
     *             @OA\Property(
     *                 property="debug_code",
     *                 type="string",
     *                 nullable=true,
     *                 description="Code (uniquement en mode debug)",
     *                 example="654321"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Session expirée ou utilisateur introuvable",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=429,
     *         description="Trop de demandes",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Veuillez attendre 60 secondes avant de demander un nouveau code.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur serveur",
     *         @OA\JsonContent(ref="#/components/schemas/ServerErrorResponse")
     *     )
     * )
     */
    public function resendCode(Request $request, string $token)
    {
        // Récupérer les données du cache
        $cacheData = cache()->get("verification_code:{$token}");

        if (!$cacheData) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session expirée. Veuillez vous reconnecter.',
                ], 404);
            }

            return redirect()->route('auth.index')->with('error', 'Session expirée. Veuillez vous reconnecter.');
        }

        $user = User::find($cacheData['user_id']);

        if (!$user) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur introuvable.',
                ], 404);
            }

            return redirect()->route('auth.index')
                ->with('error', 'Utilisateur introuvable.');
        }

        // Rate limiting pour le renvoi de code
        $key = 'resend-code:' . $user->id;
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);

            if ($request->expectsJson() || $request->wantsJson()) {
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

            if ($request->expectsJson() || $request->wantsJson()) {
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

            if ($request->expectsJson() || $request->wantsJson()) {
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
     *
     * @OA\Post(
     *     path="/auth/logout",
     *     operationId="logout",
     *     tags={"Authentification"},
     *     summary="Déconnexion de l'utilisateur",
     *     description="Supprime le token d'accès actuel et déconnecte l'utilisateur.",
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Déconnexion réussie",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Déconnexion réussie.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié",
     *         @OA\JsonContent(ref="#/components/schemas/UnauthenticatedResponse")
     *     )
     * )
     */
    public function logout(Request $request)
    {
        $user = $request->user();
        $userName = $user?->nom_complet ?? 'Utilisateur';

        // ===== POUR L'API (Sanctum) =====
        if ($request->expectsJson() || $request->wantsJson()) {
            // Supprimer le token actuel
            $user?->currentAccessToken()?->delete();

            return response()->json([
                'success' => true,
                'message' => 'Déconnexion réussie.',
            ]);
        }

        // ===== POUR LE WEB (Session) =====
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('auth.index')->with('success', "Au revoir {$userName} !");
    }

    /**
     * Afficher le formulaire de demande de réinitialisation
     *
     * @OA\Get(
     *     path="/auth/password/forgot",
     *     operationId="showForgotPasswordForm",
     *     tags={"Réinitialisation de mot de passe"},
     *     summary="Formulaire de demande de réinitialisation",
     *     description="Affiche les informations pour demander une réinitialisation de mot de passe.",
     *     @OA\Response(
     *         response=200,
     *         description="Informations du formulaire",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Formulaire de réinitialisation")
     *         )
     *     )
     * )
     */
    public function showForgotPasswordForm(Request $request)
    {
        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Formulaire de réinitialisation',
            ]);
        }

        return view('auth.forgot-password');
    }

    /**
     * Envoyer le lien de réinitialisation
     *
     * @OA\Post(
     *     path="/auth/password/email",
     *     operationId="sendResetLink",
     *     tags={"Réinitialisation de mot de passe"},
     *     summary="Envoyer le lien de réinitialisation",
     *     description="Envoie un email contenant le lien de réinitialisation du mot de passe. Limité à 3 demandes par 5 minutes.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(
     *                 property="email",
     *                 type="string",
     *                 format="email",
     *                 description="Adresse email de l'utilisateur",
     *                 example="utilisateur@exemple.com"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lien envoyé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Lien de réinitialisation envoyé."),
     *             @OA\Property(
     *                 property="debug_link",
     *                 type="string",
     *                 nullable=true,
     *                 description="Lien de réinitialisation (uniquement en mode debug)",
     *                 example="http://example.com/auth/password/reset/abc123..."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Email non trouvé ou erreur de validation",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=429,
     *         description="Trop de demandes",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Veuillez attendre 300 secondes.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur serveur",
     *         @OA\JsonContent(ref="#/components/schemas/ServerErrorResponse")
     *     )
     * )
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

            if ($request->expectsJson() || $request->wantsJson()) {
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

            if ($request->expectsJson() || $request->wantsJson()) {
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
            if ($request->expectsJson() || $request->wantsJson()) {
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
     * Changement de mot de passe (utilisateur connecté)
     *
     * @OA\Post(
     *     path="/auth/password/change",
     *     operationId="changePassword",
     *     tags={"Gestion du mot de passe"},
     *     summary="Changer le mot de passe",
     *     description="Permet à un utilisateur authentifié de changer son mot de passe actuel.",
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"current_password", "password", "password_confirmation"},
     *             @OA\Property(
     *                 property="current_password",
     *                 type="string",
     *                 format="password",
     *                 description="Mot de passe actuel",
     *                 example="AncienMotDePasse123"
     *             ),
     *             @OA\Property(
     *                 property="password",
     *                 type="string",
     *                 format="password",
     *                 minLength=8,
     *                 description="Nouveau mot de passe",
     *                 example="NouveauMotDePasse456"
     *             ),
     *             @OA\Property(
     *                 property="password_confirmation",
     *                 type="string",
     *                 format="password",
     *                 description="Confirmation du nouveau mot de passe",
     *                 example="NouveauMotDePasse456"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Mot de passe changé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Mot de passe changé avec succès.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Mot de passe actuel incorrect ou non authentifié",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erreur de validation",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=429,
     *         description="Trop de tentatives",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Trop de tentatives. Réessayez plus tard.")
     *         )
     *     )
     * )
     */
    public function changePassword(Request $request)
    {
        // À implémenter selon vos besoins
    }



        /**
     * Statut des tentatives de changement de mot de passe
     *
     * @OA\Get(
     *     path="/auth/password/status",
     *     operationId="passwordChangeStatus",
     *     tags={"Gestion du mot de passe"},
     *     summary="Statut des tentatives de changement",
     *     description="Retourne le statut des tentatives de changement de mot de passe pour l'utilisateur connecté.",
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Statut récupéré avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Statut récupéré."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="attempts_remaining", type="integer", example=3),
     *                 @OA\Property(property="locked_until", type="string", format="date-time", nullable=true),
     *                 @OA\Property(property="is_locked", type="boolean", example=false)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié",
     *         @OA\JsonContent(ref="#/components/schemas/UnauthenticatedResponse")
     *     )
     * )
     */
    public function passwordChangeStatus(Request $request)
    {
        // À implémenter selon vos besoins
    }



        /**
     * Statistiques des tentatives de changement de mot de passe
     *
     * @OA\Get(
     *     path="/auth/password/stats",
     *     operationId="getPasswordAttemptStats",
     *     tags={"Gestion du mot de passe"},
     *     summary="Statistiques des tentatives",
     *     description="Retourne les statistiques détaillées des tentatives de changement de mot de passe.",
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Statistiques récupérées",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Statistiques récupérées."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="total_attempts", type="integer", example=5),
     *                 @OA\Property(property="successful_attempts", type="integer", example=3),
     *                 @OA\Property(property="failed_attempts", type="integer", example=2),
     *                 @OA\Property(property="last_attempt_at", type="string", format="date-time"),
     *                 @OA\Property(property="last_success_at", type="string", format="date-time", nullable=true)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié",
     *         @OA\JsonContent(ref="#/components/schemas/UnauthenticatedResponse")
     *     )
     * )
     */
    public function getPasswordAttemptStats(Request $request)
    {
        // À implémenter selon vos besoins
    }

    /**
     * Afficher le formulaire de réinitialisation
     */
    public function showResetForm(Request $request, string $token)
    {
        $user = User::where('password_reset_token', $token)->first();

        if (!$user) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lien invalide ou expiré.',
                ], 404);
            }

            return redirect()->route('auth.index')->with('error', 'Lien invalide ou expiré.');
        }

        if ($request->expectsJson() || $request->wantsJson()) {
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
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lien invalide ou expiré.',
                ], 404);
            }

            return redirect()->route('auth.index')
                ->with('error', 'Lien invalide ou expiré.');
        }

        DB::beginTransaction();
        try {
            $user->update([
                'password' => Hash::make($validated['password']),
                'password_reset_token' => null,
            ]);

            DB::commit();

            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Mot de passe réinitialisé avec succès.',
                ]);
            }

            return redirect()->route('auth.index')->with('success', 'Mot de passe réinitialisé. Vous pouvez vous connecter.');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson() || $request->wantsJson()) {
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
