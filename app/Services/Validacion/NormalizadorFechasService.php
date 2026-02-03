<?php

namespace App\Services\Validacion;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class NormalizadorFechasService
{
    /**
     * Formatos de fecha comunes ordenados por prioridad
     * IMPORTANTE: El orden importa - se prueban en secuencia
     */
    protected $formatosComunes = [
        // Formato USA PRIMERO (mes/día/año) - TU FORMATO
        'm/d/Y',           // 01/27/2024 ← ESTE ES EL QUE USAS
        'm-d-Y',           // 01-27-2024
        'm/d/y',           // 01/27/24
        
        // ISO 8601 (estándar internacional)
        'Y-m-d',           // 2024-01-27
        'Y/m/d',           // 2024/01/27
        
        // Formato Latinoamérica (día/mes/año)
        'd/m/Y',           // 27/01/2024
        'd-m-Y',           // 27-01-2024
        'd.m.Y',           // 27.01.2024
        'd/m/y',           // 27/01/24
        
        // Con hora
        'm/d/Y H:i:s',     // 01/27/2024 14:30:00
        'Y-m-d H:i:s',     // 2024-01-27 14:30:00
        'd/m/Y H:i:s',     // 27/01/2024 14:30:00
        
        // Otros
        'y-m-d',           // 24-01-27
    ];

    /**
     * Detectar formato de fecha en un conjunto de fechas
     */
    public function detectarFormato(array $fechas): ?string
    {
        $fechasLimpias = array_filter($fechas, function($fecha) {
            return !empty($fecha) && $fecha !== '-' && $fecha !== 'N/A';
        });

        if (empty($fechasLimpias)) {
            Log::warning('No hay fechas para analizar');
            return null;
        }

        Log::info('Detectando formato de fechas:', [
            'total_fechas' => count($fechasLimpias),
            'muestra' => array_slice($fechasLimpias, 0, 3)
        ]);

        // Probar cada formato con las fechas de ejemplo
        foreach ($this->formatosComunes as $formato) {
            $exitosos = 0;
            $totalProbados = 0;
            $errores = [];
            
            // Probar con un máximo de 10 fechas de muestra
            $muestraFechas = array_slice($fechasLimpias, 0, 10);
            
            foreach ($muestraFechas as $fecha) {
                $totalProbados++;
                
                try {
                    $date = Carbon::createFromFormat($formato, trim($fecha));
                    
                    // IMPORTANTE: Validar que no haya caracteres extra
                    if ($date && $date->format($formato) === trim($fecha) && $this->esFechaValida($date)) {
                        $exitosos++;
                    }
                } catch (\Exception $e) {
                    $errores[] = $e->getMessage();
                    continue;
                }
            }

            // Si al menos el 80% de las fechas son válidas con este formato
            $porcentaje = $totalProbados > 0 ? ($exitosos / $totalProbados) : 0;
            
            if ($porcentaje >= 0.8) {
                Log::info("✓ Formato detectado: {$formato} ({$exitosos}/{$totalProbados} válidas)");
                return $formato;
            } else if ($porcentaje > 0) {
                Log::debug("Formato {$formato}: {$exitosos}/{$totalProbados} válidas ({$porcentaje}%)");
            }
        }

        Log::warning('No se pudo detectar formato de fecha', [
            'fechas_probadas' => $muestraFechas ?? []
        ]);

        return null;
    }

    /**
     * Normalizar una fecha a formato Y-m-d
     */
    public function normalizar(?string $fecha, ?string $formatoDetectado = null): ?string
    {
        if (empty($fecha) || $fecha === '-' || $fecha === 'N/A') {
            return null;
        }

        // Limpiar espacios
        $fecha = trim($fecha);

        // Si tenemos un formato detectado, usarlo primero
        if ($formatoDetectado) {
            try {
                $date = Carbon::createFromFormat($formatoDetectado, $fecha);
                if ($date && $this->esFechaValida($date)) {
                    $resultado = $date->format('Y-m-d');
                    Log::debug("Fecha normalizada con formato detectado: {$fecha} → {$resultado}");
                    return $resultado;
                }
            } catch (\Exception $e) {
                Log::debug("Error con formato detectado {$formatoDetectado}: " . $e->getMessage());
            }
        }

        // Intentar con todos los formatos
        foreach ($this->formatosComunes as $formato) {
            try {
                $date = Carbon::createFromFormat($formato, $fecha);
                
                if ($date && $this->esFechaValida($date)) {
                    $resultado = $date->format('Y-m-d');
                    Log::debug("Fecha normalizada: {$fecha} ({$formato}) → {$resultado}");
                    return $resultado;
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        // Último intento: dejar que Carbon lo intente parsear automáticamente
        try {
            $date = Carbon::parse($fecha);
            
            if ($date && $this->esFechaValida($date)) {
                $resultado = $date->format('Y-m-d');
                Log::info("Fecha parseada automáticamente: {$fecha} → {$resultado}");
                return $resultado;
            }
        } catch (\Exception $e) {
            Log::warning("No se pudo parsear la fecha: {$fecha}", [
                'error' => $e->getMessage()
            ]);
        }

        return null;
    }

    /**
     * Validar que una fecha sea razonable
     */
    protected function esFechaValida(Carbon $date): bool
    {
        $ahora = Carbon::now();
        
        // Validaciones de rango
        if ($date->year < 1900) {
            return false;
        }

        if ($date->year > $ahora->year + 10) {
            return false;
        }

        // Validar que sea una fecha real (no como 31/02/2024)
        try {
            $revalidada = Carbon::createFromFormat('Y-m-d', $date->format('Y-m-d'));
            if ($revalidada->format('Y-m-d') !== $date->format('Y-m-d')) {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * Validar formato de fecha de un array de fechas
     */
    public function validarFechas(array $fechas): array
    {
        $resultado = [
            'formato_detectado' => null,
            'validas' => 0,
            'invalidas' => 0,
            'errores' => []
        ];

        // Detectar formato
        $formato = $this->detectarFormato($fechas);
        $resultado['formato_detectado'] = $formato;

        // Validar cada fecha
        foreach ($fechas as $index => $fecha) {
            if (empty($fecha)) {
                continue;
            }

            $normalizada = $this->normalizar($fecha, $formato);
            
            if ($normalizada) {
                $resultado['validas']++;
            } else {
                $resultado['invalidas']++;
                $resultado['errores'][] = [
                    'fila' => $index + 1,
                    'fecha_original' => $fecha,
                    'mensaje' => 'Formato de fecha no reconocido'
                ];
            }
        }

        return $resultado;
    }

    /**
     * Obtener formatos sugeridos para el usuario
     */
    public function obtenerFormatosSugeridos(): array
    {
        return [
            'Estados Unidos (Recomendado para tu configuración)' => [
                'formato' => 'm/d/Y',
                'ejemplo' => '01/27/2024',
                'descripcion' => 'Mes/Día/Año'
            ],
            'ISO 8601' => [
                'formato' => 'Y-m-d',
                'ejemplo' => '2024-01-27',
                'descripcion' => 'Año-Mes-Día'
            ],
            'Latinoamérica' => [
                'formato' => 'd/m/Y',
                'ejemplo' => '27/01/2024',
                'descripcion' => 'Día/Mes/Año'
            ]
        ];
    }
}
