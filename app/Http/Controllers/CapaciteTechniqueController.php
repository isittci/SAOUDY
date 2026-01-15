<?php

namespace App\Http\Controllers;

use App\Models\CapaciteTechnique;
use App\Models\Prestataire;
use App\Http\Requests\StoreCapaciteTechniqueRequest;
use App\Http\Requests\UpdateCapaciteTechniqueRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CapaciteTechniqueController extends Controller
{
    /**
     * Afficher la liste des capacités techniques d'un prestataire
     */
    public function index(Request $request, $prestataireId)
    {
        $prestataire = Prestataire::findOrFail($prestataireId);

        $capacites = $prestataire->capacitesTechniques()
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('certifications_capacite_technique', 'ILIKE', '%' . $request->search . '%')
                      ->orWhere('agrements_capacite_technique', 'ILIKE', '%' . $request->search . '%')
                      ->orWhere('domaines_expertise_capacite_technique', 'ILIKE', '%' . $request->search . '%');
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        // Statistiques
        $stats = [
            'total' => $prestataire->capacitesTechniques()->count(),
            'effectif_total' => $prestataire->capacitesTechniques()
                ->selectRaw('COALESCE(SUM(effectif_permanent_capacite_technique), 0) + COALESCE(SUM(effectif_temporaire_capacite_technique), 0) as total')
                ->value('total'),
            'avec_certifications' => $prestataire->capacitesTechniques()->avecCertifications()->count(),
            'avec_agrements' => $prestataire->capacitesTechniques()->avecAgrements()->count(),
        ];

        return view('prestataires.capacites-techniques.index', compact('prestataire', 'capacites', 'stats'));
    }

    /**
     * Formulaire de création
     */
    public function create($prestataireId)
    {
        $prestataire = Prestataire::findOrFail($prestataireId);

        return view('prestataires.capacites-techniques.create', compact('prestataire'));
    }

    /**
     * Enregistrer une nouvelle capacité technique
     */
    public function store(StoreCapaciteTechniqueRequest $request, $prestataireId)
    {
        $prestataire = Prestataire::findOrFail($prestataireId);

        try {
            DB::beginTransaction();

            $capacite = CapaciteTechnique::create([
                'prestataire_id' => $prestataireId,
                'effectif_permanent_capacite_technique' => $request->effectif_permanent_capacite_technique,
                'effectif_temporaire_capacite_technique' => $request->effectif_temporaire_capacite_technique,
                'moyens_materiels_capacite_technique' => $request->moyens_materiels_capacite_technique,
                'certifications_capacite_technique' => $request->certifications_capacite_technique,
                'agrements_capacite_technique' => $request->agrements_capacite_technique,
                'references_capacite_technique' => $request->references_capacite_technique,
                'competences_cles_capacite_technique' => $request->competences_cles_capacite_technique,
                'domaines_expertise_capacite_technique' => $request->domaines_expertise_capacite_technique,
                'created_by' => auth()->id(),
            ]);

            DB::commit();

            return redirect()
                ->route('prestataires.capacites-techniques.index', $prestataireId)
                ->with('success', 'Capacité technique ajoutée avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erreur lors de l\'ajout de la capacité technique', [
                'prestataire_id' => $prestataireId,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Erreur lors de l\'ajout: ' . $e->getMessage());
        }
    }

    /**
     * Afficher une capacité technique
     */
    public function show($prestataireId, $capaciteId)
    {
        $prestataire = Prestataire::findOrFail($prestataireId);
        $capacite = CapaciteTechnique::where('prestataire_id', $prestataireId)
            ->findOrFail($capaciteId);

        return view('prestataires.capacites-techniques.show', compact('prestataire', 'capacite'));
    }

    /**
     * Formulaire d'édition
     */
    public function edit($prestataireId, $capaciteId)
    {
        $prestataire = Prestataire::findOrFail($prestataireId);
        $capacite = CapaciteTechnique::where('prestataire_id', $prestataireId)
            ->findOrFail($capaciteId);

        return view('prestataires.capacites-techniques.edit', compact('prestataire', 'capacite'));
    }

    /**
     * Mettre à jour une capacité technique
     */
    public function update(UpdateCapaciteTechniqueRequest $request, $prestataireId, $capaciteId)
    {
        $prestataire = Prestataire::findOrFail($prestataireId);
        $capacite = CapaciteTechnique::where('prestataire_id', $prestataireId)
            ->findOrFail($capaciteId);

        try {
            DB::beginTransaction();

            $capacite->update([
                'effectif_permanent_capacite_technique' => $request->effectif_permanent_capacite_technique,
                'effectif_temporaire_capacite_technique' => $request->effectif_temporaire_capacite_technique,
                'moyens_materiels_capacite_technique' => $request->moyens_materiels_capacite_technique,
                'certifications_capacite_technique' => $request->certifications_capacite_technique,
                'agrements_capacite_technique' => $request->agrements_capacite_technique,
                'references_capacite_technique' => $request->references_capacite_technique,
                'competences_cles_capacite_technique' => $request->competences_cles_capacite_technique,
                'domaines_expertise_capacite_technique' => $request->domaines_expertise_capacite_technique,
                'updated_by' => auth()->id(),
            ]);

            DB::commit();

            return redirect()
                ->route('prestataires.capacites-techniques.index', $prestataireId)
                ->with('success', 'Capacité technique mise à jour avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erreur lors de la mise à jour de la capacité technique', [
                'capacite_id' => $capaciteId,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    /**
     * Supprimer une capacité technique
     */
    public function destroy(Request $request, $prestataireId, $capaciteId)
    {
        $capacite = CapaciteTechnique::where('prestataire_id', $prestataireId)
            ->findOrFail($capaciteId);

        try {
            DB::beginTransaction();

            $capacite->deleted_by = auth()->id();
            $capacite->save();
            $capacite->delete();

            DB::commit();

            return redirect()
                ->route('prestataires.capacites-techniques.index', $prestataireId)
                ->with('success', 'Capacité technique supprimée avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erreur lors de la suppression de la capacité technique', [
                'capacite_id' => $capaciteId,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }
}
