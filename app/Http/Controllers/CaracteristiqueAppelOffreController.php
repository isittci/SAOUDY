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

class CaracteristiqueAppelOffreController extends Controller
{
    /**
     * Affiche la liste des caractéristiques d'un appel d'offres
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
            if ($request->wantsJson() || $request->is('api/*')) {
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
     * NOTE: duree_estimee_jours_caracteristique_appel_offre sera calculée automatiquement par le modèle
     */
    public function store(Request $request, $appelOffreId)
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
            'penalites_retard_journalier_caracteristique_appel_offre' => 'nullable|numeric|min:0',
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
            'penalites_retard_journalier_caracteristique_appel_offre.numeric' => 'Le montant des pénalités doit être numérique',
            'montant_garantie_caracteristique_appel_offre.numeric' => 'Le montant de garantie doit être numérique',
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
            $appelOffre = AppelOffre::findOrFail($appelOffreId);

            // Créer la caractéristique - SANS duree_estimee_jours (calculée automatiquement)
            $caracteristique = CaracteristiqueAppelOffre::create([
                'appel_offre_id' => $appelOffreId,
                'version_caracteristique_appel_offre' => 1,
                'date_demarrage_prevue_caracteristique_appel_offre' => $request->date_demarrage_prevue_caracteristique_appel_offre,
                'date_livraison_previsionnelle_caracteristique_appel_offre' => $request->date_livraison_previsionnelle_caracteristique_appel_offre,
                'lieu_execution_caracteristique_appel_offre' => $request->lieu_execution_caracteristique_appel_offre,
                'penalites_retard_journalier_caracteristique_appel_offre' => $request->penalites_retard_journalier_caracteristique_appel_offre,
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

            if ($request->wantsJson() || $request->is('api/*')) {
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
     * Affiche les détails d'une caractéristique
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

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => $caracteristique,
                    'message' => 'Détails de la caractéristique récupérés avec succès'
                ]);
            }

