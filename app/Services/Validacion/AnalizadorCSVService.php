<?php

namespace App\Services\Validacion;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class AnalizadorCSVService
{
    protected $normalizadorFechas;

    // Mapeo de variaciones de encabezados
    protected $mapaEncabezados = [
        'fechaingreso' => ['fechaingreso', 'fechaIngreso', 'fecha_ingreso', 'ingreso', 'date_ingreso', 'fechadeingreso'],
        'fechaegreso' => ['fechaegreso', 'fechaEgreso', 'fecha_egreso', 'egreso', 'date_egreso', 'fechadeegreso'],
    ];

    protected $encabezadosRequeridos = [
        'identidad',
        'nombre',
        'apellido',
        'id_empresa',
        'id_puesto',
        'fechaingreso'
    ];

    protected $encabezadosOpcionales = [
        'telefono',
        'correo',
        'direccion',
        'generom_f',
        'fecha_nacimiento',
        'comentarios',
        'fechaegreso',
        'area',
        'activo',
        'comentario',
        'validacion'
    ];

    public function __construct()
    {
        $this->normalizadorFechas = new NormalizadorFechasService();
    }

    /**
     * Analizar archivo CSV
     */
    public function analizar(UploadedFile $archivo): array
    {
        $this->validarArchivo($archivo);

        $datos = $this->procesarCSV($archivo);

        // Detectar formato de fechas
        $formatoFecha = $this->detectarFormatoFechas($datos['registros']);

        return [
            'exito' => true,
            'datos' => $datos['registros'],
            'metadata' => [
                'total_filas' => $datos['total_filas'],
                'encabezados' => $datos['encabezados'],
                'encabezados_originales' => $datos['encabezados_originales'],
                'encoding' => $datos['encoding'],
                'delimitador' => $datos['delimitador'],
                'formato_fecha_detectado' => $formatoFecha['formato'],
                'formato_fecha_info' => $formatoFecha['info'],
                'encabezados_faltantes' => $this->validarEncabezados($datos['encabezados_normalizados'])
            ]
        ];
    }

    /**
     * Normalizar nombre de encabezado
     */
    protected function normalizarEncabezado(string $encabezado): string
    {
        $limpio = strtolower(trim($encabezado));
        $limpio = str_replace([' ', '_', '-'], '', $limpio);

        // Buscar en el mapa
        foreach ($this->mapaEncabezados as $estandar => $variaciones) {
            foreach ($variaciones as $variacion) {
                $variacionLimpia = str_replace([' ', '_', '-'], '', strtolower($variacion));
                if ($limpio === $variacionLimpia) {
                    return $estandar;
                }
            }
        }

        return strtolower(trim($encabezado));
    }

    /**
     * Detectar formato de fechas en el CSV
     */
    protected function detectarFormatoFechas(array $registros): array
    {
        $fechasIngreso = [];

        // Buscar con todas las variaciones posibles
        $camposPosibles = ['fechaingreso', 'fechaIngreso', 'fecha_ingreso', 'ingreso'];

        foreach ($registros as $registro) {
            foreach ($camposPosibles as $campo) {
                if (isset($registro[$campo]) && !empty($registro[$campo])) {
                    $fechasIngreso[] = $registro[$campo];
                    break;
                }
            }
        }

        Log::info('Fechas encontradas para detectar formato:', [
            'cantidad' => count($fechasIngreso),
            'muestra' => array_slice($fechasIngreso, 0, 3)
        ]);

        if (empty($fechasIngreso)) {
            return [
                'formato' => null,
                'info' => 'No se encontraron fechas de ingreso. Campos buscados: ' . implode(', ', $camposPosibles)
            ];
        }

        // Detectar formato
        $formatoDetectado = $this->normalizadorFechas->detectarFormato($fechasIngreso);

        if ($formatoDetectado) {
            Log::info('Formato de fecha detectado: ' . $formatoDetectado);
            return [
                'formato' => $formatoDetectado,
                'info' => $this->obtenerInfoFormato($formatoDetectado),
                'fechas_analizadas' => count($fechasIngreso)
            ];
        }

        return [
            'formato' => null,
            'info' => 'No se pudo detectar automáticamente. Se intentarán múltiples formatos.',
            'fechas_analizadas' => count($fechasIngreso)
        ];
    }

