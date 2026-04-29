<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Lot;
use App\Models\Proforma;
use App\Models\Evaluation;
use App\Models\Prestataire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use App\Models\AttributionLotPrestataire;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Attributions de Lots",
 *     description="Gestion des attributions de lots aux prestataires"
 * )
 */
class AttributionLotPrestataireController extends Controller
{


    /**
     * @OA\Get(
     *     path="/attributions",
     *     operationId="getAttributions",
     *     tags={"Attributions de Lots"},
     *     summary="Liste des attributions de lots aux prestataires",
     *     description="Récupère la liste paginée des attributions avec filtres, tri et statistiques",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="statut",
     *         in="query",
     *         description="Filtrer par statut (0:En attente, 1:Attribué, 2:Suspendu, 3:Retiré, 4:Terminé, 5:Annulé)",
     *         required=false,
     *         @OA\Schema(type="integer", enum={0, 1, 2, 3, 4, 5})
     *     ),
     *     @OA\Parameter(
     *         name="is_active",
     *         in="query",
     *         description="Filtrer par statut actif/inactif",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="prestataire_id",
     *         in="query",
     *         description="Filtrer par prestataire (UUID)",
     *         required=false,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Parameter(
     *         name="lot_id",
     *         in="query",
     *         description="Filtrer par lot (UUID)",
     *         required=false,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Recherche sur numéro attribution, nom du prestataire prestataire, numéro/libellé lot",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         description="Champ de tri",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             enum={"created_at", "date_attribution", "numero_attribution", "pourcentage_avancement", "statut_attribution"},
     *             default="created_at"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sort_order",
     *         in="query",
     *         description="Ordre de tri",
     *         required=false,
     *         @OA\Schema(type="string", enum={"asc", "desc"}, default="desc")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Nombre d'éléments par page",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Numéro de page",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Liste des attributions récupérée avec succès",
     *         @OA\JsonContent(ref="#/components/schemas/AttributionIndexResponse")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié",
     *         @OA\JsonContent(ref="#/components/schemas/UnauthenticatedResponse")
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur serveur",
     *         @OA\JsonContent(ref="#/components/schemas/ServerErrorResponse")
     *     )
     * )
     */
    public function index(Request $request)
    {
        try {
            $query = AttributionLotPrestataire::with(['prestataire', 'lot.appelOffre', 'proforma', 'createdBy', 'parentAttribution', 'childAttributions']);

            // Filtres
            if ($request->filled('statut')) {
                $query->where('statut_attribution', $request->statut);
            }

            if ($request->filled('is_active')) {
                $query->where('is_active', $request->boolean('is_active'));
            }

            if ($request->filled('prestataire_id')) {
                $query->where('prestataire_id', $request->prestataire_id);
            }

            if ($request->filled('lot_id')) {
                $query->where('lot_id', $request->lot_id);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('numero_attribution', 'like', "%{$search}%")
                        ->orWhereHas('prestataire', function ($subQ) use ($search) {
                            $subQ->where('raison_sociale_prestataire', 'like', "%{$search}%");
                        })
                        ->orWhereHas('lot', function ($subQ) use ($search) {
                            $subQ->where('numero', 'like', "%{$search}%")
                                ->orWhere('libelle', 'like', "%{$search}%");
                        });
                });
            }

            // Tri
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $allowedSorts = ['created_at', 'date_attribution', 'numero_attribution', 'pourcentage_avancement', 'statut_attribution'];
            if (in_array($sortBy, $allowedSorts)) {
                $query->orderBy($sortBy, $sortOrder);
            }

            $attributions = $query->paginate($request->get('per_page', 15));

