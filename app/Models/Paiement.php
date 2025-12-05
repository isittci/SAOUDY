<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// ============================================
// MODÈLE PAIEMENT
// ============================================
class Paiement extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'paiements';
    protected $primaryKey = 'id_paiement';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'banque_id', 'montant_net_paye_paiement', 'statut_paiement',
        'date_validation_paiement', 'motif_rejet_paiement',
        'observations_paiement', 'valide_par', 'paye_par',
        'created_by', 'updated_by', 'deleted_by',
    ];

    protected $casts = [
        'montant_net_paye_paiement' => 'decimal:2',
        'statut_paiement' => 'integer',
        'date_validation_paiement' => 'datetime',
    ];

    public function banque()
    {
        return $this->belongsTo(Banque::class, 'banque_id', 'id_banque');
    }

    public function validateur()
    {
        return $this->belongsTo(User::class, 'valide_par');
    }

    public function payeur()
    {
        return $this->belongsTo(User::class, 'paye_par');
    }

    public function valider($userId)
    {
        $this->statut_paiement = 1;
        $this->valide_par = $userId;
        $this->date_validation_paiement = now();
        $this->save();
        return $this;
    }

    public function rejeter($motif, $userId)
    {
        $this->statut_paiement = 2;
        $this->motif_rejet_paiement = $motif;
        $this->valide_par = $userId;
        $this->date_validation_paiement = now();
        $this->save();
        return $this;
    }
}




