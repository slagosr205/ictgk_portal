# Instrucciones de Despliegue en Producción

## Problemas Solucionados:

### 1. Sesión Expirada Prematuramente ✅
- Modificado middleware `CheckSessionExpiration.php` para verificar autenticación primero
- Corregida lógica de expiración de sesión
- Mejorado middleware `Authenticate.php` para evitar actualizaciones innecesarias

### 2. Imágenes y Recursos en Producción ✅
- Verificado enlace simbólico `public/storage -> storage/app/public`
- Creado archivo de rutas de despliegue `routes/deploy.php`
- Configurado sistema para gestión de recursos

## Comandos para Producción:

Ejecuta estos comandos después de subir el proyecto al servidor:

```bash
# 1. Limpiar toda la caché
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 2. Crear enlace simbólico (si no existe)
php artisan storage:link

# 3. Optimizar para producción
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 4. Verificar permisos
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

## Acceso Web a Comandos de Despliegue:

Si tienes acceso web al servidor, puedes usar:
- `GET /deploy/clear-cache` - Limpiar caché
- `GET /deploy/storage-link` - Crear enlace simbólico
- `GET /deploy/optimize` - Optimizar aplicación
- `GET /deploy/all` - Ejecutar todo

## Notas Importantes:

1. **Sesión**: El middleware ahora solo actualiza la última actividad si el usuario está autenticado
2. **Storage**: El enlace simbólico ya existe y funciona correctamente
3. **Recursos**: Las imágenes están en `storage/app/public/` y accesibles vía `/storage/`
4. **Seguridad**: Recuerda proteger las rutas de despliegue o eliminarlas después de usar

## Verificación:

Para verificar que todo funciona:
1. Inicia sesión
2. Ejecuta una acción (ej: consultar candidato)
3. La sesión debería mantenerse activa según la configuración
4. Las imágenes deberían mostrarse correctamente