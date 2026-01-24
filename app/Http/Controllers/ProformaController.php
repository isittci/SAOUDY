<?php

namespace App\Http\Controllers;

use App\Models\Proforma;
use App\Models\AttributionLotPrestataire;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ProformaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Proforma::query();

            // Filtre par statut
            if ($request->filled('statut')) {
                $query->where('actif_proforma', $request->input('statut'));
            }

            // Filtre par période
            if ($request->filled('date_debut') && $request->filled('date_fin')) {
                $query->parPeriode($request->input('date_debut'), $request->input('date_fin'));
            }

            // Filtre versions actuelles uniquement
            if ($request->boolean('versions_actuelles', false)) {
                $query->whereNull('parent_id');
            }

            // Recherche
            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('numero_proforma', 'like', "%{$search}%")
                        ->orWhere('modalite_proforma', 'like', "%{$search}%")
                        ->orWhere('motif_modification_proforma', 'like', "%{$search}%");
                });
            }

            // Tri
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $allowedSortFields = ['numero_proforma', 'created_at', 'updated_at', 'date_proforma', 'montant_retenu_proforma', 'version_proforma'];

            if (in_array($sortBy, $allowedSortFields)) {
                $query->orderBy($sortBy, $sortOrder);
            } else {
                $query->orderBy('created_at', 'desc');
            }

            // Charger les relations
            $query->with(['creator', 'parent']);

            // Pagination
            $perPage = min($request->get('per_page', 10), 100);
            $proformas = $query->paginate($perPage);

            // Statistiques pour la vue
            $stats = [
                'total' => Proforma::count(),
                'actives' => Proforma::where('actif_proforma', true)->count(),
                'inactives' => Proforma::where('actif_proforma', false)->count(),
                'montant_total' => Proforma::where('actif_proforma', true)->sum('montant_retenu_proforma'),
                'utilisees' => Proforma::whereHas('prestataireLotsAttributions')->count(),
            ];

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => $proformas,
                    'stats' => $stats,
                    'message' => 'Liste des proformas récupérée avec succès'
                ]);
            }

            return view('proformas.index', compact('proformas', 'stats'));

        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des proformas: ' . $e->getMessage());

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Une erreur est survenue lors de la récupération des proformas.',
                    'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne'
                ], 500);
            }

            return redirect()->back()->with('error', 'Une erreur est survenue lors de la récupération des proformas.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        try {
            // Générer le prochain numéro
            $numeroSuggere = Proforma::genererNumeroProforma();

            // Modalités de paiement prédéfinies
            $modalites = [
                'Paiement comptant',
                'Paiement à 30 jours',
                'Paiement à 60 jours',
                'Paiement à 90 jours',
                'Paiement échelonné',
                'Acompte + solde à la livraison',
            ];

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'numero_suggere' => $numeroSuggere,
                        'modalites' => $modalites,
                    ],
                    'message' => 'Formulaire de création de proforma récupéré avec succès'
                ]);
            }

            return view('proformas.create', compact('numeroSuggere', 'modalites'));

        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération du formulaire de création: ' . $e->getMessage());

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'numero_proforma' => 'nullable|string|max:20|unique:proformas,numero_proforma',
                'date_proforma' => 'required|date',
                'montant_retenu_proforma' => 'required|numeric|min:0',
                'taxe_montant' => 'nullable|numeric|min:0',
                'remise_montant_proforma' => 'nullable|numeric|min:0',
                'modalite_proforma' => 'nullable|string|max:255',
                'actif_proforma' => 'nullable|boolean',
            ], $this->getValidationMessages());

            if ($validator->fails()) {
                if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation échouée.',
                        'errors' => $validator->errors()
                    ], 422);
                }
                return back()->withErrors($validator)->withInput();
            }

            $validatedData = $validator->validated();

            // Vérifier que la remise ne dépasse pas le montant
            if (isset($validatedData['remise_montant_proforma']) &&
                $validatedData['remise_montant_proforma'] > $validatedData['montant_retenu_proforma']) {
                $error = ['remise_montant_proforma' => ['La remise ne peut pas dépasser le montant retenu.']];
                if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation échouée.',
                        'errors' => $error
                    ], 422);
                }
                return back()->withErrors($error)->withInput();
            }

            DB::beginTransaction();

            $dataToCreate = $validatedData;
            $dataToCreate['id_proforma'] = (string) Str::uuid();
            $dataToCreate['version_proforma'] = 1;
            $dataToCreate['actif_proforma'] = $request->boolean('actif_proforma', true);
            $dataToCreate['taxe_montant'] = $validatedData['taxe_montant'] ?? 0;
            $dataToCreate['remise_montant_proforma'] = $validatedData['remise_montant_proforma'] ?? 0;
            $dataToCreate['created_by'] = Auth::id();

            // Le numéro sera généré automatiquement si non fourni (via boot())
            $proforma = Proforma::create($dataToCreate);

            DB::commit();

            Log::info('Proforma créée avec succès', ['id' => $proforma->id_proforma, 'numero' => $proforma->numero_proforma]);

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => $proforma,
                    'message' => 'Proforma créée avec succès'
                ], 201);
            }

            return redirect()->route('proformas.index')->with('success', 'Proforma créée avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la création de la proforma: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Une erreur est survenue lors de la création de la proforma.',
                    'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne'
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de la création de la proforma.')
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        try {
            $proforma = Proforma::with([
                'creator',
                'updater',
                'parent',
                'versions',
                'prestataireLotsAttributions.prestataire',
                'prestataireLotsAttributions.lot'
            ])->findOrFail($id);

            // Récupérer l'historique des versions
            $historique = $proforma->getHistorique();

            // Résumé financier
            $resume = $proforma->getResume();

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => $proforma,
                    'historique' => $historique,
                    'resume' => $resume,
                    'message' => 'Proforma récupérée avec succès.'
                ]);
            }

            return view('proformas.show', compact('proforma', 'historique', 'resume'));

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Proforma non trouvée.'
                ], 404);
            }
            return redirect()->route('proformas.index')->with('error', 'Proforma non trouvée.');

        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération de la proforma: ' . $e->getMessage());

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
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
            $proforma = Proforma::findOrFail($id);

            // Modalités de paiement prédéfinies
            $modalites = [
                'Paiement comptant',
                'Paiement à 30 jours',
                'Paiement à 60 jours',
                'Paiement à 90 jours',
                'Paiement échelonné',
                'Acompte + solde à la livraison',
            ];

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'proforma' => $proforma,
                        'modalites' => $modalites,
                    ],
                    'message' => 'Formulaire d\'édition récupéré avec succès'
                ]);
            }

            return view('proformas.edit', compact('proforma', 'modalites'));

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Proforma non trouvée.'
                ], 404);
            }
            return redirect()->route('proformas.index')->with('error', 'Proforma non trouvée.');

        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération du formulaire d\'édition: ' . $e->getMessage());

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
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
     *
     * IMPORTANT: Toute mise à jour crée une nouvelle version de la proforma.
     * L'ancienne version est désactivée et conservée dans l'historique.
     */
    public function update(Request $request, string $id)
    {
        try {
            /**
             * @var Proforma $proforma
             */
            $proforma = Proforma::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'numero_proforma' => 'required|string|max:20',
                'date_proforma' => 'required|date',
                'montant_retenu_proforma' => 'required|numeric|min:0',
                'taxe_montant' => 'nullable|numeric|min:0',
                'remise_montant_proforma' => 'nullable|numeric|min:0',
                'modalite_proforma' => 'nullable|string|max:255',
                'actif_proforma' => 'nullable|boolean',
                'motif_modification_proforma' => 'nullable|string|max:1000',
            ], $this->getValidationMessages());

            if ($validator->fails()) {
                if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation échouée.',
                        'errors' => $validator->errors()
                    ], 422);
                }
                return back()->withErrors($validator)->withInput();
            }

            $validatedData = $validator->validated();

            // Vérifier que la remise ne dépasse pas le montant
            if (isset($validatedData['remise_montant_proforma']) &&
                $validatedData['remise_montant_proforma'] > $validatedData['montant_retenu_proforma']) {
                $error = ['remise_montant_proforma' => ['La remise ne peut pas dépasser le montant retenu.']];
                if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation échouée.',
                        'errors' => $error
                    ], 422);
                }
                return back()->withErrors($error)->withInput();
            }

            // Vérifier si des modifications ont été apportées
            $hasChanges = $this->detectChanges($proforma, $validatedData);

            if (!$hasChanges) {
                // Aucune modification détectée, retourner sans créer de version
                if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                    return response()->json([
                        'success' => true,
                        'data' => $proforma,
                        'message' => 'Aucune modification détectée.'
                    ]);
                }
                return redirect()->route('proformas.show', $proforma->id_proforma)
                    ->with('info', 'Aucune modification détectée.');
            }

            DB::beginTransaction();

            // Préparer les données pour la nouvelle version
            $donneesNouvelleVersion = [
                'date_proforma' => $validatedData['date_proforma'],
                'montant_retenu_proforma' => $validatedData['montant_retenu_proforma'],
                'taxe_montant' => $validatedData['taxe_montant'] ?? 0,
                'remise_montant_proforma' => $validatedData['remise_montant_proforma'] ?? 0,
                'modalite_proforma' => $validatedData['modalite_proforma'] ?? null,
                'actif_proforma' => $request->boolean('actif_proforma', true),
                'updated_by' => Auth::id(),
            ];

            // Générer le motif de modification automatiquement si non fourni
            $motif = $validatedData['motif_modification_proforma'] ?? $this->genererMotifModification($proforma, $donneesNouvelleVersion);

            // Créer la nouvelle version (l'ancienne sera automatiquement désactivée)
            $nouvelleVersion = $proforma->creerNouvelleVersion($donneesNouvelleVersion, $motif);

            DB::commit();

            Log::info('Nouvelle version de proforma créée via update', [
                'ancienne_version_id' => $proforma->id_proforma,
                'nouvelle_version_id' => $nouvelleVersion->id_proforma,
                'version' => $nouvelleVersion->version_proforma
            ]);

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => $nouvelleVersion,
                    'message' => 'Nouvelle version de la proforma créée avec succès (v' . $nouvelleVersion->version_proforma . ')'
                ]);
            }

            return redirect()->route('proformas.show', $nouvelleVersion->id_proforma)
                ->with('success', 'Nouvelle version de la proforma créée avec succès (v' . $nouvelleVersion->version_proforma . ').');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Proforma non trouvée.'
                ], 404);
            }
            return redirect()->route('proformas.index')->with('error', 'Proforma non trouvée.');

        } catch (\Exception $e) {

            DB::rollBack();
            Log::error('Erreur lors de la mise à jour de la proforma: ' . $e->getMessage(), [
                'id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Une erreur est survenue lors de la mise à jour de la proforma.',
                    'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne'
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de la mise à jour de la proforma.')
                ->withInput();
        }
    }

    /**
     * Détecter si des modifications ont été apportées.
     */
    private function detectChanges(Proforma $proforma, array $newData): bool
    {
        $fieldsToCompare = [
            'date_proforma',
            'montant_retenu_proforma',
            'taxe_montant',
            'remise_montant_proforma',
            'modalite_proforma',
        ];

        foreach ($fieldsToCompare as $field) {
            $oldValue = $proforma->{$field};
            $newValue = $newData[$field] ?? null;

            // Gérer les dates
            if ($field === 'date_proforma') {
                $oldValue = $oldValue ? $oldValue->format('Y-m-d') : null;
            }

            // Gérer les valeurs numériques
            if (in_array($field, ['montant_retenu_proforma', 'taxe_montant', 'remise_montant_proforma'])) {
                $oldValue = floatval($oldValue ?? 0);
                $newValue = floatval($newValue ?? 0);
            }

            if ($oldValue != $newValue) {
                return true;
            }
        }

        return false;
    }

    /**
     * Générer automatiquement un motif de modification basé sur les changements.
     */
    private function genererMotifModification(Proforma $proforma, array $newData): string
    {
        $modifications = [];

        // Comparer le montant
        if (floatval($proforma->montant_retenu_proforma) != floatval($newData['montant_retenu_proforma'])) {
            $ancien = number_format($proforma->montant_retenu_proforma, 2, ',', ' ');
            $nouveau = number_format($newData['montant_retenu_proforma'], 2, ',', ' ');
            $modifications[] = "Montant HT: {$ancien} → {$nouveau} FCFA";
        }

        // Comparer la remise
        if (floatval($proforma->remise_montant_proforma ?? 0) != floatval($newData['remise_montant_proforma'] ?? 0)) {
            $ancien = number_format($proforma->remise_montant_proforma ?? 0, 2, ',', ' ');
            $nouveau = number_format($newData['remise_montant_proforma'] ?? 0, 2, ',', ' ');
            $modifications[] = "Remise: {$ancien} → {$nouveau} FCFA";
        }

        // Comparer la taxe
        if (floatval($proforma->taxe_montant ?? 0) != floatval($newData['taxe_montant'] ?? 0)) {
            $ancien = number_format($proforma->taxe_montant ?? 0, 2, ',', ' ');
            $nouveau = number_format($newData['taxe_montant'] ?? 0, 2, ',', ' ');
            $modifications[] = "Taxe: {$ancien} → {$nouveau} FCFA";
        }



        // Comparer la date
        $oldDate = $proforma->date_proforma ? $proforma->date_proforma->format('Y-m-d') : null;
        if ($oldDate != ($newData['date_proforma'] ?? null)) {
            $modifications[] = "Date modifiée";
        }

        // Comparer les modalités
        if (($proforma->modalite_proforma ?? '') != ($newData['modalite_proforma'] ?? '')) {
            $modifications[] = "Modalités de paiement modifiées";
        }

        if (empty($modifications)) {
            return "Mise à jour des informations";
        }

        return "Modifications: " . implode(', ', $modifications);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        try {
            $proforma = Proforma::findOrFail($id);

            // Vérifier si la proforma est utilisée
            if ($proforma->estUtilisee()) {
                if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Impossible de supprimer cette proforma car elle est utilisée dans des attributions.'
                    ], 422);
                }
                return redirect()->back()->with('error', 'Impossible de supprimer cette proforma car elle est utilisée dans des attributions.');
            }

            DB::beginTransaction();

            // Soft delete avec traçabilité
            $proforma->deleted_by = Auth::id();
            $proforma->save();
            $proforma->delete();

            DB::commit();

            Log::info('Proforma supprimée avec succès', ['id' => $id]);

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'message' => 'Proforma supprimée avec succès'
                ]);
            }

            return redirect()->route('proformas.index')->with('success', 'Proforma supprimée avec succès.');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Proforma non trouvée.'
                ], 404);
            }
            return redirect()->route('proformas.index')->with('error', 'Proforma non trouvée.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la suppression de la proforma: ' . $e->getMessage());

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Une erreur est survenue lors de la suppression.',
                    'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne'
                ], 500);
            }

            return redirect()->back()->with('error', 'Une erreur est survenue lors de la suppression.');
        }
    }

    /**
     * Toggle the status of the specified resource.
     */
    public function toggleStatus(Request $request, string $id)
    {
        try {
            $proforma = Proforma::findOrFail($id);

            DB::beginTransaction();

            $proforma->actif_proforma = !$proforma->actif_proforma;
            $proforma->updated_by = Auth::id();
            $proforma->save();

            DB::commit();

            $statusText = $proforma->actif_proforma ? 'activée' : 'désactivée';
            Log::info("Proforma {$statusText}", ['id' => $id]);

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => $proforma,
                    'message' => "Proforma {$statusText} avec succès"
                ]);
            }

            return redirect()->back()->with('success', "Proforma {$statusText} avec succès.");

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Proforma non trouvée.'
                ], 404);
            }
            return redirect()->back()->with('error', 'Proforma non trouvée.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors du changement de statut de la proforma: ' . $e->getMessage());

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
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
     * Create a new version of the proforma.
     */
    public function creerVersion(Request $request, string $id)
    {
        try {
            $proforma = Proforma::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'motif_modification_proforma' => 'required|string|max:1000',
                'date_proforma' => 'nullable|date',
                'montant_retenu_proforma' => 'nullable|numeric|min:0',
                'taxe_montant' => 'nullable|numeric|min:0',
                'remise_montant_proforma' => 'nullable|numeric|min:0',
                'modalite_proforma' => 'nullable|string|max:255'
            ]);

            if ($validator->fails()) {
                if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation échouée.',
                        'errors' => $validator->errors()
                    ], 422);
                }
                return back()->withErrors($validator)->withInput();
            }

            $validatedData = $validator->validated();
            $motif = $validatedData['motif_modification_proforma'];
            unset($validatedData['motif_modification_proforma']);

            // Filtrer les valeurs null
            $donnees = array_filter($validatedData, fn($value) => $value !== null);
            $donnees['updated_by'] = Auth::id();

            DB::beginTransaction();

            $nouvelleVersion = $proforma->creerNouvelleVersion($donnees, $motif);

            DB::commit();

            Log::info('Nouvelle version de proforma créée', [
                'id_original' => $id,
                'id_nouvelle' => $nouvelleVersion->id_proforma,
                'version' => $nouvelleVersion->version_proforma
            ]);

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => $nouvelleVersion,
                    'message' => 'Nouvelle version créée avec succès'
                ], 201);
            }

            return redirect()->route('proformas.show', $nouvelleVersion->id_proforma)
                ->with('success', 'Nouvelle version de la proforma créée avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la création de la nouvelle version: ' . $e->getMessage());

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Une erreur est survenue.',
                    'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne'
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de la création de la nouvelle version.')
                ->withInput();
        }
    }

    /**
     * Duplicate a proforma.
     */
    // public function duplicate(Request $request, string $id)
    // {
    //     try {
    //         $proforma = Proforma::findOrFail($id);

    //         DB::beginTransaction();

    //         $nouvelleProforma = $proforma->replicate();
    //         $nouvelleProforma->id_proforma = (string) Str::uuid();
    //         $nouvelleProforma->numero_proforma = Proforma::genererNumeroProforma();
    //         $nouvelleProforma->version_proforma = 1;
    //         $nouvelleProforma->parent_id = null;
    //         $nouvelleProforma->actif_proforma = true;
    //         $nouvelleProforma->date_proforma = now();
    //         $nouvelleProforma->motif_modification_proforma = null;
    //         $nouvelleProforma->created_by = Auth::id();
    //         $nouvelleProforma->updated_by = null;
    //         $nouvelleProforma->save();

    //         DB::commit();

    //         Log::info('Proforma dupliquée avec succès', [
    //             'id_original' => $id,
    //             'id_nouvelle' => $nouvelleProforma->id_proforma
    //         ]);

    //         if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
    //             return response()->json([
    //                 'success' => true,
    //                 'data' => $nouvelleProforma,
    //                 'message' => 'Proforma dupliquée avec succès'
    //             ], 201);
    //         }

    //         return redirect()->route('proformas.edit', $nouvelleProforma->id_proforma)
    //             ->with('success', 'Proforma dupliquée avec succès. Vous pouvez maintenant la modifier.');

    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         Log::error('Erreur lors de la duplication de la proforma: ' . $e->getMessage());

    //         if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Une erreur est survenue.',
    //                 'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne'
    //             ], 500);
    //         }

    //         return redirect()->back()->with('error', 'Une erreur est survenue lors de la duplication.');
    //     }
    // }

    /**
     * Get historique of versions.
     */
    public function historique(Request $request, string $id)
    {
        try {
            $proforma = Proforma::findOrFail($id);
            $historique = $proforma->getHistorique();

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => $historique,
                    'message' => 'Historique récupéré avec succès'
                ]);
            }

            return view('proformas.historique', compact('proforma', 'historique'));

        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération de l\'historique: ' . $e->getMessage());

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
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
     * Get validation messages.
     */
    private function getValidationMessages(): array
    {
        return [
            'numero_proforma.required' => 'Le numéro de proforma est obligatoire.',
            'numero_proforma.unique' => 'Ce numéro de proforma existe déjà.',
            'numero_proforma.max' => 'Le numéro ne peut pas dépasser 20 caractères.',
            'date_proforma.required' => 'La date de la proforma est obligatoire.',
            'date_proforma.date' => 'La date n\'est pas valide.',
            'montant_retenu_proforma.required' => 'Le montant retenu est obligatoire.',
            'montant_retenu_proforma.numeric' => 'Le montant doit être un nombre.',
            'montant_retenu_proforma.min' => 'Le montant ne peut pas être négatif.',
            'taxe_montant.numeric' => 'La taxe doit être un nombre.',
            'taxe_montant.min' => 'La taxe ne peut pas être négative.',
            'remise_montant_proforma.numeric' => 'La remise doit être un nombre.',
            'remise_montant_proforma.min' => 'La remise ne peut pas être négative.',
            'modalite_proforma.max' => 'Les modalités ne peuvent pas dépasser 255 caractères.',
        ];
    }
}
