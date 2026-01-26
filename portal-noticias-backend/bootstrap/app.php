<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// configura a aplicacao Laravel
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',      // rotas web
        api: __DIR__.'/../routes/api.php',      // rotas da API
        apiPrefix: 'api',                        // prefixo /api para rotas da API
        commands: __DIR__.'/../routes/console.php', // comandos artisan
        health: '/up',                           // endpoint de health check
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // configura middlewares globais aqui se necessario
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // configura tratamento de excecoes aqui se necessario
    })->create();
