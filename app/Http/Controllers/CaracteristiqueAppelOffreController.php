<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\AppelOffre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\CaracteristiqueAppelOffre;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * @OA\Tag(
 *     name="Caractéristiques des Appels d'Offres",
 *     description="Gestion des caractéristiques techniques et contractuelles des appels d'offres (dates, garanties, modalités, versioning)"
 * )
 */
class CaracteristiqueAppelOffreController extends Controller
{

    /**
     * Affiche la liste des caractéristiques d'un appel d'offres
     *
     * @OA\Get(
     *     path="/appels-offres/{appel_offre}/caracteristiques",
     *     operationId="getCaracteristiquesAppelOffre",
     *     tags={"Caractéristiques des Appels d'Offres"},
     *     summary="Liste des caractéristiques",
     *     description="Récupère la liste paginée des caractéristiques d'un appel d'offres. Seules les versions actives sont retournées par défaut. Requiert la permission `caracteristiques_appels_offres.read`.",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="appel_offre",
     *         in="path",
     *         required=true,
     *         description="UUID de l'appel d'offres",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         required=false,
     *         description="Recherche dans lieu d'exécution et conditions de paiement",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         required=false,
     *         description="Champ de tri",
     *         @OA\Schema(type="string", default="created_at")
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
     *                 @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/CaracteristiqueAppelOffre")),
     *                 @OA\Property(property="current_page", type="integer"),
     *                 @OA\Property(property="last_page", type="integer"),
     *                 @OA\Property(property="per_page", type="integer"),
     *                 @OA\Property(property="total", type="integer")
     *             ),
     *             @OA\Property(property="appel_offre", ref="#/components/schemas/AppelOffreSummary"),
     *             @OA\Property(property="message", type="string", example="Liste des caractéristiques récupérée avec succès")
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

            $query = CaracteristiqueAppelOffre::where('appel_offre_id', $appelOffreId)
                ->with(['creator', 'updater', 'parent'])
                // ->where('is_active_caracteristique_appel_offre', true) // CORRIGÉ : true au lieu de false
                ->versionActuelle();

            // Recherche
            if ($request->filled('search')) {
                $search = $request->get('search');
                $query->where(function ($q) use ($search) {
                    $q->where('lieu_execution_caracteristique_appel_offre', 'like', "%{$search}%")
                        ->orWhere('conditions_paiement_caracteristique_appel_offre', 'like', "%{$search}%");
                });
            }

            // Tri
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 15);
            $caracteristiques = $query->paginate($perPage);

            // Retour selon le type de requête
            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => $caracteristiques,
                    'appel_offre' => $appelOffre,
                    'message' => 'Liste des caractéristiques récupérée avec succès'
                ]);
            }

            return view('caracteristiques-appels-offres.index', compact('caracteristiques', 'appelOffre'));
        } catch (Exception $e) {
            Log::error('Erreur lors de la récupération des caractéristiques: ' . $e->getMessage());

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
     */
    public function create($appelOffreId)
    {
        try {
            $appelOffre = AppelOffre::findOrFail($appelOffreId);

            return view('caracteristiques-appels-offres.create', compact('appelOffre'));
        } catch (Exception $e) {
            return back()->with('error', 'Appel d\'offres introuvable');
        }
    }



