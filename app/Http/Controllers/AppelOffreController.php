<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\AppelOffre;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\TypeAppelOffre;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\CaracteristiqueAppelOffre;
use Illuminate\Support\Facades\Validator;


/**
 * @OA\Tag(
 *     name="Appels d'Offres",
 *     description="Gestion complète des appels d'offres (création, publication, clôture, évaluation)"
 * )
 */
class AppelOffreController extends Controller
{

    /**
     * Affiche la liste des appels d'offres
     *
     * @OA\Get(
     *     path="/appels-offres",
     *     operationId="getAppelsOffres",
     *     tags={"Appels d'Offres"},
     *     summary="Liste des appels d'offres",
     *     description="Récupère la liste paginée des appels d'offres avec filtres multiples (type, statut, état, dates, recherche). Requiert la permission `appels_offres.read`.",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="type_appel_offre_id",
     *         in="query",
     *         required=false,
     *         description="Filtrer par type d'appel d'offres (UUID)",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Parameter(
     *         name="statut",
     *         in="query",
     *         required=false,
     *         description="Filtrer par statut d'évaluation (0=inactif, 1=actif)",
     *         @OA\Schema(type="integer", enum={0, 1})
     *     ),
     *     @OA\Parameter(
     *         name="etat",
     *         in="query",
     *         required=false,
     *         description="Filtrer par état de l'appel d'offres",
     *         @OA\Schema(type="string", enum={"en_cours", "cloture", "publie"})
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         required=false,
     *         description="Recherche dans numéro, libellé et objet",
     *         @OA\Schema(type="string", example="AON-2024")
     *     ),
     *     @OA\Parameter(
     *         name="date_debut",
     *         in="query",
     *         required=false,
     *         description="Date de début (pour filtrage par période de publication)",
     *         @OA\Schema(type="string", format="date", example="2024-01-01")
     *     ),
     *     @OA\Parameter(
     *         name="date_fin",
     *         in="query",
     *         required=false,
     *         description="Date de fin (pour filtrage par période de publication)",
     *         @OA\Schema(type="string", format="date", example="2024-12-31")
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         required=false,
     *         description="Champ de tri",
     *         @OA\Schema(
     *             type="string",
     *             enum={"created_at", "numero_appel_offre", "montant_global_appel_offre", "date_publication_critere_appel_offre"},
     *             default="created_at"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sort_order",
     *         in="query",
     *         required=false,
     *         description="Ordre de tri",
     *         @OA\Schema(type="string", enum={"asc", "desc"}, default="desc")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         description="Nombre d'éléments par page",
     *         @OA\Schema(type="integer", minimum=1, maximum=100, default=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Liste récupérée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="data",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/AppelOffre")
     *                 ),
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=10),
     *                 @OA\Property(property="per_page", type="integer", example=15),
     *                 @OA\Property(property="total", type="integer", example=150)
     *             ),
     *             @OA\Property(property="message", type="string", example="Liste des appels d'offres récupérée avec succès")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié",
     *         @OA\JsonContent(ref="#/components/schemas/UnauthenticatedResponse")
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Permission refusée",
     *         @OA\JsonContent(ref="#/components/schemas/ForbiddenResponse")
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
            $query = AppelOffre::with([
                'typeAppelOffre',
                'creator',
                'caracteristiqueActive'
            ])->withCount('lots');

            // Filtres
            if ($request->filled('type_appel_offre_id')) {
                $query->where('type_appel_offre_id', $request->type_appel_offre_id);
            }

            if ($request->filled('statut')) {
                $query->where('statut_evaluation_critere_appel_offre', $request->statut);
            }

            if ($request->filled('etat')) {
                switch ($request->etat) {
                    case 'en_cours':
                        $query->enCours();
                        break;
                    case 'cloture':
                        $query->cloture();
                        break;
                    case 'publie':
                        $query->publie();
                        break;
                }
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('numero_appel_offre', 'like', "%{$search}%")
                        ->orWhere('libelle_critere_appel_offre', 'like', "%{$search}%")
                        ->orWhere('objet_critere_appel_offre', 'like', "%{$search}%");
                });
            }



            // Tri
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 15);
            $appelOffres = $query->paginate($perPage);

            // Retour selon le type de requête
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => $appelOffres,
                    'message' => 'Liste des appels d\'offres récupérée avec succès'
                ]);
            }

            // Récupérer les types pour le filtre
            $typesAO = TypeAppelOffre::actif()->get();

            return view('appels-offres.index', compact('appelOffres', 'typesAO'));
        } catch (Exception $e) {
            Log::error('Erreur lors de la récupération des AO: ' . $e->getMessage());

            if ($request->wantsJson() || $request->is('api/*')) {
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
     */
    public function create()
    {
        $typesAO = TypeAppelOffre::actif()->get();
        return view('appels-offres.create', compact('typesAO'));
    }



