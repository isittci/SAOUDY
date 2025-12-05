<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// ============================================
// MODÈLE DOCUMENT
// ============================================
class Document extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'documents';
    protected $primaryKey = 'id_document';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'lot_id', 'type_document', 'titre_document', 'fichier_nom_document',
        'fichier_path_document', 'fichier_type_document', 'fichier_taille_document',
        'description_document', 'date_document', 'version_document',
        'est_valide_document', 'valide_par', 'valide_at',
        'created_by', 'updated_by', 'deleted_by',
    ];

    protected $casts = [
        'fichier_taille_document' => 'decimal:2',
        'date_document' => 'datetime',
        'version_document' => 'integer',
        'est_valide_document' => 'boolean',
        'valide_at' => 'datetime',
    ];

    public function lot()
    {
        return $this->belongsTo(Lot::class, 'lot_id', 'id_lot');
    }

    public function validateur()
    {
        return $this->belongsTo(User::class, 'valide_par');
    }

    public function valider($userId)
    {
        $this->est_valide_document = true;
        $this->valide_par = $userId;
        $this->valide_at = now();
        $this->save();
        return $this;
    }
}