    /**
     * Enregistre une nouvelle caractéristique
     *
     * @OA\Post(
     *     path="/appels-offres/{appel_offre}/caracteristiques",
     *     operationId="createCaracteristiqueAppelOffre",
     *     tags={"Caractéristiques des Appels d'Offres"},
     *     summary="Créer une caractéristique",
     *     description="Crée une nouvelle caractéristique pour un appel d'offres. La durée estimée est calculée automatiquement à partir des dates. Requiert la permission `caracteristiques_appels_offres.create`.",
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
     *             required={"date_demarrage_prevue_caracteristique_appel_offre", "date_livraison_previsionnelle_caracteristique_appel_offre", "lieu_execution_caracteristique_appel_offre"},
     *             @OA\Property(
     *                 property="date_demarrage_prevue_caracteristique_appel_offre",
     *                 type="string",
     *                 description="Date de démarrage prévue (format: dd/mm/yyyy)",
     *                 example="01/03/2024"
     *             ),
     *             @OA\Property(
     *                 property="date_livraison_previsionnelle_caracteristique_appel_offre",
     *                 type="string",
     *                 description="Date de livraison prévisionnelle (format: dd/mm/yyyy, doit être après la date de démarrage)",
     *                 example="30/09/2024"
     *             ),
     *             @OA\Property(
     *                 property="lieu_execution_caracteristique_appel_offre",
     *                 type="string",
     *                 maxLength=255,
     *                 description="Lieu d'exécution des travaux",
     *                 example="Yamoussoukro, Quartier Habitat"
     *             ),
     *             @OA\Property(
     *                 property="montant_garantie_caracteristique_appel_offre",
     *                 type="number",
     *                 nullable=true,
     *                 description="Montant de la caution de bonne exécution (5-10% du marché)",
     *                 example=15000000
     *             ),
     *             @OA\Property(
     *                 property="delai_garantie_jours_caracteristique_appel_offre",
     *                 type="number",
     *                 nullable=true,
     *                 description="Durée de garantie après réception (en jours)",
     *                 example=365
     *             ),
     *             @OA\Property(
     *                 property="conditions_paiement_caracteristique_appel_offre",
     *                 type="string",
     *                 nullable=true,
     *                 description="Modalités de paiement",
     *                 example="30% avance, 40% mi-parcours, 30% à la livraison"
     *             ),
     *             @OA\Property(
     *                 property="modalites_execution_caracteristique_appel_offre",
     *                 type="string",
     *                 nullable=true,
     *                 description="Exigences particulières d'exécution"
     *             ),
     *             @OA\Property(
     *                 property="documents_requis_caracteristique_appel_offre",
     *                 type="string",
     *                 nullable=true,
     *                 description="Liste des documents à fournir",
     *                 example="Attestation fiscale, Assurance décennale, Caution bancaire"
     *             ),
     *             @OA\Property(
     *                 property="autres_informations_caracteristique_appel_offre",
     *                 type="string",
     *                 nullable=true,
     *                 description="Informations complémentaires"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Caractéristique créée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/CaracteristiqueAppelOffreDetailed"),
     *             @OA\Property(property="message", type="string", example="Caractéristique créée avec succès. Durée calculée : 214 jours")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Erreur de validation ou appel d'offres inexistant"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function store(Request $request, $appelOffreId)
    {
        /**
         * @var AppelOffre $appelOffre
         */
        $appelOffre = AppelOffre::find($appelOffreId);



