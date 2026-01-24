<?php

namespace App\Observers;

use App\Models\Evaluation;
use Illuminate\Support\Facades\Log;

/**
 * Observer pour le modèle Evaluation
 * Met à jour automatiquement l'état de l'appel d'offres
 *
 * Fichier: app/Observers/EvaluationObserver.php
 */
class EvaluationObserver
{
    /**
     * Handle the Evaluation "created" event.
     */
    public function created(Evaluation $evaluation): void
    {
        $this->mettreAJourEtatAppelOffre($evaluation);
    }

    /**
     * Handle the Evaluation "updated" event.
     */
    public function updated(Evaluation $evaluation): void
    {
        $champsImpactants = ['resultat_evaluation', 'statut_evaluation', 'is_current'];

        if ($evaluation->wasChanged($champsImpactants)) {
            $this->mettreAJourEtatAppelOffre($evaluation);
        }
    }

    /**
     * Handle the Evaluation "deleted" event.
     */
    public function deleted(Evaluation $evaluation): void
    {
        $this->mettreAJourEtatAppelOffre($evaluation);
    }

    /**
     * Handle the Evaluation "restored" event.
     */
    public function restored(Evaluation $evaluation): void
    {
        $this->mettreAJourEtatAppelOffre($evaluation);
    }

    /**
     * Mettre à jour l'état de l'appel d'offres
     * Chemin: Evaluation → CritereEvaluation → Lot → AppelOffre
     */
    protected function mettreAJourEtatAppelOffre(Evaluation $evaluation): void
    {
        try {
            // Remonter via le critère d'évaluation
            $critere = $evaluation->critereEvaluation;

            if ($critere && $critere->lot && $critere->lot->appelOffre) {
                $critere->lot->appelOffre->mettreAJourEtat(auth()->id());
            }
        } catch (\Exception $e) {
            Log::error("Erreur mise à jour état AO via Evaluation: " . $e->getMessage());
        }
    }
}
