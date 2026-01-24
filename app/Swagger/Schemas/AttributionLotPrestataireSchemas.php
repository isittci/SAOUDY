<?php

namespace App\Swagger\Schemas;

/**
 * ============================================
 * SCHÉMAS POUR ATTRIBUTIONS DE LOTS AUX PRESTATAIRES
 * ============================================
 *
 * NOTE: Les schémas suivants sont définis dans d'autres fichiers et ne doivent pas être redéfinis ici :
 * - UserSummary (dans CaracteristiquesLotsSchemas.php)
 * - PrestataireSummary (dans CaracteristiquesLotsSchemas.php)
 * - AppelOffreSummary (dans CaracteristiquesLotsSchemas.php)
 * - ForbiddenResponse (dans CaracteristiquesLotsSchemas.php)
 * - PaginationMeta (dans CaracteristiquesLotsSchemas.php)
 * - PaginationLinks (dans TypeAppelOffreSchemas.php)
 * - ErrorResponse, ServerErrorResponse, UnauthenticatedResponse (dans Controller.php)
 *
 * @OA\Schema(
 *     schema="LotSummary",
 *     type="object",
 *     description="Résumé d'un lot",
 *     @OA\Property(
 *         property="id_lot",
 *         type="string",
 *         format="uuid",
 *         description="Identifiant UUID du lot",
 *         example="770e8400-e29b-41d4-a716-446655440002"
 *     ),
 *     @OA\Property(
 *         property="numero",
 *         type="string",
 *         description="Numéro du lot",
 *         example="LOT-001"
 *     ),
 *     @OA\Property(
 *         property="libelle",
 *         type="string",
 *         description="Libellé du lot",
 *         example="Construction de route"
 *     ),
 *     @OA\Property(
 *         property="appel_offre",
 *         ref="#/components/schemas/AppelOffreSummary",
 *         description="Appel d'offres parent"
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="ProformaSummary",
 *     type="object",
 *     description="Résumé d'une proforma",
 *     @OA\Property(
 *         property="id_proforma",
 *         type="string",
 *         format="uuid",
 *         description="Identifiant UUID de la proforma",
 *         example="880e8400-e29b-41d4-a716-446655440003"
 *     ),
 *     @OA\Property(
 *         property="numero_proforma",
 *         type="string",
 *         description="Numéro de la proforma",
 *         example="PROF-2024-001"
 *     ),
 *     @OA\Property(
 *         property="montant_retenu_proforma",
 *         type="number",
 *         format="decimal",
 *         description="Montant retenu",
 *         example=50000000.00
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="AttributionLotPrestataire",
 *     type="object",
 *     description="Attribution d'un lot à un prestataire avec suivi de l'exécution",
 *     @OA\Property(
 *         property="id_attribution",
 *         type="string",
 *         format="uuid",
 *         description="Identifiant unique UUID de l'attribution",
 *         example="550e8400-e29b-41d4-a716-446655440000"
 *     ),
 *     @OA\Property(
 *         property="prestataire_id",
 *         type="string",
 *         format="uuid",
 *         description="UUID du prestataire attributaire",
 *         example="660e8400-e29b-41d4-a716-446655440001"
 *     ),
 *     @OA\Property(
 *         property="lot_id",
 *         type="string",
 *         format="uuid",
 *         description="UUID du lot attribué",
 *         example="770e8400-e29b-41d4-a716-446655440002"
 *     ),
 *     @OA\Property(
 *         property="proforma_id",
 *         type="string",
 *         format="uuid",
 *         description="UUID de la proforma associée (relation 1:1)",
 *         example="880e8400-e29b-41d4-a716-446655440003"
 *     ),
 *     @OA\Property(
 *         property="parent_attribution_id",
 *         type="string",
 *         format="uuid",
 *         nullable=true,
 *         description="UUID de l'attribution parente (en cas de réattribution)",
 *         example=null
 *     ),
 *     @OA\Property(
 *         property="version_attribution",
 *         type="integer",
 *         description="Numéro de version de l'attribution (incrémenté à chaque réattribution)",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="is_active",
 *         type="boolean",
 *         description="Indique si c'est l'attribution active du lot",
 *         example=true
 *     ),
 *     @OA\Property(
 *         property="numero_attribution",
 *         type="string",
 *         maxLength=50,
 *         description="Numéro unique d'attribution (généré automatiquement)",
 *         example="ATTR-2024-001"
 *     ),
 *     @OA\Property(
 *         property="date_attribution",
 *         type="string",
 *         format="date",
 *         description="Date officielle de l'attribution",
 *         example="2024-01-15"
 *     ),
 *     @OA\Property(
 *         property="date_debut_prevue",
 *         type="string",
 *         format="date",
 *         description="Date de début prévue des travaux",
 *         example="2024-02-01"
 *     ),
 *     @OA\Property(
 *         property="date_fin_prevue",
 *         type="string",
 *         format="date",
 *         description="Date de fin prévue des travaux",
 *         example="2024-06-30"
 *     ),
 *     @OA\Property(
 *         property="date_debut_reelle",
 *         type="string",
 *         format="date",
 *         nullable=true,
 *         description="Date réelle de début des travaux",
 *         example="2024-02-05"
 *     ),
 *     @OA\Property(
 *         property="date_fin_reelle",
 *         type="string",
 *         format="date",
 *         nullable=true,
 *         description="Date réelle de fin des travaux",
 *         example=null
 *     ),
 *     @OA\Property(
 *         property="statut_attribution",
 *         type="integer",
 *         description="Statut de l'attribution (0:En attente, 1:Attribué, 2:Suspendu, 3:Retiré, 4:Terminé, 5:Annulé)",
 *         example=1,
 *         enum={0, 1, 2, 3, 4, 5}
 *     ),
 *     @OA\Property(
 *         property="statut_label",
 *         type="string",
 *         description="Libellé du statut",
 *         example="Attribué"
 *     ),
 *     @OA\Property(
 *         property="statut_color",
 *         type="string",
 *         description="Couleur associée au statut",
 *         example="green"
 *     ),
 *     @OA\Property(
 *         property="motif_suspension",
 *         type="string",
 *         nullable=true,
 *         description="Motif de la suspension (si applicable)",
 *         example=null
 *     ),
 *     @OA\Property(
 *         property="date_suspension",
 *         type="string",
 *         format="date-time",
 *         nullable=true,
 *         description="Date et heure de la suspension",
 *         example=null
 *     ),
 *     @OA\Property(
 *         property="date_reprise_prevue",
 *         type="string",
 *         format="date",
 *         nullable=true,
 *         description="Date prévue de reprise après suspension",
 *         example=null
 *     ),
 *     @OA\Property(
 *         property="date_reprise_reelle",
 *         type="string",
 *         format="date",
 *         nullable=true,
 *         description="Date réelle de reprise",
 *         example=null
 *     ),
 *     @OA\Property(
 *         property="motif_retrait",
 *         type="string",
 *         nullable=true,
 *         description="Motif du retrait (si applicable)",
 *         example=null
 *     ),
 *     @OA\Property(
 *         property="date_retrait",
 *         type="string",
 *         format="date-time",
 *         nullable=true,
 *         description="Date et heure du retrait",
 *         example=null
 *     ),
 *     @OA\Property(
 *         property="type_retrait",
 *         type="string",
 *         nullable=true,
 *         description="Type de retrait (volontaire, force, resiliation, abandon)",
 *         example=null,
 *         enum={"volontaire", "force", "resiliation", "abandon"}
 *     ),
 *     @OA\Property(
 *         property="jours_retard",
 *         type="integer",
 *         description="Nombre de jours de retard (calculé ou saisi)",
 *         example=0
 *     ),
 *     @OA\Property(
 *         property="pourcentage_avancement",
 *         type="number",
 *         format="decimal",
 *         description="Pourcentage d'avancement des travaux (0-100)",
 *         example=45.50
 *     ),
 *     @OA\Property(
 *         property="montant_engage",
 *         type="number",
 *         format="decimal",
 *         description="Montant total engagé (en FCFA)",
 *         example=50000000.00
 *     ),
 *     @OA\Property(
 *         property="montant_paye",
 *         type="number",
 *         format="decimal",
 *         description="Montant déjà payé (en FCFA)",
 *         example=22500000.00
 *     ),
 *     @OA\Property(
 *         property="montant_restant",
 *         type="number",
 *         format="decimal",
 *         description="Montant restant à payer (calculé)",
 *         example=27500000.00
 *     ),
 *     @OA\Property(
 *         property="observations",
 *         type="string",
 *         nullable=true,
 *         description="Observations générales sur l'attribution",
 *         example="Travaux en cours, avancement conforme"
 *     ),
 *     @OA\Property(
 *         property="conditions_particulieres",
 *         type="string",
 *         nullable=true,
 *         description="Conditions particulières de l'attribution",
 *         example="Paiement en 3 tranches : 30% / 40% / 30%"
 *     ),
 *     @OA\Property(
 *         property="duree_prevue",
 *         type="integer",
 *         nullable=true,
 *         description="Durée prévue en jours (calculée)",
 *         example=150
 *     ),
 *     @OA\Property(
 *         property="duree_reelle",
 *         type="integer",
 *         nullable=true,
 *         description="Durée réelle en jours (calculée)",
 *         example=null
 *     ),
 *     @OA\Property(
 *         property="created_by",
 *         type="string",
 *         format="uuid",
 *         description="UUID de l'utilisateur créateur",
 *         example="990e8400-e29b-41d4-a716-446655440004"
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
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="AttributionLotPrestataireDetailed",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/AttributionLotPrestataire"),
 *         @OA\Schema(
 *             @OA\Property(
 *                 property="prestataire",
 *                 ref="#/components/schemas/PrestataireSummary",
 *                 description="Informations du prestataire"
 *             ),
 *             @OA\Property(
 *                 property="lot",
 *                 ref="#/components/schemas/LotSummary",
 *                 description="Informations du lot"
 *             ),
 *             @OA\Property(
 *                 property="proforma",
 *                 ref="#/components/schemas/ProformaSummary",
 *                 description="Informations de la proforma"
 *             ),
 *             @OA\Property(
 *                 property="parent_attribution",
 *                 ref="#/components/schemas/AttributionLotPrestataire",
 *                 nullable=true,
 *                 description="Attribution parente (si réattribution)"
 *             ),
 *             @OA\Property(
 *                 property="child_attributions",
 *                 type="array",
 *                 description="Réattributions successives",
 *                 @OA\Items(ref="#/components/schemas/AttributionLotPrestataire")
 *             ),
 *             @OA\Property(
 *                 property="creator",
 *                 ref="#/components/schemas/UserSummary",
 *                 description="Utilisateur créateur"
 *             ),
 *             @OA\Property(
 *                 property="updater",
 *                 ref="#/components/schemas/UserSummary",
 *                 nullable=true,
 *                 description="Dernier utilisateur modificateur"
 *             ),
 *             @OA\Property(
 *                 property="deleter",
 *                 ref="#/components/schemas/UserSummary",
 *                 nullable=true,
 *                 description="Utilisateur qui a supprimé"
 *             )
 *         )
 *     }
 * )
 *
 * @OA\Schema(
 *     schema="AttributionLotPrestataireRequest",
 *     type="object",
 *     required={"prestataire_id", "lot_id", "proforma_id", "date_attribution", "date_debut_prevue", "date_fin_prevue"},
 *     description="Requête de création d'une attribution de lot",
 *     @OA\Property(
 *         property="prestataire_id",
 *         type="string",
 *         format="uuid",
 *         description="UUID du prestataire",
 *         example="660e8400-e29b-41d4-a716-446655440001"
 *     ),
 *     @OA\Property(
 *         property="lot_id",
 *         type="string",
 *         format="uuid",
 *         description="UUID du lot",
 *         example="770e8400-e29b-41d4-a716-446655440002"
 *     ),
 *     @OA\Property(
 *         property="proforma_id",
 *         type="string",
 *         format="uuid",
 *         description="UUID de la proforma (ou 'new' pour créer une nouvelle proforma)",
 *         example="880e8400-e29b-41d4-a716-446655440003"
 *     ),
 *     @OA\Property(
 *         property="date_attribution",
 *         type="string",
 *         format="date",
 *         description="Date d'attribution (ne peut pas être dans le futur)",
 *         example="2024-01-15"
 *     ),
 *     @OA\Property(
 *         property="date_debut_prevue",
 *         type="string",
 *         format="date",
 *         description="Date de début prévue (>= date_attribution)",
 *         example="2024-02-01"
 *     ),
 *     @OA\Property(
 *         property="date_fin_prevue",
 *         type="string",
 *         format="date",
 *         description="Date de fin prévue (> date_debut_prevue)",
 *         example="2024-06-30"
 *     ),
 *     @OA\Property(
 *         property="montant_engage",
 *         type="number",
 *         format="decimal",
 *         description="Montant engagé (FCFA)",
 *         example=50000000.00
 *     ),
 *     @OA\Property(
 *         property="observations",
 *         type="string",
 *         maxLength=2000,
 *         nullable=true,
 *         description="Observations",
 *         example="Appel d'offres n° AO-2024-001"
 *     ),
 *     @OA\Property(
 *         property="conditions_particulieres",
 *         type="string",
 *         maxLength=5000,
 *         nullable=true,
 *         description="Conditions particulières",
 *         example="Paiement en 3 tranches"
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="AttributionLotPrestataireUpdateRequest",
 *     type="object",
 *     description="Requête de modification d'une attribution",
 *     @OA\Property(
 *         property="date_debut_prevue",
 *         type="string",
 *         format="date",
 *         nullable=true,
 *         description="Nouvelle date de début prévue",
 *         example="2024-02-05"
 *     ),
 *     @OA\Property(
 *         property="date_fin_prevue",
 *         type="string",
 *         format="date",
 *         nullable=true,
 *         description="Nouvelle date de fin prévue",
 *         example="2024-07-15"
 *     ),
 *     @OA\Property(
 *         property="montant_engage",
 *         type="number",
 *         format="decimal",
 *         nullable=true,
 *         description="Nouveau montant engagé",
 *         example=52000000.00
 *     ),
 *     @OA\Property(
 *         property="observations",
 *         type="string",
 *         maxLength=2000,
 *         nullable=true,
 *         description="Observations",
 *         example="Modification suite à avenant"
 *     ),
 *     @OA\Property(
 *         property="conditions_particulieres",
 *         type="string",
 *         maxLength=5000,
 *         nullable=true,
 *         description="Conditions particulières",
 *         example="Avenant n°1 - Paiement en 4 tranches"
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="AttributionSuspendreRequest",
 *     type="object",
 *     required={"motif_suspension"},
 *     description="Requête de suspension d'une attribution",
 *     @OA\Property(
 *         property="motif_suspension",
 *         type="string",
 *         maxLength=500,
 *         description="Motif de la suspension",
 *         example="Problème de livraison des matériaux"
 *     ),
 *     @OA\Property(
 *         property="date_reprise_prevue",
 *         type="string",
 *         format="date",
 *         nullable=true,
 *         description="Date prévue de reprise",
 *         example="2024-03-15"
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="AttributionReprendreRequest",
 *     type="object",
 *     description="Requête de reprise d'une attribution suspendue",
 *     @OA\Property(
 *         property="observations",
 *         type="string",
 *         maxLength=500,
 *         nullable=true,
 *         description="Observations sur la reprise",
 *         example="Reprise des travaux après résolution du problème"
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="AttributionRetirerRequest",
 *     type="object",
 *     required={"motif_retrait", "type_retrait"},
 *     description="Requête de retrait d'une attribution",
 *     @OA\Property(
 *         property="motif_retrait",
 *         type="string",
 *         maxLength=500,
 *         description="Motif du retrait",
 *         example="Non-respect des délais contractuels"
 *     ),
 *     @OA\Property(
 *         property="type_retrait",
 *         type="string",
 *         description="Type de retrait",
 *         example="force",
 *         enum={"volontaire", "force", "resiliation", "abandon"}
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="AttributionTerminerRequest",
 *     type="object",
 *     description="Requête de terminaison d'une attribution",
 *     @OA\Property(
 *         property="observations",
 *         type="string",
 *         maxLength=500,
 *         nullable=true,
 *         description="Observations sur la terminaison",
 *         example="Travaux terminés avec succès"
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="AttributionAvancementRequest",
 *     type="object",
 *     required={"pourcentage_avancement"},
 *     description="Requête de mise à jour de l'avancement",
 *     @OA\Property(
 *         property="pourcentage_avancement",
 *         type="number",
 *         format="decimal",
 *         minimum=0,
 *         maximum=100,
 *         description="Pourcentage d'avancement",
 *         example=65.5
 *     ),
 *     @OA\Property(
 *         property="observations",
 *         type="string",
 *         maxLength=2000,
 *         nullable=true,
 *         description="Observations sur l'avancement",
 *         example="Avancement conforme au planning"
 *     ),
 *     @OA\Property(
 *         property="date_debut_reelle",
 *         type="string",
 *         format="date",
 *         nullable=true,
 *         description="Date réelle de début (si non encore renseignée)",
 *         example="2024-02-05"
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="AttributionReattribuerRequest",
 *     type="object",
 *     required={"prestataire_id", "proforma_id", "date_attribution", "date_debut_prevue", "date_fin_prevue", "motif_reattribution"},
 *     description="Requête de réattribution d'un lot à un nouveau prestataire",
 *     @OA\Property(
 *         property="prestataire_id",
 *         type="string",
 *         format="uuid",
 *         description="UUID du nouveau prestataire",
 *         example="660e8400-e29b-41d4-a716-446655440005"
 *     ),
 *     @OA\Property(
 *         property="proforma_id",
 *         type="string",
 *         format="uuid",
 *         description="UUID de la nouvelle proforma",
 *         example="880e8400-e29b-41d4-a716-446655440006"
 *     ),
 *     @OA\Property(
 *         property="date_attribution",
 *         type="string",
 *         format="date",
 *         description="Date de la nouvelle attribution",
 *         example="2024-04-01"
 *     ),
 *     @OA\Property(
 *         property="date_debut_prevue",
 *         type="string",
 *         format="date",
 *         description="Nouvelle date de début prévue",
 *         example="2024-04-15"
 *     ),
 *     @OA\Property(
 *         property="date_fin_prevue",
 *         type="string",
 *         format="date",
 *         description="Nouvelle date de fin prévue",
 *         example="2024-08-30"
 *     ),
 *     @OA\Property(
 *         property="motif_reattribution",
 *         type="string",
 *         maxLength=500,
 *         description="Motif de la réattribution",
 *         example="Résiliation du contrat initial"
 *     ),
 *     @OA\Property(
 *         property="montant_engage",
 *         type="number",
 *         format="decimal",
 *         nullable=true,
 *         description="Nouveau montant engagé",
 *         example=48000000.00
 *     ),
 *     @OA\Property(
 *         property="observations",
 *         type="string",
 *         maxLength=2000,
 *         nullable=true,
 *         description="Observations",
 *         example="Nouveau marché suite à résiliation"
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="AttributionStatistiques",
 *     type="object",
 *     description="Statistiques globales des attributions",
 *     @OA\Property(
 *         property="total",
 *         type="integer",
 *         description="Nombre total d'attributions",
 *         example=250
 *     ),
 *     @OA\Property(
 *         property="actives",
 *         type="integer",
 *         description="Nombre d'attributions actives",
 *         example=180
 *     ),
 *     @OA\Property(
 *         property="en_cours",
 *         type="integer",
 *         description="Nombre d'attributions en cours",
 *         example=120
 *     ),
 *     @OA\Property(
 *         property="suspendues",
 *         type="integer",
 *         description="Nombre d'attributions suspendues",
 *         example=15
 *     ),
 *     @OA\Property(
 *         property="terminees",
 *         type="integer",
 *         description="Nombre d'attributions terminées",
 *         example=95
 *     ),
 *     @OA\Property(
 *         property="en_retard",
 *         type="integer",
 *         description="Nombre d'attributions en retard",
 *         example=8
 *     ),
 *     @OA\Property(
 *         property="montant_total_engage",
 *         type="number",
 *         format="decimal",
 *         description="Montant total engagé (FCFA)",
 *         example=12500000000.00
 *     ),
 *     @OA\Property(
 *         property="montant_total_paye",
 *         type="number",
 *         format="decimal",
 *         description="Montant total payé (FCFA)",
 *         example=7500000000.00
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="AttributionPrestataireStatistiques",
 *     type="object",
 *     description="Statistiques des attributions d'un prestataire",
 *     @OA\Property(
 *         property="total",
 *         type="integer",
 *         description="Nombre total d'attributions",
 *         example=15
 *     ),
 *     @OA\Property(
 *         property="en_cours",
 *         type="integer",
 *         description="Nombre d'attributions en cours",
 *         example=8
 *     ),
 *     @OA\Property(
 *         property="terminees",
 *         type="integer",
 *         description="Nombre d'attributions terminées",
 *         example=5
 *     ),
 *     @OA\Property(
 *         property="suspendues",
 *         type="integer",
 *         description="Nombre d'attributions suspendues",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="retirees",
 *         type="integer",
 *         description="Nombre d'attributions retirées",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="montant_total_engage",
 *         type="number",
 *         format="decimal",
 *         description="Montant total engagé (FCFA)",
 *         example=750000000.00
 *     ),
 *     @OA\Property(
 *         property="montant_total_paye",
 *         type="number",
 *         format="decimal",
 *         description="Montant total payé (FCFA)",
 *         example=450000000.00
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="AttributionIndexResponse",
 *     type="object",
 *     description="Réponse de la liste des attributions",
 *     @OA\Property(
 *         property="success",
 *         type="boolean",
 *         example=true
 *     ),
 *     @OA\Property(
 *         property="data",
 *         type="object",
 *         @OA\Property(
 *             property="data",
 *             type="array",
 *             @OA\Items(ref="#/components/schemas/AttributionLotPrestataireDetailed")
 *         ),
 *         @OA\Property(
 *             property="links",
 *             ref="#/components/schemas/PaginationLinks"
 *         ),
 *         @OA\Property(
 *             property="meta",
 *             ref="#/components/schemas/PaginationMeta"
 *         )
 *     ),
 *     @OA\Property(
 *         property="statistiques",
 *         ref="#/components/schemas/AttributionStatistiques"
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="AttributionShowResponse",
 *     type="object",
 *     description="Réponse de détail d'une attribution",
 *     @OA\Property(
 *         property="success",
 *         type="boolean",
 *         example=true
 *     ),
 *     @OA\Property(
 *         property="data",
 *         ref="#/components/schemas/AttributionLotPrestataireDetailed"
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="AttributionStoreResponse",
 *     type="object",
 *     description="Réponse de création d'attribution",
 *     @OA\Property(
 *         property="success",
 *         type="boolean",
 *         example=true
 *     ),
 *     @OA\Property(
 *         property="message",
 *         type="string",
 *         example="Attribution créée avec succès."
 *     ),
 *     @OA\Property(
 *         property="data",
 *         ref="#/components/schemas/AttributionLotPrestataireDetailed"
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="AttributionActionResponse",
 *     type="object",
 *     description="Réponse générique pour les actions (suspendre, reprendre, retirer, terminer)",
 *     @OA\Property(
 *         property="success",
 *         type="boolean",
 *         example=true
 *     ),
 *     @OA\Property(
 *         property="message",
 *         type="string",
 *         example="Action effectuée avec succès."
 *     ),
 *     @OA\Property(
 *         property="data",
 *         ref="#/components/schemas/AttributionLotPrestataireDetailed"
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="AttributionHistoriqueResponse",
 *     type="object",
 *     description="Réponse d'historique d'attributions",
 *     @OA\Property(
 *         property="success",
 *         type="boolean",
 *         example=true
 *     ),
 *     @OA\Property(
 *         property="data",
 *         type="object",
 *         @OA\Property(
 *             property="lot",
 *             ref="#/components/schemas/LotSummary",
 *             description="Informations du lot (pour historiqueLot)"
 *         ),
 *         @OA\Property(
 *             property="prestataire",
 *             ref="#/components/schemas/PrestataireSummary",
 *             description="Informations du prestataire (pour historiquePrestataire)"
 *         ),
 *         @OA\Property(
 *             property="historique",
 *             type="array",
 *             description="Liste des attributions",
 *             @OA\Items(ref="#/components/schemas/AttributionLotPrestataireDetailed")
 *         ),
 *         @OA\Property(
 *             property="statistiques",
 *             ref="#/components/schemas/AttributionPrestataireStatistiques",
 *             description="Statistiques (pour historiquePrestataire)"
 *         )
 *     )
 * )
 */
class AttributionLotPrestataireSchemas
{
    // Ce fichier contient uniquement les annotations Swagger pour les Attributions de Lots aux Prestataires
    // Les schémas communs (UserSummary, LotSummary, PrestataireSummary, ProformaSummary, PaginationLinks, etc.)
    // sont définis dans d'autres fichiers de schémas
    // Les schémas de réponses d'erreur sont définis dans Controller.php
}
