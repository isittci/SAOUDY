<?php

namespace App\Models;

use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

class PersonalAccessToken extends SanctumPersonalAccessToken
{
    /**
     * Indique que la clé primaire n'est pas auto-incrémentée
     */
    public $incrementing = false;

    /**
     * Type de la clé primaire
     */
    protected $keyType = 'string';

    /**
     * Boot du modèle - génération automatique de l'UUID
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }
}
