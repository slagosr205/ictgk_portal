<?php

namespace App\View\Components\puestos;

use Closure;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;
use App\Models\DepartamentosModel;
class PuestosAdd extends Component
{
    /**
     * Create a new component instance.
     */
    protected $departamentos;
    public function __construct($departament)
    {
        //
        $this->departamentos= $departament;
       
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        
        return view('components.puestos.puestos-add')->with(['departamentos'=>$this->departamentos]);
    }
}
