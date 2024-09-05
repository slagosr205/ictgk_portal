<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
class CheckSessionExpiration
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $maxSessionLifetime = config('session.lifetime') * 60; // Duración de la sesión en segundos
        $lastActivity = Session::get('last_activity');

        if (!empty($lastActivity) && (time() - $lastActivity > $maxSessionLifetime)) {
            // La sesión ha expirado
            Auth::logout(); // Cerrar sesión por seguridad
            return redirect()->route('login')->with('session_expired', 'Tu sesión ha expirado. Por favor, inicia sesión de nuevo.');
        }

        // Actualizar la última actividad en la sesión
        Session::put('last_activity', time());

        return $next($request);
    }
}
