<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    use HasFactory, HasUuids, SoftDeletes, Auditable;

    /**
     * Catégories de permissions disponibles.
     */
    public const CATEGORIES = [
        'Gestion des Utilisateurs',
        'Gestion des Rôles',
        'Gestion des Permissions',
        'Appels d\'Offres',
        'Lots',
        'Prestataires',
        'Proformas',
        'Évaluations',
        'Documents',
        'Paiements',
        'Système',
        'Rapports',
    ];

    /**
     * Actions disponibles pour les permissions.
     */
    public const ACTIONS = [
        'create' => 'Créer',
        'read' => 'Lire',
        'view-details' => 'Voir détails',
        'update' => 'Modifier',
        'delete' => 'Supprimer',
        'force-delete' => 'Supprimer définitivement',
        'duplicate' => 'Dupliquer',
        'create-version' => 'Créer version',
        'activate' => 'Activer',
        'deactivate' => 'Désactiver',
        'toggle-status' => 'Activer/Désactiver',
        'view-trash' => 'Voir corbeille',
        'restore' => 'Restaurer',
        'view-history' => 'Voir historique',
        'validate' => 'Valider',
        'reject' => 'Rejeter',
        'cancel' => 'Annuler',
        'pending' => 'Mettre en attente',
        'process' => 'Traiter',
        'confirm' => 'Confirmer',
        'complete' => 'Terminer',
        'resume' => 'Reprendre',
        'manage' => 'Gérer',
        'assign' => 'Attribuer',
        'reassign' => 'Réattribuer',
        'withdraw' => 'Retirer',
        'suspend' => 'Suspendre',
        'evaluate' => 'Évaluer',
        'export' => 'Exporter',
        'import' => 'Importer',
        'download' => 'Télécharger',
    ];

    /**
     * Modules disponibles.
     */
    public const MODULES = [
        'Utilisateurs',
        'Rôles',
        'Permissions',
        'Appels d\'Offres',
        'Lots',
        'Prestataires',
        'Proformas',
        'Évaluations',
        'Documents',
        'Paiements',
        'Système',
        'Rapports',
    ];

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
        'module',
        'priority',
        'display_order',
        'is_active',
        'is_system',
        'requires_confirmation',
        'conditions',
        'dependencies',
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
        'requires_confirmation' => 'boolean',
        'conditions' => 'array',
        'dependencies' => 'array',
        'priority' => 'integer',
        'display_order' => 'integer',
        'last_used_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Relation plusieurs-à-plusieurs avec les rôles.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            Role::class,
            'role_permissions',
            'permission_id',
            'role_id'
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
     * Récupère le libellé de l'action.
     *
     * @return string
     */
    public function getActionLabelAttribute(): string
    {
        return match ($this->action) {
            'create' => 'Créer',
            'read' => 'Lire',
            'view-details' => 'Voir détails',
            'update' => 'Modifier',
            'delete' => 'Supprimer',
            'force-delete' => 'Supprimer définitivement',
            'duplicate' => 'Dupliquer',
            'create-version' => 'Créer version',
            'activate' => 'Activer',
            'deactivate' => 'Désactiver',
            'toggle-status' => 'Activer/Désactiver',
            'view-trash' => 'Voir corbeille',
            'restore' => 'Restaurer',
            'view-history' => 'Voir historique',
            'validate' => 'Valider',
            'reject' => 'Rejeter',
            'cancel' => 'Annuler',
            'pending' => 'Mettre en attente',
            'process' => 'Traiter',
            'confirm' => 'Confirmer',
            'complete' => 'Terminer',
            'resume' => 'Reprendre',
            'manage' => 'Gérer',
            'assign' => 'Attribuer',
            'reassign' => 'Réattribuer',
            'withdraw' => 'Retirer',
            'suspend' => 'Suspendre',
            'evaluate' => 'Évaluer',
            'export' => 'Exporter',
            'import' => 'Importer',
            'download' => 'Télécharger',
            default => ucfirst($this->action),
        };
    }

    /**
     * Récupère la couleur associée à l'action.
     *
     * @return string
     */
    public function getActionColorAttribute(): string
    {
        return match ($this->action) {
            'create', 'activate', 'validate', 'confirm' => 'green',
            'read', 'view-details', 'view-trash', 'view-history', 'download' => 'blue',
            'update', 'duplicate', 'create-version' => 'amber',
            'delete', 'force-delete', 'deactivate', 'reject', 'cancel', 'withdraw' => 'red',
            'toggle-status', 'pending', 'suspend' => 'orange',
            'restore', 'resume' => 'teal',
            'manage', 'assign', 'reassign', 'evaluate' => 'purple',
            'process', 'export', 'import' => 'indigo',
            default => 'gray',
        };
    }

    /**
     * Récupère l'icône Font Awesome associée à l'action.
     *
     * @return string
     */
    public function getActionIconAttribute(): string
    {
        return match ($this->action) {
            'create' => 'fa-plus',
            'read' => 'fa-list',
            'view-details' => 'fa-eye',
            'update' => 'fa-edit',
            'delete' => 'fa-trash',
            'force-delete' => 'fa-trash-alt',
            'duplicate' => 'fa-copy',
            'create-version' => 'fa-code-branch',
            'activate' => 'fa-check',
            'deactivate' => 'fa-ban',
            'toggle-status' => 'fa-toggle-on',
            'view-trash' => 'fa-trash-restore',
            'restore' => 'fa-undo',
            'view-history' => 'fa-history',
            'validate' => 'fa-check-circle',
            'reject' => 'fa-times-circle',
            'cancel' => 'fa-times',
            'pending' => 'fa-clock',
            'process' => 'fa-cogs',
            'confirm' => 'fa-check-double',
            'complete' => 'fa-flag-checkered',
            'resume' => 'fa-play',
            'manage' => 'fa-tools',
            'assign' => 'fa-hand-point-right',
            'reassign' => 'fa-exchange-alt',
            'withdraw' => 'fa-hand-point-left',
            'suspend' => 'fa-pause',
            'evaluate' => 'fa-star',
            'export' => 'fa-download',
            'import' => 'fa-upload',
            'download' => 'fa-file-download',
            default => 'fa-cog',
        };
    }

    /**
     * Vérifie si la permission peut être modifiée.
     *
     * @return bool
     */
    public function canBeEdited(): bool
    {
        return !$this->is_system;
    }

    /**
     * Vérifie si la permission peut être supprimée.
     *
     * @return bool
     */
    public function canBeDeleted(): bool
    {
        if ($this->is_system) {
            return false;
        }

        // Vérifier si la permission est utilisée par des rôles
        return $this->roles()->count() === 0;
    }

    /**
     * Scope pour filtrer les permissions actives.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope pour filtrer par ressource.
     */
    public function scopeByResource($query, string $resource)
    {
        return $query->where('resource', $resource);
    }

    /**
     * Scope pour filtrer par action.
     */
    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope pour filtrer par module.
     */
    public function scopeByModule($query, string $module)
    {
        return $query->where('module', $module);
    }

    /**
     * Scope pour filtrer par catégorie.
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope pour rechercher dans les permissions.
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('slug', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhere('resource', 'like', "%{$search}%")
                ->orWhere('module', 'like', "%{$search}%");
        });
    }

    /**
     * Scope pour ordonner par ordre d'affichage.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('name');
    }
}
