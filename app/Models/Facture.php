<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class Facture extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * La table associée au modèle.
     *
     * @var string
     */
    protected $table = 'factures';

    /**
     * La clé primaire de la table.
     *
     * @var string
     */
    protected $primaryKey = 'id_facture';

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
        'proforma_id',
        'numero_facture',
        'montant_facture',
        'date_facture',
        'date_reception_facture',
        'statut_facture',
        'comment_facture',
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
        'montant_facture' => 'decimal:2',
        'date_facture' => 'date',
        'date_reception_facture' => 'date',
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
        'montant_formate',
        'montant_paye',
        'montant_restant',
        'est_soldee',
    ];

    /**
     * Constantes pour les statuts de facture.
     */
    const STATUT_EN_ATTENTE = 'en_attente';
    const STATUT_VALIDEE = 'validee';
    const STATUT_REJETEE = 'rejetee';
    const STATUT_PAYEE = 'payee';
    const STATUT_PARTIELLEMENT_PAYEE = 'partiellement_payee';
    const STATUT_ANNULEE = 'annulee';

    /**
     * Liste des statuts disponibles.
     *
     * @return array
     */
    public static function getStatuts(): array
    {
        return [
            self::STATUT_EN_ATTENTE => 'En attente',
            self::STATUT_VALIDEE => 'Validée',
            self::STATUT_REJETEE => 'Rejetée',
            self::STATUT_PAYEE => 'Payée',
            self::STATUT_PARTIELLEMENT_PAYEE => 'Partiellement payée',
            self::STATUT_ANNULEE => 'Annulée',
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
            self::STATUT_VALIDEE => 'blue',
            self::STATUT_REJETEE => 'red',
            self::STATUT_PAYEE => 'green',
            self::STATUT_PARTIELLEMENT_PAYEE => 'orange',
            self::STATUT_ANNULEE => 'gray',
        ];
    }

    /**
     * Boot du modèle.
     */
    protected static function boot()
    {
        parent::boot();

        // Génération automatique du numéro de facture
        static::creating(function ($facture) {
            if (empty($facture->numero_facture)) {
                $facture->numero_facture = self::generateNumeroFacture();
            }
        });
    }

    /**
     * Génère un numéro de facture unique.
     *
     * @return string
     */
    public static function generateNumeroFacture(): string
    {
        $prefix = 'FAC';
        $year = date('Y');
        $month = date('m');

        // Récupérer le dernier numéro de facture de l'année en cours
        $lastFacture = self::withTrashed()
            ->whereYear('created_at', $year)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($lastFacture && preg_match('/FAC-\d{4}-\d{2}-(\d{5})/', $lastFacture->numero_facture, $matches)) {
            $sequence = intval($matches[1]) + 1;
        } else {
            $sequence = 1;
        }

        return sprintf('%s-%s-%s-%05d', $prefix, $year, $month, $sequence);
    }

    // =========================================================================
    // RELATIONS
    // =========================================================================

    /**
     * Relation avec la proforma.
     *
     * @return BelongsTo
     */
    public function proforma(): BelongsTo 
    {
        return $this->belongsTo(Proforma::class, 'proforma_id', 'id_proforma');
    }

    /**
     * Relation avec les paiements.
     *
     * @return HasMany
     */
    public function paiements(): HasMany
    {
        return $this->hasMany(Paiement::class, 'facture_id', 'id_facture');
    }

    /**
     * Relation avec les paiements validés/payés uniquement.
     *
     * @return HasMany
     */
    public function paiementsValides(): HasMany
    {
        return $this->hasMany(Paiement::class, 'facture_id', 'id_facture')
            ->whereIn('statut_paiement', [Paiement::STATUT_VALIDE, Paiement::STATUT_PAYE]);
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
        return self::getStatuts()[$this->statut_facture] ?? 'Inconnu';
    }

    /**
     * Obtenir la couleur du statut.
     *
     * @return string
     */
    public function getStatutCouleurAttribute(): string
    {
        return self::getStatutCouleurs()[$this->statut_facture] ?? 'gray';
    }

    /**
     * Obtenir le montant formaté.
     *
     * @return string
     */
    public function getMontantFormateAttribute(): string
    {
        return number_format($this->montant_facture, 0, ',', ' ') . ' FCFA';
    }

    /**
     * Calculer le montant total payé.
     *
     * @return float
     */
    public function getMontantPayeAttribute(): float
    {
        return (float) $this->paiementsValides()->sum('montant_net_paye_paiement');
    }

    /**
     * Calculer le montant restant à payer.
     *
     * @return float
     */
    public function getMontantRestantAttribute(): float
    {
        return max(0, $this->montant_facture - $this->montant_paye);
    }

    /**
     * Vérifier si la facture est soldée.
     *
     * @return bool
     */
    public function getEstSoldeeAttribute(): bool
    {
        return $this->montant_restant <= 0;
    }

    /**
     * Obtenir le pourcentage payé.
     *
     * @return float
     */
    public function getPourcentagePayeAttribute(): float
    {
        if ($this->montant_facture <= 0) {
            return 0;
        }
        return round(($this->montant_paye / $this->montant_facture) * 100, 2);
    }

    // =========================================================================
    // SCOPES
    // =========================================================================

    /**
     * Scope pour filtrer par statut.
     *
     * @param Builder $query
     * @param string $statut
     * @return Builder
     */
    public function scopeParStatut(Builder $query, string $statut): Builder
    {
        return $query->where('statut_facture', $statut);
    }

    /**
     * Scope pour les factures en attente.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeEnAttente(Builder $query): Builder
    {
        return $query->where('statut_facture', self::STATUT_EN_ATTENTE);
    }

    /**
     * Scope pour les factures validées.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeValidees(Builder $query): Builder
    {
        return $query->where('statut_facture', self::STATUT_VALIDEE);
    }

    /**
     * Scope pour les factures payées.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopePayees(Builder $query): Builder
    {
        return $query->where('statut_facture', self::STATUT_PAYEE);
    }

    /**
     * Scope pour les factures partiellement payées.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopePartiellementPayees(Builder $query): Builder
    {
        return $query->where('statut_facture', self::STATUT_PARTIELLEMENT_PAYEE);
    }

    /**
     * Scope pour les factures rejetées.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeRejetees(Builder $query): Builder
    {
        return $query->where('statut_facture', self::STATUT_REJETEE);
    }

    /**
     * Scope pour les factures annulées.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeAnnulees(Builder $query): Builder
    {
        return $query->where('statut_facture', self::STATUT_ANNULEE);
    }

    /**
     * Scope pour filtrer par proforma.
     *
     * @param Builder $query
     * @param string $proformaId
     * @return Builder
     */
    public function scopeParProforma(Builder $query, string $proformaId): Builder
    {
        return $query->where('proforma_id', $proformaId);
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
        return $query->whereBetween('date_facture', [$dateDebut, $dateFin]);
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
            $q->where('numero_facture', 'LIKE', "%{$search}%")
              ->orWhere('comment_facture', 'LIKE', "%{$search}%")
              ->orWhereHas('proforma', function ($subQuery) use ($search) {
                  $subQuery->where('numero_proforma', 'LIKE', "%{$search}%");
              });
        });
    }

    // =========================================================================
    // MÉTHODES MÉTIER
    // =========================================================================

    /**
     * Valider la facture.
     *
     * @param string|null $userId
     * @return bool
     */
    public function valider(?string $userId = null): bool
    {
        if (!$this->peutEtreValidee()) {
            return false;
        }

        $this->statut_facture = self::STATUT_VALIDEE;
        $this->updated_by = $userId;
        return $this->save();
    }

    /**
     * Rejeter la facture.
     *
     * @param string $motif
     * @param string|null $userId
     * @return bool
     */
    public function rejeter(string $motif, ?string $userId = null): bool
    {
        if (!$this->peutEtreRejetee()) {
            return false;
        }

        $this->statut_facture = self::STATUT_REJETEE;
        $this->comment_facture = $motif;
        $this->updated_by = $userId;
        return $this->save();
    }

    /**
     * Annuler la facture.
     *
     * @param string $motif
     * @param string|null $userId
     * @return bool
     */
    public function annuler(string $motif, ?string $userId = null): bool
    {
        if (!$this->peutEtreAnnulee()) {
            return false;
        }

        $this->statut_facture = self::STATUT_ANNULEE;
        $this->comment_facture = $motif;
        $this->updated_by = $userId;
        return $this->save();
    }

    /**
     * Mettre à jour le statut de paiement.
     *
     * @return bool
     */
    public function mettreAJourStatutPaiement(): bool
    {
        if ($this->statut_facture === self::STATUT_ANNULEE ||
            $this->statut_facture === self::STATUT_REJETEE) {
            return false;
        }

        $montantPaye = $this->getMontantPayeAttribute();

        if ($montantPaye <= 0) {
            // Pas de paiement, retour à validée si elle était partiellement payée
            if ($this->statut_facture === self::STATUT_PARTIELLEMENT_PAYEE) {
                $this->statut_facture = self::STATUT_VALIDEE;
            }
        } elseif ($montantPaye >= $this->montant_facture) {
            $this->statut_facture = self::STATUT_PAYEE;
        } else {
            $this->statut_facture = self::STATUT_PARTIELLEMENT_PAYEE;
        }

        return $this->save();
    }

    /**
     * Vérifier si la facture peut être validée.
     *
     * @return bool
     */
    public function peutEtreValidee(): bool
    {
        return $this->statut_facture === self::STATUT_EN_ATTENTE;
    }

    /**
     * Vérifier si la facture peut être rejetée.
     *
     * @return bool
     */
    public function peutEtreRejetee(): bool
    {
        return in_array($this->statut_facture, [
            self::STATUT_EN_ATTENTE,
            self::STATUT_VALIDEE,
        ]);
    }

    /**
     * Vérifier si la facture peut être annulée.
     *
     * @return bool
     */
    public function peutEtreAnnulee(): bool
    {
        // Ne peut pas annuler si des paiements ont été effectués
        if ($this->montant_paye > 0) {
            return false;
        }

        return in_array($this->statut_facture, [
            self::STATUT_EN_ATTENTE,
            self::STATUT_VALIDEE,
            self::STATUT_REJETEE,
        ]);
    }

    /**
     * Vérifier si la facture peut être modifiée.
     *
     * @return bool
     */
    public function peutEtreModifiee(): bool
    {
        return in_array($this->statut_facture, [
            self::STATUT_EN_ATTENTE,
            self::STATUT_REJETEE,
        ]);
    }

    /**
     * Vérifier si la facture peut recevoir un paiement.
     *
     * @return bool
     */
    public function peutRecevoirPaiement(): bool
    {
        return in_array($this->statut_facture, [
            self::STATUT_VALIDEE,
            self::STATUT_PARTIELLEMENT_PAYEE,
        ]);
    }

    /**
     * Dupliquer la facture.
     *
     * @param string|null $userId
     * @return Facture
     */
    public function dupliquer(?string $userId = null): Facture
    {
        $nouvellefacture = $this->replicate([
            'id_facture',
            'numero_facture',
            'statut_facture',
            'created_at',
            'updated_at',
            'deleted_at',
        ]);

        $nouvellefacture->numero_facture = self::generateNumeroFacture();
        $nouvellefacture->statut_facture = self::STATUT_EN_ATTENTE;
        $nouvellefacture->date_facture = now();
        $nouvellefacture->date_reception_facture = now();
        $nouvellefacture->comment_facture = null;
        $nouvellefacture->created_by = $userId;
        $nouvellefacture->updated_by = null;
        $nouvellefacture->save();

        return $nouvellefacture;
    }

    // =========================================================================
    // STATISTIQUES
    // =========================================================================

    /**
     * Obtenir les statistiques globales des factures.
     *
     * @return array
     */
    public static function getStatistiques(): array
    {
        $total = self::count();
        $enAttente = self::enAttente()->count();
        $validees = self::validees()->count();
        $payees = self::payees()->count();
        $partiellementPayees = self::partiellementPayees()->count();
        $rejetees = self::rejetees()->count();
        $annulees = self::annulees()->count();

        $montantTotal = self::sum('montant_facture');
        $montantPaye = self::payees()->sum('montant_facture') +
                       self::partiellementPayees()
                           ->with('paiementsValides')
                           ->get()
                           ->sum(fn($f) => $f->montant_paye);

        return [
            'total' => $total,
            'en_attente' => $enAttente,
            'validees' => $validees,
            'payees' => $payees,
            'partiellement_payees' => $partiellementPayees,
            'rejetees' => $rejetees,
            'annulees' => $annulees,
            'montant_total' => $montantTotal,
            'montant_paye' => $montantPaye,
            'montant_restant' => $montantTotal - $montantPaye,
            'taux_paiement' => $montantTotal > 0 ? round(($montantPaye / $montantTotal) * 100, 2) : 0,
        ];
    }
}
