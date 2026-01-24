<?php

namespace App\Swagger\Schemas;

/**
 * ============================================
 * SCHÉMAS POUR APPELS D'OFFRES
 * ============================================
 *
 * NOTE: Les schémas suivants sont définis dans CaracteristiquesLotsSchemas.php :
 * - CaracteristiqueAppelOffre, CaracteristiqueAppelOffreDetailed, CaracteristiqueAppelOffreRequest
 * - Lot, LotDetailed, LotRequest
 * - Prestataire, PrestataireSummary
 * - Proforma
 * - CritereEvaluation, Evaluation
 * - Attribution
 * - AppelOffreSummary, UserSummary
 * - ErrorResponse, ServerErrorResponse, etc.
 *
 * @OA\Schema(
 *     schema="AppelOffre",
 *     type="object",
 *     description="Appel d'offres complet",
 *     @OA\Property(
 *         property="id_appel_offre",
 *         type="string",
 *         format="uuid",
 *         description="Identifiant unique UUID",
 *         example="660e8400-e29b-41d4-a716-446655440000"
 *     ),
 *     @OA\Property(
 *         property="type_appel_offre_id",
 *         type="string",
 *         format="uuid",
 *         description="UUID du type d'appel d'offres",
 *         example="550e8400-e29b-41d4-a716-446655440000"
 *     ),
 *     @OA\Property(
 *         property="numero_appel_offre",
 *         type="string",
 *         maxLength=20,
 *         description="Numéro officiel de l'appel d'offres",
 *         example="AON-2024-0042"
 *     ),
 *     @OA\Property(
 *         property="libelle_critere_appel_offre",
 *         type="string",
 *         maxLength=160,
 *         description="Libellé/titre de l'appel d'offres",
 *         example="Construction d'un centre de santé communautaire"
 *     ),
 *     @OA\Property(
 *         property="objet_critere_appel_offre",
 *         type="string",
 *         nullable=true,
 *         description="Objet détaillé de l'appel d'offres",
 *         example="Travaux de construction d'un centre de santé de type CSU"
 *     ),
 *     @OA\Property(
 *         property="montant_global_appel_offre",
 *         type="number",
 *         format="decimal",
 *         description="Montant total estimé (FCFA)",
 *         example=150000000.00
 *     ),
 *     @OA\Property(
 *         property="description_critere_critere_appel_offre",
 *         type="string",
 *         description="Description détaillée des travaux/prestations",
 *         example="Les travaux comprennent : gros œuvre, second œuvre, VRD..."
 *     ),
 *     @OA\Property(
 *         property="date_publication_critere_appel_offre",
 *         type="string",
 *         format="date-time",
 *         nullable=true,
 *         description="Date de publication de l'appel d'offres",
 *         example="2024-02-01T08:00:00Z"
 *     ),
 *     @OA\Property(
 *         property="statut_evaluation_critere_appel_offre",
 *         type="integer",
 *         enum={0, 1},
 *         description="Statut d'évaluation (0=inactif, 1=actif)",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="etat_appel_offre",
 *         type="integer",
 *         enum={0, 1, 2, 3},
 *         description="État de l'AO (0=En attente, 1=En cours, 2=Terminé, 3=Clôturé)",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="conditions_participation_critere_appel_offre",
 *         type="string",
 *         nullable=true,
 *         description="Conditions de participation",
 *         example="Être inscrit au registre du commerce..."
 *     ),
 *     @OA\Property(
 *         property="criteres_selection_critere_appel_offre",
 *         type="string",
 *         nullable=true,
 *         description="Critères de sélection des offres",
 *         example="Prix (40%), Qualité technique (35%)..."
 *     ),
 *     @OA\Property(
 *         property="lots_count",
 *         type="integer",
 *         description="Nombre de lots associés",
 *         example=5
 *     ),
 *     @OA\Property(
 *         property="type_appel_offre",
 *         ref="#/components/schemas/TypeAppelOffre",
 *         description="Type d'appel d'offres associé"
 *     ),
 *     @OA\Property(
 *         property="creator",
 *         ref="#/components/schemas/UserSummary",
 *         description="Utilisateur créateur"
 *     ),
 *     @OA\Property(
 *         property="created_by",
 *         type="string",
 *         format="uuid"
 *     ),
 *     @OA\Property(
 *         property="updated_by",
 *         type="string",
 *         format="uuid",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="deleted_by",
 *         type="string",
 *         format="uuid",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         example="2024-01-15T10:30:00Z"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         example="2024-01-20T14:45:00Z"
 *     ),
 *     @OA\Property(
 *         property="deleted_at",
 *         type="string",
 *         format="date-time",
 *         nullable=true
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="AppelOffreDetailed",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/AppelOffre"),
 *         @OA\Schema(
 *             @OA\Property(
 *                 property="caracteristiques",
 *                 type="array",
 *                 description="Historique des caractéristiques",
 *                 @OA\Items(ref="#/components/schemas/CaracteristiqueAppelOffre")
 *             ),
 *             @OA\Property(
 *                 property="caracteristique_active",
 *                 ref="#/components/schemas/CaracteristiqueAppelOffre",
 *                 description="Caractéristique active actuelle"
 *             ),
 *             @OA\Property(
 *                 property="lots",
 *                 type="array",
 *                 description="Lots de l'appel d'offres",
 *                 @OA\Items(ref="#/components/schemas/Lot")
 *             ),
 *             @OA\Property(
 *                 property="updater",
 *                 ref="#/components/schemas/UserSummary",
 *                 nullable=true
 *             ),
 *             @OA\Property(
 *                 property="deleter",
 *                 ref="#/components/schemas/UserSummary",
 *                 nullable=true
 *             )
 *         )
 *     }
 * )
 *
 * @OA\Schema(
 *     schema="AppelOffreRequest",
 *     type="object",
 *     required={"type_appel_offre_id", "libelle_critere_appel_offre", "montant_global_appel_offre", "description_critere_critere_appel_offre"},
 *     description="Requête de création d'un appel d'offres",
 *     @OA\Property(
 *         property="type_appel_offre_id",
 *         type="string",
 *         format="uuid",
 *         description="UUID du type d'appel d'offres"
 *     ),
 *     @OA\Property(
 *         property="libelle_critere_appel_offre",
 *         type="string",
 *         maxLength=160
 *     ),
 *     @OA\Property(
 *         property="objet_critere_appel_offre",
 *         type="string",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="montant_global_appel_offre",
 *         type="number",
 *         minimum=5
 *     ),
 *     @OA\Property(
 *         property="description_critere_critere_appel_offre",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="date_publication_critere_appel_offre",
 *         type="string",
 *         format="date-time",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="conditions_participation_critere_appel_offre",
 *         type="string",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="criteres_selection_critere_appel_offre",
 *         type="string",
 *         nullable=true
 *     )
 * )
 *
 * ============================================
 * SCHÉMAS D'ÉTAT DES APPELS D'OFFRES
 * ============================================
 *
 * @OA\Schema(
 *     schema="EtatAppelOffre",
 *     type="object",
 *     description="États possibles d'un appel d'offres",
 *     @OA\Property(
 *         property="code",
 *         type="integer",
 *         enum={0, 1, 2, 3},
 *         description="Code de l'état"
 *     ),
 *     @OA\Property(
 *         property="libelle",
 *         type="string",
 *         enum={"En attente", "En cours", "Terminé", "Clôturé"},
 *         description="Libellé de l'état"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         description="Description de l'état"
 *     ),
 *     example={
 *         "states": {
 *             {"code": 0, "libelle": "En attente", "description": "L'appel d'offres n'a pas encore de lots"},
 *             {"code": 1, "libelle": "En cours", "description": "L'appel d'offres a des lots actifs"},
 *             {"code": 2, "libelle": "Terminé", "description": "Tous les lots sont attribués et complets"},
 *             {"code": 3, "libelle": "Clôturé", "description": "L'appel d'offres est définitivement fermé"}
 *         }
 *     }
 * )
 *
 * ============================================
 * SCHÉMAS DE STATISTIQUES
 * ============================================
 *
 * @OA\Schema(
 *     schema="StatistiquesAppelOffre",
 *     type="object",
 *     description="Statistiques d'un appel d'offres",
 *     @OA\Property(
 *         property="general",
 *         type="object",
 *         @OA\Property(property="numero", type="string", example="AON-2024-0042"),
 *         @OA\Property(property="montant_global", type="number", example=150000000),
 *         @OA\Property(property="est_actif", type="boolean", example=true),
 *         @OA\Property(property="est_en_cours", type="boolean", example=true),
 *         @OA\Property(property="est_cloture", type="boolean", example=false)
 *     ),
 *     @OA\Property(
 *         property="lots",
 *         type="object",
 *         @OA\Property(property="total", type="integer", example=5),
 *         @OA\Property(property="attribues", type="integer", example=3),
 *         @OA\Property(property="non_attribues", type="integer", example=2),
 *         @OA\Property(property="montant_total", type="number", example=120000000)
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="StatistiquesLot",
 *     type="object",
 *     description="Statistiques d'un lot",
 *     @OA\Property(
 *         property="general",
 *         type="object",
 *         @OA\Property(property="numero", type="string", example="LOT-001"),
 *         @OA\Property(property="libelle", type="string", example="Gros œuvre"),
 *         @OA\Property(property="duree_prevue_jours", type="integer", example=180),
 *         @OA\Property(property="est_attribue", type="boolean", example=true),
 *         @OA\Property(property="est_retire", type="boolean", example=false)
 *     ),
 *     @OA\Property(
 *         property="attribution",
 *         type="object",
 *         nullable=true,
 *         @OA\Property(property="prestataire", type="string"),
 *         @OA\Property(property="montant", type="number"),
 *         @OA\Property(property="date_attribution", type="string", format="date")
 *     )
 * )
 */
class AppelOffreSchemas
{
    // Ce fichier contient uniquement les annotations Swagger pour les Appels d'Offres
    // Les schémas partagés (Lot, Caracteristique, etc.) sont dans CaracteristiquesLotsSchemas.php
}
