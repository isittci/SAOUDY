<?php

namespace App\Observers;

use App\Models\AttributionLotPrestataire;
use Illuminate\Support\Facades\Log;

/**
 * Observer pour le modèle AttributionLotPrestataire
 * Met à jour automatiquement l'état de l'appel d'offres via le lot
 */
class AttributionObserver
{
    /**
     * Handle the Attribution "created" event.
     */
    public function created(AttributionLotPrestataire $attribution): void
    {
        $this->mettreAJourEtatAppelOffre($attribution);
    }

    /**
     * Handle the Attribution "updated" event.
     */
    public function updated(AttributionLotPrestataire $attribution): void
    {
        // Vérifier si des champs impactant l'état ont changé
        $champsImpactants = ['statut_attribution', 'is_active', 'date_retrait'];

        if ($attribution->wasChanged($champsImpactants)) {
            $this->mettreAJourEtatAppelOffre($attribution);
        }
    }

    /**
     * Handle the Attribution "deleted" event.
     */
    public function deleted(AttributionLotPrestataire $attribution): void
    {
        $this->mettreAJourEtatAppelOffre($attribution);
    }

    /**
     * Handle the Attribution "restored" event.
     */
    public function restored(AttributionLotPrestataire $attribution): void
    {
        $this->mettreAJourEtatAppelOffre($attribution);
    }

    /**
     * Mettre à jour l'état de l'appel d'offres via le lot
     */
    protected function mettreAJourEtatAppelOffre(AttributionLotPrestataire $attribution): void
    {
        if ($attribution->lot && $attribution->lot->appelOffre) {
            try {
                $attribution->lot->appelOffre->mettreAJourEtat(auth()->id());

                Log::debug("État AO mis à jour via Attribution", [
                    'attribution_id' => $attribution->id_attribution,
                    'lot_id' => $attribution->lot_id,
                    'appel_offre_id' => $attribution->lot->appelOffre->id_appel_offre,
                    'nouvel_etat' => $attribution->lot->appelOffre->etat_label,
                ]);
            } catch (\Exception $e) {
                Log::error("Erreur mise à jour état AO via Attribution: " . $e->getMessage(), [
                    'attribution_id' => $attribution->id_attribution,
                ]);
            }
        }
    }
}
