<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

/**
 * Comandos para despliegue en producción
 * Ejecutar estos comandos al subir el proyecto a producción
 */

// 1. Limpiar caché
Route::get('/deploy/clear-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    
    return 'Caché limpiada correctamente';
});

// 2. Crear enlace simbólico (si no existe)
Route::get('/deploy/storage-link', function () {
    try {
        if (!file_exists(public_path('storage'))) {
            Artisan::call('storage:link');
            return 'Enlace simbólico creado correctamente';
        }
        return 'El enlace simbólico ya existe';
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});

// 3. Optimizar para producción
Route::get('/deploy/optimize', function () {
    Artisan::call('config:cache');
    Artisan::call('route:cache');
    Artisan::call('view:cache');
    
    return 'Aplicación optimizada para producción';
});

// 4. Ejecutar todos los comandos de despliegue
Route::get('/deploy/all', function () {
    $output = [];
    
    try {
        // Limpiar caché
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        $output[] = 'Caché limpiada';
        
        // Crear enlace simbólico
        if (!file_exists(public_path('storage'))) {
            Artisan::call('storage:link');
            $output[] = 'Enlace simbólico creado';
        } else {
            $output[] = 'Enlace simbólico ya existe';
        }
        
        // Optimizar
        Artisan::call('config:cache');
        Artisan::call('route:cache');
        Artisan::call('view:cache');
        $output[] = 'Aplicación optimizada';
        
        // Verificar permisos
        $storagePath = storage_path('app/public');
        if (is_dir($storagePath)) {
            chmod($storagePath, 0755);
            $output[] = 'Permisos verificados';
        }
        
    } catch (\Exception $e) {
        $output[] = 'Error: ' . $e->getMessage();
    }
    
    return response()->json([
        'status' => 'completed',
        'output' => $output
    ]);
});