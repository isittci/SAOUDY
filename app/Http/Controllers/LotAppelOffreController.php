<?php

namespace App\Http\Controllers;

use App\Models\Lot;
use App\Models\AppelOffre;
use App\Models\Prestataire;
use App\Models\Proforma;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Exception;

class LotAppelOffreController extends Controller
{
    /**
     * Affiche la liste des lots
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
            if ($request->wantsJson() || $request->is('api/*')) {
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
     * IMPORTANT: La création d'un lot nécessite obligatoirement un appel d'offres
     */
    public function create(Request $request, $appelOffreId)
    {
        // Vérifier qu'un appel d'offres est spécifié
        $appelOffreId = $request->get('appel_offre_id');

        if (!$appelOffreId) {
            if ($request->wantsJson() || $request->is('api/*')) {
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
     * Enregistre un nouveau lot
     * IMPORTANT: Un lot doit obligatoirement être lié à un appel d'offres
     */
    public function store(Request $request, $appelOffreId)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'appel_offre_id' => 'required|exists:appels_offres,id_appel_offre',
            'numero' => 'nullable|string|max:20',
            'libelle' => 'required|string|max:160',
            'description_critere' => 'nullable|string',
            'specifications_techniques' => 'nullable|string',
            'date_debut_prevue' => 'nullable|date',
            'date_fin_prevue' => 'nullable|date|after:date_debut_prevue',
            'taux_penalites' => 'nullable|numeric|min:0|max:100',
            'statut_lot' => 'required|in:0,1',
        ], [
            'appel_offre_id.required' => 'L\'appel d\'offres est obligatoire',
            'appel_offre_id.exists' => 'Appel d\'offres invalide',
            'numero.required' => 'Le numéro est obligatoire',
            // 'numero.unique' => 'Ce numéro de lot existe déjà pour cet appel d\'offres',
            'libelle.required' => 'Le libellé est obligatoire',
            'date_fin_prevue.after' => 'La date de fin doit être après la date de début',
            'taux_penalites.max' => 'Le taux de pénalités ne peut pas dépasser 100%',
            'statut_lot.required' => 'Le statut est obligatoire',
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
            // Vérifier que l'appel d'offres existe et est actif
            $appelOffre = AppelOffre::findOrFail($request->appel_offre_id);


            if (!$appelOffre->statut_evaluation_critere_appel_offre) {
                throw new Exception("Impossible de créer un lot pour un appel d'offres inactif");
            }


            if ($appelOffre->isCloture()) {
                throw new Exception("Impossible de créer un lot pour un appel d'offres clôturé");
            }

            // Vérifier l'unicité du numéro dans l'appel d'offres
            $existingLot = Lot::where('appel_offre_id', $request->appel_offre_id)
                ->where('numero', $request->numero)
                ->versionActuelle()
                ->first();

            if ($existingLot) {
                throw new Exception("Un lot avec ce numéro existe déjà pour cet appel d'offres");
            }

            $numeroTypeAO = $appelOffre->typeAppelOffre->code_type_appel_offre;
            $numeroAO = $appelOffre->numero_appel_offre;

            // Récupérer le dernier lot de cet AO
            $lastLot = Lot::where('appel_offre_id', $appelOffre->id_appel_offre)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($lastLot) {
                $lastNumber = intval(substr($lastLot->numero, -3)) + 1;
            } else {
                $lastNumber = 1;
            }

            // Concaténation automatique formatée à 3 chiffres
            $concatAuto = str_pad($lastNumber, 3, '0', STR_PAD_LEFT);

            // Génération du numéro complet
            $numeroLot = 'LOT-' . $numeroTypeAO . '-' . $numeroAO . '-' . $concatAuto;

            // return response()->json($numeroLot);

            // Créer le lot
            $lot = Lot::create([
                'appel_offre_id' => $request->appel_offre_id,
                'numero' => $numeroLot,
                'libelle' => $request->libelle,
                'description_critere' => $request->description_critere,
                'specifications_techniques' => $request->specifications_techniques,
                'date_debut_prevue' => $request->date_debut_prevue,
                'date_fin_prevue' => $request->date_fin_prevue,
                'taux_penalites' => $request->taux_penalites,
                'statut_lot' => $request->statut_lot,
                'attribution_lot' => 0,
                'created_by' => auth()->id(),
            ]);

            DB::commit();

            Log::info("Lot créé avec succès", [
                'id' => $lot->id_lot,
                'appel_offre_id' => $request->appel_offre_id
            ]);

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => $lot->load(['appelOffre', 'creator']),
                    'message' => 'Lot créé avec succès'
                ], 201);
            }

