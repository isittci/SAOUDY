<?php

namespace App\Http\Controllers;

use App\Models\Lot;
use App\Models\Proforma;
use App\Models\Prestataire;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PrestataireControllerCopy extends Controller
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
                $query->where(function ($query) use ($search) {
                    $query->where('raison_sociale_prestataire', 'like', "%{$search}%")
                        ->orWhere('numero_identification_prestataire', 'like', "%{$search}%")
                        ->orWhere('numero_cc_prestataire', 'like', "%{$search}%")
                        ->orWhere('numero_rccm_prestataire', 'like', "%{$search}%")
                        ->orWhere('telephone_prestataire', 'like', "%{$search}%")
                        ->orWhere('email_prestataire', 'like', "%{$search}%")
                        ->orWhere('ville_prestataire', 'like', "%{$search}%")
                        ->orWhere('pays', 'like', "%{$search}%");
                });
            }

            // Tri
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 10);
            $prestataires = $query->paginate($perPage);

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => $prestataires,
                    'message' => 'Liste des prestataires récupérée avec succès'
                ]);
            }

            return view('prestataires.index', compact('prestataires'));
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Une erreur est survenue lors de la récupération des prestataires.',
                    'error' => $e->getMessage()
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
            $proformas = Proforma::where('actif_proforma', 1)->orderByRaw('LOWER(numero_proforma) ASC')->get();
            $lotsNonAssignes = Lot::where('attribution_lot', 0)->orderByRaw('LOWER(numero) ASC')->get();

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
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Une erreur est survenue lors de la récupération du formulaire de création.',
                    'error' => $e->getMessage()
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
            // Validation des données principales
            $validator = Validator::make($request->all(), [
                'raison_sociale_prestataire' => 'required|string|max:260',
                'numero_identification_prestataire' => 'required|string|max:25',
                'email_prestataire' => 'required|email|max:255|unique:prestataires,email_prestataire',
                'numero_cc_prestataire' => 'required|string|max:50|unique:prestataires,numero_cc_prestataire',
                'numero_rccm_prestataire' => 'required|string|max:50|unique:prestataires,numero_rccm_prestataire',
                'telephone_principal_prestataire' => 'required|string|max:20|unique:prestataires,telephone_principal_prestataire',
                'telephone_secondaire_prestataire' => 'nullable|string|max:20',
                'adresse_prestataire' => 'required|string|max:150',
                'ville_prestataire' => 'required|string|max:50',
                'pays_prestataire' => 'required|string|max:50',
                // 'representant_legal_prestataire' => 'required|json',
            ], [
                'email_prestataire.unique' => 'L\'adresse email du prestataire existe déjà.',
                'email_prestataire.required' => 'L\'adresse email du prestataire est obligatoire.',
                'representant_legal_prestataire.json' => 'Le format des informations du représentant légal est invalide.',
                'raison_sociale_prestataire.required' => 'La raison sociale du prestataire est obligatoire.',
                'numero_identification_prestataire.required' => 'Le numéro d\'identification du prestataire est obligatoire.',
                'numero_identification_prestataire.unique' => 'Le numéro d\'identification du prestataire est déjà utilisé.',
                'telephone_principal_prestataire.required' => 'Le téléphone principal du prestataire est obligatoire.',
                'telephone_principal_prestataire.unique' => 'Le téléphone principal du prestataire est déjà utilisé.',
                'adresse_prestataire.required' => 'L\'adresse du prestataire est obligatoire.',
                'ville_prestataire.required' => 'La ville du prestataire est obligatoire.',
                'pays_prestataire.required' => 'Le pays du prestataire est obligatoire.',
            ]);


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


            // Décodage et validation du représentant légal
            // $representant = json_decode($validatedData['representant_legal_prestataire'], true);

            $representantValidator = Validator::make($request->all(), [
                'nom' => 'required|string|max:100',
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
            ], [
                'nom.required' => 'Le nom du représentant légal est obligatoire.',
                'contact.required' => 'Le contact du représentant légal est obligatoire.',
                'email.required' => 'L\'email du représentant légal est obligatoire.',
                'nationalite.required' => 'La nationalité du représentant légal est obligatoire.',
                'pays.required' => 'Le pays du représentant légal est obligatoire.',
                'adresse.required' => 'L\'adresse du représentant légal est obligatoire.',
                'profession.required' => 'La profession du représentant légal est obligatoire.',
                'date_naissance.required' => 'La date de naissance du représentant légal est obligatoire.',
                'lieu_naissance.required' => 'Le lieu de naissance du représentant légal est obligatoire.',
                'numero_piece_identite.required' => 'Le numéro de la pièce d\'identité du représentant légal est obligatoire.',
                'type_piece_identite.required' => 'Le type de pièce d\'identité du représentant légal est obligatoire.',
                'date_delivrance.required' => 'La date de délivrance de la pièce d\'identité du représentant légal est obligatoire.',
                'lieu_delivrance.required' => 'Le lieu de délivrance de la pièce d\'identité du représentant légal est obligatoire.',
                'date_expiration.required' => 'La date d\'expiration de la pièce d\'identité du représentant légal est obligatoire.',
                'date_expiration.after' => 'La date d\'expiration doit être postérieure à la date de délivrance.',
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

            // Formater le représentant légal avec ID et statut
            $premierRepresentant = $representantValidator->validate();
            $premierRepresentant['id'] = (string) Str::uuid();
            $premierRepresentant['statut'] = 1;

            $representantsLegaux = [$premierRepresentant];

            DB::beginTransaction();

            // Préparation des données pour la création
            $dataToCreate = $validatedData;
            $dataToCreate['id_prestataire'] = (string) Str::uuid();
            $dataToCreate['representant_legal_prestataire'] = json_encode($representantsLegaux);
            $dataToCreate['statut_prestataire'] = $request->input('statut_prestataire', false);
            $dataToCreate['created_by'] = Auth::id();

            // Création du prestataire
            $prestataire = Prestataire::create($dataToCreate);

            DB::commit();

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
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Une erreur est survenue lors de la création du prestataire.',
                    'error' => $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Une erreur est survenue lors de la création du prestataire: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        try {
            $prestataire = Prestataire::findOrFail($id);

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => $prestataire,
                    'message' => 'Prestataire récupéré avec succès.'
                ]);
            }

            return view('prestataires.show', compact('prestataire'));

        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Une erreur est survenue lors de la récupération du prestataire.',
                    'error' => $e->getMessage()
                ], 404);
            }
            return redirect()->back()->with('error', 'Prestataire non trouvé.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, string $id)
    {
        try {
            $prestataire = Prestataire::findOrFail($id);
            $proformas = Proforma::where('actif_proforma', 1)->orderByRaw('LOWER(numero_proforma) ASC')->get();
            $lotsNonAssignes = Lot::where('attribution_lot', 0)->orderByRaw('LOWER(numero) ASC')->get();

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
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Prestataire non trouvé.',
                    'error' => $e->getMessage()
                ], 404);
            }
            return redirect()->back()->with('error', 'Prestataire non trouvé.');
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
                'raison_sociale_prestataire' => 'required|string|max:260',
                'numero_identification_prestataire' => 'required|string|max:25',
                'email_prestataire' => 'required|email|max:255|unique:prestataires,email_prestataire,' . $id . ',id_prestataire',
                'numero_cc_prestataire' => 'required|string|max:50',
                'numero_rccm_prestataire' => 'required|string|max:50',
                // 'contact_principal_prestataire' => 'required|string|max:20',
                // 'contact_secondaire_prestataire' => 'nullable|string|max:20',
                'telephone_prestataire' => 'required|string|max:20',
                'adresse_prestataire' => 'required|string|max:150',
                'ville_prestataire' => 'required|string|max:50',
                'pays' => 'required|string|max:50',
                'representant_legal_prestataire' => 'nullable|json',
                'statut_prestataire' => 'nullable|boolean',
            ], [
                'email_prestataire.unique' => 'L\'adresse email du prestataire existe déjà.',
                'raison_sociale_prestataire.required' => 'La raison sociale du prestataire est obligatoire.',
                'numero_identification_prestataire.required' => 'Le numéro d\'identification du prestataire est obligatoire.',
                'contact_principal_prestataire.required' => 'Le contact principal du prestataire est obligatoire.',
                'telephone_prestataire.required' => 'Le téléphone du prestataire est obligatoire.',
                'adresse_prestataire.required' => 'L\'adresse du prestataire est obligatoire.',
                'ville_prestataire.required' => 'La ville du prestataire est obligatoire.',
                'pays.required' => 'Le pays du prestataire est obligatoire.',
            ]);

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

            // Gestion du représentant légal
            if (isset($validatedData['representant_legal_prestataire'])) {
                $representant = json_decode($validatedData['representant_legal_prestataire'], true);

                // Validation du représentant légal
                $representantValidator = Validator::make($representant, [
                    'nom' => 'required|string|max:100',
                    'prenoms' => 'required|string|max:150',
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
                $representantsExistants = json_decode($prestataire->representant_legal_prestataire, true);
                $trouve = false;

                // Vérifier si c'est une mise à jour ou un nouvel ajout
                if (isset($representant['email']) && !empty($representant['email'])) {
                    foreach ($representantsExistants as &$rep) {
                        if ($rep['email'] === $representant['email']) {
                            // Mise à jour du représentant existant
                            $rep = array_merge($rep, $representant);
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
                        $representantsExistants[] = $representant;
                    }

                    $validatedData['representant_legal_prestataire'] = json_encode($representantsExistants);
                }
            }

            DB::beginTransaction();

            // Mise à jour
            $validatedData['updated_by'] = Auth::id();
            $prestataire->update($validatedData);

            DB::commit();

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => $prestataire->fresh(),
                    'message' => 'Prestataire mis à jour avec succès'
                ]);
            }

            return redirect()->route('prestataires.index')->with('success', 'Prestataire mis à jour avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Une erreur est survenue lors de la mise à jour du prestataire.',
                    'error' => $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Une erreur est survenue lors de la mise à jour du prestataire: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        try {
            $prestataire = Prestataire::findOrFail($id);

            DB::beginTransaction();

            // Soft delete avec traçabilité
            $prestataire->deleted_by = Auth::id();
            $prestataire->save();
            $prestataire->delete();

            DB::commit();

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'message' => 'Prestataire supprimé avec succès'
                ]);
            }

            return redirect()->route('prestataires.index')->with('success', 'Prestataire supprimé avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Une erreur est survenue lors de la suppression du prestataire.',
                    'error' => $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Une erreur est survenue lors de la suppression du prestataire.');
        }
    }
}
