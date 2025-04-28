<?php

namespace App\Http\Controllers;

use App\Models\Candidatos;
use App\Models\Ingresos;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

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

    
   /**
    
   *La función comprueba si existen un candidato y un registro de ingresos según la identidad y devuelve un
    * mensaje en consecuencia.
    * 
    * @param candidatoData La función `actualizarOCrearFicha` toma un parámetro ``,
    * que parece ser una matriz que contiene datos relacionados con un candidato. La función primero comprueba si
    * existe un registro de candidato existente en la tabla `Candidatos` basado en el campo 'identidad'
    * 
    * @return La función devolverá 'Solo actualizar registro' si ambos son candidatos existentes
    * y se encuentran un registro de ingresos inactivo, o 'Crear registro con estado x' si esas condiciones son
    *no se cumplen.
    */
  
    
    private function actualizarOCrearFicha($candidatoData)
    {
        try {
            // Validar datos de entrada
            if (!isset($candidatoData['identidad'], $candidatoData['nombre'], $candidatoData['apellido'])) {
                throw new \Exception('Datos incompletos para procesar el candidato.');
            }

            // Limpiar identidad
            $identidad = str_replace('-', '', $candidatoData['identidad']);
            if (empty($identidad)) {
                throw new \Exception('El campo identidad no puede estar vacío.');
            }

            // Convertir fecha de nacimiento
            $fechaNacimiento = $candidatoData['fecha_nacimiento'] ?? '1900-12-31';
            try {
                $fechaNacimientoConvertida = Carbon::createFromFormat('d/m/Y', $fechaNacimiento)->format('Y-m-d');
            } catch (\Exception $e) {
                $fechaNacimientoConvertida = '1900-12-31';
            }
            
             // Crear nuevo registro si no existe
             $comentarios = json_encode([
                [
                    'comentarios' => 'bloquea por falta de actitud',
                    'fechaBloqueo' => now()->toISOString(),
                ]
            ], true);

            // Verificar si el candidato ya existe
            $existingCandidato = Candidatos::where('identidad', $identidad)->first();

            if ($existingCandidato) {
                // Verificar si existe un ingreso inactivo
                $existeIngreso = Ingresos::where('identidad', $identidad)->where('activo', 'n')->exists();
                
                if ($existeIngreso) {
                    // Actualizar el registro existente
                  
                    return [
                        'status' => 'success',
                        'message' => 'Registro con nombre ' . $candidatoData['nombre'] . ' ' . $candidatoData['apellido'] . ' actualizado con éxito',
                    ];
                }
               // Actualizar el candidato existente
                $existingCandidato->update([
                    'activo' => 'x',
                    'comentarios' => $comentarios,
                ]);
                // Si el ingreso no existe, no se realiza ninguna acción adicional
                return [
                    'status' => 'info',
                    'nombre' => $candidatoData['nombre'],
                    'apellido' => $candidatoData['apellido'],
                    'identidad' => $identidad,
                    'message' => 'El candidato ya existe, pero no tiene un ingreso inactivo.',
                ];
            }

           

            Candidatos::create([
                'identidad' => $identidad,
                'nombre' => $candidatoData['nombre'],
                'apellido' => $candidatoData['apellido'],
                'telefono' => $candidatoData['telefono'] ?? '999999999',
                'correo' => $candidatoData['correo'] ?? 'na@correo.com',
                'direccion' => $candidatoData['direccion'] ?? 'na',
                'generoM_F' => $candidatoData['generoM_F'],
                'fecha_nacimiento' => $fechaNacimientoConvertida,
                'activo' => 'x',
                'comentarios' => $comentarios,
            ]);

            return [
                'status' => 'success',
                'nombre' => $candidatoData['nombre'],
                'apellido' => $candidatoData['apellido'],
                'identidad' => $identidad,
                'message' => 'Registro con nombre ' . $candidatoData['nombre'] . ' ' . $candidatoData['apellido'] . ' creado con éxito y bloqueado con estado x',
            ];
        } catch (\Exception $ex) {
            Log::error('Error al procesar la ficha del candidato: ' . $ex->getMessage());
            return ['status' => 'error', 
            'nombre' => $candidatoData['nombre'],
            'apellido' => $candidatoData['apellido'],
            'identidad' => $identidad,
            'message' => 'Error al procesar la solicitud: ' . $ex->getMessage()];
        }
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
        
            //code...
         //   $controller=new CandidatosController();
            $datos=$this->candidatoController->processData2($request);

            
            $result= $this->actualizacionMasivaBloqueos($datos);

          
            //return $result;
            //return redirect()->back()->with('reponse',$result);
            
          return redirect()->back()->with('response',$result);
       
    }
    

}
