<?php

namespace App\View\Components;

use App\Models\Empresas;
use Closure;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;
use App\Models\PerfilModel;

class MenuBar extends Component
{
    /**
     * Create a new component instance.
     */
    protected $logos;
    public function __construct()
    {
        
        //
       // dd(auth()->user());
        if(is_null(auth()->user()))
        {
        $this->logos=[['nombre'=>'']];
        }else{
            $this->logos=Empresas::select('nombre')
            ->where('id',auth()->user()->empresa_id)->get();
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        
        $logo=$this->logos;
        
        
        return view('components.menu-bar',['logos'=>$logo]);
    }
}