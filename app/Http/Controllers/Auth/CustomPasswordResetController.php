<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


/**
 * @OA\Tag(
 *     name="Réinitialisation de mot de passe",
 *     description="Endpoints pour la réinitialisation du mot de passe utilisateur"
 * )
 */
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
     *
     * @OA\Get(
     *     path="/auth/password/reset/{token}",
     *     operationId="showPasswordResetForm",
     *     tags={"Réinitialisation de mot de passe"},
     *     summary="Afficher le formulaire de réinitialisation",
     *     description="Vérifie la validité du token de réinitialisation et affiche le formulaire. Le token est valable 2 heures.",
     *     @OA\Parameter(
     *         name="token",
     *         in="path",
     *         required=true,
     *         description="Token de réinitialisation reçu par email",
     *         @OA\Schema(type="string", example="abc123xyz789...")
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         required=true,
     *         description="Adresse email de l'utilisateur",
     *         @OA\Schema(type="string", format="email", example="utilisateur@exemple.com")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Token valide - Formulaire accessible",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Token valide. Vous pouvez réinitialiser votre mot de passe."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="token", type="string", example="abc123xyz789..."),
     *                 @OA\Property(property="email", type="string", format="email", example="utilisateur@exemple.com")
     *             ),
     *             @OA\Property(
     *                 property="required_fields",
     *                 type="object",
     *                 description="Champs requis pour la réinitialisation",
     *                 @OA\Property(property="token", type="string", example="string|required"),
     *                 @OA\Property(property="email", type="string", example="string|required|email"),
     *                 @OA\Property(property="password", type="string", example="string|required|min:8"),
     *                 @OA\Property(property="password_confirmation", type="string", example="string|required|same:password")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Token invalide",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Ce lien de réinitialisation est invalide.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Token non trouvé",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Ce lien de réinitialisation est invalide.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=410,
     *         description="Token expiré",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Ce lien de réinitialisation a expiré (valable 2 heures). Veuillez contacter un administrateur.")
     *         )
     *     )
     * )
     */
    public function showResetForm(Request $request, $token)
    {
        $email = $request->email;

        // Vérifier si le token existe
        $passwordReset = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (!$passwordReset) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ce lien de réinitialisation est invalide.',
                ], 404);
            }

            return redirect()->route('auth.index')
                ->with('error', 'Ce lien de réinitialisation est invalide.');
        }

        // Vérifier si le token a expiré (2 heures)
        if (now()->diffInMinutes($passwordReset->created_at) > $this->tokenExpirationMinutes) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();

            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ce lien de réinitialisation a expiré (valable 2 heures). Veuillez contacter un administrateur.',
                ], 410);
            }

            return redirect()->route('auth.index')
                ->with('error', 'Ce lien de réinitialisation a expiré (valable 2 heures). Veuillez contacter un administrateur.');
        }

        // Vérifier si le token correspond
        if (!Hash::check($token, $passwordReset->token)) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ce lien de réinitialisation est invalide.',
                ], 401);
            }

            return redirect()->route('auth.index')
                ->with('error', 'Ce lien de réinitialisation est invalide.');
        }

        // Pour l'API : retourner les infos du formulaire
        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Token valide. Vous pouvez réinitialiser votre mot de passe.',
                'data' => [
                    'token' => $token,
                    'email' => $email,
                ],
                'required_fields' => [
                    'token' => 'string|required',
                    'email' => 'string|required|email',
                    'password' => 'string|required|min:8',
                    'password_confirmation' => 'string|required|same:password',
                ],
            ]);
        }

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $email,
        ]);
    }



    /**
     * Traite la réinitialisation du mot de passe.
     *
     * @OA\Post(
     *     path="/auth/password/reset",
     *     operationId="resetPassword",
     *     tags={"Réinitialisation de mot de passe"},
     *     summary="Réinitialiser le mot de passe",
     *     description="Réinitialise le mot de passe de l'utilisateur avec le token reçu par email. Tous les tokens d'accès existants sont révoqués.",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Données de réinitialisation",
     *         @OA\JsonContent(
     *             required={"token", "email", "password", "password_confirmation"},
     *             @OA\Property(
     *                 property="token",
     *                 type="string",
     *                 description="Token de réinitialisation reçu par email",
     *                 example="abc123xyz789..."
     *             ),
     *             @OA\Property(
     *                 property="email",
     *                 type="string",
     *                 format="email",
     *                 description="Adresse email de l'utilisateur",
     *                 example="utilisateur@exemple.com"
     *             ),
     *             @OA\Property(
     *                 property="password",
     *                 type="string",
     *                 format="password",
     *                 minLength=8,
     *                 description="Nouveau mot de passe (minimum 8 caractères)",
     *                 example="NouveauMotDePasse123"
     *             ),
     *             @OA\Property(
     *                 property="password_confirmation",
     *                 type="string",
     *                 format="password",
     *                 description="Confirmation du nouveau mot de passe",
     *                 example="NouveauMotDePasse123"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Mot de passe réinitialisé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Token invalide",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Ce lien de réinitialisation est invalide.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Utilisateur introuvable",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Utilisateur introuvable.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=410,
     *         description="Token expiré",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Ce lien de réinitialisation a expiré (valable 2 heures).")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erreur de validation",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur serveur",
     *         @OA\JsonContent(ref="#/components/schemas/ServerErrorResponse")
     *     )
     * )
     */
    public function reset(Request $request)
    {
        $messages = [
            'token.required' => 'Le token est obligatoire.',
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email' => 'L\'adresse email n\'est pas valide.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
        ];

        $validated = $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ], $messages);

        // Vérifier le token
        $passwordReset = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$passwordReset || !Hash::check($request->token, $passwordReset->token)) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ce lien de réinitialisation est invalide.',
                ], 401);
            }

            return back()->with('error', 'Ce lien de réinitialisation est invalide.');
        }

        // Vérifier l'expiration (2 heures)
        if (now()->diffInMinutes($passwordReset->created_at) > $this->tokenExpirationMinutes) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ce lien de réinitialisation a expiré (valable 2 heures).',
                ], 410);
            }

            return back()->with('error', 'Ce lien de réinitialisation a expiré (valable 2 heures).');
        }

        // Trouver l'utilisateur
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur introuvable.',
                ], 404);
            }

            return back()->with('error', 'Utilisateur introuvable.');
        }

        DB::beginTransaction();
        try {
            // Mettre à jour le mot de passe
            $user->update([
                'password' => Hash::make($request->password),
                'updated_by' => $user->id,
            ]);

            // Supprimer le token utilisé
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            // Optionnel : Supprimer tous les tokens d'accès existants (forcer la reconnexion)
            $user->tokens()->delete();

            DB::commit();

            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.',
                ]);
            }

            return redirect()->route('auth.index')
                ->with('success', 'Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.');

        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la réinitialisation du mot de passe. '.$e->getMessage(),
                    'error' => config('app.debug') ? $e->getMessage() : null,
                ], 500);
            }

            return back()->with('error', 'Erreur lors de la réinitialisation du mot de passe. '.$e->getMessage());
        }
    }
}
