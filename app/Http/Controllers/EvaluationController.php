<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Lot;
use App\Models\Evaluation;
use App\Models\PrestataireLot;
use App\Models\CritereEvaluation;
use App\Models\EvaluationLotPrestataire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Contrôleur des évaluations adapté à la nouvelle logique:
 * - Une évaluation = UN critère d'évaluation
 * - Plusieurs évaluations partielles possibles pour un même critère
 * - La somme doit atteindre la note de référence avant de terminer/valider
 * - Responsables obligatoires: technique, superviseur, évaluateur
 */
class EvaluationController extends Controller
{
    /**
     * Affiche la liste de toutes les évaluations
     */
    public function index(Request $request)
    {
        try {
            $query = Evaluation::with([
                    'attribution.lot.appelOffre',
                    'attribution.prestataire',
                    'critereEvaluation',
                    'evaluateurPrincipal',
                    'creator'
                ])
                ->current();

            // Filtres
            if ($request->filled('statut')) {
                $query->where('statut_evaluation', $request->statut);
            }

            if ($request->filled('lot_id')) {
                $query->whereHas('attribution', function ($q) use ($request) {
                    $q->where('lot_id', $request->lot_id);
                });
            }

            if ($request->filled('prestataire_id')) {
                $query->whereHas('attribution', function ($q) use ($request) {
                    $q->where('prestataire_id', $request->prestataire_id);
                });
            }

            if ($request->filled('critere_id')) {
                $query->where('critere_evaluation_id', $request->critere_id);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('numero_evaluation', 'like', "%{$search}%")
                      ->orWhereHas('attribution.prestataire', function ($q2) use ($search) {
                          $q2->where('raison_sociale_prestataire', 'like', "%{$search}%");
                      })
                      ->orWhereHas('attribution.lot', function ($q2) use ($search) {
                          $q2->where('numero', 'like', "%{$search}%")
                             ->orWhere('libelle', 'like', "%{$search}%");
                      })
                      ->orWhereHas('critereEvaluation', function ($q2) use ($search) {
                          $q2->where('libelle_critere_evaluation', 'like', "%{$search}%")
                             ->orWhere('numero_critere_evaluation', 'like', "%{$search}%");
                      });
                });
            }

