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
                'created_by' => auth()->id(),
            ]);

            DB::commit();

            Log::info("Lot créé", ['id' => $lot->id_lot]);

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => $lot->load('appelOffre'),
                    'message' => 'Lot créé avec succès'
                ], 201);
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

            return back()->with('error', 'Erreur lors de la création: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Affiche les détails d'un lot
     */
    public function show(Request $request, $appelOffreId, $id)
    {
        try {
            $lot = Lot::actif()->with([
                'appelOffre.typeAppelOffre',
                'creator',
                'updater',
                'attributionActive.prestataire',
                'attributionActive.proforma',
                'criteresEvaluation'
            ])->findOrFail($id);

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => $lot,
                    'message' => 'Détails du lot récupérés avec succès'
                ]);
            }

            // Charger les prestataires et proformas pour le modal d'attribution
            $prestataires = Prestataire::actif()->orderBy('raison_sociale_prestataire')->get();
            $proformas = Proforma::actif()->orderBy('numero_proforma', 'desc')->get();

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
     * Affiche le formulaire d'édition
     */
    public function edit(Request $request, $appelOffreId, $id)
    {
        try {
            $lot = Lot::with(['appelOffre.typeAppelOffre'])->findOrFail($id);

            // Vérifier que le lot n'est pas attribué
            if ($lot->isAttribue()) {
                return back()->with('error', 'Impossible de modifier un lot attribué');
            }

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => $lot,
                    'message' => 'Données du lot récupérées pour modification'
                ]);
            }

            return view('appels-offres.lot-edit', compact('lot'));
        } catch (Exception $e) {
            Log::error('Erreur lors de la récupération du lot pour édition: ' . $e->getMessage());

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
     * Met à jour un lot
     */
    public function update(Request $request, $appelOffreId, $id)
    {
        $validator = Validator::make($request->all(), [
            'libelle' => 'required|string|max:160',
            'description_critere' => 'nullable|string',
            'specifications_techniques' => 'nullable|string',
            'date_debut_prevue' => 'nullable|date',
            'date_fin_prevue' => 'nullable|date|after:date_debut_prevue',
            'taux_penalites' => 'nullable|numeric|min:0|max:100',
            'statut_lot' => 'required|in:0,1',
        ], [
            'libelle.required' => 'Le libellé est obligatoire',
            'date_fin_prevue.after' => 'La date de fin doit être après la date de début',
            'taux_penalites.max' => 'Le taux de pénalités ne peut pas dépasser 100%',
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

            // Vérifier que le lot n'est pas attribué
            if ($lot->isAttribue()) {
                throw new Exception('Impossible de modifier un lot attribué. Créez une nouvelle version.');
            }

            $lot->update([
                'libelle' => $request->libelle,
                'description_critere' => $request->description_critere,
                'specifications_techniques' => $request->specifications_techniques,
                'date_debut_prevue' => $request->date_debut_prevue,
                'date_fin_prevue' => $request->date_fin_prevue,
                'taux_penalites' => $request->taux_penalites,
                'statut_lot' => $request->statut_lot,
                'updated_by' => auth()->id(),
            ]);

            DB::commit();

            Log::info("Lot modifié", ['id' => $id]);

            if ($request->wantsJson() || $request->is('api/*')) {
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

            if ($request->wantsJson() || $request->is('api/*')) {
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

            if ($request->wantsJson() || $request->is('api/*')) {
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

            if ($request->wantsJson() || $request->is('api/*')) {
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
     * ================================================================
     * ATTRIBUTION DU LOT - MÉTHODE AMÉLIORÉE
     * ================================================================
     *
     * Cette méthode gère deux modes d'attribution :
     * 1. Mode "select" : Sélection d'une proforma existante
     * 2. Mode "create" : Création d'une nouvelle proforma avant attribution
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
            'new_date_redemarrage.required' => 'La date de redémarrage est obligatoire.',
            'new_date_fin_validee.required' => 'La date de fin validée est obligatoire.',
            'new_date_fin_validee.after' => 'La date de fin doit être après la date de début.',
            'new_montant_retenu.required' => 'Le montant retenu est obligatoire.',
            'new_montant_retenu.numeric' => 'Le montant retenu doit être un nombre.',
            'new_montant_retenu.min' => 'Le montant retenu doit être positif.',
            'new_taux_tva.required' => 'Le taux de TVA est obligatoire.',
            'new_taux_tva.between' => 'Le taux de TVA doit être compris entre 0 et 100.',
            'new_modalite_paiement.required' => 'Les modalités de paiement sont obligatoires.',
        ];

        // Ajouter les règles selon le mode
        if ($proformaMode === 'select') {
            // Mode sélection : proforma_id obligatoire
            $rules['proforma_id'] = 'required|exists:proformas,id_proforma';
        } else {
            // Mode création : tous les champs de la proforma sont obligatoires
            $rules['new_date_proforma'] = 'required|date';
            $rules['new_date_debut_validee'] = 'required|date';
            $rules['new_date_redemarrage'] = 'required|date';
            $rules['new_date_fin_validee'] = 'required|date|after_or_equal:new_date_debut_validee';
            $rules['new_montant_retenu'] = 'required|numeric|min:0';
            $rules['new_taux_tva'] = 'required|numeric|between:0,100';
            $rules['new_taxe_montant'] = 'nullable|numeric|min:0';
            $rules['new_taux_remise'] = 'nullable|numeric|between:0,100';
            $rules['new_remise_montant'] = 'nullable|numeric|min:0';
            $rules['new_penalites'] = 'nullable|numeric|min:0';
            $rules['new_modalite_paiement'] = 'required|string|max:500';
        }

        // Validation
        $validator = Validator::make($request->all(), $rules, $messages);

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
                $penalites = floatval($request->input('new_penalites', 0));

                // Calcul TVA
                $montantTVA = $montantRetenu * ($tauxTVA / 100);

                // Calcul Remise
                $montantRemise = $montantRetenu * ($tauxRemise / 100);

                // Créer la proforma
                $proforma = Proforma::create([
                    'date_proforma' => $request->new_date_proforma,
                    'date_debut_validee_proforma' => $request->new_date_debut_validee,
                    'date_redemarrage_proforma' => $request->new_date_redemarrage,
                    'date_fin_validee_proforma' => $request->new_date_fin_validee,
                    'montant_retenu_proforma' => $montantRetenu,
                    'taxe_montant' => $montantTVA,
                    'remise_montant_proforma' => $montantRemise,
                    'penalites_proforma' => $penalites,
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

            if ($request->wantsJson() || $request->is('api/*')) {
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

            if ($request->wantsJson() || $request->is('api/*')) {
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
