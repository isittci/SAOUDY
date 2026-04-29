<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AppelOffre extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'appels_offres';
    protected $primaryKey = 'id_appel_offre';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'type_appel_offre_id',
        'etat_appel_offre',
        'numero_appel_offre',
        'libelle_critere_appel_offre',
        'objet_critere_appel_offre',
        'montant_global_appel_offre',
        'statut_evaluation_critere_appel_offre',
        'conditions_participation_critere_appel_offre',
        'criteres_selection_critere_appel_offre',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'montant_global_appel_offre' => 'decimal:2',
        'statut_evaluation_critere_appel_offre' => 'integer',
        'etat_appel_offre' => 'integer'
    ];



    /**
     * États de l'appel d'offres
     *
     * 0 = En attente : Aucun lot ou aucun lot actif attribué
     * 1 = En cours : Au moins un lot attribué, évaluations/paiements incomplets
     * 2 = Terminé : Tous les lots attribués, évaluations et paiements complets
     * 3 = Clôturé : Appel d'offres clôturé définitivement
     */
    const ETAT_EN_ATTENTE = 0;
    const ETAT_EN_COURS = 1;
    const ETAT_TERMINE = 2;
    const ETAT_CLOTURE = 3;

    const ETAT_LABELS = [
        self::ETAT_EN_ATTENTE => 'En attente',
        self::ETAT_EN_COURS => 'En cours',
        self::ETAT_TERMINE => 'Terminé',
        self::ETAT_CLOTURE => 'Clôturé',
    ];

    const ETAT_COLORS = [
        self::ETAT_EN_ATTENTE => 'gray',
        self::ETAT_EN_COURS => 'blue',
        self::ETAT_TERMINE => 'green',
        self::ETAT_CLOTURE => 'purple',
    ];

    const ETAT_BADGES = [
        self::ETAT_EN_ATTENTE => 'bg-gray-100 text-gray-800',
        self::ETAT_EN_COURS => 'bg-blue-100 text-blue-800',
        self::ETAT_TERMINE => 'bg-green-100 text-green-800',
        self::ETAT_CLOTURE => 'bg-purple-100 text-purple-800',
    ];

    const ETAT_ICONS = [
        self::ETAT_EN_ATTENTE => 'clock',
        self::ETAT_EN_COURS => 'spinner fa-spin',
        self::ETAT_TERMINE => 'check-circle',
        self::ETAT_CLOTURE => 'lock',
    ];

    // États qui peuvent être modifiés automatiquement (pas Terminé ni Clôturé)
