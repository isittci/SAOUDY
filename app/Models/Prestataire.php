<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prestataire extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'prestataires';
    protected $primaryKey = 'id_prestataire';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'raison_sociale_prestataire',
        'numero_identification_prestataire',
        'email_prestataire',
        'numero_cc_prestataire',
        'numero_rccm_prestataire',
        'telephone_principal_prestataire',
        'telephone_secondaire_prestataire',
        'adresse_prestataire',
        'ville_prestataire',
        'pays_prestataire',
        'representant_legal_prestataire',
        'statut_prestataire',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'statut_prestataire' => 'boolean',
    ];

    /**
     * ================================================================
     * RELATIONS
     * ================================================================
     */

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
     * Relations avec les lots via table pivot prestataires_lots
     */
    public function lots()
    {
        return $this->belongsToMany(Lot::class, 'prestataires_lots', 'prestataire_id', 'lot_id')
            ->withPivot([
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
                'deleted_by'
            ])
            ->withTimestamps()
            ->using(PrestataireLot::class);
    }

    /**
     * Lots actuellement attribués
     */
    public function lotsAttribues()
    {
        return $this->lots()
            ->wherePivot('statut_attribution', PrestataireLot::STATUT_ATTRIBUE)
            ->wherePivotNull('deleted_at');
    }

    /**
     * Lots en cours (attribués ou suspendus)
     */
    public function lotsEnCours()
    {
        return $this->lots()
            ->whereIn('prestataires_lots.statut_attribution', [
                PrestataireLot::STATUT_ATTRIBUE,
                PrestataireLot::STATUT_SUSPENDU
            ])
            ->wherePivotNull('deleted_at');
    }

    /**
     * Toutes les attributions (historique complet)
     */
    public function attributions()
    {
        return $this->hasMany(PrestataireLot::class, 'prestataire_id', 'id_prestataire');
    }

    /**
     * Attributions actives
     */
    public function attributionsActives()
    {
        return $this->hasMany(PrestataireLot::class, 'prestataire_id', 'id_prestataire')
            ->whereIn('statut_attribution', [
                PrestataireLot::STATUT_ATTRIBUE,
                PrestataireLot::STATUT_SUSPENDU
            ]);
    }

    /**
     * Historique complet des attributions (avec supprimés)
     */
    public function historiqueAttributions()
    {
        return $this->hasMany(PrestataireLot::class, 'prestataire_id', 'id_prestataire')
            ->withTrashed()
            ->orderBy('created_at', 'desc');
    }

    /**
     * Documents du prestataire
     */
    public function documents()
    {
        return $this->hasMany(Document::class, 'prestataire_id', 'id_prestataire');
    }

    /**
     * Banques du prestataire
     */
    public function banques()
    {
        return $this->hasMany(Banque::class, 'prestataire_id', 'id_prestataire');
    }

    /**
     * Capacités techniques
     */
    public function capacitesTechniques()
    {
        return $this->hasMany(CapaciteTechnique::class, 'prestataire_id', 'id_prestataire');
    }

    /**
     * Situations financières
     */
    public function situationsFinancieres()
    {
        return $this->hasMany(SituationFinanciere::class, 'prestataire_id', 'id_prestataire');
    }

    /**
     * Évaluations
     */
    // public function evaluations()
    // {
    //     return $this->hasMany(Evaluation::class, 'prestataire_id', 'id_prestataire');
    // }

    /**
     * ================================================================
     * SCOPES
     * ================================================================
     */

    /**
     * Scope pour obtenir les prestataires actifs
     */
    public function scopeActif($query)
    {
        return $query->where('statut_prestataire', true);
    }

    /**
     * Scope pour obtenir les prestataires inactifs
     */
    public function scopeInactif($query)
    {
        return $query->where('statut_prestataire', false);
    }

    /**
     * Scope pour rechercher par raison sociale
     */
    public function scopeRechercherRaisonSociale($query, $terme)
    {
        return $query->where('raison_sociale_prestataire', 'like', "%{$terme}%");
    }

    /**
     * Scope pour rechercher par numéro d'identification
     */
    public function scopeByNumeroIdentification($query, $numero)
    {
        return $query->where('numero_identification_prestataire', $numero);
    }

    /**
     * Scope pour rechercher par pays
     */
    public function scopeByPays($query, $pays)
    {
        return $query->where('pays_prestataire', $pays);
    }

    /**
     * Scope pour rechercher par ville
     */
    public function scopeByVille($query, $ville)
    {
        return $query->where('ville_prestataire', $ville);
    }

    /**
     * Scope pour obtenir les prestataires disponibles (sans lots actifs)
     */
    public function scopeDisponible($query)
    {
        return $query->whereDoesntHave('attributionsActives');
    }

    /**
     * Scope pour obtenir les prestataires avec lots en cours
     */
    public function scopeAvecLotsEnCours($query)
    {
        return $query->whereHas('attributionsActives');
    }

    /**
     * ================================================================
     * MÉTHODES UTILITAIRES
     * ================================================================
     */

    /**
     * Activer le prestataire
     */
    public function activer($userId = null)
    {
        $this->statut_prestataire = true;
        $this->updated_by = $userId;
        $this->save();

        return $this;
    }

    /**
     * Désactiver le prestataire
     */
    public function desactiver($userId = null)
    {
        $this->statut_prestataire = false;
        $this->updated_by = $userId;
        $this->save();

        return $this;
    }

    /**
     * Vérifier si le prestataire est actif
     */
    public function isActif()
    {
        return $this->statut_prestataire == true;
    }

    /**
     * Obtenir le nombre de lots attribués
     */
    public function getNombreLotsAttribues()
    {
        return $this->lotsAttribues()->count();
    }

    /**
     * Obtenir le nombre total d'attributions (historique)
     */
    public function getNombreTotalAttributions()
    {
        return $this->attributions()->count();
    }

    /**
     * Calculer le taux de réussite (lots terminés avec succès)
     */
    public function calculerTauxReussite()
    {
        $total = $this->attributions()->count();

        if ($total == 0) {
            return 0;
        }

        $reussis = $this->attributions()
            ->where('statut_attribution', PrestataireLot::STATUT_TERMINE)
            ->whereNull('motif_retrait')
            ->count();

        return round(($reussis / $total) * 100, 2);
    }

    /**
     * Calculer les pénalités totales
     */
    public function calculerPenalitesTotales()
    {
        return $this->attributions()
            ->sum('penalites_appliquees') ?? 0;
    }

    /**
     * Calculer le retard moyen
     */
    public function calculerRetardMoyen()
    {
        $attributions = $this->attributions()
            ->where('jours_retard', '>', 0)
            ->get();

        if ($attributions->isEmpty()) {
            return 0;
        }

        $totalRetard = $attributions->sum('jours_retard');
        return round($totalRetard / $attributions->count(), 2);
    }

    /**
     * Vérifier si le prestataire a des lots en cours
     */
    public function aDesLotsEnCours()
    {
        return $this->attributionsActives()->exists();
    }

    /**
     * Obtenir les statistiques du prestataire
     */
    public function getStatistiques()
    {
        return [
            'nombre_attributions_totales' => $this->getNombreTotalAttributions(),
            'nombre_lots_en_cours' => $this->attributionsActives()->count(),
            'nombre_lots_termines' => $this->attributions()
                ->where('statut_attribution', PrestataireLot::STATUT_TERMINE)
                ->count(),
            'nombre_lots_retires' => $this->attributions()
                ->where('statut_attribution', PrestataireLot::STATUT_RETIRE)
                ->count(),
            'taux_reussite' => $this->calculerTauxReussite(),
            'penalites_totales' => $this->calculerPenalitesTotales(),
            'retard_moyen_jours' => $this->calculerRetardMoyen(),
        ];
    }

    /**
     * Obtenir le nom complet (raison sociale)
     */
    public function getNomComplet()
    {
        return $this->raison_sociale_prestataire;
    }

    /**
     * Obtenir les informations de contact
     */
    public function getInfosContact()
    {
        return [
            'telephone_principal' => $this->telephone_principal_prestataire,
            'telephone_secondaire' => $this->telephone_secondaire_prestataire,
            'email' => $this->email_prestataire,
        ];
    }

    /**
     * Obtenir l'adresse complète
     */
    public function getAdresseComplete()
    {
        $adresse = [];

        if ($this->adresse_prestataire) {
            $adresse[] = $this->adresse_prestataire;
        }

        if ($this->ville_prestataire) {
            $adresse[] = $this->ville_prestataire;
        }

        if ($this->pays_prestataire) {
            $adresse[] = $this->pays_prestataire;
        }

        return implode(', ', $adresse);
    }

    /**
     * Vérifier si le prestataire peut recevoir une nouvelle attribution
     */
    public function peutRecevoirAttribution()
    {
        // Le prestataire doit être actif
        if (!$this->isActif()) {
            return [
                'eligible' => false,
                'raison' => 'Le prestataire est inactif.'
            ];
        }

        // Vous pouvez ajouter d'autres règles ici
        // Par exemple : limite de lots simultanés, documents expirés, etc.

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
     * Obtenir le statut formaté
     */
    public function getStatutFormatAttribute()
    {
        return $this->statut_prestataire ? 'Actif' : 'Inactif';
    }

    /**
     * ================================================================
     * EVENTS (BOOT)
     * ================================================================
     */

    protected static function boot()
    {
        parent::boot();

        // Validation avant suppression
        static::deleting(function ($prestataire) {
            // Empêcher la suppression si le prestataire a des lots en cours
            if ($prestataire->aDesLotsEnCours()) {
                throw new \Exception("Impossible de supprimer ce prestataire car il a des lots en cours d'exécution.");
            }
        });
    }
}
