<?php

namespace App\Http\Controllers;

use App\Models\Candidatos;
use App\Models\Ingresos;
use Illuminate\Http\Request;

class BloqueoController extends Controller
{
    //
    /**Funcion que recibira la informacion de candidato, primero validar que exista, 
     * si existe solo actualizar ficha en tabla candidatos en el campo activo con el valor n,    */

    protected $candidatoController;

    public function __construct(CandidatosController $candidatoController)
    {
        $this->candidatoController=$candidatoController;
    }

    
    private function actualizarOCrearFicha($candidatoData)
    {
        $existingCandidato = Candidatos::where('identidad', $candidatoData['identidad'])->first();
        $existeIngreso = Ingresos::where('identidad', $candidatoData['identidad'])
                         ->where('activo', 'n')
                         ->first();
        if($existingCandidato && $existeIngreso)
        {
            return 'Solo actualizar registro ';
        }


        

        


        return 'Crear registro con estado x';

    }

    private function actualizacionMasivaBloqueos($data)
    {
       
        $responses = [];

            foreach($data as $candidatoData){
                $responses[] = $this->actualizarOCrearFicha($candidatoData);
            }

            return ['response' => $responses];
    }

    public function recibirBloqueos(Request $request)
    {
        try {
            //code...
         //   $controller=new CandidatosController();
            $datos=$this->candidatoController->processData2($request);
          $response= $this->actualizacionMasivaBloqueos($datos);

          
           dd($response);

        } catch (\Exception $ex) {
            dd($ex);
        }
    }
    

}
