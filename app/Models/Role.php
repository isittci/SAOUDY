<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory, HasUuids, SoftDeletes, Auditable;

    protected $table = 'roles';
    /**
     * Niveaux hiérarchiques des rôles.
     */
    const LEVEL_SUPER_ADMIN = 100;
    const LEVEL_ADMIN = 80;
    const LEVEL_MANAGER = 60;
    const LEVEL_SUPERVISOR = 40;
    const LEVEL_USER = 20;
    const LEVEL_GUEST = 10;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'level',
        'is_system_role',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_system_role' => 'boolean',
        'level' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Relation avec les utilisateurs.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Relation plusieurs-à-plusieurs avec les permissions.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            Permission::class,
            'role_permissions',
            'role_id',
            'permission_id'
        )
            ->withPivot([
                'attribue_par',
                'attribue_le',
                'expire_le',
                'actif',
                'conditions',
                'notes',
                'created_by',
                'updated_by',
                'deleted_by',
                'created_at',
                'updated_at',
                'deleted_at',
            ])
            ->withTimestamps();
    }

    /**
     * Récupère les permissions actives.
     */
    public function activePermissions()
    {
        return $this->permissions()
            ->where('permissions.is_active', true)
            ->where('role_permissions.actif', true)
            ->where(function ($query) {
                $query->whereNull('role_permissions.expire_le')
                    ->orWhere('role_permissions.expire_le', '>', now());
            });
    }

    /**
     * Synchronise les permissions du rôle.
     *
     * @param array $permissions IDs des permissions
     * @param string|null $attribue_par ID de l'utilisateur
     * @return void
     */
    public function syncPermissions(array $permissions, ?string $attribue_par = null): void
    {
        $syncData = [];

        foreach ($permissions as $permissionId) {
            $syncData[$permissionId] = [
                'attribue_par' => $attribue_par ?? auth()->id(),
                'attribue_le' => now(),
                'actif' => true,
                'created_by' => auth()->id(),
                'created_at' => now(),
            ];
        }

        $this->permissions()->sync($syncData);
    }

    /**
     * Ajoute une permission au rôle.
     *
     * @param string $permissionId
     * @param array $options
     * @return void
     */
    public function givePermission(string $permissionId, array $options = []): void
    {
        $this->permissions()->attach($permissionId, array_merge([
            'attribue_par' => auth()->id(),
            'attribue_le' => now(),
            'actif' => true,
            'created_by' => auth()->id(),
            'created_at' => now(),
        ], $options));
    }

    /**
     * Retire une permission du rôle.
     *
     * @param string $permissionId
     * @return void
     */
    public function revokePermission(string $permissionId): void
    {
        $this->permissions()->detach($permissionId);
    }

    /**
     * Vérifie si le rôle possède une permission.
     *
     * @param string $slug
     * @return bool
     */
    public function hasPermission(string $slug): bool
    {
        return $this->activePermissions()
            ->where('permissions.slug', $slug)
            ->exists();
    }

    /**
     * Obtient le label du niveau.
     *
     * @return string
     */
    public function getLevelLabelAttribute(): string
    {
        return match ($this->level) {
            self::LEVEL_SUPER_ADMIN => 'Super Administrateur',
            self::LEVEL_ADMIN => 'Administrateur',
            self::LEVEL_MANAGER => 'Manager',
            self::LEVEL_SUPERVISOR => 'Superviseur',
            self::LEVEL_USER => 'Utilisateur',
            self::LEVEL_GUEST => 'Invité',
            default => 'Personnalisé',
        };
    }

    /**
     * Obtient la couleur associée au niveau.
     *
     * @return string
     */
    public function getLevelColorAttribute(): string
    {
        return match ($this->level) {
            self::LEVEL_SUPER_ADMIN => 'red',
            self::LEVEL_ADMIN => 'orange',
            self::LEVEL_MANAGER => 'blue',
            self::LEVEL_SUPERVISOR => 'green',
            self::LEVEL_USER => 'gray',
            self::LEVEL_GUEST => 'slate',
            default => 'indigo',
        };
    }

    /**
     * Vérifie si le rôle peut être modifié.
     *
     * @return bool
     */
    public function canBeEdited(): bool
    {
        // Les rôles système ne peuvent pas être modifiés
        return !$this->is_system_role;
    }

    /**
     * Vérifie si le rôle peut être supprimé.
     *
     * @return bool
     */
    public function canBeDeleted(): bool
    {
        // Les rôles système ne peuvent pas être supprimés
        if ($this->is_system_role) {
            return false;
        }

        // Les rôles avec des utilisateurs ne peuvent pas être supprimés
        return $this->users()->count() === 0;
    }

    /**
     * Compte le nombre d'utilisateurs actifs.
     *
     * @return int
     */
    public function getActiveUsersCountAttribute(): int
    {
        return $this->users()->where('statut', User::STATUT_ACTIF)->count();
    }

    /**
     * Scope pour filtrer les rôles non système.
     */
    public function scopeNonSystem($query)
    {
        return $query->where('is_system_role', false);
    }

    /**
     * Scope pour filtrer par niveau minimum.
     */
    public function scopeMinLevel($query, int $level)
    {
        return $query->where('level', '>=', $level);
    }

    /**
     * Scope pour filtrer par niveau maximum.
     */
    public function scopeMaxLevel($query, int $level)
    {
        return $query->where('level', '<=', $level);
    }
}
