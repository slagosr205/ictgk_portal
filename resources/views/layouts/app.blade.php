<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Portal HHRR') }}</title>
    <link rel="icon" href="{{Storage::url('logo__Altia.svg')}}" type="image/svg+xml">
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
  />
    <!-- Scripts -->
    @vite([
                
                
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
                'resources/js/libpuestos/puestos.js'
                ])
               
               
</head>
@guest
    <body class="login">
    @else
    <body>
    
@endguest

       
        <x-menu-bar :logos="1"/>
        <main class="py-4 container-fluid">
           
                @yield('content')
                @yield('table')
                @yield('gestion')
                @yield('informes')
                @yield('perfiles')
                @yield('empresas')
                @yield('departamentos')
                @yield('puestos')
                @yield('puestosjs')
                @yield('empresasjs')
        </main>
    </div>
    <x-historico />
</body>
</html>
    