<?php

namespace App\Http\Controllers;

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
}
