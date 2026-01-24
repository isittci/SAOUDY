<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\AppelOffre;
use Illuminate\Http\Request;
use App\Models\TypeAppelOffre;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;


/**
 * @OA\Tag(
 *     name="Types d'Appels d'Offres",
 *     description="Gestion des types d'appels d'offres (catégories selon les montants)"
 * )
 */
class TypeAppelOffreController extends Controller
{
    /**
     * Affiche la liste des types d'appels d'offres
     *
     * @OA\Get(
     *     path="/types-appels-offres",
     *     operationId="getTypesAppelsOffres",
     *     tags={"Types d'Appels d'Offres"},
     *     summary="Liste des types d'appels d'offres",
     *     description="Récupère la liste paginée des types d'appels d'offres avec filtres et tri. Requiert la permission `type_appels_offres.read`.",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="actif",
     *         in="query",
     *         required=false,
     *         description="Filtrer par statut actif (true/false)",
     *         @OA\Schema(type="boolean", example=true)
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         required=false,
     *         description="Recherche dans le libellé et le code",
     *         @OA\Schema(type="string", example="Appel ouvert")
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         required=false,
     *         description="Champ de tri",
     *         @OA\Schema(
     *             type="string",
     *             enum={"created_at", "libelle_type_appel_offre", "code_type_appel_offre", "valeur_minimuim_type_appel_offre"},
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
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="Numéro de la page",
     *         @OA\Schema(type="integer", minimum=1, default=1)
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
     *                     @OA\Items(ref="#/components/schemas/TypeAppelOffre")
     *                 ),
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=5),
     *                 @OA\Property(property="per_page", type="integer", example=15),
     *                 @OA\Property(property="total", type="integer", example=73)
     *             ),
     *             @OA\Property(property="message", type="string", example="Liste des types d'appels d'offres récupérée avec succès")
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
            $query = TypeAppelOffre::with(['creator', 'updater'])
                ->with(['parent'])
                ->withCount('appelOffres')
                /*->where('actif_type_appel_offre', true)*/?->versionActive();

            // Filtres
            if ($request->filled('actif')) {
                $query->where('actif_type_appel_offre', $request->actif);
            }



            if ($request->filled('search')) {
                $search = strtolower($request->search);
                $query->where(function ($q) use ($search) {
                    $q->whereRaw('LOWER(libelle_type_appel_offre) LIKE ?', ["%{$search}%"])
                        ->orWhereRaw('LOWER(code_type_appel_offre) LIKE ?', ["%{$search}%"]);
                 });
            }

