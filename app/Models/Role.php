<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'roles';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The data type of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

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
        'level' => 'integer',
        'is_system_role' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [];

    /**
     * Get the users for the role.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'role_id', 'id');
    }

    /**
     * Get the permissions for the role.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permissions', 'role_id', 'permission_id')
            ->using(RolePermission::class)
            ->withPivot([
                'attribue_par',
                'attribue_le',
                'expire_le',
                'actif',
                'conditions',
                'notes',
                'created_by',
                'updated_by',
                'deleted_by'
            ])
            ->withTimestamps();
    }

    /**
     * Get only active permissions for the role.
     */
    public function activePermissions(): BelongsToMany
    {
        return $this->permissions()
            ->wherePivot('actif', true)
            ->where(function ($query) {
                $query->whereNull('role_permissions.expire_le')->orWhere('role_permissions.expire_le', '>', now());
            })
            ->where('permissions.is_active', true);
    }

    /**
     * Scope a query to only include system roles.
     */
    public function scopeSystemRoles($query)
    {
        return $query->where('is_system_role', true);
    }

    /**
     * Scope a query to only include custom roles.
     */
    public function scopeCustomRoles($query)
    {
        return $query->where('is_system_role', false);
    }

    /**
     * Scope a query to filter by level.
     */
    public function scopeByLevel($query, int $level)
    {
        return $query->where('level', $level);
    }

    /**
     * Scope a query to filter by minimum level.
     */
    public function scopeMinLevel($query, int $level)
    {
        return $query->where('level', '>=', $level);
    }

    /**
     * Check if the role is a system role.
     */
    public function isSystemRole(): bool
    {
        return $this->is_system_role;
    }

    /**
     * Check if role has a specific permission.
     */
    public function hasPermission(string $permissionSlug): bool
    {
        return $this->activePermissions()
            ->where('permissions.slug', $permissionSlug)
            ->exists();
    }

    /**
     * Check if role has any of the given permissions.
     */
    public function hasAnyPermission(array $permissionSlugs): bool
    {
        return $this->activePermissions()
            ->whereIn('permissions.slug', $permissionSlugs)
            ->exists();
    }

    /**
     * Check if role has all of the given permissions.
     */
    public function hasAllPermissions(array $permissionSlugs): bool
    {
        $count = $this->activePermissions()
            ->whereIn('permissions.slug', $permissionSlugs)
            ->count();

        return $count === count($permissionSlugs);
    }

    /**
     * Assign a permission to the role.
     */
    public function givePermissionTo($permission, array $pivotData = []): void
    {
        $permissionId = $permission instanceof Permission ? $permission->id : $permission;

        $defaultPivotData = [
            'attribue_par' => auth()->id(),
            'attribue_le' => now(),
            'actif' => true,
        ];

        $this->permissions()->attach($permissionId, array_merge($defaultPivotData, $pivotData));
    }

    /**
     * Revoke a permission from the role.
     */
    public function revokePermissionTo($permission): void
    {
        $permissionId = $permission instanceof Permission ? $permission->id : $permission;
        $this->permissions()->detach($permissionId);
    }

    /**
     * Sync permissions for the role.
     */
    public function syncPermissions(array $permissions): void
    {
        $permissionsData = [];

        foreach ($permissions as $permissionId) {
            $permissionsData[$permissionId] = [
                'attribue_par' => auth()->id(),
                'attribue_le' => now(),
                'actif' => true,
            ];
        }

        $this->permissions()->sync($permissionsData);
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Générer automatiquement le slug si non fourni
        static::creating(function ($role) {
            if (empty($role->slug)) {
                $role->slug = Str::slug($role->name);
            }
        });

        // Empêcher la suppression des rôles système
        static::deleting(function ($role) {
            if ($role->is_system_role) {
                throw new \Exception('Les rôles système ne peuvent pas être supprimés.');
            }
        });
    }
}