            // Rediriger vers la page de l'appel d'offres ou du lot selon le contexte
            if ($request->has('redirect_to_ao')) {
                return redirect()->route('appels-offres.show', $request->appel_offre_id)
                    ->with('success', 'Lot créé avec succès');
            }

            return redirect()->route('lots-appels-offres.show', [$appelOffreId, $lot->id_lot])
                ->with('success', 'Lot créé avec succès');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la création du lot: ' . $e->getMessage());

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
     * Affiche les détails d'un lot
     */
    public function show(Request $request, $appelOffreId, $id)
    {
        try {
            $lot = Lot::with([
                'appelOffre.typeAppelOffre',
                'parent',
                'versions' => function ($q) {
                    $q->with(['creator', 'updater']);
                },
                'criteresEvaluation',
                'attributionActive.prestataire',
                'historiqueAttributions.prestataire',
                'creator',
                'updater'
            ])->where('appel_offre_id', $appelOffreId)->findOrFail($id);

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => $lot,
                    'historique' => $lot->getHistorique(),
                    'statistiques' => [
                        'duree_jours' => $lot->calculerDuree(),
                        'est_attribue' => $lot->isAttribue(),
                        'est_retire' => $lot->isRetire(),
                        'a_attribution_active' => $lot->aUneAttributionActive(),
                    ],
                    'message' => 'Détails récupérés avec succès'
                ]);
            }

            //Récupérer les prestataires actifs pour l'affectation
            $prestataires = Prestataire::actif()
                ->orderBy('raison_sociale_prestataire', 'asc')
                ->get();

            //Récupérer les proformas disponibles et actifs
            $proformas = Proforma::actif()
                ->orderBy('numero_proforma', 'asc')
                ->get();




            return view('appels-offres.lot-show', compact('lot', 'prestataires', 'proformas'));
        } catch (Exception $e) {
            Log::error('Erreur lors de la récupération du lot: ' . $e->getMessage());

            if ($request->wantsJson() || $request->is('api/*')) {
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
     * Affiche le formulaire de modification
     */
    public function edit($appelOffreIdb, $id)
    {
        try {
            $lot = Lot::with('appelOffre')->findOrFail($id);

            return view('appels-offres.lot-edit', compact('lot'));
        } catch (Exception $e) {
            return back()->with('error', 'Lot introuvable');
        }
    }

    /**
     * Met à jour un lot (crée une nouvelle version)
     */
    public function update(Request $request, $appelOffreId, $id)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'libelle' => 'required|string|max:160',
            'description_critere' => 'nullable|string',
            'specifications_techniques' => 'nullable|string',
            'date_debut_prevue' => 'nullable|date',
            'date_fin_prevue' => 'nullable|date|after:date_debut_prevue',
            'taux_penalites' => 'nullable|numeric|min:0|max:100',
            'statut_lot' => 'required|in:0,1',
            'motif_modification' => 'required|string',
        ], [
            'libelle.required' => 'Le libellé est obligatoire',
            'date_fin_prevue.after' => 'La date de fin doit être après la date de début',
            'motif_modification.required' => 'Le motif de modification est obligatoire',
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
            $lot = Lot::findOrFail($id);



            // Créer une nouvelle version au lieu de modifier
            $nouvelleVersion = $lot->creerNouvelleVersion([
                'libelle' => $request->libelle,
                'description_critere' => $request->description_critere,
                'specifications_techniques' => $request->specifications_techniques,
                'date_debut_prevue' => $request->date_debut_prevue,
                'date_fin_prevue' => $request->date_fin_prevue,
                'taux_penalites' => $request->taux_penalites,
                'statut_lot' => $request->statut_lot,
                'created_by' => auth()->id(),
            ], $request->motif_modification);

            $lot->update(['statut_lot' => 0]);

            DB::commit();

            Log::info("Nouvelle version de lot créée", ['id' => $nouvelleVersion->id_lot]);

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => $nouvelleVersion->load(['appelOffre', 'creator', 'parent']),
                    'message' => 'Nouvelle version créée avec succès'
                ]);
            }



            return redirect()->route('lots-appels-offres.show', [$appelOffreId, $nouvelleVersion->id_lot])
                ->with('success', 'Nouvelle version créée avec succès');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la mise à jour: ' . $e->getMessage());

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
     * Supprime un lot (soft delete)
     */
    public function destroy(Request $request, $appelOffreId, $id)
    {
        DB::beginTransaction();
        try {
            $lot = Lot::findOrFail($id);

            // Vérifier si le lot est attribué
            if ($lot->isAttribue()) {
                if ($request->wantsJson() || $request->is('api/*')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Impossible de supprimer un lot attribué'
                    ], 422);
                }

                return back()->with('error', 'Impossible de supprimer un lot attribué');
            }

            $lot->deleted_by = auth()->id();
            $lot->save();
            $lot->delete();

            DB::commit();

            Log::info("Lot supprimé", ['id' => $id]);

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'message' => 'Lot supprimé avec succès'
                ]);
            }

            return redirect()->route('lots.index')
                ->with('success', 'Lot supprimé avec succès');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la suppression: ' . $e->getMessage());

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
     * Attribue un lot à un prestataire
     */
    public function attribuer(Request $request, $appelOffreId, $id)
    {

        $validator = Validator::make($request->all(), [
            'prestataire_id' => 'required|exists:prestataires,id_prestataire',
            'proforma_id' => 'required|exists:proformas,id_proforma',
            'date_attribution' => 'nullable|date',
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
            /**
             * @var Lot $lot
             */
            $lot = Lot::findOrFail($id);

            if ($lot->isAttribue()) {
                throw new Exception('Ce lot est déjà attribué');
            }

            $prestataire = Prestataire::findOrFail($request->prestataire_id);
            $proforma = Proforma::findOrFail($request->proforma_id);

            $attribution = $lot->attribuerAuPrestataire(
                $prestataire,
                $proforma,
                auth()->id()
            );

            $lot->attribuer($request->date_attribution);

            DB::commit();

            Log::info("Lot attribué", [
                'lot_id' => $id,
                'prestataire_id' => $request->prestataire_id
            ]);

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => $attribution->load(['prestataire', 'lot', 'proforma']),
                    'message' => 'Lot attribué avec succès'
                ]);
            }

            return redirect()->route('lots-appels-offres.show', [$appelOffreId, $id])->with('success', 'Lot attribué avec succès');


        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de l\'attribution: ' . $e->getMessage());
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de l\'attribution.',
                    'error' => $e->getMessage()
                ], 500);
            }
            return back()->with('error', 'Erreur lors de l\'attribution: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Retire un lot
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
            $lot = Lot::findOrFail($id);

            $lot->retirer($request->motif_retrait, auth()->id());

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
     * Obtient l'historique des versions
     */
    public function historique(Request $request, $appelOffreId, $id)
    {

        try {
            $lot = Lot::findOrFail($id);
            $historique = $lot->getHistorique();

            if ($request->wantsJson() || $request->is('api/*')) {
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
     * Obtient les statistiques d'un lot
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

            if ($request->wantsJson() || $request->is('api/*')) {
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
}
