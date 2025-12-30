<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Lot;
use App\Models\Facture;
use App\Models\Proforma;
use Illuminate\View\View;
use App\Models\AppelOffre;
use Illuminate\Http\Request;
use App\Models\TypeAppelOffre;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\StoreFactureRequest;
use App\Http\Requests\UpdateFactureRequest;

class FactureController extends Controller
{
    /**
     * Nombre d'éléments par page pour la pagination.
     */
    protected int $perPage = 15;

    /**
     * Afficher la liste des factures.
     *
     * @param Request $request
     * @return View|JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $query = Facture::with(['proforma', 'paiements', 'createur'])->latest('created_at');

            // Filtrage par recherche
            if ($request->filled('search')) {
                $query->recherche($request->search);
            }

            // Filtrage par statut
            if ($request->filled('statut')) {
                $query->where('statut_facture', $request->statut);
            }

            // Filtrage par proforma
            if ($request->filled('proforma_id')) {
                $query->where('proforma_id', $request->proforma_id);
            }

            // Filtrage par période
            if ($request->filled('date_debut') && $request->filled('date_fin')) {
                $query->parPeriode($request->date_debut, $request->date_fin);
            }

            // Pagination
            $factures = $query->paginate($this->perPage)->withQueryString();

            // Statistiques
            $statistiques = Facture::getStatistiques();

            // Liste des proformas pour le filtre
            $proformas = Proforma::select('id_proforma', 'numero_proforma')->orderBy('numero_proforma')->get();

            // Statuts disponibles
            $statuts = Facture::getStatuts();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'factures' => $factures,
                        'statistiques' => $statistiques,
                    ],
                ]);
            }

            return view('factures.index', compact(
                'factures',
                'statistiques',
                'proformas',
                'statuts'
            ));

        } catch (Exception $e) {
            Log::error('Erreur lors de la récupération des factures: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la récupération des factures.',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Erreur lors de la récupération des factures.');
        }
    }

    /**
     * Afficher le formulaire de création de la facture.
     *
     * @param Request $request
     * @return View|JsonResponse
     */
    public function creater(Request $request)
    {
        try {

            $typesAppelsOffres = TypeAppelOffre::with(['creator', 'updater'])
                ->with(['parent', 'appelOffres'])
                ->withCount('appelOffres')?->versionActuelle()->get();


            $appelsOffres = AppelOffre::with([
                'typeAppelOffre',
                'lots.attributionActive.prestataire',
                'lots.attributionActive.proforma'
            ])
            ->whereHas('lots.attributionActive.proforma') // Seulement les AO avec au moins un lot ayant une proforma
            ->orderBy('numero_appel_offre', 'desc')
            ->get();




            // Récupérer les proformas validées sans facture associée
            $proformas = Proforma::where('actif_proforma', true)
                ->whereDoesntHave('facture')
                ->orderBy('numero_proforma')
                ->get();

            // Si proforma_id est passé en paramètre, pré-sélectionner
            $proformaSelectionnee = null;
            if ($request->filled('proforma_id')) {
                $proformaSelectionnee = Proforma::find($request->proforma_id);
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'proformas' => $proformas,
                        'proforma_selectionnee' => $proformaSelectionnee,
                    ],
                ]);
            }

            return view('factures.create', compact('proformas', 'proformaSelectionnee', 'appelsOffres'));

        } catch (Exception $e) {
            Log::error('Erreur lors du chargement du formulaire de création: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors du chargement du formulaire.',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Erreur lors du chargement du formulaire.');
        }
    }

        /**
         * Afficher le formulaire de création d'une facture.
         *
         * @param Request $request
         * @return View|JsonResponse
         */
        public function create(Request $request)
        {
            try {
                // dd(52);
                // Récupérer les types d'appels d'offres actifs avec leurs relations
                $typesAppelsOffres = TypeAppelOffre::with([
                    'creator',
                    'updater',
                    'appelOffres' => function ($query) {
                        $query->actif()
                            ->with([
                                'lots' => function ($query) {
                                    $query->versionActuelle()
                                        ->actif()
                                        ->with([
                                            'attributionActive' => function ($query) {
                                                $query->with([
                                                    'proforma' => function ($query) {
                                                        $query->actif()
                                                            ->whereDoesntHave('facture');
                                                    },
                                                    'prestataire'
                                                ]);
                                            }
                                        ]);
                                }
                            ]);
                    }
                ])
                ->versionActuelle()
                ->actif()
                ->withCount([
                    'appelOffres' => function ($query) {
                        $query->actif();
                    }
                ])
                ->orderBy('libelle_type_appel_offre')
                ->get();


                $appelsOffres = AppelOffre::with([
                    'typeAppelOffre',
                    'lots.attributionActive.prestataire',
                    'lots.attributionActive.proforma'
                ])
                ->whereHas('lots.attributionActive.proforma') // Seulement les AO avec au moins un lot ayant une proforma
                ->orderBy('numero_appel_offre', 'desc')
                ->get();



                // Si lot_id est passé en paramètre, pré-sélectionner le lot et sa proforma
                $lotSelectionne = null;
                if ($request->filled('lot_id')) {
                    $lotSelectionne = Lot::with([
                        'appelOffre.typeAppelOffre',
                        'attributionActive' => function ($query) {
                            $query->with([
                                'proforma' => function ($query) {
                                    $query->actif();
                                },
                                'prestataire'
                            ]);
                        }
                    ])
                    ->find($request->lot_id);
                }

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => true,
                        'data' => [
                            'types_appels_offres' => $typesAppelsOffres,
                            'lot_selectionne' => $lotSelectionne,
                            'proforma' => $lotSelectionne?->attributionActive?->proforma,
                        ],
                    ]);
                }
                // proformaSelectionnee
                // dd($typesAppelsOffres, $lotSelectionne);

                return view('factures.create', compact(
                    'typesAppelsOffres',
                    'lotSelectionne', 'appelsOffres'
                ));

            } catch (Exception $e) {
                Log::error('Erreur lors du chargement du formulaire de création: ' . $e->getMessage());

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Erreur lors du chargement du formulaire.',
                        'error' => $e->getMessage(),
                    ], 500);
                }

                return back()->with('error', 'Erreur lors du chargement du formulaire.');
            }
        }




    /**
     * Enregistrer une nouvelle facture.
     *
     * @param StoreFactureRequest $request
     * @return RedirectResponse|JsonResponse
     */
    public function store(StoreFactureRequest $request): RedirectResponse|JsonResponse
    {
        try {
            DB::beginTransaction();

            $validated = $request->validated();
            $validated['created_by'] = Auth::id();
            $validated['statut_facture'] = Facture::STATUT_EN_ATTENTE;

            // Générer le numéro de facture si non fourni
            if (empty($validated['numero_facture'])) {
                $validated['numero_facture'] = Facture::generateNumeroFacture();
            }

            $facture = Facture::create($validated);

            DB::commit();

            Log::info('Facture créée avec succès', ['id' => $facture->id_facture]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Facture créée avec succès.',
                    'data' => $facture->load(['proforma', 'createur']),
                ], 201);
            }

            return redirect()
                ->route('factures.show', $facture->id_facture)
                ->with('success', 'Facture créée avec succès.');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la création de la facture: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la création de la facture.',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la création de la facture.');
        }
    }

    /**
     * Afficher les détails d'une facture.
     *
     * @param Request $request
     * @param string $id
     * @return View|JsonResponse
     */
    public function show(Request $request, string $id)
    {
        try {
            $facture = Facture::with([
                'proforma',
                'paiements.banque',
                'paiements.validateur',
                'paiements.payeur',
                'createur',
                'modificateur',
            ])->findOrFail($id);

            // Statistiques des paiements
            $statistiquesPaiements = [
                'total' => $facture->paiements->count(),
                'montant_total_paye' => $facture->montant_paye,
                'montant_restant' => $facture->montant_restant,
                'pourcentage_paye' => $facture->montant_facture > 0
                    ? round(($facture->montant_paye / $facture->montant_facture) * 100, 2)
                    : 0,
            ];

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'facture' => $facture,
                        'statistiques_paiements' => $statistiquesPaiements,
                    ],
                ]);
            }

            return view('factures.show', compact('facture', 'statistiquesPaiements'));

        } catch (Exception $e) {
            Log::error('Erreur lors de la récupération de la facture: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Facture non trouvée.',
                    'error' => $e->getMessage(),
                ], 404);
            }

            return back()->with('error', 'Facture non trouvée.');
        }
    }

    /**
     * Afficher le formulaire de modification.
     *
     * @param Request $request
     * @param string $id
     * @return View|JsonResponse
     */
    public function edit(Request $request, string $id)
    {
        try {
            $facture = Facture::with('proforma')->findOrFail($id);

            // Vérifier si la facture peut être modifiée
            if (!$facture->peutEtreModifiee()) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cette facture ne peut plus être modifiée.',
                    ], 403);
                }

                return back()->with('error', 'Cette facture ne peut plus être modifiée.');
            }

            // Récupérer les proformas disponibles
            $proformas = Proforma::where('actif_proforma', true)
                ->where(function ($query) use ($facture) {
                    $query->whereDoesntHave('facture')
                        ->orWhere('id_proforma', $facture->proforma_id);
                })
                ->orderBy('numero_proforma')
                ->get();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'facture' => $facture,
                        'proformas' => $proformas,
                    ],
                ]);
            }

            return view('factures.edit', compact('facture', 'proformas'));

        } catch (Exception $e) {
            Log::error('Erreur lors du chargement du formulaire de modification: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors du chargement du formulaire.',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Erreur lors du chargement du formulaire.');
        }
    }

    /**
     * Mettre à jour une facture.
     *
     * @param UpdateFactureRequest $request
     * @param string $id
     * @return RedirectResponse|JsonResponse
     */
    public function update(UpdateFactureRequest $request, string $id): RedirectResponse|JsonResponse
    {
        try {
            $facture = Facture::findOrFail($id);

            // Vérifier si la facture peut être modifiée
            if (!$facture->peutEtreModifiee()) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cette facture ne peut plus être modifiée.',
                    ], 403);
                }

                return back()->with('error', 'Cette facture ne peut plus être modifiée.');
            }

            DB::beginTransaction();

            $validated = $request->validated();
            $validated['updated_by'] = Auth::id();

            $facture->update($validated);

            DB::commit();

            Log::info('Facture mise à jour avec succès', ['id' => $facture->id_facture]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Facture mise à jour avec succès.',
                    'data' => $facture->fresh(['proforma', 'createur', 'modificateur']),
                ]);
            }

            return redirect()
                ->route('factures.show', $facture->id_facture)
                ->with('success', 'Facture mise à jour avec succès.');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la mise à jour de la facture: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la mise à jour de la facture.',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la mise à jour de la facture.');
        }
    }

    /**
     * Supprimer une facture.
     *
     * @param Request $request
     * @param string $id
     * @return RedirectResponse|JsonResponse
     */
    public function destroy(Request $request, string $id): RedirectResponse|JsonResponse
    {
        try {
            $facture = Facture::findOrFail($id);

            // Vérifier si la facture peut être supprimée
            if (!$facture->peutEtreAnnulee()) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cette facture ne peut pas être supprimée car elle a des paiements associés.',
                    ], 403);
                }

                return back()->with('error', 'Cette facture ne peut pas être supprimée car elle a des paiements associés.');
            }

            DB::beginTransaction();

            $facture->deleted_by = Auth::id();
            $facture->save();
            $facture->delete();

            DB::commit();

            Log::info('Facture supprimée avec succès', ['id' => $id]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Facture supprimée avec succès.',
                ]);
            }

            return redirect()
                ->route('factures.index')
                ->with('success', 'Facture supprimée avec succès.');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la suppression de la facture: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la suppression de la facture.',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Erreur lors de la suppression de la facture.');
        }
    }

    /**
     * Valider une facture.
     *
     * @param Request $request
     * @param string $id
     * @return RedirectResponse|JsonResponse
     */
    public function valider(Request $request, string $id): RedirectResponse|JsonResponse
    {
        try {
            $facture = Facture::findOrFail($id);

            if (!$facture->peutEtreValidee()) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cette facture ne peut pas être validée dans son état actuel.',
                    ], 403);
                }

                return back()->with('error', 'Cette facture ne peut pas être validée dans son état actuel.');
            }

            DB::beginTransaction();

            $facture->valider(Auth::id());

            DB::commit();

            Log::info('Facture validée avec succès', ['id' => $facture->id_facture]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Facture validée avec succès.',
                    'data' => $facture->fresh(),
                ]);
            }

            return back()->with('success', 'Facture validée avec succès.');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la validation de la facture: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la validation de la facture.',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Erreur lors de la validation de la facture.');
        }
    }

    /**
     * Rejeter une facture.
     *
     * @param Request $request
     * @param string $id
     * @return RedirectResponse|JsonResponse
     */
    public function rejeter(Request $request, string $id): RedirectResponse|JsonResponse
    {
        $request->validate([
            'motif' => 'required|string|min:10|max:1000',
        ], [
            'motif.required' => 'Le motif de rejet est obligatoire.',
            'motif.min' => 'Le motif doit contenir au moins 10 caractères.',
            'motif.max' => 'Le motif ne peut pas dépasser 1000 caractères.',
        ]);

        try {
            $facture = Facture::findOrFail($id);

            if (!$facture->peutEtreRejetee()) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cette facture ne peut pas être rejetée dans son état actuel.',
                    ], 403);
                }

                return back()->with('error', 'Cette facture ne peut pas être rejetée dans son état actuel.');
            }

            DB::beginTransaction();

            $facture->rejeter($request->motif, Auth::id());

            DB::commit();

            Log::info('Facture rejetée avec succès', ['id' => $facture->id_facture, 'motif' => $request->motif]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Facture rejetée avec succès.',
                    'data' => $facture->fresh(),
                ]);
            }

            return back()->with('success', 'Facture rejetée avec succès.');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors du rejet de la facture: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors du rejet de la facture.',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Erreur lors du rejet de la facture.');
        }
    }

    /**
     * Annuler une facture.
     *
     * @param Request $request
     * @param string $id
     * @return RedirectResponse|JsonResponse
     */
    public function annuler(Request $request, string $id): RedirectResponse|JsonResponse
    {
        $request->validate([
            'motif' => 'required|string|min:10|max:1000',
        ], [
            'motif.required' => 'Le motif d\'annulation est obligatoire.',
            'motif.min' => 'Le motif doit contenir au moins 10 caractères.',
            'motif.max' => 'Le motif ne peut pas dépasser 1000 caractères.',
        ]);

        try {
            $facture = Facture::findOrFail($id);

            if (!$facture->peutEtreAnnulee()) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cette facture ne peut pas être annulée.',
                    ], 403);
                }

                return back()->with('error', 'Cette facture ne peut pas être annulée.');
            }

            DB::beginTransaction();

            $facture->annuler($request->motif, Auth::id());

            DB::commit();

            Log::info('Facture annulée avec succès', ['id' => $facture->id_facture, 'motif' => $request->motif]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Facture annulée avec succès.',
                    'data' => $facture->fresh(),
                ]);
            }

            return back()->with('success', 'Facture annulée avec succès.');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de l\'annulation de la facture: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de l\'annulation de la facture.',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Erreur lors de l\'annulation de la facture.');
        }
    }

    /**
     * Remettre une facture en attente.
     *
     * @param Request $request
     * @param string $id
     * @return RedirectResponse|JsonResponse
     */
    public function remettreEnAttente(Request $request, string $id): RedirectResponse|JsonResponse
    {
        try {
            $facture = Facture::findOrFail($id);

            if ($facture->statut_facture !== Facture::STATUT_REJETEE) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Seules les factures rejetées peuvent être remises en attente.',
                    ], 403);
                }

                return back()->with('error', 'Seules les factures rejetées peuvent être remises en attente.');
            }

            DB::beginTransaction();

            $facture->statut_facture = Facture::STATUT_EN_ATTENTE;
            $facture->comment_facture = null;
            $facture->updated_by = Auth::id();
            $facture->save();

            DB::commit();

            Log::info('Facture remise en attente avec succès', ['id' => $facture->id_facture]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Facture remise en attente avec succès.',
                    'data' => $facture->fresh(),
                ]);
            }

            return back()->with('success', 'Facture remise en attente avec succès.');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la remise en attente de la facture: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la remise en attente de la facture.',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Erreur lors de la remise en attente de la facture.');
        }
    }

    /**
     * Dupliquer une facture.
     *
     * @param Request $request
     * @param string $id
     * @return RedirectResponse|JsonResponse
     */
    public function dupliquer(Request $request, string $id): RedirectResponse|JsonResponse
    {
        try {
            $facture = Facture::findOrFail($id);

            DB::beginTransaction();

            $nouvelleFacture = $facture->dupliquer(Auth::id());

            DB::commit();

            Log::info('Facture dupliquée avec succès', [
                'original_id' => $facture->id_facture,
                'nouvelle_id' => $nouvelleFacture->id_facture,
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Facture dupliquée avec succès.',
                    'data' => $nouvelleFacture->load(['proforma', 'createur']),
                ]);
            }

            return redirect()
                ->route('factures.edit', $nouvelleFacture->id_facture)
                ->with('success', 'Facture dupliquée avec succès. Vous pouvez maintenant la modifier.');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la duplication de la facture: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la duplication de la facture.',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Erreur lors de la duplication de la facture.');
        }
    }

    /**
     * Récupérer les informations d'une proforma pour le formulaire.
     *
     * @param Request $request
     * @param string $proformaId
     * @return JsonResponse
     */
    public function getProformaInfo(Request $request, string $proformaId): JsonResponse
    {
        try {
            $proforma = Proforma::findOrFail($proformaId);

            // Calculer le montant total TTC
            $montantTTC = $proforma->montant_retenu_proforma
                + $proforma->taxe_montant
                - $proforma->remise_montant_proforma
                + $proforma->penalites_proforma;

            return response()->json([
                'success' => true,
                'data' => [
                    'proforma' => $proforma,
                    'montant_ttc' => $montantTTC,
                    'montant_ttc_formate' => number_format($montantTTC, 0, ',', ' ') . ' FCFA',
                ],
            ]);

        } catch (Exception $e) {
            Log::error('Erreur lors de la récupération des informations de la proforma: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Proforma non trouvée.',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Afficher les statistiques des factures.
     *
     * @param Request $request
     * @return View|JsonResponse
     */
    public function statistiques(Request $request)
    {
        try {
            $statistiques = Facture::getStatistiques();

            // Statistiques par mois
            $annee = $request->get('annee', date('Y'));
            $parMois = Facture::selectRaw('MONTH(date_facture) as mois, COUNT(*) as nombre, SUM(montant_facture) as montant_total')
                ->whereYear('date_facture', $annee)
                ->groupBy('mois')
                ->orderBy('mois')
                ->get();

            // Top proformas par montant facturé
            $topProformas = Facture::with('proforma')
                ->selectRaw('proforma_id, SUM(montant_facture) as total_facture')
                ->groupBy('proforma_id')
                ->orderByDesc('total_facture')
                ->limit(10)
                ->get();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'statistiques' => $statistiques,
                        'par_mois' => $parMois,
                        'top_proformas' => $topProformas,
                    ],
                ]);
            }

            return view('factures.statistiques', compact('statistiques', 'parMois', 'topProformas', 'annee'));

        } catch (Exception $e) {
            Log::error('Erreur lors de la récupération des statistiques: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la récupération des statistiques.',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Erreur lors de la récupération des statistiques.');
        }
    }
}
