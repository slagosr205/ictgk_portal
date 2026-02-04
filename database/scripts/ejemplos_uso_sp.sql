-- =====================================================
-- Ejemplos de Uso de Stored Procedures
-- Base de Datos: ictgk_portal
-- =====================================================

USE ictgk_portal;

-- =====================================================
-- 1. SP: sp_listar_candidatos_ingresos
-- =====================================================
-- Descripción: Lista candidatos con su información de ingreso activo
-- Parámetros:
--   - p_id_empresa: ID de la empresa (NULL para todas)

-- Ejemplo 1: Listar candidatos de la empresa 1
CALL sp_listar_candidatos_ingresos(1);

-- Ejemplo 2: Listar candidatos de la empresa 2
CALL sp_listar_candidatos_ingresos(2);

-- Ejemplo 3: Listar TODOS los candidatos (todas las empresas)
CALL sp_listar_candidatos_ingresos(NULL);

-- Ejemplo 4: Usando el SP en una aplicación
-- En Laravel:
-- $candidatos = DB::select('CALL sp_listar_candidatos_ingresos(?)', [$empresaId]);

-- =====================================================
-- 2. SP: sp_historial_candidato
-- =====================================================
-- Descripción: Muestra el historial completo de un candidato
-- Parámetros:
--   - p_identidad: Identidad del candidato

-- Ejemplo 1: Ver historial de un candidato específico
CALL sp_historial_candidato('0501199200350');

-- Ejemplo 2: Ver historial incluyendo candidatos sin ingresos
CALL sp_historial_candidato('9999999999999');

-- =====================================================
-- 3. Consultas Útiles con los Nuevos Índices
-- =====================================================

-- Consulta 1: Buscar candidatos activos con ingresos
-- (Usa idx_egresos_identidad_activo)
SELECT
    c.nombre,
    c.apellido,
    c.identidad,
    e.nombre AS empresa
FROM candidatos c
INNER JOIN egresos_ingresos ei ON c.identidad = ei.identidad
INNER JOIN empresas e ON ei.id_empresa = e.id
WHERE ei.activo = 's';

-- Ver el plan de ejecución (debe usar índices)
EXPLAIN SELECT
    c.nombre,
    c.apellido,
    c.identidad
FROM candidatos c
INNER JOIN egresos_ingresos ei ON c.identidad = ei.identidad
WHERE ei.activo = 's';

-- Consulta 2: Estadísticas por empresa
SELECT
    e.nombre AS empresa,
    COUNT(DISTINCT c.identidad) AS total_candidatos_activos,
    COUNT(ei.id) AS total_ingresos_activos
FROM empresas e
LEFT JOIN egresos_ingresos ei ON e.id = ei.id_empresa AND ei.activo = 's'
LEFT JOIN candidatos c ON ei.identidad = c.identidad
GROUP BY e.id, e.nombre
ORDER BY total_candidatos_activos DESC;

-- Consulta 3: Candidatos disponibles (sin ingreso activo)
SELECT
    c.identidad,
    c.nombre,
    c.apellido,
    c.telefono,
    c.correo
FROM candidatos c
LEFT JOIN egresos_ingresos ei ON c.identidad = ei.identidad AND ei.activo = 's'
WHERE ei.id IS NULL
  AND c.activo = 's'
ORDER BY c.apellido, c.nombre;

-- =====================================================
-- 4. Consultas para Reportes
-- =====================================================

-- Reporte 1: Candidatos por estado
SELECT
    CASE
        WHEN c.activo = 's' THEN 'Disponible'
        WHEN c.activo = 'n' THEN 'Trabajando'
        WHEN c.activo = 'x' THEN 'Bloqueado'
        ELSE 'Otro'
    END AS estado,
    COUNT(*) AS cantidad
FROM candidatos c
GROUP BY c.activo
ORDER BY cantidad DESC;

-- Reporte 2: Ingresos del último mes
SELECT
    e.nombre AS empresa,
    COUNT(*) AS ingresos_ultimo_mes
FROM egresos_ingresos ei
INNER JOIN empresas e ON ei.id_empresa = e.id
WHERE ei.fechaIngreso >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)
  AND ei.activo = 's'
GROUP BY e.id, e.nombre
ORDER BY ingresos_ultimo_mes DESC;

-- Reporte 3: Puestos más demandados
SELECT
    p.nombrepuesto AS puesto,
    d.nombredepartamento AS departamento,
    COUNT(*) AS total_ingresos
FROM egresos_ingresos ei
INNER JOIN puestos p ON ei.id_puesto = p.id
INNER JOIN departamentos d ON p.departamento_id = d.id
WHERE ei.fechaIngreso >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
GROUP BY p.id, p.nombrepuesto, d.nombredepartamento
ORDER BY total_ingresos DESC
LIMIT 10;

-- Reporte 4: Tasa de recomendación por empresa
SELECT
    e.nombre AS empresa,
    COUNT(*) AS total_egresos,
    SUM(CASE WHEN ei.recomendado = 's' THEN 1 ELSE 0 END) AS recomendados,
    ROUND(
        (SUM(CASE WHEN ei.recomendado = 's' THEN 1 ELSE 0 END) / COUNT(*)) * 100,
        2
    ) AS porcentaje_recomendacion
