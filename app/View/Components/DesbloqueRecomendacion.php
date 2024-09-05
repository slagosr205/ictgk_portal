<?php

namespace App\View\Components;

use Closure;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class DesbloqueRecomendacion extends Component
{
    /**
     * Create a new component instance.
     */
    public $identidad;
    public $empresaID;
    public function __construct($identidad,$empresaID)
    {
        //
        $this->identidad=$identidad;
        $this->empresaID=$empresaID;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.desbloque-recomendacion');
    }
}
