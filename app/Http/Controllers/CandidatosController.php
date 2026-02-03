<?php

namespace App\Http\Controllers;

use App\Events\RegistroActualizado;
use App\Http\Controllers\Controller;
use App\Models\Ingresos;
use App\Models\Egresos;
use App\Models\PuestosModel;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use App\Models\Candidatos;
use App\Models\Empresas;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use App\Mail\EnviarSolicitudCandidato;
use Exception;
//use Mail;
use Validator;
use Jenssegers\Date\Date;
use App\Exports\ExportTemplateOut;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use App\Exceptions\ArrayFieldCountException;
use App\Imports\CandidateImport;
use App\Imports\CsvImport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Swift_TransportException;
use Symfony\Component\Mailer\Exception\TransportException;

class CandidatosController extends Controller
{

    /**
     * funcion que valida los campos que debe traer las plantillas de ingresos
     */
    public function compareFields($importedFields, $getTable)
    {
        // Obtener campos de la tabla desde la base de datos
        $tableFields = Schema::getColumnListing($getTable);
        $fieldsToExclude = ['id', 'comentarios', 'created_at', 'updated_at', 'fechaEgreso', 'tipo_egreso', 'forma_egreso', 'Comentario', 'recomendado', 'bloqueo_recomendado', 'prohibirIngreso', 'ComenProhibir'];
        // Verificar si falta algún campo
        $filteredTableFields = array_diff($tableFields, $fieldsToExclude);
        $missingFields = array_diff($filteredTableFields, $importedFields);
        if (!empty($missingFields)) {
            return $missingFields;
        } else {
            return 'Todos los campos están presentes.';
        }
    }
    /**
     * funcion que permitira el procesamiento de los datos que vienen de un archivo csv para hacer importaciones masivas
     */


    public function processData(Request $re): array
    {
        $datos = array();
        // Verifica si la solicitud tiene un archivo CSV adjunto
        if ($re->hasFile('archivo_csv')) {
            // Obtiene el archivo CSV
            try {
                $archivoCsv = $re->file('archivo_csv');

                // Lee el contenido del archivo CSV y guárdalo en una variable
                $contenidoCsv = file_get_contents($archivoCsv->getRealPath());
                $codificacion = mb_detect_encoding($contenidoCsv, 'UTF-8', true);
                if ($codificacion === 'UTF-8') {
                    $contenidoCsv = preg_replace('/^\x{FEFF}/u', '', $contenidoCsv);
                } else {
                    $contenidoCsv = iconv(mb_detect_encoding($contenidoCsv, 'UTF-8, ISO-8859-1, Windows-1252', true), 'UTF-8//IGNORE', $contenidoCsv);
                    $contenidoCsv = preg_replace('/[^\x{0009}\x{000A}\x{000D}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]/u', '', $contenidoCsv);
                }



                /*REVISAR */
                $contenidoCsv = str_replace(' "', '"', $contenidoCsv);
                $contenidoCsv = str_replace('" ', '"', $contenidoCsv);
                // Aquí puedes hacer lo que quieras con $contenidoCsv
                // por ejemplo, puedes procesarlo y guardarlo en una variable
                $lineas = explode("\n", $contenidoCsv);

                $campos = str_getcsv($lineas[0]);
                // Recorrer cada línea y convertirla en un array asociativo
                for ($i = 1; $i < count($lineas); $i++) {
                    $linea = $lineas[$i];
                    // Ignorar líneas vacías
                    if (!empty(trim($linea))) {
                        // Convertir la línea en un array
                        $datosLinea = str_getcsv($linea);

                        // Crear un array asociativo combinando los campos y los datos
                        $fila = array_combine($campos, $datosLinea);
                        $fila = array_diff_key($fila, array_flip(['validacion']));
                        $fila = array_filter($fila);
                        $fila2[] = $fila;
                        if (array_filter($fila)) {
                            $datos[] = $fila;
                        }
                        // Agregar la fila al array de datos


                    }
                }
            } catch (Exception $exception) {
                return ['error' => 'error al procesar el csv'];
            }
        }
        return $datos;
    }


    public function processData2(Request $re): array
    {
        if (!$re->hasFile('archivo_csv')) {
            return ['error' => 'No se adjuntó un archivo CSV'];
        }

        try {
            $archivoCsv = $re->file('archivo_csv');
            // Obtén los datos desde el importador
            $datos = Excel::toArray(new CsvImport, $archivoCsv);

            // Verifica si hay datos y convierte a un array asociativo
            if (!empty($datos[0])) {
                // El primer elemento contiene los encabezados
                $headers = array_map('trim', $datos[0][0]);

                $result = [];

                // Procesar las filas de datos (ignorando la primera que son los encabezados)
                foreach (array_slice($datos[0], 1) as $row) {
                    if (!empty(array_filter($row))) { // Ignorar filas vacías
                        // Verificar que cada elemento sea una cadena antes de aplicar trim
                        $trimmedRow = array_map(function ($value) {
                            return is_string($value) ? trim($value) : $value;  // Solo aplica trim si es una cadena
                        }, $row);

                        // Asociar los valores a sus respectivos encabezados
                        $fila = array_combine($headers, $trimmedRow);

                        // Elimina la columna 'validacion' y valores vacíos
                        $fila = array_diff_key($fila, array_flip(['validacion']));
                        $fila = array_filter($fila); // Elimina valores vacíos

                        $result[] = $fila;
                    }
                }

                return $result;
            }

            return []; // Si no hay datos válidos
        } catch (\Exception $exception) {
            return ['error' => 'Error al procesar el CSV: ' . $exception->getMessage()];
        }
    }

    //Validación de Campos Insuficientes

    private function validarCamposInsuficientes($datos, $columnHeaders)
    {
        $indicesConMenosDe13Campos = [];

        foreach ($datos as $indice => $subArray) {
            if (count($subArray) < count($columnHeaders) + 1) {
                $camposFaltantes = array_diff($columnHeaders, array_keys($subArray));
                $lineNumber = $indice + 1;
                $indicesConMenosDe13Campos[] = [
                    'identidad' => $datos[$indice]['identidad'],
                    'nombre' => $datos[$indice]['nombre'],
                    'campos_faltantes' => $camposFaltantes,
                    'LineNumber' => $lineNumber,
                ];
            }
        }

        return $indicesConMenosDe13Campos;
    }


