<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCajaAbierta
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        // El Administrador siempre pasa sin restricción de caja
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Si el ROL del usuario no requiere caja, pasa directo
        // (operadores de inventario, supervisores de catálogo, etc.)
        if (! optional($user->role)->requiere_caja) {
            return $next($request);
        }

        // El rol sí requiere caja — verificamos que haya una abierta
        if (! $user->cajaActiva()) {
            if ($request->routeIs('dashboard')) {
                return redirect()->route('caja.index');
            }

            return redirect()->route('caja.index')
                ->with('error', 'Debes abrir el turno de caja para realizar movimientos u operaciones.');
        }

        return $next($request);
    }
}
