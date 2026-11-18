<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Páginas vistas CON sesión iniciada: el navegador no debe guardarlas. Sin esto, después de "Cerrar sesión"
 * el botón "Atrás" mostraba el panel desde la memoria del navegador (sin preguntarle al servidor).
 * Con `no-store` el navegador vuelve a pedir la página y el servidor, ya sin sesión, manda al login.
 */
class SinCacheConSesion
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->user()) {
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0, private');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
        }

        return $response;
    }
}
