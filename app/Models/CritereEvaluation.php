<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CritereEvaluation extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'criteres_evaluations';
    protected $primaryKey = 'id_critere_evaluation';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'lot_id',
        'numero_critere_evaluation',
        'libelle_critere_evaluation',
        'description_critere_evaluation',
        'note_reference_critere_evaluation',
        'statut_critere_evaluation',
        'ordre_execution_critere_evaluation',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'note_reference_critere_evaluation' => 'decimal:2',
        'statut_critere_evaluation' => 'integer',
        'ordre_execution_critere_evaluation' => 'integer',
    ];

    // Relations
    public function lot()
    {
        return $this->belongsTo(Lot::class, 'lot_id', 'id_lot');
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
        return $query->where('statut_critere_evaluation', 1);
    }

    public function scopeInactif($query)
    {
        return $query->where('statut_critere_evaluation', 0);
    }

    public function scopeOrdonne($query)
    {
        return $query->orderBy('ordre_execution_critere_evaluation', 'asc');
    }

    public function scopeByLot($query, $lotId)
    {
        return $query->where('lot_id', $lotId);
    }

    // Méthodes utilitaires
    public function isActif()
    {
        return $this->statut_critere_evaluation == 1;
    }

    public function activer()
    {
        $this->statut_critere_evaluation = 1;
        $this->save();
        return $this;
    }

    public function desactiver()
    {
        $this->statut_critere_evaluation = 0;
        $this->save();
        return $this;
    }

    public function calculerNoteSur($noteObtenue, $noteMaximale = 100)
    {
        if ($this->note_reference_critere_evaluation && $noteMaximale > 0) {
            return ($noteObtenue / $noteMaximale) * $this->note_reference_critere_evaluation;
        }
        return 0;
    }

    public static function genererNumeroCritere($lotId)
    {
        $dernier = self::where('lot_id', $lotId)->count();
        return 'CRIT-' . str_pad($dernier + 1, 3, '0', STR_PAD_LEFT);
    }

    public function reordonner($nouvelOrdre)
    {
        // Décaler les autres critères si nécessaire
        if ($this->ordre_execution_critere_evaluation !== $nouvelOrdre) {
            $ancienOrdre = $this->ordre_execution_critere_evaluation;

            if ($nouvelOrdre > $ancienOrdre) {
                // Déplacement vers le bas
                self::where('lot_id', $this->lot_id)
                    ->whereBetween('ordre_execution_critere_evaluation', [$ancienOrdre + 1, $nouvelOrdre])
                    ->decrement('ordre_execution_critere_evaluation');
            } else {
                // Déplacement vers le haut
                self::where('lot_id', $this->lot_id)
                    ->whereBetween('ordre_execution_critere_evaluation', [$nouvelOrdre, $ancienOrdre - 1])
                    ->increment('ordre_execution_critere_evaluation');
            }

            $this->ordre_execution_critere_evaluation = $nouvelOrdre;
            $this->save();
        }

        return $this;
    }

    public function getPourcentageNoteAttribute()
    {
        $totalNotes = self::where('lot_id', $this->lot_id)
                         ->sum('note_reference_critere_evaluation');

        if ($totalNotes > 0) {
            return ($this->note_reference_critere_evaluation / $totalNotes) * 100;
        }

        return 0;
    }
}
