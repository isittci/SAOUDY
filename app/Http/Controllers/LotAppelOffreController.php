<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Lot;
use App\Models\Proforma;
use App\Models\AppelOffre;
use App\Models\Prestataire;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Lots par Appel d'Offres",
 *     description="Gestion des lots dans le contexte d'un appel d'offres spécifique (routes imbriquées)"
 * )
 */
class LotAppelOffreController extends Controller
{

    /**
     * Affiche la liste des lots d'un appel d'offres
     *
     * @OA\Get(
     *     path="/appels-offres/{appel_offre}/lots",
     *     operationId="getLotsByAppelOffre",
     *     tags={"Lots par Appel d'Offres"},
     *     summary="Liste des lots d'un AO",
     *     description="Récupère la liste paginée des lots d'un appel d'offres spécifique. Requiert la permission `lots.read`.",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="appel_offre",
     *         in="path",
     *         required=true,
     *         description="UUID de l'appel d'offres",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Parameter(
     *         name="attribution",
     *         in="query",
     *         required=false,
     *         description="Filtrer par statut d'attribution (0=non attribué, 1=attribué)",
     *         @OA\Schema(type="integer", enum={0, 1})
     *     ),
     *     @OA\Parameter(
     *         name="statut",
     *         in="query",
     *         required=false,
     *         description="Filtrer par statut du lot",
     *         @OA\Schema(type="integer", enum={0, 1})
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         required=false,
     *         description="Recherche dans numéro et libellé",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", default="created_at")
     *     ),
     *     @OA\Parameter(
     *         name="sort_order",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", enum={"asc", "desc"}, default="desc")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Liste récupérée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Lot"))
     *             ),
     *             @OA\Property(property="message", type="string", example="Liste des lots récupérée avec succès")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Appel d'offres introuvable"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function index(Request $request, $appelOffreId)
    {

        try {

            $appelOffre = AppelOffre::findOrFail($appelOffreId);

            $query = Lot::with([
                'appelOffre.typeAppelOffre',
                'creator',
                'attributionActive.prestataire'
            ])->where('appel_offre_id', $appelOffre->id_appel_offre)?->versionActuelle();



            if ($request->filled('attribution')) {
                $query->where('attribution_lot', $request->attribution);
            }

            if ($request->filled('statut')) {
                $query->where('statut_lot', $request->statut);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('numero', 'like', "%{$search}%")->orWhere('libelle', 'like', "%{$search}%");
                });
            }

            // Tri
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 15);
            $lots = $query->paginate($perPage);

            // Retour selon le type de requête
            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => $lots,
                    'message' => 'Liste des lots récupérée avec succès'
                ]);
            }



            // Récupérer les appels d'offres pour le filtre
            $appelsOffres = AppelOffre::with('typeAppelOffre')
                ->actif()
                ->orderBy('created_at', 'desc')
                ->get();

            return view('appels-offres.lot-index', compact('lots', 'appelsOffres', 'appelOffre'));
        } catch (Exception $e) {
            Log::error('Erreur lors de la récupération des lots: ' . $e->getMessage());

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la récupération des données',
                    'error' => $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Erreur lors de la récupération des données');
        }
    }

    /**
     * Affiche le formulaire de création
     * IMPORTANT: La création d'un lot nécessite obligatoirement un appel d'offres
     */
    public function create(Request $request, $appelOffreId)
    {
        // Vérifier qu'un appel d'offres est spécifié
        $appelOffreId = $request->get('appel_offre_id');

        if (!$appelOffreId) {
            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Un appel d\'offres doit être spécifié pour créer un lot',
                    'error' => 'appel_offre_id manquant'
                ], 422);
            }

            return redirect()->route('appels-offres.index')
                ->with('error', 'Veuillez d\'abord sélectionner un appel d\'offres');
        }

        try {
            $appelOffre = AppelOffre::findOrFail($appelOffreId);

            // Vérifier que l'appel d'offres est actif
            if (!$appelOffre->statut_evaluation_critere_appel_offre) {
                return back()->with('error', 'Impossible de créer un lot pour un appel d\'offres inactif');
            }

            // Vérifier que l'appel d'offres n'est pas clôturé
            if ($appelOffre->isCloture()) {
                return back()->with('error', 'Impossible de créer un lot pour un appel d\'offres clôturé');
            }

            return view('lots.create', compact('appelOffre'));
        } catch (Exception $e) {
            return back()->with('error', 'Appel d\'offres introuvable');
        }
    }


    /**
     * Enregistre un nouveau lot pour un appel d'offres
     *
     * @OA\Post(
     *     path="/appels-offres/{appel_offre}/lots",
     *     operationId="createLotByAppelOffre",
     *     tags={"Lots par Appel d'Offres"},
     *     summary="Créer un lot pour un AO",
     *     description="Crée un nouveau lot dans le contexte d'un appel d'offres spécifique. L'AO doit être actif et non clôturé. Requiert la permission `lots.create`.",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="appel_offre",
     *         in="path",
     *         required=true,
     *         description="UUID de l'appel d'offres",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"numero", "libelle", "budget_lot", "date_debut_prevue", "date_fin_prevue", "statut_lot"},
     *             @OA\Property(property="numero", type="string", maxLength=35, example="LOT-001"),
     *             @OA\Property(property="libelle", type="string", maxLength=160, example="Gros œuvre"),
     *             @OA\Property(property="description_critere", type="string", nullable=true),
     *             @OA\Property(property="specifications_techniques", type="string", nullable=true),
     *             @OA\Property(property="budget_lot", type="number", minimum=0, example=45000000),
     *             @OA\Property(property="date_debut_prevue", type="string", format="date"),
     *             @OA\Property(property="date_fin_prevue", type="string", format="date", description="Doit être après date_debut_prevue"),
     *             @OA\Property(property="statut_lot", type="integer", enum={0, 1})
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Lot créé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Lot"),
     *             @OA\Property(property="message", type="string", example="Lot créé avec succès.")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Appel d'offres inexistant"),
     *     @OA\Response(response=422, description="Validation échouée ou AO inactif"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function store(Request $request, $appelOffreId = null)
    {
        // Utiliser l'ID de la route ou celui du body
        $appelOffreIdFinal = $appelOffreId ?? $request->appel_offre_id;

        // Vérifier qu'on a bien un ID d'appel d'offre
        if (!$appelOffreIdFinal) {
            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'L\'identifiant de l\'appel d\'offres est obligatoire.',
                    'errors' => ['appel_offre_id' => ['L\'appel d\'offres est obligatoire']]
                ], 422);
            }

            return back()->withErrors(['appel_offre_id' => 'L\'appel d\'offres est obligatoire'])->withInput();
        }

        // Vérifier que l'appel d'offres existe
        $appelOffre = AppelOffre::find($appelOffreIdFinal);

        if (!$appelOffre) {
            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'L\'appel d\'offres n\'existe pas.',
                    'errors' => ['appel_offre_id' => ['Appel d\'offres invalide']]
                ], 404);
            }

            return back()->withErrors(['appel_offre_id' => 'Appel d\'offres invalide'])->withInput();
        }

        $dateDemarrageAO = $appelOffre->caracteristiqueActive->date_demarrage_prevue_caracteristique_appel_offre;
        $dateLivraisonAO = $appelOffre->caracteristiqueActive->date_livraison_previsionnelle_caracteristique_appel_offre;

        // return response()->json($appelOffre->caracteristiqueActive);

        // Fusionner l'ID dans la requête pour la validation
        $request->merge(['appel_offre_id' => $appelOffreIdFinal]);

        // Validation
        $validator = Validator::make($request->all(), [
            'numero' => 'required|string|max:35',
            'libelle' => 'required|string|max:160',
            'description_critere' => 'nullable|string',
            'specifications_techniques' => 'nullable|string',
            'date_debut_prevue' => 'required|date',
            'budget_lot' => 'required|numeric|min:0',
            'date_fin_prevue' => 'required|date|after:date_debut_prevue',
            'statut_lot' => 'required|in:0,1',
        ], [
            'numero.required' => 'Le numéro est obligatoire.',
            'numero.max' => 'Le numéro ne peut pas dépasser 35 caractères.',
            'libelle.required' => 'Le libellé est obligatoire.',
            'libelle.max' => 'Le libellé ne peut pas dépasser 160 caractères.',
            'budget_lot.required' => 'Le budget du lot est obligatoire.',
            'budget_lot.numeric' => 'Le budget doit être un nombre.',
            'budget_lot.min' => 'Le budget ne peut pas être négatif.',
            'date_fin_prevue.after' => 'La date de fin doit être postérieure à la date de début.',
            'statut_lot.required' => 'Le statut est obligatoire.',
            'statut_lot.in' => 'Le statut doit être 0 (inactif) ou 1 (actif).',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()
                ], 422);
            }

            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            // Vérifier que l'appel d'offres est actif
            if (!$appelOffre->statut_evaluation_critere_appel_offre) {
                throw new Exception("Impossible de créer un lot pour un appel d'offres inactif.");
            }

            // Vérifier l'unicité du numéro dans l'appel d'offres
            $existingLot = Lot::where('appel_offre_id', $appelOffreIdFinal)
                ->where('numero', $request->numero)
                ->versionActuelle()
                ->first();

            if ($existingLot) {
                throw new Exception("Un lot avec ce numéro existe déjà pour cet appel d'offres.");
            }

            // Créer le lot
            $lot = Lot::create([
                'appel_offre_id' => $appelOffreIdFinal,
                'numero' => $request->numero,
                'libelle' => Str::upper($request->libelle),
                'description_critere' => $request->description_critere,
                'specifications_techniques' => $request->specifications_techniques,
                'date_debut_prevue' => $request->date_debut_prevue,
                'date_fin_prevue' => $request->date_fin_prevue,
                'budget_lot' => $request->budget_lot,
                'statut_lot' => $request->statut_lot,
                'created_by' => auth()->id(),
            ]);

            DB::commit();

            Log::info("Lot créé", ['id' => $lot->id_lot, 'appel_offre_id' => $appelOffreIdFinal]);

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => $lot->load('appelOffre'),
                    'message' => 'Lot créé avec succès.'
                ], 201);
            }

            return redirect()->route('lots-appels-offres.show', [$lot->appel_offre_id, $lot->id_lot])
                ->with('success', 'Lot créé avec succès.');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la création du lot: ' . $e->getMessage());

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la création',
                    'error' => $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Erreur: ' . $e->getMessage())->withInput();
        }
    }


    /**
     * Affiche les détails d'un lot
     *
     * @OA\Get(
     *     path="/appels-offres/{appel_offre}/lots/{id}",
     *     operationId="showLotByAppelOffre",
     *     tags={"Lots par Appel d'Offres"},
     *     summary="Détails d'un lot",
     *     description="Récupère les détails complets d'un lot dans le contexte de son appel d'offres. Requiert la permission `lots.view-details`.",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="appel_offre",
     *         in="path",
     *         required=true,
     *         description="UUID de l'appel d'offres",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="UUID du lot",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Détails récupérés avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/LotDetailed"),
     *             @OA\Property(property="message", type="string", example="Détails du lot récupérés avec succès")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Lot introuvable")
     * )
     */
    public function show(Request $request, $appelOffreId, $id)
    {

        try {
            $lot = Lot::actif()->with([
                'appelOffre.typeAppelOffre',
                'creator',
                'updater',
                'attributionActive.prestataire',
                'attributionActive.proforma.facture.paiements',
                'criteresEvaluation.evaluations'
            ])->find($id);

            $allPaiements = $lot->attributionActive?->proforma?->facture?->paiements ?? null;
            $proforma = $lot->attributionActive?->proforma?->facture ?? null;

            $sommesReferencesCriteresEvaluations = $lot->criteresEvaluation->sum('note_reference_critere_evaluation');
            $sommesNotesEvaluations = $lot->criteresEvaluation->flatMap->evaluations->sum('resultat_evaluation');

            $toutSolder = $allPaiements ? $allPaiements->sum('montant_net_paye_paiement') == $proforma->montant_facture : false;
            $evaluationTerminee = $sommesReferencesCriteresEvaluations > 0 && $sommesNotesEvaluations > 0 ? $sommesReferencesCriteresEvaluations == $sommesNotesEvaluations : false;

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => $lot,
                    'message' => 'Détails du lot récupérés avec succès'
                ]);
            }

            // Charger les prestataires et proformas pour le modal d'attribution
            $prestataires = Prestataire::actif()->orderBy('raison_sociale_prestataire')->get();
            $proformas = Proforma::actif()->orderBy('numero_proforma', 'desc')->get();

            // Méthode 2 : Plus concise avec pluck et sum
            $sommeMontantsRetenus = $lot->appelOffre->lots()
                ->with('attributionActive.proforma')
                ->get()
                ->pluck('attributionActive.proforma.montant_retenu_proforma')
                ->sum();

            // Montant total des montants retenus restant pour l'appel d'offres
            $montantRestant = $lot->appelOffre->montant_global_appel_offre - $sommeMontantsRetenus;



            return view('appels-offres.lot-show', compact('lot', 'prestataires', 'proformas', 'montantRestant', 'toutSolder', 'evaluationTerminee'));
        } catch (Exception $e) {
            Log::error('Erreur lors de la récupération du lot: ' . $e->getMessage());

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lot introuvable',
                    'error' => $e->getMessage()
                ], 404);
            }

            return back()->with('error', 'Lot introuvable');
        }
    }


    public function edit(Request $request, $appelOffreId, $id)
    {

        try {
            $lot = Lot::with(['appelOffre.typeAppelOffre'])->findOrFail($id);

            // Vérifier que le lot n'est pas attribué
            // if ($lot->isAttribue()) {
            //     return back()->with('error', 'Impossible de modifier un lot attribué');
            // }

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => $lot,
                    'message' => 'Données du lot récupérées pour modification'
                ]);
            }

            return view('appels-offres.lot-edit', compact('lot'));
        } catch (Exception $e) {
            Log::error('Erreur lors de la récupération du lot pour édition: ' . $e->getMessage());

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lot introuvable',
                    'error' => $e->getMessage()
                ], 404);
            }

            return back()->with('error', 'Lot introuvable');
        }
    }



    /**
     * Met à jour un lot
     *
     * @OA\Put(
     *     path="/appels-offres/{appel_offre}/lots/{id}",
     *     operationId="updateLotByAppelOffre",
     *     tags={"Lots par Appel d'Offres"},
     *     summary="Mettre à jour un lot",
     *     description="Met à jour un lot en créant une nouvelle version. Le lot ne doit pas être attribué. Requiert la permission `lots.update`.",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="appel_offre",
     *         in="path",
     *         required=true,
     *         description="UUID de l'appel d'offres",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="UUID du lot",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"libelle", "budget_lot", "statut_lot", "motif_modification"},
     *             @OA\Property(property="libelle", type="string", maxLength=160),
     *             @OA\Property(property="description_critere", type="string", nullable=true),
     *             @OA\Property(property="specifications_techniques", type="string", nullable=true),
     *             @OA\Property(property="budget_lot", type="number", minimum=5),
     *             @OA\Property(property="date_debut_prevue", type="string", format="date", nullable=true),
     *             @OA\Property(property="date_fin_prevue", type="string", format="date", nullable=true),
     *             @OA\Property(property="statut_lot", type="integer", enum={0, 1}),
     *             @OA\Property(property="motif_modification", type="string", description="Motif obligatoire")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Nouvelle version créée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/LotDetailed"),
     *             @OA\Property(property="message", type="string", example="Nouvelle version du lot créée avec succès")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Lot introuvable"),
     *     @OA\Response(response=422, description="Validation échouée ou lot attribué"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function update(Request $request, $appelOffreId, $id)
    {
        $validator = Validator::make($request->all(), [
            'libelle' => 'required|string|max:160',
            'description_critere' => 'nullable|string',
            'specifications_techniques' => 'nullable|string',
            'date_debut_prevue' => 'nullable|date',
            'budget_lot'=> 'required|numeric|min:5',
            'date_fin_prevue' => 'nullable|date|after:date_debut_prevue',
            'statut_lot' => 'required|in:0,1',
        ], [
            'libelle.required' => 'Le libellé est obligatoire',
            'date_fin_prevue.after' => 'La date de fin doit être après la date de début',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()
                ], 422);
            }

            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $lot = Lot::findOrFail($id);

            // Vérifier que le lot n'est pas attribué
            // if ($lot->isAttribue()) {
            //     throw new Exception('Impossible de modifier un lot attribué. Créez une nouvelle version.');
            // }

            $lot->update([
                'libelle' => Str::upper($request->libelle),
                'description_critere' => $request->description_critere,
                'specifications_techniques' => $request->specifications_techniques,
                'date_debut_prevue' => $request->date_debut_prevue,
                'date_fin_prevue' => $request->date_fin_prevue,
                'statut_lot' => $request->statut_lot,
                'budget_lot' => $request->budget_lot,
                'updated_by' => auth()->id(),
            ]);

            $attributionActive = $lot->attributionActive;



            if ($attributionActive) {
                $proforma = $attributionActive->proforma;
                if($proforma){
                    $proforma->update([
                        'montant_retenu_proforma' => $request->budget_lot - $request->budget_lot * 0.18,
                        'taxe_montant' => $request->budget_lot * 0.18,
                        'date_debut_validee_proforma' => $request->date_debut_prevue,
                        'date_fin_validee_proforma' => $request->date_fin_prevue,
                        'updated_by' => auth()->id(),
                    ]);
                }
            }

            DB::commit();

            Log::info("Lot modifié", ['id' => $id]);

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => $lot->fresh()->load('appelOffre'),
                    'message' => 'Lot modifié avec succès'
                ]);
            }

            return redirect()->route('lots-appels-offres.show', [$appelOffreId, $id])
                ->with('success', 'Lot modifié avec succès');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la modification du lot: ' . $e->getMessage());

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la modification',
                    'error' => $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Erreur lors de la modification: ' . $e->getMessage())->withInput();
        }
    }



    /**
     * Supprime un lot
     *
     * @OA\Delete(
     *     path="/appels-offres/{appel_offre}/lots/{id}",
     *     operationId="deleteLotByAppelOffre",
     *     tags={"Lots par Appel d'Offres"},
     *     summary="Supprimer un lot",
     *     description="Supprime (soft delete) un lot. Impossible si le lot est attribué. Requiert la permission `lots.delete`.",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="appel_offre",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lot supprimé avec succès"
     *     ),
     *     @OA\Response(response=422, description="Lot attribué"),
     *     @OA\Response(response=404, description="Lot introuvable")
     * )
     */
    public function destroy(Request $request, $appelOffreId, $id)
    {
        DB::beginTransaction();
        try {
            $lot = Lot::findOrFail($id);

            // Vérifier que le lot n'est pas attribué
            if ($lot->isAttribue()) {
                throw new Exception('Impossible de supprimer un lot attribué');
            }

            $lot->deleted_by = auth()->id();
            $lot->save();
            $lot->delete();

            DB::commit();

            Log::info("Lot supprimé", ['id' => $id]);

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'message' => 'Lot supprimé avec succès'
                ]);
            }

            return redirect()->route('lots-appels-offres.index', $appelOffreId)
                ->with('success', 'Lot supprimé avec succès');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la suppression du lot: ' . $e->getMessage());

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la suppression',
                    'error' => $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }



    /**
     * Attribue un lot à un prestataire
     *
     * @OA\Post(
     *     path="/appels-offres/{appel_offre}/lots/{id}/attribuer",
     *     operationId="attribuerLotByAppelOffre",
     *     tags={"Lots par Appel d'Offres"},
     *     summary="Attribuer un lot",
     *     description="Attribue un lot à un prestataire. Peut créer automatiquement une proforma si mode='create'. Requiert la permission `attributions_lots.assign`.",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="appel_offre",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"prestataire_id", "proforma_mode"},
     *             @OA\Property(property="prestataire_id", type="string", format="uuid"),
     *             @OA\Property(
     *                 property="proforma_mode",
     *                 type="string",
     *                 enum={"select", "create"},
     *                 description="select: utiliser proforma existante, create: créer nouvelle"
     *             ),
     *             @OA\Property(
     *                 property="proforma_id",
     *                 type="string",
     *                 format="uuid",
     *                 description="Requis si proforma_mode=select"
     *             ),
     *             @OA\Property(
     *                 property="montant_retenu_proforma",
     *                 type="number",
     *                 description="Requis si proforma_mode=create"
     *             ),
     *             @OA\Property(
     *                 property="date_debut_validee_proforma",
     *                 type="string",
     *                 format="date",
     *                 description="Requis si proforma_mode=create"
     *             ),
     *             @OA\Property(
     *                 property="date_fin_validee_proforma",
     *                 type="string",
     *                 format="date",
     *                 description="Requis si proforma_mode=create"
     *             ),
     *             @OA\Property(property="date_attribution", type="string", format="date", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lot attribué avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Attribution"),
     *             @OA\Property(property="message", type="string", example="Lot attribué avec succès"),
     *             @OA\Property(property="proforma_mode", type="string"),
     *             @OA\Property(property="proforma_created", type="boolean")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation échouée ou lot déjà attribué"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function attribuer(Request $request, $appelOffreId, $id)
    {
        // Déterminer le mode de proforma (select ou create)
        $proformaMode = $request->input('proforma_mode', 'select');

        // Règles de validation de base
        $rules = [
            'prestataire_id' => 'required|exists:prestataires,id_prestataire',
            'date_attribution' => 'nullable|date',
            'proforma_mode' => 'nullable|in:select,create',
        ];

        // Messages de validation personnalisés
        $messages = [
            'prestataire_id.required' => 'Le prestataire est obligatoire.',
            'prestataire_id.exists' => 'Le prestataire sélectionné est invalide.',
            'proforma_id.required' => 'La proforma est obligatoire.',
            'proforma_id.exists' => 'La proforma sélectionnée est invalide.',
            'new_date_proforma.required' => 'La date de la proforma est obligatoire.',
            'new_date_debut_validee.required' => 'La date de début validée est obligatoire.',
            // 'new_date_redemarrage.required' => 'La date de redémarrage est obligatoire.',
            'new_date_fin_validee.required' => 'La date de fin validée est obligatoire.',
            'new_date_fin_validee.after' => 'La date de fin doit être après la date de début.',
            'new_montant_retenu.required' => 'Le montant retenu est obligatoire.',
            'new_montant_retenu.numeric' => 'Le montant retenu doit être un nombre.',
            'new_montant_retenu.min' => 'Le montant retenu doit être positif.',
            'new_taux_tva.required' => 'Le taux de TVA est obligatoire.',
            'new_taux_tva.between' => 'Le taux de TVA doit être compris entre 0 et 100.'
        ];

        // Ajouter les règles selon le mode
        if ($proformaMode === 'select') {
            // Mode sélection : proforma_id obligatoire
            $rules['proforma_id'] = 'required|exists:proformas,id_proforma';
        } else {
            // Mode création : tous les champs de la proforma sont obligatoires
            $rules['new_date_proforma'] = 'required|date';
            $rules['new_date_debut_validee'] = 'required|date';
            // $rules['new_date_redemarrage'] = 'required|date';
            $rules['new_date_fin_validee'] = 'required|date|after_or_equal:new_date_debut_validee';
            $rules['new_montant_retenu'] = 'required|numeric|min:0';
            $rules['new_taux_tva'] = 'required|numeric|between:0,100';
            $rules['new_taxe_montant'] = 'nullable|numeric|min:0';
            $rules['new_taux_remise'] = 'nullable|numeric|between:0,100';
            $rules['new_remise_montant'] = 'nullable|numeric|min:0';
            $rules['new_modalite_paiement'] = 'nullable|string|max:500';
        }

        // Validation
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            /**
             * @var Lot $lot
             */
            $lot = Lot::findOrFail($id);

            // Vérifier que le lot n'est pas déjà attribué
            if ($lot->isAttribue()) {
                throw new Exception('Ce lot est déjà attribué');
            }

            // Récupérer le prestataire
            $prestataire = Prestataire::findOrFail($request->prestataire_id);

            // Gérer la proforma selon le mode
            $proforma = null;

            if ($proformaMode === 'select') {
                // ================================
                // MODE SÉLECTION : Utiliser une proforma existante
                // ================================
                $proforma = Proforma::findOrFail($request->proforma_id);

                Log::info("Attribution avec proforma existante", [
                    'proforma_id' => $proforma->id_proforma,
                    'numero_proforma' => $proforma->numero_proforma
                ]);

            } else {
                // ================================
                // MODE CRÉATION : Créer une nouvelle proforma
                // ================================

                // Calculer les montants
                $montantRetenu = floatval($request->new_montant_retenu);
                $tauxTVA = floatval($request->new_taux_tva);
                $tauxRemise = floatval($request->input('new_taux_remise', 0));

                // Calcul TVA
                $montantTVA = $montantRetenu * ($tauxTVA / 100);

                // Calcul Remise
                $montantRemise = $montantRetenu * ($tauxRemise / 100);

                // Créer la proforma
                $proforma = Proforma::create([
                    'date_proforma' => $request->new_date_proforma,
                    'date_debut_validee_proforma' => $request->new_date_debut_validee,
                    'date_fin_validee_proforma' => $request->new_date_fin_validee,
                    'montant_retenu_proforma' => $montantRetenu,
                    'taxe_montant' => $montantTVA,
                    'remise_montant_proforma' => $montantRemise,
                    'modalite_proforma' => $request->new_modalite_paiement,
                    'actif_proforma' => true,
                    'version_proforma' => 1,
                    'created_by' => auth()->id(),
                ]);

                Log::info("Nouvelle proforma créée pour attribution", [
                    'proforma_id' => $proforma->id_proforma,
                    'numero_proforma' => $proforma->numero_proforma,
                    'montant_ht' => $montantRetenu,
                    'tva' => $montantTVA,
                    'remise' => $montantRemise,
                    'total_ttc' => $proforma->calculerMontantTTC()
                ]);
            }

            // Effectuer l'attribution
            $attribution = $lot->attribuerAuPrestataire(
                $prestataire,
                $proforma,
                auth()->id()
            );

            // Mettre à jour le lot avec la date d'attribution
            $lot->attribuer($request->date_attribution ?? now());

            DB::commit();

            Log::info("Lot attribué avec succès", [
                'lot_id' => $id,
                'lot_numero' => $lot->numero,
                'prestataire_id' => $request->prestataire_id,
                'prestataire_nom' => $prestataire->raison_sociale_prestataire,
                'proforma_id' => $proforma->id_proforma,
                'proforma_numero' => $proforma->numero_proforma,
                'mode_proforma' => $proformaMode
            ]);

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => $attribution->load(['prestataire', 'lot', 'proforma']),
                    'message' => 'Lot attribué avec succès',
                    'proforma_mode' => $proformaMode,
                    'proforma_created' => $proformaMode === 'create'
                ]);
            }

            $successMessage = $proformaMode === 'create'
                ? 'Lot attribué avec succès. Une nouvelle proforma (' . $proforma->numero_proforma . ') a été créée.'
                : 'Lot attribué avec succès.';

            return redirect()
                ->route('lots-appels-offres.show', [$appelOffreId, $id])
                ->with('success', $successMessage);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de l\'attribution du lot: ' . $e->getMessage(), [
                'lot_id' => $id,
                'prestataire_id' => $request->prestataire_id,
                'proforma_mode' => $proformaMode,
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de l\'attribution.',
                    'error' => $e->getMessage()
                ], 500);
            }

            return back()
                ->with('error', 'Erreur lors de l\'attribution: ' . $e->getMessage())
                ->withInput();
        }
    }




    /**
     * Retire un lot (annule l'attribution)
     *
     * @OA\Post(
     *     path="/appels-offres/{appel_offre}/lots/{id}/retirer",
     *     operationId="retirerLotByAppelOffre",
     *     tags={"Lots par Appel d'Offres"},
     *     summary="Retirer un lot",
     *     description="Retire/annule l'attribution d'un lot. Requiert la permission `attributions_lots.withdraw`.",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="appel_offre",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"motif_retrait"},
     *             @OA\Property(property="motif_retrait", type="string", example="Non-respect des délais")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lot retiré avec succès"
     *     ),
     *     @OA\Response(response=422, description="Validation échouée"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function retirer(Request $request, $appelOffreId, $id)
    {
        $validator = Validator::make($request->all(), [
            'motif_retrait' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {

            /**
             * @var Lot $lot
             */
            $lot = Lot::findOrFail($id);

            // $lot->retirer($request->motif_retrait, auth()->id());
            $lot->retirerAttribution($request->motif_retrait, auth()->id());

            DB::commit();

            Log::info("Lot retiré", ['id' => $id]);

            return response()->json([
                'success' => true,
                'data' => $lot,
                'message' => 'Lot retiré avec succès'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors du retrait: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du retrait',
                'error' => $e->getMessage()
            ], 500);
        }
    }




    /**
     * Historique des versions d'un lot
     *
     * @OA\Get(
     *     path="/appels-offres/{appel_offre}/lots/{id}/historique",
     *     operationId="historiqueLotByAppelOffre",
     *     tags={"Lots par Appel d'Offres"},
     *     summary="Historique d'un lot",
     *     description="Récupère l'historique des versions d'un lot. Requiert la permission `lots.view-history`.",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="appel_offre", in="path", required=true, @OA\Schema(type="string", format="uuid")),
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", format="uuid")),
     *     @OA\Response(
     *         response=200,
     *         description="Historique récupéré",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Lot"))
     *         )
     *     )
     * )
     */
    public function historique(Request $request, $appelOffreId, $id)
    {

        try {
            $lot = Lot::findOrFail($id);
            $historique = $lot->getHistorique();

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => $historique,
                    'message' => 'Historique récupéré avec succès'
                ]);
            }

            return view('appels-offres.lot-historique',  compact('lot', 'historique'));
        } catch (Exception $e) {
            Log::error('Erreur lors de la récupération de l\'historique: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération de l\'historique',
                'error' => $e->getMessage()
            ], 500);
        }
    }





    /**
     * Statistiques d'un lot
     *
     * @OA\Get(
     *     path="/appels-offres/{appel_offre}/lots/{id}/statistiques",
     *     operationId="statistiquesLotByAppelOffre",
     *     tags={"Lots par Appel d'Offres"},
     *     summary="Statistiques d'un lot",
     *     description="Récupère les statistiques d'un lot. Requiert la permission `lots.view-details`.",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="appel_offre", in="path", required=true, @OA\Schema(type="string", format="uuid")),
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", format="uuid")),
     *     @OA\Response(
     *         response=200,
     *         description="Statistiques récupérées",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/StatistiquesLot")
     *         )
     *     )
     * )
     */
    public function statistiques(Request $request, $appelOffreId, $id)
    {
        try {
            $lot = Lot::with(['attributionActive', 'historiqueAttributions'])->findOrFail($id);

            $stats = [
                'general' => [
                    'numero' => $lot->numero,
                    'libelle' => $lot->libelle,
                    'duree_prevue_jours' => $lot->calculerDuree(),
                    'est_attribue' => $lot->isAttribue(),
                    'est_retire' => $lot->isRetire(),
                ],
                'attribution' => $lot->getStatistiquesAttribution(),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'Statistiques récupérées avec succès'
            ]);
        } catch (Exception $e) {
            Log::error('Erreur lors de la récupération des statistiques: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des statistiques',
                'error' => $e->getMessage()
            ], 500);
        }
    }




    /**
     * Duplique un lot
     *
     * @OA\Post(
     *     path="/appels-offres/{appel_offre}/lots/{id}/duplicate",
     *     operationId="duplicateLotByAppelOffre",
     *     tags={"Lots par Appel d'Offres"},
     *     summary="Dupliquer un lot",
     *     description="Crée une copie d'un lot. Les critères d'évaluation sont également copiés. Requiert la permission `lots.duplicate`.",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="appel_offre", in="path", required=true, @OA\Schema(type="string", format="uuid")),
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", format="uuid")),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="nouveau_numero", type="string", description="Nouveau numéro (défaut: ancien-COPIE)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Lot dupliqué",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Lot")
     *         )
     *     )
     * )
     */
    public function duplicate(Request $request, $appelOffreId, $id)
    {
        DB::beginTransaction();
        try {
            $lot = Lot::findOrFail($id);

            // Créer une copie
            $nouveauLot = $lot->replicate();
            $nouveauLot->numero = $request->input('nouveau_numero', $lot->numero . '-COPIE');
            $nouveauLot->attribution_lot = 0;
            $nouveauLot->date_attribution = null;
            $nouveauLot->created_by = auth()->id();
            $nouveauLot->save();

            // Copier les critères d'évaluation
            foreach ($lot->criteresEvaluation as $critere) {
                $nouveauCritere = $critere->replicate();
                $nouveauCritere->lot_id = $nouveauLot->id_lot;
                $nouveauCritere->save();
            }

            DB::commit();

            Log::info("Lot dupliqué", [
                'original' => $id,
                'nouveau' => $nouveauLot->id_lot
            ]);

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => $nouveauLot->load('appelOffre'),
                    'message' => 'Lot dupliqué avec succès'
                ], 201);
            }

            return redirect()->route('lots.edit', $nouveauLot->id_lot)
                ->with('success', 'Lot dupliqué avec succès');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la duplication: ' . $e->getMessage());

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la duplication',
                    'error' => $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Erreur lors de la duplication');
        }
    }
}
