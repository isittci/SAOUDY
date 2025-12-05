<?php

namespace App\Http\Controllers;

use App\Models\TypeAppelOffre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Exception;

class TypeAppelOffreController extends Controller
{
    /**
     * Affiche la liste des types d'appels d'offres
     */
    public function index(Request $request)
    {

        try {
            $query = TypeAppelOffre::with(['creator', 'updater'])
                ->withCount('appelOffres');

            // Filtres
            if ($request->filled('actif')) {
                $query->where('actif_type_appel_offre', $request->actif);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('libelle_type_appel_offre', 'like', "%{$search}%")
                      ->orWhere('code_type_appel_offre', 'like', "%{$search}%");
                });
            }

            // Tri
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 15);
            $typesAO = $query->paginate($perPage);

            // Retour selon le type de requête
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => $typesAO,
                    'message' => 'Liste des types d\'appels d\'offres récupérée avec succès'
                ]);
            }

            return view('types-appels-offres.index', compact('typesAO'));

        } catch (Exception $e) {
            Log::error('Erreur lors de la récupération des types AO: ' . $e->getMessage());

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
     * Enregistre un nouveau type d'appel d'offres
     */
    public function store(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'libelle_type_appel_offre' => 'required|string|max:160',
            'code_type_appel_offre' => 'required|string|max:10|unique:types_appels_offres,code_type_appel_offre',
            'valeur_minimuim_type_appel_offre' => 'required|numeric|min:0',
            'valeur_maximuim_type_appel_offre' => 'required|numeric|gt:valeur_minimuim_type_appel_offre',
            'description_critere_type_appel_offre' => 'nullable|string',
            'actif_type_appel_offre' => 'boolean',
        ], [
            'libelle_type_appel_offre.required' => 'Le libellé est obligatoire',
            'code_type_appel_offre.required' => 'Le code est obligatoire',
            'code_type_appel_offre.unique' => 'Ce code existe déjà',
            'valeur_minimuim_type_appel_offre.required' => 'La valeur minimale est obligatoire',
            'valeur_maximuim_type_appel_offre.required' => 'La valeur maximale est obligatoire',
            'valeur_maximuim_type_appel_offre.gt' => 'La valeur maximale doit être supérieure à la valeur minimale',
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
            $typeAO = TypeAppelOffre::create([
                'libelle_type_appel_offre' => $request->libelle_type_appel_offre,
                'code_type_appel_offre' => strtoupper($request->code_type_appel_offre),
                'valeur_minimuim_type_appel_offre' => $request->valeur_minimuim_type_appel_offre,
                'valeur_maximuim_type_appel_offre' => $request->valeur_maximuim_type_appel_offre,
                'description_critere_type_appel_offre' => $request->description_critere_type_appel_offre,
                'actif_type_appel_offre' => $request->get('actif_type_appel_offre', true),
                'created_by' => auth()->id(),
            ]);

            DB::commit();

            Log::info("Type d'AO créé avec succès", ['id' => $typeAO->id_type_appel_offre]);

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => $typeAO->load('creator'),
                    'message' => 'Type d\'appel d\'offres créé avec succès'
                ], 201);
            }

            return redirect()->route('types-appels-offres.show', $typeAO->id_type_appel_offre)
                ->with('success', 'Type d\'appel d\'offres créé avec succès');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la création du type AO: ' . $e->getMessage());

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
     * Affiche les détails d'un type d'appel d'offres
     */
    public function show(Request $request, $id)
    {
        try {
            $typeAO = TypeAppelOffre::with([
                'appelOffres' => function($q) {
                    $q->latest()->limit(10);
                },
                'creator',
                'updater',
                'deleter'
            ])->withCount('appelOffres')
              ->findOrFail($id);

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => $typeAO,
                    'message' => 'Détails récupérés avec succès'
                ]);
            }

            return view('types-appels-offres.show', compact('typeAO'));

        } catch (Exception $e) {
            Log::error('Erreur lors de la récupération du type AO: ' . $e->getMessage());

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Type d\'appel d\'offres introuvable',
                    'error' => $e->getMessage()
                ], 404);
            }

            return back()->with('error', 'Type d\'appel d\'offres introuvable');
        }
    }

    /**
     * Affiche le formulaire de modification
     */
    public function edit($id)
    {
        try {
            $typeAO = TypeAppelOffre::findOrFail($id);
            return view('types-appels-offres.edit', compact('typeAO'));
        } catch (Exception $e) {
            return back()->with('error', 'Type d\'appel d\'offres introuvable');
        }
    }

    /**
     * Met à jour un type d'appel d'offres
     */
    public function update(Request $request, $id)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'libelle_type_appel_offre' => 'required|string|max:160',
            'code_type_appel_offre' => 'required|string|max:10|unique:types_appels_offres,code_type_appel_offre,' . $id . ',id_type_appel_offre',
            'valeur_minimuim_type_appel_offre' => 'required|numeric|min:0',
            'valeur_maximuim_type_appel_offre' => 'required|numeric|gt:valeur_minimuim_type_appel_offre',
            'description_critere_type_appel_offre' => 'nullable|string',
            'actif_type_appel_offre' => 'boolean',
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
            $typeAO = TypeAppelOffre::findOrFail($id);

            // Vérifier si le type est utilisé et si on change les valeurs
            if ($typeAO->appel_offres_count > 0) {
                $changementValeurs =
                    $typeAO->valeur_minimuim_type_appel_offre != $request->valeur_minimuim_type_appel_offre ||
                    $typeAO->valeur_maximuim_type_appel_offre != $request->valeur_maximuim_type_appel_offre;

                if ($changementValeurs) {
                    if ($request->wantsJson() || $request->is('api/*')) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Impossible de modifier les valeurs. Ce type est déjà utilisé dans des appels d\'offres.'
                        ], 422);
                    }

                    return back()->with('error', 'Impossible de modifier les valeurs. Ce type est déjà utilisé.');
                }
            }

            $typeAO->update([
                'libelle_type_appel_offre' => $request->libelle_type_appel_offre,
                'code_type_appel_offre' => strtoupper($request->code_type_appel_offre),
                'valeur_minimuim_type_appel_offre' => $request->valeur_minimuim_type_appel_offre,
                'valeur_maximuim_type_appel_offre' => $request->valeur_maximuim_type_appel_offre,
                'description_critere_type_appel_offre' => $request->description_critere_type_appel_offre,
                'actif_type_appel_offre' => $request->get('actif_type_appel_offre', $typeAO->actif_type_appel_offre),
                'updated_by' => auth()->id(),
            ]);

            DB::commit();

            Log::info("Type d'AO mis à jour", ['id' => $typeAO->id_type_appel_offre]);

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => $typeAO->load('updater'),
                    'message' => 'Type d\'appel d\'offres mis à jour avec succès'
                ]);
            }

            return redirect()->route('types-appels-offres.show', $typeAO->id_type_appel_offre)
                ->with('success', 'Type d\'appel d\'offres mis à jour avec succès');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la mise à jour du type AO: ' . $e->getMessage());

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la mise à jour',
                    'error' => $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Erreur lors de la mise à jour')->withInput();
        }
    }

    /**
     * Supprime un type d'appel d'offres
     */
    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $typeAO = TypeAppelOffre::withCount('appelOffres')->findOrFail($id);

            // Vérifier si le type est utilisé
            if ($typeAO->appel_offres_count > 0) {
                if ($request->wantsJson() || $request->is('api/*')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Impossible de supprimer ce type. Il est utilisé dans ' .
                                   $typeAO->appel_offres_count . ' appel(s) d\'offres.'
                    ], 422);
                }

                return back()->with('error', 'Impossible de supprimer ce type. Il est utilisé.');
            }

            $typeAO->deleted_by = auth()->id();
            $typeAO->save();
            $typeAO->delete();

            DB::commit();

            Log::info("Type d'AO supprimé", ['id' => $id]);

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'message' => 'Type d\'appel d\'offres supprimé avec succès'
                ]);
            }

            return redirect()->route('types-appels-offres.index')
                ->with('success', 'Type d\'appel d\'offres supprimé avec succès');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la suppression du type AO: ' . $e->getMessage());

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
     * Active/Désactive un type d'appel d'offres
     */
    public function toggleStatus(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $typeAO = TypeAppelOffre::findOrFail($id);

            $typeAO->actif_type_appel_offre = !$typeAO->actif_type_appel_offre;
            $typeAO->updated_by = auth()->id();
            $typeAO->save();

            DB::commit();

            $statut = $typeAO->actif_type_appel_offre ? 'activé' : 'désactivé';

            Log::info("Type d'AO {$statut}", ['id' => $id]);

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => $typeAO,
                    'message' => "Type d'appel d'offres {$statut} avec succès"
                ]);
            }

            return back()->with('success', "Type d'appel d'offres {$statut} avec succès");

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
     * Vérifie si un montant correspond à un type d'AO
     */
    public function checkMontant(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'montant' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Montant invalide',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $montant = $request->montant;
            $types = TypeAppelOffre::actif()
                ->byValeur($montant)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $types,
                'message' => count($types) > 0
                    ? 'Type(s) correspondant(s) trouvé(s)'
                    : 'Aucun type ne correspond à ce montant'
            ]);

        } catch (Exception $e) {
            Log::error('Erreur lors de la vérification du montant: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la vérification',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Génère un numéro d'AO pour un type donné
     */
    public function genererNumero(Request $request, $id)
    {
        try {
            $typeAO = TypeAppelOffre::findOrFail($id);
            $annee = $request->get('annee', date('Y'));

            $numero = $typeAO->genererNumeroAppelOffre($annee);

            return response()->json([
                'success' => true,
                'data' => [
                    'numero' => $numero,
                    'annee' => $annee,
                    'type' => $typeAO->code_type_appel_offre
                ],
                'message' => 'Numéro généré avec succès'
            ]);

        } catch (Exception $e) {
            Log::error('Erreur lors de la génération du numéro: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la génération du numéro',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Exporte les types d'AO (CSV, Excel, PDF)
     */
    public function export(Request $request)
    {
        try {
            $format = $request->get('format', 'csv');
            $types = TypeAppelOffre::with('creator')->get();

            // Implémentation selon le format souhaité
            // Pour l'instant, retour JSON
            return response()->json([
                'success' => true,
                'data' => $types,
                'message' => 'Export en cours de développement'
            ]);

        } catch (Exception $e) {
            Log::error('Erreur lors de l\'export: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'export',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
