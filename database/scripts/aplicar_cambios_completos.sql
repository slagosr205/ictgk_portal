-- =====================================================
-- Script de Aplicación Completa de Cambios
-- Base de Datos: ictgk_portal
-- Fecha: 2026-02-03
-- =====================================================
-- IMPORTANTE:
-- 1. HACER BACKUP COMPLETO antes de ejecutar
-- 2. Ejecutar primero en ambiente de prueba
-- 3. Leer comentarios y verificar cada sección
-- =====================================================

-- Configuración inicial
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

USE ictgk_portal;

-- =====================================================
-- SECCIÓN 1: VALIDACIONES PREVIAS
-- =====================================================
SELECT '========================================' AS '';
SELECT 'FASE 1: VALIDACIONES PREVIAS' AS '';
SELECT '========================================' AS '';

-- Verificar datos inconsistentes
SELECT 'Verificando ingresos sin candidatos...' AS '';
SELECT COUNT(*) AS ingresos_sin_candidato
FROM egresos_ingresos ei
LEFT JOIN candidatos c ON ei.identidad = c.identidad
WHERE c.identidad IS NULL;

-- Si el resultado anterior es > 0, DETENER y corregir primero
-- Usar: database/scripts/identificar_ingresos_sin_candidatos.sql

SELECT 'Verificando puestos sin departamento...' AS '';
SELECT COUNT(*) AS puestos_sin_departamento
FROM puestos p
LEFT JOIN departamentos d ON p.departamento_id = d.id
WHERE d.id IS NULL;

SELECT 'Verificando departamentos sin empresa...' AS '';
SELECT COUNT(*) AS departamentos_sin_empresa
FROM departamentos d
LEFT JOIN empresas e ON d.empresa_id = e.id
WHERE e.id IS NULL;

SELECT 'Verificando relación puesto-empresa incorrecta...' AS '';
SELECT COUNT(*) AS relaciones_incorrectas
FROM egresos_ingresos ei
INNER JOIN puestos p ON ei.id_puesto = p.id
INNER JOIN departamentos d ON p.departamento_id = d.id
WHERE d.empresa_id != ei.id_empresa;

-- ⚠️ Si alguna de las consultas anteriores retorna > 0:
-- DETENER, corregir los datos y volver a ejecutar las validaciones

-- =====================================================
-- SECCIÓN 2: ÍNDICES
-- =====================================================
SELECT '========================================' AS '';
SELECT 'FASE 2: CREANDO ÍNDICES' AS '';
SELECT '========================================' AS '';

-- Índice en candidatos.identidad
SELECT 'Creando índice: idx_candidatos_identidad...' AS '';
CREATE INDEX IF NOT EXISTS idx_candidatos_identidad
ON candidatos(identidad);

-- Índice compuesto en egresos_ingresos
SELECT 'Creando índice: idx_egresos_identidad_activo...' AS '';
CREATE INDEX IF NOT EXISTS idx_egresos_identidad_activo
ON egresos_ingresos(identidad, activo);

-- Índice en egresos_ingresos.id_puesto
SELECT 'Creando índice: idx_egresos_id_puesto...' AS '';
CREATE INDEX IF NOT EXISTS idx_egresos_id_puesto
ON egresos_ingresos(id_puesto);

-- Índice en puestos.departamento_id
SELECT 'Creando índice: idx_puestos_departamento...' AS '';
CREATE INDEX IF NOT EXISTS idx_puestos_departamento
ON puestos(departamento_id);

-- Índice en departamentos.empresa_id
SELECT 'Creando índice: idx_departamentos_empresa...' AS '';
CREATE INDEX IF NOT EXISTS idx_departamentos_empresa
ON departamentos(empresa_id);

-- Analizar tablas
SELECT 'Analizando tablas para optimizar índices...' AS '';
ANALYZE TABLE candidatos;
ANALYZE TABLE egresos_ingresos;
ANALYZE TABLE puestos;
ANALYZE TABLE departamentos;

