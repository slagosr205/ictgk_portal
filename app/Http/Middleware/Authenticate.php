<?php

namespace App\Http\Middleware;

use App\Models\User;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Closure;
use Illuminate\Support\Facades\Auth;

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
            return redirect()->guest(route('login'));
        }

        if (auth()->check() && auth()->user()->status == 0) {
            auth()->logout();
            return redirect('/login')->withErrors(['Your account is blocked.']);
        }

        // Solo actualizar última sesión si no es una petición AJAX
        if (!$request->ajax() && auth()->check()) {
            $user = User::find(Auth::user()->id);
            if ($user) {
                $user->last_session = now();
                $user->save();
            }
        }

        return $next($request);
    }

    
}