    private function formatUserDate($userDate)
    {
        // Primero intentamos con el parseo libre de Carbon
        try {
            // Intentamos analizar la fecha usando Carbon
            $date = Carbon::parse($userDate);
            // Devolvemos la fecha en formato YYYY-MM-DD para la base de datos
            return $date->format('Y-m-d');
        } catch (\Exception $e) {
            // Si no se puede analizar la fecha con Carbon, continuamos con otros intentos
        }

        // Definir algunos formatos manualmente para fechas más específicas
        $formats = [
            'd-m-Y',        // 29-01-2024
            'd-M-Y',        // 29-ene-2024
            'd-F-Y',        // 29-enero-2024
            'F-d-Y',        // enero-29-2024
            'Y-m-d',        // 2024-01-29
            'Y-m-d H:i:s',  // 2024-01-29 15:00:00
            'd/m/Y',        // 29/01/2024
            'd/m/Y H:i',    // 29/01/2024 15:00
        ];

        // Intentar con cada formato
        foreach ($formats as $format) {
            try {
                $date = Carbon::createFromFormat($format, $userDate);
                // Si se logra parsear, devolvemos la fecha en el formato estándar de base de datos
                return $date->format('Y-m-d');
            } catch (\Exception $e) {
                // Continuamos con el siguiente formato si este no es válido
                continue;
            }
        }

        // Si no se pudo parsear la fecha, retornar null o un valor predeterminado
        return null;
    }


    //Validación de Formato de Fechas

    private function validarFormatoFecha($datos)
    {
        $registrosconFechamalForma = [];

        foreach ($datos as $indice => $fila) {
            // Limpiar el campo 'identidad' (sin guiones)
            $datos[$indice]['identidad'] = str_replace('-', '', $fila['identidad']);
            $lineNumber = $indice + 1;
            // Comprobar si ambas fechas (fecha_nacimiento y fechaIngreso) están presentes
            if (!empty($fila['fecha_nacimiento']) && !empty($fila['fechaIngreso'])) {
                // Intentamos formatear ambas fechas utilizando la función 'formatUserDate'
                $fecha_nacimiento = $this->formatUserDate($fila['fecha_nacimiento']);
                $fechaIngreso = $this->formatUserDate($fila['fechaIngreso']);

                // Si las fechas son válidas, las asignamos
                if ($fecha_nacimiento && $fechaIngreso) {
                    $datos[$indice]['fecha_nacimiento'] = $fecha_nacimiento;
                    $datos[$indice]['fechaIngreso'] = $fechaIngreso;
                } else {
                    // Si no se pudo convertir las fechas, agregar al registro de error
                    // Si no se pudo convertir alguna de las fechas, agregar al registro de error indicando qué campo es el que falló
                    $mensajeError = 'Las fechas deben estar en un formato válido (dd/mm/yyyy, mm/dd/yyyy, o similares)';

                    if (!$fecha_nacimiento) {
                        $mensajeError .= ' - Error en "fecha_nacimiento". Valor recibido: ' . $fila['fecha_nacimiento'];
                    }
                    if (!$fechaIngreso) {
                        $mensajeError .= ' - Error en "fechaIngreso". Valor recibido: ' . $fila['fechaIngreso'];
                    }
                    $registrosconFechamalForma[] = [
                        'identidad' => $datos[$indice]['identidad'],
                        'nombre' => $datos[$indice]['nombre'],
                        'mensaje' =>  $mensajeError,
                        'LineNumber' => $lineNumber,
                    ];
                }
            }
        }

        // Devolver los datos con las fechas corregidas y los registros con fecha mal formateada
        return [$datos, $registrosconFechamalForma];
    }

    /**
     * un campo bloqueo_recomendacion en la tabla Candidatos que indica si un candidato tiene una restricción de recomendación.
     * Si el bloqueo de recomendación se obtiene de otra tabla o de un conjunto de datos diferente, 
     * dime de dónde proviene $validarRecomendacion en tu código original y ajusto la función según corresponda. 🚀
     */
    private function tieneBloqueoRecomendacion($identidad)
    {
        return Ingresos::where('identidad', $identidad)->where('bloqueo_recomendado', 's')->exists();
    }


    private function crearOActualizarCandidato($candidatoData)
    {
        $existingCandidato = Candidatos::where('identidad', $candidatoData['identidad'])->first();

        if ($existingCandidato) {
            // Si el candidato tiene estado 'x' o está bloqueado, no actualizar
            if ($existingCandidato->activo === 'x' || $this->tieneBloqueoRecomendacion($candidatoData['identidad'])) {
                return ['estado' => 'solicitar Informacion RH ALTIA', 'candidato' => $existingCandidato];
            }

            // Si el candidato está activo, actualizar a 'n'
            if ($existingCandidato->activo === 's') {
                $existingCandidato->update(['activo' => 'n']);
                return ['estado' => 'registro actualizado', 'candidato' => $existingCandidato];
            }

            return ['estado' => 'registro activo', 'candidato' => $existingCandidato];
        }

        // Si no existe, crearlo con 'activo' => 'n'
        $nuevoCandidato = Candidatos::create(array_merge($candidatoData, ['activo' => 'n']));
        return ['estado' => 'registro nuevo', 'candidato' => $nuevoCandidato];
    }


    private function insertarCandidatosMasivos($datos)
    {
        $resultados = [];

        foreach ($datos as $candidatoData) {
            $resultado = $this->crearOActualizarCandidato($candidatoData);
            $resultados[] = [
                'identidad' => $candidatoData['identidad'],
                'estado' => $resultado['estado'],
                'nombre' => $candidatoData['nombre'] . ' ' . $candidatoData['apellido']
            ];
        }

        return $resultados;
    }


