-- =====================================================
-- Script para identificar ingresos sin candidatos
-- =====================================================
-- Este script ayuda a identificar registros en la tabla
-- egresos_ingresos que no tienen un candidato asociado
-- en la tabla candidatos
-- =====================================================

-- 1. Identificar ingresos sin candidato asociado
SELECT
    ei.id AS ingreso_id,
    ei.identidad,
    ei.id_empresa,
    ei.fechaIngreso,
    ei.fechaEgreso,
    ei.activo,
    ei.created_at,
    e.nombre AS empresa_nombre,
    'Sin candidato asociado' AS problema
FROM
    egresos_ingresos ei
LEFT JOIN
    empresas e ON ei.id_empresa = e.id
LEFT JOIN
    candidatos c ON ei.identidad = c.identidad
WHERE
    c.identidad IS NULL
ORDER BY
    ei.created_at DESC;

-- 2. Contar cuántos registros problemáticos existen
SELECT
    COUNT(*) AS total_ingresos_sin_candidato
FROM
    egresos_ingresos ei
LEFT JOIN
    candidatos c ON ei.identidad = c.identidad
WHERE
    c.identidad IS NULL;

-- 3. Agrupar por empresa para ver dónde hay más problemas
SELECT
    e.nombre AS empresa_nombre,
    COUNT(ei.id) AS cantidad_ingresos_sin_candidato
FROM
    egresos_ingresos ei
LEFT JOIN
    empresas e ON ei.id_empresa = e.id
LEFT JOIN
    candidatos c ON ei.identidad = c.identidad
WHERE
    c.identidad IS NULL
GROUP BY
    e.nombre
ORDER BY
    cantidad_ingresos_sin_candidato DESC;

-- 4. Verificar si las identidades existen pero con formato diferente
SELECT
    ei.identidad AS identidad_ingreso,
    GROUP_CONCAT(DISTINCT c.identidad) AS posibles_candidatos_similares
FROM
    egresos_ingresos ei
LEFT JOIN
    candidatos c ON REPLACE(ei.identidad, '-', '') = REPLACE(c.identidad, '-', '')
WHERE
    ei.identidad NOT IN (SELECT identidad FROM candidatos)
GROUP BY
    ei.identidad;

-- =====================================================
-- OPCIONAL: Script para limpiar datos (USAR CON CUIDADO)
-- =====================================================
-- IMPORTANTE: Antes de ejecutar cualquier DELETE o UPDATE,
-- haga un backup completo de la base de datos
-- =====================================================

-- 5. Ver detalle completo de los registros problemáticos antes de hacer cambios
SELECT
    ei.*,
    e.nombre AS empresa_nombre,
    p.puesto_descrip AS puesto_nombre
FROM
    egresos_ingresos ei
LEFT JOIN
    empresas e ON ei.id_empresa = e.id
LEFT JOIN
    puestos p ON ei.id_puesto = p.id
LEFT JOIN
    candidatos c ON ei.identidad = c.identidad
WHERE
    c.identidad IS NULL;

-- 6. OPCIÓN A: Eliminar registros de ingresos sin candidato (PELIGROSO - HACER BACKUP PRIMERO)
-- DESCOMENTAR SOLO SI ESTÁ SEGURO
/*
DELETE FROM egresos_ingresos
WHERE identidad NOT IN (SELECT identidad FROM candidatos);
*/

-- 7. OPCIÓN B: Marcar como inactivos los registros problemáticos en lugar de eliminarlos
-- DESCOMENTAR SOLO SI ESTÁ SEGURO
/*
UPDATE egresos_ingresos
SET activo = 'x',
    Comentario = CONCAT(COALESCE(Comentario, ''), ' [REGISTRO HUÉRFANO - Sin candidato asociado]')
WHERE identidad NOT IN (SELECT identidad FROM candidatos)
AND activo != 'x';
*/

-- 8. Ver estadísticas generales de integridad de datos
SELECT
    'Total Ingresos' AS metrica,
    COUNT(*) AS valor
FROM egresos_ingresos
UNION ALL
SELECT
    'Ingresos con Candidato' AS metrica,
    COUNT(DISTINCT ei.id) AS valor
FROM egresos_ingresos ei
INNER JOIN candidatos c ON ei.identidad = c.identidad
UNION ALL
SELECT
    'Ingresos sin Candidato' AS metrica,
    COUNT(ei.id) AS valor
FROM egresos_ingresos ei
LEFT JOIN candidatos c ON ei.identidad = c.identidad
WHERE c.identidad IS NULL
UNION ALL
SELECT
    'Total Candidatos' AS metrica,
    COUNT(*) AS valor
FROM candidatos;

-- =====================================================
-- Notas de uso:
-- =====================================================
-- 1. Ejecute primero las consultas SELECT (queries 1-4)
--    para identificar el alcance del problema
--
-- 2. Documente todos los registros problemáticos
--
-- 3. Investigue por qué estos registros existen sin
--    candidatos asociados
--
-- 4. Solo ejecute los comandos DELETE o UPDATE después
--    de tener un backup completo y aprobación del
--    equipo de desarrollo
--
-- 5. Considere implementar constraints de foreign key
--    para prevenir este problema en el futuro
-- =====================================================
