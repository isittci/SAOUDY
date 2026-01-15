<?php

namespace App\Models;

use App\Traits\HasPermissions;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasUuids, SoftDeletes, Auditable, HasPermissions;

    /**
     * Statuts des utilisateurs.
     */
    const STATUT_ACTIF = 1;
    const STATUT_INACTIF = 0;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nom_complet',
        'email',
        'password',
        'telephone_principal',
        'telephone_secondaire',
        'role_id',
        'statut',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'statut' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Relation avec le rôle.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Obtient les initiales de l'utilisateur.
     *
     * @return string
     */
    public function getInitialsAttribute(): string
    {
        $parts = explode(' ', $this->nom_complet);
        
        if (count($parts) >= 2) {
            return strtoupper(substr($parts[0], 0, 1) . substr($parts[1], 0, 1));
        }
        
        return strtoupper(substr($this->nom_complet, 0, 2));
    }

    /**
     * Obtient le statut formaté.
     *
     * @return string
     */
    public function getStatutLabelAttribute(): string
    {
        return $this->statut === self::STATUT_ACTIF ? 'Actif' : 'Inactif';
    }

    /**
     * Vérifie si l'utilisateur est actif.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->statut === self::STATUT_ACTIF;
    }

    /**
     * Vérifie si l'utilisateur est inactif.
     *
     * @return bool
     */
    public function isInactive(): bool
    {
        return $this->statut === self::STATUT_INACTIF;
    }

    /**
     * Vérifie si l'utilisateur a vérifié son email.
     *
     * @return bool
     */
    public function hasVerifiedEmail(): bool
    {
        return !is_null($this->email_verified_at);
    }

    /**
     * Active l'utilisateur.
     *
     * @return bool
     */
    public function activate(): bool
    {
        return $this->update(['statut' => self::STATUT_ACTIF]);
    }

    /**
     * Désactive l'utilisateur.
     *
     * @return bool
     */
    public function deactivate(): bool
    {
        return $this->update(['statut' => self::STATUT_INACTIF]);
    }

    /**
     * Bascule le statut de l'utilisateur.
     *
     * @return bool
     */
    public function toggleStatus(): bool
    {
        $newStatus = $this->statut === self::STATUT_ACTIF 
            ? self::STATUT_INACTIF 
            : self::STATUT_ACTIF;
            
        return $this->update(['statut' => $newStatus]);
    }

    /**
     * Vérifie si l'utilisateur peut être supprimé.
     *
     * @return bool
     */
    public function canBeDeleted(): bool
    {
        // Un utilisateur ne peut pas se supprimer lui-même
        if ($this->id === auth()->id()) {
            return false;
        }

        // Les Super Admin ne peuvent être supprimés que par d'autres Super Admin
        if ($this->isSuperAdmin() && !auth()->user()->isSuperAdmin()) {
            return false;
        }

        return true;
    }

    /**
     * Vérifie si l'utilisateur peut être restauré.
     *
     * @return bool
     */
    public function canBeRestored(): bool
    {
        return $this->trashed();
    }

    /**
     * Scope pour filtrer les utilisateurs actifs.
     */
    public function scopeActif($query)
    {
        return $query->where('statut', self::STATUT_ACTIF);
    }

    /**
     * Scope pour filtrer les utilisateurs inactifs.
     */
    public function scopeInactif($query)
    {
        return $query->where('statut', self::STATUT_INACTIF);
    }

    /**
     * Scope pour filtrer par rôle.
     */
    public function scopeByRole($query, $roleId)
    {
        return $query->where('role_id', $roleId);
    }

    /**
     * Scope pour rechercher des utilisateurs.
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('nom_complet', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('telephone_principal', 'like', "%{$search}%")
                ->orWhere('telephone_secondaire', 'like', "%{$search}%");
        });
    }

    /**
     * Scope pour filtrer les utilisateurs que l'utilisateur connecté peut voir.
     */
    public function scopeViewable($query)
    {
        $user = auth()->user();

        // Super Admin peut tout voir
        if ($user->isSuperAdmin()) {
            return $query;
        }

        // Les autres peuvent voir les utilisateurs de niveau inférieur ou égal
        return $query->whereHas('role', function ($q) use ($user) {
            $q->where('level', '<=', $user->role->level);
        });
    }

    /**
     * Scope pour filtrer les utilisateurs que l'utilisateur connecté peut gérer.
     */
    public function scopeManageable($query)
    {
        $user = auth()->user();

        // Super Admin peut tout gérer
        if ($user->isSuperAdmin()) {
            return $query;
        }

        // Les autres peuvent gérer les utilisateurs de niveau strictement inférieur
        return $query->whereHas('role', function ($q) use ($user) {
            $q->where('level', '<', $user->role->level);
        });
    }

    /**
     * Scope pour exclure l'utilisateur connecté.
     */
    public function scopeExceptCurrent($query)
    {
        return $query->where('id', '!=', auth()->id());
    }

    /**
     * Scope pour ordonner par nom.
     */
    public function scopeOrderedByName($query)
    {
        return $query->orderBy('nom_complet');
    }

    /**
     * Scope pour les utilisateurs vérifiés.
     */
    public function scopeVerified($query)
    {
        return $query->whereNotNull('email_verified_at');
    }

    /**
     * Scope pour les utilisateurs non vérifiés.
     */
    public function scopeUnverified($query)
    {
        return $query->whereNull('email_verified_at');
    }
}
