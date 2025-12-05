<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// ============================================
// MODÈLE ÉVALUATION PRESTATAIRE
// ============================================
class EvaluationPrestataire extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'evaluations_prestataires';
    protected $primaryKey = 'id_evaluation_prestataire';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'prestataire_id', 'note_qualification_evaluation_prestataire',
        'date_derniere_evaluation_evaluation_prestataire',
        'nombre_contrats_executes_evaluation_prestataire',
        'taux_respect_delais_evaluation_prestataire',
        'taux_qualite_evaluation_prestataire',
        'nombre_litiges_evaluation_prestataire',
        'liste_statut_evaluation_prestataire',
        'date_mise_en_liste_evaluation_prestataire',
        'date_fin_sanction_evaluation_prestataire',
        'motif_liste_noire_evaluation_prestataire',
        'commentaire_evaluation_prestataire',
        'created_by', 'updated_by', 'deleted_by',
    ];

    protected $casts = [
        'note_qualification_evaluation_prestataire' => 'decimal:2',
        'date_derniere_evaluation_evaluation_prestataire' => 'datetime',
        'nombre_contrats_executes_evaluation_prestataire' => 'decimal:2',
        'taux_respect_delais_evaluation_prestataire' => 'decimal:2',
        'taux_qualite_evaluation_prestataire' => 'decimal:2',
        'nombre_litiges_evaluation_prestataire' => 'decimal:2',
        'date_mise_en_liste_evaluation_prestataire' => 'datetime',
        'date_fin_sanction_evaluation_prestataire' => 'datetime',
    ];

    public function prestataire()
    {
        return $this->belongsTo(Prestataire::class, 'prestataire_id', 'id_prestataire');
    }

    public function mettreEnListeNoire($motif, $dureeJours = 365)
    {
        $this->liste_statut_evaluation_prestataire = 'liste_noire';
        $this->motif_liste_noire_evaluation_prestataire = $motif;
        $this->date_mise_en_liste_evaluation_prestataire = now();
        $this->date_fin_sanction_evaluation_prestataire = now()->addDays($dureeJours);
        $this->save();
        return $this;
    }
}

