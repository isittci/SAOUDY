<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Lot;
use App\Models\CritereEvaluation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CritereEvaluationController extends Controller
{
    /**
     * Récupère et valide le lot avec son appel d'offres
     * Cette méthode centralisée évite la duplication de code et assure la cohérence hiérarchique
     */
    private function getLotWithValidation($appelOffreId, $lotId)
    {
        return Lot::where('appel_offre_id', $appelOffreId)
            ->where('id_lot', $lotId)
            ->with(['appelOffre.typeAppelOffre'])
            ->firstOrFail();
    }

    /**
     * Affiche la liste des critères d'évaluation d'un lot
     */
    public function index(Request $request, $appelOffreId, $lotId)
    {
        try {
            // Validation de hiérarchie
            $lot = $this->getLotWithValidation($appelOffreId, $lotId);

            $query = CritereEvaluation::with(['lot', 'creator', 'updater'])->where('lot_id', $lotId);

            // Filtres
            if ($request->filled('statut')) {
                $query->where('statut_critere_evaluation', $request->statut);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('numero_critere_evaluation', 'like', "%{$search}%")
                        ->orWhere('libelle_critere_evaluation', 'like', "%{$search}%");
                });
            }

            // Tri (par défaut par ordre d'exécution)
            $sortBy = $request->get('sort_by', 'ordre_execution_critere_evaluation');
            $sortOrder = $request->get('sort_order', 'asc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 10);
            $criteres = $query->paginate($perPage);

            // Calculer la somme des notes de référence
            $totalNotes = CritereEvaluation::where('lot_id', $lotId)
                ->sum('note_reference_critere_evaluation');

            // Retour selon le type de requête
            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => $criteres,
                    'lot' => $lot,
                    'total_notes' => $totalNotes,
                    'message' => 'Liste des critères récupérée avec succès'
                ]);
            }


            return view('criteres-evaluations.index', compact('criteres', 'lot', 'totalNotes'));
        } catch (ModelNotFoundException $e) {
            Log::error('Lot introuvable ou incohérent: ' . $e->getMessage());

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lot introuvable ou ne correspond pas à cet appel d\'offres'
                ], 404);
            }

            return back()->with('error', 'Lot introuvable ou ne correspond pas à cet appel d\'offres');
        } catch (Exception $e) {
            Log::error('Erreur lors de la récupération des critères: ' . $e->getMessage());

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
     * IMPORTANT: La création d'un critère nécessite obligatoirement un lot actif
     */
    public function create(Request $request, $appelOffreId, $lotId)
    {
        try {
            // Validation de hiérarchie
            $lot = $this->getLotWithValidation($appelOffreId, $lotId);

            // Vérifier que le lot est actif
            if (!$lot->statut_lot) {
                return back()->with('error', 'Impossible de créer un critère pour un lot inactif');
            }

            // Vérifier que le lot n'est pas attribué
            if ($lot->isAttribue()) {
                return back()->with('error', 'Impossible de créer un critère pour un lot déjà attribué');
            }

            // Vérifier que le lot n'est pas retiré
            if ($lot->isRetire()) {
                return back()->with('error', 'Impossible de créer un critère pour un lot retiré');
            }

            // Générer le prochain numéro automatiquement
            $prochainNumero = CritereEvaluation::genererNumeroCritere($lotId);

            // Calculer le prochain ordre d'exécution
            $prochainOrdre = CritereEvaluation::where('lot_id', $lotId)
                ->max('ordre_execution_critere_evaluation') ?? 0;
            $prochainOrdre += 1;

            // Calculer la somme actuelle des notes
            $totalNotesExistantes = CritereEvaluation::where('lot_id', $lotId)
                ->sum('note_reference_critere_evaluation');

            $noteRestante = 100 - $totalNotesExistantes;

            return view('criteres-evaluations.create', compact(
                'lot',
                'prochainNumero',
                'prochainOrdre',
                'totalNotesExistantes',
                'noteRestante'
            ));
        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'Lot introuvable ou ne correspond pas à cet appel d\'offres');
        } catch (Exception $e) {
            return back()->with('error', 'Erreur lors du chargement du formulaire');
        }
    }

    /**
     * Enregistre un nouveau critère d'évaluation
     * IMPORTANT: Un critère doit obligatoirement être lié à un lot actif
     */
    public function store(Request $request, $appelOffreId, $lotId)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'libelle_critere_evaluation' => 'required|string|max:160',
            'description_critere_evaluation' => 'nullable|string',
            'note_reference_critere_evaluation' => 'required|numeric|min:0|max:100',
            'ordre_execution_critere_evaluation' => 'required|integer|min:1',
            'statut_critere_evaluation' => 'required|in:0,1',
        ], [
            'libelle_critere_evaluation.required' => 'Le libellé est obligatoire',
            'libelle_critere_evaluation.max' => 'Le libellé ne peut pas dépasser 160 caractères',
            'note_reference_critere_evaluation.required' => 'La note de référence est obligatoire',
            'note_reference_critere_evaluation.min' => 'La note de référence doit être supérieure ou égale à 0',
            'note_reference_critere_evaluation.max' => 'La note de référence ne peut pas dépasser 100',
            'ordre_execution_critere_evaluation.required' => 'L\'ordre d\'exécution est obligatoire',
            'ordre_execution_critere_evaluation.min' => 'L\'ordre d\'exécution doit être au moins 1',
            'statut_critere_evaluation.required' => 'Le statut est obligatoire',
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
            // Validation de hiérarchie et récupération du lot
            $lot = $this->getLotWithValidation($appelOffreId, $lotId);

            // Vérifications business
            if (!$lot->statut_lot) {
                throw new Exception("Impossible de créer un critère pour un lot inactif");
            }

            if ($lot->isAttribue()) {
                throw new Exception("Impossible de créer un critère pour un lot déjà attribué");
            }

            if ($lot->isRetire()) {
                throw new Exception("Impossible de créer un critère pour un lot retiré");
            }

            // Vérifier la somme des notes de référence
            $totalNotesExistantes = CritereEvaluation::where('lot_id', $lotId)
                ->sum('note_reference_critere_evaluation');

            $nouvelleSomme = $totalNotesExistantes + $request->note_reference_critere_evaluation;

            if ($nouvelleSomme > 100) {
                throw new Exception("La somme des notes de référence ne peut pas dépasser 100. Total actuel: {$totalNotesExistantes}, note restante: " . (100 - $totalNotesExistantes));
            }

            // Vérifier l'unicité de l'ordre d'exécution
            $ordreExiste = CritereEvaluation::where('lot_id', $lotId)
                ->where('ordre_execution_critere_evaluation', $request->ordre_execution_critere_evaluation)
                ->exists();

            if ($ordreExiste) {
                throw new Exception("Un critère avec cet ordre d'exécution existe déjà pour ce lot");
            }

            // Générer le numéro automatiquement
            $numero = CritereEvaluation::genererNumeroCritere($lotId);

            // Créer le critère
            $critere = CritereEvaluation::create([
                'lot_id' => $lotId,
                'numero_critere_evaluation' => $numero,
                'libelle_critere_evaluation' => $request->libelle_critere_evaluation,
                'description_critere_evaluation' => $request->description_critere_evaluation,
                'note_reference_critere_evaluation' => $request->note_reference_critere_evaluation,
                'ordre_execution_critere_evaluation' => $request->ordre_execution_critere_evaluation,
                'statut_critere_evaluation' => $request->statut_critere_evaluation,
                'created_by' => auth()->id(),
            ]);

            DB::commit();

            Log::info("Critère d'évaluation créé avec succès", [
                'id' => $critere->id_critere_evaluation,
                'lot_id' => $lotId,
                'numero' => $numero
            ]);

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => $critere->load(['lot', 'creator']),
                    'message' => 'Critère créé avec succès'
                ], 201);
            }

            return redirect()->route('criteres-evaluations.show', [
                'appel_offre' => $appelOffreId,
                'lot' => $lotId,
                'critere' => $critere->id_critere_evaluation
            ])
                ->with('success', 'Critère d\'évaluation créé avec succès');
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            Log::error('Lot introuvable: ' . $e->getMessage());

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lot introuvable ou ne correspond pas à cet appel d\'offres'
                ], 404);
            }

            return back()->with('error', 'Lot introuvable ou ne correspond pas à cet appel d\'offres')->withInput();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la création du critère: ' . $e->getMessage());

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
     * Affiche les détails d'un critère d'évaluation
     */
    public function show(Request $request, $appelOffreId, $lotId, $id)
    {
        try {
            // Validation de hiérarchie
            $lot = $this->getLotWithValidation($appelOffreId, $lotId);

            $critere = CritereEvaluation::with([
                'lot.appelOffre.typeAppelOffre',
                'creator',
                'updater'
            ])
                ->where('lot_id', $lotId)
                ->where('id_critere_evaluation', $id)
                ->firstOrFail();

            // Calculer des statistiques
            $totalNotes = CritereEvaluation::where('lot_id', $lotId)
                ->sum('note_reference_critere_evaluation');

            $pourcentage = $totalNotes > 0
                ? ($critere->note_reference_critere_evaluation / $totalNotes) * 100
                : 0;

            // Compter le nombre de critères
            $nombreCriteres = CritereEvaluation::where('lot_id', $lotId)->count();

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => $critere,
                    'statistiques' => [
                        'total_notes_lot' => $totalNotes,
                        'pourcentage_note' => round($pourcentage, 2),
                        'nombre_criteres' => $nombreCriteres,
                        'note_restante' => 100 - $totalNotes
                    ],
                    'message' => 'Détails du critère récupérés avec succès'
                ]);
            }

            return view('criteres-evaluations.show', compact('critere', 'lot', 'totalNotes', 'pourcentage', 'nombreCriteres'));
        } catch (ModelNotFoundException $e) {
            Log::error('Critère ou lot introuvable: ' . $e->getMessage());

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Critère introuvable ou ne correspond pas à ce lot'
                ], 404);
            }

            return back()->with('error', 'Critère introuvable');
        } catch (Exception $e) {
            Log::error('Erreur lors de la récupération du critère: ' . $e->getMessage());

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
     * Affiche le formulaire de modification
     */
    public function edit($appelOffreId, $lotId, $id)
    {
        try {
            // Validation de hiérarchie
            $lot = $this->getLotWithValidation($appelOffreId, $lotId);

            $critere = CritereEvaluation::with(['lot.appelOffre'])
                ->where('lot_id', $lotId)
                ->where('id_critere_evaluation', $id)
                ->firstOrFail();

            // Vérifier que le lot n'est pas attribué
            if ($lot->isAttribue()) {
                return back()->with('error', 'Impossible de modifier un critère d\'un lot déjà attribué');
            }

            // Vérifier que le lot n'est pas retiré
            if ($lot->isRetire()) {
                return back()->with('error', 'Impossible de modifier un critère d\'un lot retiré');
            }

            // Calculer la somme des autres notes pour validation
            $totalAutresNotes = CritereEvaluation::where('lot_id', $lotId)
                ->where('id_critere_evaluation', '!=', $id)
                ->sum('note_reference_critere_evaluation');

            $noteMaximaleAutorisee = 100 - $totalAutresNotes;

            return view('criteres-evaluations.edit', compact('critere', 'lot', 'totalAutresNotes', 'noteMaximaleAutorisee'));
        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'Critère introuvable ou ne correspond pas à ce lot');
        } catch (Exception $e) {
            return back()->with('error', 'Erreur lors du chargement du formulaire');
        }
    }

    /**
     * Met à jour un critère d'évaluation
     */
    public function update(Request $request, $appelOffreId, $lotId, $id)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'libelle_critere_evaluation' => 'required|string|max:160',
            'description_critere_evaluation' => 'nullable|string',
            'note_reference_critere_evaluation' => 'required|numeric|min:0|max:100',
            'ordre_execution_critere_evaluation' => 'required|integer|min:1',
            'statut_critere_evaluation' => 'required|in:0,1',
        ], [
            'libelle_critere_evaluation.required' => 'Le libellé est obligatoire',
            'note_reference_critere_evaluation.required' => 'La note de référence est obligatoire',
            'note_reference_critere_evaluation.max' => 'La note de référence ne peut pas dépasser 100',
            'ordre_execution_critere_evaluation.required' => 'L\'ordre d\'exécution est obligatoire',
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
            // Validation de hiérarchie
            $lot = $this->getLotWithValidation($appelOffreId, $lotId);

            $critere = CritereEvaluation::where('lot_id', $lotId)
                ->where('id_critere_evaluation', $id)
                ->firstOrFail();

            // Vérifications business
            if ($lot->isAttribue()) {
                throw new Exception("Impossible de modifier un critère d'un lot déjà attribué");
            }

            if ($lot->isRetire()) {
                throw new Exception("Impossible de modifier un critère d'un lot retiré");
            }

            // Vérifier la somme des notes de référence
            $totalAutresNotes = CritereEvaluation::where('lot_id', $lotId)
                ->where('id_critere_evaluation', '!=', $id)
                ->sum('note_reference_critere_evaluation');

            $nouvelleSomme = $totalAutresNotes + $request->note_reference_critere_evaluation;

            if ($nouvelleSomme > 100) {
                throw new Exception("La somme des notes de référence ne peut pas dépasser 100. Total des autres critères: {$totalAutresNotes}, note maximale autorisée: " . (100 - $totalAutresNotes));
            }

            // Vérifier l'unicité de l'ordre d'exécution (sauf pour le critère actuel)
            if ($request->ordre_execution_critere_evaluation != $critere->ordre_execution_critere_evaluation) {
                $ordreExiste = CritereEvaluation::where('lot_id', $lotId)
                    ->where('id_critere_evaluation', '!=', $id)
                    ->where('ordre_execution_critere_evaluation', $request->ordre_execution_critere_evaluation)
                    ->exists();

                if ($ordreExiste) {
                    throw new Exception("Un autre critère avec cet ordre d'exécution existe déjà pour ce lot");
                }
            }

            // Mettre à jour le critère
            $critere->update([
                'libelle_critere_evaluation' => $request->libelle_critere_evaluation,
                'description_critere_evaluation' => $request->description_critere_evaluation,
                'note_reference_critere_evaluation' => $request->note_reference_critere_evaluation,
                'ordre_execution_critere_evaluation' => $request->ordre_execution_critere_evaluation,
                'statut_critere_evaluation' => $request->statut_critere_evaluation,
                'updated_by' => auth()->id(),
            ]);

            DB::commit();

            Log::info("Critère d'évaluation mis à jour", [
                'id' => $id,
                'lot_id' => $lotId
            ]);

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => $critere->load(['lot', 'updater']),
                    'message' => 'Critère mis à jour avec succès'
                ]);
            }

            return redirect()->route('criteres-evaluations.show', [
                'appel_offre' => $appelOffreId,
                'lot' => $lotId,
                'critere' => $id
            ])
                ->with('success', 'Critère d\'évaluation mis à jour avec succès');
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            Log::error('Critère ou lot introuvable: ' . $e->getMessage());

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Critère introuvable ou ne correspond pas à ce lot'
                ], 404);
            }

            return back()->with('error', 'Critère introuvable ou ne correspond pas à ce lot')->withInput();
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
     * Supprime un critère d'évaluation (soft delete)
     */
    public function destroy(Request $request, $appelOffreId, $lotId, $id)
    {
        DB::beginTransaction();
        try {
            // Validation de hiérarchie
            $lot = $this->getLotWithValidation($appelOffreId, $lotId);

            $critere = CritereEvaluation::where('lot_id', $lotId)
                ->where('id_critere_evaluation', $id)
                ->firstOrFail();

            // Vérifier que le lot n'est pas attribué
            if ($lot->isAttribue()) {
                throw new Exception("Impossible de supprimer un critère d'un lot déjà attribué");
            }

            // Vérifier que le lot n'est pas retiré
            if ($lot->isRetire()) {
                throw new Exception("Impossible de supprimer un critère d'un lot retiré");
            }

            $critere->deleted_by = auth()->id();
            $critere->save();
            $critere->delete();

            DB::commit();

            Log::info("Critère d'évaluation supprimé", [
                'id' => $id,
                'lot_id' => $lotId
            ]);

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'message' => 'Critère supprimé avec succès'
                ]);
            }

            return redirect()->route('criteres-evaluations.index', [
                'appel_offre' => $appelOffreId,
                'lot' => $lotId
            ])
                ->with('success', 'Critère d\'évaluation supprimé avec succès');
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            Log::error('Critère ou lot introuvable: ' . $e->getMessage());

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Critère introuvable ou ne correspond pas à ce lot'
                ], 404);
            }

            return back()->with('error', 'Critère introuvable');
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

            return back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Active un critère d'évaluation
     */
    public function activer(Request $request, $appelOffreId, $lotId, $id)
    {
        DB::beginTransaction();
        try {
            // Validation de hiérarchie
            $lot = $this->getLotWithValidation($appelOffreId, $lotId);

            $critere = CritereEvaluation::where('lot_id', $lotId)
                ->where('id_critere_evaluation', $id)
                ->firstOrFail();

            if ($lot->isAttribue()) {
                throw new Exception("Impossible d'activer un critère d'un lot déjà attribué");
            }

            $critere->activer();

            DB::commit();

            Log::info("Critère d'évaluation activé", ['id' => $id]);

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => $critere,
                    'message' => 'Critère activé avec succès'
                ]);
            }

            return back()->with('success', 'Critère activé avec succès');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de l\'activation: ' . $e->getMessage());

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de l\'activation',
                    'error' => $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Désactive un critère d'évaluation
     */
    public function desactiver(Request $request, $appelOffreId, $lotId, $id)
    {
        DB::beginTransaction();
        try {
            // Validation de hiérarchie
            $lot = $this->getLotWithValidation($appelOffreId, $lotId);

            $critere = CritereEvaluation::where('lot_id', $lotId)
                ->where('id_critere_evaluation', $id)
                ->firstOrFail();

            if ($lot->isAttribue()) {
                throw new Exception("Impossible de désactiver un critère d'un lot déjà attribué");
            }

            $critere->desactiver();

            DB::commit();

            Log::info("Critère d'évaluation désactivé", ['id' => $id]);

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => $critere,
                    'message' => 'Critère désactivé avec succès'
                ]);
            }

            return back()->with('success', 'Critère désactivé avec succès');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la désactivation: ' . $e->getMessage());

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la désactivation',
                    'error' => $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Réordonne un critère d'évaluation
     */
    public function reordonner(Request $request, $appelOffreId, $lotId, $id)
    {
        $validator = Validator::make($request->all(), [
            'nouvel_ordre' => 'required|integer|min:1',
        ], [
            'nouvel_ordre.required' => 'Le nouvel ordre est obligatoire',
            'nouvel_ordre.min' => 'L\'ordre doit être au moins 1',
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
            // Validation de hiérarchie
            $lot = $this->getLotWithValidation($appelOffreId, $lotId);

            $critere = CritereEvaluation::where('lot_id', $lotId)
                ->where('id_critere_evaluation', $id)
                ->firstOrFail();

            if ($lot->isAttribue()) {
                throw new Exception("Impossible de réordonner un critère d'un lot déjà attribué");
            }

            $critere->reordonner($request->nouvel_ordre);

            DB::commit();

            Log::info("Critère d'évaluation réordonné", [
                'id' => $id,
                'nouvel_ordre' => $request->nouvel_ordre
            ]);

            return response()->json([
                'success' => true,
                'data' => $critere,
                'message' => 'Critère réordonné avec succès'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors du réordonnancement: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du réordonnancement',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtient les statistiques des critères d'un lot
     */
    public function statistiques(Request $request, $appelOffreId, $lotId)
    {
        try {
            // Validation de hiérarchie
            $lot = $this->getLotWithValidation($appelOffreId, $lotId);

            $criteres = CritereEvaluation::where('lot_id', $lotId)->get();

            $stats = [
                'nombre_total' => $criteres->count(),
                'nombre_actifs' => $criteres->where('statut_critere_evaluation', 1)->count(),
                'nombre_inactifs' => $criteres->where('statut_critere_evaluation', 0)->count(),
                'somme_notes' => $criteres->sum('note_reference_critere_evaluation'),
                'note_restante' => 100 - $criteres->sum('note_reference_critere_evaluation'),
                'note_moyenne' => $criteres->count() > 0 ? $criteres->avg('note_reference_critere_evaluation') : 0,
                'note_minimale' => $criteres->min('note_reference_critere_evaluation'),
                'note_maximale' => $criteres->max('note_reference_critere_evaluation'),
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
     * Duplique un critère d'évaluation
     */
    public function dupliquer(Request $request, $appelOffreId, $lotId, $id)
    {
        DB::beginTransaction();
        try {
            // Validation de hiérarchie
            $lot = $this->getLotWithValidation($appelOffreId, $lotId);

            $critere = CritereEvaluation::where('lot_id', $lotId)
                ->where('id_critere_evaluation', $id)
                ->firstOrFail();

            if ($lot->isAttribue()) {
                throw new Exception("Impossible de dupliquer un critère d'un lot déjà attribué");
            }

            // Vérifier que la duplication n'excède pas 100 points
            $totalNotes = CritereEvaluation::where('lot_id', $lotId)
                ->sum('note_reference_critere_evaluation');

            if ($totalNotes + $critere->note_reference_critere_evaluation > 100) {
                throw new Exception("La duplication dépasserait le total de 100 points");
            }

            // Créer une copie
            $nouveauCritere = $critere->replicate();
            $nouveauCritere->numero_critere_evaluation = CritereEvaluation::genererNumeroCritere($lotId);
            $nouveauCritere->libelle_critere_evaluation = $critere->libelle_critere_evaluation . ' (Copie)';
            $nouveauCritere->ordre_execution_critere_evaluation = CritereEvaluation::where('lot_id', $lotId)
                ->max('ordre_execution_critere_evaluation') + 1;
            $nouveauCritere->created_by = auth()->id();
            $nouveauCritere->save();

            DB::commit();

            Log::info("Critère d'évaluation dupliqué", [
                'original' => $id,
                'nouveau' => $nouveauCritere->id_critere_evaluation
            ]);

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => $nouveauCritere->load('lot'),
                    'message' => 'Critère dupliqué avec succès'
                ], 201);
            }

            return redirect()->route('criteres-evaluations.edit', [
                'appel_offre' => $appelOffreId,
                'lot' => $lotId,
                'critere' => $nouveauCritere->id_critere_evaluation
            ])
                ->with('success', 'Critère dupliqué avec succès');
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

            return back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }



    /**
     * Mise à jour en masse des ordres des critères
     * Utile pour le drag-and-drop avec sauvegarde optimisée
     */
    public function reordonnerBatch(Request $request, $appelOffreId, $lotId)
    {
        $validator = Validator::make($request->all(), [
            'ordres' => 'required|array|min:1',
            'ordres.*.id' => 'required|string',
            'ordres.*.ordre' => 'required|integer|min:1',
        ], [
            'ordres.required' => 'La liste des ordres est obligatoire',
            'ordres.array' => 'Les ordres doivent être un tableau',
            'ordres.*.id.required' => 'L\'identifiant du critère est obligatoire',
            'ordres.*.ordre.required' => 'L\'ordre est obligatoire',
            'ordres.*.ordre.min' => 'L\'ordre doit être au moins 1',
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
            // Validation de hiérarchie
            $lot = $this->getLotWithValidation($appelOffreId, $lotId);

            if ($lot->isAttribue()) {
                throw new Exception("Impossible de réordonner les critères d'un lot déjà attribué");
            }

            if ($lot->isRetire()) {
                throw new Exception("Impossible de réordonner les critères d'un lot retiré");
            }

            // Mise à jour de chaque critère
            foreach ($request->ordres as $item) {
                CritereEvaluation::where('lot_id', $lotId)
                    ->where('id_critere_evaluation', $item['id'])
                    ->update([
                        'ordre_execution_critere_evaluation' => $item['ordre'],
                        'updated_by' => auth()->id()
                    ]);
            }

            DB::commit();

            Log::info("Réordonnancement en masse des critères", [
                'lot_id' => $lotId,
                'count' => count($request->ordres)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Ordres mis à jour avec succès',
                'count' => count($request->ordres)
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors du réordonnancement en masse: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du réordonnancement',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Permute l'ordre entre deux critères
     * Optimisé pour les boutons monter/descendre
     */
    public function permuter(Request $request, $appelOffreId, $lotId)
    {
        $validator = Validator::make($request->all(), [
            'critere_1' => 'required|string',
            'critere_2' => 'required|string',
        ], [
            'critere_1.required' => 'Le premier critère est obligatoire',
            'critere_2.required' => 'Le second critère est obligatoire',
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
            // Validation de hiérarchie
            $lot = $this->getLotWithValidation($appelOffreId, $lotId);

            if ($lot->isAttribue()) {
                throw new Exception("Impossible de permuter les critères d'un lot déjà attribué");
            }

            // Récupérer les deux critères
            $critere1 = CritereEvaluation::where('lot_id', $lotId)
                ->where('id_critere_evaluation', $request->critere_1)
                ->firstOrFail();

            $critere2 = CritereEvaluation::where('lot_id', $lotId)
                ->where('id_critere_evaluation', $request->critere_2)
                ->firstOrFail();

            // Permuter les ordres
            $ordre1 = $critere1->ordre_execution_critere_evaluation;
            $ordre2 = $critere2->ordre_execution_critere_evaluation;

            $critere1->update([
                'ordre_execution_critere_evaluation' => $ordre2,
                'updated_by' => auth()->id()
            ]);

            $critere2->update([
                'ordre_execution_critere_evaluation' => $ordre1,
                'updated_by' => auth()->id()
            ]);

            DB::commit();

            Log::info("Permutation de critères", [
                'critere_1' => $request->critere_1,
                'critere_2' => $request->critere_2
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Critères permutés avec succès',
                'data' => [
                    'critere_1' => [
                        'id' => $critere1->id_critere_evaluation,
                        'nouvel_ordre' => $ordre2
                    ],
                    'critere_2' => [
                        'id' => $critere2->id_critere_evaluation,
                        'nouvel_ordre' => $ordre1
                    ]
                ]
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la permutation: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la permutation',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
