<?php

namespace App\View\Components;

use Closure;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class ActualizacionFicha extends Component
{
    /**
     * Create a new component instance.
     */
    public $infocandidatos;
    public function __construct($infocandidatos)
    {
        //
        $this->infocandidatos=$infocandidatos;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.actualizacion-ficha');
    }
}
