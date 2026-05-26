<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cache
    |--------------------------------------------------------------------------
    |
    | Configuración del caché para los settings.
    |
    */
    'cache_ttl' => env('SETTING_MANAGER_CACHE_TTL', 3600),

    /*
    |--------------------------------------------------------------------------
    | Guard
    |--------------------------------------------------------------------------
    |
    | Guard de autenticación utilizado para registrar el usuario que realiza
    | cambios en los settings. Puede usarse con lechuganegra/authmanager.
    |
    */
    'guard' => env('SETTING_MANAGER_GUARD', 'api'),
];