const ETATS_AUTOMATIQUES = [
    self::ETAT_EN_ATTENTE,
    self::ETAT_EN_COURS,
];


    // Relations
    public function typeAppelOffre()
    {
        return $this->belongsTo(TypeAppelOffre::class, 'type_appel_offre_id', 'id_type_appel_offre');
    }

    public function caracteristiques()
    {
        return $this->hasMany(CaracteristiqueAppelOffre::class, 'appel_offre_id', 'id_appel_offre');
    }

    public function caracteristiqueActive()
    {
        return $this->hasOne(CaracteristiqueAppelOffre::class, 'appel_offre_id', 'id_appel_offre')
            ->whereNull('parent_id')
            ->orWhereHas('versions', function ($q) {
                $q->orderBy('version_caracteristique_appel_offre', 'desc');
            })
            ->latest('version_caracteristique_appel_offre');
    }

    public function lots()
    {
        return $this->hasMany(Lot::class, 'appel_offre_id', 'id_appel_offre');
    }

    public function lotsActifs()
    {
        return $this->lots()->whereNull('parent_id')
            ->orWhere(function ($q) {
                $q->whereHas('versions', function ($query) {
                    $query->orderBy('created_at', 'desc');
                });
            });
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

    // Scopes
    public function scopeActif($query)
    {
        return $query->where('statut_evaluation_critere_appel_offre', 1);
    }




    public function scopePublie($query)
    {
        return $query->whereNotNull('date_publication_critere_appel_offre');
    }



    /**
     * Scope : Appels d'offres avec état "En attente"
     */
    public function scopeEtatEnAttente($query)
    {
        return $query->where('etat_appel_offre', self::ETAT_EN_ATTENTE);
    }

    /**
     * Scope : Appels d'offres avec état "En cours"
     */
    public function scopeEtatEnCours($query)
    {
        return $query->where('etat_appel_offre', self::ETAT_EN_COURS);
    }

    /**
     * Scope : Appels d'offres avec état "Terminé"
     */
    public function scopeEtatTermine($query)
    {
        return $query->where('etat_appel_offre', self::ETAT_TERMINE);
    }

    /**
     * Scope : Appels d'offres avec état "Clôturé"
     */
    public function scopeEtatCloture($query)
    {
        return $query->where('etat_appel_offre', self::ETAT_CLOTURE);
    }

    /**
     * Scope : Appels d'offres non clôturés (modifiables)
     */
    public function scopeEtatNonCloture($query)
    {
        return $query->where('etat_appel_offre', '!=', self::ETAT_CLOTURE);
    }

    /**
     * Scope : Appels d'offres actifs (en attente ou en cours)
     */
    public function scopeEtatActif($query)
    {
        return $query->whereIn('etat_appel_offre', [self::ETAT_EN_ATTENTE, self::ETAT_EN_COURS]);
    }







// ==================== MÉTHODES DE VÉRIFICATION DE L'ÉTAT ====================

    /**
     * Vérifier si l'appel d'offres est en état "En attente"
     */
    public function isEtatEnAttente(): bool
    {
        return $this->etat_appel_offre === self::ETAT_EN_ATTENTE;
    }

    /**
     * Vérifier si l'appel d'offres est en état "En cours"
     */
    public function isEtatEnCours(): bool
    {
        return $this->etat_appel_offre === self::ETAT_EN_COURS;
    }

    /**
     * Vérifier si l'appel d'offres est en état "Terminé"
     */
    public function isEtatTermine(): bool
    {
        return $this->etat_appel_offre === self::ETAT_TERMINE;
    }

    /**
     * Vérifier si l'appel d'offres est en état "Clôturé"
     */
    public function isEtatCloture(): bool
    {
        return $this->etat_appel_offre === self::ETAT_CLOTURE;
    }

    /**
     * Vérifier si l'appel d'offres peut être modifié (non clôturé)
     */
    public function isEtatModifiable(): bool
    {
        return !$this->isEtatCloture();
    }


    /**
 * Vérifier si l'état actuel est un état automatique (peut être modifié par le système)
 */
public function isEtatAutomatique(): bool
{
    return in_array($this->etat_appel_offre, self::ETATS_AUTOMATIQUES);
}


    // Méthodes utilitaires
    public function isActif()
    {
        return $this->statut_evaluation_critere_appel_offre == 1;
    }



    public function peutEtreCloture()
    {
        $lots = $this->lots();
    }


    public function peutEtreCloturer()
    {
        // Un appel d'offres est clôturé si tous ses lots sont clôturés

        // Si l'appel d'offres n'a pas de lots, il n'est pas clôturé
        if ($this->lots->isEmpty()) {
            return false;
        }

        // Vérifier si tous les lots sont clôturés
        return $this->lots->every(function ($lot) {

            return $lot->isCloture();
        });
    }


    public function estCloturer()
    {
        if ($this->peutEtreCloturer() && $this->etat_appel_offre == 4) return true;
        else return false;
    }









    public function getMontantTotalLotsAttribute()
    {
        return $this->lots()->sum('montant_lot');
    }


    // ==================== ACCESSEURS POUR L'ÉTAT ====================

    /**
     * Obtenir le libellé de l'état
     */
    public function getEtatLabelAttribute(): string
    {
        return self::ETAT_LABELS[$this->etat_appel_offre] ?? 'Inconnu';
    }

    /**
     * Obtenir la couleur de l'état
     */
    public function getEtatColorAttribute(): string
    {
        return self::ETAT_COLORS[$this->etat_appel_offre] ?? 'gray';
    }

    /**
     * Obtenir la classe CSS du badge pour l'état
     */
    public function getEtatBadgeClassAttribute(): string
    {
        return self::ETAT_BADGES[$this->etat_appel_offre] ?? 'bg-gray-100 text-gray-800';
    }

    /**
     * Obtenir l'icône FontAwesome pour l'état
     */
    public function getEtatIconAttribute(): string
    {
        return self::ETAT_ICONS[$this->etat_appel_offre] ?? 'question-circle';
    }

    public function genererNumero()
    {
        if ($this->typeAppelOffre) {
            return $this->typeAppelOffre->genererNumeroAppelOffre();
        }
        return null;
    }





// ==================== MÉTHODES DE CALCUL ET MISE À JOUR DE L'ÉTAT ====================

/**
 * Calculer l'état en fonction des lots, attributions, évaluations et paiements
 *
 * Logique :
 * - ETAT_EN_ATTENTE : Aucun lot OU aucun lot actif attribué
 * - ETAT_EN_COURS : Au moins un lot attribué mais pas tous les lots complets
 * - ETAT_TERMINE : Tous les lots actifs sont attribués ET complets (évaluations + paiements)
 * - ETAT_CLOTURE : Défini manuellement uniquement
 *
 * @return int L'état calculé
 */
public function calculerEtat(): int
{
    // Récupérer les lots actifs (non retirés, version actuelle)
    $lotsActifs = $this->lots()
        ->whereNull('date_retrait')
        ->whereNull('parent_id')
        ->get();

    // Si aucun lot actif → En attente
    if ($lotsActifs->isEmpty()) {
        return self::ETAT_EN_ATTENTE;
    }

    // Vérifier si au moins un lot est attribué
    foreach ($lotsActifs as $lot) {
        if ($lot->aUneAttributionActive()) {
            // Au moins un lot attribué → En cours
            return self::ETAT_EN_COURS;
        }
    }

    // Aucun lot attribué → En attente
    return self::ETAT_EN_ATTENTE;
}

/**
 * Mettre à jour automatiquement l'état de l'appel d'offres
 * À appeler après chaque modification sur les lots, attributions, évaluations ou paiements
 *
 * @param int|null $userId ID de l'utilisateur effectuant la modification
 * @return self
 */
public function mettreAJourEtat(string $userId ): self
{
    // Ne pas modifier si l'état est manuel (Terminé ou Clôturé)
    if (!$this->isEtatAutomatique()) {
        return $this;
    }

    $nouvelEtat = $this->calculerEtat();

    if ($this->etat_appel_offre !== $nouvelEtat) {
        $this->etat_appel_offre = $nouvelEtat;

        if ($userId) {
            $this->updated_by = $userId;
        }

        $this->save();
    }

    return $this;
}


/**
 * Marquer l'appel d'offres comme "Terminé" (action manuelle)
 *
 * @param int|null $userId ID de l'utilisateur
 * @return self
 */
public function terminerAppelOffre(string $userId ): self
{
    $this->etat_appel_offre = self::ETAT_TERMINE;

    if ($userId) {
        $this->updated_by = $userId;
    }

    $this->save();

    return $this;
}

/**
 * Clôturer l'appel d'offres (action manuelle)
 *
 * @param int|null $userId ID de l'utilisateur
 * @return self
 */
public function cloturerAppelOffre(string $userId): self
{
    $this->etat_appel_offre = self::ETAT_CLOTURE;

    if ($userId) {
        $this->updated_by = $userId;
    }

    $this->save();

    return $this;
}




/**
 * Réouvrir un appel d'offres clôturé (recalcule l'état réel)
 *
 * @param int|null $userId ID de l'utilisateur
 * @return self
 */
public function rouvrirAppelOffre(string $userId ): self
{
    if (!$this->isEtatCloture()) {
        return $this;
    }

    // Forcer le recalcul
    $this->etat_appel_offre = self::ETAT_EN_ATTENTE;
    $nouvelEtat = $this->calculerEtat();
    $this->etat_appel_offre = $nouvelEtat;

    if ($userId) {
        $this->updated_by = $userId;
    }

    $this->save();

    return $this;
}



/**
 * Obtenir les statistiques détaillées des lots pour cet appel d'offres
 *
 * @return array
 */
public function getStatistiquesLots(): array
{
    $lotsActifs = $this->lots()
        ->whereNull('date_retrait')
        ->whereNull('parent_id')
        ->get();

    $stats = [
        'total_lots' => $lotsActifs->count(),
        'lots_attribues' => 0,
        'lots_non_attribues' => 0,
        'lots_complets' => 0,
        'lots_en_cours' => 0,
        'pourcentage_attribution' => 0,
        'pourcentage_completion' => 0,
    ];

    foreach ($lotsActifs as $lot) {
        if ($lot->aUneAttributionActive()) {
            $stats['lots_attribues']++;

            if ($lot->isCloture()) {
                $stats['lots_complets']++;
            } else {
                $stats['lots_en_cours']++;
            }
        } else {
            $stats['lots_non_attribues']++;
        }
    }

    if ($stats['total_lots'] > 0) {
        $stats['pourcentage_attribution'] = round(($stats['lots_attribues'] / $stats['total_lots']) * 100, 1);
        $stats['pourcentage_completion'] = round(($stats['lots_complets'] / $stats['total_lots']) * 100, 1);
    }

    return $stats;
}

/**
 * Obtenir un résumé de l'état avec les statistiques
 *
 * @return array
 */
public function getResumeEtat(): array
{
    $stats = $this->getStatistiquesLots();

    return [
        'etat' => $this->etat_appel_offre,
        'etat_label' => $this->etat_label,
        'etat_color' => $this->etat_color,
        'etat_badge_class' => $this->etat_badge_class,
        'etat_icon' => $this->etat_icon,
        'est_etat_automatique' => $this->isEtatAutomatique(),
        'peut_etre_cloture' => $this->peutEtreCloturer(),
        'est_modifiable' => $this->isEtatModifiable(),
        'statistiques' => $stats,
    ];
}

/**
 * Vérifier si l'appel d'offres peut être marqué comme "Terminé"
 * (tous les lots attribués et complets)
 *
 * @return bool
 */
public function peutEtreMarqueTermine(): bool
{
    $stats = $this->getStatistiquesLots();

    // Au moins un lot et tous les lots sont complets
    return $stats['total_lots'] > 0
        && $stats['lots_attribues'] === $stats['total_lots']
        && $stats['lots_complets'] === $stats['total_lots'];
}











}