    /**
     * Enregistre un nouvel appel d'offres
     *
     * @OA\Post(
     *     path="/appels-offres",
     *     operationId="createAppelOffre",
     *     tags={"Appels d'Offres"},
     *     summary="Créer un appel d'offres",
     *     description="Crée un nouvel appel d'offres. Le numéro est généré automatiquement. Le montant doit correspondre à l'intervalle du type sélectionné. Requiert la permission `appels_offres.create`.",
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Données de l'appel d'offres",
     *         @OA\JsonContent(
     *             required={"type_appel_offre_id", "libelle_critere_appel_offre", "montant_global_appel_offre", "description_critere_critere_appel_offre"},
     *             @OA\Property(
     *                 property="type_appel_offre_id",
     *                 type="string",
     *                 format="uuid",
     *                 description="UUID du type d'appel d'offres",
     *                 example="550e8400-e29b-41d4-a716-446655440000"
     *             ),
     *             @OA\Property(
     *                 property="libelle_critere_appel_offre",
     *                 type="string",
     *                 maxLength=160,
     *                 description="Libellé/titre de l'appel d'offres",
     *                 example="Construction d'un centre de santé communautaire"
     *             ),
     *             @OA\Property(
     *                 property="objet_critere_appel_offre",
     *                 type="string",
     *                 nullable=true,
     *                 description="Objet détaillé de l'appel d'offres",
     *                 example="Travaux de construction d'un centre de santé de type CSU dans la commune de Yamoussoukro"
     *             ),
     *             @OA\Property(
     *                 property="montant_global_appel_offre",
     *                 type="number",
     *                 format="decimal",
     *                 minimum=5,
     *                 description="Montant total estimé (FCFA). Doit être dans l'intervalle du type.",
     *                 example=150000000
     *             ),
     *             @OA\Property(
     *                 property="description_critere_critere_appel_offre",
     *                 type="string",
     *                 description="Description détaillée des travaux/prestations",
     *                 example="Les travaux comprennent : gros œuvre, second œuvre, VRD, équipements techniques..."
     *             ),
     *             @OA\Property(
     *                 property="date_publication_critere_appel_offre",
     *                 type="string",
     *                 format="date-time",
     *                 nullable=true,
     *                 description="Date de publication (défaut: maintenant)",
     *                 example="2024-02-01T08:00:00Z"
     *             ),
     *             @OA\Property(
     *                 property="conditions_participation_critere_appel_offre",
     *                 type="string",
     *                 nullable=true,
     *                 description="Conditions de participation",
     *                 example="Être inscrit au registre du commerce. Avoir réalisé au moins 3 projets similaires..."
     *             ),
     *             @OA\Property(
     *                 property="criteres_selection_critere_appel_offre",
     *                 type="string",
     *                 nullable=true,
     *                 description="Critères de sélection des offres",
     *                 example="Prix (40%), Qualité technique (35%), Délais (15%), Références (10%)"
     *             ),
     *             @OA\Property(
     *                 property="statut_evaluation_critere_appel_offre",
     *                 type="integer",
     *                 enum={0, 1},
     *                 default=1,
     *                 description="Statut d'évaluation (0=inactif, 1=actif)",
     *                 example=1
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Appel d'offres créé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/AppelOffre"),
     *             @OA\Property(property="message", type="string", example="Appel d'offres créé avec succès")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié",
     *         @OA\JsonContent(ref="#/components/schemas/UnauthenticatedResponse")
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Permission refusée",
     *         @OA\JsonContent(ref="#/components/schemas/ForbiddenResponse")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erreur de validation ou montant hors intervalle",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Erreur de validation"),
     *             @OA\Property(property="errors", type="object")
     *         )
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
        // Validation
        $validator = Validator::make($request->all(), [
            'type_appel_offre_id' => 'required|exists:types_appels_offres,id_type_appel_offre',
            'libelle_critere_appel_offre' => 'required|string|max:160',
            'objet_critere_appel_offre' => 'nullable|string',
            'montant_global_appel_offre' => 'required|numeric|min:5',
            'conditions_participation_critere_appel_offre' => 'nullable|string',
            'criteres_selection_critere_appel_offre' => 'nullable|string',
        ], [
            'type_appel_offre_id.required' => 'Le type d\'appel d\'offres est obligatoire',
            'type_appel_offre_id.exists' => 'Type d\'appel d\'offres invalide',
            'libelle_critere_appel_offre.required' => 'Le libellé est obligatoire',
            'montant_global_appel_offre.required' => 'Le montant global est obligatoire',
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson() || $request->is('api/*')) {
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
            // Vérifier que le montant correspond au type d'AO
            $typeAO = TypeAppelOffre::findOrFail($request->type_appel_offre_id);

            if (!$typeAO->isValeurDansIntervalle($request->montant_global_appel_offre)) {
                throw new Exception(
                    "Le montant {$request->montant_global_appel_offre} ne correspond pas à l'intervalle " .
                        "du type {$typeAO->libelle_type_appel_offre} " .
                        "({$typeAO->valeur_minimuim_type_appel_offre} - {$typeAO->valeur_maximuim_type_appel_offre})"
                );
            }

            // Générer le numéro d'appel d'offres
            $numeroAO = $typeAO->genererNumeroAppelOffre();

            // Créer l'appel d'offres
            $appelOffre = AppelOffre::create([
                'type_appel_offre_id' => $request->type_appel_offre_id,
                'numero_appel_offre' => $numeroAO,
                'libelle_critere_appel_offre' => Str::upper($request->libelle_critere_appel_offre),
                'objet_critere_appel_offre' => $request->objet_critere_appel_offre,
                'montant_global_appel_offre' => $request->montant_global_appel_offre,

                'statut_evaluation_critere_appel_offre' => $request->get('statut_evaluation_critere_appel_offre', 1),
                'conditions_participation_critere_appel_offre' => $request->conditions_participation_critere_appel_offre,
                'criteres_selection_critere_appel_offre' => $request->criteres_selection_critere_appel_offre,
                'created_by' => auth()->id(),
            ]);

            DB::commit();

            Log::info("Appel d'offres créé avec succès", [
                'id' => $appelOffre->id_appel_offre,
                'numero' => $numeroAO
            ]);

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => $appelOffre->load(['typeAppelOffre', 'creator']),
                    'message' => 'Appel d\'offres créé avec succès'
                ], 201);
            }

            return redirect()->route('appels-offres.show', $appelOffre->id_appel_offre)
                ->with('success', "Appel d'offres créé avec succès. Numéro: {$numeroAO}");
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la création de l\'AO: ' . $e->getMessage());

            if ($request->wantsJson() || $request->is('api/*')) {
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
     * Affiche les détails d'un appel d'offres
     *
     * @OA\Get(
     *     path="/appels-offres/{id}",
     *     operationId="showAppelOffre",
     *     tags={"Appels d'Offres"},
     *     summary="Détails d'un appel d'offres",
     *     description="Récupère les détails complets d'un appel d'offres avec ses caractéristiques, lots et statistiques. Requiert la permission `appels_offres.view-details`.",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="UUID de l'appel d'offres",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Détails récupérés avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/AppelOffreDetailed"),
     *             @OA\Property(
     *                 property="statistiques",
     *                 type="object",
     *                 @OA\Property(property="est_actif", type="boolean", example=true),
     *                 @OA\Property(property="est_en_cours", type="boolean", example=true),
     *                 @OA\Property(property="est_cloture", type="boolean", example=false)
     *             ),
     *             @OA\Property(property="message", type="string", example="Détails récupérés avec succès")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Appel d'offres introuvable",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function show(Request $request, $id)
    {

        try {
            $appelOffre = AppelOffre::with([
                'typeAppelOffre',
                'caracteristiques' => function ($q) {
                    $q->latest('version_caracteristique_appel_offre');
                },
                'caracteristiqueActive',
                'lots' => function ($q) {

                    $q->versionActuelle()->with('attributionActive.prestataire');
                },
                'creator',
                'updater',
                'deleter'
            ])->withCount('lots')
                ->find($id);




            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => $appelOffre,
                    'statistiques' => [
                        'est_actif' => $appelOffre->isActif(),
                        'est_en_cours' => $appelOffre->etat_appel_offre == 1,
                        'est_cloture' => $appelOffre->etat_appel_offre == 3,
                    ],
                    'message' => 'Détails récupérés avec succès'
                ]);
            }


            return view('appels-offres.show', compact('appelOffre'));
        } catch (Exception $e) {
            Log::error('Erreur lors de la récupération de l\'AO: ' . $e->getMessage());


            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Appel d\'offres introuvable',
                    'error' => $e->getMessage()
                ], 404);
            }

            return back()->with('error', 'Appel d\'offres introuvable');
        }
    }

    /**
     * Affiche le formulaire de modification
     */
    public function edit($id)
    {
        try {
            $appelOffre = AppelOffre::with('typeAppelOffre')->findOrFail($id);
            $typesAO = TypeAppelOffre::actif()->get();

            return view('appels-offres.edit', compact('appelOffre', 'typesAO'));
        } catch (Exception $e) {
            return back()->with('error', 'Appel d\'offres introuvable');
        }
    }



