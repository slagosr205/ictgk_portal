<?php

namespace App\View\Components;

use Closure;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class EnviarSolicitud extends Component
{
    /**
     * Create a new component instance.
     */
    public $identidad;
    public function __construct($identidad)
    {
        //
        $this->identidad=$identidad;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.enviar-solicitud');
    }
}
