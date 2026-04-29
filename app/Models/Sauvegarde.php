<?php
// app/Models/Sauvegarde.php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Sauvegarde extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'nom_fichier',
        'chemin_stockage',
        'type',
        'statut',
        'taille_octets',
        'checksum_md5',
        'tables_incluses',
        'message_erreur',
        'expire_a',
        'creee_par_id',
        'ip_declencheur',
    ];

    protected $casts = [
        'taille_octets'   => 'integer',
        'tables_incluses' => 'array',
        'expire_a'        => 'datetime',
    ];

    // =========================================================================
    // CONSTANTES
    // =========================================================================

    const STATUT_EN_COURS = 'en_cours';
    const STATUT_TERMINEE = 'terminee';
    const STATUT_ECHEC    = 'echec';

    const TYPE_MANUELLE    = 'manuelle';
    const TYPE_AUTOMATIQUE = 'automatique';

    // Durée de rétention par défaut : 30 jours
    const RETENTION_JOURS_DEFAUT = 30;

    // =========================================================================
    // RELATIONS
    // =========================================================================

    public function creeePar()
    {
        return $this->belongsTo(User::class, 'creee_par_id')->withTrashed();
    }

    // =========================================================================
    // ACCESSEURS
    // =========================================================================

    /**
     * Taille formatée en Ko / Mo / Go
     */
    public function getTailleFormateeAttribute(): string
    {
        $octets = $this->taille_octets ?? 0;

        if ($octets >= 1_073_741_824) {
            return round($octets / 1_073_741_824, 2) . ' Go';
        }
        if ($octets >= 1_048_576) {
            return round($octets / 1_048_576, 2) . ' Mo';
        }
        if ($octets >= 1_024) {
            return round($octets / 1_024, 2) . ' Ko';
        }

        return $octets . ' o';
    }

    /**
     * Le fichier existe-t-il physiquement ?
     */
    public function getFichierExisteAttribute(): bool
    {
        return Storage::exists($this->chemin_stockage);
    }

    /**
     * Badge CSS selon statut
     */
    public function getStatutBadgeClassAttribute(): string
    {
        return match ($this->statut) {
            self::STATUT_TERMINEE => 'bg-green-100 text-green-800',
            self::STATUT_EN_COURS => 'bg-yellow-100 text-yellow-800',
            self::STATUT_ECHEC    => 'bg-red-100 text-red-800',
            default               => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Libellé statut
     */
    public function getStatutLabelAttribute(): string
    {
        return match ($this->statut) {
            self::STATUT_TERMINEE => 'Terminée',
            self::STATUT_EN_COURS => 'En cours',
            self::STATUT_ECHEC    => 'Échec',
            default               => $this->statut,
        };
    }

    // =========================================================================
    // SCOPES
    // =========================================================================

    public function scopeTerminees($query)
    {
        return $query->where('statut', self::STATUT_TERMINEE);
    }

    public function scopeExpirees($query)
    {
        return $query->whereNotNull('expire_a')
                     ->where('expire_a', '<', now());
    }
}
