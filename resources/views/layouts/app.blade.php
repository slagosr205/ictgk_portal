<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $branding['name'] ?? config('app.name', 'Portal HHRR') }}</title>
    @php
        $fav = $branding['assets']['favicon_url'] ?? null;
    @endphp
    @if ($fav)
        <link rel="icon" href="{{ $fav }}" type="image/svg+xml">
    @endif
    
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />

    @vite([
        'resources/css/animate.min.css',
        'resources/sass/app.scss',
        'resources/css/app.css',
        'resources/css/form.css',
        'resources/css/navbar.css',
       // 'resources/css/login.css',
        'resources/css/jquery.dataTables.min.css',
        'resources/css/material-dashboard.min.css',
        'resources/js/app.js',
        'resources/js/lib/jquery.counterup.js',  
        'resources/js/chart_custom.js',
        'resources/js/lib/easing.min.js',
        //'resources/js/lib/isotope.pkgd.min.js',
        'resources/js/lib/waypoints.min.js',
        'resources/js/empresas.js',
        'resources/js/departamentos.js',
        'resources/js/libpuestos/puestos.js',
        
    ])

    @stack('styles')

    @php
        $cssVars = $branding['cssVars'] ?? [];
    @endphp
    @if (!empty($cssVars))
        <style>
            :root {
@foreach ($cssVars as $k => $v)
                {{ $k }}: {{ $v }};
@endforeach
            }
        </style>
    @endif

    <script>
        window.__APP_BRANDING__ = @json($branding ?? []);
    </script>

    @stack('scripts')
</head>

@guest
    @php
        // ABSOLUTE URL so it works even when CSS is served from Vite dev server.
        $loginBg = $branding['assets']['background_url'] ?? null;
        $loginBgStyle = $loginBg ? "--brand-login-bg: url('" . $loginBg . "');" : '';
    @endphp
    <body class="login" style="height: 100vh; overflow: hidden; {{ $loginBgStyle }}">
        <main>
            @yield('content')
        </main>
    </body>
@else
    <body>
        <div class="app-wrapper">
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
       

</script>

@php session()->forget(['status', 'message']); @endphp
    </body>
</html>
@endguest
</html>