    private function validarEstadoIngreso($identidad, $idEmpresaActual)
    {
        // Validar si el candidato tiene recomendación negativa
        $recomendado = Ingresos::where('identidad', $identidad)
            ->where('recomendado', 'n')
            ->exists();

        if ($recomendado) {
            return 'Pedir información a Recursos Humanos ALTIA';
        }

        // Verificar si existe en otra empresa activa
        $existeOtraEmpresa = Ingresos::where('identidad', $identidad)
            ->where('id_empresa', '!=', auth()->user()->empresa_id)
            ->where('activo', 's')
            ->exists();

        if ($existeOtraEmpresa) {
            return 'Ya registrado en otra empresa';
        }

        // Verificar si existe en la misma empresa y está activo
        $existeMismaEmpresa = Ingresos::where('identidad', $identidad)
            ->where('id_empresa', auth()->user()->empresa_id)
            ->where('activo', 's')
            ->exists();

        if ($existeMismaEmpresa) {
            return 'Ya existe en la misma empresa';
        }

        // Verificar si existe en la misma empresa pero está inactivo
        $existeInactivoMismaEmpresa = Ingresos::where('identidad', $identidad)
            ->where('id_empresa', auth()->user()->empresa_id)
            ->where('activo', 'n')
            ->first(); // Usamos first() en lugar de exists() para obtener el registro

        if ($existeInactivoMismaEmpresa) {
            return 'Recontratado';
        }

        // Si pasa todas las validaciones, es un nuevo ingreso
        return 'Registro nuevo';
    }






    private function crearOActualizarIngreso($ingresoData)
    {
        return Ingresos::create([
            'identidad' => $ingresoData['identidad'],
            'id_empresa' => $ingresoData['id_empresa'],
            'fechaIngreso' => $ingresoData['fechaIngreso'],
            'area' => $ingresoData['area'],
            'id_puesto' => $ingresoData['id_puesto'],
            'activo' => 's' // Se marca como activo el nuevo ingreso
        ]);
    }


    private function insertarIngresosMasivos($datos)
    {
        $ingresosCreados = [];
        $datosRegistroestado = [];

        foreach ($datos as $ingresoData) {
            $estadoIngreso = $this->validarEstadoIngreso($ingresoData['identidad'], $ingresoData['id_empresa']);

            if ($estadoIngreso === 'Registro nuevo' || $estadoIngreso === 'Recontratado') {
                DB::beginTransaction();
                try {
                    $ingresoCreado = $this->crearOActualizarIngreso($ingresoData);
                    DB::commit();
                    $ingresosCreados[] = $ingresoCreado;
                } catch (\Exception $e) {
                    DB::rollBack();
                    return response()->json(['error' => 'Error al guardar los ingresos'], 500);
                }
            }

            $datosRegistroestado[] = [
                'identidad' => $ingresoData['identidad'],
                'estadoIngreso' => $estadoIngreso,
                'nombre' => $ingresoData['nombre'] . ' ' . $ingresoData['apellido']
            ];
        }

        return [
            'ingresos' => $ingresosCreados,
            'estados' => $datosRegistroestado
        ];
    }





    public function importarIngresos(Request $request)
    {
        $datos = [];
        $registrosProblematicos = [];
        $transactionId = null;
        DB::beginTransaction(); // Iniciar transacción
        try {

            //  $datos = $this->processData($request);
            $datos = $this->processData2($request);


            // O btener ID de la transacción en MySQL
            $transactionId = DB::select("SELECT CONNECTION_ID() AS connection_id")[0]->connection_id;

            $importedFields = array_keys($datos[0]);
            $candidato = new Candidatos();
            $ingresos = new Ingresos();

            $table_candidate = array_diff(Schema::getColumnListing($candidato->getTable()), ['comentarios', 'id', 'updated_at', 'created_at', 'activo']);
            $table_input = ['id_empresa', 'fechaIngreso', 'area', 'id_puesto'];

            $columnHeaders = array_merge($table_input, $table_candidate);

            // Validar campos insuficientes
            $indicesConMenosDe13Campos = $this->validarCamposInsuficientes($datos, $columnHeaders);
            if (count($indicesConMenosDe13Campos) > 0) {

                return response()->json([
                    'error' => 'Campos insuficientes',
                    'indices' => $indicesConMenosDe13Campos,
                    'tipoError' => 'datos',
                ], 400);
            }

            // Validar formato de fechas
            list($datos, $registrosconFechamalForma) = $this->validarFormatoFecha($datos);

            if (count($registrosconFechamalForma) > 0) {
                return response()->json([
                    'error' => "Formato de fecha incorrecto en el registro",
                    'indice' => $registrosconFechamalForma,
                    'tipoError' => 'fecha',
                ], 400);
            }

            // Guardar inicio de la transacción en el log
            $logId = DB::table('transactions_logo')->insertGetId([
                'identidad' => null,
                'tipo_transaccion' => 'inicio',
                'estado' => 'en progreso',
                'mensaje' => "Inicio de transacción ID: $transactionId",
                'created_at' => now()
            ]);

            // Insertar candidatos masivamente
            $candidatosCreados = $this->insertarCandidatosMasivos($datos, $candidato);

            foreach ($candidatosCreados as $candidato) {
                DB::table('transactions_logo')->insert([
                    'identidad' => $candidato['identidad'],
                    'tipo_transaccion' => 'candidato',
                    'estado' => $candidato['estado'],
                    'mensaje' => "Candidato procesado en transacción ID: $transactionId",
                    'created_at' => now()
                ]);
            }

            // Insertar ingresos masivamente
            $ingresosCreados = $this->insertarIngresosMasivos($datos, $ingresos);

            foreach ($ingresosCreados['ingresos'] as $ingreso) {
                // dd($ingreso);
                DB::table('transactions_logo')->insert([
                    'identidad' => $ingreso->identidad,
                    'tipo_transaccion' => 'ingreso',
                    'estado' => 'Ingreso registrado',
                    'mensaje' => "Ingreso procesado en transacción ID: $transactionId",
                    'created_at' => now()
                ]);
            }

            DB::commit(); // Confirmar la transacción si todo salió bien

            return response()->json(['candidatos' => $candidatosCreados, 'ingresos' => $ingresosCreados, 'status' => 202]);
        } catch (Exception $e) {
            DB::rollBack(); // Revertir la transacción en caso de error
            $registrosProblematicos[] = ['registro' => $datos, 'error' => $e->getMessage()];
            $transactionId = DB::select("SELECT CONNECTION_ID() AS connection_id")[0]->connection_id;

            DB::table('transactions_logo')->insert([
                'identidad' => null,
                'tipo_transaccion' => 'error',
                'estado' => 'fallido',
                'mensaje' => "Error en transacción ID $transactionId: " . $e->getMessage(),
                'created_at' => now()
            ]);
            return response()->json(['error' => $e->getMessage(), 'tipoError' => 'exception'], 400);
        }
    }



