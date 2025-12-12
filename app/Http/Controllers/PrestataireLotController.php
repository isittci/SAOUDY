<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Lot;
use App\Models\Proforma;
use App\Models\Prestataire;
use Illuminate\Http\Request;
use App\Models\PrestataireLot;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PrestataireLotController extends Controller
{
    /**
     * Afficher la liste des attributions.
     */
    public function index(Request $request)
    {
        try {
            $query = PrestataireLot::with(['prestataire', 'lot.appelOffre', 'proforma', 'createdBy', 'parentAttribution']);

            // Filtres
            if ($request->filled('statut')) {
                $query->where('statut_attribution', $request->statut);
            }

            if ($request->filled('is_active')) {
                $query->where('is_active', $request->boolean('is_active'));
            }

            if ($request->filled('prestataire_id')) {
                $query->where('prestataire_id', $request->prestataire_id);
            }

            if ($request->filled('lot_id')) {
                $query->where('lot_id', $request->lot_id);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('numero_attribution', 'like', "%{$search}%")
                      ->orWhereHas('prestataire', function ($subQ) use ($search) {
                          $subQ->where('raison_sociale_prestataire', 'like', "%{$search}%");
                      })
                      ->orWhereHas('lot', function ($subQ) use ($search) {
                          $subQ->where('numero', 'like', "%{$search}%")
                               ->orWhere('libelle', 'like', "%{$search}%");
                      });
                });
            }

            // Tri
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $allowedSorts = ['created_at', 'date_attribution', 'numero_attribution', 'pourcentage_avancement', 'statut_attribution'];
            if (in_array($sortBy, $allowedSorts)) {
                $query->orderBy($sortBy, $sortOrder);
            }

            $attributions = $query->paginate($request->get('per_page', 15));

            // Statistiques
            $statistiques = [
                'total' => PrestataireLot::count(),
                'actives' => PrestataireLot::where('is_active', true)->count(),
                'en_cours' => PrestataireLot::where('is_active', true)->where('statut_attribution', PrestataireLot::STATUT_ATTRIBUE)->count(),
                'suspendues' => PrestataireLot::where('is_active', true)->where('statut_attribution', PrestataireLot::STATUT_SUSPENDU)->count(),
                'terminees' => PrestataireLot::where('statut_attribution', PrestataireLot::STATUT_TERMINE)->count(),
                'en_retard' => PrestataireLot::where('is_active', true)->where('jours_retard', '>', 0)->count(),
            ];

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $attributions,
                    'statistiques' => $statistiques,
                ]);
            }

            $prestataires = Prestataire::where('statut_prestataire', true)->orderBy('raison_sociale_prestataire')->get();
            $lots = Lot::orderBy('numero')->get();

            return view('attributions.index', compact('attributions', 'statistiques', 'prestataires', 'lots'));

        } catch (\Exception $e) {
            Log::error('Erreur index attributions: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Erreur lors du chargement des attributions.'], 500);
            }

            return back()->with('error', 'Erreur lors du chargement des attributions.');
        }
    }

    /**
     * Formulaire de création.
     */
    public function create(Request $request)
    {
        $prestataires = Prestataire::actif()->where('statut_prestataire', true)
            ->orderBy('raison_sociale_prestataire')
            ->get();

        $lots = Lot::versionActuelle()->where('attribution_lot', 0)
            ->orWhereDoesntHave('attributionActive')
            ->with('appelOffre')
            ->orderBy('numero')
            ->get();

        $proformas = Proforma::actif()->where('actif_proforma', true)
            ->orderBy('numero_proforma', 'desc')
            ->get();

        // dd($lots);

        $lotPreselectionne = null;
        if ($request->filled('lot_id')) {
            $lotPreselectionne = Lot::find($request->lot_id);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => compact('prestataires', 'lots', 'proformas', 'lotPreselectionne'),
            ]);
        }

        return view('attributions.create', compact('prestataires', 'lots', 'proformas', 'lotPreselectionne'));
    }

    /**
     * Enregistrer une nouvelle attribution.
     */
    // public function store(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'prestataire_id' => 'required|uuid|exists:prestataires,id_prestataire',
    //         'lot_id' => 'required|uuid|exists:lots,id_lot',
    //         'proforma_id' => 'required|uuid|exists:proformas,id_proforma',
    //         'date_attribution' => 'required|date|before_or_equal:today',
    //         'date_debut_prevue' => 'required|date|after_or_equal:date_attribution',
    //         'date_fin_prevue' => 'required|date|after:date_debut_prevue',
    //         'taux_penalites' => 'nullable|numeric|min:0|max:100',
    //         'montant_engage' => 'nullable|numeric|min:0',
    //         'observations' => 'nullable|string|max:2000',
    //         'conditions_particulieres' => 'nullable|string|max:5000',
    //     ], $this->validationMessages());

    //     if ($validator->fails()) {
    //         if ($request->expectsJson()) {
    //             return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
    //         }
    //         return back()->withErrors($validator)->withInput();
    //     }

    //     try {
    //         DB::beginTransaction();

    //         // Vérifications
    //         $lot = Lot::findOrFail($request->lot_id);
    //         if (PrestataireLot::lotEstAttribue($request->lot_id)) {
    //             throw new \Exception('Ce lot est déjà attribué à un prestataire actif.');
    //         }

    //         $prestataire = Prestataire::findOrFail($request->prestataire_id);
    //         if (!$prestataire->statut_prestataire) {
    //             throw new \Exception('Le prestataire sélectionné n\'est pas actif.');
    //         }

    //         $proforma = Proforma::findOrFail($request->proforma_id);
    //         if (!$proforma->actif_proforma) {
    //             throw new \Exception('La proforma sélectionnée n\'est pas active.');
    //         }

    //         $attribution = PrestataireLot::attribuer($validator->validated());

    //         DB::commit();

    //         Log::info('Attribution créée', ['id' => $attribution->id_attribution, 'lot' => $lot->numero, 'user' => Auth::id()]);

    //         $message = "Le lot {$lot->numero} a été attribué avec succès à {$prestataire->raison_sociale_prestataire}.";

    //         if ($request->expectsJson()) {
    //             return response()->json([
    //                 'success' => true,
    //                 'message' => $message,
    //                 'data' => $attribution->load(['prestataire', 'lot', 'proforma']),
    //             ]);
    //         }

    //         return redirect()->route('attributions.show', $attribution->id_attribution)->with('success', $message);

    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         Log::error('Erreur création attribution: ' . $e->getMessage());

    //         if ($request->expectsJson()) {
    //             return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
    //         }

    //         return back()->withInput()->with('error', $e->getMessage());
    //     }
    // }






