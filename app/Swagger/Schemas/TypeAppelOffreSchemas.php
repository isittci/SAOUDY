<?php

namespace App\Swagger\Schemas;

/**
 * ============================================
 * SCHÉMAS POUR TYPES D'APPELS D'OFFRES
 * ============================================
 *
 * NOTE: Les schémas suivants sont définis dans d'autres fichiers et ne doivent pas être redéfinis ici :
 * - AppelOffreSummary (dans CaracteristiquesLotsSchemas.php)
 * - UserSummary (dans CaracteristiquesLotsSchemas.php)
 * - ForbiddenResponse (dans CaracteristiquesLotsSchemas.php)
 * - PaginationMeta (dans CaracteristiquesLotsSchemas.php)
 * - ErrorResponse, ServerErrorResponse, UnauthenticatedResponse (dans Controller.php)
 *
 * @OA\Schema(
 *     schema="TypeAppelOffre",
 *     type="object",
 *     description="Type d'appel d'offres (catégorie selon les montants des marchés)",
 *     @OA\Property(
 *         property="id_type_appel_offre",
 *         type="string",
 *         format="uuid",
 *         description="Identifiant unique UUID",
 *         example="550e8400-e29b-41d4-a716-446655440000"
 *     ),
 *     @OA\Property(
 *         property="libelle_type_appel_offre",
 *         type="string",
 *         maxLength=160,
 *         description="Libellé du type d'appel d'offres",
 *         example="Appel d'Offres Ouvert National"
 *     ),
 *     @OA\Property(
 *         property="code_type_appel_offre",
 *         type="string",
 *         maxLength=10,
 *         description="Code court utilisé dans les numéros d'AO",
 *         example="AON"
 *     ),
 *     @OA\Property(
 *         property="valeur_minimuim_type_appel_offre",
 *         type="number",
 *         format="decimal",
 *         description="Valeur minimale du marché (en FCFA)",
 *         example=50000000.00
 *     ),
 *     @OA\Property(
 *         property="valeur_maximuim_type_appel_offre",
 *         type="number",
 *         format="decimal",
 *         description="Valeur maximale du marché (en FCFA)",
 *         example=500000000.00
 *     ),
 *     @OA\Property(
 *         property="description_critere_type_appel_offre",
 *         type="string",
 *         nullable=true,
 *         description="Description détaillée du type et de ses critères",
 *         example="Appel d'offres ouvert à toutes les entreprises nationales pour les marchés de travaux, fournitures et services."
 *     ),
 *     @OA\Property(
 *         property="actif_type_appel_offre",
 *         type="boolean",
 *         description="Statut actif/inactif",
 *         example=true
 *     ),
 *     @OA\Property(
 *         property="version_type_appel_offre",
 *         type="integer",
 *         description="Numéro de version (incrémenté à chaque modification des valeurs)",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="motif_modification_type_appel_offre",
 *         type="string",
 *         maxLength=255,
 *         nullable=true,
 *         description="Motif de la dernière modification des valeurs",
 *         example="Ajustement des seuils suite à la réforme 2024"
 *     ),
 *     @OA\Property(
 *         property="parent_id",
 *         type="string",
 *         format="uuid",
 *         nullable=true,
 *         description="UUID de la version précédente (si versioning)",
 *         example=null
 *     ),
 *     @OA\Property(
 *         property="appel_offres_count",
 *         type="integer",
 *         description="Nombre d'appels d'offres utilisant ce type",
 *         example=15
 *     ),
 *     @OA\Property(
 *         property="created_by",
 *         type="string",
 *         format="uuid",
 *         description="UUID de l'utilisateur créateur",
 *         example="550e8400-e29b-41d4-a716-446655440001"
 *     ),
 *     @OA\Property(
 *         property="updated_by",
 *         type="string",
 *         format="uuid",
 *         nullable=true,
 *         description="UUID du dernier modificateur",
 *         example=null
 *     ),
 *     @OA\Property(
 *         property="deleted_by",
 *         type="string",
 *         format="uuid",
 *         nullable=true,
 *         description="UUID de l'utilisateur qui a supprimé",
 *         example=null
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="Date de création",
 *         example="2024-01-15T10:30:00Z"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="Date de dernière mise à jour",
 *         example="2024-01-20T14:45:00Z"
 *     ),
 *     @OA\Property(
 *         property="deleted_at",
 *         type="string",
 *         format="date-time",
 *         nullable=true,
 *         description="Date de suppression (soft delete)",
 *         example=null
 *     ),
 *     @OA\Property(
 *         property="creator",
 *         ref="#/components/schemas/UserSummary",
 *         description="Utilisateur créateur"
 *     ),
 *     @OA\Property(
 *         property="updater",
 *         ref="#/components/schemas/UserSummary",
 *         nullable=true,
 *         description="Dernier utilisateur modificateur"
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="TypeAppelOffreDetailed",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/TypeAppelOffre"),
 *         @OA\Schema(
 *             @OA\Property(
 *                 property="appel_offres",
 *                 type="array",
 *                 description="10 derniers appels d'offres associés",
 *                 @OA\Items(ref="#/components/schemas/AppelOffreSummary")
 *             ),
 *             @OA\Property(
 *                 property="deleter",
 *                 ref="#/components/schemas/UserSummary",
 *                 nullable=true,
 *                 description="Utilisateur qui a supprimé"
 *             ),
 *             @OA\Property(
 *                 property="parent",
 *                 ref="#/components/schemas/TypeAppelOffre",
 *                 nullable=true,
 *                 description="Version précédente du type"
 *             )
 *         )
 *     }
 * )
 *
 * @OA\Schema(
 *     schema="TypeAppelOffreRequest",
 *     type="object",
 *     required={"libelle_type_appel_offre", "valeur_minimuim_type_appel_offre", "valeur_maximuim_type_appel_offre"},
 *     description="Requête de création/modification d'un type d'appel d'offres",
 *     @OA\Property(
 *         property="libelle_type_appel_offre",
 *         type="string",
 *         maxLength=160,
 *         description="Libellé du type",
 *         example="Appel d'Offres Restreint"
 *     ),
 *     @OA\Property(
 *         property="valeur_minimuim_type_appel_offre",
 *         type="number",
 *         format="decimal",
 *         minimum=0,
 *         description="Valeur minimale (FCFA)",
 *         example=10000000
 *     ),
 *     @OA\Property(
 *         property="valeur_maximuim_type_appel_offre",
 *         type="number",
 *         format="decimal",
 *         description="Valeur maximale (FCFA)",
 *         example=50000000
 *     ),
 *     @OA\Property(
 *         property="description_critere_type_appel_offre",
 *         type="string",
 *         nullable=true,
 *         description="Description détaillée",
 *         example="Type réservé aux marchés de moyenne envergure"
 *     ),
 *     @OA\Property(
 *         property="actif_type_appel_offre",
 *         type="boolean",
 *         default=true,
 *         description="Statut actif",
 *         example=true
 *     ),
 *     @OA\Property(
 *         property="motif_modification_type_appel_offre",
 *         type="string",
 *         maxLength=255,
 *         nullable=true,
 *         description="Motif (obligatoire si modification des valeurs min/max)",
 *         example="Mise à jour annuelle des seuils"
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="PaginationLinks",
 *     type="object",
 *     description="Liens de pagination",
 *     @OA\Property(property="first", type="string", example="http://api.exemple.com/types-appels-offres?page=1"),
 *     @OA\Property(property="last", type="string", example="http://api.exemple.com/types-appels-offres?page=10"),
 *     @OA\Property(property="prev", type="string", nullable=true, example=null),
 *     @OA\Property(property="next", type="string", nullable=true, example="http://api.exemple.com/types-appels-offres?page=2")
 * )
 */
class TypeAppelOffreSchemas
{
    // Ce fichier contient uniquement les annotations Swagger pour les Types d'Appels d'Offres
    // Les schémas communs (AppelOffreSummary, UserSummary, etc.) sont définis dans CaracteristiquesLotsSchemas.php
    // Les schémas de réponses d'erreur sont définis dans Controller.php
}