    /**
     * Funcion que permitira procesar la importacion masiva de registros para la tabla candidatos, si no existe el campo activo se iniciara en s por la disponibilidad
     */
    public function recibirCsvCandidatos(Request $request)
    {
        $nuevoRegistros = [];
        $datosCombinados = [];
        $datos = [];
        if ($request->hasFile('archivo_csv')) {
            $datos = $this->processData($request);
            $importedFields = array_keys($datos[0]);
            $candidato = new Candidatos();

            $ingresos = new Ingresos();

            $missingFields = $this->compareFields($importedFields, $candidato->getTable());

            $vlor = is_array($missingFields);
            $missingFields2 = $this->compareFields($importedFields, $ingresos->getTable());
            $vlor2 = is_array($missingFields2);

            //validar que todos los campos sean obligatorio
            if ($vlor) {
                return redirect()->back()->with(['missingFields' => $missingFields], 400);
            }
            foreach ($datos as $indice => $fila) {
                if (!empty($fila['fecha_nacimiento'])) {
                    $fecha_nacimiento = date_create_from_format("m/d/Y", $fila['fecha_nacimiento']);
                    $datos[$indice]['fecha_nacimiento'] = date_format($fecha_nacimiento, 'Y-m-d');
                }

                $datos[$indice]['activo'] = 's';
            }

            $datos_filtrados = array_filter($datos, function ($item) {
                $noEmpty = count($item) == count((array_filter(array_map('trim', $item))));
                return $noEmpty;
            });


            try {

                foreach ($datos_filtrados as $dt) {
                    $existe = Candidatos::where('identidad', $dt['identidad'])->exists();

                    if (!$existe) {
                        Candidatos::create($dt);
                        $dt['estado'] = 'exitoso';
                        $nuevoRegistros = [
                            'datos' => $dt,

                        ];
                    } else {
                        $dt['estado'] = 'error';
                        $nuevoRegistros = [
                            'datos' => $dt,
                        ];
                    }

                    $datosCombinados[] = $nuevoRegistros;
                }

                return redirect()->back()->with('mensaje', 'Archivo CSV subido con éxito')->with('datos', $datosCombinados);
            } catch (Exception $e) {
                $registrosProblematicos[] = ['registro' => $fila, 'error' => $e->getMessage()];
                return redirect()->back()->with('mensaje', 'Archivo CSV subido con éxito')->with('datos', $datos);
            }


            // Retorna una respuesta, por ejemplo, un mensaje de éxito

        } else {
            // Si no se adjunta un archivo CSV, devuelve un mensaje de error
            return redirect()->back()->with('error', 'No se ha adjuntado un archivo CSV');
        }
    }






    /**
     * Función que permite obtener la informacion individual del colaborador, se obtiene informacion personal, informacion laboral y se manda la informacion de la empresa 
     * que esta consultando, funciona con tecnica ajax.
     * El tipo de solicitud es GET, recibe como parametro la IDENTIDAD del colaborador a consultar
     */
    public function GetIndividualInfo($dni)
    {
        /**
         * cuando se hace la consulta se tiene que validar 
         * que el usuario tenga el campo de fecha de egreso vacio
         */

        // quitar de la tabla egreso_ingresos el campo activo, y agregarlo en la tabla de candidatos y que se actualice cada vez que se haga cualquiera de las dos acciones
        $newdni = str_replace('-', '', $dni);

        //consultar la informacion personal del candidato.
        $candidatos = Candidatos::where('identidad', $newdni)->first();

        //dd($newdni);
        /* $personalInfo = Ingresos::where('identidad','=', $newdni)
            ->join('puestos', 'egresos_ingresos.id_puesto', '=', 'puestos.id')
            ->orderBy('egresos_ingresos.created_at', 'desc')
            ->get();*/

        $personalInfo = Ingresos::where('egresos_ingresos.identidad', '=', $newdni)
            ->leftJoin('puestos', 'egresos_ingresos.id_puesto', '=', 'puestos.id')
            ->leftJoin('empresas', 'egresos_ingresos.id_empresa', '=', 'empresas.id')
            ->select(
                'egresos_ingresos.*',
                'puestos.*',
                'empresas.*'
            )
            ->orderBy('egresos_ingresos.created_at', 'desc')
            ->get();

        // Validar que al menos exista el candidato
        if (!$candidatos && $personalInfo->isEmpty()) {
            return response()->json([
                'response' => 'No se encontró información para el DNI proporcionado',
                'code' => 404
            ], 404);
        }

        $empresas = Empresas::all();

        $laboralInfo = [];



        if (!$personalInfo->isEmpty() && $candidatos) {

            foreach ($personalInfo as $pi) {
                if ($pi['identidad'] == $newdni) {
                    $laboralInfo[] = $pi;
                }
            }

            // Si se encontraron datos, retornar la vista con los datos
            return view('consultaficha', ['laboralInfo' => $laboralInfo, 'candidatos' => $candidatos, 'DatosEmpresa' => $empresas]);
        } else {
            // Si no se encontraron datos, retornar un mensaje de error
            $message = [
                'response' => 'No se encontro informacion',
                'code' => 404
            ];
            return response()->json($message);
        }
    }
    /**
     * Funcion que permite procesar la actualizacion de un colaborador, la informacion se captura en con un evento click del boton de la ficha personal.
     */
    public function Actualizacion_ficha(Request $re)
    {
        $candidatos = Candidatos::find($re->input('id'));
        $candidatos->direccion = $re->input('direccion');
        $candidatos->telefono = $re->input('telefono');
        $candidatos->correo = $re->input('correo');
        $candidatos->save();
        if ($candidatos->wasChanged()) {
            event(new RegistroActualizado($candidatos));
            return response()->json(['mensaje' => 'se ha actualizado con exito!', 'icon' => 'success', 'titulo' => 'Actualizacion Exitosa']);
        } else {
            return response()->json(['mensaje' => 'No se pudo actualizar los datos!', 'icon' => 'warning', 'titulo' => 'Hubo un error!']);
        }
    }