        if(!$appelOffre){
            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => "L'appel d'offre n'existe pas.",
                    'errors' => null
                ], 422);
            }

            return back()->withErrors(["message" => "L'appel d'offre n'existe pas."])->withInput();
        };



        // Validation - SUPPRIMÉ duree_estimee_jours car calculée automatiquement
        $validator = Validator::make($request->all(), [
            'date_demarrage_prevue_caracteristique_appel_offre' => [
                    'required',
                    'date_format:d/m/Y',
                ],
                'date_livraison_previsionnelle_caracteristique_appel_offre' => [
                    'required',
                    'date_format:d/m/Y',
                    'after:date_demarrage_prevue_caracteristique_appel_offre',
                ],
            'lieu_execution_caracteristique_appel_offre' => 'required|string|max:255',
            'montant_garantie_caracteristique_appel_offre' => 'nullable|numeric|min:0',
            'delai_garantie_jours_caracteristique_appel_offre' => 'nullable|numeric|min:0',
            'conditions_paiement_caracteristique_appel_offre' => 'nullable|string',
            'modalites_execution_caracteristique_appel_offre' => 'nullable|string',
            'documents_requis_caracteristique_appel_offre' => 'nullable|string',
            'autres_informations_caracteristique_appel_offre' => 'nullable|string',
        ], [
            'date_demarrage_prevue_caracteristique_appel_offre.date' => 'La date de démarrage doit être une date valide',
            'date_livraison_previsionnelle_caracteristique_appel_offre.date' => 'La date de livraison doit être une date valide',
            'date_livraison_previsionnelle_caracteristique_appel_offre.after' => 'La date de livraison doit être postérieure à la date de démarrage',
            'montant_garantie_caracteristique_appel_offre.numeric' => 'Le montant de garantie doit être numérique',
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
            $appelOffre = AppelOffre::findOrFail($appelOffreId);

            // ✅ AJOUTER CES LIGNES - Convertir les dates du format français vers ISO
            $dateDemarrage = \Carbon\Carbon::createFromFormat('d/m/Y', $request->date_demarrage_prevue_caracteristique_appel_offre)->format('Y-m-d');
            $dateLivraison = \Carbon\Carbon::createFromFormat('d/m/Y', $request->date_livraison_previsionnelle_caracteristique_appel_offre)->format('Y-m-d');

            // Créer la caractéristique
            $caracteristique = CaracteristiqueAppelOffre::create([
                'appel_offre_id' => $appelOffreId,
                'version_caracteristique_appel_offre' => 1,
                'date_demarrage_prevue_caracteristique_appel_offre' => $dateDemarrage,  // ✅ Utiliser la date convertie
                'date_livraison_previsionnelle_caracteristique_appel_offre' => $dateLivraison,  // ✅ Utiliser la date convertie
                'lieu_execution_caracteristique_appel_offre' => $request->lieu_execution_caracteristique_appel_offre,
                'montant_garantie_caracteristique_appel_offre' => $request->montant_garantie_caracteristique_appel_offre,
                'delai_garantie_jours_caracteristique_appel_offre' => $request->delai_garantie_jours_caracteristique_appel_offre,
                'conditions_paiement_caracteristique_appel_offre' => $request->conditions_paiement_caracteristique_appel_offre,
                'modalites_execution_caracteristique_appel_offre' => $request->modalites_execution_caracteristique_appel_offre,
                'documents_requis_caracteristique_appel_offre' => $request->documents_requis_caracteristique_appel_offre,
                'autres_informations_caracteristique_appel_offre' => $request->autres_informations_caracteristique_appel_offre,
                'is_active_caracteristique_appel_offre' => true,
                'created_by' => auth()->id(),
            ]);
            // La durée a été calculée automatiquement par le modèle via boot()

            DB::commit();

            Log::info("Caractéristique créée avec succès", [
                'id' => $caracteristique->id_caracteristique_appel_offre,
                'duree_calculee' => $caracteristique->duree_estimee_jours_caracteristique_appel_offre
            ]);

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => $caracteristique->load(['appelOffre', 'creator']),
                    'message' => 'Caractéristique créée avec succès. Durée calculée : ' . $caracteristique->duree_estimee_formattee
                ], 201);
            }

            return redirect()->route('caracteristiques-appels-offres.show', [
                'appel_offre' => $appelOffreId,
                'caracteristique' => $caracteristique->id_caracteristique_appel_offre
            ])
                ->with('success', 'Caractéristique créée avec succès. Durée calculée : ' . $caracteristique->duree_estimee_formattee);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la création de la caractéristique: ' . $e->getMessage());

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
     * Affiche les détails d'une caractéristique
     *
     * @OA\Get(
     *     path="/appels-offres/{appel_offre}/caracteristiques/{caracteristique}",
     *     operationId="showCaracteristiqueAppelOffre",
     *     tags={"Caractéristiques des Appels d'Offres"},
     *     summary="Détails d'une caractéristique",
     *     description="Récupère les détails d'une caractéristique avec son historique de versions. Requiert la permission `caracteristiques_appels_offres.view-details`.",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="appel_offre",
     *         in="path",
     *         required=true,
     *         description="UUID de l'appel d'offres",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Parameter(
     *         name="caracteristique",
     *         in="path",
     *         required=true,
     *         description="UUID de la caractéristique",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Détails récupérés avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/CaracteristiqueAppelOffreDetailed"),
     *             @OA\Property(property="message", type="string", example="Détails de la caractéristique récupérés avec succès")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Caractéristique introuvable")
     * )
     */
    public function show(Request $request, $appelOffreId, $id)
    {
        try {
            $appelOffre = AppelOffre::findOrFail($appelOffreId);

            $caracteristique = CaracteristiqueAppelOffre::where('appel_offre_id', $appelOffreId)
                ->where('id_caracteristique_appel_offre', $id)
                ->with([
                    'appelOffre.typeAppelOffre',
                    'parent',
                    'versions' => function ($q) {
                        $q->with(['creator', 'updater']);
                    },
                    'creator',
                    'updater'
                ])
                ->firstOrFail();

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => $caracteristique,
                    'message' => 'Détails de la caractéristique récupérés avec succès'
                ]);
            }

            return view('caracteristiques-appels-offres.show', compact('caracteristique', 'appelOffre'));
        } catch (Exception $e) {
            Log::error('Erreur lors de la récupération de la caractéristique: ' . $e->getMessage());

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Caractéristique introuvable',
                    'error' => $e->getMessage()
                ], 404);
            }

            return back()->with('error', 'Caractéristique introuvable');
        }
    }

    /**
     * Affiche le formulaire de modification
     */
    public function edit($appelOffreId, $id)
    {
        try {
            $appelOffre = AppelOffre::findOrFail($appelOffreId);
            $caracteristique = CaracteristiqueAppelOffre::where('appel_offre_id', $appelOffreId)
                ->where('id_caracteristique_appel_offre', $id)
                ->firstOrFail();

            return view('caracteristiques-appels-offres.edit', compact('caracteristique', 'appelOffre'));
        } catch (Exception $e) {
            return back()->with('error', 'Caractéristique introuvable');
        }
    }




    /**
     * Met à jour une caractéristique (crée une nouvelle version)
     *
     * @OA\Put(
     *     path="/appels-offres/{appel_offre}/caracteristiques/{caracteristique}",
     *     operationId="updateCaracteristiqueAppelOffre",
     *     tags={"Caractéristiques des Appels d'Offres"},
     *     summary="Mettre à jour une caractéristique",
     *     description="Met à jour une caractéristique en créant une nouvelle version. L'ancienne version est conservée dans l'historique. Requiert la permission `caracteristiques_appels_offres.update`.",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="appel_offre",
     *         in="path",
     *         required=true,
     *         description="UUID de l'appel d'offres",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Parameter(
     *         name="caracteristique",
     *         in="path",
     *         required=true,
     *         description="UUID de la caractéristique",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"motif_modification_caracteristique_appel_offre"},
     *             @OA\Property(property="date_demarrage_prevue_caracteristique_appel_offre", type="string", format="date", nullable=true),
     *             @OA\Property(property="date_livraison_previsionnelle_caracteristique_appel_offre", type="string", format="date", nullable=true),
     *             @OA\Property(property="lieu_execution_caracteristique_appel_offre", type="string", nullable=true),
     *             @OA\Property(property="montant_garantie_caracteristique_appel_offre", type="number", nullable=true),
     *             @OA\Property(property="delai_garantie_jours_caracteristique_appel_offre", type="number", nullable=true),
     *             @OA\Property(property="conditions_paiement_caracteristique_appel_offre", type="string", nullable=true),
     *             @OA\Property(property="modalites_execution_caracteristique_appel_offre", type="string", nullable=true),
     *             @OA\Property(property="documents_requis_caracteristique_appel_offre", type="string", nullable=true),
     *             @OA\Property(property="autres_informations_caracteristique_appel_offre", type="string", nullable=true),
     *             @OA\Property(
     *                 property="motif_modification_caracteristique_appel_offre",
     *                 type="string",
     *                 minLength=10,
     *                 description="Motif de la modification (obligatoire, min 10 caractères)",
     *                 example="Modification des délais suite à validation du maître d'ouvrage"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Nouvelle version créée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/CaracteristiqueAppelOffreDetailed"),
     *             @OA\Property(property="message", type="string", example="Nouvelle version créée avec succès. Durée calculée : 214 jours")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Caractéristique introuvable"),
     *     @OA\Response(response=422, description="Erreur de validation"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function update(Request $request, $appelOffreId, $id)
    {
        // Validation - SUPPRIMÉ duree_estimee_jours car calculée automatiquement
        $validator = Validator::make($request->all(), [
            'date_demarrage_prevue_caracteristique_appel_offre' => 'nullable|date',
            'date_livraison_previsionnelle_caracteristique_appel_offre' => [
                'nullable',
                'date',
                'after:date_demarrage_prevue_caracteristique_appel_offre'
            ],
            'lieu_execution_caracteristique_appel_offre' => 'nullable|string|max:255',
            'montant_garantie_caracteristique_appel_offre' => 'nullable|numeric|min:0',
            'delai_garantie_jours_caracteristique_appel_offre' => 'nullable|numeric|min:0',
            'conditions_paiement_caracteristique_appel_offre' => 'nullable|string',
            'modalites_execution_caracteristique_appel_offre' => 'nullable|string',
            'documents_requis_caracteristique_appel_offre' => 'nullable|string',
            'autres_informations_caracteristique_appel_offre' => 'nullable|string',
            'motif_modification_caracteristique_appel_offre' => 'required|string|min:10',
        ], [
            'motif_modification_caracteristique_appel_offre.required' => 'Le motif de modification est obligatoire',
            'motif_modification_caracteristique_appel_offre.min' => 'Le motif de modification doit contenir au moins 10 caractères',
            'date_livraison_previsionnelle_caracteristique_appel_offre.after' => 'La date de livraison doit être postérieure à la date de démarrage',
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
            $caracteristique = CaracteristiqueAppelOffre::where('appel_offre_id', $appelOffreId)
                ->where('id_caracteristique_appel_offre', $id)
                ->firstOrFail();

            // Créer une nouvelle version - SANS duree_estimee_jours (calculée automatiquement)
            $nouvelleVersion = $caracteristique->creerNouvelleVersion([
                'date_demarrage_prevue_caracteristique_appel_offre' => $request->date_demarrage_prevue_caracteristique_appel_offre,
                'date_livraison_previsionnelle_caracteristique_appel_offre' => $request->date_livraison_previsionnelle_caracteristique_appel_offre,
                'lieu_execution_caracteristique_appel_offre' => $request->lieu_execution_caracteristique_appel_offre,
                'montant_garantie_caracteristique_appel_offre' => $request->montant_garantie_caracteristique_appel_offre,
                'delai_garantie_jours_caracteristique_appel_offre' => $request->delai_garantie_jours_caracteristique_appel_offre,
                'conditions_paiement_caracteristique_appel_offre' => $request->conditions_paiement_caracteristique_appel_offre,
                'modalites_execution_caracteristique_appel_offre' => $request->modalites_execution_caracteristique_appel_offre,
                'documents_requis_caracteristique_appel_offre' => $request->documents_requis_caracteristique_appel_offre,
                'autres_informations_caracteristique_appel_offre' => $request->autres_informations_caracteristique_appel_offre,
                'created_by' => auth()->id(),
            ], $request->motif_modification_caracteristique_appel_offre);
            // La durée sera calculée automatiquement par le modèle

            DB::commit();

            Log::info("Nouvelle version de caractéristique créée", [
                'id' => $nouvelleVersion->id_caracteristique_appel_offre,
                'version' => $nouvelleVersion->version_caracteristique_appel_offre,
                'duree_calculee' => $nouvelleVersion->duree_estimee_jours_caracteristique_appel_offre
            ]);

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => $nouvelleVersion->load(['appelOffre', 'creator', 'parent']),
                    'message' => 'Nouvelle version créée avec succès. Durée calculée : ' . $nouvelleVersion->duree_estimee_formattee
                ]);
            }

            return redirect()->route('caracteristiques-appels-offres.show', [
                'appel_offre' => $appelOffreId,
                'caracteristique' => $nouvelleVersion->id_caracteristique_appel_offre
            ])
                ->with('success', 'Nouvelle version créée avec succès (Version ' . $nouvelleVersion->version_caracteristique_appel_offre . '). Durée calculée : ' . $nouvelleVersion->duree_estimee_formattee);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la mise à jour: ' . $e->getMessage());

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
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
     * Supprime une caractéristique (soft delete)
     *
     * @OA\Delete(
     *     path="/appels-offres/{appel_offre}/caracteristiques/{caracteristique}",
     *     operationId="deleteCaracteristiqueAppelOffre",
     *     tags={"Caractéristiques des Appels d'Offres"},
     *     summary="Supprimer une caractéristique",
     *     description="Supprime (soft delete) une caractéristique. Requiert la permission `caracteristiques_appels_offres.delete`.",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="appel_offre",
     *         in="path",
     *         required=true,
     *         description="UUID de l'appel d'offres",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Parameter(
     *         name="caracteristique",
     *         in="path",
     *         required=true,
     *         description="UUID de la caractéristique",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Caractéristique supprimée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Caractéristique supprimée avec succès")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Caractéristique introuvable"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function destroy(Request $request, $appelOffreId, $id)
    {
        DB::beginTransaction();
        try {
            $caracteristique = CaracteristiqueAppelOffre::where('appel_offre_id', $appelOffreId)
                ->where('id_caracteristique_appel_offre', $id)
                ->firstOrFail();

            $caracteristique->deleted_by = auth()->id();
            $caracteristique->save();
            $caracteristique->delete();

            DB::commit();

            Log::info("Caractéristique supprimée", ['id' => $id]);

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'message' => 'Caractéristique supprimée avec succès'
                ]);
            }

            return redirect()->route('caracteristiques-appels-offres.index', ['appel_offre' => $appelOffreId])
                ->with('success', 'Caractéristique supprimée avec succès');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la suppression: ' . $e->getMessage());

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
     * Obtient l'historique des versions
     *
     * @OA\Get(
     *     path="/appels-offres/{appel_offre}/caracteristiques/{caracteristique}/historique",
     *     operationId="historiqueCaracteristiqueAppelOffre",
     *     tags={"Caractéristiques des Appels d'Offres"},
     *     summary="Historique des versions",
     *     description="Récupère l'historique complet des versions d'une caractéristique. Requiert la permission `caracteristiques_appels_offres.view-history`.",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="appel_offre",
     *         in="path",
     *         required=true,
     *         description="UUID de l'appel d'offres",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Parameter(
     *         name="caracteristique",
     *         in="path",
     *         required=true,
     *         description="UUID de la caractéristique",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Historique récupéré avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/CaracteristiqueAppelOffre")
     *             ),
     *             @OA\Property(property="caracteristique", ref="#/components/schemas/CaracteristiqueAppelOffre"),
     *             @OA\Property(property="message", type="string", example="Historique récupéré avec succès")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Caractéristique non trouvée"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function historique(Request $request, $appelOffreId, $caracteristiqueId)
    {
        try {
            $appelOffre = AppelOffre::findOrFail($appelOffreId);

            // Récupérer n'importe quelle version de la caractéristique
            $caracteristiqueDemandee = CaracteristiqueAppelOffre::where('appel_offre_id', $appelOffreId)
                ->where('id_caracteristique_appel_offre', $caracteristiqueId)
                ->firstOrFail();

            // Récupérer l'historique complet (getHistorique gère automatiquement les versions)
            $historique = $caracteristiqueDemandee->getHistorique();

            // Obtenir la version de base pour l'affichage
            if ($caracteristiqueDemandee->parent_id) {
                $caracteristique = CaracteristiqueAppelOffre::find($caracteristiqueDemandee->parent_id);
            } else {
                $caracteristique = $caracteristiqueDemandee;
            }

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => $historique,
                    'caracteristique' => $caracteristique,
                    'message' => 'Historique récupéré avec succès'
                ]);
            }

            return view('caracteristiques-appels-offres.historique', compact('historique', 'caracteristique', 'appelOffre'));
        } catch (ModelNotFoundException $e) {
            Log::error('Caractéristique non trouvée: ' . $e->getMessage());

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Caractéristique non trouvée'
                ], 404);
            }

            return back()->with('error', 'Caractéristique non trouvée');
        } catch (Exception $e) {
            Log::error('Erreur lors de la récupération de l\'historique: ' . $e->getMessage());

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la récupération de l\'historique',
                    'error' => $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Erreur lors de la récupération de l\'historique');
        }
    }



        /**
     * Restaure une version précédente
     *
     * @OA\Post(
     *     path="/appels-offres/{appel_offre}/caracteristiques/{caracteristique}/versions/{version}/restaurer",
     *     operationId="restaurerVersionCaracteristique",
     *     tags={"Caractéristiques des Appels d'Offres"},
     *     summary="Restaurer une version",
     *     description="Restaure une version précédente en créant une nouvelle version basée sur celle-ci. La durée est recalculée automatiquement. Requiert la permission `caracteristiques_appels_offres.view-history`.",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="appel_offre",
     *         in="path",
     *         required=true,
     *         description="UUID de l'appel d'offres",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Parameter(
     *         name="caracteristique",
     *         in="path",
     *         required=true,
     *         description="UUID de la caractéristique actuelle",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Parameter(
     *         name="version",
     *         in="path",
     *         required=true,
     *         description="UUID de la version à restaurer",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Version restaurée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/CaracteristiqueAppelOffre"),
     *             @OA\Property(property="message", type="string", example="Version restaurée avec succès")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Caractéristique ou version introuvable"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function restaurerVersion(Request $request, $appelOffreId, $id, $versionId)
    {
        DB::beginTransaction();
        try {
            $caracteristiqueActuelle = CaracteristiqueAppelOffre::where('appel_offre_id', $appelOffreId)
                ->where('id_caracteristique_appel_offre', $id)
                ->firstOrFail();

            $versionARestaurer = CaracteristiqueAppelOffre::findOrFail($versionId);

            // Créer une nouvelle version basée sur l'ancienne - SANS duree_estimee_jours
            $nouvelleVersion = $caracteristiqueActuelle->creerNouvelleVersion(
                $versionARestaurer->only([
                    'date_demarrage_prevue_caracteristique_appel_offre',
                    'date_livraison_previsionnelle_caracteristique_appel_offre',
                    'lieu_execution_caracteristique_appel_offre',
                    'montant_garantie_caracteristique_appel_offre',
                    'delai_garantie_jours_caracteristique_appel_offre',
                    'conditions_paiement_caracteristique_appel_offre',
                    'modalites_execution_caracteristique_appel_offre',
                    'documents_requis_caracteristique_appel_offre',
                    'autres_informations_caracteristique_appel_offre',
                ]),
                "Restauration de la version {$versionARestaurer->version_caracteristique_appel_offre}"
            );
            // La durée sera recalculée automatiquement

            DB::commit();

            Log::info("Version restaurée", [
                'nouvelle_version_id' => $nouvelleVersion->id_caracteristique_appel_offre,
                'version_restauree' => $versionARestaurer->version_caracteristique_appel_offre
            ]);

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => $nouvelleVersion,
                    'message' => 'Version restaurée avec succès'
                ]);
            }

            return redirect()->route('caracteristiques-appels-offres.show', [
                'appel_offre' => $appelOffreId,
                'caracteristique' => $nouvelleVersion->id_caracteristique_appel_offre
            ])
                ->with('success', 'Version ' . $versionARestaurer->version_caracteristique_appel_offre . ' restaurée avec succès');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la restauration: ' . $e->getMessage());

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la restauration',
                    'error' => $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Erreur lors de la restauration');
        }
    }
}