SELECT 'Índices creados exitosamente' AS '';

-- =====================================================
-- SECCIÓN 3: FOREIGN KEYS
-- =====================================================
SELECT '========================================' AS '';
SELECT 'FASE 3: CREANDO FOREIGN KEYS' AS '';
SELECT '========================================' AS '';

-- Verificar que las tablas sean InnoDB
SELECT 'Verificando que las tablas sean InnoDB...' AS '';
SELECT
    TABLE_NAME,
    ENGINE
FROM INFORMATION_SCHEMA.TABLES
WHERE TABLE_SCHEMA = 'ictgk_portal'
  AND TABLE_NAME IN ('candidatos', 'egresos_ingresos', 'puestos', 'departamentos', 'empresas')
  AND ENGINE != 'InnoDB';

-- Si alguna tabla no es InnoDB, convertirla:
-- ALTER TABLE nombre_tabla ENGINE=InnoDB;

-- FK: egresos_ingresos -> puestos
SELECT 'Creando FK: fk_ingresos_puestos...' AS '';

-- Verificar si ya existe
SET @fk_exists = (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
    WHERE CONSTRAINT_SCHEMA = 'ictgk_portal'
      AND TABLE_NAME = 'egresos_ingresos'
      AND CONSTRAINT_NAME = 'fk_ingresos_puestos'
);

