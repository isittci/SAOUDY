<?php

namespace App\Listeners;

use App\Events\PaiementValidated;
use Illuminate\Support\Facades\Log;

class UpdateAppelOffreEtatOnPaiementValidated
{
    /**
     * Handle the event.
     */
    public function handle(PaiementValidated $event): void
    {
        try {
            $paiement = $event->paiement;
            $facture = $paiement->facture;

            if (!$facture) {
                return;
            }

            $proforma = $facture->proforma;
            if (!$proforma) {
                return;
            }

            $attribution = $proforma->attribution;
            if ($attribution && $attribution->lot && $attribution->lot->appelOffre) {
                $attribution->lot->appelOffre->mettreAJourEtat(auth()->id());
            }
        } catch (\Exception $e) {
            Log::error("Erreur mise à jour état AO via Paiement validé: " . $e->getMessage());
        }
    }
}
