<?php

namespace App\Http\Controllers;

use App\Models\Paiement;
use App\Models\Facture;
use App\Models\Banque;
use App\Http\Requests\StorePaiementRequest;
use App\Http\Requests\UpdatePaiementRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class PaiementController extends Controller
{

    /**
     * Afficher la liste de tous les paiements (toutes factures confondues).
     */
    public function allPaiements(Request $request)
    {
        try {
            $query = Paiement::with([
                'facture.proforma.prestatairePrincipal.prestataire',
                'facture.proforma.prestataireLotsAttributions.lot.appelOffre',
                'banque',
                'validateur',
                'payeur',
                'createur',
            ]);

            // Filtrage par statut
            if ($request->filled('statut')) {
                $statut = (int) $request->statut;
                $query->where('statut_paiement', $statut);
            }

            // Filtrage par facture
            if ($request->filled('facture_id')) {
                $query->where('facture_id', $request->facture_id);
            }

            // Filtrage par banque
            if ($request->filled('banque_id')) {
                $query->where('banque_id', $request->banque_id);
            }

            // Filtrage par prestataire (via facture -> proforma -> prestataireLot)
            if ($request->filled('prestataire_id')) {
                $query->whereHas('facture.proforma.prestatairePrincipal', function ($q) use ($request) {
                    $q->where('prestataire_id', $request->prestataire_id);
                });
            }

            // Filtrage par période de création
            if ($request->filled('date_debut') && $request->filled('date_fin')) {
                $query->whereBetween('created_at', [
                    $request->date_debut . ' 00:00:00',
                    $request->date_fin . ' 23:59:59'
                ]);
            }

            // Filtrage par période de paiement effectif
            if ($request->filled('date_paiement_debut') && $request->filled('date_paiement_fin')) {
                $query->whereBetween('date_effectif_paiement', [
                    $request->date_paiement_debut . ' 00:00:00',
                    $request->date_paiement_fin . ' 23:59:59'
                ]);
            }

            // Filtrage par montant
            if ($request->filled('montant_min')) {
                $query->where('montant_net_paye_paiement', '>=', $request->montant_min);
            }
            if ($request->filled('montant_max')) {
                $query->where('montant_net_paye_paiement', '<=', $request->montant_max);
            }

            // Recherche globale
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('observations_paiement', 'LIKE', "%{$search}%")
                        ->orWhere('motif_rejet_paiement', 'LIKE', "%{$search}%")
                        ->orWhereHas('facture', function ($subQuery) use ($search) {
                            $subQuery->where('numero_facture', 'LIKE', "%{$search}%");
                        })
                        ->orWhereHas('facture.proforma', function ($subQuery) use ($search) {
                            $subQuery->where('numero_proforma', 'LIKE', "%{$search}%");
                        })
                        ->orWhereHas('banque', function ($subQuery) use ($search) {
                            $subQuery->where('nom_banque', 'LIKE', "%{$search}%")
                                ->orWhere('numero_compte_banque', 'LIKE', "%{$search}%");
                        })
                        ->orWhereHas('facture.proforma.prestatairePrincipal.prestataire', function ($subQuery) use ($search) {
                            $subQuery->where('raison_sociale_prestataire', 'LIKE', "%{$search}%");
                        });
                });
            }

            // Tri
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $allowedSorts = [
                'montant_net_paye_paiement',
                'statut_paiement',
                'date_validation_paiement',
                'date_effectif_paiement',
                'created_at',
                'updated_at'
            ];

            if (in_array($sortBy, $allowedSorts)) {
                $query->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');
            } else {
                $query->orderBy('created_at', 'desc');
            }

            // Pagination
            $perPage = $request->get('per_page', 10);
            $paiements = $query->paginate($perPage)->withQueryString();

            // Statistiques globales
            $stats = [
                'total' => Paiement::count(),
                'en_attente' => Paiement::where('statut_paiement', Paiement::STATUT_EN_ATTENTE)->count(),
                'valides' => Paiement::where('statut_paiement', Paiement::STATUT_VALIDE)->count(),
                'en_traitement' => Paiement::where('statut_paiement', Paiement::STATUT_EN_TRAITEMENT)->count(),
                'payes' => Paiement::where('statut_paiement', Paiement::STATUT_PAYE)->count(),
                'rejetes' => Paiement::where('statut_paiement', Paiement::STATUT_REJETE)->count(),
                'annules' => Paiement::where('statut_paiement', Paiement::STATUT_ANNULE)->count(),
                'montant_total' => Paiement::sum('montant_net_paye_paiement'),
                'montant_paye' => Paiement::where('statut_paiement', Paiement::STATUT_PAYE)->sum('montant_net_paye_paiement'),
                'montant_en_attente' => Paiement::whereIn('statut_paiement', [
                    Paiement::STATUT_EN_ATTENTE,
                    Paiement::STATUT_VALIDE,
                    Paiement::STATUT_EN_TRAITEMENT
                ])->sum('montant_net_paye_paiement'),
            ];

            // Données pour les filtres
            $statuts = Paiement::getStatuts();
            $banques = Banque::where('actif_banque', true)->orderBy('nom_banque')->get();
            $factures = Facture::with('proforma')
                ->whereIn('statut_facture', [Facture::STATUT_VALIDEE, Facture::STATUT_PARTIELLEMENT_PAYEE, Facture::STATUT_PAYEE])
                ->orderBy('numero_facture')
                ->get();

            // Réponse JSON ou Vue
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Liste de tous les paiements récupérée avec succès.',
                    'data' => [
                        'paiements' => $paiements,
                        'stats' => $stats,
                        'statuts' => $statuts,
                    ],
                ], 200);
            }

            return view('paiements.all', compact('paiements', 'stats', 'statuts', 'banques', 'factures'));
        } catch (Exception $e) {
            Log::error('Erreur lors de la récupération de tous les paiements: ' . $e->getMessage());

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Une erreur est survenue lors de la récupération des paiements.',
                    'error' => config('app.debug') ? $e->getMessage() : null,
                ], 500);
            }

            return back()->with('error', 'Une erreur est survenue lors de la récupération des paiements.');
        }
    }


    /**
     * Afficher la liste des paiements d'une facture.
     */
    public function index(Request $request, string $factureId)
    {
        try {
            // CORRIGÉ: Facture → proforma → prestataire (et non marche)
            $facture = Facture::with(['proforma.prestatairePrincipal.prestataire'])->findOrFail($factureId);

            $query = Paiement::with([
                'facture.proforma.prestatairePrincipal.prestataire', // CORRIGÉ
                'banque',
                'validateur',
                'payeur',
                'createur',
            ])->where('facture_id', $factureId);

            // Filtrage par statut
            if ($request->filled('statut')) {
                $statut = (int) $request->statut;
                $query->where('statut_paiement', $statut);
            }

            // Filtrage par banque
            if ($request->filled('banque_id')) {
                $query->where('banque_id', $request->banque_id);
            }

            // Filtrage par période de création
            if ($request->filled('date_debut') && $request->filled('date_fin')) {
                $query->whereBetween('created_at', [
                    $request->date_debut . ' 00:00:00',
                    $request->date_fin . ' 23:59:59'
                ]);
            }

            // Filtrage par période de validation
            if ($request->filled('date_validation_debut') && $request->filled('date_validation_fin')) {
                $query->whereBetween('date_validation_paiement', [
                    $request->date_validation_debut . ' 00:00:00',
                    $request->date_validation_fin . ' 23:59:59'
                ]);
            }

            // Filtrage par montant
            if ($request->filled('montant_min')) {
                $query->where('montant_net_paye_paiement', '>=', $request->montant_min);
            }
            if ($request->filled('montant_max')) {
                $query->where('montant_net_paye_paiement', '<=', $request->montant_max);
            }

            // Recherche globale
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('observations_paiement', 'LIKE', "%{$search}%")
                        ->orWhere('motif_rejet_paiement', 'LIKE', "%{$search}%")
                        ->orWhereHas('banque', function ($subQuery) use ($search) {
                            $subQuery->where('nom_banque', 'LIKE', "%{$search}%")
                                ->orWhere('numero_compte_banque', 'LIKE', "%{$search}%");
                        });
                });
            }

            // Tri
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $allowedSorts = [
                'montant_net_paye_paiement',
                'statut_paiement',
                'date_validation_paiement',
                'created_at',
                'updated_at'
            ];

            if (in_array($sortBy, $allowedSorts)) {
                $query->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');
            } else {
                $query->orderBy('created_at', 'desc');
            }

            // Pagination
            $perPage = $request->get('per_page', 15);
            $paiements = $query->paginate($perPage)->withQueryString();

            // Statistiques pour cette facture
            $stats = [
                'total' => Paiement::where('facture_id', $factureId)->count(),
                'en_attente' => Paiement::where('facture_id', $factureId)->where('statut_paiement', Paiement::STATUT_EN_ATTENTE)->count(),
                'valides' => Paiement::where('facture_id', $factureId)->where('statut_paiement', Paiement::STATUT_VALIDE)->count(),
                'payes' => Paiement::where('facture_id', $factureId)->where('statut_paiement', Paiement::STATUT_PAYE)->count(),
                'montant_total' => Paiement::where('facture_id', $factureId)->sum('montant_net_paye_paiement'),
                'montant_paye' => Paiement::where('facture_id', $factureId)->where('statut_paiement', Paiement::STATUT_PAYE)->sum('montant_net_paye_paiement'),
            ];

            // Réponse JSON ou Vue
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Liste des paiements récupérée avec succès.',
                    'data' => [
                        'paiements' => $paiements,
                        'facture' => $facture,
                        'stats' => $stats,
                        'statuts' => Paiement::getStatuts(),
                    ],
                ], 200);
            }

            return view('paiements.index', compact('paiements', 'facture', 'stats', 'factureId'));
        } catch (Exception $e) {
            Log::error('Erreur lors de la récupération des paiements: ' . $e->getMessage());

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Une erreur est survenue lors de la récupération des paiements.',
                    'error' => config('app.debug') ? $e->getMessage() : null,
                ], 500);
            }

            return back()->with('error', 'Une erreur est survenue lors de la récupération des paiements.');
        }
    }

    /**
     * Afficher le formulaire de création d'un paiement.
     */
    public function create(Request $request, string $factureId)
    {
        try {

            // CORRIGÉ: Charger facture avec prestatairePrincipal
            $facture = Facture::with(['proforma.prestatairePrincipal.prestataire'])->findOrFail($factureId);

            // Vérifier que la facture est validée
            if (method_exists($facture, 'peutRecevoirPaiement') && !$facture->peutRecevoirPaiement()) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'La facture doit être validée avant de créer un paiement.',
                    ], 422);
                }

                return back()->with('error', 'La facture doit être validée avant de créer un paiement.');
            }

            // CORRIGÉ: Utiliser la méthode getPrestataireId()
            $prestataireId = $facture->proforma?->getPrestataireId();

            if (!$prestataireId) {
                throw new Exception('Impossible de déterminer le prestataire pour cette facture. Vérifiez que la proforma a une attribution active.');
            }

            // Récupérer les banques actives du prestataire
            $banques = Banque::where('prestataire_id', $prestataireId)
                ->where('actif_banque', true)
                ->orderBy('nom_banque')
                ->get();


            if ($banques->isEmpty()) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Aucune banque active trouvée pour ce prestataire.',
                    ], 422);
                }

                return back()->with('error', 'Aucune banque active trouvée pour ce prestataire.');
            }

            $statuts = Paiement::getStatuts();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Formulaire de création prêt.',
                    'data' => [
                        'facture' => $facture,
                        'banques' => $banques,
                        'statuts' => $statuts,
                    ],
                ], 200);
            }

            return view('paiements.create', compact('facture', 'banques', 'statuts', 'factureId'));
        } catch (Exception $e) {
            Log::error('Erreur lors de la préparation du formulaire de création: ' . $e->getMessage());

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Une erreur est survenue.',
                    'error' => config('app.debug') ? $e->getMessage() : null,
                ], 500);
            }

            return back()->with('error', 'Une erreur est survenue: ' . $e->getMessage());
        }
    }

    /**
     * Enregistrer un nouveau paiement.
     */
    public function store(StorePaiementRequest $request, string $factureId)
    {
        try {
            DB::beginTransaction();

            // Récupérer la facture
            $facture = Facture::findOrFail($factureId);

            // Vérifier que la facture peut recevoir un paiement
            if (method_exists($facture, 'peutRecevoirPaiement') && !$facture->peutRecevoirPaiement()) {
                throw new Exception('La facture doit être validée avant de pouvoir créer un paiement.');
            }

            // Vérifier que le montant du paiement ne dépasse pas le reste à payer
            if (method_exists($facture, 'getMontantRestantAttribute')) {
                $resteAPayer = $facture->montant_restant;
                if ($request->montant_net_paye_paiement > $resteAPayer) {
                    throw new Exception(
                        "Le montant du paiement ({$request->montant_net_paye_paiement} FCFA) " .
                            "dépasse le reste à payer ({$resteAPayer} FCFA)."
                    );
                }
            }

            // CORRIGÉ: Vérifier que la banque appartient au prestataire de la facture
            $banque = Banque::findOrFail($request->banque_id);

            $facture->load(['proforma.prestatairePrincipal.prestataire']);

            // $facture = Facture::with(['proforma.prestatairePrincipal.prestataire'])->findOrFail($factureId);

            $prestataireId = $facture->proforma?->getPrestataireId();

            if ($banque->prestataire_id !== $prestataireId) {
                throw new Exception('La banque sélectionnée n\'appartient pas au prestataire de cette facture.');
            }

            // Créer le paiement
            $data = $request->validated();
            $data['facture_id'] = $factureId;
            $data['created_by'] = auth()->id();
            $data['statut_paiement'] = $data['statut_paiement'] ?? Paiement::STATUT_EN_ATTENTE;

            $paiement = Paiement::create($data);

            $paiement->load([
                'facture',
                'banque',
                'createur',
            ]);

            DB::commit();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Paiement créé avec succès.',
                    'data' => $paiement,
                ], 201);
            }

            return redirect()->route('paiements.show', [$factureId,  $paiement->id_paiement])->with('success', 'Paiement créé avec succès.');
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Erreur lors de la création du paiement: ' . $e->getMessage());

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'error' => config('app.debug') ? $e->getMessage() : null,
                ], 422);
            }

            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Afficher les détails d'un paiement.
     */
    public function show(Request $request, string $factureId, Paiement $paiement)
    {
        try {
            // Vérifier que le paiement appartient à la facture
            if ($paiement->facture_id !== $factureId) {
                abort(404, 'Ce paiement n\'appartient pas à cette facture.');
            }

            // CORRIGÉ: Charger les bonnes relations
            $paiement->load([
                'facture.proforma.prestatairePrincipal.prestataire',
                'banque',
                'validateur',
                'payeur',
                'createur',
                'modificateur',
            ]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Détails du paiement récupérés avec succès.',
                    'data' => $paiement,
                ], 200);
            }

            return view('paiements.show', compact('paiement', 'factureId'));
        } catch (Exception $e) {
            Log::error('Erreur lors de la récupération du paiement: ' . $e->getMessage());
            // dd($e->getMessage());
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Une erreur est survenue.',
                    'error' => config('app.debug') ? $e->getMessage() : null,
                ], 500);
            }

            return back()->with('error', 'Une erreur est survenue.');
        }
    }

    /**
     * Afficher le formulaire d'édition.
     */
    // public function edit(Request $request, string $factureId, Paiement $paiement)
    // {
    //     try {
    //         // Vérifier que le paiement appartient à la facture
    //         if ($paiement->facture_id !== $factureId) {
    //             abort(404);
    //         }

    //         // Vérifier que le paiement peut être modifié
    //         if (!$paiement->peutEtreModifie()) {
    //             throw new Exception(
    //                 'Ce paiement ne peut plus être modifié (statut: ' . $paiement->statut_libelle . ').'
    //             );
    //         }

    //         // CORRIGÉ: Charger facture avec proforma
    //         $facture = Facture::with(['proforma.prestataire'])->findOrFail($factureId);
    //         $prestataireId = $facture->proforma->prestataire_id ?? null;

    //         if (!$prestataireId) {
    //             throw new Exception('Prestataire introuvable pour cette facture.');
    //         }

    //         // Récupérer les banques du prestataire
    //         $banques = Banque::where('prestataire_id', $prestataireId)
    //             ->where('actif_banque', true)
    //             ->orderBy('nom_banque')
    //             ->get();

    //         if ($request->expectsJson() || $request->ajax()) {
    //             return response()->json([
    //                 'success' => true,
    //                 'data' => [
    //                     'paiement' => $paiement,
    //                     'facture' => $facture,
    //                     'banques' => $banques,
    //                 ],
    //             ], 200);
    //         }

    //         return view('paiements.edit', compact('paiement', 'facture', 'banques', 'factureId'));
    //     } catch (Exception $e) {
    //         Log::error('Erreur: ' . $e->getMessage());

    //         if ($request->expectsJson() || $request->ajax()) {
    //             return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    //         }

    //         return back()->with('error', $e->getMessage());
    //     }
    // }


    /**
 * Afficher le formulaire d'édition.
 */