    public function insertarCandidato(Request $request)
    {
        try {
            //code...
            $this->validate($request, [
                'identidad' => 'required|regex:/^[0-9-]+$/u'
            ]);

            $identidad = str_replace('-', '', $request->input('identidad'));
            $matchID = Candidatos::where('identidad', $identidad)->get();

            if ($matchID->count() <= 0) {

                $candidato = [
                    'identidad' => $identidad,
                    'nombre' => $request->input('nombre'),
                    'apellido' => $request->input('apellido'),
                    'telefono' => $request->input('telefono'),
                    'correo' => $request->input('correo'),
                    'direccion' => $request->input('direccion'),
                    'generoM_F' => $request->input('generoM_F'),
                    'fecha_nacimiento' => $request->input('fecha_nacimiento'),
                    'activo' => 's',

                ];
                $newCandidate = Candidatos::create($candidato);


                return redirect()->back()->with(['mensaje' => 'se a creado el nuevo registro ']);
            } else {
                return redirect()->back()->with(['mensaje' => 'ya existe registro']);
            }
        } catch (Exception $exception) {
            return redirect()->back()->with(['mensaje' => 'se ha producido un error: =>'.$exception->getMessage()]);
        }
    }



    public function recibirIngresos(Request $request)
    {

        $datos = [];
        $registrosIngreso = [];
        $registrosProblematicos = [];
        $fila2 = [];
        $estado = '';
        $estadoIngreso = '';
        $indicesConMenosDe13Campos = [];
        try {
            $datos = $this->processData($request);

            $importedFields = array_keys($datos[0]);
            $candidato = new Candidatos();
            $ingresos = new Ingresos();

            $table_candidate = array_diff(Schema::getColumnListing($candidato->getTable()), ['comentarios', 'id', 'updated_at', 'created_at', 'activo']);
            $table_input = ['id_empresa', 'fechaIngreso', 'area', 'id_puesto'];

            $columnHeaders = array_merge($table_input, $table_candidate);

            //validar que todos los campos sean obligatorio



            foreach ($datos as $indice => $subArray) {
                // Si el número de campos en el subarray es menor que el número de columnas en $columnHeaders
                if (count($subArray) < count($columnHeaders) + 1) {

                    // Encontrar cuáles campos faltan
                    $camposFaltantes = array_diff($columnHeaders, array_keys($subArray));

                    // Agregar a la lista de resultados
                    $indicesConMenosDe13Campos[] = [
                        'identidad' => $datos[$indice]['identidad'],
                        'nombre' => $datos[$indice]['nombre'],
                        'campos_faltantes' => $camposFaltantes
                    ];
                }
            }


            $tieneContenido = count($indicesConMenosDe13Campos) !== 0;
            if ($tieneContenido) {
                return response()->json([
                    'error' => 'Campos insuficientes',
                    'indices' => $indicesConMenosDe13Campos,
                ], 400);
            }

            $registrosconFechamalForma = [];
            foreach ($datos as $indice => $fila) {

                $datos[$indice]['identidad'] = str_replace('-', '', $fila['identidad']);
                if (!empty($fila['fecha_nacimiento']) && !empty($fila['fechaIngreso'])) {

                    if (strpos($fila['fecha_nacimiento'], '/') > 0 && strpos($fila['fechaIngreso'], '/') > 0) {
                        //  dd($datos);
                        $fecha_nacimiento = date_create_from_format("m/d/Y", $fila['fecha_nacimiento']);
                        $fechaIngreso = date_create_from_format("m/d/Y", $fila['fechaIngreso']);
                        if (!$fecha_nacimiento && !$fechaIngreso) {
                            $fecha_nacimiento = date_create_from_format("d/m/Y", $fila['fecha_nacimiento']);
                            $fechaIngreso = date_create_from_format("d/m/Y", $fila['fechaIngreso']);
                        }

                        if ($fecha_nacimiento && $fechaIngreso) {
                            $datos[$indice]['fecha_nacimiento'] = date_format($fecha_nacimiento, 'Y-m-d');
                            $datos[$indice]['fechaIngreso'] = date_format($fechaIngreso, 'Y-m-d');
                        }
                    } else {

                        $registrosconFechamalForma[] = ['identidad' => $datos[$indice]['identidad'], 'nombre' => $datos[$indice]['nombre']];

                        //capturando la excepcion

                    }
                }
            }



            if (count($registrosconFechamalForma) > 0) {
                return response()->json([
                    'error' => "Formato de fecha incorrecto en el registro en los registros",
                    'indiceFecha' => $registrosconFechamalForma
                ], 400);
            }


            /**
             * Creando primero los registros para los candidatos
             */
            $datosConEstado = [];
            $datosRegistroestado = [];
            /**
             * para evitar hacer muchas consultas en el ciclo, haremos uso de la clausula whereIn, volcando 
             * la informacion en un @var collect
             */
            $identidades = array_column($datos, 'identidad');
            $validarExistencia = Candidatos::whereIn('identidad', $identidades)->get();
            $validarXcandidato = Candidatos::select('identidad')->whereIn('identidad', $identidades)->where('activo', '=', 'x')->get();

            /**
             * Validar si existe algun bloque de recomendacion
             */

            $validarRecomendacion = Ingresos::whereIn('identidad', $identidades)
                ->where('recomendado', 'n')
                ->get();


            $candidatosNuevos = [];
            foreach ($datos as $dt) {

                $existeCandidato = $validarExistencia->containsStrict('identidad', $dt['identidad']);
                $tieneXcandidato = $validarXcandidato->containsStrict('identidad', $dt['identidad']);
                $tieneBloqueoRecomendacion = $validarRecomendacion->containsStrict('identidad', $dt['identidad']);
                //sino existe que proceda a crearlo caso contrario solo actualizar el campo activo en la tabla Candidatos 
                if (!$existeCandidato && !$tieneXcandidato && !$tieneBloqueoRecomendacion) {
                        // Crea un nuevo candidato si no existe
                    /**
                     * capturar los registros nuevos en un array para hacer una insercion masiva.
                     */
                    $candidatosNuevos[] = array_merge(
                        array_diff_key($dt, array_flip(['fechaIngreso', 'area', 'id_empresa', 'validacion', 'id_puesto', 'Comentario', 'activo', 'comentario'])),
                        ['identidad' => $dt['identidad'], 'activo' => 'n', 'created_at' => date('Y-m-d h:m:s', time()), 'updated_at' => date('Y-m-d h:m:s', time())]
                    );
                    $estado = 'registro nuevo';
                } else {
                    /*
                        caso contrario el registro actualizara en el campo @var activo
                        */
                    if ($tieneXcandidato || $tieneBloqueoRecomendacion) {
                        $estado = 'solicitar Informacion RH ALTIA';
                    } else {
                        $updateCandidate = Candidatos::where('identidad', '=', $dt['identidad'])
                            ->where('activo', '=', 's')
                            ->update(['activo' => 'n']);
                        if ($updateCandidate) {
                            $estado = 'registro actualizado';
                        } else {
                            $estado = 'registro activo';
                        }
                    }
                }

                $filaConEstado = [
                    'identidad' => $dt['identidad'],
                    'estado' => $estado,
                    'nombre' => $dt['nombre'] . ' ' . $dt['apellido'],
                ];

                $datosConEstado[] = $filaConEstado;
            }

            /**
             * procesado la data que viene del archivo csv y validar la informacion se procede a hacer la insercion masiva
             */
            DB::beginTransaction();
            try {

                Candidatos::insert($candidatosNuevos);

                DB::commit();
            } catch (Exception $e) {
                DB::rollBack();
                $error = ['error' => $e->getMessage()];
            }


            /**
             * Validar si existe en otra empresa antes de hacer el ingro
             */
            $validarExistenteEnOtraEmpresa = Ingresos::whereIn('identidad', $identidades)
                ->where('id_empresa', '!=', auth()->user()->empresa_id) // Excluir la empresa actual
                ->where('activo', 's')
                ->get();
            /**
             * Validar si el ingreso existe en la misma empresa y si esta activo
             */
            $validarExistenteEnMismaEmpresa = Ingresos::whereIn('identidad', $identidades)
                ->where('id_empresa', auth()->user()->empresa_id)
                ->where('activo', 's')
                ->get();
            /**
             * Validar si existe en la misma empresa pero esta inactivo, 
             * si esta inactivo, significa que debe hacer un ingreso nuevo en la misma empresa
             */
            $validarInactivoEmpresa = Ingresos::whereIn('identidad', $identidades)
                ->where('id_empresa', auth()->user()->empresa_id)
                ->where('activo', 'n')
                ->get();


            /**
             * Validar x en el candidato, si contiene x pedir informacion a Recursos humanos de ALTIA
             */



            foreach ($datos as $dt) {
                $identidad = $dt['identidad'];
                $idEmpresaActual = $dt['id_empresa'];
                //verificar si existe en otra empresa

                // Verificar si el ingreso está activo en alguna otra empresa
                $ingresoExistenteEnOtraEmpresa = $validarExistenteEnOtraEmpresa->containsStrict('identidad', $identidad);

                // Verificar si el ingreso está activo en la misma empresa
                $ingresoExistenteEnMismaEmpresa = $validarExistenteEnMismaEmpresa->containsStrict('identidad', $identidad);
                $ingresoXactivoCandidato = $validarXcandidato->containsStrict('identidad', $identidad);
                $ingresoNorecomendado = $validarRecomendacion->containsStrict('identidad', $identidad);
                // Evaluar si es un registro nuevo
                if ((!$ingresoExistenteEnMismaEmpresa && !$ingresoExistenteEnOtraEmpresa) && !$ingresoXactivoCandidato && !$ingresoNorecomendado) {
                    // No existe en la misma empresa ni en otra empresa, crear un nuevo registro
                    $ingresoInactivoEnMismaEmpresa = $validarInactivoEmpresa->containsStrict('identidad', $identidad);

                    if (!$ingresoInactivoEnMismaEmpresa) {
                        DB::beginTransaction();
                        $ingreso = Ingresos::firstOrCreate(
                            ['identidad' => $identidad, 'id_empresa' => $idEmpresaActual],
                            [
                                'fechaIngreso' => $dt['fechaIngreso'],
                                'area' => $dt['area'],
                                'id_puesto' => $dt['id_puesto'],
                                'activo' => 's'
                            ]
                        );
                        DB::commit();

                        $estadoIngreso = 'Registro nuevo';
                    } else {
                        if (!$ingresoXactivoCandidato) {
                            DB::beginTransaction();
                            $ingreso = Ingresos::create(

                                [
                                    'identidad' => $identidad,
                                    'id_empresa' => $idEmpresaActual,
                                    'fechaIngreso' => $dt['fechaIngreso'],
                                    'area' => $dt['area'],
                                    'id_puesto' => $dt['id_puesto'],
                                    'activo' => 's'
                                ]
                            );
                            DB::commit();
                            $estadoIngreso = 'Recontratado';
                        } else {
                            $estadoIngreso = 'No hubo ingreso';
                        }
                    }
                } else {
                    // Ya existe en la misma empresa o en otra empresa



                    if ($ingresoExistenteEnMismaEmpresa) {
                        $estadoIngreso = 'Ya existe en la misma empresa';
                    } else {
                        if ($ingresoExistenteEnOtraEmpresa) {
                            $estadoIngreso = 'Ya registrado en otra empresa';
                        }
                    }

                    if ($ingresoXactivoCandidato) {
                        $estadoIngreso = 'No se hizo el ingreso';
                    }

                    if ($ingresoNorecomendado) {
                        $estadoIngreso = 'Pedir informacion a Recursos Humanos ALTIA';
                    }
                }

                $datosRegistroestado[] = [
                    'identidad' => $dt['identidad'],
                    'estadoIngreso' => $estadoIngreso
                ];







                // $dt['estado'] = $estado;

                $registrosIngreso[] = $dt;
            }


            foreach ($datosConEstado as $dst => $dt) {

                foreach ($datosRegistroestado as $st) {
                    if ($st['identidad'] === $dt['identidad']) {
                        $datosConEstado[$dst]['estadoIngreso'] = $st['estadoIngreso'];
                    }
                }
            }

            // dd($datosConEstado);


            return response()->json(['incomeJobs' => $datosConEstado, 'status' => 202]);
        } catch (Exception $e) {
            $registrosProblematicos[] = ['registro' => $fila2, 'error' => $e->getMessage()];



            return response()->json(['error' => $e->getMessage()], 400);
        }


        // Retorna una respuesta, por ejemplo, un mensaje de éxito


    }

