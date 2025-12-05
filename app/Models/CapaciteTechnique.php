<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


// ============================================
// MODÈLE CAPACITÉ TECHNIQUE
// ============================================
class CapaciteTechnique extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'capacites_techniques';
    protected $primaryKey = 'id_capacite_technique';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'prestataire_id', 'effectif_permanent_capacite_technique',
        'effectif_temporaire_capacite_technique', 'moyens_materiels_capacite_technique',
        'certifications_capacite_technique', 'agrements_capacite_technique',
        'references_capacite_technique', 'competences_cles_capacite_technique',
        'domaines_expertise_capacite_technique',
        'created_by', 'updated_by', 'deleted_by',
    ];

    protected $casts = [
        'effectif_permanent_capacite_technique' => 'integer',
        'effectif_temporaire_capacite_technique' => 'integer',
    ];

    public function prestataire()
    {
        return $this->belongsTo(Prestataire::class, 'prestataire_id', 'id_prestataire');
    }

    public function getEffectifTotalAttribute()
    {
        return $this->effectif_permanent_capacite_technique +
               $this->effectif_temporaire_capacite_technique;
    }
}

