<?php

namespace App\Http\Controllers;


use App\Models\Lot;
use App\Models\Proforma;
use App\Models\Prestataire;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\PrestataireLot;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PrestataireController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Prestataire::query();

            // Filtre par statut
            if ($request->filled('statut')) {
                $query->where('statut_prestataire', $request->input('statut'));
            }

            // Recherche
            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('raison_sociale_prestataire', 'like', "%{$search}%")
                        ->orWhere('numero_identification_prestataire', 'like', "%{$search}%")
                        ->orWhere('numero_cc_prestataire', 'like', "%{$search}%")
                        ->orWhere('numero_rccm_prestataire', 'like', "%{$search}%")
                        ->orWhere('telephone_principal_prestataire', 'like', "%{$search}%")
                        ->orWhere('telephone_secondaire_prestataire', 'like', "%{$search}%")
                        ->orWhere('email_prestataire', 'like', "%{$search}%")
                        ->orWhere('ville_prestataire', 'like', "%{$search}%")
                        ->orWhere('pays_prestataire', 'like', "%{$search}%");
                });
            }

            // Tri
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $allowedSortFields = ['raison_sociale_prestataire', 'created_at', 'updated_at', 'ville_prestataire', 'statut_prestataire'];

            if (in_array($sortBy, $allowedSortFields)) {
                $query->orderBy($sortBy, $sortOrder);
            } else {
                $query->orderBy('created_at', 'desc');
            }

            // Pagination
            $perPage = min($request->get('per_page', 10), 100);
            $prestataires = $query->paginate($perPage);

            // Statistiques pour la vue
            $stats = [
                'total' => Prestataire::count(),
                'actifs' => Prestataire::where('statut_prestataire', true)->count(),
                'inactifs' => Prestataire::where('statut_prestataire', false)->count(),
                'avec_lots' => Prestataire::whereHas('attributionsActives')->count(),
            ];

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => $prestataires,
                    'stats' => $stats,
                    'message' => 'Liste des prestataires récupérée avec succès'
                ]);
            }

            return view('prestataires.index', compact('prestataires', 'stats'));

        } catch (\Exception $e) {

            Log::error('Erreur lors de la récupération des prestataires: ' . $e->getMessage());

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Une erreur est survenue lors de la récupération des prestataires.',
                    'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne'
                ], 500);
            }

            return redirect()->back()->with('error', 'Une erreur est survenue lors de la récupération des prestataires.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        try {
            $proformas = Proforma::where('actif_proforma', 1)
                ->orderByRaw('LOWER(numero_proforma) ASC')
                ->get();

            $lotsNonAssignes = Lot::where('attribution_lot', 0)
                ->orderByRaw('LOWER(numero) ASC')
                ->get();

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'proformas' => $proformas,
                        'lots_non_assignes' => $lotsNonAssignes,
                    ],
                    'message' => 'Formulaire de création de prestataire récupéré avec succès'
                ]);
            }

            return view('prestataires.create', compact('proformas', 'lotsNonAssignes'));

        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération du formulaire de création: ' . $e->getMessage());

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Une erreur est survenue lors de la récupération du formulaire de création.',
                    'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne'
                ], 500);
            }

            return redirect()->back()->with('error', 'Une erreur est survenue lors de la récupération du formulaire de création.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validation des données principales du prestataire
            $validator = Validator::make($request->all(), [
                'raison_sociale_prestataire' => 'required|string|max:255',
                'numero_identification_prestataire' => 'required|string|max:25|unique:prestataires,numero_identification_prestataire',
                'email_prestataire' => 'required|email|max:255|unique:prestataires,email_prestataire',
                'numero_cc_prestataire' => 'required|string|max:50|unique:prestataires,numero_cc_prestataire',
                'numero_rccm_prestataire' => 'required|string|max:50|unique:prestataires,numero_rccm_prestataire',
                'telephone_principal_prestataire' => 'required|string|max:20|unique:prestataires,telephone_principal_prestataire',
                'telephone_secondaire_prestataire' => 'nullable|string|max:20',
                'adresse_prestataire' => 'required|string|max:500',
                'ville_prestataire' => 'required|string|max:50',
                'pays_prestataire' => 'required|string|max:50',
                'statut_prestataire' => 'nullable|boolean',
            ], $this->getValidationMessages());

            if ($validator->fails()) {
                if ($request->wantsJson() || $request->is('api/*')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation échouée.',
                        'errors' => $validator->errors()
                    ], 422);
                }
                return back()->withErrors($validator)->withInput();
            }

            $validatedData = $validator->validated();

            // Validation du représentant légal
            $representantValidator = Validator::make($request->all(), [
                'nom' => 'required|string|max:100',
                'prenoms' => 'nullable|string|max:150',
                'contact' => 'required|string|max:20',
                'email' => 'required|email|max:255',
                'nationalite' => 'required|string|max:50',
                'pays' => 'required|string|max:50',
                'adresse' => 'required|string|max:255',
                'profession' => 'required|string|max:100',
                'date_naissance' => 'required|date|before:today',
                'lieu_naissance' => 'required|string|max:100',
                'numero_piece_identite' => 'required|string|max:50',
                'type_piece_identite' => 'required|string|max:50',
                'date_delivrance' => 'required|date|before_or_equal:today',
                'lieu_delivrance' => 'required|string|max:100',
                'date_expiration' => 'required|date|after:date_delivrance|after:today',
            ], $this->getRepresentantValidationMessages());

            if ($representantValidator->fails()) {
                if ($request->wantsJson() || $request->is('api/*')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation échouée pour le représentant légal.',
                        'errors' => $representantValidator->errors()
                    ], 422);
                }
                return back()->withErrors($representantValidator)->withInput();
            }

            // Formater le représentant légal avec ID et statut
            $premierRepresentant = $representantValidator->validated();
            $premierRepresentant['id'] = (string) Str::uuid();
            $premierRepresentant['statut'] = 1;
            $premierRepresentant['created_at'] = now()->toIso8601String();

            $representantsLegaux = [$premierRepresentant];

            DB::beginTransaction();

            // Préparation des données pour la création
            $dataToCreate = $validatedData;
            $dataToCreate['id_prestataire'] = (string) Str::uuid();
            $dataToCreate['representant_legal_prestataire'] = json_encode($representantsLegaux);
            $dataToCreate['statut_prestataire'] = $request->boolean('statut_prestataire', false);
            $dataToCreate['created_by'] = Auth::id();

            // Création du prestataire
            $prestataire = Prestataire::create($dataToCreate);

            DB::commit();

            Log::info('Prestataire créé avec succès', ['id' => $prestataire->id_prestataire]);

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => $prestataire,
                    'message' => 'Prestataire créé avec succès'
                ], 201);
            }

            return redirect()->route('prestataires.index')->with('success', 'Prestataire créé avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la création du prestataire: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Une erreur est survenue lors de la création du prestataire.',
                    'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne'
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de la création du prestataire.')
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        try {
            $prestataire = Prestataire::with([
                // 'documents',
                'banques',
                'capacitesTechniques',
                'situationsFinancieres',
                // 'evaluations',
                'creator',
                'updater'
            ])->findOrFail($id);

            // Statistiques du prestataire
            $statistiques = $prestataire->getStatistiques();

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => $prestataire,
                    'statistiques' => $statistiques,
                    'message' => 'Prestataire récupéré avec succès.'
                ]);
            }

            return view('prestataires.show', compact('prestataire', 'statistiques'));

        } catch (ModelNotFoundException $e) {
            dd($e->getMessage());
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Prestataire non trouvé.'
                ], 404);
            }
            return redirect()->route('prestataires.index')->with('error', 'Prestataire non trouvé.');

        } catch (\Exception $e) {
            dd($e->getMessage());
            Log::error('Erreur lors de la récupération du prestataire: ' . $e->getMessage());

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Une erreur est survenue.',
                    'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne'
                ], 500);
            }
            return redirect()->back()->with('error', 'Une erreur est survenue.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, string $id)
    {
        try {
            $prestataire = Prestataire::findOrFail($id);

            $proformas = Proforma::where('actif_proforma', 1)
                ->orderByRaw('LOWER(numero_proforma) ASC')
                ->get();

            $lotsNonAssignes = Lot::where('attribution_lot', 0)
                ->orderByRaw('LOWER(numero) ASC')
                ->get();

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'prestataire' => $prestataire,
                        'proformas' => $proformas,
                        'lots_non_assignes' => $lotsNonAssignes,
                    ],
                    'message' => 'Formulaire d\'édition récupéré avec succès'
                ]);
            }

            return view('prestataires.edit', compact('prestataire', 'proformas', 'lotsNonAssignes'));

        } catch (ModelNotFoundException $e) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Prestataire non trouvé.'
                ], 404);
            }
            return redirect()->route('prestataires.index')->with('error', 'Prestataire non trouvé.');

        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération du formulaire d\'édition: ' . $e->getMessage());

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Une erreur est survenue.',
                    'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne'
                ], 500);
            }
            return redirect()->back()->with('error', 'Une erreur est survenue.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $prestataire = Prestataire::findOrFail($id);


            // Validation des données principales
            $validator = Validator::make($request->all(), [
                'raison_sociale_prestataire' => 'required|string|max:255',
                'numero_identification_prestataire' => 'required|string|max:25|unique:prestataires,numero_identification_prestataire,' . $id . ',id_prestataire',
                'email_prestataire' => 'required|email|max:255|unique:prestataires,email_prestataire,' . $id . ',id_prestataire',
                'numero_cc_prestataire' => 'required|string|max:50|unique:prestataires,numero_cc_prestataire,' . $id . ',id_prestataire',
                'numero_rccm_prestataire' => 'required|string|max:50|unique:prestataires,numero_rccm_prestataire,' . $id . ',id_prestataire',
                'telephone_principal_prestataire' => 'nullable|string|max:20',
                'telephone_secondaire_prestataire' => 'nullable|string|max:20',
                'telephone_prestataire' => 'nullable|string|max:20',
                // 'contact_principal_prestataire' => 'nullable|string|max:100',
                // 'contact_secondaire_prestataire' => 'nullable|string|max:100',
                'adresse_prestataire' => 'required|string|max:500',
                'ville_prestataire' => 'required|string|max:50',
                'pays_prestataire' => 'nullable|string|max:50',
                // 'pays' => 'nullable|string|max:50',
                'representant_legal_prestataire' => 'nullable|json',
                'statut_prestataire' => 'nullable|boolean',
            ], $this->getValidationMessages());

            if ($validator->fails()) {
                if ($request->wantsJson() || $request->is('api/*')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation échouée.',
                        'errors' => $validator->errors()
                    ], 422);
                }
                return back()->withErrors($validator)->withInput();
            }

            $validatedData = $validator->validated();

            // Gestion du représentant légal si fourni
            if ($request->filled('representant_legal_prestataire')) {
                $representant = json_decode($validatedData['representant_legal_prestataire'], true);

                if ($representant && !empty($representant['email'])) {
                    // Validation du représentant légal
                    $representantValidator = Validator::make($representant, [
                        'nom' => 'required|string|max:100',
                        'prenoms' => 'nullable|string|max:150',
                        'contact' => 'required|string|max:20',
                        'email' => 'required|email|max:255',
                        'nationalite' => 'required|string|max:50',
                        'pays' => 'required|string|max:50',
                        'adresse' => 'required|string|max:255',
                        'profession' => 'required|string|max:100',
                        'date_naissance' => 'required|date',
                        'lieu_naissance' => 'required|string|max:100',
                        'numero_piece_identite' => 'required|string|max:50',
                        'type_piece_identite' => 'required|string|max:50',
                        'date_delivrance' => 'required|date',
                        'lieu_delivrance' => 'required|string|max:100',
                        'date_expiration' => 'required|date|after:date_delivrance',
                    ]);

                    if ($representantValidator->fails()) {
                        if ($request->wantsJson() || $request->is('api/*')) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Validation échouée pour le représentant légal.',
                                'errors' => $representantValidator->errors()
                            ], 422);
                        }
                        return back()->withErrors($representantValidator)->withInput();
                    }

                    // Récupération des représentants existants
                    $representantsExistants = json_decode($prestataire->representant_legal_prestataire, true) ?? [];
                    $trouve = false;

                    // Vérifier si c'est une mise à jour ou un nouvel ajout
                    foreach ($representantsExistants as &$rep) {
                        if (isset($rep['email']) && $rep['email'] === $representant['email']) {
                            // Mise à jour du représentant existant
                            $rep = array_merge($rep, $representant);
                            $rep['updated_at'] = now()->toIso8601String();
                            $trouve = true;
                            break;
                        }
                    }

                    if (!$trouve) {
                        // Nouveau représentant : désactiver les anciens
                        foreach ($representantsExistants as &$rep) {
                            $rep['statut'] = 0;
                        }
                        // Ajouter le nouveau
                        $representant['id'] = (string) Str::uuid();
                        $representant['statut'] = 1;
                        $representant['created_at'] = now()->toIso8601String();
                        $representantsExistants[] = $representant;
                    }

                    $validatedData['representant_legal_prestataire'] = json_encode($representantsExistants);
                }
            }

            // Normaliser le champ pays
            if (isset($validatedData['pays']) && !isset($validatedData['pays_prestataire'])) {
                $validatedData['pays_prestataire'] = $validatedData['pays'];
            }
            unset($validatedData['pays']);

            // Normaliser le champ téléphone
            if (isset($validatedData['telephone_prestataire']) && !isset($validatedData['telephone_principal_prestataire'])) {
                $validatedData['telephone_principal_prestataire'] = $validatedData['telephone_prestataire'];
            }
            unset($validatedData['telephone_prestataire']);

            DB::beginTransaction();

            // Mise à jour
            $validatedData['updated_by'] = Auth::id();
            $prestataire->update($validatedData);


            DB::commit();

            Log::info('Prestataire mis à jour avec succès', ['id' => $prestataire->id_prestataire]);

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => $prestataire->fresh(),
                    'message' => 'Prestataire mis à jour avec succès'
                ]);
            }

            return redirect()->route('prestataires.show', $prestataire->id_prestataire)
                ->with('success', 'Prestataire mis à jour avec succès.');

        } catch (ModelNotFoundException $e) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Prestataire non trouvé.'
                ], 404);
            }
            return redirect()->route('prestataires.index')->with('error', 'Prestataire non trouvé.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la mise à jour du prestataire: ' . $e->getMessage(), [
                'id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Une erreur est survenue lors de la mise à jour du prestataire.',
                    'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne'
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de la mise à jour du prestataire.')
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        try {
            $prestataire = Prestataire::findOrFail($id);

            // Vérifier si le prestataire a des lots en cours
            if ($prestataire->aDesLotsEnCours()) {
                if ($request->wantsJson() || $request->is('api/*')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Impossible de supprimer ce prestataire car il a des lots en cours d\'exécution.'
                    ], 422);
                }
                return redirect()->back()->with('error', 'Impossible de supprimer ce prestataire car il a des lots en cours d\'exécution.');
            }

            DB::beginTransaction();

            // Soft delete avec traçabilité
            $prestataire->deleted_by = Auth::id();
            $prestataire->save();
            $prestataire->delete();

            DB::commit();

            Log::info('Prestataire supprimé avec succès', ['id' => $id]);

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'message' => 'Prestataire supprimé avec succès'
                ]);
            }

            return redirect()->route('prestataires.index')->with('success', 'Prestataire supprimé avec succès.');

        } catch (ModelNotFoundException $e) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Prestataire non trouvé.'
                ], 404);
            }
            return redirect()->route('prestataires.index')->with('error', 'Prestataire non trouvé.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la suppression du prestataire: ' . $e->getMessage());

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Une erreur est survenue lors de la suppression du prestataire.',
                    'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne'
                ], 500);
            }

            return redirect()->back()->with('error', 'Une erreur est survenue lors de la suppression du prestataire.');
        }
    }

    /**
     * Toggle the status of the specified resource.
     */
    public function toggleStatus(Request $request, string $id)
    {
        try {
            $prestataire = Prestataire::findOrFail($id);

            DB::beginTransaction();

            $prestataire->statut_prestataire = !$prestataire->statut_prestataire;
            $prestataire->updated_by = Auth::id();
            $prestataire->save();

            DB::commit();

            $statusText = $prestataire->statut_prestataire ? 'activé' : 'désactivé';
            Log::info("Prestataire {$statusText}", ['id' => $id]);

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => $prestataire,
                    'message' => "Prestataire {$statusText} avec succès"
                ]);
            }

            return redirect()->back()->with('success', "Prestataire {$statusText} avec succès.");

        } catch (ModelNotFoundException $e) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Prestataire non trouvé.'
                ], 404);
            }
            return redirect()->back()->with('error', 'Prestataire non trouvé.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors du changement de statut du prestataire: ' . $e->getMessage());

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Une erreur est survenue.',
                    'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne'
                ], 500);
            }

            return redirect()->back()->with('error', 'Une erreur est survenue.');
        }
    }

    /**
     * Get statistics for a prestataire.
     */
    public function statistiques(Request $request, string $id)
    {
        try {
            $prestataire = Prestataire::findOrFail($id);
            $statistiques = $prestataire->getStatistiques();

            // Statistiques détaillées supplémentaires
            $statistiques['attributions_par_statut'] = [
                'en_attente' => $prestataire->attributions()->where('statut_attribution', PrestataireLot::STATUT_EN_ATTENTE)->count(),
                'attribue' => $prestataire->attributions()->where('statut_attribution', PrestataireLot::STATUT_ATTRIBUE)->count(),
                'suspendu' => $prestataire->attributions()->where('statut_attribution', PrestataireLot::STATUT_SUSPENDU)->count(),
                'termine' => $prestataire->attributions()->where('statut_attribution', PrestataireLot::STATUT_TERMINE)->count(),
                'retire' => $prestataire->attributions()->where('statut_attribution', PrestataireLot::STATUT_RETIRE)->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => $statistiques,
                'message' => 'Statistiques récupérées avec succès'
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des statistiques: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue.',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne'
            ], 500);
        }
    }


    //Récupérer les lots affectés au prestataire
    public function lotsPrestataire(Request $request, $prestataireId){

    }


    // public function retirer(Request $request, $id, $lotId){
    //     try{
    //         $prestataire = Prestataire::find($id);
    //         if(!$prestataire){
    //             if($request->acceptsJson() || $request->wantsJson()){
    //                 return response()->json([
    //                     "errors" => "Données invalides",
    //                     "succes" => false,
    //                     "message" => "Le prestataire concerné n'existe pas."
    //                 ], 200);
    //             }
    //             return redirect()->back()->with('error', "Le prestataire concerné n'existe pas.");
    //         }
    //         $lot = Lot::find($lotId);
    //         if(!$lot){
    //             if($request->acceptsJson() || $request->wantsJson()){
    //                 return response()->json([
    //                     "errors" => "Données invalides",
    //                     "succes" => false,
    //                     "message" => "Le lot concerné n'existe pas."
    //                 ], 200);
    //             }
    //             return redirect()->back()->with('error', "Le lot concerné n'existe pas.");
    //         }

    //         $prestataireLot = PrestataireLot::



    //     }catch(\Exception $e){
    //         //
    //     }
    // }

    /**
     * Get validation messages for prestataire.
     */
    private function getValidationMessages(): array
    {
        return [
            'raison_sociale_prestataire.required' => 'La raison sociale du prestataire est obligatoire.',
            'raison_sociale_prestataire.max' => 'La raison sociale ne peut pas dépasser 255 caractères.',
            'numero_identification_prestataire.required' => 'Le numéro d\'identification du prestataire est obligatoire.',
            'numero_identification_prestataire.unique' => 'Ce numéro d\'identification est déjà utilisé.',
            'email_prestataire.required' => 'L\'adresse email du prestataire est obligatoire.',
            'email_prestataire.email' => 'L\'adresse email n\'est pas valide.',
            'email_prestataire.unique' => 'Cette adresse email est déjà utilisée.',
            'numero_cc_prestataire.required' => 'Le numéro de carte de contribuable est obligatoire.',
            'numero_cc_prestataire.unique' => 'Ce numéro de carte de contribuable est déjà utilisé.',
            'numero_rccm_prestataire.required' => 'Le numéro RCCM est obligatoire.',
            'numero_rccm_prestataire.unique' => 'Ce numéro RCCM est déjà utilisé.',
            'telephone_principal_prestataire.required' => 'Le téléphone principal est obligatoire.',
            'telephone_principal_prestataire.unique' => 'Ce numéro de téléphone est déjà utilisé.',
            'adresse_prestataire.required' => 'L\'adresse du prestataire est obligatoire.',
            'ville_prestataire.required' => 'La ville du prestataire est obligatoire.',
            'pays_prestataire.required' => 'Le pays du prestataire est obligatoire.',
        ];
    }

    /**
     * Get validation messages for representant legal.
     */
    private function getRepresentantValidationMessages(): array
    {
        return [
            'nom.required' => 'Le nom du représentant légal est obligatoire.',
            'contact.required' => 'Le contact du représentant légal est obligatoire.',
            'email.required' => 'L\'email du représentant légal est obligatoire.',
            'email.email' => 'L\'email du représentant légal n\'est pas valide.',
            'nationalite.required' => 'La nationalité du représentant légal est obligatoire.',
            'pays.required' => 'Le pays du représentant légal est obligatoire.',
            'adresse.required' => 'L\'adresse du représentant légal est obligatoire.',
            'profession.required' => 'La profession du représentant légal est obligatoire.',
            'date_naissance.required' => 'La date de naissance du représentant légal est obligatoire.',
            'date_naissance.before' => 'La date de naissance doit être antérieure à aujourd\'hui.',
            'lieu_naissance.required' => 'Le lieu de naissance du représentant légal est obligatoire.',
            'numero_piece_identite.required' => 'Le numéro de pièce d\'identité est obligatoire.',
            'type_piece_identite.required' => 'Le type de pièce d\'identité est obligatoire.',
            'date_delivrance.required' => 'La date de délivrance est obligatoire.',
            'date_delivrance.before_or_equal' => 'La date de délivrance ne peut pas être dans le futur.',
            'lieu_delivrance.required' => 'Le lieu de délivrance est obligatoire.',
            'date_expiration.required' => 'La date d\'expiration est obligatoire.',
            'date_expiration.after' => 'La date d\'expiration doit être postérieure à la date de délivrance et à aujourd\'hui.',
        ];
    }


    // fetchLotsByPrestataire
    // public function fetchLotsByPrestataire($id){
    //     try{
    //         //Vérifier si le prestataire existe
    //         $prestataire = Prestataire::
    //     }catch(\Exception){

    //     }
    // }


    public function detailByLot(Request $request, $prestataireId, $lotId){
        try{
            $prestataire = Prestataire::find($prestataireId);
            $lot = Lot::find($lotId);

            if(!$prestataire){
                if($request->acceptsJson() || $request->wantsJson()){
                    return response()->json([
                        'errors' => "Données invalides",
                        "message" => "Ce prestataire n'existe pas,  veuillez s'il-vous-plaît réessayer !",
                        "succes" => false
                    ], 404);
                }
                return redirect()->back()->with('error', "Ce prestataire n'existe pas,  veuillez s'il-vous-plaît réessayer !",);
            }

            if(!$lot){
                if($request->acceptsJson() || $request->wantsJson()){
                    return response()->json([
                        'errors' => "Données invalides",
                        "message" => "Ce lot n'existe pas,  veuillez s'il-vous-plaît réessayer !",
                        "succes" => false
                    ], 404);
                }
                return redirect()->back()->with('error', "Ce lot n'existe pas,  veuillez s'il-vous-plaît réessayer !");
            }
        }catch(\Exception $e){

        }
    }
}
