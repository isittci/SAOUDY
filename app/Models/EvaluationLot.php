<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;


// ============================================
// MODÈLE PIVOT EVALUATION-LOT
// Évaluation des prestataires par critère
// ============================================
class EvaluationLot extends Pivot
{
    use HasFactory, SoftDeletes;

    protected $table = 'evaluations_lots';

    public $incrementing = false;
    public $timestamps = true;

    // Clé composite
    protected $primaryKey = ['critere_evaluation_id', 'evaluation_id', 'prestatiare_id'];

    protected $fillable = [
        'critere_evaluation_id',
        'evaluation_id',
        'prestatiare_id',
        'note_obtenue',
        'note_sur',
        'note_finale',
        'commentaire',
        'justification',
        'documents_fournis',
        'conforme',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'note_obtenue' => 'decimal:2',
        'note_sur' => 'decimal:2',
        'note_finale' => 'decimal:2',
        'conforme' => 'boolean',
        'documents_fournis' => 'array',
    ];

    protected function setKeysForSaveQuery($query)
    {
        $keys = $this->getKeyName();
        if (!is_array($keys)) {
            return parent::setKeysForSaveQuery($query);
        }

        foreach ($keys as $keyName) {
            $query->where($keyName, '=', $this->getKeyForSaveQuery($keyName));
        }

        return $query;
    }

    protected function getKeyForSaveQuery($keyName = null)
    {
        if (is_null($keyName)) {
            $keyName = $this->getKeyName();
        }

        if (isset($this->original[$keyName])) {
            return $this->original[$keyName];
        }

        return $this->getAttribute($keyName);
    }

    // Relations
    public function critereEvaluation()
    {
        return $this->belongsTo(CritereEvaluation::class, 'critere_evaluation_id', 'id_critere_evaluation');
    }

    public function evaluation()
    {
        return $this->belongsTo(Evaluation::class, 'evaluation_id', 'id_evaluation');
    }

    public function prestataire()
    {
        return $this->belongsTo(Prestataire::class, 'prestatiare_id', 'id_prestataire');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopeConforme($query)
    {
        return $query->where('conforme', true);
    }

    public function scopeNonConforme($query)
    {
        return $query->where('conforme', false);
    }

    // Méthodes utilitaires
    public function calculerNoteFinale()
    {
        if ($this->note_sur && $this->note_sur > 0 && $this->critereEvaluation) {
            $pourcentage = ($this->note_obtenue / $this->note_sur) * 100;
            $this->note_finale = ($pourcentage / 100) * $this->critereEvaluation->note_reference_critere_evaluation;
        } else {
            $this->note_finale = 0;
        }

        $this->save();
        return $this->note_finale;
    }

    public function attribuerNote($noteObtenue, $noteSur, $commentaire = null, $userId = null)
    {
        $this->note_obtenue = $noteObtenue;
        $this->note_sur = $noteSur;
        $this->commentaire = $commentaire;
        $this->updated_by = $userId;

        $this->calculerNoteFinale();

        return $this;
    }

    public function marquerConforme($conforme = true, $justification = null, $userId = null)
    {
        $this->conforme = $conforme;
        $this->justification = $justification;
        $this->updated_by = $userId;
        $this->save();

        return $this;
    }
}
