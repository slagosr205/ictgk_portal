<?php

namespace App\View\Components;

use Closure;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class Dmtables extends Component
{
    /**
     * Create a new component instance.
     */
    public $candidatos;
    public function __construct($candidatos)
    {
        //
        $this->candidatos=$candidatos;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.dmtables');
    }
}