            // Statistiques
            $statistiques = [
                'total' => AttributionLotPrestataire::count(),
                'actives' => AttributionLotPrestataire::where('is_active', true)->count(),
                'en_cours' => AttributionLotPrestataire::where('is_active', true)->where('statut_attribution', AttributionLotPrestataire::STATUT_ATTRIBUE)->count(),
                'suspendues' => AttributionLotPrestataire::where('is_active', true)->where('statut_attribution', AttributionLotPrestataire::STATUT_SUSPENDU)->count(),
                'terminees' => AttributionLotPrestataire::where('statut_attribution', AttributionLotPrestataire::STATUT_TERMINE)->count(),
                'en_retard' => AttributionLotPrestataire::where('is_active', true)->where('jours_retard', '>', 0)->count(),
            ];

            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $attributions,
                    'statistiques' => $statistiques,
                ]);
            }

            $prestataires = Prestataire::where('statut_prestataire', true)->orderBy('raison_sociale_prestataire')->get();
            $lots = Lot::orderBy('numero')->get();

            return view('attributions.index', compact('attributions', 'statistiques', 'prestataires', 'lots'));
        } catch (\Exception $e) {
            Log::error('Erreur index attributions: ' . $e->getMessage());

            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Erreur lors du chargement des attributions.'], 500);
            }

            return back()->with('error', 'Erreur lors du chargement des attributions.');
        }
    }

    /**
     * @OA\Get(
     *     path="/lots/{lot}/attribuer",
     *     operationId="createAttributionForm",
     *     tags={"Attributions de Lots"},
     *     summary="Formulaire de création d'attribution",
     *     description="Récupère les données nécessaires pour créer une nouvelle attribution (prestataires actifs, lots disponibles, proformas)",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="lot",
     *         in="path",
     *         description="UUID du lot à attribuer",
     *         required=false,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Données du formulaire récupérées avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="prestataires",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/PrestataireSummary")
     *                 ),
     *                 @OA\Property(
     *                     property="lots",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/LotSummary")
     *                 ),
     *                 @OA\Property(
     *                     property="proformas",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/ProformaSummary")
     *                 ),
     *                 @OA\Property(
     *                     property="lotPreselectionne",
     *                     ref="#/components/schemas/LotSummary",
     *                     nullable=true
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié",
     *         @OA\JsonContent(ref="#/components/schemas/UnauthenticatedResponse")
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur serveur",
     *         @OA\JsonContent(ref="#/components/schemas/ServerErrorResponse")
     *     )
     * )
     */
    public function create(Request $request)
    {
        $prestataires = Prestataire::actif()->where('statut_prestataire', true)
            ->orderBy('raison_sociale_prestataire')
            ->get();

        $lots = Lot::versionActuelle()->where('attribution_lot', 0)
            ->orWhereDoesntHave('attributionActive')
            ->with('appelOffre')
            ->orderBy('numero')
            ->get();

        $proformas = Proforma::actif()->where('actif_proforma', true)
            ->orderBy('numero_proforma', 'desc')
            ->get();

        $lotPreselectionne = null;
        if ($request->filled('lot_id')) {
            $lotPreselectionne = Lot::find($request->lot_id);
        }

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => compact('prestataires', 'lots', 'proformas', 'lotPreselectionne'),
            ]);
        }

        return view('attributions.create', compact('prestataires', 'lots', 'proformas', 'lotPreselectionne'));
    }


    /**
     * Nettoie un nombre formaté (avec espaces et virgules) pour le convertir en nombre décimal
     *
     * @param mixed $value Valeur à nettoyer (ex: "1 000 000,50")
     * @return float|null Valeur numérique (ex: 1000000.50) ou null
     */
    private function cleanFormattedNumber($value)
    {
        if (is_null($value) || $value === '') {
            return null;
        }

        // Retirer les espaces (séparateurs de milliers)
        $value = str_replace(' ', '', $value);

        // Remplacer la virgule par un point (décimales)
        $value = str_replace(',', '.', $value);

        return $value;
    }

    /**
     * Enregistrer une nouvelle attribution.
     * Gère deux modes : sélection proforma existante OU création nouvelle proforma
     *
     * @OA\Post(
     *     path="/attributions",
     *     operationId="storeAttribution",
     *     tags={"Attributions de Lots"},
     *     summary="Créer une nouvelle attribution",
     *     description="Crée une nouvelle attribution de lot à un prestataire. Peut créer une nouvelle proforma ou utiliser une existante.",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/AttributionLotPrestataireRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Attribution créée avec succès",
     *         @OA\JsonContent(ref="#/components/schemas/AttributionStoreResponse")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erreur de validation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Erreur de validation"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 description="Détails des erreurs de validation"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié",
     *         @OA\JsonContent(ref="#/components/schemas/UnauthenticatedResponse")
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur serveur",
     *         @OA\JsonContent(ref="#/components/schemas/ServerErrorResponse")
     *     )
     * )
     */
    public function store(Request $request)
    {
        // ==========================================
        // NETTOYAGE DES MONTANTS FORMATÉS
        // ==========================================

        // Liste des champs numériques à nettoyer (reçus avec formatage : espaces + virgules)
        $fieldsToClean = [
            'new_montant_retenu',
            'new_taux_tva',
            'new_taux_remise',
            'new_taxe_montant',
            'new_remise_montant',
            'new_total_ttc',
            'montant_engage',
        ];

        foreach ($fieldsToClean as $field) {
            if ($request->has($field)) {
                $request->merge([
                    $field => $this->cleanFormattedNumber($request->input($field))
                ]);
            }
        }

        // ==========================================
        // RÉCUPÉRATION DES LIMITES DU TYPE D'APPEL D'OFFRE
        // ==========================================

        // Récupérer le lot avec ses relations pour validation dynamique
        $lot = null;
        if ($request->has('lot_id')) {
            try {
                $lot = Lot::with('appelOffre.typeAppelOffre')->findOrFail($request->lot_id);
            } catch (\Exception $e) {
                // Le lot sera validé plus tard dans les règles exists
            }
        }

        $valeurMin = $lot && $lot->appelOffre && $lot->appelOffre->typeAppelOffre
            ? $lot->appelOffre->typeAppelOffre->valeur_minimuim_type_appel_offre
            : 0;

        $valeurMax = $lot && $lot->appelOffre && $lot->appelOffre->typeAppelOffre
            ? $lot->appelOffre->typeAppelOffre->valeur_maximuim_type_appel_offre
            : PHP_INT_MAX;

        // ==========================================
        // RÈGLES DE VALIDATION
        // ==========================================

        // Règles de base
        $rules = [
            'prestataire_id' => 'required|uuid|exists:prestataires,id_prestataire',
            'lot_id' => 'required|uuid|exists:lots,id_lot',
            'proforma_mode' => 'required|in:select,create',
            'date_attribution' => 'required|date|before_or_equal:today',
            'montant_engage' => 'nullable|numeric|min:0',
            'observations' => 'nullable|string|max:2000',
            'conditions_particulieres' => 'nullable|string|max:5000',
        ];

        // Règles conditionnelles selon le mode proforma
        if ($request->proforma_mode === 'select') {
            $rules['proforma_id'] = 'required|uuid|exists:proformas,id_proforma';
        } else {
            // Mode création : champs nouvelle proforma avec validation améliorée
            $rules['new_numero_proforma'] = 'nullable|string|max:20|unique:proformas,numero_proforma';
            $rules['new_date_proforma'] = 'required|date|before_or_equal:today';
            $rules['new_date_debut_validee'] = 'required|date';
            // $rules['new_date_redemarrage'] = 'required|date|after_or_equal:new_date_debut_validee';
            $rules['new_date_fin_validee'] = 'required|date|after:new_date_debut_validee';

            // Montant retenu avec validation dynamique selon le type d'appel d'offre
            $rules['new_montant_retenu'] = [
                'required',
                'numeric',
                // 'min:' . $valeurMin,
                // 'max:' . $valeurMax,
            ];

            // Taux (en pourcentage)
            $rules['new_taux_tva'] = 'required|numeric|min:0|max:100';
            $rules['new_taux_remise'] = 'nullable|numeric|min:0|max:100';

            // Montants calculés
            $rules['new_taxe_montant'] = 'nullable|numeric|min:0';
            $rules['new_remise_montant'] = 'nullable|numeric|min:0';
            $rules['new_total_ttc'] = 'required|numeric|min:0';


            // Modalités de paiement
            $rules['new_modalite_paiement'] = 'nullable|string|max:1000';
        }

        // ==========================================
        // MESSAGES PERSONNALISÉS
        // ==========================================

        $messages = array_merge($this->validationMessages(), [
            // Mode proforma
            'proforma_mode.required' => 'Veuillez choisir un mode de proforma.',
            'proforma_mode.in' => 'Mode de proforma invalide.',

            // Proforma existante
            'proforma_id.required' => 'Veuillez sélectionner une proforma.',
            'proforma_id.exists' => 'La proforma sélectionnée est invalide.',

            // Numéro proforma
            'new_numero_proforma.unique' => 'Ce numéro de proforma existe déjà.',
            'new_numero_proforma.max' => 'Le numéro ne peut pas dépasser 20 caractères.',

            // Dates
            'new_date_proforma.required' => 'La date de proforma est obligatoire.',
            'new_date_proforma.before_or_equal' => 'La date de proforma ne peut pas être dans le futur.',

            'new_date_debut_validee.required' => 'La date de début validée est obligatoire.',
            'new_date_debut_validee.date' => 'La date de début validée doit être une date valide.',


            'new_date_fin_validee.required' => 'La date de fin validée est obligatoire.',
            'new_date_fin_validee.date' => 'La date de fin validée doit être une date valide.',
            'new_date_fin_validee.after' => 'La date de fin validée doit être postérieure à la date de redémarrage.',

            // Montant retenu avec limites dynamiques
            'new_montant_retenu.required' => 'Le montant retenu est obligatoire.',
            'new_montant_retenu.numeric' => 'Le montant retenu doit être un nombre.',


            // Taux TVA
            'new_taux_tva.required' => 'Le taux de TVA est obligatoire.',
            'new_taux_tva.numeric' => 'Le taux de TVA doit être un nombre.',
            'new_taux_tva.min' => 'Le taux de TVA doit être positif.',
            'new_taux_tva.max' => 'Le taux de TVA ne peut pas dépasser 100%.',

            // Taux remise
            'new_taux_remise.numeric' => 'Le taux de remise doit être un nombre.',
            'new_taux_remise.min' => 'Le taux de remise doit être positif.',
            'new_taux_remise.max' => 'Le taux de remise ne peut pas dépasser 100%.',

            // Montants calculés
            'new_taxe_montant.numeric' => 'Le montant de la TVA doit être un nombre.',
            'new_taxe_montant.min' => 'Le montant de la TVA doit être positif.',

            'new_remise_montant.numeric' => 'Le montant de la remise doit être un nombre.',
            'new_remise_montant.min' => 'Le montant de la remise doit être positif.',

            'new_total_ttc.required' => 'Le total TTC est obligatoire.',
            'new_total_ttc.numeric' => 'Le total TTC doit être un nombre.',
            'new_total_ttc.min' => 'Le total TTC doit être positif.',


            // Modalités
            // 'new_modalite_paiement.required' => 'Les modalités de paiement sont obligatoires.',
            'new_modalite_paiement.max' => 'Les modalités de paiement ne peuvent pas dépasser 1000 caractères.',

            // Attribution
            'date_attribution.before_or_equal' => 'La date d\'attribution ne peut pas être dans le futur.',
        ]);

        $data = $request->all();
        $data['new_date_debut_validee'] = $request->input('date_debut_prevue');
        $data['new_date_fin_validee'] = $request->input('date_fin_prevue');

        $validator = Validator::make($data, $rules, $messages);


        $validator->after(function ($validator) use ($request) {
            // Vérifier la cohérence des calculs si nouvelle proforma
            if ($request->proforma_mode === 'create') {
                $montantRetenu = (float) $request->new_montant_retenu;
                $tauxTVA = (float) ($request->new_taux_tva ?? 0);
                $tauxRemise = (float) ($request->new_taux_remise ?? 0);

                // Recalculer pour vérifier la cohérence
                $montantTVACalcule = $montantRetenu * ($tauxTVA / 100);
                $montantRemiseCalcule = $montantRetenu * ($tauxRemise / 100);
                $totalTTCCalcule = $montantRetenu + $montantTVACalcule - $montantRemiseCalcule;

                $montantTVARecu = (float) ($request->new_taxe_montant ?? 0);
                $totalTTCRecu = (float) ($request->new_total_ttc ?? 0);

                // Tolérance de 1 FCFA pour les arrondis
                if (abs($montantTVACalcule - $montantTVARecu) > 1) {
                    $validator->errors()->add(
                        'new_taxe_montant',
                        'Le montant de la TVA (' . number_format(floor($montantTVARecu), 0, ',', ' ') . ' FCFA) ne correspond pas au calcul attendu (' . number_format(floor($montantTVACalcule), 0, ',', ' ') . ' FCFA).'
                    );
                }

                if (abs($totalTTCCalcule - $totalTTCRecu) > 1) {
                    $validator->errors()->add(
                        'new_total_ttc',
                        'Le total TTC (' . number_format(floor($totalTTCRecu), 0, ',', ' ') . ' FCFA) ne correspond pas au calcul attendu (' . number_format(floor($totalTTCCalcule), 0, ',', ' ') . ' FCFA).'
                    );
                }
            }
        });

        if ($validator->fails()) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            // Vérifications lot
            $lot = Lot::findOrFail($request->lot_id);
            if (AttributionLotPrestataire::lotEstAttribue($request->lot_id)) {
                throw new \Exception('Ce lot est déjà attribué à un prestataire actif.');
            }

            // Vérification prestataire
            $prestataire = Prestataire::findOrFail($request->prestataire_id);
            if (!$prestataire->statut_prestataire) {
                throw new \Exception('Le prestataire sélectionné n\'est pas actif.');
            }

            // Gestion de la proforma selon le mode
            if ($request->proforma_mode === 'select') {
                // Mode sélection : vérifier la proforma existante
                $proforma = Proforma::findOrFail($request->proforma_id);
                if (!$proforma->actif_proforma) {
                    throw new \Exception('La proforma sélectionnée n\'est pas active.');
                }
                $proformaId = $proforma->id_proforma;
            } else {
                // Mode création : créer la nouvelle proforma

                $proformaData = [
                    'date_proforma' => $request->new_date_proforma,
                    'date_debut_validee_proforma' => $request->date_debut_prevue,
                    'date_fin_validee_proforma' => $request->date_fin_prevue,
                    'montant_retenu_proforma' => $request->new_montant_retenu,
                    'taxe_montant' => $request->new_taxe_montant ?? 0,
                    'remise_montant_proforma' => $request->new_remise_montant ?? 0,
                    'modalite_proforma' => $request->new_modalite_paiement,
                    'actif_proforma' => true,
                    'version_proforma' => 1,
                    'created_by' => Auth::id(),
                ];

                // Stocker aussi les taux pour référence (si votre modèle le supporte)
                // Ajustez selon votre structure de table
                if (Schema::hasColumn('proformas', 'taux_tva')) {
                    $proformaData['taux_tva'] = $request->new_taux_tva ?? 0;
                }
                if (Schema::hasColumn('proformas', 'taux_remise')) {
                    $proformaData['taux_remise'] = $request->new_taux_remise ?? 0;
                }
                if (Schema::hasColumn('proformas', 'total_ttc')) {
                    $proformaData['total_ttc'] = $request->new_total_ttc ?? 0;
                }

                // Numéro personnalisé si fourni, sinon auto-généré
                if (!empty($request->new_numero_proforma)) {
                    $proformaData['numero_proforma'] = $request->new_numero_proforma;
                }

                $proforma = Proforma::create($proformaData);

                Log::info('Nouvelle proforma créée avec formatage', [
                    'id' => $proforma->id_proforma,
                    'numero' => $proforma->numero_proforma,
                    'montant_ht' => $proforma->montant_retenu_proforma,
                    'taux_tva' => $request->new_taux_tva,
                    'taxe' => $proforma->taxe_montant,
                    'taux_remise' => $request->new_taux_remise,
                    'remise' => $proforma->remise_montant_proforma,
                    'ttc' => $request->new_total_ttc,
                    'user' => Auth::id()
                ]);

                $proformaId = $proforma->id_proforma;
            }

            // Préparer les données pour l'attribution
            $attributionData = [
                'prestataire_id' => $request->prestataire_id,
                'lot_id' => $request->lot_id,
                'proforma_id' => $proformaId,
                'date_attribution' => $request->date_attribution,
                'date_debut_prevue' => $proforma->date_debut_validee ?? null,
                'date_fin_prevue' => $proforma->date_fin_validee ?? null,
                'montant_engage' => $request->montant_engage ?? 0,
                'observations' => $request->observations,
                'conditions_particulieres' => $request->conditions_particulieres,
            ];

            // Créer l'attribution
            $attribution = AttributionLotPrestataire::attribuer($attributionData);

            DB::commit();

            Log::info('Attribution créée', [
                'id' => $attribution->id_attribution,
                'lot' => $lot->numero,
                'proforma_mode' => $request->proforma_mode,
                'montant_formatage' => 'supporté',
                'user' => Auth::id()
            ]);

            $message = "Le lot {$lot->numero} a été attribué avec succès à {$prestataire->raison_sociale_prestataire}.";

            if ($request->proforma_mode === 'create') {
                $message .= " Une nouvelle proforma ({$proforma->numero_proforma}) a été créée.";
            }

            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => $attribution->load(['prestataire', 'lot', 'proforma']),
                ]);
            }

            return redirect()
                ->route('attributions.show', $attribution->id_attribution)
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erreur création attribution: ' . $e->getMessage(), [
                'request' => $request->except(['_token']),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }

            return back()->withInput()->with('error', $e->getMessage());
        }
    }





    /**
     * Afficher une attribution.
     *
     * @OA\Get(
     *     path="/attributions/{attribution}",
     *     operationId="showAttribution",
     *     tags={"Attributions de Lots"},
     *     summary="Détails d'une attribution",
     *     description="Récupère les détails complets d'une attribution spécifique",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="attribution",
     *         in="path",
     *         description="UUID de l'attribution",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Détails de l'attribution récupérés avec succès",
     *         @OA\JsonContent(ref="#/components/schemas/AttributionShowResponse")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Attribution non trouvée",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Attribution non trouvée")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié",
     *         @OA\JsonContent(ref="#/components/schemas/UnauthenticatedResponse")
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur serveur",
     *         @OA\JsonContent(ref="#/components/schemas/ServerErrorResponse")
     *     )
     * )
     */
    public function show(Request $request, string $id)
    {
        try {
            $attribution = AttributionLotPrestataire::with([
                'prestataire',
                'lot.appelOffre',
                'proforma',
                'createdBy',
                'updatedBy',
                'parentAttribution.prestataire',
                'childAttributions.prestataire'
            ])->findOrFail($id);

            $statistiquesCriteres = Evaluation::statistiquesCriterePourAttribution($id);

            $historiqueLot = $attribution->getHistoriqueComplet();

            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => compact('attribution', 'historiqueLot', 'statistiquesCriteres'),
                ]);
            }



            return view('attributions.show', compact('attribution', 'historiqueLot', 'statistiquesCriteres'));
        } catch (\Exception $e) {
            Log::error('Erreur affichage attribution: ' . $e->getMessage());

            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Attribution introuvable.'], 404);
            }

            return redirect()->route('attributions.index')->with('error', 'Attribution introuvable.');
        }
    }

    /**
     * Formulaire de modification.
     *
     * @OA\Get(
     *     path="/attributions/{attribution}/edit",
     *     operationId="editAttribution",
     *     tags={"Attributions de Lots"},
     *     summary="Formulaire de modification d'attribution",
     *     description="Récupère les données pour modifier une attribution",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="attribution",
     *         in="path",
     *         description="UUID de l'attribution",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Données du formulaire récupérées avec succès",
     *         @OA\JsonContent(ref="#/components/schemas/AttributionShowResponse")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Attribution non trouvée",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Attribution non trouvée")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié",
     *         @OA\JsonContent(ref="#/components/schemas/UnauthenticatedResponse")
     *     )
     * )
     */
    public function edit(Request $request, string $id)
    {
        $attribution = AttributionLotPrestataire::with(['prestataire', 'lot', 'proforma'])->findOrFail($id);

        if (!$attribution->is_active) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Impossible de modifier une attribution historique.'], 422);
            }
            return back()->with('error', 'Impossible de modifier une attribution historique.');
        }

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $attribution]);
        }

        return view('attributions.edit', compact('attribution'));
    }

    /**
     * Mettre à jour une attribution.
     *
     * @OA\Put(
     *     path="/attributions/{attribution}",
     *     operationId="updateAttribution",
     *     tags={"Attributions de Lots"},
     *     summary="Modifier une attribution",
     *     description="Met à jour les informations d'une attribution existante (dates, montants, observations, etc.)",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="attribution",
     *         in="path",
     *         description="UUID de l'attribution",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/AttributionLotPrestataireUpdateRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Attribution mise à jour avec succès",
     *         @OA\JsonContent(ref="#/components/schemas/AttributionActionResponse")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erreur de validation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Erreur de validation"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Attribution non trouvée",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Attribution non trouvée")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié",
     *         @OA\JsonContent(ref="#/components/schemas/UnauthenticatedResponse")
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur serveur",
     *         @OA\JsonContent(ref="#/components/schemas/ServerErrorResponse")
     *     )
     * )
     */
    public function update(Request $request, string $id)
    {
        $attribution = AttributionLotPrestataire::findOrFail($id);

        if (!$attribution->is_active) {
            $message = 'Impossible de modifier une attribution historique.';
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }
            return back()->with('error', $message);
        }

        $validator = Validator::make($request->all(), [
            'observations' => 'nullable|string|max:2000',
            'conditions_particulieres' => 'nullable|string|max:5000',
            'date_debut_prevue' => 'nullable|date',
            'date_fin_prevue' => 'nullable|date|after:date_debut_prevue',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        try {
            $attribution->update($validator->validated());

            Log::info('Attribution mise à jour', ['id' => $id, 'user' => Auth::id()]);

            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Attribution mise à jour avec succès.',
                    'data' => $attribution->fresh(),
                ]);
            }

            return redirect()->route('attributions.show', $id)->with('success', 'Attribution mise à jour avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur mise à jour attribution: ' . $e->getMessage());

            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Erreur lors de la mise à jour.'], 500);
            }

            return back()->with('error', 'Erreur lors de la mise à jour.');
        }
    }

    /**
     * Suspendre une attribution.
     *
     *
     * @OA\Post(
     *     path="/attributions/{attribution}/suspendre",
     *     operationId="suspendreAttribution",
     *     tags={"Attributions de Lots"},
     *     summary="Suspendre une attribution",
     *     description="Suspend temporairement une attribution (statut passe à 'Suspendu')",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="attribution",
     *         in="path",
     *         description="UUID de l'attribution",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/AttributionSuspendreRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Attribution suspendue avec succès",
     *         @OA\JsonContent(ref="#/components/schemas/AttributionActionResponse")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="L'attribution ne peut pas être suspendue",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Cette attribution ne peut pas être suspendue."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Attribution non trouvée",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Attribution non trouvée")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié",
     *         @OA\JsonContent(ref="#/components/schemas/UnauthenticatedResponse")
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur serveur",
     *         @OA\JsonContent(ref="#/components/schemas/ServerErrorResponse")
     *     )
     * )
     */
    public function suspendre(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'motif_suspension' => 'required|string|min:10|max:2000',
            'date_reprise_prevue' => 'nullable|date|after:today',
        ], [
            'motif_suspension.required' => 'Le motif de suspension est obligatoire.',
            'motif_suspension.min' => 'Le motif doit contenir au moins 10 caractères.',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $attribution = AttributionLotPrestataire::findOrFail($id);

        if (!$attribution->peutEtreSuspendue()) {
            $message = 'Cette attribution ne peut pas être suspendue.';
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }
            return back()->with('error', $message);
        }

        try {
            $dateReprise = $request->filled('date_reprise_prevue')
                ? Carbon::parse($request->date_reprise_prevue)
                : null;

            $attribution->suspendre($request->motif_suspension, $dateReprise);

            Log::info('Attribution suspendue', ['id' => $id, 'user' => Auth::id()]);

            $message = 'Attribution suspendue avec succès.';

            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json(['success' => true, 'message' => $message, 'data' => $attribution->fresh()]);
            }

            return redirect()->route('attributions.show', $id)->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Erreur suspension: ' . $e->getMessage());

            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Erreur lors de la suspension.'], 500);
            }

            return back()->with('error', 'Erreur lors de la suspension.');
        }
    }

    /**
     * Reprendre une attribution suspendue.
     *
     *
     * @OA\Post(
     *     path="/attributions/{attribution}/reprendre",
     *     operationId="reprendreAttribution",
     *     tags={"Attributions de Lots"},
     *     summary="Reprendre une attribution suspendue",
     *     description="Reprend une attribution précédemment suspendue (statut repasse à 'Attribué')",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="attribution",
     *         in="path",
     *         description="UUID de l'attribution",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(ref="#/components/schemas/AttributionReprendreRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Attribution reprise avec succès",
     *         @OA\JsonContent(ref="#/components/schemas/AttributionActionResponse")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="L'attribution ne peut pas être reprise",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Cette attribution ne peut pas être reprise.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Attribution non trouvée",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Attribution non trouvée")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié",
     *         @OA\JsonContent(ref="#/components/schemas/UnauthenticatedResponse")
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur serveur",
     *         @OA\JsonContent(ref="#/components/schemas/ServerErrorResponse")
     *     )
     * )
     */
    public function reprendre(Request $request, string $id)
    {
        $attribution = AttributionLotPrestataire::findOrFail($id);

        if (!$attribution->peutEtreReprise()) {
            $message = 'Cette attribution ne peut pas être reprise.';
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }
            return back()->with('error', $message);
        }

        try {
            $attribution->reprendre($request->input('observations'));

            Log::info('Attribution reprise', ['id' => $id, 'user' => Auth::id()]);

            $message = 'Attribution reprise avec succès.';

            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json(['success' => true, 'message' => $message, 'data' => $attribution->fresh()]);
            }

            return redirect()->route('attributions.show', $id)->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Erreur reprise: ' . $e->getMessage());

            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Erreur lors de la reprise.'], 500);
            }

            return back()->with('error', 'Erreur lors de la reprise.');
        }
    }

    /**
     * Retirer une attribution.
     *
     *
     * @OA\Post(
     *     path="/attributions/{attribution}/retirer",
     *     operationId="retirerAttribution",
     *     tags={"Attributions de Lots"},
     *     summary="Retirer une attribution",
     *     description="Retire définitivement une attribution (résiliation, retrait volontaire, etc.). Le lot devient disponible pour réattribution.",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="attribution",
     *         in="path",
     *         description="UUID de l'attribution",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/AttributionRetirerRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Attribution retirée avec succès",
     *         @OA\JsonContent(ref="#/components/schemas/AttributionActionResponse")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="L'attribution ne peut pas être retirée",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Cette attribution ne peut pas être retirée.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Attribution non trouvée",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Attribution non trouvée")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié",
     *         @OA\JsonContent(ref="#/components/schemas/UnauthenticatedResponse")
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur serveur",
     *         @OA\JsonContent(ref="#/components/schemas/ServerErrorResponse")
     *     )
     * )
     */
    public function retirer(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'motif_retrait' => 'required|string|min:10|max:2000',
            'type_retrait' => 'required|in:volontaire,force,resiliation,abandon',
        ], [
            'motif_retrait.required' => 'Le motif de retrait est obligatoire.',
            'motif_retrait.min' => 'Le motif doit contenir au moins 10 caractères.',
            'type_retrait.required' => 'Le type de retrait est obligatoire.',
            'type_retrait.in' => 'Le type de retrait est invalide.',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $attribution = AttributionLotPrestataire::findOrFail($id);

        if (!$attribution->peutEtreRetiree()) {
            $message = 'Cette attribution ne peut pas être retirée.';
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }
            return back()->with('error', $message);
        }

        try {
            $attribution->retirer($request->motif_retrait, $request->type_retrait);

            Log::info('Attribution retirée', ['id' => $id, 'user' => Auth::id()]);

            $message = 'Attribution retirée avec succès. Le lot est disponible pour réattribution.';

            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json(['success' => true, 'message' => $message, 'data' => $attribution->fresh()]);
            }

            return redirect()->route('attributions.show', $id)->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Erreur retrait: ' . $e->getMessage());

            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Erreur lors du retrait.'], 500);
            }

            return back()->with('error', 'Erreur lors du retrait.');
        }
    }

    /**
     * Formulaire de réattribution.
     *
     *
     * @OA\Get(
     *     path="/attributions/{attribution}/reattribuer",
     *     operationId="reattribuerForm",
     *     tags={"Attributions de Lots"},
     *     summary="Formulaire de réattribution",
     *     description="Récupère les données pour réattribuer un lot à un nouveau prestataire",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="attribution",
     *         in="path",
     *         description="UUID de l'attribution actuelle",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Données du formulaire récupérées avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="attribution", ref="#/components/schemas/AttributionLotPrestataireDetailed"),
     *                 @OA\Property(
     *                     property="prestataires",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/PrestataireSummary")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Attribution non trouvée",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Attribution non trouvée")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié",
     *         @OA\JsonContent(ref="#/components/schemas/UnauthenticatedResponse")
     *     )
     * )
     */
    public function reattribuerForm(Request $request, string $id)
    {
        $attribution = AttributionLotPrestataire::with(['prestataire', 'lot.appelOffre', 'proforma', 'parentAttribution.parentAttribution', 'childAttributions'])->findOrFail($id);

        $prestataires = Prestataire::where('statut_prestataire', true)
            ->orderBy('raison_sociale_prestataire')
            ->get();

        // Plus besoin de charger les proformas existantes
        // $proformas = Proforma::where('actif_proforma', true)...

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => compact('attribution', 'prestataires'),
            ]);
        }

        return view('attributions.reattribuer', compact('attribution', 'prestataires'));
    }





    /**
     * Réattribuer un lot avec création d'une nouvelle proforma.
     *
     *
     * @OA\Post(
     *     path="/attributions/{attribution}/reattribuer",
     *     operationId="reattribuer",
     *     tags={"Attributions de Lots"},
     *     summary="Réattribuer un lot",
     *     description="Réattribue un lot à un nouveau prestataire avec création d'une nouvelle proforma. L'ancienne attribution est retirée.",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="attribution",
     *         in="path",
     *         description="UUID de l'attribution actuelle à remplacer",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/AttributionReattribuerRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Lot réattribué avec succès",
     *         @OA\JsonContent(ref="#/components/schemas/AttributionStoreResponse")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erreur de validation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Erreur de validation"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Attribution non trouvée",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Attribution non trouvée")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié",
     *         @OA\JsonContent(ref="#/components/schemas/UnauthenticatedResponse")
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur serveur",
     *         @OA\JsonContent(ref="#/components/schemas/ServerErrorResponse")
     *     )
     * )
     */
    public function reattribuer(Request $request, string $id)
    {
        // Règles de validation incluant les champs de la nouvelle proforma
        $rules = [
            'prestataire_id' => 'required|uuid|exists:prestataires,id_prestataire',
            'date_attribution' => 'required|date|before_or_equal:today',
            'date_debut_prevue' => 'required|date',
            'date_fin_prevue' => 'required|date|after:date_debut_prevue',
            'montant_engage' => 'nullable|numeric|min:0',
            'observations' => 'nullable|string|max:2000',
            'motif_reattribution' => 'required|string|min:10|max:2000',

            // Champs nouvelle proforma
            'new_numero_proforma' => 'nullable|string|max:35|unique:proformas,numero_proforma',
            'new_date_proforma' => 'required|date|before_or_equal:today',
            // 'new_date_redemarrage' => 'nullable|date',
            'new_montant_retenu' => 'required|numeric|min:0',
            'new_taux_tva' => 'nullable|numeric|min:0|max:100',
            'new_taxe_montant' => 'nullable|numeric|min:0',
            'new_remise_montant' => 'nullable|numeric|min:0',
            'new_modalite' => 'nullable|string|max:500',
        ];

        $messages = array_merge($this->validationMessages(), [
            'new_numero_proforma.unique' => 'Ce numéro de proforma existe déjà.',
            'new_numero_proforma.max' => 'Le numéro ne peut pas dépasser 35 caractères.',
            'new_date_proforma.required' => 'La date de proforma est obligatoire.',
            'new_montant_retenu.required' => 'Le montant retenu HT est obligatoire.',
            'new_montant_retenu.numeric' => 'Le montant retenu doit être un nombre.',
            'new_montant_retenu.min' => 'Le montant retenu ne peut pas être négatif.',
        ]);

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $ancienneAttribution = AttributionLotPrestataire::findOrFail($id);

        try {
            DB::beginTransaction();

            // 1. Créer la nouvelle proforma
            $numeroProforma = $request->new_numero_proforma;
            if (empty($numeroProforma)) {
                // Auto-génération du numéro
                $annee = date('Y');
                $dernierNum = Proforma::whereYear('created_at', $annee)
                    ->orderBy('created_at', 'desc')
                    ->first();

                $sequence = 1;
                if ($dernierNum && preg_match('/PROF-' . $annee . '-(\d+)/', $dernierNum->numero_proforma, $matches)) {
                    $sequence = intval($matches[1]) + 1;
                }
                $numeroProforma = 'PROF-' . $annee . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
            }

            // Calcul du montant TTC
            $montantHT = floatval($request->new_montant_retenu) ?: 0;
            $taxeMontant = floatval($request->new_taxe_montant) ?: 0;
            $remiseMontant = floatval($request->new_remise_montant) ?: 0;
            $montantTTC = $montantHT + $taxeMontant - $remiseMontant;

            $proforma = Proforma::create([
                'numero_proforma' => $numeroProforma,
                'date_proforma' => $request->new_date_proforma,
                'date_debut_validee_proforma' => $request->date_debut_prevue,
                'date_fin_validee_proforma' => $request->date_fin_prevue,
                'montant_retenu_proforma' => $montantHT,
                'taxe_montant' => $taxeMontant,
                'remise_montant_proforma' => $remiseMontant,
                'montant_ttc' => $montantTTC,
                'modalite_proforma' => $request->new_modalite,
                'actif_proforma' => true,
                'version_proforma' => 1,
                'prestataire_id' => $request->prestataire_id,
                'lot_id' => $ancienneAttribution->lot_id,
                'created_by' => Auth::id(),
            ]);



            // 2. Retirer l'ancienne attribution si encore active
            if (
                $ancienneAttribution->is_active &&
                $ancienneAttribution->statut_attribution === AttributionLotPrestataire::STATUT_ATTRIBUE
            ) {
                $ancienneAttribution->retirer(
                    $request->motif_reattribution,
                    AttributionLotPrestataire::TYPE_RETRAIT_RESILIATION
                );
            }

            // 3. Préparer les données pour la nouvelle attribution
            $dataReattribution = $validator->validated();
            $dataReattribution['proforma_id'] = $proforma->id_proforma;

            // Utiliser le montant TTC comme montant engagé si non spécifié
            if (empty($dataReattribution['montant_engage'])) {
                $dataReattribution['montant_engage'] = $montantTTC;
            }

            // 4. Créer la nouvelle attribution
            $nouvelleAttribution = $ancienneAttribution->reattribuer($dataReattribution);

            DB::commit();

            Log::info('Lot réattribué avec nouvelle proforma', [
                'ancienne_attribution' => $id,
                'nouvelle_attribution' => $nouvelleAttribution->id_attribution,
                'proforma' => $proforma->id_proforma,
                'user' => Auth::id()
            ]);

            $message = 'Le lot a été réattribué avec succès. Nouvelle proforma créée : ' . $numeroProforma;

            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => $nouvelleAttribution->load(['prestataire', 'lot', 'proforma']),
                ]);
            }

            return redirect()->route('attributions.show', $nouvelleAttribution->id_attribution)->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur réattribution: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }

            return back()->withInput()->with('error', 'Erreur lors de la réattribution : ' . $e->getMessage());
        }
    }

    /**
     * Terminer une attribution.
     *
     *
     * @OA\Post(
     *     path="/attributions/{attribution}/terminer",
     *     operationId="terminerAttribution",
     *     tags={"Attributions de Lots"},
     *     summary="Terminer une attribution",
     *     description="Marque une attribution comme terminée (travaux achevés, avancement à 100%)",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="attribution",
     *         in="path",
     *         description="UUID de l'attribution",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(ref="#/components/schemas/AttributionTerminerRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Attribution terminée avec succès",
     *         @OA\JsonContent(ref="#/components/schemas/AttributionActionResponse")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="L'attribution ne peut pas être terminée",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Cette attribution ne peut pas être terminée.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Attribution non trouvée",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Attribution non trouvée")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié",
     *         @OA\JsonContent(ref="#/components/schemas/UnauthenticatedResponse")
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur serveur",
     *         @OA\JsonContent(ref="#/components/schemas/ServerErrorResponse")
     *     )
     * )
     */
    public function terminer(Request $request, string $id)
    {
        $attribution = AttributionLotPrestataire::findOrFail($id);

        if (!$attribution->peutEtreTerminee()) {
            $message = 'Cette attribution ne peut pas être terminée.';
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }
            return back()->with('error', $message);
        }

        try {
            $attribution->terminer($request->input('observations'));

            Log::info('Attribution terminée', ['id' => $id, 'user' => Auth::id()]);

            $message = 'Attribution terminée avec succès.';

            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json(['success' => true, 'message' => $message, 'data' => $attribution->fresh()]);
            }

            return redirect()->route('attributions.show', $id)->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Erreur terminaison: ' . $e->getMessage());

            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Erreur lors de la terminaison.'], 500);
            }

            return back()->with('error', 'Erreur lors de la terminaison.');
        }
    }



    /**
     * Historique d'un lot.
     *
     *
     * @OA\Get(
     *     path="/lots/{lot}/attributions",
     *     operationId="historiqueAttributionLot",
     *     tags={"Attributions de Lots"},
     *     summary="Historique des attributions d'un lot",
     *     description="Récupère toutes les attributions (actuelle et passées) pour un lot donné",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="lot",
     *         in="path",
     *         description="UUID du lot",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Historique récupéré avec succès",
     *         @OA\JsonContent(ref="#/components/schemas/AttributionHistoriqueResponse")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Lot non trouvé",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Lot introuvable.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié",
     *         @OA\JsonContent(ref="#/components/schemas/UnauthenticatedResponse")
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur serveur",
     *         @OA\JsonContent(ref="#/components/schemas/ServerErrorResponse")
     *     )
     * )
     */
    public function historiqueLot(Request $request, string $lotId)
    {
        try {
            $lot = Lot::with('appelOffre')->findOrFail($lotId);

            $historique = AttributionLotPrestataire::where('lot_id', $lotId)
                ->with(['prestataire', 'proforma', 'createdBy'])
                ->orderBy('version_attribution', 'asc')
                ->get();

            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => compact('lot', 'historique'),
                ]);
            }

            return view('attributions.historique-lot', compact('lot', 'historique'));
        } catch (\Exception $e) {
            Log::error('Erreur historique lot: ' . $e->getMessage());

            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Lot introuvable.'], 404);
            }

            return back()->with('error', 'Lot introuvable.');
        }
    }

    /**
     * Historique d'un prestataire.
     *
     *
     * @OA\Get(
     *     path="/prestataires/{prestataire}/attributions",
     *     operationId="historiquePrestataire",
     *     tags={"Attributions de Lots"},
     *     summary="Historique des attributions d'un prestataire",
     *     description="Récupère toutes les attributions d'un prestataire avec statistiques",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="prestataire",
     *         in="path",
     *         description="UUID du prestataire",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Historique récupéré avec succès",
     *         @OA\JsonContent(ref="#/components/schemas/AttributionHistoriqueResponse")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Prestataire non trouvé",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Prestataire introuvable.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié",
     *         @OA\JsonContent(ref="#/components/schemas/UnauthenticatedResponse")
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur serveur",
     *         @OA\JsonContent(ref="#/components/schemas/ServerErrorResponse")
     *     )
     * )
     */
    public function historiquePrestataire(Request $request, string $prestataireId)
    {
        try {
            $prestataire = Prestataire::findOrFail($prestataireId);

            $historique = AttributionLotPrestataire::where('prestataire_id', $prestataireId)
                ->with(['lot.appelOffre', 'proforma'])
                ->orderBy('created_at', 'desc')
                ->get();

            $statistiques = AttributionLotPrestataire::statistiquesPrestataire($prestataireId);

            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => compact('prestataire', 'historique', 'statistiques'),
                ]);
            }

            return view('attributions.historique-prestataire', compact('prestataire', 'historique', 'statistiques'));
        } catch (\Exception $e) {
            Log::error('Erreur historique prestataire: ' . $e->getMessage());

            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Prestataire introuvable.'], 404);
            }

            return back()->with('error', 'Prestataire introuvable.');
        }
    }



    public function ajoutDateEffectiveFin(Request $request, string $id){

        $attribution = AttributionLotPrestataire::findOrFail($id);
        $dateDebutPrevue = $attribution->date_debut_prevue;
        $dateEffectiveFin = $attribution->date_effective_fin;


        if($dateEffectiveFin){
            if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json(['success' => false, 'message' => 'La date effective est déjà ajouté.'], 422);
            }

            return back()->with('error', 'La date effective est déjà ajouté.');
        }


        $validator = Validator::make($request->all(), [
            'date_effective_fin' => 'required|date|before_or_equal:today',
        ], [
            'date_effective_fin.required' => 'La date effective de fin est obligatoire.'
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        try {
            $dateReprise = $request->filled('date_effective_fin')
                ? Carbon::parse($request->date_reprise_prevue)
                : null;

            $attribution->ajoutDateEffectiveFin( $dateReprise);

            Log::info('Date effective de fin ajoutée', ['id' => $id, 'user' => Auth::id()]);

            $message = 'Date effective de fin ajoutée avec succès.';

            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json(['success' => true, 'message' => $message, 'data' => $attribution->fresh()]);
            }

            return redirect()->route('attributions.show', $id)->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Erreur suspension: ' . $e->getMessage());

            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Erreur lors de la suspension.'], 500);
            }

            return back()->with('error', 'Erreur lors de la suspension.');
        }
    }



    /**
     * Messages de validation personnalisés
     */
    private function validationMessages(): array
    {
        return [
            'prestataire_id.required' => 'Veuillez sélectionner un prestataire.',
            'prestataire_id.exists' => 'Le prestataire sélectionné n\'existe pas.',
            'lot_id.required' => 'Veuillez sélectionner un lot.',
            'lot_id.exists' => 'Le lot sélectionné n\'existe pas.',
            'proforma_id.required' => 'Veuillez sélectionner une proforma.',
            'proforma_id.exists' => 'La proforma sélectionnée n\'existe pas.',
            'date_attribution.required' => 'La date d\'attribution est obligatoire.',
            'date_attribution.before_or_equal' => 'La date d\'attribution ne peut pas être dans le futur.',
            'date_debut_prevue.required' => 'La date de début est obligatoire.',
            'date_debut_prevue.after_or_equal' => 'La date de début doit être égale ou postérieure à la date d\'attribution.',
            'date_fin_prevue.required' => 'La date de fin est obligatoire.',
            'date_fin_prevue.after' => 'La date de fin doit être postérieure à la date de début.',

            'montant_engage.numeric' => 'Le montant engagé doit être un nombre.',
            'montant_engage.min' => 'Le montant engagé ne peut pas être négatif.',
            'observations.max' => 'Les observations ne peuvent pas dépasser 2000 caractères.',
            'conditions_particulieres.max' => 'Les conditions particulières ne peuvent pas dépasser 5000 caractères.',
        ];
    }




    /**
     * Récupérer les lots attribués à un prestataire spécifique.
     *
     * @param Request $request
     * @param string $prestataireId
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function lotsAttribuesPrestataire(Request $request, string $prestataireId)
    {
        try {
            // Vérifier que le prestataire existe
            $prestataire = Prestataire::findOrFail($prestataireId);

            // Construire la requête de base
            $query = AttributionLotPrestataire::where('prestataire_id', $prestataireId)
                ->with([
                    'lot.appelOffre',
                    'proforma',
                    'createdBy'
                ]);

            // Filtre par statut d'attribution
            if ($request->filled('statut')) {
                $query->where('statut_attribution', $request->statut);
            }

            // Filtre par attributions actives uniquement
            if ($request->filled('is_active')) {
                $query->where('is_active', $request->boolean('is_active'));
            } else {
                // Par défaut, récupérer uniquement les attributions actives avec statut "Attribué"
                $query->where('is_active', true)
                      ->where('statut_attribution', AttributionLotPrestataire::STATUT_ATTRIBUE);
            }

            // Filtre par appel d'offre
            if ($request->filled('appel_offre_id')) {
                $query->whereHas('lot', function ($q) use ($request) {
                    $q->where('appel_offre_id', $request->appel_offre_id);
                });
            }

            // Recherche textuelle
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('numero_attribution', 'like', "%{$search}%")
                      ->orWhereHas('lot', function ($subQ) use ($search) {
                          $subQ->where('numero', 'like', "%{$search}%")
                               ->orWhere('libelle', 'like', "%{$search}%");
                      });
                });
            }

            // Tri
            $sortBy = $request->get('sort_by', 'date_attribution');
            $sortOrder = $request->get('sort_order', 'desc');
            $allowedSorts = [
                'date_attribution',
                'created_at',
                'numero_attribution',
                'pourcentage_avancement',
                'statut_attribution',
                'date_debut_prevue',
                'date_fin_prevue'
            ];

            if (in_array($sortBy, $allowedSorts)) {
                $query->orderBy($sortBy, $sortOrder);
            }

            // Pagination ou collection complète
            if ($request->boolean('all')) {
                $lotsAttribues = $query->get();
            } else {
                $lotsAttribues = $query->paginate($request->get('per_page', 15));
            }

            // Statistiques du prestataire
            $statistiques = [
                'total_attributions' => AttributionLotPrestataire::where('prestataire_id', $prestataireId)->count(),
                'lots_en_cours' => AttributionLotPrestataire::where('prestataire_id', $prestataireId)
                    ->where('is_active', true)
                    ->where('statut_attribution', AttributionLotPrestataire::STATUT_ATTRIBUE)
                    ->count(),
                'lots_suspendus' => AttributionLotPrestataire::where('prestataire_id', $prestataireId)
                    ->where('is_active', true)
                    ->where('statut_attribution', AttributionLotPrestataire::STATUT_SUSPENDU)
                    ->count(),
                'lots_termines' => AttributionLotPrestataire::where('prestataire_id', $prestataireId)
                    ->where('statut_attribution', AttributionLotPrestataire::STATUT_TERMINE)
                    ->count(),
                'lots_retires' => AttributionLotPrestataire::where('prestataire_id', $prestataireId)
                    ->where('statut_attribution', AttributionLotPrestataire::STATUT_RETIRE)
                    ->count(),
                'lots_en_retard' => AttributionLotPrestataire::where('prestataire_id', $prestataireId)
                    ->where('is_active', true)
                    ->where('statut_attribution', AttributionLotPrestataire::STATUT_ATTRIBUE)
                    ->where(function ($q) {
                        $q->where('jours_retard', '>', 0)
                          ->orWhere(function ($subQ) {
                              $subQ->whereNotNull('date_fin_prevue')
                                   ->whereNull('date_fin_reelle')
                                   ->where('date_fin_prevue', '<', now());
                          });
                    })
                    ->count(),
                'montant_total_engage' => AttributionLotPrestataire::where('prestataire_id', $prestataireId)
                    ->where('is_active', true)
                    ->sum('montant_engage'),
                'montant_total_paye' => AttributionLotPrestataire::where('prestataire_id', $prestataireId)
                    ->where('is_active', true)
                    ->sum('montant_paye'),
            ];

            // Réponse JSON pour API
            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'prestataire' => $prestataire,
                        'lots_attribues' => $lotsAttribues,
                        'statistiques' => $statistiques,
                    ],
                    'message' => 'Lots attribués récupérés avec succès.'
                ]);
            }

            // Réponse Web (Vue Blade)
            return view('attributions.lots-prestataire', compact(
                'prestataire',
                'lotsAttribues',
                'statistiques'
            ));

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Prestataire non trouvé pour lots attribués', ['prestataire_id' => $prestataireId]);

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Prestataire non trouvé.'
                ], 404);
            }

            return back()->with('error', 'Prestataire non trouvé.');

        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des lots attribués au prestataire', [
                'prestataire_id' => $prestataireId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la récupération des lots attribués.',
                    'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne'
                ], 500);
            }

            return back()->with('error', 'Erreur lors de la récupération des lots attribués.');
        }
    }
}
