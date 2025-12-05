<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TypeAppelOffre extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'types_appels_offres';
    protected $primaryKey = 'id_type_appel_offre';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'libelle_type_appel_offre',
        'code_type_appel_offre',
        'valeur_minimuim_type_appel_offre',
        'valeur_maximuim_type_appel_offre',
        'description_critere_type_appel_offre',
        'actif_type_appel_offre',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'valeur_minimuim_type_appel_offre' => 'decimal:2',
        'valeur_maximuim_type_appel_offre' => 'decimal:2',
        'actif_type_appel_offre' => 'boolean',
    ];

    // Relations
    public function appelOffres()
    {
        return $this->hasMany(AppelOffre::class, 'type_appel_offre_id', 'id_type_appel_offre');
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
        return $query->where('actif_type_appel_offre', true);
    }

    public function scopeInactif($query)
    {
        return $query->where('actif_type_appel_offre', false);
    }

    public function scopeByValeur($query, $montant)
    {
        return $query->where('valeur_minimuim_type_appel_offre', '<=', $montant)
                     ->where('valeur_maximuim_type_appel_offre', '>=', $montant);
    }

    // Méthodes utilitaires
    public function isValeurDansIntervalle($montant)
    {
        return $montant >= $this->valeur_minimuim_type_appel_offre
            && $montant <= $this->valeur_maximuim_type_appel_offre;
    }

    public function genererNumeroAppelOffre($annee = null)
    {
        $annee = $annee ?? date('Y');
        $dernier = AppelOffre::where('type_appel_offre_id', $this->id_type_appel_offre)
            ->whereYear('created_at', $annee)
            ->count();

        $sequence = str_pad($dernier + 1, 3, '0', STR_PAD_LEFT);
        return "{$this->code_type_appel_offre}-{$annee}-{$sequence}";
    }
}
