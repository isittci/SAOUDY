<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait Auditable
{
    /**
     * Boot the trait.
     */
    protected static function bootAuditable(): void
    {
        // Lors de la création
        static::creating(function ($model) {
            if (Auth::check() && !$model->created_by) {
                $model->created_by = Auth::id();
            }
        });

        // Lors de la mise à jour
        static::updating(function ($model) {
            if (Auth::check()) {
                $model->updated_by = Auth::id();
            }
        });

        // Lors de la suppression (soft delete)
        static::deleting(function ($model) {
            if (Auth::check() && method_exists($model, 'trashed') && !$model->trashed()) {
                $model->deleted_by = Auth::id();
                $model->save();
            }
        });
    }

    /**
     * Relation avec l'utilisateur créateur.
     */
    public function creator()
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'created_by');
    }

    /**
     * Relation avec l'utilisateur modificateur.
     */
    public function updater()
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'updated_by');
    }

    /**
     * Relation avec l'utilisateur suppresseur.
     */
    public function deleter()
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'deleted_by');
    }
}