    /**
     * Obtener información del formato
     */
    protected function obtenerInfoFormato(string $formato): string
    {
        $formatos = [
            'Y-m-d' => 'ISO 8601: Año-Mes-Día (2024-01-27)',
            'd/m/Y' => 'Latinoamérica: Día/Mes/Año (27/01/2024)',
            'm/d/Y' => 'Estados Unidos: Mes/Día/Año (01/27/2024)',
            'd-m-Y' => 'Día-Mes-Año (27-01-2024)',
            'Y/m/d' => 'Año/Mes/Día (2024/01/27)',
            'm-d-Y' => 'Mes-Día-Año (01-27-2024)',
        ];

        return $formatos[$formato] ?? "Formato: {$formato}";
    }

    /**
     * Validar archivo
     */
    protected function validarArchivo(UploadedFile $archivo): void
    {
        if (!in_array($archivo->getClientOriginalExtension(), ['csv', 'txt'])) {
            throw new \InvalidArgumentException('El archivo debe ser CSV o TXT');
        }

        if ($archivo->getSize() > 10 * 1024 * 1024) {
            throw new \InvalidArgumentException('El archivo no puede exceder 10MB');
        }

        if ($archivo->getSize() === 0) {
            throw new \InvalidArgumentException('El archivo está vacío');
        }
    }

    /**
     * Procesar CSV
     */
    protected function procesarCSV(UploadedFile $archivo): array
    {
        $contenido = file_get_contents($archivo->getPathname());

        $encoding = mb_detect_encoding($contenido, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);

        if ($encoding !== 'UTF-8') {
            $contenido = mb_convert_encoding($contenido, 'UTF-8', $encoding);
        }

        $delimitador = $this->detectarDelimitador($contenido);
        $lineas = str_getcsv($contenido, "\n");

        if (empty($lineas)) {
            throw new \InvalidArgumentException('El archivo no contiene datos');
        }

        // Obtener encabezados ORIGINALES
        $encabezadosOriginales = str_getcsv($lineas[0], $delimitador);
        $encabezadosOriginales = array_map('trim', $encabezadosOriginales);

        // Normalizar encabezados
        $encabezadosNormalizados = array_map([$this, 'normalizarEncabezado'], $encabezadosOriginales);

        // Para el procesamiento, usar los normalizados
        $encabezados = $encabezadosNormalizados;

        Log::info('Procesamiento CSV:', [
            'originales' => $encabezadosOriginales,
            'normalizados' => $encabezadosNormalizados,
            'encoding' => $encoding,
            'delimitador' => $delimitador === ',' ? 'coma' : ($delimitador === ';' ? 'punto y coma' : 'otro')
        ]);

        $registros = [];
        $totalFilas = 0;

        for ($i = 1; $i < count($lineas); $i++) {
            $lineaTrimmed = trim($lineas[$i]);
            
            // Saltar líneas vacías o comentarios
            if (empty($lineaTrimmed) || str_starts_with($lineaTrimmed, '#')) {
                continue;
            }

            $valores = str_getcsv($lineas[$i], $delimitador);

            if (empty(array_filter($valores))) {
                continue;
            }

            if (count($valores) === count($encabezados)) {
                $registro = array_combine($encabezados, $valores);
                $registros[] = $registro;
                $totalFilas++;
                
                // Log del primer registro para debugging
                if ($i === 1) {
                    Log::info('Primer registro procesado:', $registro);
                }
            } else {
                throw new \InvalidArgumentException(
                    "Fila " . ($i + 1) . ": tiene " . count($valores) . " columnas, se esperaban " . count($encabezados) . 
                    "\nEncabezados: " . implode(', ', $encabezados)
                );
            }
        }

        return [
            'registros' => $registros,
            'total_filas' => $totalFilas,
            'encabezados' => $encabezados,
            'encabezados_originales' => $encabezadosOriginales,
            'encabezados_normalizados' => $encabezadosNormalizados,
            'encoding' => $encoding,
            'delimitador' => $delimitador
        ];
    }

