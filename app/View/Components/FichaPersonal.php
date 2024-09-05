<?php

namespace App\View\Components;

use Closure;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class FichaPersonal extends Component
{
    /**
     * Create a new component instance.
     */

     public $informacionlaboral;
     public $infocandidatos;
     public $datosEmpresaActual;
    public function __construct($informacionlaboral, $infocandidatos, $datosEmpresaActual)
    {
        //
        $this->informacionlaboral=$informacionlaboral;
        $this->infocandidatos=$infocandidatos;
        $this->datosEmpresaActual=$datosEmpresaActual;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.ficha-personal');
    }
}
