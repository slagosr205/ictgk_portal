<?php

namespace App\View\Components;

use Closure;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;
use App\Models\PuestosModel;
class Ingreso extends Component
{
    /**
     * Create a new component instance.
     */
 
     public $informacionlaboral;
     public $empresas;

     public $candidato;

     public $puestos;
    public function __construct($informacionlaboral,$empresas, $candidato)
    {
        //

        $this->puestos=PuestosModel::select('puestos.*')
        ->join('departamentos','departamentos.id','=','puestos.departamento_id')
        ->where('departamentos.empresa_id',auth()->user()->empresa_id)->get();
        $this->informacionlaboral=$informacionlaboral;
        $this->empresas=$empresas;
        $this->candidato=$candidato;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.ingreso',['puestos'=>$this->puestos]);
    }
}
