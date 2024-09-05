<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Closure;
class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
       
        return $request->expectsJson() ? null : route('login');
    }

    public function handle($request, Closure $next, ...$guards)
    {
       
        if ($this->authenticate($request, $guards) === 'authentication_required') {
            // La sesión del usuario ha expirado
            return redirect()->guest(route('login'));
        }

        if (auth()->check() && auth()->user()->status == 0) {
            auth()->logout();
            return redirect('/login')->withErrors(['Your account is blocked.']);
        }


        return $next($request);
    }

    
}
