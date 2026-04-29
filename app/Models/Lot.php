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

        // Si pas de facture, considérer le lot comme non clôturé (paiement d'abord avant de clôturer)
        if (!$facture) {
            return false;
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
                'pourcentage_avancement',
                'observations',
                'created_by',
                'updated_by',
                'deleted_by'
            ])
            ->withTimestamps()
            ->using(AttributionLotPrestataire::class);
    }

    public function prestataireActuel()
    {
        return $this->prestataires()
            ->wherePivot('statut_attribution', AttributionLotPrestataire::STATUT_ATTRIBUE)
            ->wherePivotNull('deleted_at')
            ->latest('prestataires_lots.created_at');
    }

    public function attributions()
    {
        return $this->hasMany(AttributionLotPrestataire::class, 'lot_id', 'id_lot');
    }

    public function attributionActive()
    {

        return $this->hasOne(AttributionLotPrestataire::class, 'lot_id', 'id_lot');
    }

    public function historiqueAttributions()
    {
        return $this->hasMany(AttributionLotPrestataire::class, 'lot_id', 'id_lot')
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
        $eligibilite = AttributionLotPrestataire::prestataireEligible($prestataire, $this);

        if (!$eligibilite['eligible']) {
            throw new \Exception($eligibilite['raison']);
        }

        // Créer l'attribution
        /**
         * @var AttributionLotPrestataire $attribution
         */
        $attribution = AttributionLotPrestataire::create([
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
         * @var AttributionLotPrestataire $attributionActive
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











    /**
 * Obtenir toutes les proformas utilisées pour ce lot (via attributions)
 *
 * @return \Illuminate\Database\Eloquent\Collection
 */
public function proformasUtilisees()
{
    return Proforma::whereHas('attribution', function ($query) {
        $query->where('lot_id', $this->id_lot);
    })->get();
}

/**
 * Relation pour obtenir les proformas directement via les attributions
 *
 * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
 */
public function proformas()
{
    return $this->hasManyThrough(
        Proforma::class,
        AttributionLotPrestataire::class,
        'lot_id',           // Clé étrangère sur prestataires_lots
        'id_proforma',      // Clé primaire sur proformas
        'id_lot',           // Clé locale sur lots
        'proforma_id'       // Clé étrangère sur prestataires_lots vers proformas
    );
}

/**
 * Vérifier si une proforma spécifique est déjà utilisée pour ce lot
 *
 * @param string $proformaId
 * @return bool
 */
public function proformaDejaUtiliseePourCeLot(string $proformaId): bool
{
    return $this->attributions()
        ->where('proforma_id', $proformaId)
        ->exists();
}

/**
 * Obtenir l'attribution pour un prestataire et une proforma donnés
 *
 * @param string $prestataireId
 * @param string $proformaId
 * @return AttributionLotPrestataire|null
 */
public function getAttributionParPrestataireEtProforma(string $prestataireId, string $proformaId): ?AttributionLotPrestataire
{
    return $this->attributions()
        ->where('prestataire_id', $prestataireId)
        ->where('proforma_id', $proformaId)
        ->first();
}

/**
 * Vérifier si le triplet existe pour ce lot
 *
 * @param string $prestataireId
 * @param string $proformaId
 * @return bool
 */
public function tripletExistePourCeLot(string $prestataireId, string $proformaId): bool
{
    return AttributionLotPrestataire::tripletExiste($prestataireId, $this->id_lot, $proformaId);
}

/**
 * Obtenir la proforma de l'attribution active
 *
 * @return Proforma|null
 */
public function getProformaActive(): ?Proforma
{
    return $this->attributionActive?->proforma;
}

/**
 * Vérifier si une proforma peut être utilisée pour ce lot
 *
 * @param Proforma $proforma
 * @param string|null $prestataireId
 * @return array ['valide' => bool, 'raison' => string|null]
 */
public function validerProformaPourAttribution(Proforma $proforma, ?string $prestataireId = null): array
{
    // Vérifier si la proforma est déjà utilisée (globalement)
    if ($proforma->estAttribuee()) {
        return [
            'valide' => false,
            'raison' => "Cette proforma est déjà utilisée dans une autre attribution."
        ];
    }

    // Vérifier si la proforma est active
    if (!$proforma->actif_proforma) {
        return [
            'valide' => false,
            'raison' => "Cette proforma est inactive."
        ];
    }

    // Vérifier le triplet si un prestataire est spécifié
    if ($prestataireId && $this->tripletExistePourCeLot($prestataireId, $proforma->id_proforma)) {
        return [
            'valide' => false,
            'raison' => "Une attribution avec ce prestataire et cette proforma existe déjà pour ce lot."
        ];
    }

    return [
        'valide' => true,
        'raison' => null
    ];
}










/**
 * Notifier l'appel d'offres parent pour qu'il recalcule son état
 * À appeler après toute modification impactant l'état (attribution, évaluation, paiement)
 *
 * @param int|null $userId ID de l'utilisateur
 * @return void
 */
public function notifierAppelOffreParent(?int $userId = null): void
{
    if ($this->appelOffre) {
        $this->appelOffre->mettreAJourEtat($userId);
    }
}

/**
 * Obtenir le détail du statut de clôture du lot
 *
 * @return array
 */
public function getDetailStatutCloture(): array
{
    $attributionActive = $this->attributionActive;

    // Vérification de l'attribution
    $aAttribution = $attributionActive !== null;

    // Vérification des évaluations
    $sommesReferencesCriteres = $this->criteresEvaluation->sum('note_reference_critere_evaluation');
    $sommesNotesEvaluations = $this->criteresEvaluation->flatMap->evaluations->sum('resultat_evaluation');
    $evaluationsCompletes = $sommesReferencesCriteres === 0 || $sommesNotesEvaluations >= $sommesReferencesCriteres;
    $pourcentageEvaluations = $sommesReferencesCriteres > 0
        ? round(($sommesNotesEvaluations / $sommesReferencesCriteres) * 100, 1)
        : 100;

    // Vérification des paiements
    $facture = $attributionActive?->proforma?->facture;
    $montantFacture = $facture?->montant_facture ?? 0;
    $montantPaye = $facture?->paiements?->sum('montant_net_paye_paiement') ?? 0;
    $paiementsComplets = $montantFacture === 0 || $montantPaye >= $montantFacture;
    $pourcentagePaiements = $montantFacture > 0
        ? round(($montantPaye / $montantFacture) * 100, 1)
        : 100;

    return [
        'est_cloture' => $this->isCloture(),
        'a_attribution' => $aAttribution,
        'evaluations' => [
            'completes' => $evaluationsCompletes,
            'note_reference' => $sommesReferencesCriteres,
            'note_obtenue' => $sommesNotesEvaluations,
            'pourcentage' => $pourcentageEvaluations,
        ],
        'paiements' => [
            'complets' => $paiementsComplets,
            'montant_facture' => $montantFacture,
            'montant_paye' => $montantPaye,
            'pourcentage' => $pourcentagePaiements,
        ],
        'raison_non_cloture' => $this->getRaisonNonCloture(),
    ];
}

/**
 * Obtenir la raison pour laquelle le lot n'est pas clôturé
 *
 * @return string|null
 */
public function getRaisonNonCloture(): ?string
{
    if ($this->isCloture()) {
        return null;
    }

    $attributionActive = $this->attributionActive;

    if (!$attributionActive) {
        return "Aucune attribution active";
    }

    // Vérification des évaluations
    $sommesReferencesCriteres = $this->criteresEvaluation->sum('note_reference_critere_evaluation');
    $sommesNotesEvaluations = $this->criteresEvaluation->flatMap->evaluations->sum('resultat_evaluation');

    if ($sommesReferencesCriteres > 0 && $sommesNotesEvaluations < $sommesReferencesCriteres) {
        $reste = $sommesReferencesCriteres - $sommesNotesEvaluations;
        return "Évaluations incomplètes (reste {$reste} points)";
    }

    // Vérification des paiements
    $facture = $attributionActive->proforma?->facture;

    if ($facture) {
        $montantFacture = $facture->montant_facture ?? 0;
        $montantPaye = $facture->paiements?->sum('montant_net_paye_paiement') ?? 0;

        if ($montantFacture > 0 && $montantPaye < $montantFacture) {
            $reste = $montantFacture - $montantPaye;
            return "Paiements incomplets (reste " . number_format(floor($reste), 0, ',', ' ') . " FCFA)";
        }
    }

    return "Raison inconnue";
}

/**
 * Obtenir le pourcentage de progression du lot
 *
 * @return float
 */
public function getPourcentageProgressionAttribute(): float
{
    if (!$this->aUneAttributionActive()) {
        return 0;
    }

    $details = $this->getDetailStatutCloture();

    $poids = 0;
    $total = 0;

    // Évaluations (50% du poids si prévues)
    if ($details['evaluations']['note_reference'] > 0) {
        $total += $details['evaluations']['pourcentage'] * 0.5;
        $poids += 0.5;
    }

    // Paiements (50% du poids si prévus)
    if ($details['paiements']['montant_facture'] > 0) {
        $total += $details['paiements']['pourcentage'] * 0.5;
        $poids += 0.5;
    }

    // Si ni évaluations ni paiements prévus, 100% si attribué
    if ($poids === 0) {
        return 100;
    }

    return round($total / $poids, 1);
}
}
