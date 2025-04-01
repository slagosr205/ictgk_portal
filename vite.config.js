import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/animate.min.css',
                'resources/sass/app.scss',
                'resources/css/app.css',
                'resources/css/form.css',
                'resources/css/jquery.dataTables.min.css',
                'resources/css/material-dashboard.min.css',
                           
                'resources/js/app.js',
                'resources/js/lib/jquery.counterup.js' ,  
                'resources/js/chart_custom.js',
              'resources/js/lib/easing.min.js' ,
                'resources/js/lib/isotope.pkgd.min.js' ,
                'resources/js/lib/waypoints.min.js' ,
                'resources/js/empresas.js',
                'resources/js/departamentos.js',
                'resources/js/libpuestos/puestos.js',
                'resources/js/libpuestos/modalpuestos.js',
                'resources/js/libpuestos/alertpuestos.js',
                'resources/js/empresas.ajax.js'
            ],
            refresh: true,
        }),
    ],
});