    public function exportarEgresos(Request $request)
    {
        // Obtener todos los datos de la solicitud como un arreglo asociativo
        $data = $request->all();

        // Verificar si se recibieron datos y extraer las identidades del arreglo
        if (!empty($data)) {
            $exportarEgresos = [];

            // Extraer las identidades del arreglo
            foreach ($data['egresosNuevos'] as $dt) {
                $egresos = Egresos::select('egresos_ingresos.id', DB::raw("CONCAT(candidatos.nombre, ' ', candidatos.apellido) AS nombre_completo"), 'egresos_ingresos.identidad', 'egresos_ingresos.fechaIngreso')
                    ->join('candidatos', 'egresos_ingresos.identidad', 'candidatos.identidad')
                    ->where('egresos_ingresos.identidad', $dt)
                    ->where('candidatos.activo', '=', 'n')
                    ->first();
                $exportarEgresos[] = $egresos;
            }

            $bookExporter = 'egresos.xlsx';
            if (!is_null($exportarEgresos) || !empty($exportarEgresos)) {
                return Excel::download(new ExportTemplateOut($exportarEgresos), $bookExporter);
            } else {
                return response()->json(['error' => 'error al exportar archivo', 'status' => 200]);
            }
        }

        // En caso de que no se procese correctamente la solicitud
        return response()->json(['error' => 'Datos no válidos'], 400);
    }


