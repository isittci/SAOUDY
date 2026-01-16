<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pays extends Model
{
    use HasFactory, HasUuids;

    /**
     * La table associée au modèle
     */
    protected $table = 'pays';

    /**
     * Les attributs assignables en masse
     */
    protected $fillable = [
        'nom',
        'code_iso_2',
        'code_iso_3',
        'indicatif',
        'actif',
    ];

    /**
     * Les attributs castés
     */
    protected $casts = [
        'actif' => 'boolean',
    ];

    /**
     * Scope pour les pays actifs
     */
    public function scopeActif($query)
    {
        return $query->where('actif', true);
    }

    /**
     * Scope pour rechercher par nom
     */
    public function scopeRecherche($query, string $terme)
    {
        return $query->where('nom', 'ILIKE', "%{$terme}%")
                     ->orWhere('code_iso_2', 'ILIKE', "%{$terme}%")
                     ->orWhere('code_iso_3', 'ILIKE', "%{$terme}%");
    }

    /**
     * Récupère le pays par son code ISO alpha-2
     */
    public static function parCodeIso2(string $code): ?self
    {
        return static::where('code_iso_2', strtoupper($code))->first();
    }

    /**
     * Récupère le pays par son code ISO alpha-3
     */
    public static function parCodeIso3(string $code): ?self
    {
        return static::where('code_iso_3', strtoupper($code))->first();
    }

    /**
     * Retourne le nom formaté avec l'indicatif
     */
    public function getNomAvecIndicatifAttribute(): string
    {
        return "{$this->nom} ({$this->indicatif})";
    }

    /**
     * Retourne le format pour un select (dropdown)
     */
    public function getOptionLabelAttribute(): string
    {
        return "{$this->nom} ({$this->code_iso_2})";
    }
}
