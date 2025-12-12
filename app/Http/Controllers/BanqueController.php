<?php

namespace App\Http\Controllers;

use App\Models\Banque;
use App\Models\Prestataire;
use App\Http\Requests\StoreBanqueRequest;
use App\Http\Requests\UpdateBanqueRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class BanqueController extends Controller
{
    /**
     * Afficher la liste des banques d'un prestataire.
     *
     * @param Request $request
     * @param string $prestataireId
     * @return JsonResponse|View
     */
    public function index(Request $request, string $prestataireId)
    {
        try {
            // Récupérer le prestataire
            $prestataire = Prestataire::findOrFail($prestataireId);

            $query = Banque::with(['createur', 'modificateur'])
                ->where('prestataire_id', $prestataireId);

            // Recherche
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('nom_banque', 'LIKE', "%{$search}%")
                      ->orWhere('code_banque', 'LIKE', "%{$search}%")
                      ->orWhere('numero_compte_banque', 'LIKE', "%{$search}%")
                      ->orWhere('iban_banque', 'LIKE', "%{$search}%")
                      ->orWhere('swift_bic_banque', 'LIKE', "%{$search}%")
                      ->orWhere('titulaire_compte_banque', 'LIKE', "%{$search}%");
                });
            }

            // Filtrage par statut
            if ($request->filled('statut')) {
                if ($request->statut === 'actif') {
                    $query->where('actif_banque', true);
                } elseif ($request->statut === 'inactif') {
                    $query->where('actif_banque', false);
                }
            }

            // Tri
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $allowedSorts = ['nom_banque', 'code_banque', 'numero_compte_banque', 'actif_banque', 'created_at', 'updated_at'];

            if (in_array($sortBy, $allowedSorts)) {
                $query->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');
            } else {
                $query->orderBy('created_at', 'desc');
            }

            // Pagination
            $perPage = $request->get('per_page', 15);
            $banques = $query->paginate($perPage);

            // Statistiques
            $stats = [
                'total' => Banque::where('prestataire_id', $prestataireId)->count(),
                'actives' => Banque::where('prestataire_id', $prestataireId)->where('actif_banque', true)->count(),
                'inactives' => Banque::where('prestataire_id', $prestataireId)->where('actif_banque', false)->count(),
                'avec_paiements' => Banque::where('prestataire_id', $prestataireId)->whereHas('paiements')->count(),
            ];

            // Réponse JSON ou Vue
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Liste des banques récupérée avec succès.',
                    'data' => [
                        'banques' => $banques,
                        'prestataire' => $prestataire,
                        'stats' => $stats,
                    ],
                ], 200);
            }

            return view('banques.index', compact('banques', 'prestataire', 'stats'));

        } catch (Exception $e) {
            Log::error('Erreur lors de la récupération des banques: ' . $e->getMessage());

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Une erreur est survenue lors de la récupération des banques.',
                    'error' => config('app.debug') ? $e->getMessage() : null,
                ], 500);
            }

            return back()->with('error', 'Une erreur est survenue lors de la récupération des banques.');
        }
    }

    /**
     * Afficher le formulaire de création d'une banque.
     *
     * @param Request $request
     * @param string $prestataireId
     * @return JsonResponse|View
     */
    public function create(Request $request, string $prestataireId)
    {
        $prestataire = Prestataire::findOrFail($prestataireId);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Formulaire de création prêt.',
                'data' => [
                    'prestataire' => $prestataire,
                ],
            ], 200);
        }

        return view('banques.create', compact('prestataire'));
    }

    /**
     * Enregistrer une nouvelle banque.
     *
     * @param StoreBanqueRequest $request
     * @param string $prestataireId
     * @return JsonResponse|RedirectResponse
     */
    public function store(StoreBanqueRequest $request, string $prestataireId)
    {
        try {
            // Vérifier que le prestataire existe
            $prestataire = Prestataire::findOrFail($prestataireId);

            DB::beginTransaction();

            $data = $request->validated();
            $data['prestataire_id'] = $prestataireId;
            $data['created_by'] = auth()->id();
            $data['actif_banque'] = $request->boolean('actif_banque', true);

            $banque = Banque::create($data);
            $banque->load(['createur']);

            DB::commit();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Banque créée avec succès.',
                    'data' => $banque,
                ], 201);
            }

            return redirect()
                ->route('banques.show', ['prestataireId' => $prestataireId, 'banque' => $banque->id_banque])
                ->with('success', 'Banque créée avec succès.');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la création de la banque: ' . $e->getMessage());

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Une erreur est survenue lors de la création de la banque.',
                    'error' => config('app.debug') ? $e->getMessage() : null,
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la création de la banque.');
        }
    }

    /**
     * Afficher les détails d'une banque.
     *
     * @param Request $request
     * @param string $prestataireId
     * @param Banque $banque
     * @return JsonResponse|View
     */
    public function show(Request $request, string $prestataireId, Banque $banque)
    {
        try {
            // Vérifier que la banque appartient bien au prestataire
            $this->verifierAppartenancePrestataire($banque, $prestataireId);

            $prestataire = Prestataire::findOrFail($prestataireId);

            $banque->load([
                'paiements' => function ($query) {
                    $query->orderBy('created_at', 'desc')->limit(10);
                },
                'createur',
                'modificateur',
            ]);

            // Statistiques
            $stats = [
                'nombre_paiements' => $banque->nombrePaiements(),
                'montant_total_paiements' => $banque->montantTotalPaiements(),
                'rib_complet' => $banque->isRibComplet(),
                'has_iban' => $banque->hasIban(),
                'has_swift' => $banque->hasSwift(),
            ];

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Détails de la banque récupérés avec succès.',
                    'data' => [
                        'banque' => $banque,
                        'prestataire' => $prestataire,
                        'stats' => $stats,
                    ],
                ], 200);
            }

            return view('banques.show', compact('banque', 'prestataire', 'stats'));

        } catch (Exception $e) {
            Log::error('Erreur lors de la récupération de la banque: ' . $e->getMessage());

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Une erreur est survenue lors de la récupération de la banque.',
                    'error' => config('app.debug') ? $e->getMessage() : null,
                ], 500);
            }

            return back()->with('error', 'Une erreur est survenue lors de la récupération de la banque.');
        }
    }

    /**
     * Afficher le formulaire d'édition d'une banque.
     *
     * @param Request $request
     * @param string $prestataireId
     * @param Banque $banque
     * @return JsonResponse|View
     */
    public function edit(Request $request, string $prestataireId, Banque $banque)
    {
        // Vérifier que la banque appartient bien au prestataire
        $this->verifierAppartenancePrestataire($banque, $prestataireId);

        $prestataire = Prestataire::findOrFail($prestataireId);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Données pour modification récupérées.',
                'data' => [
                    'banque' => $banque,
                    'prestataire' => $prestataire,
                ],
            ], 200);
        }

        return view('banques.edit', compact('banque', 'prestataire'));
    }

    /**
     * Mettre à jour une banque.
     *
     * @param UpdateBanqueRequest $request
     * @param string $prestataireId
     * @param Banque $banque
     * @return JsonResponse|RedirectResponse
     */
    public function update(UpdateBanqueRequest $request, string $prestataireId, Banque $banque)
    {
        try {
            // Vérifier que la banque appartient bien au prestataire
            $this->verifierAppartenancePrestataire($banque, $prestataireId);

            DB::beginTransaction();

            $data = $request->validated();
            $data['updated_by'] = auth()->id();

            if ($request->has('actif_banque')) {
                $data['actif_banque'] = $request->boolean('actif_banque');
            }

            $banque->update($data);
            $banque->load(['createur', 'modificateur']);

            DB::commit();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Banque mise à jour avec succès.',
                    'data' => $banque,
                ], 200);
            }

            return redirect()
                ->route('banques.show', ['prestataireId' => $prestataireId, 'banque' => $banque->id_banque])
                ->with('success', 'Banque mise à jour avec succès.');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la mise à jour de la banque: ' . $e->getMessage());

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Une erreur est survenue lors de la mise à jour de la banque.',
                    'error' => config('app.debug') ? $e->getMessage() : null,
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la mise à jour de la banque.');
        }
    }

    /**
     * Supprimer une banque (soft delete).
     *
     * @param Request $request
     * @param string $prestataireId
     * @param Banque $banque
     * @return JsonResponse|RedirectResponse
     */
    public function destroy(Request $request, string $prestataireId, Banque $banque)
    {
        try {
            // Vérifier que la banque appartient bien au prestataire
            $this->verifierAppartenancePrestataire($banque, $prestataireId);

            // Vérifier s'il y a des paiements associés
            if ($banque->hasPaiements()) {
                $message = 'Impossible de supprimer cette banque car elle possède des paiements associés.';

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $message,
                    ], 422);
                }

                return back()->with('error', $message);
            }

            DB::beginTransaction();

            $banque->deleted_by = auth()->id();
            $banque->save();
            $banque->delete();

            DB::commit();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Banque supprimée avec succès.',
                ], 200);
            }

            return redirect()
                ->route('banques.index', ['prestataireId' => $prestataireId])
                ->with('success', 'Banque supprimée avec succès.');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la suppression de la banque: ' . $e->getMessage());

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Une erreur est survenue lors de la suppression de la banque.',
                    'error' => config('app.debug') ? $e->getMessage() : null,
                ], 500);
            }

            return back()->with('error', 'Une erreur est survenue lors de la suppression de la banque.');
        }
    }

    /**
     * Basculer le statut d'une banque (actif/inactif).
     *
     * @param Request $request
     * @param string $prestataireId
     * @param Banque $banque
     * @return JsonResponse|RedirectResponse
     */
    public function toggleStatut(Request $request, string $prestataireId, Banque $banque)
    {
        try {
            // Vérifier que la banque appartient bien au prestataire
            $this->verifierAppartenancePrestataire($banque, $prestataireId);

            $banque->toggleStatut();

            $message = $banque->actif_banque
                ? 'Banque activée avec succès.'
                : 'Banque désactivée avec succès.';

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => [
                        'actif_banque' => $banque->actif_banque,
                        'statut_format' => $banque->statut_format,
                    ],
                ], 200);
            }

            return back()->with('success', $message);

        } catch (Exception $e) {
            Log::error('Erreur lors du changement de statut de la banque: ' . $e->getMessage());

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Une erreur est survenue lors du changement de statut.',
                    'error' => config('app.debug') ? $e->getMessage() : null,
                ], 500);
            }

            return back()->with('error', 'Une erreur est survenue lors du changement de statut.');
        }
    }

    /**
     * Dupliquer une banque.
     *
     * @param Request $request
     * @param string $prestataireId
     * @param Banque $banque
     * @return JsonResponse|RedirectResponse
     */
    public function dupliquer(Request $request, string $prestataireId, Banque $banque)
    {
        try {
            // Vérifier que la banque appartient bien au prestataire
            $this->verifierAppartenancePrestataire($banque, $prestataireId);

            DB::beginTransaction();

            $nouvelleBanque = $banque->dupliquer();
            $nouvelleBanque->load(['createur']);

            DB::commit();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Banque dupliquée avec succès.',
                    'data' => $nouvelleBanque,
                ], 201);
            }

            return redirect()
                ->route('banques.edit', ['prestataireId' => $prestataireId, 'banque' => $nouvelleBanque->id_banque])
                ->with('success', 'Banque dupliquée avec succès.');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la duplication de la banque: ' . $e->getMessage());

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Une erreur est survenue lors de la duplication de la banque.',
                    'error' => config('app.debug') ? $e->getMessage() : null,
                ], 500);
            }

            return back()->with('error', 'Une erreur est survenue lors de la duplication de la banque.');
        }
    }

    /**
     * Restaurer une banque supprimée.
     *
     * @param Request $request
     * @param string $prestataireId
     * @param string $id
     * @return JsonResponse|RedirectResponse
     */
    public function restore(Request $request, string $prestataireId, string $id)
    {
        try {
            $banque = Banque::withTrashed()
                ->where('prestataire_id', $prestataireId)
                ->findOrFail($id);

            DB::beginTransaction();

            $banque->restore();
            $banque->deleted_by = null;
            $banque->updated_by = auth()->id();
            $banque->save();

            DB::commit();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Banque restaurée avec succès.',
                    'data' => $banque,
                ], 200);
            }

            return redirect()
                ->route('banques.show', ['prestataireId' => $prestataireId, 'banque' => $banque->id_banque])
                ->with('success', 'Banque restaurée avec succès.');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la restauration de la banque: ' . $e->getMessage());

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Une erreur est survenue lors de la restauration de la banque.',
                    'error' => config('app.debug') ? $e->getMessage() : null,
                ], 500);
            }

            return back()->with('error', 'Une erreur est survenue lors de la restauration de la banque.');
        }
    }

    /**
     * Suppression définitive d'une banque.
     *
     * @param Request $request
     * @param string $prestataireId
     * @param string $id
     * @return JsonResponse|RedirectResponse
     */
    public function forceDelete(Request $request, string $prestataireId, string $id)
    {
        try {
            $banque = Banque::withTrashed()
                ->where('prestataire_id', $prestataireId)
                ->findOrFail($id);

            // Vérifier s'il y a des paiements associés
            if ($banque->hasPaiements()) {
                $message = 'Impossible de supprimer définitivement cette banque car elle possède des paiements associés.';

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $message,
                    ], 422);
                }

                return back()->with('error', $message);
            }

            DB::beginTransaction();

            $banque->forceDelete();

            DB::commit();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Banque supprimée définitivement.',
                ], 200);
            }

            return redirect()
                ->route('banques.index', ['prestataireId' => $prestataireId])
                ->with('success', 'Banque supprimée définitivement.');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la suppression définitive de la banque: ' . $e->getMessage());

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Une erreur est survenue lors de la suppression définitive.',
                    'error' => config('app.debug') ? $e->getMessage() : null,
                ], 500);
            }

            return back()->with('error', 'Une erreur est survenue lors de la suppression définitive.');
        }
    }

    /**
     * Afficher les banques supprimées (corbeille).
     *
     * @param Request $request
     * @param string $prestataireId
     * @return JsonResponse|View
     */
    public function trashed(Request $request, string $prestataireId)
    {
        try {
            $prestataire = Prestataire::findOrFail($prestataireId);

            $query = Banque::onlyTrashed()
                ->where('prestataire_id', $prestataireId)
                ->with(['suppresseur']);

            // Recherche
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('nom_banque', 'LIKE', "%{$search}%")
                      ->orWhere('code_banque', 'LIKE', "%{$search}%");
                });
            }

            $perPage = $request->get('per_page', 15);
            $banques = $query->orderBy('deleted_at', 'desc')
                ->paginate($perPage)
                ->withQueryString();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Banques supprimées récupérées avec succès.',
                    'data' => [
                        'banques' => $banques,
                        'prestataire' => $prestataire,
                    ],
                ], 200);
            }

            return view('banques.trashed', compact('banques', 'prestataire'));

        } catch (Exception $e) {
            Log::error('Erreur lors de la récupération des banques supprimées: ' . $e->getMessage());

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
     * Exporter les banques en différents formats.
     *
     * @param Request $request
     * @param string $prestataireId
     * @return JsonResponse
     */
    public function export(Request $request, string $prestataireId)
    {
        try {
            $prestataire = Prestataire::findOrFail($prestataireId);

            $banques = Banque::where('prestataire_id', $prestataireId)
                ->orderBy('nom_banque')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Export généré avec succès.',
                'data' => [
                    'prestataire' => $prestataire,
                    'banques' => $banques,
                    'count' => $banques->count(),
                ],
            ], 200);

        } catch (Exception $e) {
            Log::error('Erreur lors de l\'export des banques: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de l\'export.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Récupérer les banques d'un prestataire (pour AJAX).
     *
     * @param Request $request
     * @param string $prestataireId
     * @return JsonResponse
     */
    public function getByPrestataire(Request $request, string $prestataireId)
    {
        try {
            $banques = Banque::where('prestataire_id', $prestataireId)
                ->where('actif_banque', true)
                ->orderBy('nom_banque')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Banques du prestataire récupérées avec succès.',
                'data' => $banques,
            ], 200);

        } catch (Exception $e) {
            Log::error('Erreur lors de la récupération des banques du prestataire: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Vérifier que la banque appartient bien au prestataire.
     *
     * @param Banque $banque
     * @param string $prestataireId
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    private function verifierAppartenancePrestataire(Banque $banque, string $prestataireId): void
    {
        if ($banque->prestataire_id !== $prestataireId) {
            abort(404, 'Cette banque n\'appartient pas à ce prestataire.');
        }
    }
}
