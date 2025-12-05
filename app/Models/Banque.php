<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// ============================================
// MODÈLE BANQUE
// ============================================
class Banque extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'banques';
    protected $primaryKey = 'id_banque';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'prestataire_id', 'nom_banque', 'code_banque', 'numero_compte_banque',
        'code_guichet_banque', 'cle_rib_banque', 'iban_banque', 'swift_bic_banque',
        'titulaire_compte_banque', 'actif_banque', 'created_by', 'updated_by', 'deleted_by',
    ];

    protected $casts = ['actif_banque' => 'boolean'];

    public function prestataire()
    {
        return $this->belongsTo(Prestataire::class, 'prestataire_id', 'id_prestataire');
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class, 'banque_id', 'id_banque');
    }

    public function scopeActif($query)
    {
        return $query->where('actif_banque', true);
    }
}