/**
 * Enregistrer une nouvelle attribution.
 * Gère deux modes : sélection proforma existante OU création nouvelle proforma
 *
 * Adapté au modèle Proforma existant (sans tva_pourcentage/remise_pourcentage)
 */
public function store(Request $request)
{
    // Règles de base
    $rules = [
        'prestataire_id' => 'required|uuid|exists:prestataires,id_prestataire',
        'lot_id' => 'required|uuid|exists:lots,id_lot',
        'proforma_mode' => 'required|in:select,create',
        'date_attribution' => 'required|date|before_or_equal:today',
        'date_debut_prevue' => 'required|date|after_or_equal:date_attribution',
        'date_fin_prevue' => 'required|date|after:date_debut_prevue',
        'taux_penalites' => 'nullable|numeric|min:0|max:100',
        'montant_engage' => 'nullable|numeric|min:0',
        'observations' => 'nullable|string|max:2000',
        'conditions_particulieres' => 'nullable|string|max:5000',
    ];

    // Règles conditionnelles selon le mode proforma
    if ($request->proforma_mode === 'select') {
        $rules['proforma_id'] = 'required|uuid|exists:proformas,id_proforma';
    } else {
        // Mode création : champs nouvelle proforma
        // Note: numero_proforma peut être auto-généré si vide
        $rules['new_numero_proforma'] = 'nullable|string|max:20|unique:proformas,numero_proforma';
        $rules['new_date_proforma'] = 'required|date|before_or_equal:today';
        $rules['new_date_redemarrage'] = 'required|date';
        $rules['new_montant_retenu'] = 'required|numeric|min:0';
        // On stocke les montants directement (pas les pourcentages dans le modèle)
        $rules['new_taxe_montant'] = 'nullable|numeric|min:0';
        $rules['new_remise_montant'] = 'nullable|numeric|min:0';
        $rules['new_penalites'] = 'nullable|numeric|min:0';
        $rules['new_modalite'] = 'nullable|string|max:500';
    }

    $messages = array_merge($this->validationMessages(), [
        'proforma_mode.required' => 'Veuillez choisir un mode de proforma.',
        'proforma_mode.in' => 'Mode de proforma invalide.',
        'new_numero_proforma.unique' => 'Ce numéro de proforma existe déjà.',
        'new_numero_proforma.max' => 'Le numéro ne peut pas dépasser 20 caractères.',
        'new_date_proforma.required' => 'La date de proforma est obligatoire.',
        'new_date_redemarrage.required' => 'La date de redémarrage est obligatoire.',
        'new_date_redemarrage.date' => 'La date de redémarrage doit être une date valide.',
        'new_montant_retenu.required' => 'Le montant retenu est obligatoire.',
        'new_montant_retenu.min' => 'Le montant retenu doit être positif.',
        'new_taxe_montant.min' => 'Le montant de la taxe doit être positif.',
        'new_remise_montant.min' => 'Le montant de la remise doit être positif.',
    ]);

    $validator = Validator::make($request->all(), $rules, $messages);

    if ($validator->fails()) {
        if ($request->expectsJson()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        return back()->withErrors($validator)->withInput();
    }

    try {
        DB::beginTransaction();

        // Vérifications lot
        $lot = Lot::findOrFail($request->lot_id);
        if (PrestataireLot::lotEstAttribue($request->lot_id)) {
            throw new \Exception('Ce lot est déjà attribué à un prestataire actif.');
        }

        // Vérification prestataire
        $prestataire = Prestataire::findOrFail($request->prestataire_id);
        if (!$prestataire->statut_prestataire) {
            throw new \Exception('Le prestataire sélectionné n\'est pas actif.');
        }

        // Gestion de la proforma selon le mode
        if ($request->proforma_mode === 'select') {
            // Mode sélection : vérifier la proforma existante
            $proforma = Proforma::findOrFail($request->proforma_id);
            if (!$proforma->actif_proforma) {
                throw new \Exception('La proforma sélectionnée n\'est pas active.');
            }
            $proformaId = $proforma->id_proforma;
        } else {
            // Mode création : créer la nouvelle proforma
            // Le numéro sera auto-généré si vide (voir boot() du modèle)
            $proformaData = [
                'date_proforma' => $request->new_date_proforma,
                'date_redemarrage_proforma' => $request->new_date_redemarrage,
                'montant_retenu_proforma' => $request->new_montant_retenu,
                'taxe_montant' => $request->new_taxe_montant ?? 0,
                'remise_montant_proforma' => $request->new_remise_montant ?? 0,
                'penalites_proforma' => $request->new_penalites ?? 0,
                'modalite_proforma' => $request->new_modalite,
                'actif_proforma' => true,
                'version_proforma' => 1,
                'created_by' => Auth::id(),
            ];

            // Numéro personnalisé si fourni, sinon auto-généré
            if (!empty($request->new_numero_proforma)) {
                $proformaData['numero_proforma'] = $request->new_numero_proforma;
            }

            $proforma = Proforma::create($proformaData);

            Log::info('Nouvelle proforma créée', [
                'id' => $proforma->id_proforma,
                'numero' => $proforma->numero_proforma,
                'montant_ht' => $proforma->montant_retenu_proforma,
                'taxe' => $proforma->taxe_montant,
                'remise' => $proforma->remise_montant_proforma,
                'ttc' => $proforma->calculerMontantTTC(),
                'user' => Auth::id()
            ]);

            $proformaId = $proforma->id_proforma;
        }

        // Préparer les données pour l'attribution
        $attributionData = [
            'prestataire_id' => $request->prestataire_id,
            'lot_id' => $request->lot_id,
            'proforma_id' => $proformaId,
            'date_attribution' => $request->date_attribution,
            'date_debut_prevue' => $request->date_debut_prevue,
            'date_fin_prevue' => $request->date_fin_prevue,
            'taux_penalites' => $request->taux_penalites ?? 0,
            'montant_engage' => $request->montant_engage ?? 0,
            'observations' => $request->observations,
            'conditions_particulieres' => $request->conditions_particulieres,
        ];

        // Créer l'attribution
        $attribution = PrestataireLot::attribuer($attributionData);

        DB::commit();

        Log::info('Attribution créée', [
            'id' => $attribution->id_attribution,
            'lot' => $lot->numero,
            'proforma_mode' => $request->proforma_mode,
            'user' => Auth::id()
        ]);

        $message = "Le lot {$lot->numero} a été attribué avec succès à {$prestataire->raison_sociale_prestataire}.";

        if ($request->proforma_mode === 'create') {
            $message .= " Une nouvelle proforma ({$proforma->numero_proforma}) a été créée.";
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $attribution->load(['prestataire', 'lot', 'proforma']),
            ]);
        }

        return redirect()
            ->route('attributions.show', $attribution->id_attribution)
            ->with('success', $message);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Erreur création attribution: ' . $e->getMessage(), [
            'request' => $request->except(['_token']),
            'trace' => $e->getTraceAsString()
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return back()->withInput()->with('error', $e->getMessage());
    }
}







    /**
     * Afficher une attribution.
     */
    public function show(Request $request, string $id)
    {
        try {
            $attribution = PrestataireLot::with([
                'prestataire',
                'lot.appelOffre',
                'proforma',
                'createdBy',
                'updatedBy',
                'parentAttribution.prestataire',
                'childAttributions.prestataire'
            ])->findOrFail($id);

            $historiqueLot = $attribution->getHistoriqueComplet();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => compact('attribution', 'historiqueLot'),
                ]);
            }

            return view('attributions.show', compact('attribution', 'historiqueLot'));

        } catch (\Exception $e) {
            Log::error('Erreur affichage attribution: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Attribution introuvable.'], 404);
            }

            return redirect()->route('attributions.index')->with('error', 'Attribution introuvable.');
        }
    }

    /**
     * Formulaire de modification.
     */
    public function edit(Request $request, string $id)
    {
        $attribution = PrestataireLot::with(['prestataire', 'lot', 'proforma'])->findOrFail($id);

        if (!$attribution->is_active) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Impossible de modifier une attribution historique.'], 422);
            }
            return back()->with('error', 'Impossible de modifier une attribution historique.');
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'data' => $attribution]);
        }

        return view('attributions.edit', compact('attribution'));
    }

    /**
     * Mettre à jour une attribution.
     */
    public function update(Request $request, string $id)
    {
        $attribution = PrestataireLot::findOrFail($id);

        if (!$attribution->is_active) {
            $message = 'Impossible de modifier une attribution historique.';
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }
            return back()->with('error', $message);
        }

        $validator = Validator::make($request->all(), [
            'observations' => 'nullable|string|max:2000',
            'conditions_particulieres' => 'nullable|string|max:5000',
            'date_debut_prevue' => 'nullable|date',
            'date_fin_prevue' => 'nullable|date|after:date_debut_prevue',
            'taux_penalites' => 'nullable|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        try {
            $attribution->update($validator->validated());

            Log::info('Attribution mise à jour', ['id' => $id, 'user' => Auth::id()]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Attribution mise à jour avec succès.',
                    'data' => $attribution->fresh(),
                ]);
            }

            return redirect()->route('attributions.show', $id)->with('success', 'Attribution mise à jour avec succès.');

        } catch (\Exception $e) {
            Log::error('Erreur mise à jour attribution: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Erreur lors de la mise à jour.'], 500);
            }

            return back()->with('error', 'Erreur lors de la mise à jour.');
        }
    }

    /**
     * Suspendre une attribution.
     */
    public function suspendre(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'motif_suspension' => 'required|string|min:10|max:2000',
            'date_reprise_prevue' => 'nullable|date|after:today',
        ], [
            'motif_suspension.required' => 'Le motif de suspension est obligatoire.',
            'motif_suspension.min' => 'Le motif doit contenir au moins 10 caractères.',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $attribution = PrestataireLot::findOrFail($id);

        if (!$attribution->peutEtreSuspendue()) {
            $message = 'Cette attribution ne peut pas être suspendue.';
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }
            return back()->with('error', $message);
        }

        try {
            $dateReprise = $request->filled('date_reprise_prevue')
                ? Carbon::parse($request->date_reprise_prevue)
                : null;

            $attribution->suspendre($request->motif_suspension, $dateReprise);

            Log::info('Attribution suspendue', ['id' => $id, 'user' => Auth::id()]);

            $message = 'Attribution suspendue avec succès.';

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => $message, 'data' => $attribution->fresh()]);
            }

            return redirect()->route('attributions.show', $id)->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Erreur suspension: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Erreur lors de la suspension.'], 500);
            }

            return back()->with('error', 'Erreur lors de la suspension.');
        }
    }

    /**
     * Reprendre une attribution suspendue.
     */
    public function reprendre(Request $request, string $id)
    {
        $attribution = PrestataireLot::findOrFail($id);

        if (!$attribution->peutEtreReprise()) {
            $message = 'Cette attribution ne peut pas être reprise.';
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }
            return back()->with('error', $message);
        }

        try {
            $attribution->reprendre($request->input('observations'));

            Log::info('Attribution reprise', ['id' => $id, 'user' => Auth::id()]);

            $message = 'Attribution reprise avec succès.';

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => $message, 'data' => $attribution->fresh()]);
            }

            return redirect()->route('attributions.show', $id)->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Erreur reprise: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Erreur lors de la reprise.'], 500);
            }

            return back()->with('error', 'Erreur lors de la reprise.');
        }
    }

    /**
     * Retirer une attribution.
     */
    public function retirer(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'motif_retrait' => 'required|string|min:10|max:2000',
            'type_retrait' => 'required|in:volontaire,force,resiliation,abandon',
        ], [
            'motif_retrait.required' => 'Le motif de retrait est obligatoire.',
            'motif_retrait.min' => 'Le motif doit contenir au moins 10 caractères.',
            'type_retrait.required' => 'Le type de retrait est obligatoire.',
            'type_retrait.in' => 'Le type de retrait est invalide.',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $attribution = PrestataireLot::findOrFail($id);

        if (!$attribution->peutEtreRetiree()) {
            $message = 'Cette attribution ne peut pas être retirée.';
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }
            return back()->with('error', $message);
        }

        try {
            $attribution->retirer($request->motif_retrait, $request->type_retrait);

            Log::info('Attribution retirée', ['id' => $id, 'user' => Auth::id()]);

            $message = 'Attribution retirée avec succès. Le lot est disponible pour réattribution.';

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => $message, 'data' => $attribution->fresh()]);
            }

            return redirect()->route('attributions.show', $id)->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Erreur retrait: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Erreur lors du retrait.'], 500);
            }

            return back()->with('error', 'Erreur lors du retrait.');
        }
    }

    /**
     * Formulaire de réattribution.
     */
    public function reattribuerForm(Request $request, string $id)
    {
        $attribution = PrestataireLot::with(['prestataire', 'lot.appelOffre', 'proforma'])->findOrFail($id);

        $prestataires = Prestataire::where('statut_prestataire', true)
            ->orderBy('raison_sociale_prestataire')
            ->get();

        $proformas = Proforma::where('actif_proforma', true)
            ->orderBy('numero_proforma', 'desc')
            ->get();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => compact('attribution', 'prestataires', 'proformas'),
            ]);
        }

        return view('attributions.reattribuer', compact('attribution', 'prestataires', 'proformas'));
    }

    /**
     * Réattribuer un lot.
     */
    public function reattribuer(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'prestataire_id' => 'required|uuid|exists:prestataires,id_prestataire',
            'proforma_id' => 'required|uuid|exists:proformas,id_proforma',
            'date_attribution' => 'required|date|before_or_equal:today',
            'date_debut_prevue' => 'required|date|after_or_equal:date_attribution',
            'date_fin_prevue' => 'required|date|after:date_debut_prevue',
            'taux_penalites' => 'nullable|numeric|min:0|max:100',
            'montant_engage' => 'nullable|numeric|min:0',
            'observations' => 'nullable|string|max:2000',
            'motif_reattribution' => 'required|string|min:10|max:2000',
        ], $this->validationMessages());

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $ancienneAttribution = PrestataireLot::findOrFail($id);

        try {
            DB::beginTransaction();

            // Retirer l'ancienne si encore active
            if ($ancienneAttribution->is_active &&
                $ancienneAttribution->statut_attribution === PrestataireLot::STATUT_ATTRIBUE) {
                $ancienneAttribution->retirer(
                    $request->motif_reattribution,
                    PrestataireLot::TYPE_RETRAIT_RESILIATION
                );
            }

            $nouvelleAttribution = $ancienneAttribution->reattribuer($validator->validated());

            DB::commit();

            Log::info('Lot réattribué', [
                'ancienne' => $id,
                'nouvelle' => $nouvelleAttribution->id_attribution,
                'user' => Auth::id()
            ]);

            $message = 'Le lot a été réattribué avec succès.';

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => $nouvelleAttribution->load(['prestataire', 'lot', 'proforma']),
                ]);
            }

            return redirect()->route('attributions.show', $nouvelleAttribution->id_attribution)->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur réattribution: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }

            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Terminer une attribution.
     */
    public function terminer(Request $request, string $id)
    {
        $attribution = PrestataireLot::findOrFail($id);

        if (!$attribution->peutEtreTerminee()) {
            $message = 'Cette attribution ne peut pas être terminée.';
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }
            return back()->with('error', $message);
        }

        try {
            $attribution->terminer($request->input('observations'));

            Log::info('Attribution terminée', ['id' => $id, 'user' => Auth::id()]);

            $message = 'Attribution terminée avec succès.';

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => $message, 'data' => $attribution->fresh()]);
            }

            return redirect()->route('attributions.show', $id)->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Erreur terminaison: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Erreur lors de la terminaison.'], 500);
            }

            return back()->with('error', 'Erreur lors de la terminaison.');
        }
    }

    /**
     * Mettre à jour l'avancement.
     */
    public function mettreAJourAvancement(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'pourcentage_avancement' => 'required|numeric|min:0|max:100',
            'observations' => 'nullable|string|max:2000',
            'date_debut_reelle' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $attribution = PrestataireLot::findOrFail($id);

        if (!$attribution->is_active || $attribution->statut_attribution !== PrestataireLot::STATUT_ATTRIBUE) {
            $message = 'L\'avancement ne peut être mis à jour que pour une attribution active.';
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }
            return back()->with('error', $message);
        }

        try {
            if ($request->filled('date_debut_reelle') && !$attribution->date_debut_reelle) {
                $attribution->date_debut_reelle = $request->date_debut_reelle;
                $attribution->save();
            }

            $attribution->mettreAJourAvancement($request->pourcentage_avancement, $request->observations);

            Log::info('Avancement mis à jour', ['id' => $id, 'pourcentage' => $request->pourcentage_avancement]);

            $message = 'Avancement mis à jour avec succès.';

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => $message, 'data' => $attribution->fresh()]);
            }

            return redirect()->route('attributions.show', $id)->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Erreur avancement: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Erreur lors de la mise à jour.'], 500);
            }

            return back()->with('error', 'Erreur lors de la mise à jour.');
        }
    }

    /**
     * Historique d'un lot.
     */
    public function historiqueLot(Request $request, string $lotId)
    {
        try {
            $lot = Lot::with('appelOffre')->findOrFail($lotId);

            $historique = PrestataireLot::where('lot_id', $lotId)
                ->with(['prestataire', 'proforma', 'createdBy'])
                ->orderBy('version_attribution', 'asc')
                ->get();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => compact('lot', 'historique'),
                ]);
            }

            return view('attributions.historique-lot', compact('lot', 'historique'));

        } catch (\Exception $e) {
            Log::error('Erreur historique lot: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Lot introuvable.'], 404);
            }

            return back()->with('error', 'Lot introuvable.');
        }
    }

    /**
     * Historique d'un prestataire.
     */
    public function historiquePrestataire(Request $request, string $prestataireId)
    {
        try {
            $prestataire = Prestataire::findOrFail($prestataireId);

            $historique = PrestataireLot::where('prestataire_id', $prestataireId)
                ->with(['lot.appelOffre', 'proforma'])
                ->orderBy('created_at', 'desc')
                ->get();

            $statistiques = PrestataireLot::statistiquesPrestataire($prestataireId);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => compact('prestataire', 'historique', 'statistiques'),
                ]);
            }

            return view('attributions.historique-prestataire', compact('prestataire', 'historique', 'statistiques'));

        } catch (\Exception $e) {
            Log::error('Erreur historique prestataire: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Prestataire introuvable.'], 404);
            }

            return back()->with('error', 'Prestataire introuvable.');
        }
    }

    /**
     * Tableau de bord.
     */
    public function dashboard(Request $request)
    {
        $statistiques = [
            'total' => PrestataireLot::count(),
            'actives' => PrestataireLot::where('is_active', true)->count(),
            'en_cours' => PrestataireLot::where('is_active', true)->where('statut_attribution', PrestataireLot::STATUT_ATTRIBUE)->count(),
            'suspendues' => PrestataireLot::where('is_active', true)->where('statut_attribution', PrestataireLot::STATUT_SUSPENDU)->count(),
            'terminees' => PrestataireLot::where('statut_attribution', PrestataireLot::STATUT_TERMINE)->count(),
            'retirees' => PrestataireLot::where('statut_attribution', PrestataireLot::STATUT_RETIRE)->count(),
            'en_retard' => PrestataireLot::where('is_active', true)->where('jours_retard', '>', 0)->count(),
            'montant_total_engage' => PrestataireLot::sum('montant_engage'),
            'montant_total_paye' => PrestataireLot::sum('montant_paye'),
        ];

        $dernieresAttributions = PrestataireLot::with(['prestataire', 'lot'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $attributionsEnRetard = PrestataireLot::where('is_active', true)
            ->where('jours_retard', '>', 0)
            ->with(['prestataire', 'lot'])
            ->orderBy('date_fin_prevue', 'asc')
            ->limit(10)
            ->get();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => compact('statistiques', 'dernieresAttributions', 'attributionsEnRetard'),
            ]);
        }

        return view('attributions.dashboard', compact('statistiques', 'dernieresAttributions', 'attributionsEnRetard'));
    }

