<?php

namespace App\Http\Controllers;

use App\Models\AppelOffre;
use App\Models\TypeAppelOffre;
use App\Models\CaracteristiqueAppelOffre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Exception;

class AppelOffreController extends Controller
{

    /**
     * Affiche la liste des appels d'offres
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
                $query->where(function($q) use ($search) {
                    $q->where('numero_appel_offre', 'like', "%{$search}%")
                      ->orWhere('libelle_critere_appel_offre', 'like', "%{$search}%")
                      ->orWhere('objet_critere_appel_offre', 'like', "%{$search}%");
                });
            }

            // Filtres de dates
            if ($request->filled('date_debut') && $request->filled('date_fin')) {
                $query->whereBetween('date_publication_critere_appel_offre', [
                    $request->date_debut,
                    $request->date_fin
                ]);
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
     */
    public function store(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'type_appel_offre_id' => 'required|exists:types_appels_offres,id_type_appel_offre',
            'libelle_critere_appel_offre' => 'required|string|max:160',
            // 'numero_appel_offre' => 'required|string|unique:appels_offres,numero_appel_offre',
            'objet_critere_appel_offre' => 'required|string',
            'montant_global_appel_offre' => 'required|numeric|min:0',
            'description_critere_critere_appel_offre' => 'required|string',
            'date_publication_critere_appel_offre' => 'nullable|date',
            'date_limite_depot_critere_appel_offre' => 'required|date|after:date_publication_critere_appel_offre',
            'date_ouverture_plis_critere_appel_offre' => 'nullable|date|after:date_limite_depot_critere_appel_offre',
            'conditions_participation_critere_appel_offre' => 'nullable|string',
            'criteres_selection_critere_appel_offre' => 'nullable|string',
        ], [
            'type_appel_offre_id.required' => 'Le type d\'appel d\'offres est obligatoire',
            'type_appel_offre_id.exists' => 'Type d\'appel d\'offres invalide',
            'libelle_critere_appel_offre.required' => 'Le libellé est obligatoire',
            // 'numero_appel_offre.required' => 'Le numéro est obligatoire',
            // 'numero_appel_offre.unique' => 'Le numéro est déjà utilisé.',
            'objet_critere_appel_offre.required' => 'L\'objet est obligatoire',
            'montant_global_appel_offre.required' => 'Le montant global est obligatoire',
            'date_limite_depot_critere_appel_offre.required' => 'La date limite de dépôt est obligatoire',
            'date_limite_depot_critere_appel_offre.after' => 'La date limite doit être après la date de publication',
            'date_ouverture_plis_critere_appel_offre.after' => 'La date d\'ouverture doit être après la date limite',
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
                'libelle_critere_appel_offre' => $request->libelle_critere_appel_offre,
                'objet_critere_appel_offre' => $request->objet_critere_appel_offre,
                'montant_global_appel_offre' => $request->montant_global_appel_offre,
                'description_critere_critere_appel_offre' => $request->description_critere_critere_appel_offre,
                'date_publication_critere_appel_offre' => $request->date_publication_critere_appel_offre ?? now(),
                'date_limite_depot_critere_appel_offre' => $request->date_limite_depot_critere_appel_offre,
                'date_ouverture_plis_critere_appel_offre' => $request->date_ouverture_plis_critere_appel_offre,
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
     */
    public function show(Request $request, $id)
    {

        try {
            $appelOffre = AppelOffre::with([
                'typeAppelOffre',
                'caracteristiques' => function($q) {
                    $q->latest('version_caracteristique_appel_offre');
                },
                'caracteristiqueActive',
                'lots' => function($q) {

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
                        'jours_restants' => $appelOffre->joursRestants(),
                        'est_actif' => $appelOffre->isActif(),
                        'est_en_cours' => $appelOffre->isEnCours(),
                        'est_cloture' => $appelOffre->isCloture(),
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
     */
    public function update(Request $request, $id)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'libelle_critere_appel_offre' => 'required|string|max:160',
            'objet_critere_appel_offre' => 'required|string',
            'montant_global_appel_offre' => 'required|numeric|min:0',
            'description_critere_critere_appel_offre' => 'required|string',
            'date_publication_critere_appel_offre' => 'nullable|date',
            'date_limite_depot_critere_appel_offre' => 'required|date',
            'date_ouverture_plis_critere_appel_offre' => 'nullable|date|after:date_limite_depot_critere_appel_offre',
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
                'libelle_critere_appel_offre' => $request->libelle_critere_appel_offre,
                'objet_critere_appel_offre' => $request->objet_critere_appel_offre,
                'montant_global_appel_offre' => $request->montant_global_appel_offre,
                'description_critere_critere_appel_offre' => $request->description_critere_critere_appel_offre,
                'date_publication_critere_appel_offre' => $request->date_publication_critere_appel_offre,
                'date_limite_depot_critere_appel_offre' => $request->date_limite_depot_critere_appel_offre,
                'date_ouverture_plis_critere_appel_offre' => $request->date_ouverture_plis_critere_appel_offre,
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
     */
    public function publier(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $appelOffre = AppelOffre::findOrFail($id);

            if ($appelOffre->date_publication_critere_appel_offre) {
                throw new Exception('Cet appel d\'offres est déjà publié');
            }

            $appelOffre->date_publication_critere_appel_offre = now();
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
     * Clôture un appel d'offres
     */
    public function cloturer(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $appelOffre = AppelOffre::findOrFail($id);

            if ($appelOffre->isCloture()) {
                throw new Exception('Cet appel d\'offres est déjà clôturé');
            }

            $appelOffre->date_limite_depot_critere_appel_offre = now();
            $appelOffre->statut_evaluation_critere_appel_offre = 0;
            $appelOffre->updated_by = auth()->id();
            $appelOffre->save();

            DB::commit();

            Log::info("Appel d'offres clôturé", ['id' => $id]);

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => $appelOffre,
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
                    'message' => 'Erreur lors de la clôture',
                    'error' => $e->getMessage()
                ], 500);
            }

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Obtient les statistiques d'un appel d'offres
     */
    public function statistiques(Request $request, $id)
    {
        try {
            $appelOffre = AppelOffre::with(['lots', 'lots.attributionActive'])->findOrFail($id);

            $stats = [
                'general' => [
                    'numero' => $appelOffre->numero_appel_offre,
                    'montant_global' => $appelOffre->montant_global_appel_offre,
                    'jours_restants' => $appelOffre->joursRestants(),
                    'est_actif' => $appelOffre->isActif(),
                    'est_en_cours' => $appelOffre->isEnCours(),
                    'est_cloture' => $appelOffre->isCloture(),
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
            $nouveauAO->date_publication_critere_appel_offre = null;
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
}
