<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class RolePermission extends Pivot
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'role_permissions';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'role_id',
        'permission_id',
        'attribue_par',
        'attribue_le',
        'expire_le',
        'actif',
        'conditions',
        'notes',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'attribue_le' => 'datetime',
        'expire_le' => 'datetime',
        'actif' => 'boolean',
        'conditions' => 'array',
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
    protected $appends = ['is_expired', 'is_active'];

    /**
     * Get the role that owns this pivot.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    /**
     * Get the permission that owns this pivot.
     */
    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class, 'permission_id', 'id');
    }

    /**
     * Get the user who attributed this permission.
     */
    public function attributor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'attribue_par', 'id');
    }

    /**
     * Get the user who created this record.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    /**
     * Get the user who last updated this record.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    /**
     * Get the user who deleted this record.
     */
    public function deleter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by', 'id');
    }

    /**
     * Scope a query to only include active permissions.
     */
    public function scopeActive($query)
    {
        return $query->where('actif', true);
    }

    /**
     * Scope a query to only include inactive permissions.
     */
    public function scopeInactive($query)
    {
        return $query->where('actif', false);
    }

    /**
     * Scope a query to only include non-expired permissions.
     */
    public function scopeNotExpired($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expire_le')
                ->orWhere('expire_le', '>', now());
        });
    }

    /**
     * Scope a query to only include expired permissions.
     */
    public function scopeExpired($query)
    {
        return $query->whereNotNull('expire_le')
            ->where('expire_le', '<=', now());
    }

    /**
     * Scope a query to filter by role.
     */
    public function scopeByRole($query, $roleId)
    {
        return $query->where('role_id', $roleId);
    }

    /**
     * Scope a query to filter by permission.
     */
    public function scopeByPermission($query, $permissionId)
    {
        return $query->where('permission_id', $permissionId);
    }

    /**
     * Scope a query to get valid permissions (active and not expired).
     */
    public function scopeValid($query)
    {
        return $query->active()->notExpired();
    }

    /**
     * Scope to filter by attributor.
     */
    public function scopeAttributedBy($query, $userId)
    {
        return $query->where('attribue_par', $userId);
    }

    /**
     * Check if the permission is expired.
     */
    public function isExpired(): bool
    {
        if (is_null($this->expire_le)) {
            return false;
        }
        return $this->expire_le->isPast();
    }

    /**
     * Check if the permission is active (actif and not expired).
     */
    public function isActive(): bool
    {
        return $this->actif && !$this->isExpired();
    }

    /**
     * Get the is_expired attribute.
     */
    public function getIsExpiredAttribute(): bool
    {
        return $this->isExpired();
    }

    /**
     * Get the is_active attribute.
     */
    public function getIsActiveAttribute(): bool
    {
        return $this->isActive();
    }

    /**
     * Activate the permission for this role.
     */
    public function activate(): bool
    {
        $this->actif = true;
        return $this->save();
    }

    /**
     * Deactivate the permission for this role.
     */
    public function deactivate(): bool
    {
        $this->actif = false;
        return $this->save();
    }

    /**
     * Set expiration date.
     */
    public function setExpiration(Carbon $date): bool
    {
        $this->expire_le = $date;
        return $this->save();
    }

    /**
     * Remove expiration date.
     */
    public function removeExpiration(): bool
    {
        $this->expire_le = null;
        return $this->save();
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
     * Get days until expiration.
     */
    public function daysUntilExpiration(): ?int
    {
        if (is_null($this->expire_le)) {
            return null;
        }
        return now()->diffInDays($this->expire_le, false);
    }

    /**
     * Check if expiring soon (within specified days).
     */
    public function isExpiringSoon(int $days = 7): bool
    {
        if (is_null($this->expire_le)) {
            return false;
        }

        $daysUntil = $this->daysUntilExpiration();
        return $daysUntil !== null && $daysUntil > 0 && $daysUntil <= $days;
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Enregistrer automatiquement l'utilisateur qui crée
        static::creating(function ($model) {
            if (auth()->check()) {
                if (!$model->created_by) {
                    $model->created_by = auth()->id();
                }
                if (!$model->attribue_par) {
                    $model->attribue_par = auth()->id();
                }
            }

            // Définir la date d'attribution si non fournie
            if (!$model->attribue_le) {
                $model->attribue_le = now();
            }
        });

        // Enregistrer automatiquement l'utilisateur qui met à jour
        static::updating(function ($model) {
            if (auth()->check() && !$model->updated_by) {
                $model->updated_by = auth()->id();
            }
        });

        // Enregistrer automatiquement l'utilisateur qui supprime
        static::deleting(function ($model) {
            if (auth()->check() && !$model->deleted_by) {
                $model->deleted_by = auth()->id();
                $model->saveQuietly();
            }
        });
    }
}
