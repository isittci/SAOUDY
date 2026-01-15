<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Proforma extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'proformas';
    protected $primaryKey = 'id_proforma';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'parent_id',
        'version_proforma',
        'numero_proforma',
        'date_proforma',
        'date_fin_validee_proforma',
        'date_debut_validee_proforma',
        'date_redemarrage_proforma',
        'montant_retenu_proforma',
        'taxe_montant',
        'remise_montant_proforma',
        'modalite_proforma',
        'penalites_proforma',
        'motif_modification_proforma',
        'actif_proforma',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'date_proforma' => 'date',
        'date_fin_validee_proforma' => 'date',
        'date_debut_validee_proforma' => 'date',
        'date_redemarrage_proforma' => 'date',
        'montant_retenu_proforma' => 'decimal:2',
        'taxe_montant' => 'decimal:2',
        'remise_montant_proforma' => 'decimal:2',
        'penalites_proforma' => 'decimal:2',
        'actif_proforma' => 'boolean',
        'version_proforma' => 'integer',
    ];

    /**
     * ================================================================
     * RELATIONS
     * ================================================================
     */

    /**
     * Relation parent (auto-référence pour versionnement)
     */
    public function parent()
    {
        return $this->belongsTo(Proforma::class, 'parent_id', 'id_proforma');
    }

    /**
     * Relation versions (enfants)
     */
    public function versions()
    {
        return $this->hasMany(Proforma::class, 'parent_id', 'id_proforma')
            ->orderBy('version_proforma', 'desc');
    }

    /**
     * Dernière version
     */
    public function derniereVersion()
    {
        return $this->hasOne(Proforma::class, 'parent_id', 'id_proforma')
            ->latest('version_proforma');
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
     * Personne ayant supprimé
     */
    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * Relations avec les prestataires et lots via table pivot
     */
    public function prestataireLotsAttributions()
    {
        return $this->hasMany(PrestataireLot::class, 'proforma_id', 'id_proforma');
    }


    /**
     * ================================================================
     * SCOPES
     * ================================================================
     */

    /**
     * Scope pour obtenir uniquement les proformas actives
     */
    public function scopeActif($query)
    {
        return $query->where('actif_proforma', true);
    }

    /**
     * Scope pour obtenir uniquement les proformas inactives
     */
    public function scopeInactif($query)
    {
        return $query->where('actif_proforma', false);
    }

    /**
     * Scope pour obtenir les versions actuelles (sans parent)
     */
    public function scopeVersionActuelle($query)
    {
        return $query->whereNull('parent_id')
            ->where('actif_proforma', true)
            ->orWhereDoesntHave('versions');
    }

    /**
     * Scope pour filtrer par numéro
     */
    public function scopeByNumero($query, $numero)
    {
        return $query->where('numero_proforma', 'like', "%{$numero}%");
    }

    /**
     * Scope pour filtrer par période
     */
    public function scopeParPeriode($query, $dateDebut, $dateFin)
    {
        return $query->whereBetween('date_proforma', [$dateDebut, $dateFin]);
    }

    /**
     * ================================================================
     * MÉTHODES UTILITAIRES
     * ================================================================
     */

    /**
     * Générer le prochain numéro de proforma
     */
    public static function genererNumeroProforma()
    {
        $annee = date('Y');
        $dernier = self::where('numero_proforma', 'like', "PROF-{$annee}-%")
            ->orderBy('numero_proforma', 'desc')
            ->first();

        if ($dernier) {
            $dernierNumero = intval(substr($dernier->numero_proforma, -4));
            $nouveauNumero = $dernierNumero + 1;
        } else {
            $nouveauNumero = 1;
        }

        return 'PROF-' . $annee . '-' . str_pad($nouveauNumero, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Créer une nouvelle version de la proforma
     */
    public function creerNouvelleVersion(array $donnees, $motif = null)
    {

        // Désactiver la version actuelle
        $this->actif_proforma = false;
        $this->save();

        // Créer la nouvelle version
        $nouvelleVersion = $this->replicate();
        $nouvelleVersion->parent_id = $this->id_proforma;
        $nouvelleVersion->version_proforma = $this->version_proforma + 1;
        $nouvelleVersion->actif_proforma = true;

        if ($motif) {
            $nouvelleVersion->motif_modification_proforma = $motif;
        }

        // Appliquer les nouvelles données
        foreach ($donnees as $cle => $valeur) {
            if (in_array($cle, $this->fillable)) {
                $nouvelleVersion->$cle = $valeur;
            }
        }

        $nouvelleVersion->save();

        return $nouvelleVersion;
    }

    /**
     * Vérifier si c'est la version actuelle
     */
    public function isVersionActuelle()
    {
        if ($this->parent_id) {
            return false;
        }
        return !$this->versions()->exists() || $this->actif_proforma;
    }

    /**
     * Obtenir l'historique complet des versions
     */
    public function getHistorique()
    {
        if ($this->parent_id) {
            return $this->parent->getHistorique();
        }

        return $this->versions()
            ->with(['creator', 'updater'])
            ->get()
            ->prepend($this)
            ->sortByDesc('version_proforma');
    }

    /**
     * Calculer le montant total TTC
     */
    public function calculerMontantTTC()
    {
        $montantHT = $this->montant_retenu_proforma - $this->remise_montant_proforma;
        $montantTTC = $montantHT + $this->taxe_montant;

        return round($montantTTC, 2);
    }

    /**
     * Calculer le montant HT après remise
     */
    public function calculerMontantHTApresRemise()
    {
        return round($this->montant_retenu_proforma - $this->remise_montant_proforma, 2);
    }

    /**
     * Calculer le pourcentage de remise
     */
    public function calculerPourcentageRemise()
    {
        if ($this->montant_retenu_proforma > 0) {
            return round(($this->remise_montant_proforma / $this->montant_retenu_proforma) * 100, 2);
        }
        return 0;
    }

    /**
     * Calculer le taux de taxe
     */
    public function calculerTauxTaxe()
    {
        $montantHT = $this->calculerMontantHTApresRemise();
        if ($montantHT > 0) {
            return round(($this->taxe_montant / $montantHT) * 100, 2);
        }
        return 0;
    }

    /**
     * Activer la proforma
     */
    public function activer($userId = null)
    {
        $this->actif_proforma = true;
        $this->updated_by = $userId;
        $this->save();

        return $this;
    }

    /**
     * Désactiver la proforma
     */
    public function desactiver($userId = null)
    {
        $this->actif_proforma = false;
        $this->updated_by = $userId;
        $this->save();

        return $this;
    }

    /**
     * Vérifier si la proforma est utilisée dans une attribution
     */
    public function estUtilisee()
    {
        return $this->prestataireLotsAttributions()->exists();
    }

    /**
     * Obtenir le résumé de la proforma
     */
    public function getResume()
    {
        return [
            'numero' => $this->numero_proforma,
            'version' => $this->version_proforma,
            'date' => $this->date_proforma ? $this->date_proforma->format('d/m/Y') : null,
            'montant_ht' => number_format($this->montant_retenu_proforma, 2, ',', ' '),
            'remise' => number_format($this->remise_montant_proforma, 2, ',', ' '),
            'montant_ht_apres_remise' => number_format($this->calculerMontantHTApresRemise(), 2, ',', ' '),
            'taxe' => number_format($this->taxe_montant, 2, ',', ' '),
            'montant_ttc' => number_format($this->calculerMontantTTC(), 2, ',', ' '),
            'penalites' => number_format($this->penalites_proforma, 2, ',', ' '),
            'modalite' => $this->modalite_proforma,
            'actif' => $this->actif_proforma,
        ];
    }

    /**
     * ================================================================
     * ACCESSEURS (ATTRIBUTES)
     * ================================================================
     */

    /**
     * Obtenir le montant TTC (accesseur)
     */
    public function getMontantTtcAttribute()
    {
        return $this->calculerMontantTTC();
    }

    /**
     * Obtenir le montant HT après remise (accesseur)
     */
    public function getMontantHtApresRemiseAttribute()
    {
        return $this->calculerMontantHTApresRemise();
    }

    /**
     * Obtenir le pourcentage de remise (accesseur)
     */
    public function getPourcentageRemiseAttribute()
    {
        return $this->calculerPourcentageRemise();
    }

    /**
     * Obtenir le taux de taxe (accesseur)
     */
    public function getTauxTaxeAttribute()
    {
        return $this->calculerTauxTaxe();
    }


    /**
     * Relation avec la facture associée.
     * Une proforma peut avoir une seule facture (relation 1:1)
     *
     * @return HasOne
     */
    public function facture(): HasOne
    {
        return $this->hasOne(Facture::class, 'proforma_id', 'id_proforma');
    }

    /**
     * Vérifier si la proforma a une facture.
     *
     * @return bool
     */
    public function aUneFacture(): bool
    {
        return $this->facture()->exists();
    }

    /**
     * Scope pour les proformas sans facture.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSansFacture($query)
    {
        return $query->whereDoesntHave('facture');
    }

    /**
     * Scope pour les proformas avec facture.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAvecFacture($query)
    {
        return $query->whereHas('facture');
    }


    public function prestatairePrincipal()
    {
        return $this->hasOne(PrestataireLot::class, 'proforma_id', 'id_proforma')
            ->where('is_active', true)
            ->with(['prestataire', 'lot'])
            ->oldest('created_at');
    }

    public function prestataireRetire()
    {
        return $this->hasOne(PrestataireLot::class, 'proforma_id', 'id_proforma')
            ->where('is_active', false)
            ->with(['prestataire', 'lot'])
            ->oldest('created_at');
    }

    public function getPrestataire()
    {
        return $this->prestatairePrincipal?->prestataire;
    }

     public function getPrestataireRetire()
    {
        return $this->prestataireRetire?->prestataire;
    }

    public function getPrestataireId()
    {
        return $this->prestatairePrincipal?->prestataire_id;
    }

    public function aUnPrestataire(): bool
    {
        return $this->prestataireLotsAttributions()
            ->where('is_active', true)
            ->exists();
    }


    /**
     * ================================================================
     * EVENTS (BOOT)
     * ================================================================
     */

    protected static function boot()
    {
        parent::boot();

        // Génération automatique du numéro lors de la création
        static::creating(function ($proforma) {
            if (empty($proforma->numero_proforma)) {
                $proforma->numero_proforma = self::genererNumeroProforma();
            }
        });

        // Validation avant suppression
        static::deleting(function ($proforma) {
            if ($proforma->estUtilisee()) {
                throw new \Exception("Impossible de supprimer cette proforma car elle est utilisée dans des attributions.");
            }
        });
    }
}
