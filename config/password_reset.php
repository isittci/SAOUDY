<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Durée de validité du token de réinitialisation
    |--------------------------------------------------------------------------
    |
    | Définit la durée de validité en minutes du lien de réinitialisation
    | envoyé par email aux utilisateurs. Par défaut : 2 heures (120 minutes)
    |
    */
    'token_expiration_minutes' => env('PASSWORD_RESET_EXPIRATION', 120),

    /*
    |--------------------------------------------------------------------------
    | Nombre maximum de tentatives
    |--------------------------------------------------------------------------
    |
    | Nombre maximum de tentatives de réinitialisation autorisées
    |
    */
    'max_attempts' => env('PASSWORD_RESET_MAX_ATTEMPTS', 3),
];
