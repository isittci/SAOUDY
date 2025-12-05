<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Permission extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'permissions';

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
     * Actions disponibles pour les permissions.
     */
    const ACTION_CREATE = 'create';
    const ACTION_READ = 'read';
    const ACTION_UPDATE = 'update';
    const ACTION_DELETE = 'delete';
    const ACTION_EXPORT = 'export';
    const ACTION_IMPORT = 'import';
    const ACTION_VALIDATE = 'validate';
    const ACTION_REJECT = 'reject';
    const ACTION_RESTORE = 'restore';
    const ACTION_MANAGE = 'manage';
    const ACTION_DOWNLOAD = 'download';
    const ACTION_DUPLICATE = 'duplicate';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'resource',
        'action',
        'guard_name',
        'category',
        'priority',
        'is_active',
        'is_system',
        'conditions',
        'created_by',
        'updated_by',
        'last_used_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'is_system' => 'boolean',
        'conditions' => 'array',
        'priority' => 'integer',
        'last_used_at' => 'datetime',
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
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['full_name'];

    /**
     * Get the roles that have this permission.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_permissions', 'permission_id', 'role_id')
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
     * Get the user who created this permission.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    /**
     * Get the user who last updated this permission.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    /**
     * Scope a query to only include active permissions.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include inactive permissions.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope a query to only include system permissions.
     */
    public function scopeSystem($query)
    {
        return $query->where('is_system', true);
    }

    /**
     * Scope a query to only include custom permissions.
     */
    public function scopeCustom($query)
    {
        return $query->where('is_system', false);
    }

    /**
     * Scope a query to filter by resource.
     */
    public function scopeByResource($query, string $resource)
    {
        return $query->where('resource', $resource);
    }

    /**
     * Scope a query to filter by action.
     */
    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope a query to filter by guard name.
     */
    public function scopeByGuard($query, string $guard)
    {
        return $query->where('guard_name', $guard);
    }

    /**
     * Scope a query to filter by category.
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope a query to filter by resource and action.
     */
    public function scopeByResourceAndAction($query, string $resource, string $action)
    {
        return $query->where('resource', $resource)->where('action', $action);
    }

    /**
     * Scope a query to search permissions.
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('slug', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('resource', 'like', "%{$search}%");
        });
    }

    /**
     * Scope to order by priority.
     */
    public function scopeOrderByPriority($query, string $direction = 'desc')
    {
        return $query->orderBy('priority', $direction);
    }

    /**
     * Check if the permission is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Check if the permission is a system permission.
     */
    public function isSystem(): bool
    {
        return $this->is_system;
    }

    /**
     * Activate the permission.
     */
    public function activate(): bool
    {
        $this->is_active = true;
        return $this->save();
    }

    /**
     * Deactivate the permission.
     */
    public function deactivate(): bool
    {
        $this->is_active = false;
        return $this->save();
    }

    /**
     * Update last used timestamp.
     */
    public function markAsUsed(): bool
    {
        $this->last_used_at = now();
        return $this->save();
    }

    /**
     * Get the full name of the permission (resource.action).
     */
    public function getFullNameAttribute(): string
    {
        if ($this->resource && $this->action) {
            return "{$this->resource}.{$this->action}";
        }
        return $this->name;
    }

    /**
     * Check if permission has a specific condition.
     */
    public function hasCondition(string $key): bool
    {
        return isset($this->conditions[$key]);
    }

    /**
     * Get a specific condition value.
     */
    public function getCondition(string $key, $default = null)
    {
        return $this->conditions[$key] ?? $default;
    }

    /**
     * Set a condition.
     */
    public function setCondition(string $key, $value): void
    {
        $conditions = $this->conditions ?? [];
        $conditions[$key] = $value;
        $this->conditions = $conditions;
    }

    /**
     * Get all available actions.
     */
    public static function getAvailableActions(): array
    {
        return [
            self::ACTION_CREATE,
            self::ACTION_READ,
            self::ACTION_UPDATE,
            self::ACTION_DELETE,
            self::ACTION_EXPORT,
            self::ACTION_IMPORT,
            self::ACTION_VALIDATE,
            self::ACTION_REJECT,
            self::ACTION_RESTORE,
            self::ACTION_MANAGE,
            self::ACTION_DOWNLOAD,
            self::ACTION_DUPLICATE,
        ];
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
        static::creating(function ($permission) {
            if (empty($permission->slug)) {
                if ($permission->resource && $permission->action) {
                    $permission->slug = Str::slug("{$permission->resource}-{$permission->action}");
                } else {
                    $permission->slug = Str::slug($permission->name);
                }
            }

            // Enregistrer l'utilisateur créateur
            if (auth()->check() && !$permission->created_by) {
                $permission->created_by = auth()->id();
            }
        });

        // Enregistrer l'utilisateur qui met à jour
        static::updating(function ($permission) {
            if (auth()->check() && !$permission->updated_by) {
                $permission->updated_by = auth()->id();
            }
        });

        // Empêcher la suppression des permissions système
        static::deleting(function ($permission) {
            if ($permission->is_system) {
                throw new \Exception('Les permissions système ne peuvent pas être supprimées.');
            }
        });
    }
}