FROM egresos_ingresos ei
INNER JOIN empresas e ON ei.id_empresa = e.id
WHERE ei.fechaEgreso IS NOT NULL
  AND ei.recomendado IS NOT NULL
GROUP BY e.id, e.nombre
ORDER BY porcentaje_recomendacion DESC;

-- =====================================================
-- 5. Consultas de Validación Post-Cambios
-- =====================================================

-- Verificar que no hay datos inconsistentes
SELECT 'Validando integridad de datos...' AS '';

-- 1. Ingresos sin candidatos (debe ser 0)
SELECT
    'Ingresos sin candidatos' AS validacion,
    COUNT(*) AS cantidad,
    CASE WHEN COUNT(*) = 0 THEN 'OK' ELSE 'ERROR' END AS estado
FROM egresos_ingresos ei
LEFT JOIN candidatos c ON ei.identidad = c.identidad
WHERE c.identidad IS NULL;

-- 2. Puestos sin departamento (debe ser 0)
SELECT
    'Puestos sin departamento' AS validacion,
    COUNT(*) AS cantidad,
    CASE WHEN COUNT(*) = 0 THEN 'OK' ELSE 'ERROR' END AS estado
FROM puestos p
LEFT JOIN departamentos d ON p.departamento_id = d.id
WHERE d.id IS NULL;

-- 3. Departamentos sin empresa (debe ser 0)
SELECT
    'Departamentos sin empresa' AS validacion,
    COUNT(*) AS cantidad,
    CASE WHEN COUNT(*) = 0 THEN 'OK' ELSE 'ERROR' END AS estado
FROM departamentos d
LEFT JOIN empresas e ON d.empresa_id = e.id
WHERE e.id IS NULL;

-- 4. Relaciones puesto-empresa correctas (debe ser 0)
SELECT
    'Relaciones puesto-empresa incorrectas' AS validacion,
    COUNT(*) AS cantidad,
    CASE WHEN COUNT(*) = 0 THEN 'OK' ELSE 'ERROR' END AS estado
FROM egresos_ingresos ei
INNER JOIN puestos p ON ei.id_puesto = p.id
INNER JOIN departamentos d ON p.departamento_id = d.id
WHERE d.empresa_id != ei.id_empresa;

-- =====================================================
-- 6. Pruebas de Rendimiento
-- =====================================================

-- Habilitar profiling
SET profiling = 1;

-- Ejecutar algunas consultas
SELECT * FROM candidatos WHERE identidad = '0501199200350';

SELECT * FROM egresos_ingresos
WHERE identidad = '0501199200350' AND activo = 's';

CALL sp_listar_candidatos_ingresos(1);

-- Ver tiempos de ejecución
SHOW PROFILES;

-- Deshabilitar profiling
SET profiling = 0;

-- =====================================================
-- 7. Consultas de Monitoreo
-- =====================================================

-- Tamaño de las tablas
SELECT
    TABLE_NAME AS tabla,
    ROUND(((DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024), 2) AS tamanio_mb,
    TABLE_ROWS AS filas
FROM INFORMATION_SCHEMA.TABLES
WHERE TABLE_SCHEMA = 'ictgk_portal'
  AND TABLE_NAME IN ('candidatos', 'egresos_ingresos', 'puestos', 'departamentos', 'empresas')
ORDER BY (DATA_LENGTH + INDEX_LENGTH) DESC;

-- Uso de índices
SELECT
    TABLE_NAME AS tabla,
    INDEX_NAME AS indice,
    CARDINALITY AS cardinalidad,
    INDEX_TYPE AS tipo
FROM INFORMATION_SCHEMA.STATISTICS
WHERE TABLE_SCHEMA = 'ictgk_portal'
  AND INDEX_NAME LIKE 'idx_%'
ORDER BY TABLE_NAME, INDEX_NAME;

-- =====================================================
-- 8. Integración con Laravel
-- =====================================================

/*
// En Laravel Controller o Service

// Ejemplo 1: Usar SP desde Laravel
$empresaId = 1;
$candidatos = DB::select('CALL sp_listar_candidatos_ingresos(?)', [$empresaId]);

// Ejemplo 2: Ver historial de candidato
$identidad = '0501199200350';
$historial = DB::select('CALL sp_historial_candidato(?)', [$identidad]);

// Ejemplo 3: Manejo de errores de constraints
try {
    $ingreso = new Ingresos();
    $ingreso->identidad = $request->identidad;
    $ingreso->id_empresa = $request->id_empresa;
    $ingreso->id_puesto = $request->id_puesto; // Si no pertenece a la empresa, el trigger lo rechaza
    // ... más campos
    $ingreso->save();
} catch (\PDOException $e) {
    if (strpos($e->getMessage(), 'El puesto no pertenece') !== false) {
        return response()->json([
            'error' => 'El puesto seleccionado no pertenece a la empresa elegida',
            'code' => 'INVALID_PUESTO_EMPRESA'
        ], 400);
    }
    throw $e;
}

// Ejemplo 4: Usar índices eficientemente
// Las consultas con where en identidad + activo usarán el índice compuesto automáticamente
$ingresoActivo = Ingresos::where('identidad', $identidad)
    ->where('activo', 's')
    ->first();
*/

-- =====================================================
-- FIN DE EJEMPLOS
-- =====================================================
