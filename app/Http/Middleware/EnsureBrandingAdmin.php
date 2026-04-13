<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureBrandingAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user) {
            abort(401);
        }

        $allowed = (array) config('branding.admin_perfil_ids', [1]);
        if (in_array((int) $user->perfil_id, $allowed, true)) {
            return $next($request);
        }

        abort(403, 'acceso no autorizado');
    }
}
