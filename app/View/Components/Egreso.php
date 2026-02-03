<?php

namespace App\View\Components;

use Closure;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class Egreso extends Component
{
    /**
     * Create a new component instance.
     */
    public $informacionlaboral;
    public $empresas;
    public $empresaActual;
    public function __construct($informacionlaboral,$empresas, $empresaActual)
    {
        //
        $this->informacionlaboral=$informacionlaboral;
        $this->empresas=$empresas;
        $this->empresaActual=$empresaActual;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.egreso');
    }
}
