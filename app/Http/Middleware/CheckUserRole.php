<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, ...$roles)
    {
        // Verifica si el usuario autenticado tiene uno de los roles permitidos
        if (!Auth::check() || !Auth::user()->hasAnyRole($roles)) {
            // Si no tiene permisos, redirige o muestra un error
            abort(403, 'Acceso denegado');
        }

        return $next($request);
    }
}