            // Tri
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 15);
            $typesAO = $query->paginate($perPage);

            // Retour selon le type de requête
            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => $typesAO,
                    'message' => 'Liste des types d\'appels d\'offres récupérée avec succès'
                ]);
            }

            return view('types-appels-offres.index', compact('typesAO'));
        } catch (Exception $e) {
            Log::error('Erreur lors de la récupération des types AO: ' . $e->getMessage());

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
     * Enregistre un nouveau type d'appel d'offres
     *
     * @OA\Post(
     *     path="/types-appels-offres",
     *     operationId="createTypeAppelOffre",
     *     tags={"Types d'Appels d'Offres"},
     *     summary="Créer un type d'appel d'offres",
     *     description="Crée un nouveau type d'appel d'offres avec ses plages de valeurs. Requiert la permission `type_appels_offres.create`.",
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Données du type d'appel d'offres",
     *         @OA\JsonContent(
     *             required={"libelle_type_appel_offre", "valeur_minimuim_type_appel_offre", "valeur_maximuim_type_appel_offre"},
     *             @OA\Property(
     *                 property="libelle_type_appel_offre",
     *                 type="string",
     *                 maxLength=160,
     *                 description="Libellé du type d'appel d'offres",
     *                 example="Appel d'Offres Ouvert National"
     *             ),
     *             @OA\Property(
     *                 property="valeur_minimuim_type_appel_offre",
     *                 type="number",
     *                 format="decimal",
     *                 minimum=0,
     *                 description="Valeur minimale du marché (en FCFA)",
     *                 example=50000000
     *             ),
     *             @OA\Property(
     *                 property="valeur_maximuim_type_appel_offre",
     *                 type="number",
     *                 format="decimal",
     *                 description="Valeur maximale du marché (en FCFA). Doit être supérieure à la valeur minimale.",
     *                 example=500000000
     *             ),
     *             @OA\Property(
     *                 property="description_critere_type_appel_offre",
     *                 type="string",
     *                 nullable=true,
     *                 description="Description détaillée du type et de ses critères",
     *                 example="Appel d'offres ouvert à toutes les entreprises nationales pour les marchés de travaux."
     *             ),
     *             @OA\Property(
     *                 property="actif_type_appel_offre",
     *                 type="boolean",
     *                 default=true,
     *                 description="Statut actif/inactif",
     *                 example=true
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Type créé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/TypeAppelOffre"),
     *             @OA\Property(property="message", type="string", example="Type d'appel d'offres créé avec succès")
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
     *         description="Erreur de validation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Erreur de validation"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="libelle_type_appel_offre",
     *                     type="array",
     *                     @OA\Items(type="string", example="Le libellé est obligatoire")
     *                 ),
     *                 @OA\Property(
     *                     property="valeur_maximuim_type_appel_offre",
     *                     type="array",
     *                     @OA\Items(type="string", example="La valeur maximale doit être supérieure à la valeur minimale")
     *                 )
     *             )
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
            'libelle_type_appel_offre' => 'required|string|max:160',
            'valeur_minimuim_type_appel_offre' => 'required|numeric|min:0',
            'valeur_maximuim_type_appel_offre' => 'required|numeric|gt:valeur_minimuim_type_appel_offre',
            'description_critere_type_appel_offre' => 'nullable|string',
            'actif_type_appel_offre' => 'boolean',
        ], [
            'libelle_type_appel_offre.required' => 'Le libellé est obligatoire',
            'valeur_minimuim_type_appel_offre.required' => 'La valeur minimale est obligatoire',
            'valeur_maximuim_type_appel_offre.required' => 'La valeur maximale est obligatoire',
            'valeur_maximuim_type_appel_offre.gt' => 'La valeur maximale doit être supérieure à la valeur minimale',
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
            $typeAO = TypeAppelOffre::create([
                'libelle_type_appel_offre' => $request->libelle_type_appel_offre,
                'valeur_minimuim_type_appel_offre' => $request->valeur_minimuim_type_appel_offre,
                'valeur_maximuim_type_appel_offre' => $request->valeur_maximuim_type_appel_offre,
                'description_critere_type_appel_offre' => $request->description_critere_type_appel_offre,
                'actif_type_appel_offre' => $request->get('actif_type_appel_offre', true),
                'created_by' => auth()->id(),
            ]);

            DB::commit();

            Log::info("Type d'AO créé avec succès", ['id' => $typeAO->id_type_appel_offre]);

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => $typeAO->load('creator'),
                    'message' => 'Type d\'appel d\'offres créé avec succès'
                ], 201);
            }

            return redirect()->route('types-appels-offres.show', $typeAO->id_type_appel_offre)
                ->with('success', 'Type d\'appel d\'offres créé avec succès');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la création du type AO: ' . $e->getMessage());

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la création',
                    'error' => $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Erreur lors de la création: ' . $e->getMessage())->withInput();
        }
    }



    /**
     * Affiche les détails d'un type d'appel d'offres
     *
     * @OA\Get(
     *     path="/types-appels-offres/{id}",
     *     operationId="showTypeAppelOffre",
     *     tags={"Types d'Appels d'Offres"},
     *     summary="Détails d'un type d'appel d'offres",
     *     description="Récupère les détails complets d'un type d'appel d'offres avec ses 10 derniers appels d'offres associés. Requiert la permission `type_appels_offres.view-details`.",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="UUID du type d'appel d'offres",
     *         @OA\Schema(type="string", format="uuid", example="550e8400-e29b-41d4-a716-446655440000")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Détails récupérés avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/TypeAppelOffreDetailed"),
     *             @OA\Property(property="message", type="string", example="Détails récupérés avec succès")
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
     *         response=404,
     *         description="Type introuvable",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Type d'appel d'offres introuvable"),
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    public function show(Request $request, $id)
    {
        try {
            $typeAO = TypeAppelOffre::with([
                'appelOffres' => function ($q) {
                    $q->latest()->limit(10);
                },
                'creator',
                'updater',
                'deleter'
            ])->withCount('appelOffres')
                ->findOrFail($id);

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => $typeAO,
                    'message' => 'Détails récupérés avec succès'
                ]);
            }

            return view('types-appels-offres.show', compact('typeAO'));
        } catch (Exception $e) {
            Log::error('Erreur lors de la récupération du type AO: ' . $e->getMessage());

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Type d\'appel d\'offres introuvable',
                    'error' => $e->getMessage()
                ], 404);
            }

            return back()->with('error', 'Type d\'appel d\'offres introuvable');
        }
    }


    /**
     *
     * Récupérer les appels d'offres d'un type spécifique
     * @param Request $request
     * @param mixed $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     *
     *
     * @OA\Get(
     *     path="/types-appels-offres/{id}/appels-offres",
     *     operationId="fetchAOByTAO",
     *     tags={"Types d'Appels d'Offres"},
     *     summary="Appels d'offres par type",
     *     description="Récupère la liste paginée des appels d'offres associés à un type spécifique. Requiert la permission `appels_offres.read`.",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="UUID du type d'appel d'offres",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         description="Nombre d'éléments par page",
     *         @OA\Schema(type="integer", default=10, minimum=1, maximum=100)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Liste récupérée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="type_appel_offre", ref="#/components/schemas/TypeAppelOffre"),
     *                 @OA\Property(
     *                     property="appels_offres",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/AppelOffreSummary")
     *                 ),
     *                 @OA\Property(
     *                     property="pagination",
     *                     type="object",
     *                     @OA\Property(property="current_page", type="integer", example=1),
     *                     @OA\Property(property="last_page", type="integer", example=3),
     *                     @OA\Property(property="per_page", type="integer", example=10),
     *                     @OA\Property(property="total", type="integer", example=25),
     *                     @OA\Property(property="from", type="integer", example=1),
     *                     @OA\Property(property="to", type="integer", example=10)
     *                 )
     *             ),
     *             @OA\Property(property="message", type="string", example="Détails récupérés avec succès")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Type introuvable",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
public function fetchAOByTAO(Request $request, $id)
{
    try {
        // Récupérer le type d'appel d'offres
        $typeAO = TypeAppelOffre::with(['creator', 'updater', 'deleter'])
            ->withCount('appelOffres')
            ->findOrFail($id);

        // Récupérer les appels d'offres avec pagination
        $perPage = $request->input('per_page', 10);
        $appelOffres = AppelOffre::where('type_appel_offre_id', $id)
            ->with(['creator', 'updater']) // Ajoute les relations nécessaires
            ->latest()
            ->paginate($perPage);

        if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'data' => [
                    'type_appel_offre' => $typeAO,
                    'appels_offres' => $appelOffres->items(),
                    'pagination' => [
                        'current_page' => $appelOffres->currentPage(),
                        'last_page' => $appelOffres->lastPage(),
                        'per_page' => $appelOffres->perPage(),
                        'total' => $appelOffres->total(),
                        'from' => $appelOffres->firstItem(),
                        'to' => $appelOffres->lastItem(),
                    ]
                ],
                'message' => 'Détails récupérés avec succès'
            ]);
        }

        return view('types-appels-offres.liste-appel-offre', compact('typeAO', 'appelOffres'));

    } catch (Exception $e) {
        Log::error('Erreur lors de la récupération du type AO: ' . $e->getMessage());

        if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => false,
                'message' => 'Type d\'appel d\'offres introuvable',
                'error' => $e->getMessage()
            ], 404);
        }

        return back()->with('error', 'Type d\'appel d\'offres introuvable');
    }
}

    /**
     * Affiche le formulaire de modification
     */
    public function edit($id)
    {
        try {
            $typeAO = TypeAppelOffre::findOrFail($id);
            return view('types-appels-offres.edit', compact('typeAO'));
        } catch (Exception $e) {
            return back()->with('error', 'Type d\'appel d\'offres introuvable');
        }
    }



    /**
     * Met à jour un type d'appel d'offres
     *
     * @OA\Put(
     *     path="/types-appels-offres/{id}",
     *     operationId="updateTypeAppelOffre",
     *     tags={"Types d'Appels d'Offres"},
     *     summary="Mettre à jour un type d'appel d'offres",
     *     description="Met à jour un type d'appel d'offres. Si les valeurs min/max changent, une nouvelle version est créée (versioning). Requiert la permission `type_appels_offres.update`.",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="UUID du type d'appel d'offres",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"libelle_type_appel_offre", "valeur_minimuim_type_appel_offre", "valeur_maximuim_type_appel_offre"},
     *             @OA\Property(
     *                 property="libelle_type_appel_offre",
     *                 type="string",
     *                 maxLength=160,
     *                 example="Appel d'Offres Ouvert International"
     *             ),
     *             @OA\Property(
     *                 property="valeur_minimuim_type_appel_offre",
     *                 type="number",
     *                 format="decimal",
     *                 minimum=0,
     *                 example=100000000
     *             ),
     *             @OA\Property(
     *                 property="valeur_maximuim_type_appel_offre",
     *                 type="number",
     *                 format="decimal",
     *                 example=1000000000
     *             ),
     *             @OA\Property(
     *                 property="description_critere_type_appel_offre",
     *                 type="string",
     *                 nullable=true,
     *                 example="Appel d'offres ouvert aux entreprises internationales."
     *             ),
     *             @OA\Property(
     *                 property="motif_modification_type_appel_offre",
     *                 type="string",
     *                 maxLength=255,
     *                 nullable=true,
     *                 description="Obligatoire si les valeurs min/max changent",
     *                 example="Ajustement des seuils suite à la réforme des marchés publics 2024"
     *             ),
     *             @OA\Property(
     *                 property="actif_type_appel_offre",
     *                 type="boolean",
     *                 example=true
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Type mis à jour avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/TypeAppelOffre"),
     *             @OA\Property(property="message", type="string", example="Type d'appel d'offres mis à jour avec succès")
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
     *         response=404,
     *         description="Type introuvable",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
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
    public function update(Request $request, $id)
    {
        $typeAO = TypeAppelOffre::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'libelle_type_appel_offre' => 'required|string|max:160',
            'valeur_minimuim_type_appel_offre' => 'required|numeric|min:0',
            'valeur_maximuim_type_appel_offre' => 'required|numeric|gt:valeur_minimuim_type_appel_offre',
            'description_critere_type_appel_offre' => 'nullable|string',
            'motif_modification_type_appel_offre' => 'nullable|string',
            'actif_type_appel_offre' => 'boolean',
        ]);

        // Rendre motif obligatoire seulement si les valeurs changent
        $validator->sometimes('motif_modification_type_appel_offre', 'required|string', function ($input) use ($typeAO) {
            return $input->valeur_minimuim_type_appel_offre != $typeAO->valeur_minimuim_type_appel_offre
                || $input->valeur_maximuim_type_appel_offre != $typeAO->valeur_maximuim_type_appel_offre;
        });

        $validator->validate();


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
            $validator = $validator->validate();


            // // Vérifier si le type est utilisé et si on change les valeurs
            // if ($typeAO->appel_offres_count > 0) {
            //     $changementValeurs =
            //         $typeAO->valeur_minimuim_type_appel_offre != $request->valeur_minimuim_type_appel_offre ||
            //         $typeAO->valeur_maximuim_type_appel_offre != $request->valeur_maximuim_type_appel_offre;

            //     if ($changementValeurs) {
            //         if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
            //             return response()->json([
            //                 'success' => false,
            //                 'message' => 'Impossible de modifier les valeurs. Ce type est déjà utilisé dans des appels d\'offres.'
            //             ], 422);
            //         }

            //         return back()->with('error', 'Impossible de modifier les valeurs. Ce type est déjà utilisé.');
            //     }
            // }

            $updateName = $validator['libelle_type_appel_offre'] == $typeAO->libelle_type_appel_offre;
            $updateMixValue = $validator['valeur_minimuim_type_appel_offre'] == $typeAO->valeur_minimuim_type_appel_offre;
            $updateMaxValue = $validator['valeur_maximuim_type_appel_offre'] == $typeAO->valeur_maximuim_type_appel_offre;
            // $updateDescription = $request->description_critere_type_appel_offre == $typeAO->description_critere_type_appel_offre;


            if ($updateName && $updateMixValue && $updateMaxValue) {
                $typeAO->update([
                    'libelle_type_appel_offre' => $validator['libelle_type_appel_offre'],
                    // 'code_type_appel_offre' => strtoupper($request->code_type_appel_offre),
                    'valeur_minimuim_type_appel_offre' => $typeAO->valeur_minimuim_type_appel_offre,
                    'valeur_maximuim_type_appel_offre' => $typeAO->valeur_maximuim_type_appel_offre,
                    'description_critere_type_appel_offre' => $request->description_critere_type_appel_offre,
                    'actif_type_appel_offre' => $request->get('actif_type_appel_offre', $typeAO->actif_type_appel_offre),

                    'updated_by' => auth()->id(),
                ]);
            } else {
                // Créer une nouvelle version - SANS duree_estimee_jours (calculée automatiquement)
                $typeAO = $typeAO->creerNouvelleVersion([
                    'libelle_type_appel_offre' => $request->libelle_type_appel_offre,
                    'valeur_minimuim_type_appel_offre' => $validator['valeur_minimuim_type_appel_offre'],
                    'valeur_maximuim_type_appel_offre' => $validator['valeur_maximuim_type_appel_offre'],
                    'description_critere_type_appel_offre' => $validator['description_critere_type_appel_offre'],

                    'actif_type_appel_offre' => $request->get('actif_type_appel_offre', $typeAO->actif_type_appel_offre),
                    'updated_by' => auth()->id(),
                    'created_by' => auth()->id(),
                ], $validator['motif_modification_type_appel_offre']);
                // La durée sera calculée automatiquement par le modèle
            }

            DB::commit();

            Log::info("Type d'AO mis à jour", ['id' => $typeAO->id_type_appel_offre]);

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => $typeAO->load('updater'),
                    'message' => 'Type d\'appel d\'offres mis à jour avec succès'
                ]);
            }

            return redirect()->route('types-appels-offres.show', $typeAO->id_type_appel_offre)
                ->with('success', 'Type d\'appel d\'offres mis à jour avec succès');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la mise à jour du type AO: ' . $e->getMessage());

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la mise à jour',
                    'error' => $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Erreur lors de la mise à jour')->withInput();
        }
    }



    /**
     * Supprime un type d'appel d'offres
     *
     * @OA\Delete(
     *     path="/types-appels-offres/{id}",
     *     operationId="deleteTypeAppelOffre",
     *     tags={"Types d'Appels d'Offres"},
     *     summary="Supprimer un type d'appel d'offres",
     *     description="Supprime (soft delete) un type d'appel d'offres. Impossible si le type est utilisé dans des appels d'offres. Requiert la permission `type_appels_offres.delete`.",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="UUID du type d'appel d'offres",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Type supprimé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Type d'appel d'offres supprimé avec succès")
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
     *         response=404,
     *         description="Type introuvable",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Impossible de supprimer - type utilisé",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Impossible de supprimer ce type. Il est utilisé dans 5 appel(s) d'offres.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur serveur",
     *         @OA\JsonContent(ref="#/components/schemas/ServerErrorResponse")
     *     )
     * )
     */
    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $typeAO = TypeAppelOffre::withCount('appelOffres')->findOrFail($id);

            // Vérifier si le type est utilisé
            if ($typeAO->appel_offres_count > 0) {
                if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Impossible de supprimer ce type. Il est utilisé dans ' .
                            $typeAO->appel_offres_count . ' appel(s) d\'offres.'
                    ], 422);
                }

                return back()->with('error', 'Impossible de supprimer ce type. Il est utilisé.');
            }

            $typeAO->deleted_by = auth()->id();
            $typeAO->save();
            $typeAO->delete();

            DB::commit();

            Log::info("Type d'AO supprimé", ['id' => $id]);

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'message' => 'Type d\'appel d\'offres supprimé avec succès'
                ]);
            }

            return redirect()->route('types-appels-offres.index')
                ->with('success', 'Type d\'appel d\'offres supprimé avec succès');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la suppression du type AO: ' . $e->getMessage());

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
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
     * Active/Désactive un type d'appel d'offres
     *
     * @OA\Post(
     *     path="/types-appels-offres/{id}/toggle-status",
     *     operationId="toggleStatusTypeAppelOffre",
     *     tags={"Types d'Appels d'Offres"},
     *     summary="Activer/Désactiver un type",
     *     description="Inverse le statut actif/inactif d'un type d'appel d'offres. Requiert la permission `type_appels_offres.toggle-status`.",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="UUID du type d'appel d'offres",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Statut modifié avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/TypeAppelOffre"),
     *             @OA\Property(property="message", type="string", example="Type d'appel d'offres activé avec succès")
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
     *         response=404,
     *         description="Type introuvable",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur serveur",
     *         @OA\JsonContent(ref="#/components/schemas/ServerErrorResponse")
     *     )
     * )
     */
    public function toggleStatus(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $typeAO = TypeAppelOffre::findOrFail($id);

            $typeAO->actif_type_appel_offre = !$typeAO->actif_type_appel_offre;
            $typeAO->updated_by = auth()->id();
            $typeAO->save();

            DB::commit();

            $statut = $typeAO->actif_type_appel_offre ? 'activé' : 'désactivé';

            Log::info("Type d'AO {$statut}", ['id' => $id]);

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => $typeAO,
                    'message' => "Type d'appel d'offres {$statut} avec succès"
                ]);
            }

            return back()->with('success', "Type d'appel d'offres {$statut} avec succès");
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors du changement de statut: ' . $e->getMessage());

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
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
     * Vérifie si un montant correspond à un type d'AO
     *
     * @OA\Post(
     *     path="/types-appels-offres/check-montant",
     *     operationId="checkMontantTypeAppelOffre",
     *     tags={"Types d'Appels d'Offres"},
     *     summary="Vérifier le type par montant",
     *     description="Recherche les types d'appels d'offres dont la plage de valeurs inclut le montant donné. Requiert la permission `type_appels_offres.read`.",
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"montant"},
     *             @OA\Property(
     *                 property="montant",
     *                 type="number",
     *                 format="decimal",
     *                 minimum=0,
     *                 description="Montant du marché à vérifier (en FCFA)",
     *                 example=75000000
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Vérification effectuée",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/TypeAppelOffre")
     *             ),
     *             @OA\Property(property="message", type="string", example="Type(s) correspondant(s) trouvé(s)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Montant invalide",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Montant invalide"),
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
    public function checkMontant(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'montant' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Montant invalide',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $montant = $request->montant;
            $types = TypeAppelOffre::actif()
                ->byValeur($montant)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $types,
                'message' => count($types) > 0
                    ? 'Type(s) correspondant(s) trouvé(s)'
                    : 'Aucun type ne correspond à ce montant'
            ]);
        } catch (Exception $e) {
            Log::error('Erreur lors de la vérification du montant: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la vérification',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    /**
     * Génère un numéro d'AO pour un type donné
     *
     * @OA\Get(
     *     path="/types-appels-offres/{id}/generer-numero",
     *     operationId="genererNumeroTypeAppelOffre",
     *     tags={"Types d'Appels d'Offres"},
     *     summary="Générer un numéro d'appel d'offres",
     *     description="Génère un nouveau numéro d'appel d'offres basé sur le code du type et l'année. Requiert la permission `type_appels_offres.create`.",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="UUID du type d'appel d'offres",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Parameter(
     *         name="annee",
     *         in="query",
     *         required=false,
     *         description="Année pour le numéro (défaut: année courante)",
     *         @OA\Schema(type="integer", example=2024)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Numéro généré avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="numero", type="string", example="AON-2024-0042"),
     *                 @OA\Property(property="annee", type="integer", example=2024),
     *                 @OA\Property(property="type", type="string", example="AON")
     *             ),
     *             @OA\Property(property="message", type="string", example="Numéro généré avec succès")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Type introuvable",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur serveur",
     *         @OA\JsonContent(ref="#/components/schemas/ServerErrorResponse")
     *     )
     * )
     */
    public function genererNumero(Request $request, $id)
    {
        try {
            $typeAO = TypeAppelOffre::findOrFail($id);
            $annee = $request->get('annee', date('Y'));

            $numero = $typeAO->genererNumeroAppelOffre($annee);

            return response()->json([
                'success' => true,
                'data' => [
                    'numero' => $numero,
                    'annee' => $annee,
                    'type' => $typeAO->code_type_appel_offre
                ],
                'message' => 'Numéro généré avec succès'
            ]);
        } catch (Exception $e) {
            Log::error('Erreur lors de la génération du numéro: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la génération du numéro',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    /**
     * Exporte les types d'AO (CSV, Excel, PDF)
     *
     * @OA\Get(
     *     path="/types-appels-offres/export/{format}",
     *     operationId="exportTypesAppelsOffres",
     *     tags={"Types d'Appels d'Offres"},
     *     summary="Exporter les types d'appels d'offres",
     *     description="Exporte la liste des types d'appels d'offres dans le format spécifié. Requiert la permission `type_appels_offres.download`.",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="format",
     *         in="path",
     *         required=false,
     *         description="Format d'export",
     *         @OA\Schema(type="string", enum={"csv", "xlsx", "pdf"}, default="csv")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Export réussi ou en cours",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/TypeAppelOffre")
     *             ),
     *             @OA\Property(property="message", type="string", example="Export en cours de développement")
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
    public function export(Request $request)
    {
        try {
            $format = $request->get('format', 'csv');
            $types = TypeAppelOffre::with('creator')->get();

            // Implémentation selon le format souhaité
            // Pour l'instant, retour JSON
            return response()->json([
                'success' => true,
                'data' => $types,
                'message' => 'Export en cours de développement'
            ]);
        } catch (Exception $e) {
            Log::error('Erreur lors de l\'export: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'export',
                'error' => $e->getMessage()
            ], 500);
        }
    }


}
