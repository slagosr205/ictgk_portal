# ✅ Checklist Rápido - Cambios Base de Datos

## 📋 Pre-Ejecución

- [ ] Hacer backup completo: `mysqldump -u root -p ictgk_portal > backup_$(date +%Y%m%d_%H%M%S).sql`
- [ ] Probar en ambiente de desarrollo primero
- [ ] Notificar a usuarios (ventana de mantenimiento)
- [ ] Tener plan de rollback preparado

---

## 🔍 Fase 1: Validar Datos (CRÍTICO)

```bash
mysql -u root -p ictgk_portal < database/scripts/identificar_ingresos_sin_candidatos.sql
```

**Resultado esperado**: Todas las consultas deben retornar **0 filas**

Si hay datos inconsistentes:
- [ ] Identificar registros problemáticos
- [ ] Corregir o limpiar datos
- [ ] Re-validar hasta que todo esté en 0

---

## 📊 Fase 2: Aplicar Cambios

```bash
mysql -u root -p ictgk_portal < database/scripts/aplicar_cambios_completos.sql
```

Este script crea:
- ✅ 5 índices
- ✅ 3 foreign keys
- ✅ 2 triggers
- ✅ 2 stored procedures

**Tiempo estimado**: 5-15 minutos

---

## ✅ Fase 3: Verificar

### Verificación Básica
```sql
-- Debe mostrar 3 FKs
SELECT COUNT(*) FROM INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS
WHERE CONSTRAINT_SCHEMA = 'ictgk_portal';

-- Debe mostrar 5+ índices
SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
WHERE TABLE_SCHEMA = 'ictgk_portal' AND INDEX_NAME LIKE 'idx_%';

-- Debe mostrar 2 triggers
SELECT COUNT(*) FROM INFORMATION_SCHEMA.TRIGGERS
WHERE TRIGGER_SCHEMA = 'ictgk_portal';

-- Debe mostrar 2+ SPs
SELECT COUNT(*) FROM INFORMATION_SCHEMA.ROUTINES
WHERE ROUTINE_SCHEMA = 'ictgk_portal';
```

### Prueba Funcional
```sql
-- Probar SP
CALL sp_listar_candidatos_ingresos(1);

-- Probar trigger (debe fallar con mensaje claro)
-- Intentar insertar ingreso con puesto de otra empresa
```

---

## 🌐 Fase 4: Probar en Aplicación

- [ ] Buscar un candidato existente
- [ ] Ver su ficha personal
- [ ] Intentar hacer un ingreso nuevo
- [ ] Verificar que los errores sean claros
- [ ] Revisar logs de Laravel: `tail -f storage/logs/laravel.log`

---

## 📈 Fase 5: Monitoreo (Primera Semana)

### Día 1
- [ ] Verificar logs de errores
- [ ] Verificar rendimiento de consultas
- [ ] Revisar feedback de usuarios

### Día 7
- [ ] Ejecutar reporte de integridad
- [ ] Verificar que no hay datos nuevos inconsistentes
- [ ] Documentar problemas encontrados

---

## 🚨 Si Algo Sale Mal

### Rollback Rápido

```sql
-- 1. Eliminar triggers
DROP TRIGGER IF EXISTS trg_validar_puesto_empresa_ins;
DROP TRIGGER IF EXISTS trg_validar_puesto_empresa_upd;

-- 2. Eliminar SPs
DROP PROCEDURE IF EXISTS sp_listar_candidatos_ingresos;
DROP PROCEDURE IF EXISTS sp_historial_candidato;

-- 3. Eliminar FKs
ALTER TABLE egresos_ingresos DROP FOREIGN KEY fk_ingresos_puestos;
ALTER TABLE puestos DROP FOREIGN KEY fk_puestos_departamentos;
ALTER TABLE departamentos DROP FOREIGN KEY fk_departamentos_empresas;

-- 4. Eliminar índices
DROP INDEX idx_candidatos_identidad ON candidatos;
DROP INDEX idx_egresos_identidad_activo ON egresos_ingresos;
DROP INDEX idx_egresos_id_puesto ON egresos_ingresos;
DROP INDEX idx_puestos_departamento ON puestos;
DROP INDEX idx_departamentos_empresa ON departamentos;
```

### Restaurar desde Backup
```bash
mysql -u root -p ictgk_portal < backup_YYYYMMDD_HHMMSS.sql
```

---

## 📚 Documentos de Referencia

- **Guía completa**: [GUIA_CAMBIOS_BASE_DATOS.md](GUIA_CAMBIOS_BASE_DATOS.md)
- **Script principal**: [database/scripts/aplicar_cambios_completos.sql](database/scripts/aplicar_cambios_completos.sql)
- **Ejemplos de uso**: [database/scripts/ejemplos_uso_sp.sql](database/scripts/ejemplos_uso_sp.sql)
- **Diagnóstico**: [database/scripts/identificar_ingresos_sin_candidatos.sql](database/scripts/identificar_ingresos_sin_candidatos.sql)

---

## 📞 Soporte

**En caso de problemas**: portal.reclutamiento@altiabusinesspark.com

---

## ✨ Resultado Final

Al completar, tendrás:
- 🔒 **Integridad referencial** (Foreign Keys)
- ⚡ **Mejor rendimiento** (Índices)
- 🛡️ **Validación automática** (Triggers)
- 🚀 **Consultas optimizadas** (Stored Procedures)
- 🎯 **Datos consistentes** (Sin registros huérfanos)

**Tiempo total estimado**: 30-60 minutos