            // Tri
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 15);
            $evaluations = $query->paginate($perPage);

            // Statistiques
            $stats = [
                'total' => Evaluation::current()->count(),
                'en_attente' => Evaluation::current()->enAttente()->count(),
                'en_cours' => Evaluation::current()->enCours()->count(),
                'terminees' => Evaluation::current()->terminee()->count(),
                'validees' => Evaluation::current()->validee()->count(),
                'rejetees' => Evaluation::current()->rejetee()->count(),
            ];

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $evaluations,
                    'stats' => $stats,
                ]);
            }

            return view('evaluations.index', compact('evaluations', 'stats'));

        } catch (Exception $e) {
            Log::error('Erreur lors de la récupération des évaluations: ' . $e->getMessage());

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la récupération des données',
                ], 500);
            }

            return back()->with('error', 'Erreur lors de la récupération des données');
        }
    }

    /**
     * Affiche les évaluations d'une attribution spécifique
     * Avec la nouvelle logique: groupées par critère
     */
    public function pourAttribution(Request $request, $attributionId)
    {
        try {
            $attribution = PrestataireLot::with([
                    'lot.appelOffre',
                    'lot.criteresEvaluation' => function ($q) {
                        $q->actif()->ordonne();
                    },
                    'prestataire',
                    'proforma'
                ])
                ->findOrFail($attributionId);

            // Statistiques par critère
            $statistiquesCriteres = Evaluation::statistiquesCriterePourAttribution($attributionId);


            // Toutes les évaluations pour cette attribution
            $evaluations = Evaluation::with(['critereEvaluation', 'evaluateurPrincipal', 'validateur', 'creator'])
                ->pourAttribution($attributionId)
                ->current()
                ->orderBy('created_at', 'desc')
                ->get();

            // Critères disponibles pour nouvelles évaluations
            $criteresDisponibles = [];
            foreach ($attribution->lot->criteresEvaluation as $critere) {
                if (Evaluation::peutCreerEvaluationPourCritere($critere->id_critere_evaluation, $attributionId)) {
                    $criteresDisponibles[] = [
                        'critere' => $critere,
                        'reste_a_evaluer' => Evaluation::getResteAEvaluerPourCritere(
                            $critere->id_critere_evaluation,
                            $attributionId
                        ),
                    ];
                }
            }

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'attribution' => $attribution,
                    'evaluations' => $evaluations,
                    'statistiques_criteres' => $statistiquesCriteres,
                    'criteres_disponibles' => $criteresDisponibles,
                ]);
            }

            return view('evaluations.pour-attribution', compact(
                'attribution',
                'evaluations',
                'statistiquesCriteres',
                'criteresDisponibles'
            ));

        } catch (ModelNotFoundException $e) {
            // dd($e->getMessage());
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Attribution introuvable',
                ], 404);
            }

            return back()->with('error', 'Attribution introuvable');
        } catch (Exception $e) {
            dd($e->getMessage(), 2);
            Log::error('Erreur: ' . $e->getMessage());

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la récupération des données',
                ], 500);
            }

            return back()->with('error', 'Erreur lors de la récupération des données');
        }
    }

    /**
     * Affiche le formulaire de création d'une évaluation pour un critère
     */
    public function create(Request $request, $attributionId)
    {
        try {
            $attribution = PrestataireLot::with([
                    'lot.appelOffre',
                    'lot.criteresEvaluation' => function ($q) {
                        $q->actif()->ordonne();
                    },
                    'prestataire',
                    'proforma'
                ])
                ->findOrFail($attributionId);

            // Récupérer les critères avec le reste à évaluer
            $criteresAvecReste = [];
            foreach ($attribution->lot->criteresEvaluation as $critere) {
                $totalEvalue = Evaluation::getTotalEvaluePourCritere(
                    $critere->id_critere_evaluation,
                    $attributionId
                );
                $resteAEvaluer = max(0, $critere->note_reference_critere_evaluation - $totalEvalue);

                $criteresAvecReste[] = [
                    'critere' => $critere,
                    'total_evalue' => $totalEvalue,
                    'reste_a_evaluer' => $resteAEvaluer,
                    'peut_evaluer' => $resteAEvaluer > 0,
                    'pourcentage_complete' => $critere->note_reference_critere_evaluation > 0
                        ? ($totalEvalue / $critere->note_reference_critere_evaluation * 100)
                        : 0,
                ];
            }

            // Si un critère spécifique est demandé
            $critereSelectionne = null;
            $resteAEvaluer = null;
            if ($request->has('critere_id')) {
                $critereSelectionne = CritereEvaluation::find($request->critere_id);
                if ($critereSelectionne) {
                    $resteAEvaluer = Evaluation::getResteAEvaluerPourCritere(
                        $request->critere_id,
                        $attributionId
                    );

                    // Vérifier qu'on peut encore évaluer ce critère
                    if ($resteAEvaluer <= 0) {
                        return back()->with('warning', 'Ce critère a déjà été complètement évalué');
                    }
                }
            }

            return view('evaluations.create', compact(
                'attribution',
                'criteresAvecReste',
                'critereSelectionne',
                'resteAEvaluer'
            ));

        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'Attribution introuvable');
        } catch (Exception $e) {
            Log::error('Erreur: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors du chargement du formulaire');
        }
    }

    /**
     * Enregistre une nouvelle évaluation pour un critère
     */
    public function store(Request $request, $attributionId)
    {
        $validator = Validator::make($request->all(), [
            'critere_id' => 'required|uuid|exists:criteres_evaluations,id_critere_evaluation',
            'resultat_evaluation' => 'required|numeric|min:0',
            'respo_technique' => 'required|array',
            'respo_technique.nom_complet' => 'required|string|max:255',
            'respo_technique.email' => 'nullable|email|max:255',
            'respo_technique.telephone' => 'nullable|string|max:20',
            'superviseur' => 'required|array',
            'superviseur.nom_complet' => 'required|string|max:255',
            'superviseur.email' => 'nullable|email|max:255',
            'superviseur.telephone' => 'nullable|string|max:20',
            'evalue_par' => 'required|array',
            'evalue_par.nom_complet' => 'required|string|max:255',
            'evalue_par.email' => 'nullable|email|max:255',
            'evalue_par.telephone' => 'nullable|string|max:20',
            'commentaire_general' => 'nullable|string',
            'observation' => 'nullable|string',
            'justification' => 'nullable|string',
        ], [
            'critere_id.required' => 'Le critère d\'évaluation est obligatoire',
            'resultat_evaluation.required' => 'Le résultat de l\'évaluation est obligatoire',
            'resultat_evaluation.min' => 'Le résultat ne peut pas être négatif',
            'respo_technique.nom_complet.required' => 'Le nom du responsable technique est obligatoire',
            'superviseur.nom_complet.required' => 'Le nom du superviseur est obligatoire',
            'evalue_par.nom_complet.required' => 'Le nom de l\'évaluateur est obligatoire',
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $attribution = PrestataireLot::with(['lot', 'prestataire'])
                ->findOrFail($attributionId);

            $critere = CritereEvaluation::findOrFail($request->critere_id);

            // Vérifier qu'on peut encore évaluer ce critère
            $resteAEvaluer = Evaluation::getResteAEvaluerPourCritere(
                $critere->id_critere_evaluation,
                $attributionId
            );

            if ($resteAEvaluer <= 0) {
                throw new Exception('Ce critère a déjà été complètement évalué');
            }

            // Vérifier que le résultat ne dépasse pas le reste à évaluer
            $resultat = floatval($request->resultat_evaluation);
            if ($resultat > $resteAEvaluer) {
                throw new Exception("Le résultat ({$resultat}) ne peut pas dépasser le reste à évaluer ({$resteAEvaluer})");
            }

            // Créer l'évaluation
            $evaluation = Evaluation::creerPourAttributionCritere(
                $attribution,
                $critere,
                $resultat,
                [
                    'respo_technique' => $request->respo_technique,
                    'superviseur' => $request->superviseur,
                    'evalue_par' => $request->evalue_par,
                ],
                Auth::id()
            );

            // Mettre à jour les champs additionnels
            $evaluation->update([
                'commentaire_general' => $request->commentaire_general,
            ]);

            // Mettre à jour l'entrée pivot si elle existe
            $noteCritere = EvaluationLotPrestataire::where('evaluation_id', $evaluation->id_evaluation)->first();
            if ($noteCritere) {
                $noteCritere->update([
                    'observation' => $request->observation,
                    'justification' => $request->justification,
                ]);
            }

            DB::commit();

            Log::info('Évaluation créée', [
                'id' => $evaluation->id_evaluation,
                'critere' => $critere->numero_critere_evaluation,
                'resultat' => $resultat,
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $evaluation->load(['critereEvaluation', 'notesCriteres']),
                    'message' => 'Évaluation créée avec succès',
                    'reste_a_evaluer' => Evaluation::getResteAEvaluerPourCritere(
                        $critere->id_critere_evaluation,
                        $attributionId
                    ),
                ], 201);
            }

            // Rediriger vers la page de l'attribution pour voir toutes les évaluations
            return redirect()
                ->route('evaluations.pour-attribution', $attributionId)
                ->with('success', 'Évaluation créée avec succès');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur création évaluation: ' . $e->getMessage());

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la création',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Erreur: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Affiche les détails d'une évaluation
     */
    public function show(Request $request, $id)
    {
        try {
            $evaluation = Evaluation::with([
                    'attribution.lot.appelOffre',
                    'attribution.lot.criteresEvaluation',
                    'attribution.prestataire',
                    'attribution.proforma',
                    'critereEvaluation',
                    'notesCriteres.critereEvaluation',
                    'evaluateurPrincipal',
                    'validateur',
                    'rejeteur',
                    'creator',
                    'parent',
                    'versions'
                ])
                ->findOrFail($id);

            // Historique des versions
            $historiqueVersions = $evaluation->getHistoriqueVersions();

            // Autres évaluations pour le même critère
            $autresEvaluationsCritere = Evaluation::where('critere_evaluation_id', $evaluation->critere_evaluation_id)
                ->where('attribution_id', $evaluation->attribution_id)
                ->where('id_evaluation', '!=', $evaluation->id_evaluation)
                ->where('is_current', true)
                ->with(['creator', 'validateur'])
                ->orderBy('created_at', 'asc')
                ->get();
// dd($autresEvaluationsCritere);
            // Statistiques du critère
            $totalEvalueCritere = Evaluation::getTotalEvaluePourCritere(
                $evaluation->critere_evaluation_id,
                $evaluation->attribution_id
            );
            $noteReferenceCritere = $evaluation->note_reference_critere;
            $resteAEvaluer = max(0, $noteReferenceCritere - $totalEvalueCritere);

            // Raisons si non terminable
            $raisonsNonTerminable = $evaluation->raisons_non_terminable;

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $evaluation,
                    'historique_versions' => $historiqueVersions,
                    'autres_evaluations_critere' => $autresEvaluationsCritere,
                    'statistiques_critere' => [
                        'total_evalue' => $totalEvalueCritere,
                        'note_reference' => $noteReferenceCritere,
                        'reste_a_evaluer' => $resteAEvaluer,
                        'pourcentage' => $noteReferenceCritere > 0 ? ($totalEvalueCritere / $noteReferenceCritere * 100) : 0,
                    ],
                    'raisons_non_terminable' => $raisonsNonTerminable,
                ]);
            }

            return view('evaluations.show', compact(
                'evaluation',
                'historiqueVersions',
                'autresEvaluationsCritere',
                'totalEvalueCritere',
                'noteReferenceCritere',
                'resteAEvaluer',
                'raisonsNonTerminable'
            ));

        } catch (ModelNotFoundException $e) {
            // dd($e->getMessage(), 40);
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Évaluation introuvable',
                ], 404);
            }

            return back()->with('error', 'Évaluation introuvable');
        } catch (Exception $e) {
            Log::error('Erreur: ' . $e->getMessage());

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la récupération des données',
                ], 500);
            }

            return back()->with('error', 'Erreur lors de la récupération des données');
        }
    }

    /**
     * Affiche le formulaire de modification
     */
    public function edit(Request $request, $id)
    {
        try {
            $evaluation = Evaluation::with([
                    'attribution.lot.appelOffre',
                    'attribution.prestataire',
                    'critereEvaluation',
                    'notesCriteres.critereEvaluation',
                ])
                ->findOrFail($id);

            if (!$evaluation->peutEtreModifiee()) {
                return back()->with('error', 'Cette évaluation ne peut plus être modifiée');
            }

            // Calculer le reste à évaluer (en excluant cette évaluation)
            $totalAutres = Evaluation::where('critere_evaluation_id', $evaluation->critere_evaluation_id)
                ->where('attribution_id', $evaluation->attribution_id)
                ->where('id_evaluation', '!=', $evaluation->id_evaluation)
                ->where('is_current', true)
                ->whereIn('statut_evaluation', [
                    Evaluation::STATUT_EN_COURS,
                    Evaluation::STATUT_TERMINEE,
                    Evaluation::STATUT_VALIDEE
                ])
                ->sum('resultat_evaluation');


                // dd($totalAutres);

            $noteReference = $evaluation->note_reference_critere;
            $maxModifiable = $noteReference - $totalAutres;

            return view('evaluations.edit', compact(
                'evaluation',
                'maxModifiable',
                'noteReference',
                'totalAutres'
            ));

        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'Évaluation introuvable');
        } catch (Exception $e) {
            Log::error('Erreur: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors du chargement du formulaire');
        }
    }

    /**
     * Met à jour une évaluation
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'resultat_evaluation' => 'required|numeric|min:0',
            'respo_technique' => 'required|array',
            'respo_technique.nom_complet' => 'required|string|max:255',
            'respo_technique.email' => 'nullable|email|max:255',
            'respo_technique.telephone' => 'nullable|string|max:20',
            'superviseur' => 'required|array',
            'superviseur.nom_complet' => 'required|string|max:255',
            'superviseur.email' => 'nullable|email|max:255',
            'superviseur.telephone' => 'nullable|string|max:20',
            'evalue_par' => 'required|array',
            'evalue_par.nom_complet' => 'required|string|max:255',
            'evalue_par.email' => 'nullable|email|max:255',
            'evalue_par.telephone' => 'nullable|string|max:20',
            'commentaire_general' => 'nullable|string',
            'recommandation' => 'nullable|string',
            'observation' => 'nullable|string',
            'justification' => 'nullable|string',
        ], [
            'resultat_evaluation.required' => 'Le résultat de l\'évaluation est obligatoire',
            'resultat_evaluation.min' => 'Le résultat ne peut pas être négatif',
            'respo_technique.nom_complet.required' => 'Le nom du responsable technique est obligatoire',
            'superviseur.nom_complet.required' => 'Le nom du superviseur est obligatoire',
            'evalue_par.nom_complet.required' => 'Le nom de l\'évaluateur est obligatoire',
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $evaluation = Evaluation::findOrFail($id);

            if (!$evaluation->peutEtreModifiee()) {
                throw new Exception('Cette évaluation ne peut plus être modifiée');
            }

            // Calculer le max modifiable
            $totalAutres = Evaluation::where('critere_evaluation_id', $evaluation->critere_evaluation_id)
                ->where('attribution_id', $evaluation->attribution_id)
                ->where('id_evaluation', '!=', $evaluation->id_evaluation)
                ->where('is_current', true)
                ->whereIn('statut_evaluation', [
                    Evaluation::STATUT_EN_COURS,
                    Evaluation::STATUT_TERMINEE,
                    Evaluation::STATUT_VALIDEE
                ])
                ->sum('resultat_evaluation');

            $noteReference = $evaluation->note_reference_critere;
            $maxModifiable = $noteReference - $totalAutres;

            $resultat = floatval($request->resultat_evaluation);
            if ($resultat > $maxModifiable) {
                throw new Exception("Le résultat ({$resultat}) ne peut pas dépasser {$maxModifiable} (note référence: {$noteReference}, autres évaluations: {$totalAutres})");
            }

            // Mettre à jour l'évaluation
            $evaluation->update([
                'resultat_evaluation' => $resultat,
                'respo_technique_evaluation' => $request->respo_technique,
                'superviseur_evaluation' => $request->superviseur,
                'evalue_par' => $request->evalue_par,
                'commentaire_general' => $request->commentaire_general,
                'recommandation' => $request->recommandation,
                'updated_by' => Auth::id(),
            ]);

            // Recalculer la note finale
            $evaluation->calculerNoteFinale();

            // Mettre à jour l'entrée pivot
            $noteCritere = EvaluationLotPrestataire::where('evaluation_id', $id)->first();
            if ($noteCritere) {
                $pourcentage = $noteReference > 0 ? ($resultat / $noteReference * 100) : 0;
                $noteCritere->update([
                    'note_obtenue' => $resultat,
                    'note_finale' => $resultat,
                    'pourcentage' => $pourcentage,
                    'observation' => $request->observation,
                    'justification' => $request->justification,
                    'updated_by' => Auth::id(),
                ]);
            }

            DB::commit();

            Log::info('Évaluation mise à jour', ['id' => $id, 'resultat' => $resultat]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $evaluation->fresh()->load(['critereEvaluation', 'notesCriteres']),
                    'message' => 'Évaluation mise à jour avec succès',
                ]);
            }

            return redirect()
                ->route('evaluations.show', $id)
                ->with('success', 'Évaluation mise à jour avec succès');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur mise à jour évaluation: ' . $e->getMessage());

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la mise à jour',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Erreur: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Démarre une évaluation
     */
    public function demarrer(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $evaluation = Evaluation::findOrFail($id);

            if (!$evaluation->demarrer(Auth::id())) {
                throw new Exception('Impossible de démarrer cette évaluation');
            }

            DB::commit();

            Log::info('Évaluation démarrée', ['id' => $id]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $evaluation->fresh(),
                    'message' => 'Évaluation démarrée avec succès',
                ]);
            }

            return back()->with('success', 'Évaluation démarrée avec succès');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur démarrage: ' . $e->getMessage());

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 500);
            }

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Termine une évaluation avec vérifications complètes
     */
    public function terminer(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $evaluation = Evaluation::with('critereEvaluation')->findOrFail($id);

            // Utiliser la méthode avec vérification complète
            $result = $evaluation->terminerAvecVerification(Auth::id());

            if (!$result['success']) {
                DB::rollBack();

                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $result['message'],
                        'raisons' => $result['raisons'],
                    ], 422);
                }

                // Construire le message d'erreur avec les raisons
                $messageErreur = $result['message'];
                if (!empty($result['raisons'])) {
                    $messageErreur .= ': ' . implode(', ', $result['raisons']);
                }

                return back()->with('error', $messageErreur);
            }

            DB::commit();

            Log::info('Évaluation terminée', ['id' => $id]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $evaluation->fresh(),
                    'message' => 'Évaluation terminée avec succès',
                ]);
            }

            return back()->with('success', 'Évaluation terminée avec succès');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur terminaison: ' . $e->getMessage());

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 500);
            }

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Valide une évaluation avec vérifications complètes
     */
    public function valider(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'motif_validation' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        DB::beginTransaction();
        try {
            $evaluation = Evaluation::with('critereEvaluation')->findOrFail($id);

            // Utiliser la méthode avec vérification complète
            $result = $evaluation->validerAvecVerification($request->motif_validation, Auth::id());

            if (!$result['success']) {
                DB::rollBack();

                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $result['message'],
                        'raisons' => $result['raisons'],
                    ], 422);
                }

                $messageErreur = $result['message'];
                if (!empty($result['raisons'])) {
                    $messageErreur .= ': ' . implode(', ', $result['raisons']);
                }

                return back()->with('error', $messageErreur);
            }

            DB::commit();

            Log::info('Évaluation validée', ['id' => $id]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $evaluation->fresh(),
                    'message' => 'Évaluation validée avec succès',
                ]);
            }

            return back()->with('success', 'Évaluation validée avec succès');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur validation: ' . $e->getMessage());

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 500);
            }

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Rejette une évaluation
     */
    public function rejeter(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'motif_rejet' => 'required|string|min:10|max:1000',
        ], [
            'motif_rejet.required' => 'Le motif de rejet est obligatoire',
            'motif_rejet.min' => 'Le motif doit contenir au moins 10 caractères',
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }
            return back()->withErrors($validator);
        }

        DB::beginTransaction();
        try {
            $evaluation = Evaluation::findOrFail($id);

            if (!$evaluation->rejeter($request->motif_rejet, Auth::id())) {
                throw new Exception('Impossible de rejeter cette évaluation');
            }

            DB::commit();

            Log::info('Évaluation rejetée', ['id' => $id]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $evaluation->fresh(),
                    'message' => 'Évaluation rejetée',
                ]);
            }

            return back()->with('success', 'Évaluation rejetée');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur rejet: ' . $e->getMessage());

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 500);
            }

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Reprend une évaluation rejetée
     */
    public function reprendre(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $evaluation = Evaluation::findOrFail($id);

            if (!$evaluation->reprendre(Auth::id())) {
                throw new Exception('Impossible de reprendre cette évaluation');
            }

            DB::commit();

            Log::info('Évaluation reprise', ['id' => $id]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $evaluation->fresh(),
                    'message' => 'Évaluation reprise avec succès',
                ]);
            }

            return redirect()
                ->route('evaluations.edit', $id)
                ->with('success', 'Évaluation reprise. Vous pouvez maintenant la modifier.');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur reprise: ' . $e->getMessage());

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 500);
            }

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Crée une nouvelle version d'une évaluation
     */
    public function creerVersion(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $evaluation = Evaluation::findOrFail($id);

            $nouvelleVersion = $evaluation->creerNouvelleVersion(Auth::id());

            DB::commit();

            Log::info('Nouvelle version créée', [
                'original' => $id,
                'nouvelle' => $nouvelleVersion->id_evaluation,
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $nouvelleVersion->load(['critereEvaluation', 'notesCriteres']),
                    'message' => 'Nouvelle version créée avec succès',
                ], 201);
            }

            return redirect()
                ->route('evaluations.edit', $nouvelleVersion->id_evaluation)
                ->with('success', 'Nouvelle version créée avec succès');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur création version: ' . $e->getMessage());

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 500);
            }

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Affiche le classement des évaluations pour un lot
     */
    public function classementLot(Request $request, $lotId)
    {
        try {
            $lot = Lot::with(['appelOffre', 'criteresEvaluation'])->findOrFail($lotId);

            $evaluations = Evaluation::with([
                    'attribution.prestataire',
                    'critereEvaluation',
                    'notesCriteres.critereEvaluation',
                    'validateur'
                ])
                ->whereHas('attribution', function ($q) use ($lotId) {
                    $q->where('lot_id', $lotId);
                })
                ->current()
                ->validee()
                ->orderBy('rang', 'asc')
                ->get();

            $stats = Evaluation::statistiquesLot($lotId);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'lot' => $lot,
                    'evaluations' => $evaluations,
                    'stats' => $stats,
                ]);
            }

            return view('evaluations.classement', compact('lot', 'evaluations', 'stats'));

        } catch (ModelNotFoundException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lot introuvable',
                ], 404);
            }

            return back()->with('error', 'Lot introuvable');
        } catch (Exception $e) {
            Log::error('Erreur classement: ' . $e->getMessage());

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la récupération du classement',
                ], 500);
            }

            return back()->with('error', 'Erreur lors de la récupération du classement');
        }
    }

    /**
     * Génère un rapport PDF de l'évaluation
     */
    public function genererRapport(Request $request, $id)
    {
        try {
            $evaluation = Evaluation::with([
                    'attribution.lot.appelOffre',
                    'attribution.prestataire',
                    'critereEvaluation',
                    'notesCriteres.critereEvaluation',
                    'evaluateurPrincipal',
                    'validateur'
                ])
                ->findOrFail($id);

            // Autres évaluations du même critère
            $autresEvaluations = Evaluation::where('critere_evaluation_id', $evaluation->critere_evaluation_id)
                ->where('attribution_id', $evaluation->attribution_id)
                ->where('id_evaluation', '!=', $evaluation->id_evaluation)
                ->where('is_current', true)
                ->get();

            // Données pour le rapport
            $rapport = [
                'evaluation' => $evaluation,
                'attribution' => $evaluation->attribution,
                'lot' => $evaluation->lot,
                'prestataire' => $evaluation->prestataire,
                'appelOffre' => $evaluation->appelOffre,
                'critere' => $evaluation->critereEvaluation,
                'autresEvaluations' => $autresEvaluations,
                'totalEvalueCritere' => Evaluation::getTotalEvaluePourCritere(
                    $evaluation->critere_evaluation_id,
                    $evaluation->attribution_id
                ),
                'genereLe' => now()->format('d/m/Y H:i'),
            ];

            return view('evaluations.rapport', $rapport);

        } catch (Exception $e) {
            Log::error('Erreur génération rapport: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de la génération du rapport');
        }
    }

    /**
     * Supprime une évaluation (soft delete)
     */
    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $evaluation = Evaluation::findOrFail($id);

            // Vérifier que l'évaluation peut être supprimée
            if ($evaluation->isValidee()) {
                throw new Exception('Impossible de supprimer une évaluation validée');
            }

            $evaluation->deleted_by = Auth::id();
            $evaluation->save();
            $evaluation->delete();

            DB::commit();

            Log::info('Évaluation supprimée', ['id' => $id]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Évaluation supprimée avec succès',
                ]);
            }

            return redirect()
                ->route('evaluations.index')
                ->with('success', 'Évaluation supprimée avec succès');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur suppression: ' . $e->getMessage());

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 500);
            }

            return back()->with('error', $e->getMessage());
        }
    }
}
