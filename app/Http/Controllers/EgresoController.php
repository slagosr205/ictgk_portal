<?php
// app/Http/Controllers/EgresoController.php

namespace App\Http\Controllers;

use App\Models\Candidatos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Ingresos;
use App\Models\Empresas;
use App\Models\PuestosModel;
use App\Models\DepartamentosModel;
use App\Models\PerfilModel;
use Carbon\Carbon;

class EgresoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Vista principal
     */
    public function index()
    {
        $user = Auth::user();

        $perfilUsers = PerfilModel::where('id', $user->perfil_id)->first();

        // Determinar si es admin
        $esAdmin = $perfilUsers->perfilesdescrip === 'admin' || $perfilUsers->perfilesdescrip === 'Administrador';

        // Cargar empresas según permisos
        if ($esAdmin) {
            $empresas = Empresas::orderBy('nombre')->get();
        } else {
            $empresas = Empresas::where('id', $user->empresa_id)->get();
        }

        return view('egresos.index', compact('empresas', 'esAdmin'));
    }

    /**
     * Listar empleados activos con filtros
     */
    public function listar(Request $request)
    {
        try {
            $user = Auth::user();
            $perfilUsers = PerfilModel::where('id', $user->perfil_id)->first();

            // Determinar si es admin
            $esAdmin = $perfilUsers->perfilesdescrip === 'admin' || $perfilUsers->perfilesdescrip === 'Administrador';
            // Query base
            $query = Ingresos::with(['candidato', 'puesto.departamento.empresa'])
                ->where('activo', 's')
                ->whereNull('fechaEgreso')
                ->whereHas('candidato');

            // Filtro por empresa (permisos)
            if (!$esAdmin) {
                $query->where('id_empresa', $user->empresa_id);
            } else if ($request->filled('empresa_id')) {
                $query->where('id_empresa', $request->empresa_id);
            }

            // Filtro por búsqueda (nombre, apellido, identidad)
            if ($request->filled('busqueda')) {
                $busqueda = $request->busqueda;
                $query->whereHas('candidato', function ($q) use ($busqueda) {
                    $q->where('nombre', 'LIKE', "%{$busqueda}%")
                        ->orWhere('apellido', 'LIKE', "%{$busqueda}%")
                        ->orWhere('identidad', 'LIKE', "%{$busqueda}%");
                });
            }

            // Filtro por puesto
            if ($request->filled('puesto_id')) {
                $query->where('id_puesto', $request->puesto_id);
            }

            // Filtro por departamento
            if ($request->filled('departamento_id')) {
                $query->whereHas('puesto', function ($q) use ($request) {
                    $q->where('departamento_id', $request->departamento_id);
                });
            }

            // Filtro por área
            if ($request->filled('area')) {
                $query->where('area', 'LIKE', "%{$request->area}%");
            }

            // Filtro por fecha de ingreso
            if ($request->filled('fecha_ingreso_desde')) {
                $query->where('fechaIngreso', '>=', $request->fecha_ingreso_desde);
            }
            if ($request->filled('fecha_ingreso_hasta')) {
                $query->where('fechaIngreso', '<=', $request->fecha_ingreso_hasta);
            }

            // Ordenar
            $orderBy = $request->get('order_by', 'fechaIngreso');
            $orderDir = $request->get('order_dir', 'desc');

            if ($orderBy === 'nombre') {
                // Para ordenar por nombre necesitamos un join
                $query->join('candidatos', 'egresos_ingresos.identidad', '=', 'candidatos.identidad')
                    ->orderBy('candidatos.nombre', $orderDir)
                    ->select('egresos_ingresos.*');
            } else {
                $query->orderBy($orderBy, $orderDir);
            }

            // Paginación
            $perPage = $request->get('per_page', 50);
            $empleados = $query->paginate($perPage);

            // Formatear datos para el frontend
            $data = $empleados->map(function ($ingreso) {
                $candidato = $ingreso->candidato;
                $puesto = $ingreso->puesto;
                $departamento = $puesto->departamento ?? null;
                $empresa = $departamento->empresa ?? null;

                return [
                    'id' => $ingreso->id,
                    'identidad' => $candidato->identidad ?? 'N/A',
                    'nombre_completo' => ($candidato->nombre ?? '') . ' ' . ($candidato->apellido ?? ''),
                    'nombre' => $candidato->nombre ?? '',
                    'apellido' => $candidato->apellido ?? '',
                    'puesto' => $puesto->nombrepuesto ?? 'N/A',
                    'puesto_id' => $ingreso->id_puesto,
                    'departamento' => $departamento->nombredepartamento ?? 'N/A',
                    'departamento_id' => $departamento->id ?? null,
                    'empresa' => $empresa->nombre ?? 'N/A',
                    'empresa_id' => $ingreso->id_empresa,
                    'area' => $ingreso->area ?? 'N/A',
                    'fechaIngreso' => $ingreso->fechaIngreso,
                    'fechaIngreso_formatted' => Carbon::parse($ingreso->fechaIngreso)->format('d/m/Y'),
                    'antiguedad' => $this->calcularAntiguedad($ingreso->fechaIngreso),
                    'telefono' => $candidato->telefono ?? '',
                    'correo' => $candidato->correo ?? '',
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data,
                'pagination' => [
                    'total' => $empleados->total(),
                    'per_page' => $empleados->perPage(),
                    'current_page' => $empleados->currentPage(),
                    'last_page' => $empleados->lastPage(),
                    'from' => $empleados->firstItem(),
                    'to' => $empleados->lastItem(),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error al listar empleados activos:', [
                'mensaje' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al cargar empleados: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Procesar egresos (uno o múltiples)
     */
    /**
     * Procesar egresos (uno o múltiples)
     */
    /**
     * Procesar egresos (uno o múltiples)
     */
    public function procesar(Request $request)
    {
        $request->validate([
            'empleados' => 'required|array|min:1',
            'empleados.*.id' => 'required|exists:egresos_ingresos,id',
            'empleados.*.fecha_egreso' => 'required|date',
            'motivo_egreso' => 'required|string|max:500',
            'recomendado' => 'required|in:s,n',
            'comentarios' => 'nullable|string|max:1000',
            'tipo_egreso' => 'required|string|max:100',
          
        ]);
//0318199601402
        $user = Auth::user();

        $perfilUsers = PerfilModel::where('id', $user->perfil_id)->first();

        // Determinar si es admin
        $esAdmin = $perfilUsers->perfilesdescrip === 'admin' || $perfilUsers->perfilesdescrip === 'Administrador';

        DB::beginTransaction();

        try {
            $procesados = 0;
            $errores = [];

            foreach ($request->empleados as $empleadoData) {
                try {
                    $ingreso = Ingresos::with('candidato')->find($empleadoData['id']);

                    if (!$ingreso) {
                        $errores[] = "Ingreso ID {$empleadoData['id']} no encontrado";
                        continue;
                    }

                    // Validar permisos
                    if (!$esAdmin && $ingreso->id_empresa != $user->empresa_id) {
                        $errores[] = "Sin permisos para egresar a {$ingreso->candidato->nombre}";
                        continue;
                    }

                    // Validar que esté activo
                    if ($ingreso->activo !== 's') {
                        $errores[] = "{$ingreso->candidato->nombre} ya no está activo";
                        continue;
                    }

                    // Validar fecha de egreso
                    $fechaEgreso = Carbon::parse($empleadoData['fecha_egreso']);
                    $fechaIngreso = Carbon::parse($ingreso->fechaIngreso);

                    if ($fechaEgreso->lt($fechaIngreso)) {
                        $errores[] = "{$ingreso->candidato->nombre}: fecha de egreso no puede ser anterior a fecha de ingreso";
                        continue;
                    }

                    // Construir comentario con toda la información
                    $comentarioNuevo = "EGRESO [{$fechaEgreso->format('d/m/Y')}]: {$request->motivo_egreso}. ";
                    $comentarioNuevo .= "Recomendado: " . ($request->recomendado === 's' ? 'Sí' : 'No') . ". ";
                   

                    if (!empty($request->comentarios)) {
                        $comentarioNuevo .= " Observaciones: {$request->comentarios}";
                    }

                    // Concatenar con comentarios anteriores
                    $comentarioFinal = $ingreso->Comentario
                        ? $ingreso->Comentario . ' | ' . $comentarioNuevo
                        : $comentarioNuevo;

                    // Actualizar registro
                    $ingreso->update([
                        'fechaEgreso' => $fechaEgreso->format('Y-m-d'),
                        'activo' => 'n',
                        'forma_egreso' => $request->motivo_egreso,
                        'tipo_egreso' => $request->tipo_egreso,
                        'recomendado' => $request->recomendado,
                        'Comentario' => $comentarioFinal
                    ]);

                   
                    // Actualizar estado del candidato
                    if ($ingreso?->candidato) {
                        $ingreso->candidato->update([
                            'activo' => 's'
                        ]);
                    }

                    $procesados++;
                } catch (\Exception $e) {
                    Log::error("Error procesando egreso ID {$empleadoData['id']}: " . $e->getMessage());
                    $errores[] = "Error al procesar empleado ID {$empleadoData['id']}";
                }
            }

            if ($procesados > 0) {
                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => "Se procesaron {$procesados} egreso(s) exitosamente",
                    'procesados' => $procesados,
                    'errores' => $errores
                ]);
            } else {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => 'No se pudo procesar ningún egreso',
                    'errores' => $errores
                ], 422);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error al procesar egresos:', [
                'mensaje' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al procesar egresos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener catálogos para filtros
     */
    public function catalogos(Request $request)
    {
        try {
            $user = Auth::user();
            $esAdmin = $user->perfil === 'admin' || $user->perfil === 'Administrador';

            $empresaId = $request->get('empresa_id');

            // Si no es admin, forzar su empresa
            /*    if (!$esAdmin) {
                $empresaId = $user->empresa_id;
            }*/

            $data = [
                'departamentos' => [],
                'puestos' => [],
                'areas' => []
            ];

            if ($empresaId) {
                // Departamentos de la empresa
                $data['departamentos'] = DepartamentosModel::where('empresa_id', $empresaId)
                    ->orderBy('nombredepartamento')
                    ->get(['id', 'nombredepartamento as nombre'])
                    ->toArray();

                // Puestos de la empresa
                $data['puestos'] = PuestosModel::whereHas('departamento', function ($q) use ($empresaId) {
                    $q->where('empresa_id', $empresaId);
                })
                    ->orderBy('nombrepuesto')
                    ->get(['id', 'nombrepuesto as nombre', 'departamento_id'])
                    ->toArray();

                // Áreas únicas de la empresa
                $data['areas'] = Ingresos::where('id_empresa', $empresaId)
                    ->where('activo', 's')
                    ->whereNotNull('area')
                    ->distinct()
                    ->pluck('area')
                    ->filter()
                    ->values()
                    ->toArray();
            }

            return response()->json([
                'success' => true,
                'catalogos' => $data
            ]);
        } catch (\Exception $e) {
            Log::error('Error al cargar catálogos:', [
                'mensaje' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al cargar catálogos'
            ], 500);
        }
    }

    /**
     * Calcular antigüedad
     */
    private function calcularAntiguedad(string $fechaIngreso): string
    {
        try {
            $ingreso = Carbon::parse($fechaIngreso);
            $ahora = Carbon::now();

            $diff = $ingreso->diff($ahora);

            $partes = [];

            if ($diff->y > 0) {
                $partes[] = $diff->y . ' año' . ($diff->y > 1 ? 's' : '');
            }
            if ($diff->m > 0) {
                $partes[] = $diff->m . ' mes' . ($diff->m > 1 ? 'es' : '');
            }
            if ($diff->d > 0 && empty($partes)) {
                $partes[] = $diff->d . ' día' . ($diff->d > 1 ? 's' : '');
            }

            return !empty($partes) ? implode(', ', $partes) : 'Menos de 1 día';
        } catch (\Exception $e) {
            return 'N/A';
        }
    }
}
