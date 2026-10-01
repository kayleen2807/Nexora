<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRol
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $rol = strtolower($request->user()->rol->nombre ?? '');

        if (! in_array($rol, $roles, true)) {
            abort(403);
        }

        return $next($request);
    }
}