            return view('caracteristiques-appels-offres.show', compact('caracteristique', 'appelOffre'));
        } catch (Exception $e) {
            Log::error('Erreur lors de la récupération de la caractéristique: ' . $e->getMessage());

            if ($request->wantsJson() || $request->is('api/*')) {
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
     * NOTE: duree_estimee_jours_caracteristique_appel_offre sera calculée automatiquement
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
            'penalites_retard_journalier_caracteristique_appel_offre' => 'nullable|numeric|min:0',
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
            $caracteristique = CaracteristiqueAppelOffre::where('appel_offre_id', $appelOffreId)
                ->where('id_caracteristique_appel_offre', $id)
                ->firstOrFail();

            // Créer une nouvelle version - SANS duree_estimee_jours (calculée automatiquement)
            $nouvelleVersion = $caracteristique->creerNouvelleVersion([
                'date_demarrage_prevue_caracteristique_appel_offre' => $request->date_demarrage_prevue_caracteristique_appel_offre,
                'date_livraison_previsionnelle_caracteristique_appel_offre' => $request->date_livraison_previsionnelle_caracteristique_appel_offre,
                'lieu_execution_caracteristique_appel_offre' => $request->lieu_execution_caracteristique_appel_offre,
                'penalites_retard_journalier_caracteristique_appel_offre' => $request->penalites_retard_journalier_caracteristique_appel_offre,
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

            if ($request->wantsJson() || $request->is('api/*')) {
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
     * Supprime une caractéristique (soft delete)
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

            if ($request->wantsJson() || $request->is('api/*')) {
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

    // /**
    //  * Obtient l'historique des versions
    //  */
    // public function historique(Request $request, $appelOffreId, $caracteristiqueId)
    // {

    //     try {
    //         $appelOffre = AppelOffre::findOrFail($appelOffreId);

    //         // Récupérer la caractéristique (version de base, sans parent)
    //         $caracteristique = CaracteristiqueAppelOffre::where('appel_offre_id', $appelOffreId)
    //             ->where('id_caracteristique_appel_offre', $caracteristiqueId)
    //             ->whereNull('parent_id')
    //             ->firstOrFail();

    //         $historique = $caracteristique->getHistorique();

    //         if ($request->wantsJson() || $request->is('api/*')) {
    //             return response()->json([
    //                 'success' => true,
    //                 'data' => $historique,
    //                 'message' => 'Historique récupéré avec succès'
    //             ]);
    //         }

    //         return view('caracteristiques-appels-offres.historique', compact('historique', 'caracteristique', 'appelOffre'));

    //     } catch (ModelNotFoundException $e) {
    //         Log::error('Caractéristique non trouvée: ' . $e->getMessage());

    //         if ($request->wantsJson() || $request->is('api/*')) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Caractéristique non trouvée'
    //             ], 404);
    //         }

    //         return back()->with('error', 'Caractéristique non trouvée');

    //     } catch (Exception $e) {
    //         Log::error('Erreur lors de la récupération de l\'historique: ' . $e->getMessage());

    //         if ($request->wantsJson() || $request->is('api/*')) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Erreur lors de la récupération de l\'historique',
    //                 'error' => $e->getMessage()
    //             ], 500);
    //         }

    //         return back()->with('error', 'Erreur lors de la récupération de l\'historique');
    //     }
    // }

    /**
     * Obtient l'historique des versions
     */

    // public function historique(Request $request, $appelOffreId, $caracteristiqueId)
    // {
    //     try {
    //         $appelOffre = AppelOffre::findOrFail($appelOffreId);

    //         // Récupérer n'importe quelle version de la caractéristique
    //         $caracteristique = CaracteristiqueAppelOffre::where('appel_offre_id', $appelOffreId)
    //             ->where('id_caracteristique_appel_offre', $caracteristiqueId)
    //             ->firstOrFail();

    //         // getHistorique() gère automatiquement le cas où c'est une version
    //         $historique = $caracteristique->getHistorique();

    //         // Récupérer la version de base pour l'affichage
    //         $versionBase = $caracteristique->parent_id
    //             ? CaracteristiqueAppelOffre::find($caracteristique->parent_id)
    //             : $caracteristique;

    //         if ($request->wantsJson() || $request->is('api/*')) {
    //             return response()->json([
    //                 'success' => true,
    //                 'data' => $historique,
    //                 'version_base' => $versionBase,
    //                 'message' => 'Historique récupéré avec succès'
    //             ]);
    //         }

    //         return view('caracteristiques-appels-offres.historique', compact('historique', 'versionBase', 'appelOffre'));
    //     } catch (ModelNotFoundException $e) {
    //         Log::error('Caractéristique non trouvée: ' . $e->getMessage());

    //         if ($request->wantsJson() || $request->is('api/*')) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Caractéristique non trouvée'
    //             ], 404);
    //         }

    //         return back()->with('error', 'Caractéristique non trouvée');
    //     } catch (Exception $e) {
    //         Log::error('Erreur lors de la récupération de l\'historique: ' . $e->getMessage());

    //         if ($request->wantsJson() || $request->is('api/*')) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Erreur lors de la récupération de l\'historique',
    //                 'error' => $e->getMessage()
    //             ], 500);
    //         }

    //         return back()->with('error', 'Erreur lors de la récupération de l\'historique');
    //     }
    // }


    /**
 * Obtient l'historique des versions
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

        if ($request->wantsJson() || $request->is('api/*')) {
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

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => false,
                'message' => 'Caractéristique non trouvée'
            ], 404);
        }

        return back()->with('error', 'Caractéristique non trouvée');

    } catch (Exception $e) {
        Log::error('Erreur lors de la récupération de l\'historique: ' . $e->getMessage());

        if ($request->wantsJson() || $request->is('api/*')) {
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
     * NOTE: La durée sera recalculée automatiquement
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
                    'penalites_retard_journalier_caracteristique_appel_offre',
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

            if ($request->wantsJson() || $request->is('api/*')) {
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

            if ($request->wantsJson() || $request->is('api/*')) {
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
