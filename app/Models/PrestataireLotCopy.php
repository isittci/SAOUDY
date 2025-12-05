<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PrestataireLotCopy extends Pivot
{
    use SoftDeletes, HasUuids;

    protected $table = 'prestataires_lots';

    // Très important : empêcher Laravel de chercher une colonne "id"
    protected $primaryKey = null;
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * Constantes pour les statuts d'attribution
     */
    const STATUT_EN_ATTENTE = 0;
    const STATUT_ATTRIBUE = 1;
    const STATUT_SUSPENDU = 2;
    const STATUT_RETIRE = 3;
    const STATUT_TERMINE = 4;

    protected $fillable = [
        'prestataire_id',
        'lot_id',
        'proforma_id',
        'date_debut_reelle',
        'date_fin_reelle',
        'statut_attribution',
        'motif_suspension',
        'date_suspension',
        'motif_retrait',
        'date_retrait',
        'jours_retard',
        'penalites_appliquees',
        'pourcentage_avancement',
        'observations',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'date_debut_reelle' => 'date',
        'date_fin_reelle' => 'date',
        'date_suspension' => 'datetime',
        'date_retrait' => 'datetime',
        'statut_attribution' => 'integer',
        'jours_retard' => 'integer',
        'penalites_appliquees' => 'decimal:2',
        'pourcentage_avancement' => 'decimal:2',
    ];

    /**
     * ================================================================
     * RELATIONS
     * ================================================================
     */

    /**
     * Prestataire
     */
    public function prestataire()
    {
        return $this->belongsTo(Prestataire::class, 'prestataire_id', 'id_prestataire');
    }

    /**
     * Lot
     */
    public function lot()
    {
        return $this->belongsTo(Lot::class, 'lot_id', 'id_lot');
    }

    /**
     * Proforma
     */
    public function proforma()
    {
        return $this->belongsTo(Proforma::class, 'proforma_id', 'id_proforma');
    }

    /**
     * Créateur
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Dernière personne à avoir modifié
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Personne ayant supprimé/retiré
     */
    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * ================================================================
     * SCOPES
     * ================================================================
     */

    /**
     * Attributions en attente
     */
    public function scopeEnAttente($query)
    {
        return $query->where('statut_attribution', self::STATUT_EN_ATTENTE);
    }

    /**
     * Attributions actives (attribuées)
     */
    public function scopeAttribue($query)
    {
        return $query->where('statut_attribution', self::STATUT_ATTRIBUE);
    }

    /**
     * Attributions suspendues
     */
    public function scopeSuspendu($query)
    {
        return $query->where('statut_attribution', self::STATUT_SUSPENDU);
    }

    /**
     * Attributions retirées
     */
    public function scopeRetire($query)
    {
        return $query->where('statut_attribution', self::STATUT_RETIRE);
    }

    /**
     * Attributions terminées
     */
    public function scopeTermine($query)
    {
        return $query->where('statut_attribution', self::STATUT_TERMINE);
    }

    /**
     * Attributions en cours (attribuées ou suspendues)
     */
    public function scopeEnCours($query)
    {
        return $query->whereIn('statut_attribution', [self::STATUT_ATTRIBUE, self::STATUT_SUSPENDU]);
    }

    /**
     * Attributions avec retard
     */
    public function scopeEnRetard($query)
    {
        return $query->where('jours_retard', '>', 0);
    }

    /**
     * ================================================================
     * MÉTHODES UTILITAIRES
     * ================================================================
     */

    /**
     * Attribuer le lot
     */
    public function attribuer($userId = null)
    {
        $this->statut_attribution = self::STATUT_ATTRIBUE;
        $this->date_debut_reelle = $this->date_debut_reelle ?? now();
        $this->updated_by = $userId;
        $this->save();

        // Mettre à jour le statut du lot
        $this->lot->attribuer();

        return $this;
    }

    /**
     * Suspendre l'attribution
     */
    public function suspendre($motif, $userId = null)
    {
        if ($this->statut_attribution !== self::STATUT_ATTRIBUE) {
            throw new \Exception("Seule une attribution active peut être suspendue.");
        }

        $this->statut_attribution = self::STATUT_SUSPENDU;
        $this->motif_suspension = $motif;
        $this->date_suspension = now();
        $this->updated_by = $userId;
        $this->save();

        return $this;
    }

    /**
     * Reprendre après suspension
     */
    public function reprendre($userId = null)
    {
        if ($this->statut_attribution !== self::STATUT_SUSPENDU) {
            throw new \Exception("Seule une attribution suspendue peut être reprise.");
        }

        $this->statut_attribution = self::STATUT_ATTRIBUE;
        $this->updated_by = $userId;
        $this->save();

        return $this;
    }

    /**
     * Retirer l'attribution
     */
    public function retirer($motif, $userId = null)
    {
        $this->statut_attribution = self::STATUT_RETIRE;
        $this->motif_retrait = $motif;
        $this->date_retrait = now();
        $this->deleted_by = $userId;
        $this->save();

        // Soft delete
        $this->delete();

        return $this;
    }

    /**
     * Marquer comme terminé
     */
    public function terminer($userId = null)
    {
        if (!in_array($this->statut_attribution, [self::STATUT_ATTRIBUE, self::STATUT_SUSPENDU])) {
            throw new \Exception("Seule une attribution en cours peut être marquée comme terminée.");
        }

        $this->statut_attribution = self::STATUT_TERMINE;
        $this->date_fin_reelle = $this->date_fin_reelle ?? now();
        $this->pourcentage_avancement = 100;
        $this->updated_by = $userId;

        // Calculer les retards et pénalités
        $this->calculerRetards();

        $this->save();

        return $this;
    }

    /**
     * Réattribuer à un nouveau prestataire
     */
    public function reattribuerA(Prestataire $nouveauPrestataire, Proforma $nouvelleProforma, $motif, $userId = null)
    {
        // Retirer l'attribution actuelle
        $this->retirer($motif, $userId);

        // Créer une nouvelle attribution
        $nouvelleAttribution = self::create([
            'prestataire_id' => $nouveauPrestataire->id_prestataire,
            'lot_id' => $this->lot_id,
            'proforma_id' => $nouvelleProforma->id_proforma,
            'created_by' => $userId,
        ]);

        // Attribuer directement
        $nouvelleAttribution->attribuer($userId);

        return $nouvelleAttribution;
    }

    /**
     * Calculer les retards
     */
    public function calculerRetards()
    {
        if (!$this->date_fin_reelle || !$this->lot->date_fin_prevue) {
            return 0;
        }

        $dateFinPrevue = $this->lot->date_fin_prevue;
        $dateFinReelle = $this->date_fin_reelle;

        if ($dateFinReelle > $dateFinPrevue) {
            $joursRetard = $dateFinPrevue->diffInDays($dateFinReelle);
            $this->jours_retard = $joursRetard;

            // Calculer les pénalités
            if ($this->lot->taux_penalites) {
                $this->penalites_appliquees = $this->lot->calculerPenalites($joursRetard);
            }

            $this->save();

            return $joursRetard;
        }

        return 0;
    }

    /**
     * Mettre à jour l'avancement
     */
    public function mettreAJourAvancement($pourcentage, $observations = null, $userId = null)
    {
        if ($pourcentage < 0 || $pourcentage > 100) {
            throw new \Exception("Le pourcentage d'avancement doit être entre 0 et 100.");
        }

        $this->pourcentage_avancement = $pourcentage;

        if ($observations) {
            $this->observations = $observations;
        }

        $this->updated_by = $userId;
        $this->save();

        // Si 100%, marquer comme terminé
        if ($pourcentage == 100) {
            $this->terminer($userId);
        }

        return $this;
    }

    /**
     * Ajouter des observations
     */
    public function ajouterObservations($observations, $userId = null)
    {
        $anciennes = $this->observations ?? '';
        $nouvelles = "[" . now()->format('d/m/Y H:i') . "] " . $observations;

        $this->observations = $anciennes . "\n" . $nouvelles;
        $this->updated_by = $userId;
        $this->save();

        return $this;
    }

    /**
     * Obtenir le libellé du statut
     */
    public function getLibelleStatut()
    {
        return match($this->statut_attribution) {
            self::STATUT_EN_ATTENTE => 'En attente',
            self::STATUT_ATTRIBUE => 'Attribué',
            self::STATUT_SUSPENDU => 'Suspendu',
            self::STATUT_RETIRE => 'Retiré',
            self::STATUT_TERMINE => 'Terminé',
            default => 'Inconnu'
        };
    }

    /**
     * Vérifier si l'attribution est active
     */
    public function isActive()
    {
        return in_array($this->statut_attribution, [self::STATUT_ATTRIBUE, self::STATUT_SUSPENDU]);
    }

    /**
     * Vérifier si l'attribution est en retard
     */
    public function isEnRetard()
    {
        if (!$this->lot->date_fin_prevue || $this->statut_attribution === self::STATUT_TERMINE) {
            return false;
        }

        return now() > $this->lot->date_fin_prevue;
    }

    /**
     * Calculer la durée d'exécution (en jours)
     */
    public function calculerDureeExecution()
    {
        if (!$this->date_debut_reelle) {
            return null;
        }

        $dateFin = $this->date_fin_reelle ?? now();
        return $this->date_debut_reelle->diffInDays($dateFin);
    }

    /**
     * Obtenir les statistiques d'exécution
     */
    public function getStatistiquesExecution()
    {
        return [
            'prestataire' => $this->prestataire->raison_sociale_prestataire,
            'lot' => $this->lot->numero,
            'statut' => $this->getLibelleStatut(),
            'date_debut' => $this->date_debut_reelle ? $this->date_debut_reelle->format('d/m/Y') : null,
            'date_fin_prevue' => $this->lot->date_fin_prevue ? $this->lot->date_fin_prevue->format('d/m/Y') : null,
            'date_fin_reelle' => $this->date_fin_reelle ? $this->date_fin_reelle->format('d/m/Y') : null,
            'duree_execution_jours' => $this->calculerDureeExecution(),
            'jours_retard' => $this->jours_retard,
            'penalites' => number_format($this->penalites_appliquees, 2, ',', ' '),
            'avancement' => $this->pourcentage_avancement . '%',
            'en_retard' => $this->isEnRetard(),
        ];
    }

    /**
     * Vérifier si un prestataire est éligible pour un lot
     */
    public static function prestataireEligible(Prestataire $prestataire, Lot $lot)
    {
        // Le prestataire doit être actif
        if (!$prestataire->isActif()) {
            return [
                'eligible' => false,
                'raison' => 'Le prestataire est inactif.'
            ];
        }

        // Le lot ne doit pas déjà être attribué
        if ($lot->isAttribue()) {
            return [
                'eligible' => false,
                'raison' => 'Le lot est déjà attribué.'
            ];
        }

        // Le lot ne doit pas être retiré
        if ($lot->isRetire()) {
            return [
                'eligible' => false,
                'raison' => 'Le lot a été retiré.'
            ];
        }

        // Vérifier qu'il n'y a pas déjà une attribution active
        $attributionExistante = self::where('lot_id', $lot->id_lot)
            ->whereIn('statut_attribution', [self::STATUT_ATTRIBUE, self::STATUT_SUSPENDU])
            ->exists();

        if ($attributionExistante) {
            return [
                'eligible' => false,
                'raison' => 'Le lot a déjà une attribution active.'
            ];
        }

        return [
            'eligible' => true,
            'raison' => null
        ];
    }

    /**
     * ================================================================
     * ACCESSEURS (ATTRIBUTES)
     * ================================================================
     */

    /**
     * Obtenir le libellé du statut (accesseur)
     */
    public function getStatutLibelleAttribute()
    {
        return $this->getLibelleStatut();
    }

    /**
     * ================================================================
     * EVENTS (BOOT)
     * ================================================================
     */

    protected static function boot()
    {
        parent::boot();

        // Valider avant création
        static::creating(function ($attribution) {

            $eligibilite = self::prestataireEligible($attribution->prestataire, $attribution->lot);

            if (!$eligibilite['eligible']) {
                throw new \Exception($eligibilite['raison']);
            }
        });
    }
}
