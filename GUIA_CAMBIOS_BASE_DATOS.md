# 📋 Guía de Cambios a la Base de Datos - Portal ICTGK

## 📑 Índice
1. [Pre-requisitos y Validaciones](#pre-requisitos)
2. [Backup y Seguridad](#backup)
3. [Fase 1: Limpieza de Datos](#fase-1)
4. [Fase 2: Índices](#fase-2)
5. [Fase 3: Foreign Keys](#fase-3)
6. [Fase 4: Triggers](#fase-4)
7. [Fase 5: Stored Procedures](#fase-5)
8. [Verificación y Pruebas](#verificacion)
9. [Plan de Rollback](#rollback)
10. [Monitoreo Post-Implementación](#monitoreo)

---

## 🔍 Pre-requisitos y Validaciones {#pre-requisitos}

### ☑️ Checklist Pre-Ejecución

- [ ] **Ambiente de prueba disponible**
  - Tener una copia de la base de datos en ambiente de desarrollo/staging
  - NO ejecutar primero en producción

- [ ] **Permisos necesarios**
  - Usuario con privilegios `ALTER TABLE`, `CREATE TRIGGER`, `CREATE PROCEDURE`
  - Usuario con privilegios `CREATE INDEX`
  - Acceso de escritura al servidor de base de datos

- [ ] **Ventana de mantenimiento**
  - Notificar a usuarios del sistema
  - Tiempo estimado: 30-60 minutos
  - Horario recomendado: Fuera de horas laborales

- [ ] **Herramientas preparadas**
  - Cliente MySQL (MySQL Workbench, DBeaver, o CLI)
  - Editor de texto para logs
  - Script de rollback preparado

---

## 💾 Backup y Seguridad {#backup}

### ☑️ Checklist de Backup

- [ ] **Backup completo de la base de datos**
```bash
# Ejecutar ANTES de cualquier cambio
mysqldump -u root -p ictgk_portal > backup_ictgk_portal_$(date +%Y%m%d_%H%M%S).sql

# Verificar que el backup se creó correctamente
ls -lh backup_ictgk_portal_*.sql
```

- [ ] **Backup de tablas críticas individuales**
```bash
mysqldump -u root -p ictgk_portal \
  candidatos \
  egresos_ingresos \
  puestos \
  departamentos \
  empresas \
  > backup_tablas_criticas_$(date +%Y%m%d_%H%M%S).sql
```

- [ ] **Documentar estado actual**
```sql
-- Guardar en un archivo: estado_actual.sql
SELECT 'Conteo de registros' AS info;

SELECT 'candidatos' AS tabla, COUNT(*) AS registros FROM candidatos
UNION ALL
SELECT 'egresos_ingresos', COUNT(*) FROM egresos_ingresos
UNION ALL
SELECT 'puestos', COUNT(*) FROM puestos
UNION ALL
SELECT 'departamentos', COUNT(*) FROM departamentos
UNION ALL
SELECT 'empresas', COUNT(*) FROM empresas;

-- Listar constraints existentes
SELECT
    TABLE_NAME,
    CONSTRAINT_NAME,
    CONSTRAINT_TYPE
FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
WHERE TABLE_SCHEMA = 'ictgk_portal'
ORDER BY TABLE_NAME, CONSTRAINT_TYPE;

-- Listar índices existentes
SELECT
    TABLE_NAME,
    INDEX_NAME,
    COLUMN_NAME,
    INDEX_TYPE
FROM INFORMATION_SCHEMA.STATISTICS
WHERE TABLE_SCHEMA = 'ictgk_portal'
ORDER BY TABLE_NAME, INDEX_NAME;
```

- [ ] **Copiar backup a ubicación segura**
  - Copiar archivos de backup a servidor diferente
  - O guardar en almacenamiento en la nube

---

## 🧹 Fase 1: Limpieza de Datos {#fase-1}

### ☑️ Checklist de Limpieza

**⚠️ IMPORTANTE**: Esta fase identifica y corrige inconsistencias ANTES de agregar constraints.

#### 1.1 Identificar Datos Inconsistentes

- [ ] **Ejecutar script de diagnóstico**
```bash
mysql -u root -p ictgk_portal < database/scripts/identificar_ingresos_sin_candidatos.sql > reporte_inconsistencias.txt
```

- [ ] **Revisar el reporte generado**
  - Abrir `reporte_inconsistencias.txt`
  - Documentar cuántos registros tienen problemas
  - Identificar las identidades problemáticas

#### 1.2 Validar Relaciones

- [ ] **Verificar ingresos sin candidatos**
```sql
-- Debe retornar 0 filas para poder continuar
SELECT
    ei.id,
    ei.identidad,
    'Sin candidato' AS problema
FROM egresos_ingresos ei
LEFT JOIN candidatos c ON ei.identidad = c.identidad
WHERE c.identidad IS NULL;
```

**Si retorna filas**:
  - [ ] Crear los candidatos faltantes O
  - [ ] Marcar ingresos como inactivos O
  - [ ] Eliminar registros huérfanos (con aprobación)

- [ ] **Verificar puestos sin departamento**
```sql
-- Debe retornar 0 filas
SELECT
    p.id,
    p.nombrepuesto,
    p.departamento_id,
    'Departamento no existe' AS problema
FROM puestos p
LEFT JOIN departamentos d ON p.departamento_id = d.id
WHERE d.id IS NULL;
```

**Si retorna filas**: Corregir antes de continuar

- [ ] **Verificar departamentos sin empresa**
```sql
-- Debe retornar 0 filas
SELECT
    d.id,
    d.nombredepartamento,
    d.empresa_id,
    'Empresa no existe' AS problema
FROM departamentos d
LEFT JOIN empresas e ON d.empresa_id = e.id
WHERE e.id IS NULL;
```

**Si retorna filas**: Corregir antes de continuar

- [ ] **Verificar relación puesto-empresa en ingresos**
```sql
-- Esta es la validación que hará el trigger
SELECT
    ei.id,
    ei.identidad,
    ei.id_puesto,
    ei.id_empresa,
    p.nombrepuesto,
    d.nombredepartamento,
    d.empresa_id AS empresa_del_departamento,
    'Puesto no pertenece a la empresa' AS problema
FROM egresos_ingresos ei
INNER JOIN puestos p ON ei.id_puesto = p.id
INNER JOIN departamentos d ON p.departamento_id = d.id
WHERE d.empresa_id != ei.id_empresa;
```

**Si retorna filas**:
  - [ ] Corregir id_empresa O
  - [ ] Corregir id_puesto
  - [ ] Documentar cada corrección realizada

#### 1.3 Corregir Datos (Si es necesario)

- [ ] **Crear script de corrección personalizado**
```sql
-- Ejemplo de corrección (PERSONALIZAR según sus datos)
-- GUARDAR EN: database/scripts/correccion_datos_YYYYMMDD.sql

-- Ejemplo 1: Crear candidatos faltantes
/*
INSERT INTO candidatos (identidad, nombre, apellido, telefono, correo, direccion, generoM_F, fecha_nacimiento, activo, created_at, updated_at)
VALUES
('0501199200350', 'NOMBRE', 'APELLIDO', '0000-0000', 'temp@email.com', 'DIRECCION PENDIENTE', 'M', '1990-01-01', 'n', NOW(), NOW());
*/

-- Ejemplo 2: Marcar ingresos problemáticos como inactivos
/*
UPDATE egresos_ingresos
SET activo = 'x',
    Comentario = CONCAT(COALESCE(Comentario, ''), ' [CORREGIDO: Registro huérfano]')
WHERE identidad IN ('identidad1', 'identidad2');
*/
```

- [ ] **Ejecutar script de corrección**
```bash
mysql -u root -p ictgk_portal < database/scripts/correccion_datos_YYYYMMDD.sql
```

- [ ] **Validar que todas las correcciones se aplicaron**
  - Re-ejecutar las consultas de validación de 1.2
  - Todas deben retornar 0 filas

---

## 📊 Fase 2: Índices {#fase-2}

### ☑️ Checklist de Índices

**Por qué primero**: Los índices mejoran el rendimiento y son necesarios antes de los foreign keys.

- [ ] **Verificar índices existentes**
```sql
SHOW INDEX FROM candidatos;
SHOW INDEX FROM egresos_ingresos;
```

- [ ] **Crear índice en candidatos.identidad** (si no existe)
```sql
-- Verificar primero si existe
SELECT COUNT(*) AS existe
FROM INFORMATION_SCHEMA.STATISTICS
WHERE TABLE_SCHEMA = 'ictgk_portal'
  AND TABLE_NAME = 'candidatos'
  AND INDEX_NAME = 'idx_candidatos_identidad';

-- Si retorna 0, crear el índice:
CREATE INDEX idx_candidatos_identidad
ON candidatos(identidad);

-- Verificar creación
SHOW INDEX FROM candidatos WHERE Key_name = 'idx_candidatos_identidad';
```

- [ ] **Crear índice compuesto en egresos_ingresos**
```sql
-- Verificar primero si existe
SELECT COUNT(*) AS existe
FROM INFORMATION_SCHEMA.STATISTICS
WHERE TABLE_SCHEMA = 'ictgk_portal'
  AND TABLE_NAME = 'egresos_ingresos'
  AND INDEX_NAME = 'idx_egresos_identidad_activo';

-- Si retorna 0, crear el índice:
CREATE INDEX idx_egresos_identidad_activo
ON egresos_ingresos(identidad, activo);

-- Verificar creación
SHOW INDEX FROM egresos_ingresos WHERE Key_name = 'idx_egresos_identidad_activo';
```

- [ ] **Crear índice en egresos_ingresos.id_puesto**
```sql
CREATE INDEX idx_egresos_id_puesto
ON egresos_ingresos(id_puesto);
```

- [ ] **Crear índice en puestos.departamento_id**
```sql
CREATE INDEX idx_puestos_departamento
ON puestos(departamento_id);
```

- [ ] **Crear índice en departamentos.empresa_id**
```sql
CREATE INDEX idx_departamentos_empresa
ON departamentos(empresa_id);
```

- [ ] **Analizar tablas después de crear índices**
```sql
ANALYZE TABLE candidatos;
ANALYZE TABLE egresos_ingresos;
ANALYZE TABLE puestos;
ANALYZE TABLE departamentos;
```

- [ ] **Verificar mejora de rendimiento**
```sql
-- Probar consulta ANTES tenía mal rendimiento
EXPLAIN SELECT *
FROM egresos_ingresos ei
INNER JOIN candidatos c ON ei.identidad = c.identidad
WHERE ei.activo = 's';

-- Verificar que ahora usa los índices (columna "key" debe mostrar el índice)
```

---

## 🔗 Fase 3: Foreign Keys {#fase-3}

### ☑️ Checklist de Foreign Keys

**⚠️ CRÍTICO**: Solo ejecutar después de completar Fase 1 y Fase 2.

#### 3.1 Verificar Pre-requisitos

- [ ] **Fase 1 completada** (todas las validaciones retornan 0 filas)
- [ ] **Fase 2 completada** (todos los índices creados)
- [ ] **Tablas usan InnoDB**
```sql
SELECT
    TABLE_NAME,
    ENGINE
FROM INFORMATION_SCHEMA.TABLES
WHERE TABLE_SCHEMA = 'ictgk_portal'
  AND TABLE_NAME IN ('candidatos', 'egresos_ingresos', 'puestos', 'departamentos', 'empresas');

-- Si alguna no es InnoDB, convertir:
-- ALTER TABLE nombre_tabla ENGINE=InnoDB;
```

#### 3.2 Agregar Foreign Keys

- [ ] **FK: egresos_ingresos → puestos**
```sql
-- Verificar que no existe
SELECT CONSTRAINT_NAME
FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
WHERE TABLE_SCHEMA = 'ictgk_portal'
  AND TABLE_NAME = 'egresos_ingresos'
  AND CONSTRAINT_NAME = 'fk_ingresos_puestos';

-- Si no existe, crear:
ALTER TABLE egresos_ingresos
ADD CONSTRAINT fk_ingresos_puestos
FOREIGN KEY (id_puesto)
REFERENCES puestos(id)
ON UPDATE CASCADE
ON DELETE RESTRICT;

-- Verificar creación
SELECT
    CONSTRAINT_NAME,
    REFERENCED_TABLE_NAME,
    UPDATE_RULE,
    DELETE_RULE
FROM INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS
WHERE CONSTRAINT_SCHEMA = 'ictgk_portal'
  AND CONSTRAINT_NAME = 'fk_ingresos_puestos';
```

**Nota**: `ON DELETE RESTRICT` previene eliminar puestos que tienen ingresos asociados.

- [ ] **FK: puestos → departamentos**
```sql
ALTER TABLE puestos
ADD CONSTRAINT fk_puestos_departamentos
FOREIGN KEY (departamento_id)
REFERENCES departamentos(id)
ON UPDATE CASCADE
ON DELETE RESTRICT;

-- Verificar
SELECT * FROM INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS
WHERE CONSTRAINT_NAME = 'fk_puestos_departamentos';
```

- [ ] **FK: departamentos → empresas**
```sql
ALTER TABLE departamentos
ADD CONSTRAINT fk_departamentos_empresas
FOREIGN KEY (empresa_id)
REFERENCES empresas(id)
ON UPDATE CASCADE
ON DELETE RESTRICT;

-- Verificar
SELECT * FROM INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS
WHERE CONSTRAINT_NAME = 'fk_departamentos_empresas';
```

#### 3.3 Verificar Todas las Foreign Keys

- [ ] **Listar todas las FKs creadas**
```sql
SELECT
    TABLE_NAME,
    CONSTRAINT_NAME,
    REFERENCED_TABLE_NAME,
    UPDATE_RULE,
    DELETE_RULE
FROM INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS
WHERE CONSTRAINT_SCHEMA = 'ictgk_portal'
ORDER BY TABLE_NAME;
```

**Resultado esperado**: 3 filas mostrando las 3 FKs creadas

#### 3.4 Probar Restricciones

- [ ] **Probar que ON DELETE RESTRICT funciona**
```sql
-- Intentar eliminar un puesto que tiene ingresos
-- Debe fallar con error
DELETE FROM puestos WHERE id = (
    SELECT id_puesto FROM egresos_ingresos LIMIT 1
);
-- Error esperado: Cannot delete or update a parent row
```

- [ ] **Probar que ON UPDATE CASCADE funciona**
```sql
-- Hacer una actualización de prueba (luego revertir)
START TRANSACTION;

-- Intentar actualizar un ID (solo para probar, luego rollback)
UPDATE empresas SET id = id WHERE id = 1;

-- Verificar que no hubo errores
ROLLBACK;
```

---

## ⚙️ Fase 4: Triggers {#fase-4}

### ☑️ Checklist de Triggers

#### 4.1 Crear Trigger de Validación

- [ ] **Verificar si ya existe el trigger**
```sql
SELECT TRIGGER_NAME
FROM INFORMATION_SCHEMA.TRIGGERS
WHERE TRIGGER_SCHEMA = 'ictgk_portal'
  AND TRIGGER_NAME = 'trg_validar_puesto_empresa_ins';

-- Si existe, eliminarlo primero:
-- DROP TRIGGER IF EXISTS trg_validar_puesto_empresa_ins;
```

- [ ] **Crear trigger para validar puesto-empresa en INSERT**
```sql
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
```

- [ ] **Crear trigger para validar puesto-empresa en UPDATE**
```sql
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
```

#### 4.2 Verificar Triggers

- [ ] **Listar triggers creados**
```sql
SELECT
    TRIGGER_NAME,
    EVENT_MANIPULATION,
    EVENT_OBJECT_TABLE,
    ACTION_TIMING
FROM INFORMATION_SCHEMA.TRIGGERS
WHERE TRIGGER_SCHEMA = 'ictgk_portal'
ORDER BY EVENT_OBJECT_TABLE, EVENT_MANIPULATION;
```

**Resultado esperado**: 2 triggers

#### 4.3 Probar Triggers

- [ ] **Probar INSERT válido**
```sql
START TRANSACTION;

-- Obtener un puesto y su empresa correcta
SELECT
    p.id AS id_puesto,
    d.empresa_id,
    p.nombrepuesto,
    e.nombre AS empresa_nombre
FROM puestos p
INNER JOIN departamentos d ON p.departamento_id = d.id
INNER JOIN empresas e ON d.empresa_id = e.id
LIMIT 1;

-- Probar insertar con datos correctos (ajustar valores)
INSERT INTO egresos_ingresos
(identidad, id_empresa, fechaIngreso, area, id_puesto, activo, forma_egreso, Comentario, recomendado, recontrataria, prohibirIngreso, ComenProhibir)
VALUES
('9999999999999', 1, '2026-01-01', 'Test', 1, 'n', '', 'PRUEBA', 's', 's', 'n', '');

-- Si no hay error, el trigger funciona
ROLLBACK;
```

- [ ] **Probar INSERT inválido (debe fallar)**
```sql
START TRANSACTION;

-- Intentar insertar con puesto de empresa diferente
-- Debe generar error: "El puesto no pertenece a la empresa seleccionada"
INSERT INTO egresos_ingresos
(identidad, id_empresa, fechaIngreso, area, id_puesto, activo, forma_egreso, Comentario, recomendado, recontrataria, prohibirIngreso, ComenProhibir)
VALUES
('9999999999999', 999, '2026-01-01', 'Test', 1, 'n', '', 'PRUEBA', 's', 's', 'n', '');

-- Error esperado: El puesto no pertenece a la empresa seleccionada
ROLLBACK;
```

- [ ] **Probar UPDATE (debe funcionar similar)**
```sql
START TRANSACTION;

-- Intentar actualizar un registro existente con datos inválidos
-- Debe fallar
UPDATE egresos_ingresos
SET id_empresa = 999
WHERE id = 1;

-- Error esperado
ROLLBACK;
```

---

## 🔧 Fase 5: Stored Procedures {#fase-5}

### ☑️ Checklist de Stored Procedures

#### 5.1 Crear SP de Listado

- [ ] **Verificar si existe**
```sql
SELECT ROUTINE_NAME
FROM INFORMATION_SCHEMA.ROUTINES
WHERE ROUTINE_SCHEMA = 'ictgk_portal'
  AND ROUTINE_NAME = 'sp_listar_candidatos_ingresos';

-- Si existe, eliminarlo:
-- DROP PROCEDURE IF EXISTS sp_listar_candidatos_ingresos;
```

- [ ] **Crear stored procedure mejorado**
```sql
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

    WHERE p_id_empresa IS NULL OR ei.id_empresa = p_id_empresa

    ORDER BY c.apellido, c.nombre;
END $$

DELIMITER ;
```

#### 5.2 Probar Stored Procedure

- [ ] **Probar con empresa específica**
```sql
-- Listar candidatos de empresa 1
CALL sp_listar_candidatos_ingresos(1);
```

- [ ] **Probar con empresa específica 2**
```sql
-- Listar candidatos de empresa 2
CALL sp_listar_candidatos_ingresos(2);
```

- [ ] **Probar con NULL (todas las empresas)**
```sql
-- Listar todos los candidatos
CALL sp_listar_candidatos_ingresos(NULL);
```

- [ ] **Verificar resultados**
  - Debe retornar datos coherentes
  - Los campos de ingreso deben ser NULL para candidatos sin ingreso activo
  - Los campos de puesto/departamento/empresa deben estar correctos

#### 5.3 Crear SP adicionales (Opcional)

- [ ] **SP para obtener historial completo de un candidato**
```sql
DELIMITER $$

CREATE PROCEDURE sp_historial_candidato(
    IN p_identidad VARCHAR(20)
)
BEGIN
    SELECT
        c.nombre,
        c.apellido,
        c.identidad,
        ei.id AS ingreso_id,
        e.nombre AS empresa,
        p.nombrepuesto AS puesto,
        d.nombredepartamento AS departamento,
        ei.fechaIngreso,
        ei.fechaEgreso,
        ei.activo,
        ei.recomendado,
        ei.Comentario
    FROM candidatos c
    LEFT JOIN egresos_ingresos ei ON c.identidad = ei.identidad
    LEFT JOIN empresas e ON ei.id_empresa = e.id
    LEFT JOIN puestos p ON ei.id_puesto = p.id
    LEFT JOIN departamentos d ON p.departamento_id = d.id
    WHERE c.identidad = p_identidad
    ORDER BY ei.fechaIngreso DESC;
END $$

DELIMITER ;
```

- [ ] **Probar SP de historial**
```sql
-- Usar una identidad real de su base de datos
CALL sp_historial_candidato('0501199200350');
```

---

## ✅ Verificación y Pruebas {#verificacion}

### ☑️ Checklist de Verificación Final

#### Verificación Estructural

- [ ] **Verificar todas las constraints**
```sql
-- Debe mostrar 3 foreign keys
SELECT
    CONSTRAINT_NAME,
    TABLE_NAME,
    REFERENCED_TABLE_NAME
FROM INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS
WHERE CONSTRAINT_SCHEMA = 'ictgk_portal'
ORDER BY TABLE_NAME;
```

- [ ] **Verificar todos los índices**
```sql
-- Debe mostrar al menos 5 índices nuevos
SELECT
    TABLE_NAME,
    INDEX_NAME,
    GROUP_CONCAT(COLUMN_NAME ORDER BY SEQ_IN_INDEX) AS columnas
FROM INFORMATION_SCHEMA.STATISTICS
WHERE TABLE_SCHEMA = 'ictgk_portal'
  AND INDEX_NAME LIKE 'idx_%'
GROUP BY TABLE_NAME, INDEX_NAME
ORDER BY TABLE_NAME;
```

- [ ] **Verificar todos los triggers**
```sql
-- Debe mostrar 2 triggers
SELECT
    TRIGGER_NAME,
    EVENT_MANIPULATION,
    EVENT_OBJECT_TABLE
FROM INFORMATION_SCHEMA.TRIGGERS
WHERE TRIGGER_SCHEMA = 'ictgk_portal';
```

- [ ] **Verificar stored procedures**
```sql
-- Debe mostrar al menos 1 SP
SELECT
    ROUTINE_NAME,
    ROUTINE_TYPE,
    CREATED
FROM INFORMATION_SCHEMA.ROUTINES
WHERE ROUTINE_SCHEMA = 'ictgk_portal';
```

#### Pruebas Funcionales

- [ ] **Probar flujo completo de ingreso**
```sql
START TRANSACTION;

-- 1. Insertar candidato
INSERT INTO candidatos
(identidad, nombre, apellido, telefono, correo, direccion, generoM_F, fecha_nacimiento, activo)
VALUES
('0000000000001', 'TEST', 'PRUEBA', '0000-0000', 'test@test.com', 'Test', 'M', '1990-01-01', 's');

-- 2. Insertar ingreso válido
INSERT INTO egresos_ingresos
(identidad, id_empresa, fechaIngreso, area, id_puesto, activo, forma_egreso, Comentario, recomendado, recontrataria, prohibirIngreso, ComenProhibir)
SELECT
    '0000000000001',
    d.empresa_id,
    CURDATE(),
    'Test',
    p.id,
    's',
    '',
    'REGISTRO DE PRUEBA',
    's',
    's',
    'n',
    ''
FROM puestos p
INNER JOIN departamentos d ON p.departamento_id = d.id
LIMIT 1;

-- 3. Verificar que se creó correctamente
SELECT * FROM candidatos WHERE identidad = '0000000000001';
SELECT * FROM egresos_ingresos WHERE identidad = '0000000000001';

-- 4. Limpiar
ROLLBACK;
```

- [ ] **Probar SP con los nuevos datos**
```sql
-- Debe funcionar sin errores
CALL sp_listar_candidatos_ingresos(1);
```

- [ ] **Probar en la aplicación web**
  - [ ] Buscar un candidato existente
  - [ ] Verificar que se muestra correctamente
  - [ ] Intentar hacer un ingreso nuevo
  - [ ] Intentar hacer un egreso
  - [ ] Verificar mensajes de error claros

#### Pruebas de Rendimiento

- [ ] **Medir tiempo de consultas principales**
```sql
-- Habilitar profiling
SET profiling = 1;

-- Ejecutar consultas principales
SELECT * FROM egresos_ingresos ei
INNER JOIN candidatos c ON ei.identidad = c.identidad
WHERE ei.activo = 's';

CALL sp_listar_candidatos_ingresos(1);

-- Ver tiempos
SHOW PROFILES;

-- Debe ser más rápido que antes de los índices
```

- [ ] **Verificar planes de ejecución**
```sql
-- Debe usar índices (columna "key" populated)
EXPLAIN SELECT * FROM egresos_ingresos
WHERE identidad = '0501199200350' AND activo = 's';
```

---

## ↩️ Plan de Rollback {#rollback}

### ☑️ Scripts de Rollback

**⚠️ Solo usar en caso de problemas críticos**

#### Eliminar Triggers

```sql
-- Guardar en: rollback_triggers.sql
DROP TRIGGER IF EXISTS trg_validar_puesto_empresa_ins;
DROP TRIGGER IF EXISTS trg_validar_puesto_empresa_upd;
```

#### Eliminar Stored Procedures

```sql
-- Guardar en: rollback_procedures.sql
DROP PROCEDURE IF EXISTS sp_listar_candidatos_ingresos;
DROP PROCEDURE IF EXISTS sp_historial_candidato;
```

#### Eliminar Foreign Keys

```sql
-- Guardar en: rollback_foreign_keys.sql
ALTER TABLE egresos_ingresos DROP FOREIGN KEY fk_ingresos_puestos;
ALTER TABLE puestos DROP FOREIGN KEY fk_puestos_departamentos;
ALTER TABLE departamentos DROP FOREIGN KEY fk_departamentos_empresas;
```

#### Eliminar Índices

```sql
-- Guardar en: rollback_indices.sql
DROP INDEX idx_candidatos_identidad ON candidatos;
DROP INDEX idx_egresos_identidad_activo ON egresos_ingresos;
DROP INDEX idx_egresos_id_puesto ON egresos_ingresos;
DROP INDEX idx_puestos_departamento ON puestos;
DROP INDEX idx_departamentos_empresa ON departamentos;
```

#### Restaurar desde Backup

```bash
# Guardar en: rollback_full.sh

# Detener la aplicación
# sudo systemctl stop nombre-aplicacion

# Restaurar backup completo
mysql -u root -p ictgk_portal < backup_ictgk_portal_YYYYMMDD_HHMMSS.sql

# Reiniciar aplicación
# sudo systemctl start nombre-aplicacion
```

### ☑️ Procedimiento de Rollback

- [ ] **Documentar el problema encontrado**
- [ ] **Notificar al equipo**
- [ ] **Ejecutar scripts de rollback en orden inverso**:
  1. Triggers
  2. Stored Procedures
  3. Foreign Keys
  4. Índices
  5. (Si es necesario) Restaurar backup completo
- [ ] **Verificar estado post-rollback**
- [ ] **Analizar causa del problema**
- [ ] **Corregir en ambiente de prueba antes de reintentar**

---

## 📈 Monitoreo Post-Implementación {#monitoreo}

### ☑️ Checklist de Monitoreo

#### Primera Semana

- [ ] **Día 1: Verificar logs de Laravel**
```bash
# Ver errores de base de datos
tail -f storage/logs/laravel.log | grep -i "database\|sql\|constraint"
```

- [ ] **Día 1: Verificar rendimiento**
```sql
-- Ver queries lentas
SELECT * FROM mysql.slow_log
WHERE start_time >= DATE_SUB(NOW(), INTERVAL 1 DAY)
ORDER BY query_time DESC
LIMIT 20;
```

- [ ] **Día 2-3: Monitorear errores de constraints**
```sql
-- Buscar intentos fallidos (si hay logging)
SELECT * FROM event_logs
WHERE event_type LIKE '%error%'
  AND created_at >= DATE_SUB(NOW(), INTERVAL 3 DAY)
ORDER BY created_at DESC;
```

- [ ] **Día 7: Reporte semanal**
```sql
-- Estadísticas de la semana
SELECT
    'Total Ingresos Nuevos' AS metrica,
    COUNT(*) AS valor
FROM egresos_ingresos
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY);

SELECT
    'Total Candidatos Nuevos' AS metrica,
    COUNT(*) AS valor
FROM candidatos
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY);
```

#### Mensual

- [ ] **Ejecutar script de verificación de integridad**
```bash
mysql -u root -p ictgk_portal < database/scripts/identificar_ingresos_sin_candidatos.sql
```

- [ ] **Verificar que no hay datos inconsistentes nuevos**
- [ ] **Revisar rendimiento general**
- [ ] **Documentar lecciones aprendidas**

---

## 📝 Documentación Final

### ☑️ Documentar Todo

- [ ] **Crear reporte de implementación**
  - Fecha y hora de ejecución
  - Versión de MySQL
  - Usuario que ejecutó
  - Tiempo total
  - Problemas encontrados y soluciones
  - Estado final de cada fase

- [ ] **Actualizar documentación del proyecto**
  - Agregar esta guía al repositorio
  - Actualizar README.md
  - Actualizar diagrama de base de datos (si existe)

- [ ] **Capacitar al equipo**
  - Explicar los nuevos constraints
  - Mostrar cómo usar los stored procedures
  - Explicar mensajes de error posibles

---

## 📞 Contactos de Soporte

En caso de problemas durante la implementación:

- **Desarrollador Principal**: [Tu nombre/email]
- **DBA**: [Nombre/email del DBA]
- **Equipo de RRHH ALTIA**: portal.reclutamiento@altiabusinesspark.com

---

## 📚 Referencias

- [Documentación MySQL Foreign Keys](https://dev.mysql.com/doc/refman/8.0/en/create-table-foreign-keys.html)
- [Documentación MySQL Triggers](https://dev.mysql.com/doc/refman/8.0/en/triggers.html)
- [Documentación MySQL Stored Procedures](https://dev.mysql.com/doc/refman/8.0/en/stored-programs.html)
- [Laravel Database](https://laravel.com/docs/database)

---

**Última actualización**: 2026-02-03
**Versión del documento**: 1.0
**Autor**: Equipo de Desarrollo ICTGK Portal

---

## ✨ Resumen Ejecutivo

Esta guía cubre la implementación completa de:
- ✅ 5 índices para mejor rendimiento
- ✅ 3 foreign keys para integridad referencial
- ✅ 2 triggers para validación de negocio
- ✅ 2+ stored procedures para consultas optimizadas
- ✅ Scripts de diagnóstico y corrección
- ✅ Plan completo de rollback
- ✅ Monitoreo post-implementación

**Tiempo estimado total**: 2-4 horas (incluyendo pruebas)
**Nivel de riesgo**: Medio (con backups y ambiente de prueba: Bajo)
**Impacto en usuarios**: Mínimo (con ventana de mantenimiento)
