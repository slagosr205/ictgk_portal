<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BloqueoController extends CandidatosController
{
    //

    public function recibirBloqueos(Request $request)
    {
        try {
            //code...
         //   $controller=new CandidatosController();
            $datos=$this->processData2($request);

            dd($datos);

        } catch (\Exception $th) {
            dd($th);
        }
    }
    

}
