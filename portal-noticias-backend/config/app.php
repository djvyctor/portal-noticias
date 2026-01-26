<?php

return [
    // nome da aplicacao
    'name' => env('APP_NAME', 'Laravel'),

    // ambiente da aplicacao e qual ambiente esta rodando
    'env' => env('APP_ENV', 'production'),

    // modo de debug
    // IMPORTANTE: deixar false em producao
    'debug' => (bool) env('APP_DEBUG', false),

    // URL base da aplicacao, gera URLs
    'url' => env('APP_URL', 'http://localhost'),

    // fuso horario
    'timezone' => 'UTC',

    // idioma
    'locale' => env('APP_LOCALE', 'en'),
    
    // idioma de fallback
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),
    
    // idioma para gerar dados
    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),

    // algoritmo de criptografia usado pelo Laravel
    'cipher' => 'AES-256-CBC',
    
    // chave de criptografia -> gerada automaticamente com php artisan key:generate
    'key' => env('APP_KEY'),
    
    // chaves anteriores
    'previous_keys' => [
        ...array_filter(
            explode(',', (string) env('APP_PREVIOUS_KEYS', ''))
        ),
    ],

    // configuracao do modo de manutencao -> como armazenar status e onde armazenar
    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],
];
