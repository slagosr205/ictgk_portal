<?php

namespace App\Http\Controllers;

use App\Models\Empresas;
use Illuminate\Http\Request;
use App\Models\PerfilModel;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $perfilUsers=PerfilModel::where('id',\Auth::user()->perfil_id)->get();
        
        return view('home')->with(['perfilUsers'=>$perfilUsers]);
    }

    public function cargarDatosUsuario()
    {
        $datosUsuarios=Empresas::
        join('departamentos', 'empresas.id', '=', 'departamentos.empresa_id')
        ->join('puestos', 'departamentos.id', '=', 'puestos.departamento_id')
        ->where('empresas.id',\Auth::user()->empresa_id)
        ->get();
        
        return response()->json(['datosUsuarios'=>$datosUsuarios]);
    }
}
