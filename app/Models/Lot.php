<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lot extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'lots';
    protected $primaryKey = 'id_lot';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'appel_offre_id',
        'parent_id',
        'numero',
        'budget_lot',
        'libelle',
        'description_critere',
        'specifications_techniques',
        'date_attribution',
        'date_debut_prevue',
        'date_fin_prevue',
        'attribution_lot',
        'statut_lot',
        'taux_penalites',
        'date_retrait',
        'motif_retrait',
        'statut_retrait',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'date_attribution' => 'date',
        'date_debut_prevue' => 'datetime',
        'date_fin_prevue' => 'datetime',
        'date_retrait' => 'date',
        'attribution_lot' => 'integer',
        'statut_lot' => 'integer',
        'taux_penalites' => 'decimal:2',
        'statut_retrait' => 'integer',
    ];

    // Relations
    public function appelOffre()
    {
        return $this->belongsTo(AppelOffre::class, 'appel_offre_id', 'id_appel_offre');
    }

    public function parent()
    {
        return $this->belongsTo(Lot::class, 'parent_id', 'id_lot');
    }

    public function versions()
    {
        return $this->hasMany(Lot::class, 'parent_id', 'id_lot')
            ->orderBy('created_at', 'desc');
    }

    public function derniereVersion()
    {
        return $this->hasOne(Lot::class, 'parent_id', 'id_lot')
            ->latest('created_at');
    }

    public function isCloture()
{
    // Un lot est clôturé si :
    // 1. Il a une attribution active
    // 2. L'évaluation est terminée (si prévue)
    // 3. Le paiement est terminé (si prévu)

    $attributionActive = $this->attributionActive;

    // Si pas d'attribution active, le lot n'est pas clôturé
    if (!$attributionActive) {
        return false;
    }

    // Vérification de l'évaluation
    $sommesReferencesCriteresEvaluations = $this->criteresEvaluation->sum('note_reference_critere_evaluation');
    $sommesNotesEvaluations = $this->criteresEvaluation->flatMap->evaluations->sum('resultat_evaluation');

    // Si une évaluation est prévue mais non terminée
    if ($sommesReferencesCriteresEvaluations > 0 && $sommesNotesEvaluations < $sommesReferencesCriteresEvaluations) {
        return false;
    }

    // Vérification du paiement
    $facture = $attributionActive->proforma?->facture;

    // Si pas de facture, considérer le lot comme clôturé (pas de paiement prévu)
    if (!$facture) {
        return true;
    }

    $allPaiements = $facture->paiements ?? collect();
    $montantPaye = $allPaiements->sum('montant_net_paye_paiement');
    $montantFacture = $facture->montant_facture ?? 0;

    // Le lot est clôturé si le paiement est terminé
    return $montantFacture > 0 && $montantPaye >= $montantFacture;
}

    public function criteresEvaluation()
    {
        return $this->hasMany(CritereEvaluation::class, 'lot_id', 'id_lot');
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'lot_id', 'id_lot');
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

    // Relations avec prestataires via pivot
    public function prestataires()
    {
        return $this->belongsToMany(Prestataire::class, 'prestataires_lots', 'lot_id', 'prestataire_id')
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

    public function prestataireActuel()
    {
        return $this->prestataires()
            ->wherePivot('statut_attribution', PrestataireLot::STATUT_ATTRIBUE)
            ->wherePivotNull('deleted_at')
            ->latest('prestataires_lots.created_at');
    }

    public function attributions()
    {
        return $this->hasMany(PrestataireLot::class, 'lot_id', 'id_lot');
    }

    public function attributionActive()
    {

        return $this->hasOne(PrestataireLot::class, 'lot_id', 'id_lot')
            ->whereIn('statut_attribution', [
                PrestataireLot::STATUT_ATTRIBUE,
                PrestataireLot::STATUT_SUSPENDU
            ]);
    }

    public function historiqueAttributions()
    {
        return $this->hasMany(PrestataireLot::class, 'lot_id', 'id_lot')
            ->withTrashed()
            ->orderBy('created_at', 'desc');
    }

    // Scopes
    public function scopeVersionActuelle($query)
    {

        return $query->whereNull('parent_id')->where('statut_lot', 1)
            ->orWhereDoesntHave('versions');
    }

    public function scopeAttribue($query)
    {
        return $query->where('attribution_lot', 1);
    }

    public function scopeNonAttribue($query)
    {
        return $query->where('attribution_lot', 0);
    }

    public function scopeActif($query)
    {
        return $query->where('statut_lot', 1);
    }

    public function scopeRetire($query)
    {
        return $query->whereNotNull('date_retrait');
    }

    // Méthodes utilitaires
    public function creerNouvelleVersion(array $donnees, $motif = null)
    {
        $nouvelleVersion = $this->replicate();
        $nouvelleVersion->parent_id = $this->id_lot;

        if ($motif) {
            $nouvelleVersion->motif_retrait = $motif;
        }

        foreach ($donnees as $cle => $valeur) {
            if (in_array($cle, $this->fillable)) {
                $nouvelleVersion->$cle = $valeur;
            }
        }

        $nouvelleVersion->save();

        // $this->statut_lot = 0;
        // $this->save();

        // Copier les critères d'évaluation vers la nouvelle version
        foreach ($this->criteresEvaluation as $critere) {
            $nouveauCritere = $critere->replicate();
            $nouveauCritere->lot_id = $nouvelleVersion->id_lot;
            $nouveauCritere->save();
        }

        return $nouvelleVersion;
    }

    public function isVersionActuelle()
    {
        if ($this->parent_id) {
            return false;
        }
        return !$this->versions()->exists();
    }

    public function isAttribue()
    {
        return $this->attribution_lot == 1;
    }

    public function isRetire()
    {
        return !is_null($this->date_retrait);
    }

    public function getHistorique()
    {
        if ($this->parent_id) {
            return $this->parent->getHistorique();
        }

        return $this->versions()
            ->with(['creator', 'updater'])
            ->get()/*->orderBy('version_caracteristique_appel_offre', 'asc')*/
            ->prepend($this);
    }

    public function calculerDuree()
    {
        if ($this->date_debut_prevue && $this->date_fin_prevue) {
            return $this->date_debut_prevue->diffInDays($this->date_fin_prevue);
        }
        return null;
    }

    public function calculerPenalites($joursRetard)
    {
        if ($this->taux_penalites && $joursRetard > 0) {
            // Calcul basé sur le taux de pénalités
            return $this->taux_penalites * $joursRetard;
        }
        return 0;
    }

    public function retirer($motif, $userId = null)
    {
        $this->date_retrait = now();
        $this->motif_retrait = $motif;
        $this->statut_retrait = 1;
        // $this->deleted_by = $userId;
        $this->attribution_lot = 0;
        $this->save();

        return $this;
    }

    public function attribuer($dateAttribution = null)
    {
        $this->attribution_lot = 1;
        $this->date_attribution = $dateAttribution ?? now();
        $this->save();

        return $this;
    }

    /**
     * Attribuer le lot à un prestataire
     */
    public function attribuerAuPrestataire(Prestataire $prestataire, Proforma $proforma, $userId = null)
    {

        // Vérifier l'éligibilité
        $eligibilite = PrestataireLot::prestataireEligible($prestataire, $this);

        if (!$eligibilite['eligible']) {
            throw new \Exception($eligibilite['raison']);
        }

        // Créer l'attribution
        /**
         * @var PrestataireLot $attribution
         */
        $attribution = PrestataireLot::create([
            'prestataire_id' => $prestataire->id_prestataire,
            'lot_id' => $this->id_lot,
            'proforma_id' => $proforma->id_proforma,
            'created_by' => $userId,
        ]);

        // Attribuer via la méthode du pivot
        $attribution->attribuer($userId);

        return $attribution;
    }

    /**
     * Retirer le lot au prestataire actuel
     */
    public function retirerAttribution($motif, $userId = null)
    {
        /**
         * @var PrestataireLot $attributionActive
         */
        $attributionActive = $this->attributionActive;

        if (!$attributionActive) {
            throw new \Exception("Aucune attribution active pour ce lot.");
        }

        return $attributionActive->retirer($motif, $userId);
    }

    /**
     * Réattribuer à un nouveau prestataire
     */
    public function reattribuer(Prestataire $nouveauPrestataire, Proforma $nouvelleProforma, $motif, $userId = null)
    {
        $attributionActive = $this->attributionActive;

        if (!$attributionActive) {
            throw new \Exception("Aucune attribution active à réattribuer.");
        }

        return $attributionActive->reattribuerA($nouveauPrestataire, $nouvelleProforma, $motif, $userId);
    }

    /**
     * Suspendre l'attribution actuelle
     */
    public function suspendreAttribution($motif, $userId = null)
    {
        $attributionActive = $this->attributionActive;

        if (!$attributionActive) {
            throw new \Exception("Aucune attribution active à suspendre.");
        }

        return $attributionActive->suspendre($motif, $userId);
    }

    /**
     * Vérifier si le lot a une attribution active
     */
    public function aUneAttributionActive()
    {
        return $this->attributionActive()->exists();
    }

    /**
     * Obtenir les statistiques d'attribution
     */
    public function getStatistiquesAttribution()
    {
        $attributionActive = $this->attributionActive;

        if (!$attributionActive) {
            return null;
        }

        return $attributionActive->getStatistiquesExecution();
    }
}
