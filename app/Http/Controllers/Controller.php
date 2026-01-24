<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;


/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="API Gestion des Appels d'Offres",
 *     description="Documentation API pour le système de gestion des appels d'offres. Cette API gère l'authentification des utilisateurs avec double authentification (2FA) par code email.",
 *     termsOfService="https://isittci.com/contact",
 *     @OA\Contact(
 *         name="Support Technique",
 *         email="support@isittci.com",
 *         url="https://isittci.com/bienvenue"
 *     ),
 *     @OA\License(
 *         name="Propriétaire",
 *         url="https://isittci.com/contact"
 *     )
 * )
 *
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="Serveur API Principal"
 * )
 *
 * @OA\Server(
 *     url="http://localhost:8000/api",
 *     description="Serveur de développement local"
 * )
 *
 * @OA\Server(
 *     url="https://district-yamoussoukro.isittci.com/api",
 *     description="Serveur de production"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Entrez le token Bearer obtenu après la connexion. Format: Bearer {token}"
 * )
 *
 * @OA\Tag(
 *     name="Authentification",
 *     description="Endpoints pour la connexion et la déconnexion des utilisateurs avec authentification à deux facteurs (2FA)"
 * )
 *
 * @OA\Tag(
 *     name="Réinitialisation de mot de passe",
 *     description="Endpoints pour la réinitialisation du mot de passe par email"
 * )
 *
 * @OA\Tag(
 *     name="Gestion du mot de passe",
 *     description="Endpoints pour la gestion du mot de passe des utilisateurs authentifiés"
 * )
 *
 * ============================================
 * SCHÉMAS DE RÉPONSE COMMUNS
 * ============================================
 *
 * @OA\Schema(
 *     schema="SuccessResponse",
 *     type="object",
 *     description="Réponse de succès standard",
 *     @OA\Property(
 *         property="success",
 *         type="boolean",
 *         example=true,
 *         description="Indicateur de succès"
 *     ),
 *     @OA\Property(
 *         property="message",
 *         type="string",
 *         example="Opération réussie.",
 *         description="Message descriptif"
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="ErrorResponse",
 *     type="object",
 *     description="Réponse d'erreur standard",
 *     @OA\Property(
 *         property="success",
 *         type="boolean",
 *         example=false,
 *         description="Indicateur d'échec"
 *     ),
 *     @OA\Property(
 *         property="message",
 *         type="string",
 *         example="Une erreur est survenue.",
 *         description="Message d'erreur"
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="ValidationErrorResponse",
 *     type="object",
 *     description="Réponse d'erreur de validation",
 *     @OA\Property(
 *         property="message",
 *         type="string",
 *         example="Les données fournies ne sont pas valides.",
 *         description="Message d'erreur général"
 *     ),
 *     @OA\Property(
 *         property="errors",
 *         type="object",
 *         description="Détails des erreurs par champ",
 *         @OA\AdditionalProperties(
 *             type="array",
 *             @OA\Items(type="string")
 *         ),
 *         example={
 *             "email": {"L'adresse email est obligatoire.", "L'adresse email n'est pas valide."},
 *             "password": {"Le mot de passe doit contenir au moins 8 caractères."}
 *         }
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="ServerErrorResponse",
 *     type="object",
 *     description="Réponse d'erreur serveur",
 *     @OA\Property(
 *         property="success",
 *         type="boolean",
 *         example=false
 *     ),
 *     @OA\Property(
 *         property="message",
 *         type="string",
 *         example="Erreur interne du serveur."
 *     ),
 *     @OA\Property(
 *         property="error",
 *         type="string",
 *         nullable=true,
 *         description="Détails de l'erreur (uniquement en mode debug)",
 *         example="SQLSTATE[42S02]: Base table or view not found..."
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="UnauthenticatedResponse",
 *     type="object",
 *     description="Réponse pour utilisateur non authentifié",
 *     @OA\Property(
 *         property="message",
 *         type="string",
 *         example="Unauthenticated.",
 *         description="Message d'erreur d'authentification"
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="TooManyRequestsResponse",
 *     type="object",
 *     description="Réponse pour trop de tentatives",
 *     @OA\Property(
 *         property="success",
 *         type="boolean",
 *         example=false
 *     ),
 *     @OA\Property(
 *         property="message",
 *         type="string",
 *         example="Trop de tentatives. Réessayez dans 60 secondes."
 *     )
 * )
 *
 * ============================================
 * SCHÉMAS DE MODÈLES
 * ============================================
 *
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     description="Modèle utilisateur",
 *     @OA\Property(
 *         property="id",
 *         type="string",
 *         format="uuid",
 *         example="550e8400-e29b-41d4-a716-446655440000",
 *         description="Identifiant unique UUID de l'utilisateur"
 *     ),
 *     @OA\Property(
 *         property="nom_complet",
 *         type="string",
 *         maxLength=100,
 *         example="Jean Dupont",
 *         description="Nom complet de l'utilisateur"
 *     ),
 *     @OA\Property(
 *         property="email",
 *         type="string",
 *         format="email",
 *         example="jean.dupont@exemple.com",
 *         description="Adresse email unique"
 *     ),
 *     @OA\Property(
 *         property="telephone_principal",
 *         type="string",
 *         nullable=true,
 *         example="+225 07 00 00 00 00",
 *         description="Numéro de téléphone principal"
 *     ),
 *     @OA\Property(
 *         property="telephone_secondaire",
 *         type="string",
 *         nullable=true,
 *         example="+225 05 00 00 00 00",
 *         description="Numéro de téléphone secondaire"
 *     ),
 *     @OA\Property(
 *         property="role",
 *         type="string",
 *         example="administrateur",
 *         description="Rôle de l'utilisateur"
 *     ),
 *     @OA\Property(
 *         property="statut",
 *         type="string",
 *         enum={"0", "1"},
 *         example="1",
 *         description="Statut du compte (0 = inactif, 1 = actif)"
 *     ),
 *     @OA\Property(
 *         property="email_verified_at",
 *         type="string",
 *         format="date-time",
 *         nullable=true,
 *         example="2024-01-15T10:30:00Z",
 *         description="Date de vérification de l'email"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         example="2024-01-01T00:00:00Z",
 *         description="Date de création"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         example="2024-01-15T10:30:00Z",
 *         description="Date de dernière mise à jour"
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="AuthenticatedUser",
 *     type="object",
 *     description="Informations de l'utilisateur authentifié (retournées après connexion)",
 *     @OA\Property(
 *         property="id",
 *         type="string",
 *         format="uuid",
 *         example="550e8400-e29b-41d4-a716-446655440000"
 *     ),
 *     @OA\Property(
 *         property="nom_complet",
 *         type="string",
 *         example="Jean Dupont"
 *     ),
 *     @OA\Property(
 *         property="email",
 *         type="string",
 *         format="email",
 *         example="jean.dupont@exemple.com"
 *     ),
 *     @OA\Property(
 *         property="role",
 *         type="string",
 *         example="administrateur"
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="LoginRequest",
 *     type="object",
 *     required={"email", "password"},
 *     description="Requête de connexion",
 *     @OA\Property(
 *         property="email",
 *         type="string",
 *         description="Email ou numéro de téléphone",
 *         example="utilisateur@exemple.com"
 *     ),
 *     @OA\Property(
 *         property="password",
 *         type="string",
 *         format="password",
 *         description="Mot de passe",
 *         example="MonMotDePasse123"
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="VerifyCodeRequest",
 *     type="object",
 *     required={"code"},
 *     description="Requête de vérification du code 2FA",
 *     @OA\Property(
 *         property="code",
 *         type="string",
 *         minLength=6,
 *         maxLength=6,
 *         pattern="^[0-9]{6}$",
 *         description="Code de vérification à 6 chiffres",
 *         example="123456"
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="ResetPasswordRequest",
 *     type="object",
 *     required={"token", "email", "password", "password_confirmation"},
 *     description="Requête de réinitialisation du mot de passe",
 *     @OA\Property(
 *         property="token",
 *         type="string",
 *         description="Token de réinitialisation",
 *         example="abc123xyz789..."
 *     ),
 *     @OA\Property(
 *         property="email",
 *         type="string",
 *         format="email",
 *         description="Adresse email",
 *         example="utilisateur@exemple.com"
 *     ),
 *     @OA\Property(
 *         property="password",
 *         type="string",
 *         format="password",
 *         minLength=8,
 *         description="Nouveau mot de passe",
 *         example="NouveauMotDePasse123"
 *     ),
 *     @OA\Property(
 *         property="password_confirmation",
 *         type="string",
 *         format="password",
 *         description="Confirmation du mot de passe",
 *         example="NouveauMotDePasse123"
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="ChangePasswordRequest",
 *     type="object",
 *     required={"current_password", "password", "password_confirmation"},
 *     description="Requête de changement de mot de passe",
 *     @OA\Property(
 *         property="current_password",
 *         type="string",
 *         format="password",
 *         description="Mot de passe actuel",
 *         example="AncienMotDePasse123"
 *     ),
 *     @OA\Property(
 *         property="password",
 *         type="string",
 *         format="password",
 *         minLength=8,
 *         description="Nouveau mot de passe",
 *         example="NouveauMotDePasse456"
 *     ),
 *     @OA\Property(
 *         property="password_confirmation",
 *         type="string",
 *         format="password",
 *         description="Confirmation du nouveau mot de passe",
 *         example="NouveauMotDePasse456"
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="ForgotPasswordRequest",
 *     type="object",
 *     required={"email"},
 *     description="Requête de demande de réinitialisation",
 *     @OA\Property(
 *         property="email",
 *         type="string",
 *         format="email",
 *         description="Adresse email du compte",
 *         example="utilisateur@exemple.com"
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="LoginSuccessResponse",
 *     type="object",
 *     description="Réponse après envoi du code de vérification",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Code de vérification envoyé par email."),
 *     @OA\Property(property="token", type="string", example="abc123xyz789..."),
 *     @OA\Property(property="expires_in", type="string", example="600 secondes"),
 *     @OA\Property(
 *         property="debug_code",
 *         type="string",
 *         nullable=true,
 *         example="123456",
 *         description="Code de vérification (mode debug uniquement)"
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="AuthenticatedResponse",
 *     type="object",
 *     description="Réponse après vérification du code réussie",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Connexion réussie."),
 *     @OA\Property(
 *         property="user",
 *         ref="#/components/schemas/AuthenticatedUser"
 *     ),
 *     @OA\Property(
 *         property="token",
 *         type="string",
 *         example="1|abc123xyz789...",
 *         description="Token d'accès Bearer"
 *     ),
 *     @OA\Property(
 *         property="token_type",
 *         type="string",
 *         example="Bearer",
 *         description="Type de token"
 *     )
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
