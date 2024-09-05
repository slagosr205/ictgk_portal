<?php

namespace App\View\Components;

use Closure;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class HistorialLaboral extends Component
{
    /**
     * Create a new component instance.
     */
    public $informacionLaboral;
    public $datosEmpresa;
    public function __construct($informacionLaboral,$datosEmpresa)
    {
        //
        $this->informacionLaboral=$informacionLaboral;
        $this->datosEmpresa=$datosEmpresa;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.historial-laboral');
    }
}
