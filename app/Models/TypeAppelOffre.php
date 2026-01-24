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

    public function scopeVersionActuelle($query)
    {

        return $query->whereNull('parent_id')->where('actif_type_appel_offre', 1)
            ->orWhereDoesntHave('versions');
    }

    /**
 * Scope pour obtenir uniquement les versions actives
 */
public function scopeVersionActive($query)
{
    return $query->where('actif_type_appel_offre', true);
}

/**
 * Scope pour obtenir les types parents (originaux)
 */
public function scopeParentsOnly($query)
{
    return $query->whereNull('parent_id');
}

/**
 * Obtenir la version active de ce type
 */
public function getVersionActiveAttribute()
{
    if ($this->actif_type_appel_offre) {
        return $this;
    }

    return $this->versions()->where('actif_type_appel_offre', true)->first()
        ?? $this->parent?->versions()->where('actif_type_appel_offre', true)->first();
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
        return $montant >= $this->valeur_minimuim_type_appel_offre && $montant <= $this->valeur_maximuim_type_appel_offre;
    }

    public function genererNumeroAppelOffre($annee = null)
    {
        $annee = $annee ?? date('Y');
        $dernier = AppelOffre::where('type_appel_offre_id', $this->id_type_appel_offre)->whereYear('created_at', $annee)->count();

        $sequence = str_pad($dernier + 1, 3, '0', STR_PAD_LEFT);
        return "{$this->code_type_appel_offre}-{$annee}-{$sequence}";
    }


    /**
     * Crée une nouvelle version de la caractéristique
     */
    public function creerNouvelleVersion(array $donnees, $motif = null)
    {

        // Désactiver la version actuelle
        $this->actif_type_appel_offre  = false;
        $this->save();


        // Créer la nouvelle version
        $nouvelleVersion = $this->replicate();
        $nouvelleVersion->parent_id = $this->id_type_appel_offre;
        $nouvelleVersion->version_type_appel_offre = $this->getProchainNumeroVersion();
        $nouvelleVersion->motif_modification_type_appel_offre = $motif;
        $nouvelleVersion->actif_type_appel_offre = true;


        // Appliquer les nouvelles données
        foreach ($donnees as $cle => $valeur) {
            if (in_array($cle, $this->fillable)) {
                $nouvelleVersion->$cle = $valeur;
            }
        }

        // La durée sera calculée automatiquement via l'événement creating
        $nouvelleVersion->save();
        return $nouvelleVersion;
    }


    /**
     * Obtient le prochain numéro de version
     */
    public function getProchainNumeroVersion()
    {
        if ($this->parent_id) {
            $parent = $this->parent;
            $derniereVersion = $parent->versions()->max('version_type_appel_offre');
        } else {
            $derniereVersion = $this->versions()->max('version_type_appel_offre');
        }

        return ($derniereVersion ?? $this->version_type_appel_offre) + 1;
    }


    public function parent()
    {
        return $this->belongsTo(TypeAppelOffre::class, 'parent_id', 'id_type_appel_offre');
    }

    public function versions()
    {
        return $this->hasMany(TypeAppelOffre::class, 'parent_id', 'id_type_appel_offre')->orderBy('created_at', 'desc')
            /*->orderBy('version_type_appel_offre', 'desc')*/;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($typeAppelOffre) {
            // Générer le code automatiquement si non fourni
            if (empty($typeAppelOffre->code_type_appel_offre)) {
                $typeAppelOffre->code_type_appel_offre = self::genererCodeTypeAppelOffre();
            }

            // Initialiser la version à 1 pour une nouvelle création
            if (empty($typeAppelOffre->version_type_appel_offre) && empty($typeAppelOffre->parent_id)) {
                $typeAppelOffre->version_type_appel_offre = 1;
            }
        });
    }

    /**
     * Génère un code unique pour le type d'appel d'offre
     */
    public static function genererCodeTypeAppelOffre(): string
{
    $annee = date('y'); // 2 chiffres (26 au lieu de 2026)
    $prefixe = 'TAO';

    $dernierNumero = self::withTrashed()
        ->whereYear('created_at', date('Y'))
        ->whereNull('parent_id')
        ->count();

    $sequence = str_pad($dernierNumero + 1, 3, '0', STR_PAD_LEFT);

    // Format: TAO26-001 (9 caractères)
    return "{$prefixe}{$annee}-{$sequence}";
}
}
