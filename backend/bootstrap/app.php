<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [\App\Http\Middleware\SetLocale::class,
        ]);

        $middleware->alias([
            'rol' => \App\Http\Middleware\CheckRol::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Las rutas de datos de los paneles (fetch desde JS) siempre responden JSON,
        // incluso en errores de validación (en vez de redirigir con 302 a HTML)
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*', 'administrador/*', 'gerente/*', 'cajero/*')
                || $request->expectsJson(),
        );
    })->create();
