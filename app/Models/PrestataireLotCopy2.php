<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class PrestataireLotCopy2 extends Model
{
    use SoftDeletes;

    protected $table = 'prestataires_lots';

    // Pas de clé primaire auto-incrémentée
    public $incrementing = false;
    protected $keyType = 'string';

    // Désactiver la clé primaire pour utiliser une clé composite
    protected $primaryKey = null;

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
     * Override de la méthode getKey pour gérer la clé composite
     */
    public function getKey()
    {
        return [
            'prestataire_id' => $this->prestataire_id,
            'lot_id' => $this->lot_id,
            'proforma_id' => $this->proforma_id
        ];
    }

    /**
     * Override de la méthode getKeyName pour retourner un tableau
     */
    public function getKeyName()
    {
        return ['prestataire_id', 'lot_id', 'proforma_id'];
    }

    /**
     * Set la clé composite sur le modèle
     */
    protected function setKeysForSaveQuery($query)
    {
        $keys = $this->getKeyName();
        if (!is_array($keys)) {
            return parent::setKeysForSaveQuery($query);
        }

        foreach ($keys as $keyName) {
            $query->where($keyName, '=', $this->getKeyForSaveQuery($keyName));
        }

        return $query;
    }

    /**
     * Obtenir la valeur de la clé pour la requête de sauvegarde
     */
    protected function getKeyForSaveQuery($keyName = null)
    {
        if (is_null($keyName)) {
            $keyName = $this->getKeyName();
        }

        if (isset($this->original[$keyName])) {
            return $this->original[$keyName];
        }

        return $this->getAttribute($keyName);
    }

    /**
     * ================================================================
     * RELATIONS
     * ================================================================
     */

    public function prestataire()
    {
        return $this->belongsTo(Prestataire::class, 'prestataire_id', 'id_prestataire');
    }

    public function lot()
    {
        return $this->belongsTo(Lot::class, 'lot_id', 'id_lot');
    }

    public function proforma()
    {
        return $this->belongsTo(Proforma::class, 'proforma_id', 'id_proforma');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * ================================================================
     * SCOPES
     * ================================================================
     */

    public function scopeEnAttente($query)
    {
        return $query->where('statut_attribution', self::STATUT_EN_ATTENTE);
    }

    public function scopeAttribue($query)
    {
        return $query->where('statut_attribution', self::STATUT_ATTRIBUE);
    }

    public function scopeSuspendu($query)
    {
        return $query->where('statut_attribution', self::STATUT_SUSPENDU);
    }

    public function scopeRetire($query)
    {
        return $query->where('statut_attribution', self::STATUT_RETIRE);
    }

    public function scopeTermine($query)
    {
        return $query->where('statut_attribution', self::STATUT_TERMINE);
    }

    public function scopeEnCours($query)
    {
        return $query->whereIn('statut_attribution', [self::STATUT_ATTRIBUE, self::STATUT_SUSPENDU]);
    }

    public function scopeEnRetard($query)
    {
        return $query->where('jours_retard', '>', 0);
    }

    /**
     * ================================================================
     * MÉTHODES UTILITAIRES
     * ================================================================
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

    public function retirer($motif, $userId = null)
    {
        $this->statut_attribution = self::STATUT_RETIRE;
        $this->motif_retrait = $motif;
        $this->date_retrait = now();
        // $this->deleted_by = $userId;
        $this->save();

        // Soft delete
        // $this->delete();

        return $this;
    }

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

    public function reattribuerA(Prestataire $nouveauPrestataire, Proforma $nouvelleProforma, $motif, $userId = null)
    {
        // Retirer l'attribution actuelle
        $this->retirer($motif, $userId);

        // Créer une nouvelle attribution
        $nouvelleAttribution = self::create([
            'prestataire_id' => $nouveauPrestataire->id_prestataire,
            'lot_id' => $this->lot_id,
            'proforma_id' => $nouvelleProforma->id_proforma,
            'statut_attribution' => self::STATUT_EN_ATTENTE,
            'created_by' => $userId,
        ]);

        // Attribuer directement
        $nouvelleAttribution->attribuer($userId);

        return $nouvelleAttribution;
    }

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

            if ($this->lot->taux_penalites) {
                $this->penalites_appliquees = $this->lot->calculerPenalites($joursRetard);
            }

            $this->save();

            return $joursRetard;
        }

        return 0;
    }

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

        if ($pourcentage == 100) {
            $this->terminer($userId);
        }

        return $this;
    }

    public function ajouterObservations($observations, $userId = null)
    {
        $anciennes = $this->observations ?? '';
        $nouvelles = "[" . now()->format('d/m/Y H:i') . "] " . $observations;

        $this->observations = $anciennes . "\n" . $nouvelles;
        $this->updated_by = $userId;
        $this->save();

        return $this;
    }

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

    public function isActive()
    {
        return in_array($this->statut_attribution, [self::STATUT_ATTRIBUE, self::STATUT_SUSPENDU]);
    }

    public function isEnRetard()
    {
        if (!$this->lot->date_fin_prevue || $this->statut_attribution === self::STATUT_TERMINE) {
            return false;
        }

        return now() > $this->lot->date_fin_prevue;
    }

    public function calculerDureeExecution()
    {
        if (!$this->date_debut_reelle) {
            return null;
        }

        $dateFin = $this->date_fin_reelle ?? now();
        return $this->date_debut_reelle->diffInDays($dateFin);
    }

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

    public static function prestataireEligible(Prestataire $prestataire, Lot $lot)
    {
        if (!$prestataire->isActif()) {
            return [
                'eligible' => false,
                'raison' => 'Le prestataire est inactif.'
            ];
        }

        if ($lot->isAttribue()) {
            return [
                'eligible' => false,
                'raison' => 'Le lot est déjà attribué.'
            ];
        }

        if ($lot->isRetire()) {
            return [
                'eligible' => false,
                'raison' => 'Le lot a été retiré.'
            ];
        }

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

    public function getStatutLibelleAttribute()
    {
        return $this->getLibelleStatut();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($attribution) {
            $eligibilite = self::prestataireEligible($attribution->prestataire, $attribution->lot);

            if (!$eligibilite['eligible']) {
                throw new \Exception($eligibilite['raison']);
            }
        });
    }
}
