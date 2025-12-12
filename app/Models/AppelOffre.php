<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AppelOffre extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'appels_offres';
    protected $primaryKey = 'id_appel_offre';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'type_appel_offre_id',
        'numero_appel_offre',
        'libelle_critere_appel_offre',
        'objet_critere_appel_offre',
        'montant_global_appel_offre',
        'description_critere_critere_appel_offre',
        'date_publication_critere_appel_offre',
        'date_limite_depot_critere_appel_offre',
        'date_ouverture_plis_critere_appel_offre',
        'statut_evaluation_critere_appel_offre',
        'conditions_participation_critere_appel_offre',
        'criteres_selection_critere_appel_offre',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'montant_global_appel_offre' => 'decimal:2',
        'date_publication_critere_appel_offre' => 'datetime',
        'date_limite_depot_critere_appel_offre' => 'datetime',
        'date_ouverture_plis_critere_appel_offre' => 'datetime',
        'statut_evaluation_critere_appel_offre' => 'integer',
    ];

    // Relations
    public function typeAppelOffre()
    {
        return $this->belongsTo(TypeAppelOffre::class, 'type_appel_offre_id', 'id_type_appel_offre');
    }

    public function caracteristiques()
    {
        return $this->hasMany(CaracteristiqueAppelOffre::class, 'appel_offre_id', 'id_appel_offre');
    }

    public function caracteristiqueActive()
    {
        return $this->hasOne(CaracteristiqueAppelOffre::class, 'appel_offre_id', 'id_appel_offre')
            ->whereNull('parent_id')
            ->orWhereHas('versions', function ($q) {
                $q->orderBy('version_caracteristique_appel_offre', 'desc');
            })
            ->latest('version_caracteristique_appel_offre');
    }

    public function lots()
    {
        return $this->hasMany(Lot::class, 'appel_offre_id', 'id_appel_offre');
    }

    public function lotsActifs()
    {
        return $this->lots()->whereNull('parent_id')
            ->orWhere(function ($q) {
                $q->whereHas('versions', function ($query) {
                    $query->orderBy('created_at', 'desc');
                });
            });
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
        return $query->where('statut_evaluation_critere_appel_offre', 1);
    }

    public function scopeEnCours($query)
    {
        return $query->whereDate('date_limite_depot_critere_appel_offre', '>=', now());
    }

    public function scopeCloture($query)
    {
        return $query->whereDate('date_limite_depot_critere_appel_offre', '<', now());
    }

    public function scopePublie($query)
    {
        return $query->whereNotNull('date_publication_critere_appel_offre');
    }

    // Méthodes utilitaires
    public function isActif()
    {
        return $this->statut_evaluation_critere_appel_offre == 1;
    }

    public function isEnCours()
    {
        return $this->date_limite_depot_critere_appel_offre >= now();
    }

    public function isCloture()
    {
        return $this->date_limite_depot_critere_appel_offre < now();
    }

    public function joursRestants(): int
    {
        if ($this->isCloture()) {
            return 0;
        }

        $joursRestants = now()->startOfDay()->diffInDays(
            Carbon::parse($this->date_limite_depot_critere_appel_offre)->startOfDay(),
            false
        ) + 1;

        return max(0, $joursRestants);
    }

    public function getMontantTotalLotsAttribute()
    {
        return $this->lots()->sum('montant_lot');
    }

    public function genererNumero()
    {
        if ($this->typeAppelOffre) {
            return $this->typeAppelOffre->genererNumeroAppelOffre();
        }
        return null;
    }
}
