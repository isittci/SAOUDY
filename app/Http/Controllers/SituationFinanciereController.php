<?php

namespace App\Http\Controllers;

use App\Models\SituationFinanciere;
use App\Models\Prestataire;
use App\Http\Requests\StoreSituationFinanciereRequest;
use App\Http\Requests\UpdateSituationFinanciereRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SituationFinanciereController extends Controller
{
    /**
     * Afficher la liste des situations financières d'un prestataire
     */
    public function index(Request $request, $prestataireId)
    {
        $prestataire = Prestataire::findOrFail($prestataireId);

        $situations = $prestataire->situationsFinancieres()
            ->when($request->filled('exercice'), function ($query) use ($request) {
                $query->where('exercice_fiscal_situation_financiere', $request->exercice);
            })
            ->when($request->filled('resultat'), function ($query) use ($request) {
                if ($request->resultat === 'positif') {
                    $query->where('resultat_net_situation_financiere', '>=', 0);
                } else {
                    $query->where('resultat_net_situation_financiere', '<', 0);
                }
            })
            ->orderBy('exercice_fiscal_situation_financiere', 'desc')
            ->paginate(10)
            ->withQueryString();

        // Statistiques
        $stats = [
            'total' => $prestataire->situationsFinancieres()->count(),
            'ca_total' => $prestataire->situationsFinancieres()->sum('chiffre_affaire_situation_financiere'),
            'ca_moyen' => $prestataire->situationsFinancieres()->avg('chiffre_affaire_situation_financiere'),
            'resultat_positif' => $prestataire->situationsFinancieres()->where('resultat_net_situation_financiere', '>=', 0)->count(),
            'dernier_exercice' => $prestataire->situationsFinancieres()->max('exercice_fiscal_situation_financiere'),
        ];

        // Dernière situation pour comparaison
        $derniereSituation = $prestataire->situationsFinancieres()
            ->orderBy('exercice_fiscal_situation_financiere', 'desc')
            ->first();

        // Liste des exercices pour le filtre
        $exercices = $prestataire->situationsFinancieres()
            ->distinct()
            ->orderBy('exercice_fiscal_situation_financiere', 'desc')
            ->pluck('exercice_fiscal_situation_financiere')
            ->filter();

        return view('situations-financieres.index', compact('prestataire', 'situations', 'stats', 'derniereSituation', 'exercices'));
    }

    /**
     * Formulaire de création
     */
    public function create($prestataireId)
    {
        $prestataire = Prestataire::findOrFail($prestataireId);

        // Générer les exercices fiscaux possibles (5 dernières années + année en cours)
        $anneeActuelle = (int) date('Y');
        $exercices = [];
        for ($i = 0; $i <= 5; $i++) {
            $annee = $anneeActuelle - $i;
            $exercices[$annee] = $annee;
        }

        return view('situations-financieres.create', compact('prestataire', 'exercices'));
    }

    /**
     * Enregistrer une nouvelle situation financière
     */
    public function store(StoreSituationFinanciereRequest $request, $prestataireId)
    {
        $prestataire = Prestataire::findOrFail($prestataireId);

        // Vérifier si une situation existe déjà pour cet exercice
        $existante = SituationFinanciere::where('prestataire_id', $prestataireId)
            ->where('exercice_fiscal_situation_financiere', $request->exercice_fiscal_situation_financiere)
            ->exists();

        if ($existante) {
            return back()
                ->withInput()
                ->with('error', 'Une situation financière existe déjà pour l\'exercice ' . $request->exercice_fiscal_situation_financiere);
        }

        try {
            DB::beginTransaction();

            $situation = SituationFinanciere::create([
                'prestataire_id' => $prestataireId,
                'exercice_fiscal_situation_financiere' => $request->exercice_fiscal_situation_financiere,
                'chiffre_affaire_situation_financiere' => $request->chiffre_affaire_situation_financiere,
                'fonds_propres_situation_financiere' => $request->fonds_propres_situation_financiere,
                'capacite_emprunt_situation_financiere' => $request->capacite_emprunt_situation_financiere,
                'ratio_solvabilite_situation_financiere' => $request->ratio_solvabilite_situation_financiere,
                'ratio_liquidite_situation_financiere' => $request->ratio_liquidite_situation_financiere,
                'resultat_net_situation_financiere' => $request->resultat_net_situation_financiere,
                'total_actif_situation_financiere' => $request->total_actif_situation_financiere,
                'total_passif_situation_financiere' => $request->total_passif_situation_financiere,
                'observations_situation_financiere' => $request->observations_situation_financiere,
                'created_by' => auth()->id(),
            ]);

            DB::commit();

            return redirect()
                ->route('prestataires.situations-financieres.index', $prestataireId)
                ->with('success', 'Situation financière pour l\'exercice ' . $situation->exercice_fiscal_situation_financiere . ' ajoutée avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erreur lors de l\'ajout de la situation financière', [
                'prestataire_id' => $prestataireId,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Erreur lors de l\'ajout: ' . $e->getMessage());
        }
    }

    /**
     * Afficher une situation financière
     */
    public function show($prestataireId, $situationId)
    {
        $prestataire = Prestataire::findOrFail($prestataireId);
        $situation = SituationFinanciere::where('prestataire_id', $prestataireId)
            ->findOrFail($situationId);

        // Situation précédente pour comparaison
        $situationPrecedente = SituationFinanciere::where('prestataire_id', $prestataireId)
            ->where('exercice_fiscal_situation_financiere', '<', $situation->exercice_fiscal_situation_financiere)
            ->orderBy('exercice_fiscal_situation_financiere', 'desc')
            ->first();

        $comparaison = null;
        if ($situationPrecedente) {
            $comparaison = $situation->comparerAvec($situationPrecedente);
        }

        return view('situations-financieres.show', compact('prestataire', 'situation', 'situationPrecedente', 'comparaison'));
    }

    /**
     * Formulaire d'édition
     */
    public function edit($prestataireId, $situationId)
    {
        $prestataire = Prestataire::findOrFail($prestataireId);
        $situation = SituationFinanciere::where('prestataire_id', $prestataireId)
            ->findOrFail($situationId);

        // Générer les exercices fiscaux possibles
        $anneeActuelle = (int) date('Y');
        $exercices = [];
        for ($i = 0; $i <= 10; $i++) {
            $annee = $anneeActuelle - $i;
            $exercices[$annee] = $annee;
        }

        return view('situations-financieres.edit', compact('prestataire', 'situation', 'exercices'));
    }

    /**
     * Mettre à jour une situation financière
     */
    public function update(UpdateSituationFinanciereRequest $request, $prestataireId, $situationId)
    {
        $prestataire = Prestataire::findOrFail($prestataireId);
        $situation = SituationFinanciere::where('prestataire_id', $prestataireId)
            ->findOrFail($situationId);

        // Vérifier si l'exercice est déjà utilisé par une autre situation
        $existante = SituationFinanciere::where('prestataire_id', $prestataireId)
            ->where('exercice_fiscal_situation_financiere', $request->exercice_fiscal_situation_financiere)
            ->where('id_situation_financiere', '!=', $situationId)
            ->exists();

        if ($existante) {
            return back()
                ->withInput()
                ->with('error', 'Une autre situation financière existe déjà pour l\'exercice ' . $request->exercice_fiscal_situation_financiere);
        }

        try {
            DB::beginTransaction();

            $situation->update([
                'exercice_fiscal_situation_financiere' => $request->exercice_fiscal_situation_financiere,
                'chiffre_affaire_situation_financiere' => $request->chiffre_affaire_situation_financiere,
                'fonds_propres_situation_financiere' => $request->fonds_propres_situation_financiere,
                'capacite_emprunt_situation_financiere' => $request->capacite_emprunt_situation_financiere,
                'ratio_solvabilite_situation_financiere' => $request->ratio_solvabilite_situation_financiere,
                'ratio_liquidite_situation_financiere' => $request->ratio_liquidite_situation_financiere,
                'resultat_net_situation_financiere' => $request->resultat_net_situation_financiere,
                'total_actif_situation_financiere' => $request->total_actif_situation_financiere,
                'total_passif_situation_financiere' => $request->total_passif_situation_financiere,
                'observations_situation_financiere' => $request->observations_situation_financiere,
                'updated_by' => auth()->id(),
            ]);

            DB::commit();

            return redirect()
                ->route('prestataires.situations-financieres.index', $prestataireId)
                ->with('success', 'Situation financière mise à jour avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erreur lors de la mise à jour de la situation financière', [
                'situation_id' => $situationId,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    /**
     * Supprimer une situation financière
     */
    public function destroy(Request $request, $prestataireId, $situationId)
    {
        $situation = SituationFinanciere::where('prestataire_id', $prestataireId)
            ->findOrFail($situationId);

        $exercice = $situation->exercice_fiscal_situation_financiere;

        try {
            DB::beginTransaction();

            $situation->deleted_by = auth()->id();
            $situation->save();
            $situation->delete();

            DB::commit();

            return redirect()
                ->route('prestataires.situations-financieres.index', $prestataireId)
                ->with('success', 'Situation financière de l\'exercice ' . $exercice . ' supprimée avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erreur lors de la suppression de la situation financière', [
                'situation_id' => $situationId,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    /**
     * Graphique d'évolution pour un prestataire
     */
    public function evolution($prestataireId)
    {
        $prestataire = Prestataire::findOrFail($prestataireId);

        $situations = $prestataire->situationsFinancieres()
            ->orderBy('exercice_fiscal_situation_financiere', 'asc')
            ->get();

        $data = [
            'labels' => $situations->pluck('exercice_fiscal_situation_financiere'),
            'chiffre_affaires' => $situations->pluck('chiffre_affaire_situation_financiere'),
            'resultat_net' => $situations->pluck('resultat_net_situation_financiere'),
            'fonds_propres' => $situations->pluck('fonds_propres_situation_financiere'),
        ];

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
}
