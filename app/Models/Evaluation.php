<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Modèle Evaluation adapté à la nouvelle logique:
 * - Une évaluation correspond à UN SEUL critère d'évaluation
 * - Pour un critère donné, on peut effectuer PLUSIEURS évaluations partielles
 * - La somme des resultat_evaluation doit atteindre note_reference_critere_evaluation
 * - Chaque évaluation doit avoir: responsable technique, superviseur, évaluateur
 */
class Evaluation extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'evaluations';
    protected $primaryKey = 'id_evaluation';
    protected $keyType = 'string';
    public $incrementing = false;

    // ==================== CONSTANTES STATUTS ====================
    const STATUT_EN_ATTENTE = 0;
    const STATUT_EN_COURS = 1;
    const STATUT_TERMINEE = 2;
    const STATUT_VALIDEE = 3;
    const STATUT_REJETEE = 4;

    const STATUT_LABELS = [
        self::STATUT_EN_ATTENTE => 'En attente',
        self::STATUT_EN_COURS => 'En cours',
        self::STATUT_TERMINEE => 'Terminée',
        self::STATUT_VALIDEE => 'Validée',
        self::STATUT_REJETEE => 'Rejetée',
    ];

    const STATUT_COLORS = [
        self::STATUT_EN_ATTENTE => 'gray',
        self::STATUT_EN_COURS => 'blue',
        self::STATUT_TERMINEE => 'green',
        self::STATUT_VALIDEE => 'emerald',
        self::STATUT_REJETEE => 'red',
    ];

    const STATUT_ICONS = [
        self::STATUT_EN_ATTENTE => 'clock',
        self::STATUT_EN_COURS => 'spinner',
        self::STATUT_TERMINEE => 'check-circle',
        self::STATUT_VALIDEE => 'check-double',
        self::STATUT_REJETEE => 'times-circle',
    ];

    // ==================== FILLABLE ====================
    protected $fillable = [
        'evaluation_parent_id',
        'attribution_id',
        'critere_evaluation_id', // NOUVEAU: Référence directe au critère
        'numero_evaluation',
        'version',
        'is_current',
        'date_evaluation',
        'resultat_evaluation',
        'note_maximale',
        'pourcentage_final',
        'rang',
        'respo_technique_evaluation',
        'superviseur_evaluation',
        'evalue_par',
        'statut_evaluation',
        'commentaire_general',
        'recommandation',
        'documents_evalues',
        'evaluateur_principal_id',
        'date_validation',
        'motif_validation',
        'valide_par',
        'date_rejet',
        'motif_rejet',
        'rejete_par',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    // ==================== CASTS ====================
    protected $casts = [
        'date_evaluation' => 'datetime',
        'date_validation' => 'datetime',
        'date_rejet' => 'datetime',
        'resultat_evaluation' => 'decimal:2',
        'note_maximale' => 'decimal:2',
        'pourcentage_final' => 'decimal:2',
        'statut_evaluation' => 'integer',
        'version' => 'integer',
        'rang' => 'integer',
        'is_current' => 'boolean',
        'respo_technique_evaluation' => 'array',
        'superviseur_evaluation' => 'array',
        'evalue_par' => 'array',
        'documents_evalues' => 'array',
    ];

    // ==================== ATTRIBUTS PAR DÉFAUT ====================
    protected $attributes = [
        'statut_evaluation' => 0,
        'version' => 1,
        'is_current' => true,
        'resultat_evaluation' => 0,
        'note_maximale' => 0,
        'pourcentage_final' => 0,
    ];

    // ==================== RELATIONS ====================

    /**
     * Attribution associée
     */
    public function attribution(): BelongsTo
    {
        return $this->belongsTo(PrestataireLot::class, 'attribution_id', 'id_attribution');
    }

    /**
     * Critère d'évaluation associé (NOUVELLE RELATION)
     */
    public function critereEvaluation(): BelongsTo
    {
        return $this->belongsTo(CritereEvaluation::class, 'critere_evaluation_id', 'id_critere_evaluation');
    }

    /**
     * Évaluation parente (versioning)
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'evaluation_parent_id', 'id_evaluation');
    }

    /**
     * Versions enfants
     */
    public function versions(): HasMany
    {
        return $this->hasMany(self::class, 'evaluation_parent_id', 'id_evaluation')
            ->orderBy('version', 'desc');
    }

    /**
     * Notes par critère (via table pivot) - Conservé pour compatibilité
     */
    public function notesCriteres(): HasMany
    {
        return $this->hasMany(EvaluationLotPrestataire::class, 'evaluation_id', 'id_evaluation');
    }

    /**
     * Évaluateur principal
     */
    public function evaluateurPrincipal(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluateur_principal_id', 'id');
    }

    /**
     * Validateur
     */
    public function validateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'valide_par', 'id');
    }

    /**
     * Rejeteur
     */
    public function rejeteur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejete_par', 'id');
    }

    /**
     * Créateur
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    /**
     * Modificateur
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    /**
     * Suppresseur
     */
    public function deleter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by', 'id');
    }

    // ==================== ACCESSEURS RELATIONS ====================

    /**
     * Accès au lot via l'attribution
     */
    public function getLotAttribute()
    {
        return $this->attribution?->lot;
    }

    /**
     * Accès au prestataire via l'attribution
     */
    public function getPrestataireAttribute()
    {
        return $this->attribution?->prestataire;
    }

    /**
     * Accès à l'appel d'offre via l'attribution->lot
     */
    public function getAppelOffreAttribute()
    {
        return $this->attribution?->lot?->appelOffre;
    }

    /**
     * Note de référence du critère associé
     */
    public function getNoteReferenceCritereAttribute(): float
    {
        return $this->critereEvaluation?->note_reference_critere_evaluation ?? 0;
    }

    /**
     * Libellé du critère
     */
    public function getLibelleCritereAttribute(): ?string
    {
        return $this->critereEvaluation?->libelle_critere_evaluation;
    }

    /**
     * Numéro du critère
     */
    public function getNumeroCritereAttribute(): ?string
    {
        return $this->critereEvaluation?->numero_critere_evaluation;
    }

    // ==================== ACCESSEURS STATUT ====================

    public function getStatutLabelAttribute(): string
    {
        return self::STATUT_LABELS[$this->statut_evaluation] ?? 'Inconnu';
    }

    public function getStatutColorAttribute(): string
    {
        return self::STATUT_COLORS[$this->statut_evaluation] ?? 'gray';
    }

    public function getStatutIconAttribute(): string
    {
        return self::STATUT_ICONS[$this->statut_evaluation] ?? 'question-circle';
    }

    public function getStatutBadgeClassAttribute(): string
    {
        $colors = [
            self::STATUT_EN_ATTENTE => 'bg-gray-100 text-gray-800',
            self::STATUT_EN_COURS => 'bg-blue-100 text-blue-800',
            self::STATUT_TERMINEE => 'bg-green-100 text-green-800',
            self::STATUT_VALIDEE => 'bg-emerald-100 text-emerald-800',
            self::STATUT_REJETEE => 'bg-red-100 text-red-800',
        ];
        return $colors[$this->statut_evaluation] ?? 'bg-gray-100 text-gray-800';
    }

    // ==================== ACCESSEURS RESPONSABLES ====================

    /**
     * Vérifie si le responsable technique est renseigné
     */
    public function hasRespoTechnique(): bool
    {
        $respo = $this->respo_technique_evaluation;
        return !empty($respo) && !empty($respo['nom_complet']);
    }

    /**
     * Vérifie si le superviseur est renseigné
     */
    public function hasSuperviseur(): bool
    {
        $sup = $this->superviseur_evaluation;
        return !empty($sup) && !empty($sup['nom_complet']);
    }

    /**
     * Vérifie si l'évaluateur est renseigné
     */
    public function hasEvaluePar(): bool
    {
        $eval = $this->evalue_par;
        return !empty($eval) && !empty($eval['nom_complet']);
    }

    /**
     * Vérifie si tous les responsables sont renseignés
     */
    public function hasAllResponsables(): bool
    {
        return $this->hasRespoTechnique() && $this->hasSuperviseur() && $this->hasEvaluePar();
    }

    /**
     * Liste des responsables manquants
     */
    public function getResponsablesManquantsAttribute(): array
    {
        $manquants = [];
        if (!$this->hasRespoTechnique()) {
            $manquants[] = 'Responsable technique';
        }
        if (!$this->hasSuperviseur()) {
            $manquants[] = 'Superviseur';
        }
        if (!$this->hasEvaluePar()) {
            $manquants[] = 'Évaluateur';
        }
        return $manquants;
    }

    // ==================== SCOPES ====================

    public function scopeCurrent(Builder $query): Builder
    {
        return $query->where('is_current', true);
    }

    public function scopeEnAttente(Builder $query): Builder
    {
        return $query->where('statut_evaluation', self::STATUT_EN_ATTENTE);
    }

    public function scopeEnCours(Builder $query): Builder
    {
        return $query->where('statut_evaluation', self::STATUT_EN_COURS);
    }

    public function scopeTerminee(Builder $query): Builder
    {
        return $query->where('statut_evaluation', self::STATUT_TERMINEE);
    }

    public function scopeValidee(Builder $query): Builder
    {
        return $query->where('statut_evaluation', self::STATUT_VALIDEE);
    }

    public function scopeRejetee(Builder $query): Builder
    {
        return $query->where('statut_evaluation', self::STATUT_REJETEE);
    }

    public function scopePourAttribution(Builder $query, string $attributionId): Builder
    {
        return $query->where('attribution_id', $attributionId);
    }

    /**
     * Scope pour récupérer les évaluations d'un critère spécifique
     */
    public function scopePourCritere(Builder $query, string $critereId): Builder
    {
        return $query->where('critere_evaluation_id', $critereId);
    }

    /**
     * Scope pour récupérer les évaluations d'un critère pour une attribution
     */
    public function scopePourCritereAttribution(Builder $query, string $critereId, string $attributionId): Builder
    {
        return $query->where('critere_evaluation_id', $critereId)
                     ->where('attribution_id', $attributionId);
    }

    // ==================== MÉTHODES DE VÉRIFICATION ====================

    public function isEnAttente(): bool
    {
        return $this->statut_evaluation === self::STATUT_EN_ATTENTE;
    }

    public function isEnCours(): bool
    {
        return $this->statut_evaluation === self::STATUT_EN_COURS;
    }

    public function isTerminee(): bool
    {
        return $this->statut_evaluation === self::STATUT_TERMINEE;
    }

    public function isValidee(): bool
    {
        return $this->statut_evaluation === self::STATUT_VALIDEE;
    }

    public function isRejetee(): bool
    {
        return $this->statut_evaluation === self::STATUT_REJETEE;
    }

    public function peutEtreModifiee(): bool
    {
        return in_array($this->statut_evaluation, [
            self::STATUT_EN_ATTENTE,
            self::STATUT_EN_COURS,
            self::STATUT_REJETEE
        ]);
    }

    public function peutEtreDemarree(): bool
    {
        return $this->statut_evaluation === self::STATUT_EN_ATTENTE;
    }

    /**
     * Vérifie si l'évaluation peut être terminée
     * Conditions:
     * - Statut en cours
     * - Tous les responsables renseignés
     * - La somme des évaluations pour ce critère atteint la note de référence
     */
    public function peutEtreTerminee(): bool
    {
        if ($this->statut_evaluation !== self::STATUT_EN_COURS) {
            return false;
        }

        // Vérifier que tous les responsables sont renseignés
        if (!$this->hasAllResponsables()) {
            return false;
        }

        // Vérifier que la somme des résultats atteint la note de référence
        return $this->sommeResultatsCritereAtteinte();
    }

    /**
     * Vérifie si l'évaluation peut être validée
     * Conditions:
     * - Statut terminée
     * - La somme des évaluations pour ce critère atteint la note de référence
     */
    public function peutEtreValidee(): bool
    {
        if ($this->statut_evaluation !== self::STATUT_TERMINEE) {
            return false;
        }

        return $this->sommeResultatsCritereAtteinte();
    }

    public function peutEtreRejetee(): bool
    {
        return $this->statut_evaluation === self::STATUT_TERMINEE;
    }

    /**
     * Vérifie si la somme des résultats des évaluations pour ce critère
     * atteint ou dépasse la note de référence du critère
     */
    public function sommeResultatsCritereAtteinte(): bool
    {
        $somme = $this->getSommeResultatsCritere();
        $noteReference = $this->note_reference_critere;

        return $somme >= $noteReference;
    }

    /**
     * Calcule la somme des résultats de toutes les évaluations (validées ou en cours)
     * pour le même critère et la même attribution
     */
    public function getSommeResultatsCritere(): float
    {
        if (!$this->critere_evaluation_id || !$this->attribution_id) {
            return 0;
        }

        return self::where('critere_evaluation_id', $this->critere_evaluation_id)
            ->where('attribution_id', $this->attribution_id)
            ->where('is_current', true)
            ->whereIn('statut_evaluation', [
                self::STATUT_EN_COURS,
                self::STATUT_TERMINEE,
                self::STATUT_VALIDEE
            ])
            ->sum('resultat_evaluation');
    }

    /**
     * Calcule le reste à évaluer pour ce critère
     */
    public function getResteAEvaluerAttribute(): float
    {
        $noteReference = $this->note_reference_critere;
        $somme = $this->getSommeResultatsCritere();

        return max(0, $noteReference - $somme);
    }

    /**
     * Pourcentage d'avancement pour ce critère
     */
    public function getPourcentageAvancementCritereAttribute(): float
    {
        $noteReference = $this->note_reference_critere;
        if ($noteReference <= 0) {
            return 0;
        }

        $somme = $this->getSommeResultatsCritere();
        return min(100, ($somme / $noteReference) * 100);
    }

    /**
     * Vérifie si on peut créer une nouvelle évaluation pour ce critère
     */
    public function peutCreerNouvelleEvaluationPourCritere(): bool
    {
        return !$this->sommeResultatsCritereAtteinte();
    }

    /**
     * Liste des raisons pour lesquelles on ne peut pas terminer
     */
    public function getRaisonsNonTerminableAttribute(): array
    {
        $raisons = [];

        if ($this->statut_evaluation !== self::STATUT_EN_COURS) {
            $raisons[] = "L'évaluation n'est pas en cours";
        }

        if (!$this->hasRespoTechnique()) {
            $raisons[] = "Le responsable technique n'est pas renseigné";
        }

        if (!$this->hasSuperviseur()) {
            $raisons[] = "Le superviseur n'est pas renseigné";
        }

        if (!$this->hasEvaluePar()) {
            $raisons[] = "L'évaluateur n'est pas renseigné";
        }

        if (!$this->sommeResultatsCritereAtteinte()) {
            $somme = $this->getSommeResultatsCritere();
            $noteRef = $this->note_reference_critere;
            $reste = $noteRef - $somme;
            $raisons[] = "La somme des résultats ({$somme}) n'atteint pas la note de référence ({$noteRef}). Reste: {$reste} pts";
        }

        return $raisons;
    }

    // ==================== MÉTHODES D'ACTION ====================

    /**
     * Démarrer l'évaluation
     */
    public function demarrer(?string $userId = null): bool
    {
        if (!$this->peutEtreDemarree()) {
            return false;
        }

        return $this->update([
            'statut_evaluation' => self::STATUT_EN_COURS,
            'date_evaluation' => now(),
            'evaluateur_principal_id' => $userId ?? Auth::id(),
            'updated_by' => Auth::id(),
        ]);
    }

    /**
     * Terminer l'évaluation avec vérifications
     * @return array ['success' => bool, 'message' => string, 'raisons' => array]
     */
    public function terminerAvecVerification(?string $userId = null): array
    {
        // Vérifier le statut
        if ($this->statut_evaluation !== self::STATUT_EN_COURS) {
            return [
                'success' => false,
                'message' => "L'évaluation n'est pas en cours",
                'raisons' => ["Statut actuel: {$this->statut_label}"]
            ];
        }

        // Vérifier les responsables
        if (!$this->hasAllResponsables()) {
            return [
                'success' => false,
                'message' => 'Tous les responsables doivent être renseignés',
                'raisons' => $this->responsables_manquants
            ];
        }

        // Vérifier la somme des résultats
        if (!$this->sommeResultatsCritereAtteinte()) {
            $somme = $this->getSommeResultatsCritere();
            $noteRef = $this->note_reference_critere;
            return [
                'success' => false,
                'message' => 'La somme des résultats n\'atteint pas la note de référence',
                'raisons' => [
                    "Somme actuelle: {$somme} pts",
                    "Note de référence: {$noteRef} pts",
                    "Reste à évaluer: " . ($noteRef - $somme) . " pts"
                ]
            ];
        }

        // Calculer la note finale
        $this->calculerNoteFinale();

        $updated = $this->update([
            'statut_evaluation' => self::STATUT_TERMINEE,
            'updated_by' => $userId ?? Auth::id(),
        ]);

        return [
            'success' => $updated,
            'message' => $updated ? 'Évaluation terminée avec succès' : 'Erreur lors de la terminaison',
            'raisons' => []
        ];
    }

    /**
     * Terminer l'évaluation (méthode simplifiée pour compatibilité)
     */
    public function terminer(?string $userId = null): bool
    {
        $result = $this->terminerAvecVerification($userId);
        return $result['success'];
    }

    /**
     * Valider l'évaluation avec vérifications
     * @return array ['success' => bool, 'message' => string, 'raisons' => array]
     */
    public function validerAvecVerification(string $motif = null, ?string $userId = null): array
    {
        if ($this->statut_evaluation !== self::STATUT_TERMINEE) {
            return [
                'success' => false,
                'message' => "L'évaluation doit être terminée avant d'être validée",
                'raisons' => ["Statut actuel: {$this->statut_label}"]
            ];
        }

        // Vérifier à nouveau la somme (sécurité)
        if (!$this->sommeResultatsCritereAtteinte()) {
            $somme = $this->getSommeResultatsCritere();
            $noteRef = $this->note_reference_critere;
            return [
                'success' => false,
                'message' => 'La somme des résultats n\'atteint pas la note de référence',
                'raisons' => [
                    "Somme actuelle: {$somme} pts",
                    "Note de référence: {$noteRef} pts"
                ]
            ];
        }

        $validateurId = $userId ?? Auth::id();

        $updated = $this->update([
            'statut_evaluation' => self::STATUT_VALIDEE,
            'valide_par' => $validateurId,
            'date_validation' => now(),
            'motif_validation' => $motif,
            'updated_by' => $validateurId,
        ]);

        if ($updated) {
            $this->calculerRang();
        }

        return [
            'success' => $updated,
            'message' => $updated ? 'Évaluation validée avec succès' : 'Erreur lors de la validation',
            'raisons' => []
        ];
    }

    /**
     * Valider l'évaluation (méthode simplifiée pour compatibilité)
     */
    public function valider(string $motif = null, ?string $userId = null): bool
    {
        $result = $this->validerAvecVerification($motif, $userId);
        return $result['success'];
    }

    /**
     * Rejeter l'évaluation
     */
    public function rejeter(string $motif, ?string $userId = null): bool
    {
        if (!$this->peutEtreRejetee()) {
            return false;
        }

        $rejeteurId = $userId ?? Auth::id();

        return $this->update([
            'statut_evaluation' => self::STATUT_REJETEE,
            'rejete_par' => $rejeteurId,
            'date_rejet' => now(),
            'motif_rejet' => $motif,
            'updated_by' => $rejeteurId,
        ]);
    }

    /**
     * Reprendre après rejet (remet en cours)
     */
    public function reprendre(?string $userId = null): bool
    {
        if (!$this->isRejetee()) {
            return false;
        }

        return $this->update([
            'statut_evaluation' => self::STATUT_EN_COURS,
            'updated_by' => $userId ?? Auth::id(),
        ]);
    }

    // ==================== MÉTHODES DE CALCUL ====================

    /**
     * Calculer la note finale
     */
    public function calculerNoteFinale(): void
    {
        // Pour la nouvelle logique, on utilise directement resultat_evaluation
        // et on compare avec la note de référence du critère
        $this->note_maximale = $this->note_reference_critere;

        if ($this->note_maximale > 0) {
            $this->pourcentage_final = ($this->resultat_evaluation / $this->note_maximale) * 100;
        } else {
            $this->pourcentage_final = 0;
        }

        $this->save();
    }

    /**
     * Calculer le rang parmi les évaluations du même lot/critère
     */
    public function calculerRang(): void
    {
        $lot = $this->lot;
        $critereId = $this->critere_evaluation_id;

        if (!$lot || !$critereId) {
            return;
        }

        // Récupérer toutes les évaluations validées pour ce lot et ce critère
        $evaluations = self::whereHas('attribution', function ($q) use ($lot) {
                $q->where('lot_id', $lot->id_lot);
            })
            ->where('critere_evaluation_id', $critereId)
            ->where('is_current', true)
            ->where('statut_evaluation', self::STATUT_VALIDEE)
            ->orderBy('pourcentage_final', 'desc')
            ->orderBy('resultat_evaluation', 'desc')
            ->get();

        // Attribuer les rangs
        $rang = 1;
        foreach ($evaluations as $evaluation) {
            $evaluation->update(['rang' => $rang]);
            $rang++;
        }
    }

    // ==================== MÉTHODES DE VERSIONING ====================

    /**
     * Créer une nouvelle version
     */
    public function creerNouvelleVersion(?string $userId = null): self
    {
        return DB::transaction(function () use ($userId) {
            // Désactiver la version actuelle
            $this->update([
                'is_current' => false,
                'updated_by' => $userId ?? Auth::id(),
            ]);

            // Créer la nouvelle version
            $nouvelleVersion = $this->replicate();
            $nouvelleVersion->evaluation_parent_id = $this->id_evaluation;
            $nouvelleVersion->version = $this->version + 1;
            $nouvelleVersion->is_current = true;
            $nouvelleVersion->statut_evaluation = self::STATUT_EN_COURS;
            $nouvelleVersion->date_rejet = null;
            $nouvelleVersion->motif_rejet = null;
            $nouvelleVersion->rejete_par = null;
            $nouvelleVersion->created_by = $userId ?? Auth::id();
            $nouvelleVersion->save();

            // Copier les notes des critères (pour compatibilité)
            foreach ($this->notesCriteres as $noteCritere) {
                $nouvelleNote = $noteCritere->replicate();
                $nouvelleNote->evaluation_id = $nouvelleVersion->id_evaluation;
                $nouvelleNote->created_by = $userId ?? Auth::id();
                $nouvelleNote->save();
            }

            return $nouvelleVersion;
        });
    }

    /**
     * Obtenir l'historique complet des versions
     */
    public function getHistoriqueVersions()
    {
        // Trouver la version originale
        $original = $this;
        while ($original->evaluation_parent_id) {
            $original = $original->parent;
        }

        // Récupérer toutes les versions
        return self::where('numero_evaluation', $original->numero_evaluation)
            ->with(['creator', 'validateur', 'rejeteur', 'critereEvaluation'])
            ->orderBy('version', 'asc')
            ->get();
    }

    // ==================== MÉTHODES STATIQUES ====================

    /**
     * Générer un numéro d'évaluation unique
     */
    public static function genererNumeroEvaluation(string $attributionId, ?string $critereId = null): string
    {
        $attribution = PrestataireLot::with('lot')->find($attributionId);
        $annee = date('Y');
        $lotNumero = $attribution?->lot?->numero ?? 'LOT';

        // Ajouter le numéro du critère si disponible
        $critereInfo = '';
        if ($critereId) {
            $critere = CritereEvaluation::find($critereId);
            $critereInfo = $critere?->numero_critere_evaluation ?? '';
            $critereInfo = $critereInfo ? "-{$critereInfo}" : '';
        }

        $prefix = "EVAL-{$lotNumero}{$critereInfo}-{$annee}";

        $dernierNumero = self::where('numero_evaluation', 'like', "{$prefix}-%")
            ->orderBy('numero_evaluation', 'desc')
            ->value('numero_evaluation');

        $sequence = 1;
        if ($dernierNumero) {
            $parts = explode('-', $dernierNumero);
            $sequence = (int) end($parts) + 1;
        }

        return sprintf('%s-%04d', $prefix, $sequence);
    }

    /**
     * Créer une évaluation pour une attribution et un critère spécifique
     */
    public static function creerPourAttributionCritere(
        PrestataireLot $attribution,
        CritereEvaluation $critere,
        float $resultatEvaluation = 0,
        ?array $responsables = null,
        ?string $userId = null
    ): self {
        return DB::transaction(function () use ($attribution, $critere, $resultatEvaluation, $responsables, $userId) {
            $evaluation = self::create([
                'attribution_id' => $attribution->id_attribution,
                'critere_evaluation_id' => $critere->id_critere_evaluation,
                'numero_evaluation' => self::genererNumeroEvaluation(
                    $attribution->id_attribution,
                    $critere->id_critere_evaluation
                ),
                'statut_evaluation' => self::STATUT_EN_COURS,
                'date_evaluation' => now(),
                'resultat_evaluation' => $resultatEvaluation,
                'note_maximale' => $critere->note_reference_critere_evaluation,
                'evaluateur_principal_id' => $userId ?? Auth::id(),
                'respo_technique_evaluation' => $responsables['respo_technique'] ?? null,
                'superviseur_evaluation' => $responsables['superviseur'] ?? null,
                'evalue_par' => $responsables['evalue_par'] ?? null,
                'created_by' => $userId ?? Auth::id(),
            ]);

            // Calculer le pourcentage
            $evaluation->calculerNoteFinale();

            // Créer également l'entrée dans la table pivot pour compatibilité
            EvaluationLotPrestataire::create([
                'critere_evaluation_id' => $critere->id_critere_evaluation,
                'evaluation_id' => $evaluation->id_evaluation,
                'prestataire_id' => $attribution->prestataire_id,
                'note_obtenue' => $resultatEvaluation,
                'note_reference' => $critere->note_reference_critere_evaluation,
                'note_finale' => $resultatEvaluation,
                'pourcentage' => $critere->note_reference_critere_evaluation > 0
                    ? ($resultatEvaluation / $critere->note_reference_critere_evaluation * 100)
                    : 0,
                'created_by' => $userId ?? Auth::id(),
            ]);

            return $evaluation;
        });
    }

    /**
     * Récupérer toutes les évaluations pour un critère et une attribution
     */
    public static function getEvaluationsPourCritereAttribution(string $critereId, string $attributionId)
    {
        return self::where('critere_evaluation_id', $critereId)
            ->where('attribution_id', $attributionId)
            ->where('is_current', true)
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Calculer le total évalué pour un critère et une attribution
     */
    public static function getTotalEvaluePourCritere(string $critereId, string $attributionId): float
    {
        return self::where('critere_evaluation_id', $critereId)
            ->where('attribution_id', $attributionId)
            ->where('is_current', true)
            ->whereIn('statut_evaluation', [
                self::STATUT_EN_COURS,
                self::STATUT_TERMINEE,
                self::STATUT_VALIDEE
            ])
            ->sum('resultat_evaluation');
    }

    /**
     * Vérifier si on peut créer une nouvelle évaluation pour un critère
     */
    public static function peutCreerEvaluationPourCritere(string $critereId, string $attributionId): bool
    {
        $critere = CritereEvaluation::find($critereId);
        if (!$critere) {
            return false;
        }

        $totalEvalue = self::getTotalEvaluePourCritere($critereId, $attributionId);
        return $totalEvalue < $critere->note_reference_critere_evaluation;
    }

    /**
     * Obtenir le reste à évaluer pour un critère
     */
    public static function getResteAEvaluerPourCritere(string $critereId, string $attributionId): float
    {
        $critere = CritereEvaluation::find($critereId);
        if (!$critere) {
            return 0;
        }

        $totalEvalue = self::getTotalEvaluePourCritere($critereId, $attributionId);
        return max(0, $critere->note_reference_critere_evaluation - $totalEvalue);
    }

    /**
     * Obtenir les statistiques d'évaluation pour un lot
     */
    public static function statistiquesLot(string $lotId): array
    {
        $evaluations = self::whereHas('attribution', function ($q) use ($lotId) {
                $q->where('lot_id', $lotId);
            })
            ->where('is_current', true)
            ->get();

        return [
            'total' => $evaluations->count(),
            'en_attente' => $evaluations->where('statut_evaluation', self::STATUT_EN_ATTENTE)->count(),
            'en_cours' => $evaluations->where('statut_evaluation', self::STATUT_EN_COURS)->count(),
            'terminees' => $evaluations->where('statut_evaluation', self::STATUT_TERMINEE)->count(),
            'validees' => $evaluations->where('statut_evaluation', self::STATUT_VALIDEE)->count(),
            'rejetees' => $evaluations->where('statut_evaluation', self::STATUT_REJETEE)->count(),
            'moyenne_pourcentage' => $evaluations->where('statut_evaluation', '>=', self::STATUT_TERMINEE)->avg('pourcentage_final') ?? 0,
            'meilleure_note' => $evaluations->where('statut_evaluation', '>=', self::STATUT_TERMINEE)->max('pourcentage_final') ?? 0,
        ];
    }

    /**
     * Statistiques par critère pour une attribution
     */
    public static function statistiquesCriterePourAttribution(string $attributionId): array
    {
        $attribution = PrestataireLot::with('lot.criteresEvaluation')->find($attributionId);
        if (!$attribution) {
            return [];
        }

        $stats = [];
        $criteres = $attribution->lot->criteresEvaluation()->actif()->ordonne()->get();
// dd($criteres);
        foreach ($criteres as $critere) {
            $evaluations = self::where('critere_evaluation_id', $critere->id_critere_evaluation)
                ->where('attribution_id', $attributionId)
                ->where('is_current', true)
                ->get();

            $totalEvalue = $evaluations->whereIn('statut_evaluation', [
                self::STATUT_EN_COURS,
                self::STATUT_TERMINEE,
                self::STATUT_VALIDEE
            ])->sum('resultat_evaluation');

            $noteReference = $critere->note_reference_critere_evaluation;
            $resteAEvaluer = max(0, $noteReference - $totalEvalue);
            $pourcentageComplete = $noteReference > 0 ? ($totalEvalue / $noteReference * 100) : 0;

            $stats[$critere->id_critere_evaluation] = [
                'critere' => $critere,
                'nombre_evaluations' => $evaluations->count(),
                'total_evalue' => $totalEvalue,
                'note_reference' => $noteReference,
                'reste_a_evaluer' => $resteAEvaluer,
                'pourcentage_complete' => min(100, $pourcentageComplete),
                'est_complet' => $totalEvalue >= $noteReference,
                'peut_ajouter_evaluation' => $totalEvalue < $noteReference,
                'evaluations' => $evaluations,
            ];
        }

        return $stats;
    }

    // ==================== BOOT ====================

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->created_by)) {
                $model->created_by = Auth::id();
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
