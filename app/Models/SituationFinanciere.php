<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// ============================================
// MODÈLE SITUATION FINANCIÈRE
// ============================================
class SituationFinanciere extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'situations_financieres';
    protected $primaryKey = 'id_situation_financiere';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'prestataire_id', 'exercice_fiscal_situation_financiere',
        'chiffre_affaire_situation_financiere', 'fonds_propres_situation_financiere',
        'capacite_emprunt_situation_financiere', 'ratio_solvabilite_situation_financiere',
        'ratio_liquidite_situation_financiere', 'resultat_net_situation_financiere',
        'total_actif_situation_financiere', 'total_passif_situation_financiere',
        'observations_situation_financiere',
        'created_by', 'updated_by', 'deleted_by',
    ];

    protected $casts = [
        'chiffre_affaire_situation_financiere' => 'decimal:2',
        'fonds_propres_situation_financiere' => 'decimal:2',
        'capacite_emprunt_situation_financiere' => 'decimal:2',
        'ratio_solvabilite_situation_financiere' => 'decimal:2',
        'ratio_liquidite_situation_financiere' => 'decimal:2',
        'resultat_net_situation_financiere' => 'decimal:2',
        'total_actif_situation_financiere' => 'decimal:2',
        'total_passif_situation_financiere' => 'decimal:2',
    ];

    public function prestataire()
    {
        return $this->belongsTo(Prestataire::class, 'prestataire_id', 'id_prestataire');
    }

    public function isSolvable()
    {
        return $this->ratio_solvabilite_situation_financiere >= 1;
    }
}

