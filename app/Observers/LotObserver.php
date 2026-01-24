<?php

namespace App\Observers;

use App\Models\Lot;
use Illuminate\Support\Facades\Log;

/**
 * Observer pour le modèle Lot
 * Met à jour automatiquement l'état de l'appel d'offres parent
 */
class LotObserver
{
    /**
     * Handle the Lot "created" event.
     */
    public function created(Lot $lot): void
    {
        $this->mettreAJourEtatAppelOffre($lot);
    }

    /**
     * Handle the Lot "updated" event.
     */
    public function updated(Lot $lot): void
    {
        // Vérifier si des champs impactant l'état ont changé
        $champsImpactants = ['attribution_lot', 'date_retrait', 'statut_lot', 'statut_retrait'];

        if ($lot->wasChanged($champsImpactants)) {
            $this->mettreAJourEtatAppelOffre($lot);
        }
    }

    /**
     * Handle the Lot "deleted" event.
     */
    public function deleted(Lot $lot): void
    {
        $this->mettreAJourEtatAppelOffre($lot);
    }

    /**
     * Handle the Lot "restored" event.
     */
    public function restored(Lot $lot): void
    {
        $this->mettreAJourEtatAppelOffre($lot);
    }

    /**
     * Mettre à jour l'état de l'appel d'offres parent
     */
    protected function mettreAJourEtatAppelOffre(Lot $lot): void
    {
        if ($lot->appelOffre) {
            try {
                $lot->appelOffre->mettreAJourEtat(auth()->id());

                Log::debug("État AO mis à jour via Lot", [
                    'lot_id' => $lot->id_lot,
                    'appel_offre_id' => $lot->appelOffre->id_appel_offre,
                    'nouvel_etat' => $lot->appelOffre->etat_label,
                ]);
            } catch (\Exception $e) {
                Log::error("Erreur mise à jour état AO via Lot: " . $e->getMessage(), [
                    'lot_id' => $lot->id_lot,
                ]);
            }
        }
    }
}