    /**
     * Funcion para importar candidatos desde un archivo csv, 
     * el procesamiento y actualizacion de los egresos de una empresa,
     * Paso 1 obtener data de una peticion ajax
     */

    public function importarEgresos(Request $request)
    {
        $datos = [];
        try {
            $datos = $this->processData($request);

            foreach ($datos as $indice => $fila) {
                /**
                 * Validando y formateando los datos del tipo fecha
                 */
                $datos[$indice]['identidad'] = str_replace('-', '', $fila['identidad']);
                $datos[$indice]['identidad'] = str_replace(' ', '', $fila['identidad']);
                if (!empty($fila['fechaEgreso'])) {
                    if (strpos($fila['fechaEgreso'], '/') > 0) {

                        $fecha_egreso = date_create_from_format("m/d/Y", $fila['fechaEgreso']);

                        if (!$fecha_egreso) {
                            $fecha_egreso = date_create_from_format("d/m/Y", $fila['fechaEgreso']);
                        }

                        if ($fecha_egreso) {
                            $datos[$indice]['fechaEgreso'] = date_format($fecha_egreso, 'Y-m-d');
                        }
                    } else {

                        //capturando la excepcion
                        throw new Exception("Formato de fecha incorrecto en la fila $indice");
                    }
                }
            }


            $jsonData = json_encode($datos);
            if (DB::statement('CALL SPupdate_candidatos_egresos_from_json(?)', [$jsonData])) {
                return response()->json(['success' => 'se realizo con exito la actualizacion', 'icon' => 'success', 'status' => 202]);
            } else {
                return response()->json(['error' => 'No se puedo actualizar la informacion', 'icon' => 'warning', 'status' => 404]);
            }
        } catch (Exception $e) {
            //throw $th;
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function hacerIngreso(Request $request)
    {
        try {

            $existemismaEmpresa = Ingresos::where('identidad', $request->input('identidad'))
                ->where('id_empresa', $request->input('id_empresa'))
                ->where('activo', '=', 's')
                ->get();
            $candidato = Candidatos::where('identidad', $request->input('identidad'))->get();
            if ($existemismaEmpresa->count() > 0) {

                return redirect()->back()->with(['mensaje' => 'ya existe en esta compañia'])->with(['icon' => 'warning']);
                // return response()->json(['mensaje'=>'ya existe en esta compañia','icon'=>'warning']);
            } else {

                $ingreso = new Ingresos();
                $ingreso->identidad = $request->input('identidad');
                $ingreso->id_empresa = $request->input('id_empresa');
                $ingreso->area = $request->input('area');
                $ingreso->id_puesto = $request->input('id_puesto');
                $ingreso->activo = 's';
                $ingreso->fechaIngreso = $request->input('fecha_ingreso');
                $ingreso->Comentario = $request->input('comentarios');
                $ingreso->save();

                $updateCandidato = Candidatos::find($candidato[0]['id']);
                $updateCandidato->activo = 'n';
                $updateCandidato->save();

                return redirect()->back()->with(['mensaje' => 'fue ingresado con exito'])->with(['icon' => 'success']);
            }
        } catch (Exception $e) {
            return redirect()->back()->with(['mensaje' => $e->getMessage(), 'icon' => 'danger']);
        }
    }

    /*Metodo para hacer un egreso */
    public function hacerEgreso(Request $request)
    {
        try {


            if (!is_null($request)) {
                $existemismaEmpresa = Egresos::where('identidad', $request->input('identidad'))
                    ->where('id_empresa', $request->input('id_empresa'))
                    ->where('activo', '=', 's')
                    ->get();

                $candidato = Candidatos::where('identidad', $request->input('identidad'))->get();

                if ($existemismaEmpresa->count() > 0 && $candidato->count() > 0) {
                    $updateRegistro = Egresos::find($existemismaEmpresa[0]['id']);
                    $updateRegistro->fechaEgreso = $request->input('tiempo');
                    $updateRegistro->activo = 'n';
                    $updateRegistro->Comentario = $request->input('comentarios');
                    $updateRegistro->recomendado = $request->input('recomendado');
                    $updateRegistro->forma_egreso = $request->input('forma_egreso');
                    $updateRegistro->tipo_egreso = $request->input('tipo_egreso');
                    $updateRegistro->save();

                    $updateCandidato = Candidatos::find($candidato[0]['id']);
                    $updateCandidato->activo = 's';
                    $updateCandidato->save();

                    return redirect()->back()->with(['msjIngreso' => 'ya fue actualizado el registro ']);
                    // return response()->json(['mensaje'=>'ya existe en esta compañia','icon'=>'warning']);
                } else {



                    return redirect()->back()->with(['mensaje' => 'fue ingresado con exito'])->with(['icon' => 'success']);
                }
                //code...


            } else {
                return response()->json(['mensaje' => 'es nulo']);
            }
        } catch (Exception $e) {
            return response()->json($e->getMessage());
        }
    }

    public function lockCandidate(Request $request)
    {
        try {
            //Validacion de los campos con la informacion proveniente del fomulario de Bloqueo Candidatos
            $request->validate([
                'identidad' => 'required|string',
                'prohibirIngreso' => 'required|string',
                'ComenProhibir' => 'required|string',
            ]);

            $empresaId = auth()->user()->empresa_id;
            $identidad = $request->input('identidad');

            if (!is_null($request)) {
                //obteniendo el ultimo registro de la tabla egresos_ingresos
                $ultimoEgreso = Egresos::where('identidad',)
                    ->where('id_empresa', $empresaId)
                    ->where('activo', '=', 's')
                    ->get()
                    ->last();
                // dd ($existemismaEmpresa);
                $candidato = Candidatos::where('identidad', $identidad)->first();
                if ($ultimoEgreso || $candidato) {

                    if ($ultimoEgreso) {

                        $ultimoEgreso->activo = 'n';
                        $ultimoEgreso->prohibirIngreso = $request->input('prohibirIngreso');
                        $ultimoEgreso->ComenProhibir = $request->input('ComenProhibir');
                        $ultimoEgreso->save();
                    }


                    if ($candidato) {

                        $comentarioActual = $request->input('ComenProhibir');
                        $comentariosAnteriores = json_decode($candidato->comentarios, true);
                        $nuevosComentarios = [

                            'comentarios' => $comentarioActual,

                            'fechaBloqueo' => now()
                        ];

                        $comentariosActualizados = [];
                        // Si hay comentarios anteriores, agregarlos al nuevo arreglo
                        // Agregar el nuevo comentario al inicio del historial
                        if (is_array($comentariosAnteriores)) {
                            $comentariosActualizados = $comentariosAnteriores;
                        }

                        array_unshift($comentariosActualizados, $nuevosComentarios);

                        $candidato->comentarios = json_encode($comentariosActualizados);
                        $candidato->activo = 'x';
                        $candidato->save();
                    }

                    return redirect()->back();
                } else {
                    return response()->json(['mensaje' => 'no se encontro registro']);
                }
            } else {
                return response()->json(['mensaje' => 'no envio los datos']);
            }
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->validator->getMessageBag())->withInput();
        }
    }

    public function unlockCandidate(Request $request)
    {
        try {
            $request->validate([
                'identidad' => 'required|string',
                'prohibirIngreso' => 'required|string',
                'ComenProhibir' => 'required|string',
            ]);

            if (!is_null($request)) {
                $ultimoEgreso = Egresos::where('identidad', $request->input('identidad'))
                    ->where('activo', '=', 'n')
                    ->get()
                    ->last();

                $candidato = Candidatos::where('identidad', $request->input('identidad'))->first();
                if (($ultimoEgreso || is_null($ultimoEgreso)) && $candidato) {
                    if ($ultimoEgreso) {
                        $updateRegistro = Egresos::find($ultimoEgreso->id);
                        $updateRegistro->activo = 'n';
                        $updateRegistro->prohibirIngreso = $request->input('prohibirIngreso');
                        $updateRegistro->ComenProhibir = $request->input('ComenProhibir');
                        $updateRegistro->save();
                    }

                    if ($candidato) {

                        $comentarioActual = $request->input('ComenProhibir');
                        $comentariosAnteriores = json_decode($candidato->comentarios, true);
                        $nuevosComentarios = [

                            'comentarios' => $comentarioActual,

                            'fechaDesbloqueo' => now()
                        ];

                        $comentariosActualizados = [];
                        // Si hay comentarios anteriores, agregarlos al nuevo arreglo
                        // Agregar el nuevo comentario al inicio del historial
                        if (is_array($comentariosAnteriores)) {
                            $comentariosActualizados = $comentariosAnteriores;
                        }

                        array_unshift($comentariosActualizados, $nuevosComentarios);

                        $candidato->comentarios = json_encode($comentariosActualizados);
                        $candidato->activo = 's';
                        $candidato->save();
                    }

                    return redirect()->back()->with('success', 'Candidato desbloqueado con éxito');
                } else {
                    return response()->json(['mensaje' => 'no se encontro registro']);
                }
            } else {
                return response()->json(['mensaje' => 'no envio los datos']);
            }
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->validator->getMessageBag())->withInput();
        }
    }