public function edit(Request $request, string $factureId, Paiement $paiement)
{
    try {
        // Vérifier que le paiement appartient à la facture
        if ($paiement->facture_id !== $factureId) {
            abort(404);
        }

        // Vérifier que le paiement peut être modifié
        if (!$paiement->peutEtreModifie()) {
            throw new Exception(
                'Ce paiement ne peut plus être modifié (statut: ' . $paiement->statut_libelle . ').'
            );
        }

        // CORRIGÉ: Charger facture avec prestatairePrincipal (même structure que create)
        $facture = Facture::with([
            'proforma.prestatairePrincipal.prestataire',
            'proforma.prestatairePrincipal.lot.appelOffre',
            'paiements.banque'
        ])->findOrFail($factureId);

        // CORRIGÉ: Utiliser la méthode getPrestataireId() comme dans create()
        $prestataireId = $facture->proforma?->getPrestataireId();

        if (!$prestataireId) {
            throw new Exception('Impossible de déterminer le prestataire pour cette facture. Vérifiez que la proforma a une attribution active.');
        }

        // Récupérer les banques actives du prestataire
        $banques = Banque::where('prestataire_id', $prestataireId)
            ->where('actif_banque', true)
            ->orderBy('nom_banque')
            ->get();

        if ($banques->isEmpty()) {
            throw new Exception('Aucune banque active trouvée pour ce prestataire.');
        }

        // Charger les relations du paiement pour la vue
        $paiement->load(['facture', 'banque']);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'paiement' => $paiement,
                    'facture' => $facture,
                    'banques' => $banques,
                ],
            ], 200);
        }

        return view('paiements.edit', compact('paiement', 'facture', 'banques', 'factureId'));
    } catch (Exception $e) {
        Log::error('Erreur lors de l\'édition du paiement: ' . $e->getMessage());

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }

        return back()->with('error', $e->getMessage());
    }
}

    /**
     * Mettre à jour un paiement.
     */
    public function update(UpdatePaiementRequest $request, string $factureId, Paiement $paiement)
    {
        try {
            // Vérifier que le paiement appartient à la facture
            if ($paiement->facture_id !== $factureId) {
                abort(404, 'Ce paiement n\'appartient pas à cette facture.');
            }

            // Vérifier que le paiement peut être modifié
            if (!$paiement->peutEtreModifie()) {
                throw new Exception(
                    'Ce paiement ne peut plus être modifié (statut: ' . $paiement->statut_libelle . ').'
                );
            }

            DB::beginTransaction();

            // Si le montant change, vérifier qu'il ne dépasse pas le reste à payer
            if (
                $request->has('montant_net_paye_paiement') &&
                $request->montant_net_paye_paiement != $paiement->montant_net_paye_paiement
            ) {

                $facture = $paiement->facture;
                if (method_exists($facture, 'getMontantRestantAttribute')) {
                    $resteAPayer = $facture->montant_restant + $paiement->montant_net_paye_paiement;

                    if ($request->montant_net_paye_paiement > $resteAPayer) {
                        throw new Exception(
                            "Le nouveau montant ({$request->montant_net_paye_paiement} FCFA) " .
                                "dépasse le reste à payer disponible ({$resteAPayer} FCFA)."
                        );
                    }
                }
            }

            $data = $request->validated();
            $data['updated_by'] = auth()->id();

            $paiement->update($data);
            $paiement->load([
                'facture',
                'banque',
                'modificateur',
            ]);

            DB::commit();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Paiement modifié avec succès.',
                    'data' => $paiement,
                ], 200);
            }

            return redirect()
                ->route('paiements.show', ['factureId' => $factureId, 'paiement' => $paiement->id_paiement])
                ->with('success', 'Paiement modifié avec succès.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la modification du paiement: ' . $e->getMessage());

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'error' => config('app.debug') ? $e->getMessage() : null,
                ], 422);
            }

            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }



    /**
     * Supprimer (soft delete) un paiement.
     *
     * @param Request $request
     * @param string $factureId
     * @param Paiement $paiement
     * @return JsonResponse|RedirectResponse
     */
    public function destroy(Request $request, string $factureId, Paiement $paiement)
    {
        try {
            // Vérifier que le paiement appartient à la facture
            if ($paiement->facture_id !== $factureId) {
                abort(404, 'Ce paiement n\'appartient pas à cette facture.');
            }

            // Vérifier que le paiement peut être supprimé
            if ($paiement->statut_paiement === Paiement::STATUT_PAYE) {
                $message = 'Un paiement déjà effectué ne peut pas être supprimé.';

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $message,
                    ], 422);
                }

                return back()->with('error', $message);
            }

            DB::beginTransaction();

            $paiement->deleted_by = auth()->id();
            $paiement->save();
            $paiement->delete();

            DB::commit();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Paiement supprimé avec succès.',
                ], 200);
            }

            return redirect()
                ->route('paiements.index', ['factureId' => $factureId])
                ->with('success', 'Paiement supprimé avec succès.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la suppression du paiement: ' . $e->getMessage());

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Une erreur est survenue lors de la suppression.',
                    'error' => config('app.debug') ? $e->getMessage() : null,
                ], 500);
            }

            return back()->with('error', 'Une erreur est survenue lors de la suppression.');
        }
    }

    // =========================================================================
    // ACTIONS SPÉCIFIQUES AUX PAIEMENTS
    // =========================================================================

    /**
     * Valider un paiement.
     */
    public function valider(Request $request, string $factureId, Paiement $paiement)
    {
        try {
            if ($paiement->facture_id !== $factureId) {
                abort(404);
            }

            if (!$paiement->peutEtreValide()) {
                $message = 'Ce paiement ne peut pas être validé (statut actuel: ' . $paiement->statut_libelle . ').';

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'message' => $message], 422);
                }

                return back()->with('error', $message);
            }

            DB::beginTransaction();
            $paiement->valider(auth()->id());
            DB::commit();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Paiement validé avec succès.',
                    'data' => $paiement->fresh(['validateur']),
                ], 200);
            }

            return redirect()
                ->route('paiements.show', ['factureId' => $factureId, 'paiement' => $paiement->id_paiement])
                ->with('success', 'Paiement validé avec succès.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la validation du paiement: ' . $e->getMessage());

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Une erreur est survenue.'], 500);
            }

            return back()->with('error', 'Une erreur est survenue.');
        }
    }

    /**
     * Mettre un paiement en traitement bancaire.
     */
    public function mettreEnTraitement(Request $request, string $factureId, Paiement $paiement)
    {
        try {
            if ($paiement->facture_id !== $factureId) {
                abort(404);
            }

            if ($paiement->statut_paiement !== Paiement::STATUT_VALIDE) {
                $message = 'Seul un paiement validé peut être mis en traitement bancaire.';

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'message' => $message], 422);
                }

                return back()->with('error', $message);
            }

            DB::beginTransaction();
            $paiement->mettreEnTraitement(auth()->id());
            DB::commit();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Paiement mis en traitement bancaire.',
                    'data' => $paiement->fresh(),
                ], 200);
            }

            return redirect()
                ->route('paiements.show', ['factureId' => $factureId, 'paiement' => $paiement->id_paiement])
                ->with('success', 'Paiement mis en traitement bancaire.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur: ' . $e->getMessage());

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Une erreur est survenue.'], 500);
            }

            return back()->with('error', 'Une erreur est survenue.');
        }
    }

    /**
     * Confirmer qu'un paiement a été effectué.
     */
    public function confirmerPaiement(Request $request, string $factureId, Paiement $paiement)
    {
        try {
            if ($paiement->facture_id !== $factureId) {
                abort(404);
            }

            if (!in_array($paiement->statut_paiement, [Paiement::STATUT_VALIDE, Paiement::STATUT_EN_TRAITEMENT])) {
                $message = 'Seul un paiement validé ou en traitement peut être confirmé comme payé.';

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'message' => $message], 422);
                }

                return back()->with('error', $message);
            }

            DB::beginTransaction();
            $paiement->confirmerPaiement(auth()->id());
            DB::commit();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Paiement confirmé avec succès.',
                    'data' => $paiement->fresh(['payeur']),
                ], 200);
            }

            return redirect()
                ->route('paiements.show', ['factureId' => $factureId, 'paiement' => $paiement->id_paiement])
                ->with('success', 'Paiement confirmé avec succès.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur: ' . $e->getMessage());

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Une erreur est survenue.'], 500);
            }

            return back()->with('error', 'Une erreur est survenue.');
        }
    }

    /**
     * Rejeter un paiement.
     */
    public function rejeter(Request $request, string $factureId, Paiement $paiement)
    {
        $request->validate([
            'motif_rejet' => 'required|string|min:10',
        ], [
            'motif_rejet.required' => 'Le motif de rejet est obligatoire.',
            'motif_rejet.min' => 'Le motif de rejet doit contenir au moins 10 caractères.',
        ]);

        try {
            if ($paiement->facture_id !== $factureId) {
                abort(404);
            }

            if (!$paiement->peutEtreRejete()) {
                $message = 'Ce paiement ne peut pas être rejeté (statut actuel: ' . $paiement->statut_libelle . ').';

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'message' => $message], 422);
                }

                return back()->with('error', $message);
            }

            DB::beginTransaction();
            $paiement->rejeter($request->motif_rejet, auth()->id());
            DB::commit();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Paiement rejeté avec succès.',
                    'data' => $paiement->fresh(),
                ], 200);
            }

            return redirect()
                ->route('paiements.show', ['factureId' => $factureId, 'paiement' => $paiement->id_paiement])
                ->with('success', 'Paiement rejeté avec succès.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur: ' . $e->getMessage());

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Une erreur est survenue.'], 500);
            }

            return back()->with('error', 'Une erreur est survenue.');
        }
    }

    /**
     * Annuler un paiement.
     */
    public function annuler(Request $request, string $factureId, Paiement $paiement)
    {
        $request->validate([
            'motif_annulation' => 'required|string|min:10',
        ], [
            'motif_annulation.required' => 'Le motif d\'annulation est obligatoire.',
            'motif_annulation.min' => 'Le motif d\'annulation doit contenir au moins 10 caractères.',
        ]);

        try {
            if ($paiement->facture_id !== $factureId) {
                abort(404);
            }

            if (!$paiement->peutEtreAnnule()) {
                $message = 'Ce paiement ne peut pas être annulé (statut actuel: ' . $paiement->statut_libelle . ').';

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'message' => $message], 422);
                }

                return back()->with('error', $message);
            }

            DB::beginTransaction();
            $paiement->annuler($request->motif_annulation, auth()->id());
            DB::commit();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Paiement annulé avec succès.',
                    'data' => $paiement->fresh(),
                ], 200);
            }

            return redirect()
                ->route('paiements.show', ['factureId' => $factureId, 'paiement' => $paiement->id_paiement])
                ->with('success', 'Paiement annulé avec succès.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur: ' . $e->getMessage());

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Une erreur est survenue.'], 500);
            }

            return back()->with('error', 'Une erreur est survenue.');
        }
    }

    /**
     * Remettre un paiement en attente (après rejet).
     */
    public function remettreEnAttente(Request $request, string $factureId, Paiement $paiement)
    {
        try {
            if ($paiement->facture_id !== $factureId) {
                abort(404);
            }

            if ($paiement->statut_paiement !== Paiement::STATUT_REJETE) {
                $message = 'Seul un paiement rejeté peut être remis en attente.';

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'message' => $message], 422);
                }

                return back()->with('error', $message);
            }

            DB::beginTransaction();
            $paiement->remettreEnAttente(auth()->id());
            DB::commit();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Paiement remis en attente avec succès.',
                    'data' => $paiement->fresh(),
                ], 200);
            }

            return redirect()
                ->route('paiements.show', ['factureId' => $factureId, 'paiement' => $paiement->id_paiement])
                ->with('success', 'Paiement remis en attente avec succès.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur: ' . $e->getMessage());

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Une erreur est survenue.'], 500);
            }

            return back()->with('error', 'Une erreur est survenue.');
        }
    }

    // =========================================================================
    // ACTIONS DE GESTION MULTIPLE
    // =========================================================================

    /**
     * Valider plusieurs paiements en masse.
     */
    public function validerMasse(Request $request, string $factureId)
    {
        $request->validate([
            'paiement_ids' => 'required|array|min:1',
            'paiement_ids.*' => 'required|uuid|exists:paiements,id_paiement',
        ]);

        try {
            DB::beginTransaction();

            $paiements = Paiement::whereIn('id_paiement', $request->paiement_ids)
                ->where('facture_id', $factureId)
                ->where('statut_paiement', Paiement::STATUT_EN_ATTENTE)
                ->get();

            if ($paiements->isEmpty()) {
                throw new Exception('Aucun paiement en attente trouvé parmi la sélection.');
            }

            $valides = 0;
            foreach ($paiements as $paiement) {
                if ($paiement->valider(auth()->id())) {
                    $valides++;
                }
            }

            DB::commit();

            $message = "{$valides} paiement(s) validé(s) avec succès.";

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => ['nombre_valides' => $valides],
                ], 200);
            }

            return back()->with('success', $message);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur: ' . $e->getMessage());

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Confirmer plusieurs paiements en masse.
     */
    public function confirmerMasse(Request $request, string $factureId)
    {
        $request->validate([
            'paiement_ids' => 'required|array|min:1',
            'paiement_ids.*' => 'required|uuid|exists:paiements,id_paiement',
        ]);

        try {
            DB::beginTransaction();

            $paiements = Paiement::whereIn('id_paiement', $request->paiement_ids)
                ->where('facture_id', $factureId)
                ->whereIn('statut_paiement', [Paiement::STATUT_VALIDE, Paiement::STATUT_EN_TRAITEMENT])
                ->get();

            if ($paiements->isEmpty()) {
                throw new Exception('Aucun paiement validé ou en traitement trouvé parmi la sélection.');
            }

            $confirmes = 0;
            foreach ($paiements as $paiement) {
                if ($paiement->confirmerPaiement(auth()->id())) {
                    $confirmes++;
                }
            }

            DB::commit();

            $message = "{$confirmes} paiement(s) confirmé(s) avec succès.";

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => ['nombre_confirmes' => $confirmes],
                ], 200);
            }

            return back()->with('success', $message);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur: ' . $e->getMessage());

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }

            return back()->with('error', $e->getMessage());
        }
    }

    // =========================================================================
    // STATISTIQUES ET RAPPORTS
    // =========================================================================

    /**
     * Obtenir les statistiques des paiements d'une facture.
     */
    public function statistiques(Request $request, string $factureId)
    {
        try {
            $facture = Facture::findOrFail($factureId);

            $stats = [
                'total' => Paiement::where('facture_id', $factureId)->count(),
                'en_attente' => Paiement::where('facture_id', $factureId)->where('statut_paiement', Paiement::STATUT_EN_ATTENTE)->count(),
                'valides' => Paiement::where('facture_id', $factureId)->where('statut_paiement', Paiement::STATUT_VALIDE)->count(),
                'en_traitement' => Paiement::where('facture_id', $factureId)->where('statut_paiement', Paiement::STATUT_EN_TRAITEMENT)->count(),
                'payes' => Paiement::where('facture_id', $factureId)->where('statut_paiement', Paiement::STATUT_PAYE)->count(),
                'rejetes' => Paiement::where('facture_id', $factureId)->where('statut_paiement', Paiement::STATUT_REJETE)->count(),
                'annules' => Paiement::where('facture_id', $factureId)->where('statut_paiement', Paiement::STATUT_ANNULE)->count(),
                'montant_total' => Paiement::where('facture_id', $factureId)->sum('montant_net_paye_paiement'),
                'montant_paye' => Paiement::where('facture_id', $factureId)->where('statut_paiement', Paiement::STATUT_PAYE)->sum('montant_net_paye_paiement'),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Statistiques récupérées avec succès.',
                'data' => $stats,
            ], 200);
        } catch (Exception $e) {
            Log::error('Erreur: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue.',
            ], 500);
        }
    }

    /**
     * Obtenir les statistiques par banque pour une facture.
     */
    public function statistiquesParBanque(Request $request, string $factureId)
    {
        try {
            $stats = Paiement::where('facture_id', $factureId)
                ->selectRaw('banque_id, COUNT(*) as nombre, SUM(montant_net_paye_paiement) as montant_total')
                ->with('banque:id_banque,nom_banque')
                ->groupBy('banque_id')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Statistiques par banque récupérées avec succès.',
                'data' => $stats,
            ], 200);
        } catch (Exception $e) {
            Log::error('Erreur: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue.',
            ], 500);
        }
    }

    /**
     * Obtenir les statistiques par mois pour une facture.
     */
    public function statistiquesParMois(Request $request, string $factureId)
    {
        $request->validate([
            'annee' => 'nullable|integer|min:2000|max:2100',
        ]);

        try {
            $annee = $request->get('annee', now()->year);
            $stats = Paiement::where('facture_id', $factureId)
                ->selectRaw('MONTH(created_at) as mois, COUNT(*) as nombre, SUM(montant_net_paye_paiement) as montant_total')
                ->whereYear('created_at', $annee)
                ->groupBy('mois')
                ->orderBy('mois')
                ->get();

            return response()->json([
                'success' => true,
                'message' => "Statistiques pour l'année {$annee} récupérées avec succès.",
                'data' => [
                    'annee' => $annee,
                    'statistiques' => $stats,
                ],
            ], 200);
        } catch (Exception $e) {
            Log::error('Erreur: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue.',
            ], 500);
        }
    }

    /**
     * Exporter les paiements d'une facture.
     */
    public function export(Request $request, string $factureId)
    {
        try {
            $query = Paiement::with([
                'facture.marche.prestataire',
                'banque',
                'validateur',
                'payeur',
            ])->where('facture_id', $factureId);

            // Appliquer les mêmes filtres que dans index
            if ($request->filled('statut')) {
                $query->where('statut_paiement', $request->statut);
            }

            if ($request->filled('date_debut') && $request->filled('date_fin')) {
                $query->whereBetween('created_at', [
                    $request->date_debut . ' 00:00:00',
                    $request->date_fin . ' 23:59:59'
                ]);
            }

            $paiements = $query->orderBy('created_at', 'desc')->get();

            return response()->json([
                'success' => true,
                'message' => 'Export généré avec succès.',
                'data' => [
                    'paiements' => $paiements,
                    'count' => $paiements->count(),
                    'montant_total' => $paiements->sum('montant_net_paye_paiement'),
                ],
            ], 200);
        } catch (Exception $e) {
            Log::error('Erreur: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de l\'export.',
            ], 500);
        }
    }

    // =========================================================================
    // GESTION DE LA CORBEILLE
    // =========================================================================

    /**
     * Afficher les paiements supprimés d'une facture.
     */
    public function trashed(Request $request, string $factureId)
    {
        try {

            $facture = Facture::findOrFail($factureId);

            $query = Paiement::onlyTrashed()
                ->where('facture_id', $factureId)
                ->with(['facture', 'banque', 'suppresseur']);

            // Recherche
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('observations_paiement', 'LIKE', "%{$search}%");
                });
            }

            $perPage = $request->get('per_page', 15);
            $paiements = $query->orderBy('deleted_at', 'desc')
                ->paginate($perPage)
                ->withQueryString();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Paiements supprimés récupérés avec succès.',
                    'data' => [
                        'paiements' => $paiements,
                        'facture' => $facture,
                    ],
                ], 200);
            }

            return view('paiements.trashed', compact('paiements', 'facture', 'factureId'));
        } catch (Exception $e) {
            Log::error('Erreur: ' . $e->getMessage());

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Une erreur est survenue.'], 500);
            }

            return back()->with('error', 'Une erreur est survenue.');
        }
    }

    /**
     * Restaurer un paiement supprimé.
     */
    public function restore(Request $request, string $factureId, string $id)
    {
        try {
            $paiement = Paiement::withTrashed()
                ->where('facture_id', $factureId)
                ->findOrFail($id);

            if (!$paiement->trashed()) {
                $message = 'Ce paiement n\'est pas supprimé.';

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'message' => $message], 422);
                }

                return back()->with('error', $message);
            }

            DB::beginTransaction();

            $paiement->restore();
            $paiement->deleted_by = null;
            $paiement->save();

            DB::commit();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Paiement restauré avec succès.',
                    'data' => $paiement->fresh(),
                ], 200);
            }

            return redirect()
                ->route('paiements.show', ['factureId' => $factureId, 'paiement' => $paiement->id_paiement])
                ->with('success', 'Paiement restauré avec succès.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur: ' . $e->getMessage());

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Une erreur est survenue.'], 500);
            }

            return back()->with('error', 'Une erreur est survenue.');
        }
    }

    /**
     * Suppression définitive d'un paiement.
     */
    public function forceDelete(Request $request, string $factureId, string $id)
    {
        try {
            $paiement = Paiement::withTrashed()
                ->where('facture_id', $factureId)
                ->findOrFail($id);

            // Vérifier si le paiement a été effectué
            if ($paiement->statut_paiement === Paiement::STATUT_PAYE) {
                $message = 'Un paiement effectué ne peut jamais être supprimé définitivement pour des raisons d\'audit.';

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'message' => $message], 422);
                }

                return back()->with('error', $message);
            }

            DB::beginTransaction();
            $paiement->forceDelete();
            DB::commit();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Paiement supprimé définitivement.',
                ], 200);
            }

            return redirect()
                ->route('paiements.trashed', ['factureId' => $factureId])
                ->with('success', 'Paiement supprimé définitivement.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur: ' . $e->getMessage());

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Une erreur est survenue.'], 500);
            }

            return back()->with('error', 'Une erreur est survenue.');
        }
    }
}
