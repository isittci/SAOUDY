<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class EvaluationLotPrestataire extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'evaluations_lots_prestataires';
    protected $primaryKey = 'id_evaluation_critere';
    protected $keyType = 'string';
    public $incrementing = false;

    // ==================== FILLABLE ====================
    protected $fillable = [
        'critere_evaluation_id',
        'evaluation_id',
        'prestataire_id',
        'note_obtenue',
        'note_reference',
        'note_finale',
        'pourcentage',
        'conforme',
        'observation',
        'justification',
        'documents_fournis',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    // ==================== CASTS ====================
    protected $casts = [
        'note_obtenue' => 'decimal:2',
        'note_reference' => 'decimal:2',
        'note_finale' => 'decimal:2',
        'pourcentage' => 'decimal:2',
        'conforme' => 'boolean',
        'documents_fournis' => 'array',
    ];

    // ==================== ATTRIBUTS PAR DÉFAUT ====================
    protected $attributes = [
        'note_obtenue' => 0,
        'note_reference' => 0,
        'note_finale' => 0,
        'pourcentage' => 0,
        'conforme' => false,
    ];

    // ==================== RELATIONS ====================

    /**
     * Critère d'évaluation
     */
    public function critereEvaluation(): BelongsTo
    {
        return $this->belongsTo(CritereEvaluation::class, 'critere_evaluation_id', 'id_critere_evaluation');
    }

    /**
     * Évaluation parente
     */
    public function evaluation(): BelongsTo
    {
        return $this->belongsTo(Evaluation::class, 'evaluation_id', 'id_evaluation');
    }

    /**
     * Prestataire
     */
    public function prestataire(): BelongsTo
    {
        return $this->belongsTo(Prestataire::class, 'prestataire_id', 'id_prestataire');
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

    // ==================== ACCESSEURS ====================

    /**
     * Accès au lot via le critère
     */
    public function getLotAttribute()
    {
        return $this->critereEvaluation?->lot;
    }

    /**
     * Badge de conformité
     */
    public function getConformeBadgeClassAttribute(): string
    {
        return $this->conforme
            ? 'bg-green-100 text-green-800'
            : 'bg-red-100 text-red-800';
    }

    /**
     * Label de conformité
     */
    public function getConformeLabelAttribute(): string
    {
        return $this->conforme ? 'Conforme' : 'Non conforme';
    }

    /**
     * Couleur basée sur le pourcentage
     */
    public function getPourcentageColorAttribute(): string
    {
        if ($this->pourcentage >= 80) {
            return 'text-green-600';
        } elseif ($this->pourcentage >= 60) {
            return 'text-blue-600';
        } elseif ($this->pourcentage >= 40) {
            return 'text-yellow-600';
        } else {
            return 'text-red-600';
        }
    }

    // ==================== MÉTHODES ====================

    /**
     * Calculer les notes (finale et pourcentage)
     */
    public function calculerNotes(): void
    {
        // Note finale = note obtenue (peut être modifié selon logique métier)
        $this->note_finale = $this->note_obtenue;

        // Pourcentage
        if ($this->note_reference > 0) {
            $this->pourcentage = ($this->note_obtenue / $this->note_reference) * 100;
        } else {
            $this->pourcentage = 0;
        }

        $this->save();
    }

    /**
     * Mettre à jour la note
     */
    public function mettreAJourNote(
        float $noteObtenue,
        ?string $observation = null,
        ?string $justification = null,
        bool $conforme = false,
        ?string $userId = null
    ): bool {
        $updated = $this->update([
            'note_obtenue' => $noteObtenue,
            'observation' => $observation,
            'justification' => $justification,
            'conforme' => $conforme,
            'updated_by' => $userId ?? Auth::id(),
        ]);

        if ($updated) {
            $this->calculerNotes();

            // Recalculer la note de l'évaluation parente
            $this->evaluation->calculerNoteFinale();
        }

        return $updated;
    }

    /**
     * Marquer comme conforme
     */
    public function marquerConforme(?string $justification = null, ?string $userId = null): bool
    {
        return $this->update([
            'conforme' => true,
            'justification' => $justification ?? $this->justification,
            'updated_by' => $userId ?? Auth::id(),
        ]);
    }

    /**
     * Marquer comme non conforme
     */
    public function marquerNonConforme(string $justification, ?string $userId = null): bool
    {
        return $this->update([
            'conforme' => false,
            'justification' => $justification,
            'updated_by' => $userId ?? Auth::id(),
        ]);
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

        // static::saved(function ($model) {
        //     // Recalculer automatiquement les notes après sauvegarde
        //     if ($model->wasChanged(['note_obtenue'])) {
        //         $model->calculerNotes();
        //     }
        // });
    }
}
