<?php

namespace App\Http\Controllers;

use App\Exports\ExportTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\Validacion\ValidadorImportacionService;
use App\Services\Validacion\AnalizadorCSVService;
use App\Models\Candidatos;
use App\Models\Ingresos;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ValidadorImportacionController extends Controller
{
    protected $validadorService;
    protected $analizadorService;

    public function __construct(
        ValidadorImportacionService $validadorService,
        AnalizadorCSVService $analizadorService
    ) {
        $this->middleware('auth');

        $this->validadorService = $validadorService;
        $this->analizadorService = $analizadorService;
    }

    /**
     * Mostrar vista
     */
    public function index()
    {
        // Verificar que el usuario esté autenticado
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Debe iniciar sesión');
        }

        return view('validador-importacion.index');
    }

    /**
     * Validar archivo
     */
    public function validar(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:csv,txt|max:10240'
        ]);

        try {
            // Obtener usuario autenticado
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no autenticado'
                ], 401);
            }

            // Crear servicio pasando el usuario explícitamente
            $validadorService = new ValidadorImportacionService($user);

            $analisis = $this->analizadorService->analizar($request->file('archivo'));

            if (!empty($analisis['metadata']['encabezados_faltantes'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Faltan columnas requeridas: ' .
                        implode(', ', $analisis['metadata']['encabezados_faltantes'])
                ], 422);
            }

            $validacion = $validadorService->validarRegistros($analisis['datos']);

            // Guardar en sesión
            session(['validacion_importacion' => $validacion]);

            return response()->json([
                'success' => true,
                'data' => $validacion
            ]);
        } catch (\Exception $e) {
            Log::error('Error en validación: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Error al procesar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Revalidar registro
     */
    public function revalidarRegistro(Request $request)
    {
        try {
            // Obtener usuario autenticado
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no autenticado'
                ], 401);
            }

            // Crear servicio pasando el usuario explícitamente
            $validadorService = new ValidadorImportacionService($user);

            $datos = $request->input('datos');
            $numeroFila = $request->input('fila', 0);

            $resultado = $validadorService->validarRegistroIndividual($datos, $numeroFila);

            return response()->json([
                'success' => true,
                'registro' => $resultado
            ]);
        } catch (\Exception $e) {
            Log::error('Error en revalidación: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Confirmar importación con lógica de reactivación
     */
    public function confirmar(Request $request)
    {
        $registros = $request->input('registros');

        if (!$registros || !is_array($registros)) {
            return response()->json([
                'success' => false,
                'message' => 'No hay registros para importar'
            ], 422);
        }

        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no autenticado'
            ], 401);
        }

        try {
            $validadorService = new ValidadorImportacionService($user);
            $catalogos = $validadorService->getCatalogos();

            $esAdmin = $catalogos['es_admin'];
            $empresaUsuario = $catalogos['empresa_usuario'];
            $empresasPermitidas = array_column($catalogos['empresas'], 'id');

            DB::beginTransaction();

            $insertados = 0;
            $reactivados = 0;
            $errores = [];

            foreach ($registros as $registro) {
                if (!$registro['valido']) {
                    continue;
                }

                try {
                    $datos = $registro['datos'];
                    $accion = $datos['_accion'] ?? 'crear';

                    // Validar permisos
                    if (!$esAdmin && $datos['id_empresa'] != $empresaUsuario) {
                        $errores[] = [
                            'fila' => $registro['fila'],
                            'identidad' => $datos['identidad'],
                            'errores' => ['No tiene permisos para importar a esta empresa']
                        ];
                        continue;
                    }
                    $identidad = str_replace('-', '', $datos['identidad']);
                    if (!in_array($datos['id_empresa'], $empresasPermitidas)) {
                        $errores[] = [
                            'fila' => $registro['fila'],
                            'identidad' => $identidad,
                            'errores' => ['La empresa no está dentro de sus permisos']
                        ];
                        continue;
                    }

                    // ============================================
                    // PASO 1: CREAR/ACTUALIZAR CANDIDATO
                    // ============================================
                    $datosCandidato = $datos['_candidato'] ?? [
                        'identidad' => $identidad,
                        'nombre' => $datos['nombre'],
                        'apellido' => $datos['apellido'],
                        'telefono' => $datos['telefono'],
                        'correo' => $datos['correo'],
                        'direccion' => $datos['direccion'],
                        'generoM_F' => $datos['generoM_F'],
                        'fecha_nacimiento' => $datos['fecha_nacimiento'],
                        'activo' => 'n',
                        'comentarios' => $datos['comentarios'] ?? '',
                    ];

                    $candidato = Candidatos::updateOrCreate(
                        ['identidad' => $identidad],
                        $datosCandidato
                    );

                    // ============================================
                    // PASO 2: CREAR/ACTUALIZAR INGRESO
                    // ============================================
                    $datosIngreso = $datos['_ingreso'] ?? [
                        'identidad' => $identidad,
                        'id_empresa' => $datos['id_empresa'],
                        'id_puesto' => $datos['id_puesto'],
                        'fechaIngreso' => $datos['fechaIngreso'],
                        'fechaEgreso' => $datos['fechaEgreso'],
                        'area' => $datos['area'],
                        'activo' => empty($datos['fechaEgreso']) ? 's' : 'n',
                        'Comentario' => $datos['Comentario'] ?? '',
                    ];

                    if ($accion === 'reactivar') {
                        // REACTIVAR: Actualizar ingreso existente
                        $ingresoId = $datos['_ingreso_id'];

                        $ingreso = Ingresos::find($ingresoId);

                        if ($ingreso) {
                            if (!in_array($ingreso->id_empresa, $empresasPermitidas)) {
                                $errores[] = [
                                    'fila' => $registro['fila'],
                                    'identidad' => $identidad,
                                    'errores' => ['No tiene permisos para modificar este ingreso']
                                ];
                                continue;
                            }
                            
                            $ingreso->create($datosIngreso);

                            $reactivados++;
                        } else {
                            throw new \Exception("No se encontró el ingreso a reactivar");
                        }
                    } else {
                        // CREAR: Nuevo ingreso
                        Ingresos::create($datosIngreso);
                        $insertados++;
                    }
                } catch (\Illuminate\Database\QueryException $e) {
                    $mensaje = $e->getMessage();

                    Log::error("Error BD en fila {$registro['fila']}: " . $mensaje);

                    if (str_contains($mensaje, 'El puesto no pertenece')) {
                        $errores[] = [
                            'fila' => $registro['fila'],
                            'identidad' => $identidad,
                            'errores' => ['El puesto no pertenece a la empresa']
                        ];
                    } elseif (str_contains($mensaje, 'Duplicate entry')) {
                        $errores[] = [
                            'fila' => $registro['fila'],
                            'identidad' => $identidad,
                            'errores' => ['Ya existe un ingreso activo para este colaborador']
                        ];
                    } else {
                        $errores[] = [
                            'fila' => $registro['fila'],
                            'identidad' => $identidad,
                            'errores' => ['Error BD: ' . substr($mensaje, 0, 100)]
                        ];
                    }
                } catch (\Exception $e) {
                    Log::error("Error en fila {$registro['fila']}: " . $e->getMessage());

                    $errores[] = [
                        'fila' => $registro['fila'],
                        'identidad' => $identidad ?? 'N/A',
                        'errores' => [$e->getMessage()]
                    ];
                }
            }

            $totalProcesados = $insertados + $reactivados;

            if ($totalProcesados > 0) {
                DB::commit();
                session()->forget('validacion_importacion');

                $mensaje = [];
                if ($insertados > 0) {
                    $mensaje[] = "{$insertados} nuevo(s) ingreso(s)";
                }
                if ($reactivados > 0) {
                    $mensaje[] = "{$reactivados} reactivación(es)";
                }

                return response()->json([
                    'success' => true,
                    'message' => "Importación exitosa: " . implode(', ', $mensaje),
                    'insertados' => $insertados,
                    'reactivados' => $reactivados,
                    'total' => $totalProcesados,
                    'errores' => $errores
                ]);
            } else {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => 'No se pudo importar ningún registro',
                    'errores' => $errores
                ], 422);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error en importación: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Catálogos según permisos del usuario
     */
    /**
     * Catálogos según permisos del usuario
     */
    public function catalogos()
    {
        try {
            // Obtener usuario autenticado
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no autenticado'
                ], 401);
            }

            // Crear servicio pasando el usuario explícitamente
            $validadorService = new ValidadorImportacionService($user);
            $catalogos = $validadorService->getCatalogos();

            return response()->json([
                'success' => true,
                'catalogos' => $catalogos
            ]);
        } catch (\Exception $e) {
            Log::error('Error al cargar catálogos: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al cargar catálogos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Descargar plantilla
     */
    public function descargarPlantilla(Request $request)
    {
        try {
            // Obtener cantidad de filas del request (default: 10)
            $cantidadFilas = (int) $request->get('filas', 10);

            // Validar que sea un número razonable
            if ($cantidadFilas < 1 || $cantidadFilas > 10000) {
                $cantidadFilas = 10;
            }

            // Generar plantilla con la cantidad solicitada
            $excel = new ExportTemplate($cantidadFilas);

            $nombreArchivo = 'plantilla_importacion_' . $cantidadFilas . '_filas_' . date('Ymd') . '.xlsx';

            return Excel::download($excel, $nombreArchivo);
        } catch (\Exception $e) {
            Log::error('Error generando plantilla: ' . $e->getMessage());

            return back()->with([
                'mensaje' => 'Error al generar plantilla: ' . $e->getMessage(),
                'icon' => 'error'
            ]);
        }
    }
}
