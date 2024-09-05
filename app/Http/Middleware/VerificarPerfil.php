<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\PerfilModel;
class VerificarPerfil
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $perfil_id=$request->user()->perfil_id;
        $perfil=PerfilModel::find($perfil_id);
        if ($perfil->count()>0 && $perfil->ingreso===1 )
        {
            # code...
            return $next($request);
        }

        abort(403,'acceso no autorizado');
        
    }
}
