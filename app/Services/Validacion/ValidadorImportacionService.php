<?php

namespace App\Services\Validacion;

use App\Rules\DniUnicoRule;
use App\Rules\PuestoEmpresaRule;
use App\Rules\FormatoDniRule;
use App\Models\Candidatos;
use App\Models\Ingresos;
use App\Models\Empresas;
use App\Models\DepartamentosModel;
use App\Models\PerfilModel;
use App\Models\PuestosModel;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ValidadorImportacionService
{
    protected $catalogos;
    protected $user;
    protected $esAdmin;
    protected $empresaUsuario;
    
    public function __construct(User $user)
    {
        $this->user = $user ?? Auth::user();
        
         // Verificar perfil del usuario
        $perfil = PerfilModel::find($user->perfil_id);
        $this->esAdmin = $perfil && strtolower($perfil->perfilesdescrip) === 'admin';
        $this->empresaUsuario = $user->empresa_id;

        $this->cargarCatalogos();
    }

    /**
     * Validar conjunto de registros
     */
    public function validarRegistros(array $registros, array $opciones = []): array
    {
        $resultados = [];
        $estadisticas = [
            'total' => count($registros),
            'validos' => 0,
            'con_errores' => 0,
            'con_advertencias' => 0,
            'reactivaciones' => 0,
            'nuevos' => 0
        ];

        foreach ($registros as $index => $registro) {
            $resultado = $this->validarRegistroIndividual($registro, $index + 1);
            
            $resultados[] = $resultado;
            
            if ($resultado['valido']) {
                $estadisticas['validos']++;
                
                // Contar tipo de acción
                if (isset($resultado['datos']['_accion'])) {
                    if ($resultado['datos']['_accion'] === 'reactivar') {
                        $estadisticas['reactivaciones']++;
                    } else {
                        $estadisticas['nuevos']++;
                    }
                }
            } else {
                $estadisticas['con_errores']++;
            }
            
            if (count($resultado['advertencias']) > 0) {
                $estadisticas['con_advertencias']++;
            }
        }

        return [
            'registros' => $resultados,
            'estadisticas' => $estadisticas,
            'catalogos' => $this->catalogos
        ];
    }

    /**
     * Validar un registro individual
     */
    public function validarRegistroIndividual(array $registro, int $numeroFila): array
    {
        $errores = [];
        $advertencias = [];
        $datos = $this->normalizarDatos($registro);
        
        // Aplicar reglas de validación básicas
        $validator = Validator::make($datos, $this->obtenerReglas($datos), $this->obtenerMensajes());

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $errores[] = $error;
            }
        }

        // ============================================
        // LÓGICA DE NEGOCIO: ESCENARIOS DE INGRESO
        // ============================================
        $estadoIngreso = $this->verificarEstadoIngreso($datos);
        
        // Agregar información del estado al resultado
        $datos['_escenario'] = $estadoIngreso['escenario'];
        $datos['_accion'] = $estadoIngreso['accion'] ?? 'crear';
        
        if (isset($estadoIngreso['ingreso_id'])) {
            $datos['_ingreso_id'] = $estadoIngreso['ingreso_id'];
        }

        // Manejar errores según el escenario
        if (isset($estadoIngreso['error'])) {
            $errores[] = $estadoIngreso['error'];
        }

        // Manejar advertencias según el escenario
        if (isset($estadoIngreso['advertencia'])) {
            $advertencias[] = $estadoIngreso['advertencia'];
        }

        // Validaciones adicionales personalizadas
        $erroresAdicionales = $this->validacionesPersonalizadas($datos, $estadoIngreso);
        $errores = array_merge($errores, $erroresAdicionales);
        
        // Advertencias adicionales
        $advertenciasAdicionales = $this->generarAdvertencias($datos);
        $advertencias = array_merge($advertencias, $advertenciasAdicionales);

        return [
            'fila' => $numeroFila,
            'datos' => $datos,
            'datos_originales' => $registro,
            'valido' => empty($errores),
            'errores' => $errores,
            'advertencias' => $advertencias,
            'sugerencias' => $this->generarSugerencias($datos, $errores),
            'estado_ingreso' => $estadoIngreso
        ];
    }

    /**
     * Verificar el estado de ingreso del colaborador
     * 
     * ESCENARIOS:
     * 1. activo_misma_empresa: Ya está activo en la misma empresa → ERROR
     * 2. inactivo_misma_empresa: Estuvo en la empresa pero está inactivo → REACTIVAR
     * 3. activo_otra_empresa: Está activo en otra empresa → ERROR
     * 4. nuevo_ingreso: Candidato existe pero sin ingresos previos → CREAR
     * 5. primer_ingreso: No existe el candidato → CREAR
     */
    protected function verificarEstadoIngreso(array $datos): array
    {
        $identidad = str_replace('-', '', $datos['identidad']);
        $empresaId = $datos['id_empresa'];

        // Verificar si el candidato existe
        $candidato = Candidatos::where('identidad', $identidad)->first();
        
        if (!$candidato) {
            return [
                'escenario' => 'primer_ingreso',
                'accion' => 'crear',
                'mensaje' => 'Primer ingreso al sistema',
                'advertencia' => 'Nuevo colaborador en el sistema'
            ];
        }

        // Buscar ingresos del candidato
        $ingresos = Ingresos::where('identidad', $identidad)
            ->with(['puesto.departamento'])
            ->orderBy('fechaIngreso', 'desc')
            ->get();

        if ($ingresos->isEmpty()) {
            return [
                'escenario' => 'nuevo_ingreso',
                'accion' => 'crear',
                'mensaje' => 'Candidato existe pero sin ingresos previos',
                'advertencia' => 'Se creará su primer ingreso'
            ];
        }

        // ================================================
        // ESCENARIO 1: Verificar si está ACTIVO en la MISMA empresa
        // ================================================
        $activoMismaEmpresa = $ingresos->first(function($ing) use ($empresaId) {
            return $ing->id_empresa == $empresaId && 
                   strtolower($ing->activo) == 's' && 
                   empty($ing->fechaEgreso);
        });

        if ($activoMismaEmpresa) {
            $empresa = Empresas::find($empresaId);
            $puesto = $activoMismaEmpresa->puesto;
            
            return [
                'escenario' => 'activo_misma_empresa',
                'accion' => 'ninguna',
                'ingreso_id' => $activoMismaEmpresa->id,
                'empresa_nombre' => $empresa->nombre ?? 'Empresa #' . $empresaId,
                'puesto_nombre' => $puesto->nombrepuesto ?? 'N/A',
                'fecha_ingreso' => $activoMismaEmpresa->fechaIngreso,
                'error' => sprintf(
                    'El colaborador YA ESTÁ ACTIVO en %s como %s desde el %s',
                    $empresa->nombre ?? 'esta empresa',
                    $puesto->nombrepuesto ?? 'este puesto',
                    Carbon::parse($activoMismaEmpresa->fechaIngreso)->format('d/m/Y')
                )
            ];
        }

        // ================================================
        // ESCENARIO 2: Verificar si estuvo INACTIVO en la MISMA empresa
        // ================================================
        $inactivoMismaEmpresa = $ingresos->first(function($ing) use ($empresaId) {
            return $ing->id_empresa == $empresaId && 
                   strtolower($ing->activo) == 'n';
        });

        if ($inactivoMismaEmpresa) {
            $empresa = Empresas::find($empresaId);
            $puesto = $inactivoMismaEmpresa->puesto;
            
            return [
                'escenario' => 'inactivo_misma_empresa',
                'accion' => 'reactivar',
                'ingreso_id' => $inactivoMismaEmpresa->id,
                'empresa_nombre' => $empresa->nombre ?? 'Empresa #' . $empresaId,
                'puesto_anterior' => $puesto->nombrepuesto ?? 'N/A',
                'fecha_ingreso_anterior' => $inactivoMismaEmpresa->fechaIngreso,
                'fecha_egreso' => $inactivoMismaEmpresa->fechaEgreso,
                'advertencia' => sprintf(
                    'REACTIVACIÓN: El colaborador trabajó en %s del %s al %s. Se actualizará su registro como ACTIVO',
                    $empresa->nombre ?? 'esta empresa',
                    Carbon::parse($inactivoMismaEmpresa->fechaIngreso)->format('d/m/Y'),
                    $inactivoMismaEmpresa->fechaEgreso ? Carbon::parse($inactivoMismaEmpresa->fechaEgreso)->format('d/m/Y') : 'N/A'
                )
            ];
        }

        // ================================================
        // ESCENARIO 3: Verificar si está ACTIVO en OTRA empresa
        // ================================================
        $activoOtraEmpresa = $ingresos->first(function($ing) use ($empresaId) {
            return $ing->id_empresa != $empresaId && 
                   strtolower($ing->activo) == 's' && 
                   empty($ing->fechaEgreso);
        });

        if ($activoOtraEmpresa) {
            $empresa = Empresas::find($activoOtraEmpresa->id_empresa);
            $puesto = $activoOtraEmpresa->puesto;
            
            return [
                'escenario' => 'activo_otra_empresa',
                'accion' => 'ninguna',
                'ingreso_id' => $activoOtraEmpresa->id,
                'empresa_nombre' => $empresa->nombre ?? 'Empresa #' . $activoOtraEmpresa->id_empresa,
                'puesto_nombre' => $puesto->nombrepuesto ?? 'N/A',
                'fecha_ingreso' => $activoOtraEmpresa->fechaIngreso,
                'error' => sprintf(
                    'El colaborador está ACTIVO en %s como %s desde el %s. No puede tener contratos simultáneos en múltiples empresas',
                    $empresa->nombre ?? 'otra empresa',
                    $puesto->nombrepuesto ?? 'otro puesto',
                    Carbon::parse($activoOtraEmpresa->fechaIngreso)->format('d/m/Y')
                )
            ];
        }

        // Si llegamos aquí, tiene ingresos pero ninguno está activo
        return [
            'escenario' => 'nuevo_ingreso',
            'accion' => 'crear',
            'mensaje' => 'Candidato con historial, crear nuevo ingreso',
            'advertencia' => 'El colaborador tiene historial pero no está activo actualmente'
        ];
    }

    /**
     * Validaciones personalizadas adicionales
     */
    protected function validacionesPersonalizadas(array $datos, array $estadoIngreso): array
    {
        $errores = [];

        // Validar edad mínima si hay fecha de nacimiento
        if (!empty($datos['fecha_nacimiento'])) {
            try {
                $edad = Carbon::parse($datos['fecha_nacimiento'])->age;
                
                if ($edad < 18) {
                    $errores[] = 'El colaborador debe ser mayor de 18 años';
                }
                
                if ($edad > 100) {
                    $errores[] = 'La fecha de nacimiento no es válida (edad > 100 años)';
                }
            } catch (\Exception $e) {
                $errores[] = 'Formato de fecha de nacimiento inválido';
            }
        }

        // Validar coherencia de fechas
        if (!empty($datos['fechaIngreso']) && !empty($datos['fecha_nacimiento'])) {
            try {
                $edadIngreso = Carbon::parse($datos['fecha_nacimiento'])
                    ->diffInYears(Carbon::parse($datos['fechaIngreso']));
                
                if ($edadIngreso < 18) {
                    $errores[] = 'El colaborador era menor de edad en la fecha de ingreso';
                }
            } catch (\Exception $e) {
                // Silenciar
            }
        }

        // Validar fechas de ingreso y egreso
        if (!empty($datos['fechaEgreso']) && !empty($datos['fechaIngreso'])) {
            try {
                $ingreso = Carbon::parse($datos['fechaIngreso']);
                $egreso = Carbon::parse($datos['fechaEgreso']);
                
                if ($egreso->lte($ingreso)) {
                    $errores[] = 'La fecha de egreso debe ser posterior a la fecha de ingreso';
                }
            } catch (\Exception $e) {
                // Ya se manejó en las validaciones básicas
            }
        }

        // Si es reactivación, validar que la nueva fecha de ingreso sea posterior al egreso
        if ($estadoIngreso['escenario'] === 'inactivo_misma_empresa' && !empty($estadoIngreso['fecha_egreso'])) {
            try {
                $egresoAnterior = Carbon::parse($estadoIngreso['fecha_egreso']);
                $nuevoIngreso = Carbon::parse($datos['fechaIngreso']);
                
                if ($nuevoIngreso->lt($egresoAnterior)) {
                    $errores[] = sprintf(
                        'La fecha de reingreso (%s) debe ser posterior a la fecha de egreso anterior (%s)',
                        $nuevoIngreso->format('d/m/Y'),
                        $egresoAnterior->format('d/m/Y')
                    );
                }
            } catch (\Exception $e) {
                // Silenciar
            }
        }

        return $errores;
    }

    /**
     * Generar advertencias
     */
    protected function generarAdvertencias(array $datos): array
    {
        $advertencias = [];

        // Fecha de ingreso muy antigua
        if (!empty($datos['fechaIngreso'])) {
            try {
                $antiguedad = Carbon::parse($datos['fechaIngreso'])->diffInYears(Carbon::now());
                if ($antiguedad > 30) {
                    $advertencias[] = "Antigüedad inusual: {$antiguedad} años";
                }
            } catch (\Exception $e) {
                // Silenciar
            }
        }

        // Falta información opcional importante
        if (empty($datos['correo'])) {
            $advertencias[] = 'No se proporcionó correo electrónico';
        }

        if (empty($datos['telefono'])) {
            $advertencias[] = 'No se proporcionó teléfono';
        }

        if (empty($datos['fecha_nacimiento'])) {
            $advertencias[] = 'No se proporcionó fecha de nacimiento';
        }

        if (empty($datos['generoM_F'])) {
            $advertencias[] = 'No se proporcionó género';
        }

        if (empty($datos['direccion'])) {
            $advertencias[] = 'No se proporcionó dirección';
        }

        // Si tiene fecha de egreso, advertir que está marcado como inactivo
        if (!empty($datos['fechaEgreso'])) {
            $advertencias[] = 'El registro incluye fecha de egreso (se marcará como inactivo)';
        }

        return $advertencias;
    }

    /**
     * Generar sugerencias
     */
    protected function generarSugerencias(array $datos, array $errores): array
    {
        $sugerencias = [];

        foreach ($errores as $error) {
            if (str_contains($error, 'identidad') || str_contains($error, 'formato')) {
                $sugerencias[] = 'Formato correcto de identidad: 0000-0000-00000';
            }

            if (str_contains($error, 'puesto no pertenece')) {
                $puestos = $this->obtenerPuestosDeEmpresa($datos['id_empresa'] ?? null);
                if (!empty($puestos)) {
                    $nombres = array_slice(array_column($puestos, 'nombrepuesto'), 0, 5);
                    $sugerencias[] = 'Puestos disponibles: ' . implode(', ', $nombres);
                }
            }

            if (str_contains($error, 'género')) {
                $sugerencias[] = 'Valores válidos para género: M (Masculino) o F (Femenino)';
            }

            if (str_contains($error, 'ACTIVO')) {
                $sugerencias[] = 'Para cambiar de empresa, primero debe egresar al colaborador de su empresa actual';
            }

            if (str_contains($error, 'múltiples empresas')) {
                $sugerencias[] = 'Registre primero el egreso de la empresa actual antes de ingresar a una nueva';
            }
        }

        return array_unique($sugerencias);
    }

    /**
     * Obtener reglas de validación
     */
    protected function obtenerReglas(array $datos): array
    {
        return [
            // Campos de Candidatos
            'identidad' => [
                'required',
                'string',
                new FormatoDniRule()
            ],
            'nombre' => ['required', 'string', 'max:255'],
            'apellido' => ['required', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'correo' => ['nullable', 'email', 'max:255'],
            'direccion' => ['nullable', 'string', 'max:500'],
            'generoM_F' => ['nullable', 'in:M,F,m,f,Masculino,Femenino,masculino,femenino'],
            'fecha_nacimiento' => ['nullable', 'date', 'before:-18 years'],
            
            // Campos de Ingresos
            'id_empresa' => ['required', 'integer', 'exists:empresas,id'],
            'id_puesto' => [
                'required',
                'integer',
                'exists:puestos,id',
                new PuestoEmpresaRule($datos['id_empresa'] ?? null)
            ],
            'fechaIngreso' => ['required', 'date', 'before_or_equal:today'],
            'fechaEgreso' => ['nullable', 'date', 'after:fechaIngreso'],
            'area' => ['nullable', 'string', 'max:255'],
            'activo' => ['nullable', 'in:s,n,S,N,si,no,SI,NO'],
        ];
    }

    /**
     * Mensajes personalizados
     */
    protected function obtenerMensajes(): array
    {
        return [
            'identidad.required' => 'La identidad es obligatoria',
            'nombre.required' => 'El nombre es obligatorio',
            'nombre.max' => 'El nombre no puede exceder 255 caracteres',
            'apellido.required' => 'El apellido es obligatorio',
            'apellido.max' => 'El apellido no puede exceder 255 caracteres',
            'correo.email' => 'El formato del correo no es válido',
            'generoM_F.in' => 'El género debe ser M (Masculino) o F (Femenino)',
            'fecha_nacimiento.before' => 'El colaborador debe ser mayor de 18 años',
            'id_empresa.required' => 'La empresa es obligatoria',
            'id_empresa.exists' => 'La empresa seleccionada no existe',
            'id_puesto.required' => 'El puesto es obligatorio',
            'id_puesto.exists' => 'El puesto seleccionado no existe',
            'fechaIngreso.required' => 'La fecha de ingreso es obligatoria',
            'fechaIngreso.date' => 'La fecha de ingreso no es válida',
            'fechaIngreso.before_or_equal' => 'La fecha de ingreso no puede ser futura',
            'fechaEgreso.after' => 'La fecha de egreso debe ser posterior a la fecha de ingreso',
            'activo.in' => 'El valor de activo debe ser: s (sí) o n (no)',
        ];
    }

    /**
     * Normalizar datos
     */
    protected function normalizarDatos(array $datos): array
    {
        $normalizadorFechas = new NormalizadorFechasService();
       
        return [
            // Candidatos
            'identidad' => $this->normalizarDni($datos['identidad'] ?? $datos['dni'] ?? ''),
            'nombre' => trim($datos['nombre'] ?? ''),
            'apellido' => trim($datos['apellido'] ?? ''),
            'telefono' => $this->normalizarTelefono($datos['telefono'] ?? ''),
            'correo' => strtolower(trim($datos['correo'] ?? $datos['email'] ?? '')),
            'direccion' => trim($datos['direccion'] ?? ''),
            'generoM_F' => $this->normalizarGenero($datos['generoM_F'] ?? $datos['genero'] ?? ''),
            'fecha_nacimiento' => $normalizadorFechas->normalizar($datos['fecha_nacimiento'] ?? ''),
            'comentarios' => trim($datos['comentarios'] ?? ''),
            
            // Ingresos
            'id_empresa' => (int)($datos['id_empresa'] ?? $datos['empresa_id'] ?? 0),
            'id_puesto' => (int)($datos['id_puesto'] ?? $datos['puesto_id'] ?? 0),
            'fechaIngreso' => $normalizadorFechas->normalizar($datos['fechaingreso'] ?? $datos['fecha_ingreso'] ?? $datos['fechaIngreso'] ?? ''),
            'fechaEgreso' => $normalizadorFechas->normalizar($datos['fechaEgreso'] ?? $datos['fecha_egreso'] ?? ''),
            'area' => trim($datos['area'] ?? ''),
            'activo' => $this->normalizarActivo($datos['activo'] ?? 's'),
        ];
    }

    /**
     * Normalizar DNI/Identidad
     */
    protected function normalizarDni(string $dni): string
    {
        $dni = preg_replace('/[^0-9-]/', '', $dni);
        
        if (strlen($dni) === 13 && !str_contains($dni, '-')) {
            $dni = substr($dni, 0, 4) . '-' . substr($dni, 4, 4) . '-' . substr($dni, 8);
        }
        
        return $dni;
    }

    /**
     * Normalizar género
     */
    protected function normalizarGenero(string $genero): string
    {
        $genero = strtoupper(trim($genero));
        
        $mapeo = [
            'MASCULINO' => 'm',
            'HOMBRE' => 'm',
            'MALE' => 'm',
            'FEMENINO' => 'f',
            'MUJER' => 'f',
            'FEMALE' => 'f',
        ];
        
        return $mapeo[$genero] ?? $genero;
    }

    /**
     * Normalizar activo (s/n)
     */
    protected function normalizarActivo($valor): string
    {
        if (is_bool($valor)) {
            return $valor ? 's' : 'n';
        }
        
        $valor = strtolower(trim($valor));
        
        return in_array($valor, ['si', 'yes', 'true', 'activo', '1', 's']) ? 's' : 'n';
    }

    /**
     * Normalizar fecha
     */
    protected function normalizarFecha(?string $fecha): ?string
    {
        if (empty($fecha)) return null;
        
        try {
            $formatos = ['Y-m-d', 'd/m/Y', 'd-m-Y', 'm/d/Y'];
            
            foreach ($formatos as $formato) {
                $date = Carbon::createFromFormat($formato, $fecha);
                if ($date !== false) {
                    return $date->format('Y-m-d');
                }
            }
            
            return Carbon::parse($fecha)->format('Y-m-d');
            
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Normalizar teléfono
     */
    protected function normalizarTelefono(string $telefono): string
    {
        $telefono = preg_replace('/[^0-9+]/', '', $telefono);
        
        if (strlen($telefono) === 8 && !str_starts_with($telefono, '+')) {
            $telefono = '+504' . $telefono;
        }
        
        return $telefono;
    }

    /**
     * Cargar catálogos
     */
    /**
     * Cargar catálogos según el perfil del usuario
     */
    protected function cargarCatalogos(): void
    {
        // ============================================
        // FILTRAR EMPRESAS SEGÚN PERFIL
        // ============================================
        $queryEmpresas = Empresas::select('id', 'nombre')
            ->where('estado', 'a')
            ->orderBy('nombre');

            

        // Si NO es admin, filtrar solo su empresa
        if (!$this->esAdmin && $this->empresaUsuario) {
            $queryEmpresas->where('id', $this->empresaUsuario);
        }

        $empresas = $queryEmpresas->get()->toArray();

        // Si no hay empresas disponibles
        if (empty($empresas)) {
            throw new \Exception('No hay empresas disponibles para este usuario');
        }

        // ============================================
        // FILTRAR DEPARTAMENTOS Y PUESTOS
        // ============================================
        
        // Obtener IDs de empresas permitidas
        $empresasPermitidas = array_column($empresas, 'id');

        // Departamentos solo de las empresas permitidas
        $departamentos = DepartamentosModel::select('id', 'nombredepartamento as nombre', 'empresa_id')
            ->whereIn('empresa_id', $empresasPermitidas)
            ->orderBy('nombredepartamento')
            ->get()
            ->toArray();

        // IDs de departamentos permitidos
        $departamentosPermitidos = array_column($departamentos, 'id');

        // Puestos solo de los departamentos permitidos
        $puestos = PuestosModel::select('id', 'nombrepuesto as nombre', 'departamento_id')
            ->whereIn('departamento_id', $departamentosPermitidos)
            ->orderBy('nombrepuesto')
            ->get()
            ->toArray();

        // ============================================
        // ASIGNAR CATÁLOGOS
        // ============================================
        $this->catalogos = [
            'empresas' => $empresas,
            'departamentos' => $departamentos,
            'puestos' => $puestos,
            'es_admin' => $this->esAdmin,
            'empresa_usuario' => $this->empresaUsuario,
            'usuario_nombre' => $this->user->name ?? 'Usuario'
        ];
    }


    /**
     * Obtener puestos de una empresa
     */
    protected function obtenerPuestosDeEmpresa(?int $empresaId): array
    {
        if (!$empresaId) return [];

        return DB::table('puestos as p')
            ->join('departamentos as d', 'd.id', '=', 'p.departamento_id')
            ->where('d.empresa_id', $empresaId)
            ->select('p.id', 'p.nombrepuesto')
            ->get()
            ->toArray();
    }

    /**
     * Obtener catálogos
     */
    public function getCatalogos(): array
    {
        return $this->catalogos;
    }
}