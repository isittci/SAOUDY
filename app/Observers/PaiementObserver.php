<?php

namespace App\Observers;

use App\Models\Paiement;
use Illuminate\Support\Facades\Log;

/**
 * Observer pour le modèle Paiement
 * Met à jour automatiquement l'état de l'appel d'offres
 *
 * Fichier: app/Observers/PaiementObserver.php
 */
class PaiementObserver
{
    /**
     * Handle the Paiement "created" event.
     */
    public function created(Paiement $paiement): void
    {
        $this->mettreAJourEtatAppelOffre($paiement);
    }

    public function validated(Paiement $paiement): void
    {
        $this->mettreAJourEtatAppelOffre($paiement);
    }

    /**
     * Handle the Paiement "updated" event.
     */
    public function updated(Paiement $paiement): void
    {
        $champsImpactants = ['montant_net_paye_paiement', 'statut_paiement'];

        if ($paiement->wasChanged($champsImpactants)) {
            $this->mettreAJourEtatAppelOffre($paiement);
        }
    }

    /**
     * Handle the Paiement "deleted" event.
     */
    public function deleted(Paiement $paiement): void
    {
        $this->mettreAJourEtatAppelOffre($paiement);
    }

    /**
     * Handle the Paiement "restored" event.
     */
    public function restored(Paiement $paiement): void
    {
        $this->mettreAJourEtatAppelOffre($paiement);
    }

    /**
     * Mettre à jour l'état de l'appel d'offres
     * Chemin: Paiement → Facture → Proforma → Attribution → Lot → AppelOffre
     */
    protected function mettreAJourEtatAppelOffre(Paiement $paiement): void
    {
        try {
            $facture = $paiement->facture;

            if (!$facture) {
                return;
            }

            $proforma = $facture->proforma;

            if (!$proforma) {
                return;
            }

            // Obtenir l'attribution via la proforma
            $attribution = $proforma->attribution;

            if ($attribution && $attribution->lot && $attribution->lot->appelOffre) {
                $attribution->lot->appelOffre->mettreAJourEtat(auth()->id());
            }
        } catch (\Exception $e) {
            Log::error("Erreur mise à jour état AO via Paiement: " . $e->getMessage());
        }
    }
}
