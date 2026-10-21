<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveBusiness
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if (! $user->is_active) {
            abort(403, 'Tu cuenta está desactivada. Contacta al propietario del negocio.');
        }

        // El superadministrador puede administrar todos los negocios.
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // Cada usuario pertenece a un solo negocio.
        $business = $user->business;

        if (! $business || $business->status !== 'active') {
            abort(
                403,
                'Tu negocio está suspendido o no tienes un negocio asignado. Contacta al administrador.'
            );
        }

        return $next($request);
    }
}
