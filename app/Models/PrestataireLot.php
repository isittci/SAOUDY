<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PrestataireLot extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'prestataires_lots';
    protected $primaryKey = 'id_attribution';
    public $incrementing = false;
    protected $keyType = 'string';

    // Constantes statuts
    const STATUT_EN_ATTENTE = 0;
    const STATUT_ATTRIBUE = 1;
    const STATUT_SUSPENDU = 2;
    const STATUT_RETIRE = 3;
    const STATUT_TERMINE = 4;
    const STATUT_ANNULE = 5;

    const STATUT_LABELS = [
        self::STATUT_EN_ATTENTE => 'En attente',
        self::STATUT_ATTRIBUE => 'Attribué',
        self::STATUT_SUSPENDU => 'Suspendu',
        self::STATUT_RETIRE => 'Retiré',
        self::STATUT_TERMINE => 'Terminé',
        self::STATUT_ANNULE => 'Annulé',
    ];

    const STATUT_COLORS = [
        self::STATUT_EN_ATTENTE => 'gray',
        self::STATUT_ATTRIBUE => 'green',
        self::STATUT_SUSPENDU => 'yellow',
        self::STATUT_RETIRE => 'red',
        self::STATUT_TERMINE => 'blue',
        self::STATUT_ANNULE => 'gray',
    ];

    // Types de retrait
    const TYPE_RETRAIT_VOLONTAIRE = 'volontaire';
    const TYPE_RETRAIT_FORCE = 'force';
    const TYPE_RETRAIT_RESILIATION = 'resiliation';
    const TYPE_RETRAIT_ABANDON = 'abandon';

    protected $fillable = [
        'prestataire_id',
        'lot_id',
        'proforma_id',
        'parent_attribution_id',
        'version_attribution',
        'is_active',
        'numero_attribution',
        'date_attribution',
        'date_debut_prevue',
        'date_fin_prevue',
        'date_debut_reelle',
        'date_fin_reelle',
        'statut_attribution',
        'motif_suspension',
        'date_suspension',
        'date_reprise_prevue',
        'date_reprise_reelle',
        'motif_retrait',
        'date_retrait',
        'type_retrait',
        'jours_retard',
        'taux_penalites',
        'penalites_appliquees',
        'penalites_payees',
        'pourcentage_avancement',
        'montant_engage',
        'montant_paye',
        'observations',
        'conditions_particulieres',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'date_attribution' => 'date',
        'date_debut_prevue' => 'date',
        'date_fin_prevue' => 'date',
        'date_debut_reelle' => 'date',
        'date_fin_reelle' => 'date',
        'date_suspension' => 'datetime',
        'date_reprise_prevue' => 'date',
        'date_reprise_reelle' => 'date',
        'date_retrait' => 'datetime',
        'is_active' => 'boolean',
        'statut_attribution' => 'integer',
        'version_attribution' => 'integer',
        'jours_retard' => 'integer',
        'taux_penalites' => 'decimal:2',
        'penalites_appliquees' => 'decimal:2',
        'penalites_payees' => 'decimal:2',
        'pourcentage_avancement' => 'decimal:2',
        'montant_engage' => 'decimal:2',
        'montant_paye' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $attributes = [
        'is_active' => true,
        'statut_attribution' => 0,
        'version_attribution' => 1,
        'jours_retard' => 0,
        'taux_penalites' => 0,
        'penalites_appliquees' => 0,
        'penalites_payees' => 0,
        'pourcentage_avancement' => 0,
        'montant_engage' => 0,
        'montant_paye' => 0,
    ];

    // ==================== RELATIONS ====================

    public function prestataire(): BelongsTo
    {
        return $this->belongsTo(Prestataire::class, 'prestataire_id', 'id_prestataire');
    }

    public function lot(): BelongsTo
    {
        return $this->belongsTo(Lot::class, 'lot_id', 'id_lot');
    }

    public function proforma(): BelongsTo
    {
        return $this->belongsTo(Proforma::class, 'proforma_id', 'id_proforma');
    }

    public function parentAttribution(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_attribution_id', 'id_attribution');
    }

    public function childAttributions(): HasMany
    {
        return $this->hasMany(self::class, 'parent_attribution_id', 'id_attribution');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by', 'id');
    }

    // ==================== SCOPES ====================

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeHistorique(Builder $query): Builder
    {
        return $query->where('is_active', false);
    }

    public function scopeStatut(Builder $query, int $statut): Builder
    {
        return $query->where('statut_attribution', $statut);
    }

    public function scopeAttribuees(Builder $query): Builder
    {
        return $query->where('statut_attribution', self::STATUT_ATTRIBUE);
    }

    public function scopeSuspendues(Builder $query): Builder
    {
        return $query->where('statut_attribution', self::STATUT_SUSPENDU);
    }

    public function scopeRetirees(Builder $query): Builder
    {
        return $query->where('statut_attribution', self::STATUT_RETIRE);
    }

    public function scopeTerminees(Builder $query): Builder
    {
        return $query->where('statut_attribution', self::STATUT_TERMINE);
    }

    public function scopePourLot(Builder $query, string $lotId): Builder
    {
        return $query->where('lot_id', $lotId);
    }

    public function scopePourPrestataire(Builder $query, string $prestataireId): Builder
    {
        return $query->where('prestataire_id', $prestataireId);
    }

    public function scopeEnRetard(Builder $query): Builder
    {
        return $query->where('jours_retard', '>', 0)
            ->orWhere(function ($q) {
                $q->whereNotNull('date_fin_prevue')
                    ->whereNull('date_fin_reelle')
                    ->where('date_fin_prevue', '<', now())
                    ->where('statut_attribution', self::STATUT_ATTRIBUE);
            });
    }

    // ==================== ACCESSEURS ==================== 

    public function getStatutLabelAttribute(): string
    {
        return self::STATUT_LABELS[$this->statut_attribution] ?? 'Inconnu';
    }

    public function getStatutColorAttribute(): string
    {
        return self::STATUT_COLORS[$this->statut_attribution] ?? 'gray';
    }

    public function getStatutBadgeClassAttribute(): string
    {
        $colors = [
            self::STATUT_EN_ATTENTE => 'bg-gray-100 text-gray-800',
            self::STATUT_ATTRIBUE => 'bg-green-100 text-green-800',
            self::STATUT_SUSPENDU => 'bg-yellow-100 text-yellow-800',
            self::STATUT_RETIRE => 'bg-red-100 text-red-800',
            self::STATUT_TERMINE => 'bg-blue-100 text-blue-800',
            self::STATUT_ANNULE => 'bg-gray-200 text-gray-600',
        ];
        return $colors[$this->statut_attribution] ?? 'bg-gray-100 text-gray-800';
    }

    public function getDureePrevueAttribute(): ?int
    {
        if ($this->date_debut_prevue && $this->date_fin_prevue) {
            return $this->date_debut_prevue->diffInDays($this->date_fin_prevue);
        }
        return null;
    }

    public function getDureeReelleAttribute(): ?int
    {
        if ($this->date_debut_reelle && $this->date_fin_reelle) {
            return $this->date_debut_reelle->diffInDays($this->date_fin_reelle);
        }
        return null;
    }

    public function getMontantRestantAttribute(): float
    {
        return max(0, $this->montant_engage - $this->montant_paye);
    }

    public function getPenalitesRestantesAttribute(): float
    {
        return max(0, $this->penalites_appliquees - $this->penalites_payees);
    }

    public function getJoursRetardActuelsAttribute(): int
    {
        if ($this->statut_attribution !== self::STATUT_ATTRIBUE) {
            return $this->jours_retard;
        }
        if ($this->date_fin_prevue && !$this->date_fin_reelle) {
            $retard = $this->date_fin_prevue->diffInDays(now(), false);
            return max(0, $retard);
        }
        return $this->jours_retard;
    }

    // ==================== VÉRIFICATIONS ====================

    public function peutEtreSuspendue(): bool
    {
        return $this->is_active && $this->statut_attribution === self::STATUT_ATTRIBUE;
    }

    public function peutEtreReprise(): bool
    {
        return $this->is_active && $this->statut_attribution === self::STATUT_SUSPENDU;
    }

    public function peutEtreRetiree(): bool
    {
        return $this->is_active && in_array($this->statut_attribution, [
            self::STATUT_ATTRIBUE,
            self::STATUT_SUSPENDU,
        ]);
    }

    public function peutEtreTerminee(): bool
    {
        return $this->is_active && $this->statut_attribution === self::STATUT_ATTRIBUE;
    }

    public function estEnRetard(): bool
    {
        return $this->jours_retard_actuels > 0;
    }

    // ==================== MÉTHODES MÉTIER ====================

    /**
     * Attribuer un lot à un prestataire
     */
    public static function attribuer(array $data): self
    {
        return DB::transaction(function () use ($data) {
            // Désactiver toute attribution active existante pour ce lot
            self::where('lot_id', $data['lot_id'])
                ->where('is_active', true)
                ->update(['is_active' => false]);

            // Récupérer la dernière attribution pour ce lot
            $derniereAttribution = self::where('lot_id', $data['lot_id'])
                ->orderBy('version_attribution', 'desc')
                ->first();

            $nouvelleVersion = $derniereAttribution
                ? $derniereAttribution->version_attribution + 1
                : 1;

            $numeroAttribution = self::genererNumeroAttribution();

            $attribution = self::create([
                'prestataire_id' => $data['prestataire_id'],
                'lot_id' => $data['lot_id'],
                'proforma_id' => $data['proforma_id'],
                'parent_attribution_id' => $derniereAttribution?->id_attribution,
                'version_attribution' => $nouvelleVersion,
                'is_active' => true,
                'numero_attribution' => $numeroAttribution,
                'date_attribution' => $data['date_attribution'] ?? now(),
                'date_debut_prevue' => $data['date_debut_prevue'] ?? null,
                'date_fin_prevue' => $data['date_fin_prevue'] ?? null,
                'statut_attribution' => self::STATUT_ATTRIBUE,
                'taux_penalites' => $data['taux_penalites'] ?? 0,
                'montant_engage' => $data['montant_engage'] ?? 0,
                'observations' => $data['observations'] ?? null,
                'conditions_particulieres' => $data['conditions_particulieres'] ?? null,
                'created_by' => Auth::id(),
            ]);

            // Mettre à jour le lot
            $attribution->lot->update([
                'attribution_lot' => 1,
                'date_attribution' => $data['date_attribution'] ?? now(),
            ]);

            return $attribution;
        });
    }

    /**
     * Générer numéro unique
     */
    public static function genererNumeroAttribution(): string
    {
        $annee = date('Y');
        $prefix = 'ATT';

        $dernierNumero = self::whereYear('created_at', $annee)
            ->where('numero_attribution', 'like', "{$prefix}-{$annee}-%")
            ->orderBy('numero_attribution', 'desc')
            ->value('numero_attribution');

        $sequence = 1;
        if ($dernierNumero) {
            $parts = explode('-', $dernierNumero);
            $sequence = (int) end($parts) + 1;
        }

        return sprintf('%s-%s-%04d', $prefix, $annee, $sequence);
    }

    /**
     * Suspendre l'attribution
     */
    public function suspendre(string $motif, ?Carbon $dateReprisePrevue = null): bool
    {
        if (!$this->peutEtreSuspendue()) {
            return false;
        }

        return $this->update([
            'statut_attribution' => self::STATUT_SUSPENDU,
            'motif_suspension' => $motif,
            'date_suspension' => now(),
            'date_reprise_prevue' => $dateReprisePrevue,
            'updated_by' => Auth::id(),
        ]);
    }

    /**
     * Reprendre après suspension
     */
    public function reprendre(?string $observations = null): bool
    {
        if (!$this->peutEtreReprise()) {
            return false;
        }

        $nouvelleDateFin = $this->date_fin_prevue;
        if ($this->date_suspension && $this->date_fin_prevue) {
            $joursSuspension = $this->date_suspension->diffInDays(now());
            $nouvelleDateFin = $this->date_fin_prevue->addDays($joursSuspension);
        }

        return $this->update([
            'statut_attribution' => self::STATUT_ATTRIBUE,
            'date_reprise_reelle' => now(),
            'date_fin_prevue' => $nouvelleDateFin,
            'observations' => $observations
                ? $this->observations . "\n[Reprise] " . $observations
                : $this->observations,
            'updated_by' => Auth::id(),
        ]);
    }

    /**
     * Retirer l'attribution
     */
    public function retirer(string $motif, string $typeRetrait = self::TYPE_RETRAIT_FORCE): bool
    {
        if (!$this->peutEtreRetiree()) {
            return false;
        }

        return DB::transaction(function () use ($motif, $typeRetrait) {
            $penalitesFinales = $this->calculerPenalites();

            $this->update([
                'statut_attribution' => self::STATUT_RETIRE,
                'is_active' => false,
                'motif_retrait' => $motif,
                'date_retrait' => now(),
                'type_retrait' => $typeRetrait,
                'jours_retard' => $this->jours_retard_actuels,
                'penalites_appliquees' => $penalitesFinales,
                'date_fin_reelle' => now(),
                'updated_by' => Auth::id(),
            ]);

            $this->lot->update([
                'attribution_lot' => 0,
                'date_retrait' => now(),
                'motif_retrait' => $motif,
            ]);

            return true;
        });
    }

    /**
     * Réattribuer le lot
     */
    public function reattribuer(array $data): self
    {
        return DB::transaction(function () use ($data) {
            if ($this->is_active) {
                $this->update([
                    'is_active' => false,
                    'updated_by' => Auth::id(),
                ]);
            }

            return self::attribuer(array_merge($data, [
                'lot_id' => $this->lot_id,
            ]));
        });
    }

    /**
     * Terminer l'attribution
     */
    public function terminer(?string $observations = null): bool
    {
        if (!$this->peutEtreTerminee()) {
            return false;
        }

        return DB::transaction(function () use ($observations) {
            $joursRetardFinal = $this->jours_retard_actuels;
            $penalitesFinales = $this->calculerPenalites();

            $this->update([
                'statut_attribution' => self::STATUT_TERMINE,
                'date_fin_reelle' => now(),
                'pourcentage_avancement' => 100,
                'jours_retard' => $joursRetardFinal,
                'penalites_appliquees' => $penalitesFinales,
                'observations' => $observations
                    ? $this->observations . "\n[Terminé] " . $observations
                    : $this->observations,
                'updated_by' => Auth::id(),
            ]);

            $this->lot->update(['statut_lot' => 1]);

            return true;
        });
    }

    /**
     * Mettre à jour l'avancement
     */
    public function mettreAJourAvancement(float $pourcentage, ?string $observations = null): bool
    {
        $pourcentage = max(0, min(100, $pourcentage));

        return $this->update([
            'pourcentage_avancement' => $pourcentage,
            'observations' => $observations
                ? $this->observations . "\n[Avancement " . $pourcentage . "%] " . $observations
                : $this->observations,
            'updated_by' => Auth::id(),
        ]);
    }

    /**
     * Calculer les pénalités
     */
    public function calculerPenalites(): float
    {
        if ($this->taux_penalites <= 0 || $this->montant_engage <= 0) {
            return 0;
        }

        $joursRetard = $this->jours_retard_actuels;
        if ($joursRetard <= 0) {
            return 0;
        }

        $penalites = ($this->montant_engage * $this->taux_penalites * $joursRetard) / 100;
        $plafond = $this->montant_engage * 0.10;

        return min($penalites, $plafond);
    }

    /**
     * Obtenir l'historique complet du lot
     */
    public function getHistoriqueComplet()
    {
        return self::where('lot_id', $this->lot_id)
            ->with(['prestataire', 'proforma', 'createdBy'])
            ->orderBy('version_attribution', 'asc')
            ->get();
    }

    /**
     * Vérifier si un lot a une attribution active
     */
    public static function lotEstAttribue(string $lotId): bool
    {
        return self::where('lot_id', $lotId)
            ->where('is_active', true)
            ->where('statut_attribution', self::STATUT_ATTRIBUE)
            ->exists();
    }

    /**
     * Obtenir l'attribution active d'un lot
     */
    public static function getAttributionActiveDuLot(string $lotId): ?self
    {
        return self::where('lot_id', $lotId)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Statistiques par prestataire
     */
    public static function statistiquesPrestataire(string $prestataireId): array
    {
        $attributions = self::where('prestataire_id', $prestataireId)->get();

        return [
            'total' => $attributions->count(),
            'en_cours' => $attributions->where('statut_attribution', self::STATUT_ATTRIBUE)->where('is_active', true)->count(),
            'terminees' => $attributions->where('statut_attribution', self::STATUT_TERMINE)->count(),
            'suspendues' => $attributions->where('statut_attribution', self::STATUT_SUSPENDU)->count(),
            'retirees' => $attributions->where('statut_attribution', self::STATUT_RETIRE)->count(),
            'montant_total_engage' => $attributions->sum('montant_engage'),
            'montant_total_paye' => $attributions->sum('montant_paye'),
            'penalites_totales' => $attributions->sum('penalites_appliquees'),
        ];
    }

    // ==================== BOOT ====================

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->created_by)) {
                $model->created_by = Auth::id();
            }
            if (empty($model->numero_attribution)) {
                $model->numero_attribution = self::genererNumeroAttribution();
            }
        });

        static::updating(function ($model) {
            $model->updated_by = Auth::id();
        });

        static::deleting(function ($model) {
            $model->deleted_by = Auth::id();
            $model->save();
        });
    }
}