    /**
     * Detectar delimitador
     */
    protected function detectarDelimitador(string $contenido): string
    {
        $delimitadores = [',', ';', "\t", '|'];
        $primeraLinea = strtok($contenido, "\n");

        $conteos = [];
        foreach ($delimitadores as $delimitador) {
            $conteos[$delimitador] = substr_count($primeraLinea, $delimitador);
        }

        arsort($conteos);
        return array_key_first($conteos);
    }

    /**
     * Validar encabezados
     */
    protected function validarEncabezados(array $encabezadosNormalizados): array
    {
        $faltantes = [];

        foreach ($this->encabezadosRequeridos as $requerido) {
            $encontrado = false;
            
            // Buscar directamente
            if (in_array($requerido, $encabezadosNormalizados)) {
                $encontrado = true;
            }
            
            // Buscar en variaciones si existe en el mapa
            if (!$encontrado && isset($this->mapaEncabezados[$requerido])) {
                foreach ($this->mapaEncabezados[$requerido] as $variacion) {
                    $variacionNormalizada = $this->normalizarEncabezado($variacion);
                    if (in_array($variacionNormalizada, $encabezadosNormalizados)) {
                        $encontrado = true;
                        break;
                    }
                }
            }

            if (!$encontrado) {
                $variaciones = isset($this->mapaEncabezados[$requerido]) 
                    ? implode(', ', array_slice($this->mapaEncabezados[$requerido], 0, 3))
                    : $requerido;
                $faltantes[] = "$requerido (acepta: $variaciones)";
            }
        }

        if (!empty($faltantes)) {
            Log::warning('Encabezados faltantes:', [
                'faltantes' => $faltantes,
                'encabezados_recibidos' => $encabezadosNormalizados
            ]);
        }

        return $faltantes;
    }

    /**
     * Generar plantilla CSV
     */
    public function generarPlantilla(): string
    {
        $encabezados = [
            'identidad',
            'nombre',
            'apellido',
            'telefono',
            'correo',
            'direccion',
            'generoM_F',
            'fecha_nacimiento',
            'comentarios',
            'id_empresa',
            'id_puesto',
            'fechaIngreso',  // Con mayúscula para que coincida
            'fechaEgreso',
            'area',
            'activo',
            'Comentario',
        ];

        $ejemplos = [
            '0801199012345',
            'Juan',
            'Pérez López',
            '+50412345678',
            'juan.perez@example.com',
            'Col. Centro, Calle Principal',
            'M',
            '1990-05-20',
            'Comentarios del candidato',
            '1',
            '5',
            '01/27/2024',  // Formato mm/dd/yyyy
            '',
            'Producción',
            's',
            'Comentario del ingreso',
        ];

        $csv = implode(',', $encabezados) . "\n";
        $csv .= "# FORMATO DE IDENTIDAD: 13 dígitos sin guiones. Ejemplo: 0801199012345\n";
        $csv .= "# FORMATO DE FECHAS: mm/dd/yyyy (Mes/Día/Año) ejemplo: 01/27/2024\n";
        $csv .= "# TAMBIÉN ACEPTA: dd/mm/yyyy o yyyy-mm-dd\n";
        $csv .= "# No modifique los nombres de las columnas\n";
        $csv .= implode(',', array_map(function ($val) {
            return '"' . str_replace('"', '""', $val) . '"';
        }, $ejemplos)) . "\n";

        return $csv;
    }
}