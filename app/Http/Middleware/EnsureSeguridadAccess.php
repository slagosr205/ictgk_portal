<?php

namespace App\Http\Middleware;

use App\Models\PerfilModel;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSeguridadAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user) {
            abort(401);
        }

        $perfil = PerfilModel::find($user->perfil_id);
        if (! $perfil) {
            abort(403, 'acceso no autorizado');
        }

        // Allow admins by description, and allow profiles with seguridad=1.
        if (($perfil->perfilesdescrip ?? null) === 'admin' || (bool) ($perfil->seguridad ?? false)) {
            return $next($request);
        }

        abort(403, 'acceso no autorizado');
    }
}
