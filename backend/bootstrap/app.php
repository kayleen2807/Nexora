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
            \App\Http\Middleware\SinCacheConSesion::class,   // "Atrás" después de cerrar sesión ya no muestra el panel
        ]);

        $middleware->alias([
            'rol' => \App\Http\Middleware\CheckRol::class,
        ]);

        // En Railway la visita llega por HTTPS a su proxy y este la pasa a Laravel por HTTP.
        // Confiar en el proxy hace que Laravel lea X-Forwarded-Proto y sepa que el original era HTTPS
        // (enlaces de correos, invitaciones y el regreso de Google). En local no hay proxy: no cambia nada.
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Las rutas de datos de los paneles (fetch desde JS) siempre responden JSON,
        // incluso en errores de validación (en vez de redirigir con 302 a HTML)
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*', 'administrador/*', 'gerente/*', 'cajero/*')
                || $request->expectsJson(),
        );
    })->create();