    public function enviarSolicitudRecomendacion(Request $request)
    {
        $mensaje = $request->input('mensaje');
        $identidad = $request->input('solID');

        $datos = Ingresos::select('candidatos.identidad', 'candidatos.nombre', 'candidatos.apellido', 'egresos_ingresos.fechaEgreso', 'egresos_ingresos.comentario', 'empresas.nombre as nombreEmpresa')
            ->where('egresos_ingresos.identidad', $identidad)
            ->where('egresos_ingresos.recomendado', 'n')
            ->join('candidatos', 'candidatos.identidad', '=', 'egresos_ingresos.identidad')
            ->join('empresas', 'empresas.id', '=', 'egresos_ingresos.id_empresa')
            ->get();

        $correoSolicitante = auth()->user()->email;
        $nombreSolicitante = auth()->user()->name;
        $enviarsolicitud = new EnviarSolicitudCandidato($mensaje, $datos, $correoSolicitante, $nombreSolicitante);
        //los usuarios que tengan en el perfil habilitado la opcion bloqueocolaborador pueden recibir las notificaciones de correo sobre el desbloqueo de colaborador
        $userto = User::select('users.email')->where('perfiles.bloqueocolaborador', '1')->join('perfiles', 'users.perfil_id', '=', 'perfiles.id')->get();
        try {
            foreach ($userto as $ut) {
                Mail::to($ut->email)->send($enviarsolicitud);
            }
        } catch (TransportException $e) {

            return redirect()->back()->with(['errorEmail' => 'No se pudo enviar el correo. Por favor, intente de nuevo más tarde.']);
        }

        return redirect()->back()->with(['successmail' => 'Se ha enviado con exito el mensaje, espere que la solicitud hay sido resuelta']);
    }

    public function desbloquearRecomendacion(Request $request)
    {
        try {
            $identidad = $request->input('identidad');
            $empresaID = $request->input('empresa_id');
            $desbloquearRecomendacion = Ingresos::select('id')->where('identidad', $identidad)
                ->where('id_empresa', $empresaID)
                ->where('recomendado', 'n')
                ->get();

            $desbloquearRecomendacion[0]->recomendado = 's';
            $desbloquearRecomendacion[0]->save();
            $mensaje = [
                'success' => 'se ha desbloqueado la recomendacion con exito',
                'id' => $desbloquearRecomendacion[0]->id,
                'status' => 200
            ];
            return response()->json($mensaje);
        } catch (Exception $e) {
            $mensaje = [
                'fail' => $e->getMessage(),
                'status' => 404
            ];
            return response()->json($mensaje);
        }
    }
}
