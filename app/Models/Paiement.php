<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class Paiement extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $dispatchesEvents = [
        'validated' => \App\Events\PaiementValidated::class,
    ];

    /**
     * La table associée au modèle.
     *
     * @var string
     */
    protected $table = 'paiements';

    /**
     * La clé primaire de la table.
     *
     * @var string
     */
    protected $primaryKey = 'id_paiement';

    /**
     * Indique si la clé primaire est auto-incrémentée.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Le type de la clé primaire.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Les attributs qui sont mass assignables.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'facture_id',
        'banque_id',
        'montant_net_paye_paiement',
        'statut_paiement',
        'date_validation_paiement',
        'date_effectif_paiement',
        'motif_rejet_paiement',
        'observations_paiement',
        'valide_par',
        'paye_par',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * Les attributs qui doivent être castés.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'montant_net_paye_paiement' => 'decimal:2',
        'statut_paiement' => 'integer',
        'date_validation_paiement' => 'datetime',
        'date_effectif_paiement' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Les attributs qui doivent être cachés pour la sérialisation.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'deleted_at',
        'deleted_by',
    ];

    /**
     * Les accesseurs à ajouter au tableau du modèle.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'statut_libelle',
        'statut_couleur',
        'montant_formate',
        'reference_paiement',
    ];

    /**
     * Constantes pour les statuts de paiement.
     */
    const STATUT_EN_ATTENTE = 0;
    const STATUT_VALIDE = 1;
    const STATUT_EN_TRAITEMENT = 2;
    const STATUT_PAYE = 3;
    const STATUT_REJETE = 4;
    const STATUT_ANNULE = 5;

    /**
     * Liste des statuts disponibles.
     *
     * @return array
     */
    public static function getStatuts(): array
    {
        return [
            self::STATUT_EN_ATTENTE => 'En attente de validation',
            self::STATUT_VALIDE => 'Validé / Approuvé',
            self::STATUT_EN_TRAITEMENT => 'En cours de traitement bancaire',
            self::STATUT_PAYE => 'Payé / Exécuté',
            self::STATUT_REJETE => 'Rejeté',
            self::STATUT_ANNULE => 'Annulé',
        ];
    }

    /**
     * Couleurs associées aux statuts pour l'affichage.
     *
     * @return array
     */
    public static function getStatutCouleurs(): array
    {
        return [
            self::STATUT_EN_ATTENTE => 'yellow',
            self::STATUT_VALIDE => 'blue',
            self::STATUT_EN_TRAITEMENT => 'indigo',
            self::STATUT_PAYE => 'green',
            self::STATUT_REJETE => 'red',
            self::STATUT_ANNULE => 'gray',
        ];
    }

    /**
     * Icônes associées aux statuts.
     *
     * @return array
     */
    public static function getStatutIcones(): array
    {
        return [
            self::STATUT_EN_ATTENTE => 'fa-clock',
            self::STATUT_VALIDE => 'fa-check-circle',
            self::STATUT_EN_TRAITEMENT => 'fa-spinner',
            self::STATUT_PAYE => 'fa-check-double',
            self::STATUT_REJETE => 'fa-times-circle',
            self::STATUT_ANNULE => 'fa-ban',
        ];
    }

    /**
     * Boot du modèle.
     */
    protected static function boot()
    {
        parent::boot();

        // Après sauvegarde, mettre à jour le statut de la facture
        static::saved(function ($paiement) {
            if ($paiement->facture) {
                $paiement->facture->mettreAJourStatutPaiement();
            }
        });

        // Après suppression, mettre à jour le statut de la facture
        static::deleted(function ($paiement) {
            if ($paiement->facture) {
                $paiement->facture->mettreAJourStatutPaiement();
            }
        });
    }

    // =========================================================================
    // RELATIONS
    // =========================================================================

    /**
     * Relation avec la facture.
     *
     * @return BelongsTo
     */
    public function facture(): BelongsTo
    {
        return $this->belongsTo(Facture::class, 'facture_id', 'id_facture');
    }

    /**
     * Relation avec la banque.
     *
     * @return BelongsTo
     */
    public function banque(): BelongsTo
    {
        return $this->belongsTo(Banque::class, 'banque_id', 'id_banque');
    }

    /**
     * Relation avec l'utilisateur validateur.
     *
     * @return BelongsTo
     */
    public function validateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'valide_par', 'id');
    }

    /**
     * Relation avec l'utilisateur payeur.
     *
     * @return BelongsTo
     */
    public function payeur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paye_par', 'id');
    }

    /**
     * Relation avec l'utilisateur créateur.
     *
     * @return BelongsTo
     */
    public function createur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    /**
     * Relation avec l'utilisateur modificateur.
     *
     * @return BelongsTo
     */
    public function modificateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    /**
     * Relation avec l'utilisateur suppresseur.
     *
     * @return BelongsTo
     */
    public function suppresseur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by', 'id');
    }

    // =========================================================================
    // ACCESSEURS
    // =========================================================================

    /**
     * Obtenir le libellé du statut.
     *
     * @return string
     */
    public function getStatutLibelleAttribute(): string
    {
        return self::getStatuts()[$this->statut_paiement] ?? 'Inconnu';
    }

    /**
     * Obtenir la couleur du statut.
     *
     * @return string
     */
    public function getStatutCouleurAttribute(): string
    {
        return self::getStatutCouleurs()[$this->statut_paiement] ?? 'gray';
    }

    /**
     * Obtenir l'icône du statut.
     *
     * @return string
     */
    public function getStatutIconeAttribute(): string
    {
        return self::getStatutIcones()[$this->statut_paiement] ?? 'fa-question-circle';
    }

    /**
     * Obtenir le montant formaté.
     *
     * @return string
     */
    public function getMontantFormateAttribute(): string
    {
        return number_format(floor($this->montant_net_paye_paiement ?? 0), 0, ',', ' ') . ' FCFA';
    }

    /**
     * Générer une référence de paiement lisible.
     *
     * @return string
     */
    public function getReferencePaiementAttribute(): string
    {
        $date = $this->created_at ? $this->created_at->format('Ymd') : date('Ymd');
        $shortId = strtoupper(substr($this->id_paiement, 0, 8));
        return "PAY-{$date}-{$shortId}";
    }

    // =========================================================================
    // SCOPES
    // =========================================================================

    /**
     * Scope pour filtrer par statut.
     *
     * @param Builder $query
     * @param int $statut
     * @return Builder
     */
    public function scopeParStatut(Builder $query, int $statut): Builder
    {
        return $query->where('statut_paiement', $statut);
    }

    /**
     * Scope pour les paiements en attente.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeEnAttente(Builder $query): Builder
    {
        return $query->where('statut_paiement', self::STATUT_EN_ATTENTE);
    }

    /**
     * Scope pour les paiements validés.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeValides(Builder $query): Builder
    {
        return $query->where('statut_paiement', self::STATUT_VALIDE);
    }

    /**
     * Scope pour les paiements en traitement.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeEnTraitement(Builder $query): Builder
    {
        return $query->where('statut_paiement', self::STATUT_EN_TRAITEMENT);
    }

    /**
     * Scope pour les paiements payés.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopePayes(Builder $query): Builder
    {
        return $query->where('statut_paiement', self::STATUT_PAYE);
    }

    /**
     * Scope pour les paiements rejetés.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeRejetes(Builder $query): Builder
    {
        return $query->where('statut_paiement', self::STATUT_REJETE);
    }

    /**
     * Scope pour les paiements annulés.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeAnnules(Builder $query): Builder
    {
        return $query->where('statut_paiement', self::STATUT_ANNULE);
    }

    /**
     * Scope pour les paiements effectifs (validés ou payés).
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeEffectifs(Builder $query): Builder
    {
        return $query->whereIn('statut_paiement', [
            self::STATUT_VALIDE,
            self::STATUT_EN_TRAITEMENT,
            self::STATUT_PAYE,
        ]);
    }

    /**
     * Scope pour filtrer par facture.
     *
     * @param Builder $query
     * @param string $factureId
     * @return Builder
     */
    public function scopeParFacture(Builder $query, string $factureId): Builder
    {
        return $query->where('facture_id', $factureId);
    }

    /**
     * Scope pour filtrer par banque.
     *
     * @param Builder $query
     * @param string $banqueId
     * @return Builder
     */
    public function scopeParBanque(Builder $query, string $banqueId): Builder
    {
        return $query->where('banque_id', $banqueId);
    }

    /**
     * Scope pour filtrer par période.
     *
     * @param Builder $query
     * @param string $dateDebut
     * @param string $dateFin
     * @return Builder
     */
    public function scopeParPeriode(Builder $query, string $dateDebut, string $dateFin): Builder
    {
        return $query->whereBetween('created_at', [$dateDebut, $dateFin]);
    }

    /**
     * Scope pour filtrer par période de validation.
     *
     * @param Builder $query
     * @param string $dateDebut
     * @param string $dateFin
     * @return Builder
     */
    public function scopeParPeriodeValidation(Builder $query, string $dateDebut, string $dateFin): Builder
    {
        return $query->whereBetween('date_validation_paiement', [$dateDebut, $dateFin]);
    }

    /**
     * Scope pour la recherche.
     *
     * @param Builder $query
     * @param string|null $search
     * @return Builder
     */
    public function scopeRecherche(Builder $query, ?string $search): Builder
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('observations_paiement', 'LIKE', "%{$search}%")
              ->orWhere('motif_rejet_paiement', 'LIKE', "%{$search}%")
              ->orWhereHas('facture', function ($subQuery) use ($search) {
                  $subQuery->where('numero_facture', 'LIKE', "%{$search}%");
              })
              ->orWhereHas('banque', function ($subQuery) use ($search) {
                  $subQuery->where('nom_banque', 'LIKE', "%{$search}%")
                           ->orWhere('numero_compte_banque', 'LIKE', "%{$search}%");
              });
        });
    }

    // =========================================================================
    // MÉTHODES MÉTIER
    // =========================================================================

    /**
     * Valider le paiement.
     *
     * @param string $userId
     * @return bool
     */
    public function valider(string $userId): bool
    {
        if (!$this->peutEtreValide()) {
            return false;
        }

        $this->statut_paiement = self::STATUT_VALIDE;
        $this->valide_par = $userId;
        $this->date_validation_paiement = now();
        $this->updated_by = $userId;
        return $this->save();
    }

    /**
     * Mettre en traitement bancaire.
     *
     * @param string|null $userId
     * @return bool
     */
    public function mettreEnTraitement(?string $userId = null): bool
    {
        if ($this->statut_paiement !== self::STATUT_VALIDE) {
            return false;
        }

        $this->statut_paiement = self::STATUT_EN_TRAITEMENT;
        $this->updated_by = $userId;
        return $this->save();
    }

    /**
     * Confirmer le paiement comme effectué.
     *
     * @param string $userId
     * @return bool
     */
    public function confirmerPaiement(string $userId): bool
    {
        if (!in_array($this->statut_paiement, [self::STATUT_VALIDE, self::STATUT_EN_TRAITEMENT])) {
            return false;
        }

        $this->statut_paiement = self::STATUT_PAYE;
        $this->paye_par = $userId;
        $this->updated_by = $userId;
        return $this->save();
    }

    /**
     * Rejeter le paiement.
     *
     * @param string $motif
     * @param string $userId
     * @return bool
     */
    public function rejeter(string $motif, string $userId): bool
    {
        if (!$this->peutEtreRejete()) {
            return false;
        }

        $this->statut_paiement = self::STATUT_REJETE;
        $this->motif_rejet_paiement = $motif;
        $this->updated_by = $userId;
        return $this->save();
    }

    /**
     * Annuler le paiement.
     *
     * @param string $motif
     * @param string $userId
     * @return bool
     */
    public function annuler(string $motif, string $userId): bool
    {
        if (!$this->peutEtreAnnule()) {
            return false;
        }

        $this->statut_paiement = self::STATUT_ANNULE;
        $this->motif_rejet_paiement = $motif;
        $this->updated_by = $userId;
        return $this->save();
    }

    /**
     * Remettre en attente (après rejet par exemple).
     *
     * @param string|null $userId
     * @return bool
     */
    public function remettreEnAttente(?string $userId = null): bool
    {
        if ($this->statut_paiement !== self::STATUT_REJETE) {
            return false;
        }

        $this->statut_paiement = self::STATUT_EN_ATTENTE;
        $this->motif_rejet_paiement = null;
        $this->valide_par = null;
        $this->date_validation_paiement = null;
        $this->updated_by = $userId;
        return $this->save();
    }

    /**
     * Vérifier si le paiement peut être validé.
     *
     * @return bool
     */
    public function peutEtreValide(): bool
    {
        return $this->statut_paiement === self::STATUT_EN_ATTENTE;
    }

    /**
     * Vérifier si le paiement peut être rejeté.
     *
     * @return bool
     */
    public function peutEtreRejete(): bool
    {
        return in_array($this->statut_paiement, [
            self::STATUT_EN_ATTENTE,
            self::STATUT_VALIDE,
            self::STATUT_EN_TRAITEMENT,
        ]);
    }

    /**
     * Vérifier si le paiement peut être annulé.
     *
     * @return bool
     */
    public function peutEtreAnnule(): bool
    {
        return in_array($this->statut_paiement, [
            self::STATUT_EN_ATTENTE,
            self::STATUT_VALIDE,
            self::STATUT_REJETE,
        ]);
    }

    /**
     * Vérifier si le paiement peut être modifié.
     *
     * @return bool
     */
    public function peutEtreModifie(): bool
    {
        return in_array($this->statut_paiement, [
            self::STATUT_EN_ATTENTE,
            self::STATUT_REJETE,
        ]);
    }

    /**
     * Vérifier si le paiement est effectif (compte dans le solde).
     *
     * @return bool
     */
    public function estEffectif(): bool
    {
        return in_array($this->statut_paiement, [
            self::STATUT_VALIDE,
            self::STATUT_EN_TRAITEMENT,
            self::STATUT_PAYE,
        ]);
    }

    // =========================================================================
    // STATISTIQUES
    // =========================================================================

    /**
     * Obtenir les statistiques globales des paiements.
     *
     * @return array
     */
    public static function getStatistiques(): array
    {
        $total = self::count();
        $enAttente = self::enAttente()->count();
        $valides = self::valides()->count();
        $enTraitement = self::enTraitement()->count();
        $payes = self::payes()->count();
        $rejetes = self::rejetes()->count();
        $annules = self::annules()->count();

        $montantTotal = self::sum('montant_net_paye_paiement');
        $montantEffectif = self::effectifs()->sum('montant_net_paye_paiement');
        $montantPaye = self::payes()->sum('montant_net_paye_paiement');

        return [
            'total' => $total,
            'en_attente' => $enAttente,
            'valides' => $valides,
            'en_traitement' => $enTraitement,
            'payes' => $payes,
            'rejetes' => $rejetes,
            'annules' => $annules,
            'montant_total' => $montantTotal,
            'montant_effectif' => $montantEffectif,
            'montant_paye' => $montantPaye,
            'taux_validation' => $total > 0 ? round((($valides + $enTraitement + $payes) / $total) * 100, 2) : 0,
            'taux_paiement' => $total > 0 ? round(($payes / $total) * 100, 2) : 0,
        ];
    }

    /**
     * Obtenir les statistiques par banque.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getStatistiquesParBanque()
    {
        return self::selectRaw('banque_id, COUNT(*) as nombre, SUM(montant_net_paye_paiement) as montant_total')
            ->with('banque:id_banque,nom_banque')
            ->groupBy('banque_id')
            ->get();
    }

    /**
     * Obtenir les statistiques par mois.
     *
     * @param int $annee
     * @return \Illuminate\Support\Collection
     */
    public static function getStatistiquesParMois(int $annee)
    {
        return self::selectRaw('MONTH(created_at) as mois, COUNT(*) as nombre, SUM(montant_net_paye_paiement) as montant_total')
            ->whereYear('created_at', $annee)
            ->groupBy('mois')
            ->orderBy('mois')
            ->get();
    }
}
