<?php

namespace App\Swagger\Schemas;

/**
 * ============================================
 * SCHÉMAS POUR CARACTÉRISTIQUES DES APPELS D'OFFRES
 * ============================================
 *
 * NOTE: Les schémas suivants sont définis dans Controller.php et ne doivent pas être redéfinis ici :
 * - ErrorResponse
 * - ServerErrorResponse
 * - UnauthenticatedResponse
 * - ValidationErrorResponse
 * - SuccessResponse
 *
 * @OA\Schema(
 *     schema="CaracteristiqueAppelOffre",
 *     type="object",
 *     description="Caractéristique d'un appel d'offres (dates, garanties, modalités)",
 *     @OA\Property(
 *         property="id_caracteristique_appel_offre",
 *         type="string",
 *         format="uuid",
 *         description="Identifiant unique UUID",
 *         example="880e8400-e29b-41d4-a716-446655440000"
 *     ),
 *     @OA\Property(
 *         property="appel_offre_id",
 *         type="string",
 *         format="uuid",
 *         description="UUID de l'appel d'offres parent"
 *     ),
 *     @OA\Property(
 *         property="version_caracteristique_appel_offre",
 *         type="integer",
 *         description="Numéro de version",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="date_demarrage_prevue_caracteristique_appel_offre",
 *         type="string",
 *         format="date",
 *         description="Date prévue de démarrage des travaux",
 *         example="2024-03-01"
 *     ),
 *     @OA\Property(
 *         property="duree_estimee_jours_caracteristique_appel_offre",
 *         type="integer",
 *         description="Durée estimée en jours (calculée automatiquement)",
 *         example=214
 *     ),
 *     @OA\Property(
 *         property="date_livraison_previsionnelle_caracteristique_appel_offre",
 *         type="string",
 *         format="date",
 *         description="Date prévue de livraison",
 *         example="2024-09-30"
 *     ),
 *     @OA\Property(
 *         property="lieu_execution_caracteristique_appel_offre",
 *         type="string",
 *         maxLength=255,
 *         description="Lieu d'exécution des travaux",
 *         example="Yamoussoukro, Quartier Habitat"
 *     ),
 *     @OA\Property(
 *         property="montant_garantie_caracteristique_appel_offre",
 *         type="number",
 *         format="decimal",
 *         nullable=true,
 *         description="Caution de bonne exécution (5-10% du marché)",
 *         example=15000000.00
 *     ),
 *     @OA\Property(
 *         property="delai_garantie_jours_caracteristique_appel_offre",
 *         type="number",
 *         nullable=true,
 *         description="Durée de garantie après réception (en jours)",
 *         example=365
 *     ),
 *     @OA\Property(
 *         property="conditions_paiement_caracteristique_appel_offre",
 *         type="string",
 *         nullable=true,
 *         description="Modalités de paiement",
 *         example="30% avance, 40% mi-parcours, 30% livraison"
 *     ),
 *     @OA\Property(
 *         property="modalites_execution_caracteristique_appel_offre",
 *         type="string",
 *         nullable=true,
 *         description="Exigences particulières d'exécution"
 *     ),
 *     @OA\Property(
 *         property="documents_requis_caracteristique_appel_offre",
 *         type="string",
 *         nullable=true,
 *         description="Liste des documents à fournir",
 *         example="Attestation fiscale, Assurance décennale, Caution bancaire"
 *     ),
 *     @OA\Property(
 *         property="is_active_caracteristique_appel_offre",
 *         type="boolean",
 *         description="Indique si cette version est active",
 *         example=true
 *     ),
 *     @OA\Property(
 *         property="autres_informations_caracteristique_appel_offre",
 *         type="string",
 *         nullable=true,
 *         description="Informations complémentaires"
 *     ),
 *     @OA\Property(
 *         property="motif_modification_caracteristique_appel_offre",
 *         type="string",
 *         nullable=true,
 *         description="Motif de la modification (pour les versions > 1)"
 *     ),
 *     @OA\Property(
 *         property="parent_id",
 *         type="string",
 *         format="uuid",
 *         nullable=true,
 *         description="UUID de la version précédente"
 *     ),
 *     @OA\Property(property="created_by", type="string", format="uuid"),
 *     @OA\Property(property="updated_by", type="string", format="uuid", nullable=true),
 *     @OA\Property(property="deleted_by", type="string", format="uuid", nullable=true),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true)
 * )
 *
 * @OA\Schema(
 *     schema="CaracteristiqueAppelOffreDetailed",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/CaracteristiqueAppelOffre"),
 *         @OA\Schema(
 *             @OA\Property(
 *                 property="appel_offre",
 *                 ref="#/components/schemas/AppelOffreSummary",
 *                 description="Appel d'offres parent"
 *             ),
 *             @OA\Property(
 *                 property="parent",
 *                 ref="#/components/schemas/CaracteristiqueAppelOffre",
 *                 nullable=true,
 *                 description="Version précédente"
 *             ),
 *             @OA\Property(
 *                 property="versions",
 *                 type="array",
 *                 description="Historique des versions",
 *                 @OA\Items(ref="#/components/schemas/CaracteristiqueAppelOffre")
 *             ),
 *             @OA\Property(property="creator", ref="#/components/schemas/UserSummary"),
 *             @OA\Property(property="updater", ref="#/components/schemas/UserSummary", nullable=true),
 *             @OA\Property(
 *                 property="duree_estimee_formattee",
 *                 type="string",
 *                 description="Durée formatée pour affichage",
 *                 example="214 jours"
 *             )
 *         )
 *     }
 * )
 *
 * @OA\Schema(
 *     schema="CaracteristiqueAppelOffreRequest",
 *     type="object",
 *     required={"date_demarrage_prevue_caracteristique_appel_offre", "date_livraison_previsionnelle_caracteristique_appel_offre", "lieu_execution_caracteristique_appel_offre"},
 *     @OA\Property(property="date_demarrage_prevue_caracteristique_appel_offre", type="string", example="01/03/2024"),
 *     @OA\Property(property="date_livraison_previsionnelle_caracteristique_appel_offre", type="string", example="30/09/2024"),
 *     @OA\Property(property="lieu_execution_caracteristique_appel_offre", type="string", maxLength=255),
 *     @OA\Property(property="montant_garantie_caracteristique_appel_offre", type="number", nullable=true),
 *     @OA\Property(property="delai_garantie_jours_caracteristique_appel_offre", type="number", nullable=true),
 *     @OA\Property(property="conditions_paiement_caracteristique_appel_offre", type="string", nullable=true),
 *     @OA\Property(property="modalites_execution_caracteristique_appel_offre", type="string", nullable=true),
 *     @OA\Property(property="documents_requis_caracteristique_appel_offre", type="string", nullable=true),
 *     @OA\Property(property="autres_informations_caracteristique_appel_offre", type="string", nullable=true)
 * )
 *
 * ============================================
 * SCHÉMAS POUR LES LOTS
 * ============================================
 *
 * @OA\Schema(
 *     schema="Lot",
 *     type="object",
 *     description="Lot d'un appel d'offres",
 *     @OA\Property(
 *         property="id_lot",
 *         type="string",
 *         format="uuid",
 *         description="Identifiant unique UUID",
 *         example="770e8400-e29b-41d4-a716-446655440000"
 *     ),
 *     @OA\Property(
 *         property="appel_offre_id",
 *         type="string",
 *         format="uuid",
 *         description="UUID de l'appel d'offres parent"
 *     ),
 *     @OA\Property(
 *         property="numero",
 *         type="string",
 *         maxLength=35,
 *         description="Numéro du lot",
 *         example="LOT-001"
 *     ),
 *     @OA\Property(
 *         property="libelle",
 *         type="string",
 *         maxLength=160,
 *         description="Libellé du lot",
 *         example="Gros œuvre - Bâtiment principal"
 *     ),
 *     @OA\Property(
 *         property="description_critere",
 *         type="string",
 *         nullable=true,
 *         description="Description détaillée"
 *     ),
 *     @OA\Property(
 *         property="specifications_techniques",
 *         type="string",
 *         nullable=true,
 *         description="Spécifications techniques"
 *     ),
 *     @OA\Property(
 *         property="budget_lot",
 *         type="number",
 *         format="decimal",
 *         nullable=true,
 *         description="Budget alloué (FCFA)",
 *         example=45000000.00
 *     ),
 *     @OA\Property(
 *         property="attribution_lot",
 *         type="integer",
 *         enum={0, 1},
 *         description="Statut d'attribution (0=non attribué, 1=attribué)",
 *         example=0
 *     ),
 *     @OA\Property(
 *         property="statut_lot",
 *         type="integer",
 *         enum={0, 1},
 *         nullable=true,
 *         description="Statut du lot (0=inactif, 1=actif)",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="date_attribution",
 *         type="string",
 *         format="date",
 *         nullable=true,
 *         description="Date d'attribution"
 *     ),
 *     @OA\Property(
 *         property="date_debut_prevue",
 *         type="string",
 *         format="date-time",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="date_fin_prevue",
 *         type="string",
 *         format="date-time",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="version_lot",
 *         type="integer",
 *         description="Numéro de version",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="parent_id",
 *         type="string",
 *         format="uuid",
 *         nullable=true,
 *         description="UUID de la version précédente"
 *     ),
 *     @OA\Property(
 *         property="motif_retrait",
 *         type="string",
 *         nullable=true,
 *         description="Motif de retrait du lot"
 *     ),
 *     @OA\Property(
 *         property="date_retrait",
 *         type="string",
 *         format="date",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="statut_retrait",
 *         type="integer",
 *         nullable=true
 *     ),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true)
 * )
 *
 * @OA\Schema(
 *     schema="LotDetailed",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/Lot"),
 *         @OA\Schema(
 *             @OA\Property(
 *                 property="appel_offre",
 *                 ref="#/components/schemas/AppelOffreSummary"
 *             ),
 *             @OA\Property(
 *                 property="parent",
 *                 ref="#/components/schemas/Lot",
 *                 nullable=true
 *             ),
 *             @OA\Property(
 *                 property="versions",
 *                 type="array",
 *                 @OA\Items(ref="#/components/schemas/Lot")
 *             ),
 *             @OA\Property(
 *                 property="criteres_evaluation",
 *                 type="array",
 *                 @OA\Items(ref="#/components/schemas/CritereEvaluation")
 *             ),
 *             @OA\Property(
 *                 property="attribution_active",
 *                 ref="#/components/schemas/Attribution",
 *                 nullable=true
 *             ),
 *             @OA\Property(
 *                 property="historique_attributions",
 *                 type="array",
 *                 @OA\Items(ref="#/components/schemas/Attribution")
 *             ),
 *             @OA\Property(property="creator", ref="#/components/schemas/UserSummary"),
 *             @OA\Property(property="updater", ref="#/components/schemas/UserSummary", nullable=true)
 *         )
 *     }
 * )
 *
 * @OA\Schema(
 *     schema="LotRequest",
 *     type="object",
 *     required={"appel_offre_id", "numero", "libelle", "statut_lot"},
 *     @OA\Property(property="appel_offre_id", type="string", format="uuid"),
 *     @OA\Property(property="numero", type="string", maxLength=35),
 *     @OA\Property(property="libelle", type="string", maxLength=160),
 *     @OA\Property(property="description_critere", type="string", nullable=true),
 *     @OA\Property(property="specifications_techniques", type="string", nullable=true),
 *     @OA\Property(property="budget_lot", type="number", nullable=true),
 *     @OA\Property(property="date_debut_prevue", type="string", format="date", nullable=true),
 *     @OA\Property(property="date_fin_prevue", type="string", format="date", nullable=true),
 *     @OA\Property(property="statut_lot", type="integer", enum={0, 1})
 * )
 *
 * ============================================
 * SCHÉMAS POUR LES ATTRIBUTIONS
 * ============================================
 *
 * @OA\Schema(
 *     schema="Attribution",
 *     type="object",
 *     description="Attribution d'un lot à un prestataire",
 *     @OA\Property(property="id", type="string", format="uuid"),
 *     @OA\Property(property="lot_id", type="string", format="uuid"),
 *     @OA\Property(property="prestataire_id", type="string", format="uuid"),
 *     @OA\Property(property="proforma_id", type="string", format="uuid"),
 *     @OA\Property(property="date_attribution", type="string", format="date"),
 *     @OA\Property(property="montant_attribution", type="number"),
 *     @OA\Property(property="statut_attribution", type="integer"),
 *     @OA\Property(property="is_active", type="boolean"),
 *     @OA\Property(property="motif_retrait", type="string", nullable=true),
 *     @OA\Property(property="date_retrait", type="string", format="date", nullable=true),
 *     @OA\Property(
 *         property="prestataire",
 *         ref="#/components/schemas/PrestataireSummary",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="proforma",
 *         ref="#/components/schemas/Proforma",
 *         nullable=true
 *     ),
 *     @OA\Property(property="created_at", type="string", format="date-time")
 * )
 *
 * ============================================
 * SCHÉMAS POUR LES PRESTATAIRES
 * ============================================
 *
 * @OA\Schema(
 *     schema="Prestataire",
 *     type="object",
 *     description="Prestataire/fournisseur",
 *     @OA\Property(
 *         property="id_prestataire",
 *         type="string",
 *         format="uuid",
 *         example="990e8400-e29b-41d4-a716-446655440000"
 *     ),
 *     @OA\Property(
 *         property="raison_sociale_prestataire",
 *         type="string",
 *         maxLength=255,
 *         example="Entreprise BTP Excellence SARL"
 *     ),
 *     @OA\Property(
 *         property="numero_identification_prestataire",
 *         type="string",
 *         maxLength=25,
 *         example="CI-2020-12345"
 *     ),
 *     @OA\Property(
 *         property="email_prestataire",
 *         type="string",
 *         format="email",
 *         example="contact@btp-excellence.ci"
 *     ),
 *     @OA\Property(
 *         property="numero_cc_prestataire",
 *         type="string",
 *         maxLength=50,
 *         description="Numéro carte contribuable",
 *         example="CC-2020-00123456"
 *     ),
 *     @OA\Property(
 *         property="numero_rccm_prestataire",
 *         type="string",
 *         maxLength=50,
 *         description="Numéro RCCM",
 *         example="CI-ABJ-2020-B-12345"
 *     ),
 *     @OA\Property(
 *         property="telephone_principal_prestataire",
 *         type="string",
 *         maxLength=20,
 *         example="+225 27 22 00 00 00"
 *     ),
 *     @OA\Property(
 *         property="telephone_secondaire_prestataire",
 *         type="string",
 *         maxLength=20,
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="adresse_prestataire",
 *         type="string",
 *         description="Adresse physique"
 *     ),
 *     @OA\Property(
 *         property="ville_prestataire",
 *         type="string",
 *         maxLength=50,
 *         example="Abidjan"
 *     ),
 *     @OA\Property(
 *         property="pays_prestataire",
 *         type="string",
 *         format="uuid",
 *         description="UUID du pays"
 *     ),
 *     @OA\Property(
 *         property="representant_legal_prestataire",
 *         type="object",
 *         description="Informations du représentant légal (JSON)"
 *     ),
 *     @OA\Property(
 *         property="statut_prestataire",
 *         type="boolean",
 *         description="Statut actif/inactif"
 *     ),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="PrestataireSummary",
 *     type="object",
 *     description="Résumé d'un prestataire",
 *     @OA\Property(property="id_prestataire", type="string", format="uuid"),
 *     @OA\Property(property="raison_sociale_prestataire", type="string"),
 *     @OA\Property(property="numero_rccm_prestataire", type="string"),
 *     @OA\Property(property="email_prestataire", type="string", format="email"),
 *     @OA\Property(property="telephone_principal_prestataire", type="string")
 * )
 *
 * ============================================
 * SCHÉMAS POUR LES PROFORMAS
 * ============================================
 *
 * @OA\Schema(
 *     schema="Proforma",
 *     type="object",
 *     description="Proforma/Devis",
 *     @OA\Property(
 *         property="id_proforma",
 *         type="string",
 *         format="uuid",
 *         example="aa0e8400-e29b-41d4-a716-446655440000"
 *     ),
 *     @OA\Property(
 *         property="version_proforma",
 *         type="integer",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="numero_proforma",
 *         type="string",
 *         maxLength=20,
 *         description="Numéro de référence",
 *         example="PRO-2024-0001"
 *     ),
 *     @OA\Property(
 *         property="date_proforma",
 *         type="string",
 *         format="date",
 *         description="Date de création"
 *     ),
 *     @OA\Property(
 *         property="date_debut_validee_proforma",
 *         type="string",
 *         format="date",
 *         description="Date de début validée"
 *     ),
 *     @OA\Property(
 *         property="date_redemarrage_proforma",
 *         type="string",
 *         format="date",
 *         description="Date de redémarrage"
 *     ),
 *     @OA\Property(
 *         property="date_fin_validee_proforma",
 *         type="string",
 *         format="date",
 *         description="Date de fin validée"
 *     ),
 *     @OA\Property(
 *         property="montant_retenu_proforma",
 *         type="number",
 *         format="decimal",
 *         description="Montant retenu (FCFA)",
 *         example=45000000.00
 *     ),
 *     @OA\Property(
 *         property="taxe_montant",
 *         type="number",
 *         format="decimal",
 *         description="Montant TVA (18%)",
 *         example=8100000.00
 *     ),
 *     @OA\Property(
 *         property="remise_montant_proforma",
 *         type="number",
 *         format="decimal",
 *         description="Remise accordée",
 *         example=0.00
 *     ),
 *     @OA\Property(
 *         property="modalite_proforma",
 *         type="string",
 *         nullable=true,
 *         description="Modalités de paiement"
 *     ),
 *     @OA\Property(
 *         property="motif_modification_proforma",
 *         type="string",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="actif_proforma",
 *         type="boolean",
 *         example=true
 *     ),
 *     @OA\Property(
 *         property="parent_id",
 *         type="string",
 *         format="uuid",
 *         nullable=true
 *     ),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * ============================================
 * SCHÉMAS POUR LES CRITÈRES D'ÉVALUATION
 * ============================================
 *
 * @OA\Schema(
 *     schema="CritereEvaluation",
 *     type="object",
 *     description="Critère d'évaluation d'un lot",
 *     @OA\Property(property="id", type="string", format="uuid"),
 *     @OA\Property(property="lot_id", type="string", format="uuid"),
 *     @OA\Property(
 *         property="libelle_critere_evaluation",
 *         type="string",
 *         description="Libellé du critère",
 *         example="Qualité technique"
 *     ),
 *     @OA\Property(
 *         property="note_reference_critere_evaluation",
 *         type="number",
 *         description="Note de référence/pondération",
 *         example=35
 *     ),
 *     @OA\Property(
 *         property="description_critere_evaluation",
 *         type="string",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="evaluations",
 *         type="array",
 *         description="Évaluations effectuées",
 *         @OA\Items(ref="#/components/schemas/Evaluation")
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="Evaluation",
 *     type="object",
 *     description="Évaluation d'un critère",
 *     @OA\Property(property="id", type="string", format="uuid"),
 *     @OA\Property(property="critere_evaluation_id", type="string", format="uuid"),
 *     @OA\Property(property="prestataire_id", type="string", format="uuid"),
 *     @OA\Property(
 *         property="resultat_evaluation",
 *         type="number",
 *         description="Note obtenue",
 *         example=30
 *     ),
 *     @OA\Property(
 *         property="commentaire_evaluation",
 *         type="string",
 *         nullable=true
 *     ),
 *     @OA\Property(property="created_at", type="string", format="date-time")
 * )
 *
 * ============================================
 * SCHÉMAS COMMUNS (non définis dans Controller.php)
 * ============================================
 *
 * @OA\Schema(
 *     schema="AppelOffreSummary",
 *     type="object",
 *     description="Résumé d'un appel d'offres",
 *     @OA\Property(property="id_appel_offre", type="string", format="uuid"),
 *     @OA\Property(property="numero_appel_offre", type="string", example="AON-2024-0042"),
 *     @OA\Property(property="libelle_critere_appel_offre", type="string"),
 *     @OA\Property(property="montant_global_appel_offre", type="number"),
 *     @OA\Property(property="statut_evaluation_critere_appel_offre", type="integer"),
 *     @OA\Property(property="etat_appel_offre", type="integer"),
 *     @OA\Property(property="date_publication_critere_appel_offre", type="string", format="date-time", nullable=true)
 * )
 *
 * @OA\Schema(
 *     schema="UserSummary",
 *     type="object",
 *     description="Résumé d'un utilisateur",
 *     @OA\Property(property="id", type="string", format="uuid"),
 *     @OA\Property(property="nom_complet", type="string", example="Jean Dupont"),
 *     @OA\Property(property="email", type="string", format="email")
 * )
 *
 * @OA\Schema(
 *     schema="ForbiddenResponse",
 *     type="object",
 *     description="Réponse pour accès interdit",
 *     @OA\Property(property="success", type="boolean", example=false),
 *     @OA\Property(property="message", type="string", example="Cette action n'est pas autorisée.")
 * )
 *
 * @OA\Schema(
 *     schema="PaginationMeta",
 *     type="object",
 *     description="Métadonnées de pagination",
 *     @OA\Property(property="current_page", type="integer", example=1),
 *     @OA\Property(property="from", type="integer", example=1),
 *     @OA\Property(property="last_page", type="integer", example=10),
 *     @OA\Property(property="per_page", type="integer", example=15),
 *     @OA\Property(property="to", type="integer", example=15),
 *     @OA\Property(property="total", type="integer", example=150)
 * )
 */
class CaracteristiquesLotsSchemas
{
    // Ce fichier contient uniquement les annotations Swagger pour Caractéristiques et Lots
    // Les schémas ErrorResponse, ServerErrorResponse, UnauthenticatedResponse sont définis dans Controller.php
}
