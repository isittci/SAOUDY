<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Banque extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * La table associée au modèle.
     *
     * @var string
     */
    protected $table = 'banques';

    /**
     * La clé primaire de la table.
     *
     * @var string
     */
    protected $primaryKey = 'id_banque';

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
        'prestataire_id',
        'nom_banque',
        'code_banque',
        'numero_compte_banque',
        'code_guichet_banque',
        'cle_rib_banque',
        'iban_banque',
        'swift_bic_banque',
        'titulaire_compte_banque',
        'actif_banque',
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
        'actif_banque' => 'boolean',
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
        'statut_format',
        'rib_complet',
    ];

    // =========================================================================
    // RELATIONS
    // =========================================================================

    /**
     * Récupérer le prestataire associé à cette banque.
     */
    public function prestataire(): BelongsTo
    {
        return $this->belongsTo(Prestataire::class, 'prestataire_id', 'id_prestataire');
    }

    /**
     * Récupérer les paiements effectués via cette banque.
     */
    public function paiements(): HasMany
    {
        return $this->hasMany(Paiement::class, 'banque_id', 'id_banque');
    }

    /**
     * Récupérer l'utilisateur qui a créé cette banque.
     */
    public function createur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    /**
     * Récupérer l'utilisateur qui a modifié cette banque.
     */
    public function modificateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    /**
     * Récupérer l'utilisateur qui a supprimé cette banque.
     */
    public function suppresseur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by', 'id');
    }

    // =========================================================================
    // SCOPES
    // =========================================================================

    /**
     * Scope pour filtrer les banques actives.
     */
    public function scopeActif(Builder $query): Builder
    {
        return $query->where('actif_banque', true);
    }

    /**
     * Scope pour filtrer les banques inactives.
     */
    public function scopeInactif(Builder $query): Builder
    {
        return $query->where('actif_banque', false);
    }

    /**
     * Scope pour filtrer par prestataire.
     */
    public function scopeByPrestataire(Builder $query, string $prestataireId): Builder
    {
        return $query->where('prestataire_id', $prestataireId);
    }

    /**
     * Scope pour la recherche globale.
     */
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('nom_banque', 'LIKE', "%{$search}%")
              ->orWhere('code_banque', 'LIKE', "%{$search}%")
              ->orWhere('numero_compte_banque', 'LIKE', "%{$search}%")
              ->orWhere('iban_banque', 'LIKE', "%{$search}%")
              ->orWhere('swift_bic_banque', 'LIKE', "%{$search}%")
              ->orWhere('titulaire_compte_banque', 'LIKE', "%{$search}%")
              ->orWhereHas('prestataire', function ($subQuery) use ($search) {
                  $subQuery->where('raison_sociale_prestataire', 'LIKE', "%{$search}%");
              });
        });
    }

    /**
     * Scope pour le tri dynamique.
     */
    public function scopeSorted(Builder $query, ?string $sortBy = 'created_at', ?string $sortOrder = 'desc'): Builder
    {
        $allowedSorts = [
            'nom_banque',
            'code_banque',
            'numero_compte_banque',
            'titulaire_compte_banque',
            'actif_banque',
            'created_at',
            'updated_at',
        ];

        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'created_at';
        }

        $sortOrder = in_array(strtolower($sortOrder), ['asc', 'desc']) ? strtolower($sortOrder) : 'desc';

        return $query->orderBy($sortBy, $sortOrder);
    }

    // =========================================================================
    // ACCESSEURS
    // =========================================================================

    /**
     * Obtenir le statut formaté de la banque.
     */
    public function getStatutFormatAttribute(): string
    {
        return $this->actif_banque ? 'Actif' : 'Inactif';
    }

    /**
     * Obtenir le RIB complet.
     */
    public function getRibCompletAttribute(): ?string
    {
        $parts = array_filter([
            $this->code_banque,
            $this->code_guichet_banque,
            $this->numero_compte_banque,
            $this->cle_rib_banque,
        ]);

        return !empty($parts) ? implode(' ', $parts) : null;
    }

    /**
     * Obtenir le nom de la banque avec le code.
     */
    public function getNomCompletAttribute(): string
    {
        if ($this->code_banque) {
            return "{$this->nom_banque} ({$this->code_banque})";
        }
        return $this->nom_banque ?? '';
    }

    /**
     * Obtenir le numéro de compte masqué (pour affichage sécurisé).
     */
    public function getNumeroCompteMasqueAttribute(): ?string
    {
        if (empty($this->numero_compte_banque)) {
            return null;
        }

        $length = strlen($this->numero_compte_banque);
        if ($length <= 4) {
            return str_repeat('*', $length);
        }

        return str_repeat('*', $length - 4) . substr($this->numero_compte_banque, -4);
    }

    // =========================================================================
    // MUTATEURS
    // =========================================================================

    /**
     * Formater le code banque avant sauvegarde.
     */
    public function setCodeBanqueAttribute(?string $value): void
    {
        $this->attributes['code_banque'] = $value ? strtoupper(trim($value)) : null;
    }

    /**
     * Formater le code guichet avant sauvegarde.
     */
    public function setCodeGuichetBanqueAttribute(?string $value): void
    {
        $this->attributes['code_guichet_banque'] = $value ? strtoupper(trim($value)) : null;
    }

    /**
     * Formater l'IBAN avant sauvegarde.
     */
    public function setIbanBanqueAttribute(?string $value): void
    {
        $this->attributes['iban_banque'] = $value ? strtoupper(str_replace(' ', '', trim($value))) : null;
    }

    /**
     * Formater le SWIFT/BIC avant sauvegarde.
     */
    public function setSwiftBicBanqueAttribute(?string $value): void
    {
        $this->attributes['swift_bic_banque'] = $value ? strtoupper(trim($value)) : null;
    }

    // =========================================================================
    // MÉTHODES MÉTIER
    // =========================================================================

    /**
     * Activer la banque.
     */
    public function activer(): bool
    {
        $this->actif_banque = true;
        $this->updated_by = auth()->id();
        return $this->save();
    }

    /**
     * Désactiver la banque.
     */
    public function desactiver(): bool
    {
        $this->actif_banque = false;
        $this->updated_by = auth()->id();
        return $this->save();
    }

    /**
     * Basculer le statut de la banque.
     */
    public function toggleStatut(): bool
    {
        $this->actif_banque = !$this->actif_banque;
        $this->updated_by = auth()->id();
        return $this->save();
    }

    /**
     * Vérifier si la banque a des paiements associés.
     */
    public function hasPaiements(): bool
    {
        return $this->paiements()->exists();
    }

    /**
     * Obtenir le nombre de paiements.
     */
    public function nombrePaiements(): int
    {
        return $this->paiements()->count();
    }

    /**
     * Obtenir le montant total des paiements.
     */
    public function montantTotalPaiements(): float
    {
        return (float) $this->paiements()->sum('montant_net_paye_paiement');
    }

    /**
     * Dupliquer la banque pour un autre prestataire.
     */
    public function dupliquer(?string $nouveauPrestataireId = null): self
    {
        $nouvelle = $this->replicate([
            'id_banque',
            'created_at',
            'updated_at',
            'deleted_at',
            'created_by',
            'updated_by',
            'deleted_by',
        ]);

        if ($nouveauPrestataireId) {
            $nouvelle->prestataire_id = $nouveauPrestataireId;
        }

        $nouvelle->created_by = auth()->id();
        $nouvelle->save();

        return $nouvelle;
    }

    /**
     * Vérifier si les informations RIB sont complètes.
     */
    public function isRibComplet(): bool
    {
        return !empty($this->code_banque)
            && !empty($this->code_guichet_banque)
            && !empty($this->numero_compte_banque)
            && !empty($this->cle_rib_banque);
    }

    /**
     * Vérifier si les informations IBAN sont présentes.
     */
    public function hasIban(): bool
    {
        return !empty($this->iban_banque);
    }

    /**
     * Vérifier si les informations SWIFT/BIC sont présentes.
     */
    public function hasSwift(): bool
    {
        return !empty($this->swift_bic_banque);
    }

    // =========================================================================
    // BOOT
    // =========================================================================

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        // Avant la création, définir l'utilisateur créateur
        static::creating(function (Banque $banque) {
            if (auth()->check() && empty($banque->created_by)) {
                $banque->created_by = auth()->id();
            }
        });

        // Avant la mise à jour, définir l'utilisateur modificateur
        static::updating(function (Banque $banque) {
            if (auth()->check()) {
                $banque->updated_by = auth()->id();
            }
        });

        // Avant la suppression (soft delete), définir l'utilisateur suppresseur
        static::deleting(function (Banque $banque) {
            if (auth()->check()) {
                $banque->deleted_by = auth()->id();
                $banque->saveQuietly();
            }
        });
    }
}