/**
 * Messages de validation personnalisés
 */
private function validationMessages(): array
{
    return [
        'prestataire_id.required' => 'Veuillez sélectionner un prestataire.',
        'prestataire_id.exists' => 'Le prestataire sélectionné n\'existe pas.',
        'lot_id.required' => 'Veuillez sélectionner un lot.',
        'lot_id.exists' => 'Le lot sélectionné n\'existe pas.',
        'proforma_id.required' => 'Veuillez sélectionner une proforma.',
        'proforma_id.exists' => 'La proforma sélectionnée n\'existe pas.',
        'date_attribution.required' => 'La date d\'attribution est obligatoire.',
        'date_attribution.before_or_equal' => 'La date d\'attribution ne peut pas être dans le futur.',
        'date_debut_prevue.required' => 'La date de début est obligatoire.',
        'date_debut_prevue.after_or_equal' => 'La date de début doit être égale ou postérieure à la date d\'attribution.',
        'date_fin_prevue.required' => 'La date de fin est obligatoire.',
        'date_fin_prevue.after' => 'La date de fin doit être postérieure à la date de début.',
        'taux_penalites.numeric' => 'Le taux de pénalités doit être un nombre.',
        'taux_penalites.min' => 'Le taux de pénalités ne peut pas être négatif.',
        'taux_penalites.max' => 'Le taux de pénalités ne peut pas dépasser 100%.',
        'montant_engage.numeric' => 'Le montant engagé doit être un nombre.',
        'montant_engage.min' => 'Le montant engagé ne peut pas être négatif.',
        'observations.max' => 'Les observations ne peuvent pas dépasser 2000 caractères.',
        'conditions_particulieres.max' => 'Les conditions particulières ne peuvent pas dépasser 5000 caractères.',
    ];
}
}