-- Crear solo si no existe
SET @sql = IF(@fk_exists = 0,
    'ALTER TABLE egresos_ingresos
     ADD CONSTRAINT fk_ingresos_puestos
     FOREIGN KEY (id_puesto)
     REFERENCES puestos(id)
     ON UPDATE CASCADE
     ON DELETE RESTRICT',
    'SELECT "FK fk_ingresos_puestos ya existe" AS mensaje'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- FK: puestos -> departamentos
SELECT 'Creando FK: fk_puestos_departamentos...' AS '';

SET @fk_exists = (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
    WHERE CONSTRAINT_SCHEMA = 'ictgk_portal'
      AND TABLE_NAME = 'puestos'
      AND CONSTRAINT_NAME = 'fk_puestos_departamentos'
);

SET @sql = IF(@fk_exists = 0,
    'ALTER TABLE puestos
     ADD CONSTRAINT fk_puestos_departamentos
     FOREIGN KEY (departamento_id)
     REFERENCES departamentos(id)
     ON UPDATE CASCADE
     ON DELETE RESTRICT',
    'SELECT "FK fk_puestos_departamentos ya existe" AS mensaje'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- FK: departamentos -> empresas
SELECT 'Creando FK: fk_departamentos_empresas...' AS '';

SET @fk_exists = (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
    WHERE CONSTRAINT_SCHEMA = 'ictgk_portal'
      AND TABLE_NAME = 'departamentos'
      AND CONSTRAINT_NAME = 'fk_departamentos_empresas'
);

SET @sql = IF(@fk_exists = 0,
    'ALTER TABLE departamentos
     ADD CONSTRAINT fk_departamentos_empresas
     FOREIGN KEY (empresa_id)
     REFERENCES empresas(id)
     ON UPDATE CASCADE
     ON DELETE RESTRICT',
    'SELECT "FK fk_departamentos_empresas ya existe" AS mensaje'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SELECT 'Foreign keys creadas exitosamente' AS '';

-- =====================================================
-- SECCIÓN 4: TRIGGERS
-- =====================================================
SELECT '========================================' AS '';
SELECT 'FASE 4: CREANDO TRIGGERS' AS '';
SELECT '========================================' AS '';

-- Eliminar triggers si existen (para recrear)
DROP TRIGGER IF EXISTS trg_validar_puesto_empresa_ins;
DROP TRIGGER IF EXISTS trg_validar_puesto_empresa_upd;

-- Trigger para INSERT
SELECT 'Creando trigger: trg_validar_puesto_empresa_ins...' AS '';

DELIMITER $$

CREATE TRIGGER trg_validar_puesto_empresa_ins
BEFORE INSERT ON egresos_ingresos
FOR EACH ROW
BEGIN
    DECLARE v_empresa_puesto INT;

    -- Obtener la empresa del puesto
    SELECT d.empresa_id INTO v_empresa_puesto
    FROM puestos p
    INNER JOIN departamentos d ON d.id = p.departamento_id
    WHERE p.id = NEW.id_puesto;

    -- Si el puesto no pertenece a la empresa, rechazar
    IF v_empresa_puesto IS NULL OR v_empresa_puesto != NEW.id_empresa THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El puesto no pertenece a la empresa seleccionada';
    END IF;
END$$

DELIMITER ;

-- Trigger para UPDATE
SELECT 'Creando trigger: trg_validar_puesto_empresa_upd...' AS '';

DELIMITER $$

CREATE TRIGGER trg_validar_puesto_empresa_upd
BEFORE UPDATE ON egresos_ingresos
FOR EACH ROW
BEGIN
    DECLARE v_empresa_puesto INT;

    -- Solo validar si cambian id_puesto o id_empresa
    IF NEW.id_puesto != OLD.id_puesto OR NEW.id_empresa != OLD.id_empresa THEN
        SELECT d.empresa_id INTO v_empresa_puesto
        FROM puestos p
        INNER JOIN departamentos d ON d.id = p.departamento_id
        WHERE p.id = NEW.id_puesto;

        IF v_empresa_puesto IS NULL OR v_empresa_puesto != NEW.id_empresa THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'El puesto no pertenece a la empresa seleccionada';
        END IF;
    END IF;
END$$

DELIMITER ;

SELECT 'Triggers creados exitosamente' AS '';

-- =====================================================
-- SECCIÓN 5: STORED PROCEDURES
-- =====================================================
SELECT '========================================' AS '';
SELECT 'FASE 5: CREANDO STORED PROCEDURES' AS '';
SELECT '========================================' AS '';

-- Eliminar SPs si existen (para recrear)
DROP PROCEDURE IF EXISTS sp_listar_candidatos_ingresos;
DROP PROCEDURE IF EXISTS sp_historial_candidato;

-- SP: Listar candidatos con ingresos
SELECT 'Creando SP: sp_listar_candidatos_ingresos...' AS '';

DELIMITER $$

CREATE PROCEDURE sp_listar_candidatos_ingresos(
    IN p_id_empresa INT
)
BEGIN
    SELECT
        c.id,
        c.identidad,
        c.nombre,
        c.apellido,
        c.telefono,
        c.correo,
        c.direccion,
        c.generoM_F,
        c.activo AS activo_candidato,
        c.fecha_nacimiento,
        c.comentarios,

        -- Indicador de ingreso activo
        CASE
            WHEN ei.id IS NOT NULL THEN 's'
            ELSE 'n'
        END AS activo_ingreso,

        ei.id AS ingreso_id,
        ei.fechaIngreso,
        ei.area,
        ei.ComenProhibir,
        ei.prohibirIngreso,
        ei.bloqueo_recomendado,
        ei.recomendado,

        -- Información del puesto y empresa
        p.nombrepuesto,
        d.nombredepartamento,
        e.nombre AS empresa_nombre

    FROM candidatos c

    LEFT JOIN egresos_ingresos ei
        ON ei.identidad = c.identidad
        AND ei.activo = 's'
        AND (p_id_empresa IS NULL OR ei.id_empresa = p_id_empresa)

    LEFT JOIN puestos p ON ei.id_puesto = p.id
    LEFT JOIN departamentos d ON p.departamento_id = d.id
    LEFT JOIN empresas e ON d.empresa_id = e.id

    WHERE p_id_empresa IS NULL OR ei.id_empresa = p_id_empresa OR ei.id IS NULL

    ORDER BY c.apellido, c.nombre;
END $$

DELIMITER ;

-- SP: Historial de candidato
SELECT 'Creando SP: sp_historial_candidato...' AS '';

DELIMITER $$

CREATE PROCEDURE sp_historial_candidato(
    IN p_identidad VARCHAR(20)
)
BEGIN
    SELECT
        c.nombre,
        c.apellido,
        c.identidad,
        c.activo AS activo_candidato,
        ei.id AS ingreso_id,
        e.nombre AS empresa,
        p.nombrepuesto AS puesto,
        d.nombredepartamento AS departamento,
        ei.fechaIngreso,
        ei.fechaEgreso,
        ei.activo AS activo_ingreso,
        ei.recomendado,
        ei.Comentario,
        DATEDIFF(
            COALESCE(ei.fechaEgreso, CURDATE()),
            ei.fechaIngreso
        ) AS dias_laborados
    FROM candidatos c
    LEFT JOIN egresos_ingresos ei ON c.identidad = ei.identidad
    LEFT JOIN empresas e ON ei.id_empresa = e.id
    LEFT JOIN puestos p ON ei.id_puesto = p.id
    LEFT JOIN departamentos d ON p.departamento_id = d.id
    WHERE c.identidad = p_identidad
    ORDER BY ei.fechaIngreso DESC;
END $$

DELIMITER ;

SELECT 'Stored procedures creados exitosamente' AS '';

-- =====================================================
-- SECCIÓN 6: VERIFICACIÓN FINAL
-- =====================================================
SELECT '========================================' AS '';
SELECT 'FASE 6: VERIFICACIÓN FINAL' AS '';
SELECT '========================================' AS '';

-- Verificar foreign keys
SELECT 'Foreign Keys creadas:' AS '';
SELECT
    CONSTRAINT_NAME,
    TABLE_NAME,
    REFERENCED_TABLE_NAME,
    UPDATE_RULE,
    DELETE_RULE
FROM INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS
WHERE CONSTRAINT_SCHEMA = 'ictgk_portal'
ORDER BY TABLE_NAME;

-- Verificar índices
SELECT 'Índices creados:' AS '';
SELECT
    TABLE_NAME,
    INDEX_NAME,
    GROUP_CONCAT(COLUMN_NAME ORDER BY SEQ_IN_INDEX) AS columnas
FROM INFORMATION_SCHEMA.STATISTICS
WHERE TABLE_SCHEMA = 'ictgk_portal'
  AND INDEX_NAME LIKE 'idx_%'
GROUP BY TABLE_NAME, INDEX_NAME
ORDER BY TABLE_NAME;

-- Verificar triggers
SELECT 'Triggers creados:' AS '';
SELECT
    TRIGGER_NAME,
    EVENT_MANIPULATION,
    EVENT_OBJECT_TABLE,
    ACTION_TIMING
FROM INFORMATION_SCHEMA.TRIGGERS
WHERE TRIGGER_SCHEMA = 'ictgk_portal'
ORDER BY EVENT_OBJECT_TABLE;

-- Verificar stored procedures
SELECT 'Stored Procedures creados:' AS '';
SELECT
    ROUTINE_NAME,
    ROUTINE_TYPE,
    CREATED
FROM INFORMATION_SCHEMA.ROUTINES
WHERE ROUTINE_SCHEMA = 'ictgk_portal'
ORDER BY ROUTINE_NAME;

-- Restaurar configuración
SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;

-- =====================================================
-- RESUMEN FINAL
-- =====================================================
SELECT '========================================' AS '';
SELECT 'IMPLEMENTACIÓN COMPLETADA' AS '';
SELECT '========================================' AS '';
SELECT 'Índices: 5' AS '';
SELECT 'Foreign Keys: 3' AS '';
SELECT 'Triggers: 2' AS '';
SELECT 'Stored Procedures: 2' AS '';
SELECT '========================================' AS '';
SELECT 'Próximos pasos:' AS '';
SELECT '1. Probar en la aplicación web' AS '';
SELECT '2. Verificar logs de Laravel' AS '';
SELECT '3. Monitorear rendimiento' AS '';
SELECT '4. Documentar cambios' AS '';
SELECT '========================================' AS '';