    /**
     * Met à jour un appel d'offres
     *
     * @OA\Put(
     *     path="/appels-offres/{id}",
     *     operationId="updateAppelOffre",
     *     tags={"Appels d'Offres"},
     *     summary="Mettre à jour un appel d'offres",
     *     description="Met à jour les informations d'un appel d'offres. Le montant doit toujours correspondre au type. Requiert la permission `appels_offres.update`.",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="UUID de l'appel d'offres",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"libelle_critere_appel_offre", "objet_critere_appel_offre", "montant_global_appel_offre", "description_critere_critere_appel_offre", "statut_evaluation_critere_appel_offre"},
     *             @OA\Property(property="libelle_critere_appel_offre", type="string", maxLength=160),
     *             @OA\Property(property="objet_critere_appel_offre", type="string"),
     *             @OA\Property(property="montant_global_appel_offre", type="number", format="decimal", minimum=0),
     *             @OA\Property(property="description_critere_critere_appel_offre", type="string"),
     *             @OA\Property(property="date_publication_critere_appel_offre", type="string", format="date-time", nullable=true),
     *             @OA\Property(property="conditions_participation_critere_appel_offre", type="string", nullable=true),
     *             @OA\Property(property="criteres_selection_critere_appel_offre", type="string", nullable=true),
     *             @OA\Property(property="statut_evaluation_critere_appel_offre", type="integer", enum={0, 1})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Appel d'offres mis à jour avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/AppelOffre"),
     *             @OA\Property(property="message", type="string", example="Appel d'offres mis à jour avec succès")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Appel d'offres introuvable"),
     *     @OA\Response(response=422, description="Erreur de validation"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function update(Request $request, $id)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'libelle_critere_appel_offre' => 'required|string|max:160',
            'objet_critere_appel_offre' => 'nullable|string',
            'montant_global_appel_offre' => 'required|numeric|min:0',
            'conditions_participation_critere_appel_offre' => 'nullable|string',
            'criteres_selection_critere_appel_offre' => 'nullable|string',
            'statut_evaluation_critere_appel_offre' => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson() || $request->is('api/*')) {
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
            $appelOffre = AppelOffre::with('typeAppelOffre')->findOrFail($id);

            // Vérifier que le montant correspond toujours au type
            if (!$appelOffre->typeAppelOffre->isValeurDansIntervalle($request->montant_global_appel_offre)) {
                throw new Exception(
                    "Le montant ne correspond pas au type d'appel d'offres sélectionné"
                );
            }

            $appelOffre->update([
                'libelle_critere_appel_offre' => Str::upper($request->libelle_critere_appel_offre),
                'objet_critere_appel_offre' => $request->objet_critere_appel_offre,
                'montant_global_appel_offre' => $request->montant_global_appel_offre,
                'statut_evaluation_critere_appel_offre' => $request->statut_evaluation_critere_appel_offre,
                'conditions_participation_critere_appel_offre' => $request->conditions_participation_critere_appel_offre,
                'criteres_selection_critere_appel_offre' => $request->criteres_selection_critere_appel_offre,
                'updated_by' => auth()->id(),
            ]);

            DB::commit();

            Log::info("Appel d'offres mis à jour", ['id' => $appelOffre->id_appel_offre]);

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => $appelOffre->load(['typeAppelOffre', 'updater']),
                    'message' => 'Appel d\'offres mis à jour avec succès'
                ]);
            }

            return redirect()->route('appels-offres.show', $appelOffre->id_appel_offre)
                ->with('success', 'Appel d\'offres mis à jour avec succès');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la mise à jour de l\'AO: ' . $e->getMessage());

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la mise à jour',
                    'error' => $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Erreur: ' . $e->getMessage())->withInput();
        }
    }




    /**
     * Supprime un appel d'offres
     *
     * @OA\Delete(
     *     path="/appels-offres/{id}",
     *     operationId="deleteAppelOffre",
     *     tags={"Appels d'Offres"},
     *     summary="Supprimer un appel d'offres",
     *     description="Supprime (soft delete) un appel d'offres. Impossible si l'AO contient des lots. Requiert la permission `appels_offres.delete`.",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="UUID de l'appel d'offres",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Appel d'offres supprimé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Appel d'offres supprimé avec succès")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Suppression impossible - AO contient des lots",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Impossible de supprimer cet appel d'offres. Il contient 5 lot(s).")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Appel d'offres introuvable"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $appelOffre = AppelOffre::withCount('lots')->findOrFail($id);

            // Vérifier si l'AO a des lots
            if ($appelOffre->lots_count > 0) {
                if ($request->wantsJson() || $request->is('api/*')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Impossible de supprimer cet appel d\'offres. Il contient ' .
                            $appelOffre->lots_count . ' lot(s).'
                    ], 422);
                }

                return back()->with('error', 'Impossible de supprimer. L\'AO contient des lots.');
            }

            $appelOffre->deleted_by = auth()->id();
            $appelOffre->save();
            $appelOffre->delete();

            DB::commit();

            Log::info("Appel d'offres supprimé", ['id' => $id]);

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'message' => 'Appel d\'offres supprimé avec succès'
                ]);
            }

            return redirect()->route('appels-offres.index')
                ->with('success', 'Appel d\'offres supprimé avec succès');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la suppression de l\'AO: ' . $e->getMessage());

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la suppression',
                    'error' => $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Erreur lors de la suppression');
        }
    }




    /**
     * Active/Désactive un appel d'offres
     *
     * @OA\Post(
     *     path="/appels-offres/{id}/toggle-status",
     *     operationId="toggleStatusAppelOffre",
     *     tags={"Appels d'Offres"},
     *     summary="Activer/Désactiver un appel d'offres",
     *     description="Inverse le statut d'évaluation (actif/inactif) d'un appel d'offres. Requiert la permission `appels_offres.toggle-status`.",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="UUID de l'appel d'offres",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Statut modifié avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/AppelOffre"),
     *             @OA\Property(property="message", type="string", example="Appel d'offres activé avec succès")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Appel d'offres introuvable"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function toggleStatus(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $appelOffre = AppelOffre::findOrFail($id);

            $appelOffre->statut_evaluation_critere_appel_offre =
                $appelOffre->statut_evaluation_critere_appel_offre == 1 ? 0 : 1;
            $appelOffre->updated_by = auth()->id();
            $appelOffre->save();

            DB::commit();

            $statut = $appelOffre->statut_evaluation_critere_appel_offre == 1 ? 'activé' : 'désactivé';

            Log::info("Appel d'offres {$statut}", ['id' => $id]);

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => $appelOffre,
                    'message' => "Appel d'offres {$statut} avec succès"
                ]);
            }

            return back()->with('success', "Appel d'offres {$statut} avec succès");
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors du changement de statut: ' . $e->getMessage());

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors du changement de statut',
                    'error' => $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Erreur lors du changement de statut');
        }
    }



    /**
     * Publie un appel d'offres
     *
     * @OA\Post(
     *     path="/appels-offres/{id}/publier",
     *     operationId="publierAppelOffre",
     *     tags={"Appels d'Offres"},
     *     summary="Publier un appel d'offres",
     *     description="Publie un appel d'offres en définissant sa date de publication et en l'activant. Requiert la permission `appels_offres.update`.",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="UUID de l'appel d'offres",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Appel d'offres publié avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/AppelOffre"),
     *             @OA\Property(property="message", type="string", example="Appel d'offres publié avec succès")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Déjà publié",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Cet appel d'offres est déjà publié")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Appel d'offres introuvable"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function publier(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $appelOffre = AppelOffre::findOrFail($id);



            $appelOffre->statut_evaluation_critere_appel_offre = 1;
            $appelOffre->updated_by = auth()->id();
            $appelOffre->save();

            DB::commit();

            Log::info("Appel d'offres publié", ['id' => $id]);

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => $appelOffre,
                    'message' => 'Appel d\'offres publié avec succès'
                ]);
            }

            return back()->with('success', 'Appel d\'offres publié avec succès');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la publication: ' . $e->getMessage());

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la publication',
                    'error' => $e->getMessage()
                ], 500);
            }

            return back()->with('error', $e->getMessage());
        }
    }





    /**
     * Marque un appel d'offres comme "Terminé"
     *
     * @OA\Post(
     *     path="/appels-offres/{id}/terminer",
     *     operationId="terminerAppelOffre",
     *     tags={"Appels d'Offres"},
     *     summary="Terminer un appel d'offres",
     *     description="Marque un appel d'offres comme terminé. Tous les lots doivent être attribués et complets. Requiert la permission `appels_offres.update`.",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="UUID de l'appel d'offres",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Appel d'offres terminé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/AppelOffre"),
     *             @OA\Property(property="message", type="string", example="Appel d'offres marqué comme terminé avec succès")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Conditions non remplies",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Cet appel d'offres ne peut pas être marqué comme terminé. Tous les lots doivent être attribués et complets.")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Appel d'offres introuvable")
     * )
     */
    public function terminer(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $appelOffre = AppelOffre::findOrFail($id);



            // Vérifier si tous les lots sont complets (optionnel mais recommandé)
            if (!$appelOffre->peutEtreMarqueTermine()) {
                throw new Exception(
                    "Cet appel d'offres ne peut pas être marqué comme terminé. " .
                        "Tous les lots doivent être attribués et complets (évaluations et paiements terminés)."
                );
            }

            // Marquer comme terminé
            $appelOffre->terminerAppelOffre(auth()->id());

            DB::commit();

            Log::info("Appel d'offres marqué comme terminé", ['id' => $id, 'user' => auth()->id()]);

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => $appelOffre->fresh(),
                    'message' => 'Appel d\'offres marqué comme terminé avec succès'
                ]);
            }

            return back()->with('success', 'Appel d\'offres marqué comme terminé avec succès');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors du marquage comme terminé: ' . $e->getMessage());

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 400);
            }

            return back()->with('error', $e->getMessage());
        }
    }



    /**
     * Réouvre un appel d'offres terminé ou clôturé
     *
     * @OA\Post(
     *     path="/appels-offres/{id}/rouvrir",
     *     operationId="rouvrirAppelOffre",
     *     tags={"Appels d'Offres"},
     *     summary="Réouvrir un appel d'offres",
     *     description="Réouvre un appel d'offres terminé ou clôturé. Remet l'état en mode automatique. Requiert la permission `appels_offres.update`.",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="UUID de l'appel d'offres",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Appel d'offres réouvert avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/AppelOffre"),
     *             @OA\Property(property="message", type="string", example="Appel d'offres réouvert avec succès. Nouvel état : En cours")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Réouverture impossible",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Cet appel d'offres est déjà en état automatique.")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Appel d'offres introuvable")
     * )
     */
    public function rouvrir(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $appelOffre = AppelOffre::findOrFail($id);

            // Vérifier si l'AO est en état manuel
            // if ($appelOffre->isEtatAutomatique()) {
            //     throw new Exception("Cet appel d'offres est déjà en état automatique (En attente ou En cours).");
            // }

            // Réouvrir (recalcule l'état automatique)
            $appelOffre->rouvrirAppelOffre(auth()->id());

            DB::commit();

            Log::info("Appel d'offres réouvert", [
                'id' => $id,
                'user' => auth()->id(),
                'nouvel_etat' => $appelOffre->etat_label
            ]);

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => $appelOffre->fresh(),
                    'message' => 'Appel d\'offres réouvert avec succès. Nouvel état : ' . $appelOffre->etat_label
                ]);
            }

            return back()->with('success', 'Appel d\'offres réouvert avec succès. Nouvel état : ' . $appelOffre->etat_label);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la réouverture: ' . $e->getMessage());

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 400);
            }

            return back()->with('error', $e->getMessage());
        }
    }


    /**
     * Clôture un appel d'offres
     *
     * @OA\Post(
     *     path="/appels-offres/{id}/cloturer",
     *     operationId="cloturerAppelOffre",
     *     tags={"Appels d'Offres"},
     *     summary="Clôturer un appel d'offres",
     *     description="Clôture définitivement un appel d'offres. L'évaluation sur tous les critères doit être terminée et les paiements effectués. Requiert la permission `appels_offres.update`.",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="UUID de l'appel d'offres",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Appel d'offres clôturé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/AppelOffre"),
     *             @OA\Property(property="message", type="string", example="Appel d'offres clôturé avec succès")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Clôture impossible",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Cet appel d'offres ne peut pas être clôturé. L'évaluation sur certains critères n'est pas encore terminée.")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Appel d'offres introuvable")
     * )
     */
    public function cloturer(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $appelOffre = AppelOffre::findOrFail($id);

            // Vérifier si l'AO peut être clôturé (tous les lots complets)
            if (!$appelOffre->peutEtreCloturer()) {
                throw new Exception(
                    "Cet appel d'offres ne peut pas être clôturé. " .
                        "Il s'avère que l'évaluation sur certains critères n'est pas encore terminée " .
                        "ou le paiement de la facture n'est pas encore totalement effectué."
                );
            }

            // Clôturer via la nouvelle méthode
            $appelOffre->cloturerAppelOffre(auth()->id());

            // Garder la compatibilité avec l'ancien système
            $appelOffre->statut_evaluation_critere_appel_offre = 1;
            $appelOffre->save();

            DB::commit();

            Log::info("Appel d'offres clôturé", ['id' => $id, 'user' => auth()->id()]);

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => $appelOffre->fresh(),
                    'message' => 'Appel d\'offres clôturé avec succès'
                ]);
            }

            return back()->with('success', 'Appel d\'offres clôturé avec succès');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la clôture: ' . $e->getMessage());

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 400);
            }

            return back()->with('error', $e->getMessage());
        }
    }



    /**
     * Obtient les statistiques d'un appel d'offres
     *
     * @OA\Get(
     *     path="/appels-offres/{id}/statistiques",
     *     operationId="statistiquesAppelOffre",
     *     tags={"Appels d'Offres"},
     *     summary="Statistiques d'un appel d'offres",
     *     description="Retourne les statistiques détaillées d'un appel d'offres (lots, attributions, montants). Requiert la permission `appels_offres.view-details`.",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="UUID de l'appel d'offres",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Statistiques récupérées avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="general",
     *                     type="object",
     *                     @OA\Property(property="numero", type="string", example="AON-2024-0042"),
     *                     @OA\Property(property="montant_global", type="number", example=150000000),
     *                     @OA\Property(property="est_actif", type="boolean", example=true),
     *                     @OA\Property(property="est_en_cours", type="boolean", example=true),
     *                     @OA\Property(property="est_cloture", type="boolean", example=false)
     *                 ),
     *                 @OA\Property(
     *                     property="lots",
     *                     type="object",
     *                     @OA\Property(property="total", type="integer", example=5),
     *                     @OA\Property(property="attribues", type="integer", example=3),
     *                     @OA\Property(property="non_attribues", type="integer", example=2),
     *                     @OA\Property(property="montant_total", type="number", example=120000000)
     *                 )
     *             ),
     *             @OA\Property(property="message", type="string", example="Statistiques récupérées avec succès")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Appel d'offres introuvable"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function statistiques(Request $request, $id)
    {
        try {
            $appelOffre = AppelOffre::with(['lots', 'lots.attributionActive'])->findOrFail($id);

            $stats = [
                'general' => [
                    'numero' => $appelOffre->numero_appel_offre,
                    'montant_global' => $appelOffre->montant_global_appel_offre,
                    'est_actif' => $appelOffre->isActif(),
                    'est_en_cours' => $appelOffre->etat_appel_offre == 1,
                    'est_cloture' => $appelOffre->etat_appel_offre == 3,
                ],
                'lots' => [
                    'total' => $appelOffre->lots->count(),
                    'attribues' => $appelOffre->lots->where('attribution_lot', 1)->count(),
                    'non_attribues' => $appelOffre->lots->where('attribution_lot', 0)->count(),
                    'montant_total' => $appelOffre->lots->sum('montant_lot'),
                ],
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
     * Duplique un appel d'offres
     *
     * @OA\Post(
     *     path="/appels-offres/{id}/duplicate",
     *     operationId="duplicateAppelOffre",
     *     tags={"Appels d'Offres"},
     *     summary="Dupliquer un appel d'offres",
     *     description="Crée une copie d'un appel d'offres existant avec un nouveau numéro. La copie est en statut brouillon (non publié). Requiert la permission `appels_offres.duplicate`.",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="UUID de l'appel d'offres à dupliquer",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Appel d'offres dupliqué avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/AppelOffre"),
     *             @OA\Property(property="message", type="string", example="Appel d'offres dupliqué avec succès")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Appel d'offres introuvable"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function duplicate(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $appelOffre = AppelOffre::with(['caracteristiques', 'lots'])
                ->findOrFail($id);

            // Générer un nouveau numéro
            $typeAO = $appelOffre->typeAppelOffre;
            $numeroAO = $typeAO->genererNumeroAppelOffre();

            // Créer une copie
            $nouveauAO = $appelOffre->replicate();
            $nouveauAO->numero_appel_offre = $numeroAO;
            $nouveauAO->statut_evaluation_critere_appel_offre = 0;
            $nouveauAO->created_by = auth()->id();
            $nouveauAO->save();

            DB::commit();

            Log::info("Appel d'offres dupliqué", [
                'original' => $id,
                'nouveau' => $nouveauAO->id_appel_offre
            ]);

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => $nouveauAO->load('typeAppelOffre'),
                    'message' => 'Appel d\'offres dupliqué avec succès'
                ], 201);
            }

            return redirect()->route('appels-offres.edit', $nouveauAO->id_appel_offre)
                ->with('success', 'Appel d\'offres dupliqué avec succès');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la duplication: ' . $e->getMessage());

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la duplication',
                    'error' => $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Erreur lors de la duplication');
        }
    }



    /**
     * Liste des lots d'un appel d'offres
     *
     * @OA\Get(
     *     path="/appels-offres/{id}/lot",
     *     operationId="lotsByAppelOffre",
     *     tags={"Appels d'Offres"},
     *     summary="Lots d'un appel d'offres",
     *     description="Récupère la liste des lots associés à un appel d'offres. Requiert la permission `lots.read`.",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="UUID de l'appel d'offres",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         description="Nombre d'éléments par page",
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Liste des lots récupérée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="appel_offre", ref="#/components/schemas/AppelOffreSummary"),
     *                 @OA\Property(
     *                     property="lots",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/Lot")
     *                 ),
     *                 @OA\Property(property="pagination", ref="#/components/schemas/PaginationMeta")
     *             ),
     *             @OA\Property(property="message", type="string", example="Lots récupérés avec succès")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Appel d'offres introuvable")
     * )
     */
    public function lotsByOffre(Request $request, $id)
    {
        // À implémenter
    }



    public function showLotByOffre(Request $request, $id, $slug)
    {
        // À implémenter
    }

}
