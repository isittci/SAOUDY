<?php

namespace App\Models;

use App\Traits\HasPermissions;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasUuids, SoftDeletes, HasPermissions;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

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
        'nom_complet',
        'email',
        'password',
        'telephone_principal',
        'telepone_secondaire',
        'role_id',
        'email_verified_at',
        'statut',
        'created_by',
        'updated_by',
        'deleted_by',
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
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['is_active'];

    /**
     * Get the role that owns the user.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    /**
     * Get the user who created this user.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    /**
     * Get the user who last updated this user.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    /**
     * Get the user who deleted this user.
     */
    public function deleter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by', 'id');
    }

    /**
     * Scope a query to only include active users.
     */
    public function scopeActive($query)
    {
        return $query->where('statut', 1);
    }

    /**
     * Scope a query to only include inactive users.
     */
    public function scopeInactive($query)
    {
        return $query->where('statut', 0);
    }

    /**
     * Scope a query to only include verified users.
     */
    public function scopeVerified($query)
    {
        return $query->whereNotNull('email_verified_at');
    }

    /**
     * Scope a query to only include unverified users.
     */
    public function scopeUnverified($query)
    {
        return $query->whereNull('email_verified_at');
    }

    /**
     * Scope a query to filter by role.
     */
    public function scopeByRole($query, $roleId)
    {
        return $query->where('role_id', $roleId);
    }

    /**
     * Scope a query to search users by name or email.
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('nom_complet', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('telephone_principal', 'like', "%{$search}%");
        });
    }

    /**
     * Check if the user is active.
     */
    public function isActive(): bool
    {
        return $this->statut == 1;
    }

    /**
     * Check if the user is verified.
     */
    public function isVerified(): bool
    {
        return !is_null($this->email_verified_at);
    }

    /**
     * Check if user has a specific role.
     */
    public function hasRole(string $roleSlug): bool
    {
        return $this->role && $this->role->slug === $roleSlug;
    }

    /**
     * Check if user has a role with minimum level.
     */
    public function hasMinimumLevel(int $level): bool
    {
        return $this->role && $this->role->level >= $level;
    }

    /**
     * Check if user has a specific permission.
     */
    public function hasPermission(string $permissionSlug): bool
    {
        return $this->role && $this->role->hasPermission($permissionSlug);
    }

    /**
     * Check if user has any of the given permissions.
     */
    public function hasAnyPermission(array $permissionSlugs): bool
    {
        return $this->role && $this->role->hasAnyPermission($permissionSlugs);
    }

    /**
     * Check if user has all of the given permissions.
     */
    public function hasAllPermissions(array $permissionSlugs): bool
    {
        return $this->role && $this->role->hasAllPermissions($permissionSlugs);
    }

    /**
     * Get all permissions for the user through their role.
     */
    public function getAllPermissions()
    {
        return $this->role ? $this->role->activePermissions : collect();
    }

    /**
     * Check if user can perform action on resource.
     */
    public function can($ability, $arguments = []): bool
    {
        // Vérifier d'abord avec la méthode parent de Laravel
        $laravelCan = parent::can($ability, $arguments);

        if ($laravelCan) {
            return true;
        }

        // Ensuite vérifier avec notre système de permissions
        return $this->hasPermission($ability);
    }

    /**
     * Activate the user account.
     */
    public function activate(): bool
    {
        $this->statut = 1;
        return $this->save();
    }

    /**
     * Deactivate the user account.
     */
    public function deactivate(): bool
    {
        $this->statut = 0;
        return $this->save();
    }

    /**
     * Get the is_active attribute.
     */
    public function getIsActiveAttribute(): bool
    {
        return $this->isActive();
    }

    /**
     * Get the full name attribute (alias).
     */
    public function getFullNameAttribute(): string
    {
        return $this->nom_complet;
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Enregistrer automatiquement l'utilisateur qui crée
        static::creating(function ($model) {
            if (auth()->check() && !$model->created_by) {
                $model->created_by = auth()->id();
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
