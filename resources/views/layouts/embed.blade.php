<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? ($branding['name'] ?? config('app.name', 'Portal')) }}</title>

    @php
        $fav = $branding['assets']['favicon_url'] ?? null;
        $cssVars = $branding['cssVars'] ?? [];
    @endphp

    @if ($fav)
        <link rel="icon" href="{{ $fav }}" type="image/svg+xml">
    @endif

    @vite([
        'resources/css/app.css',
        'resources/css/navbar.css',
        'resources/css/form.css',
        'resources/css/animate.css',
        'resources/js/app.js'
    ])

    @if (!empty($cssVars))
        <style>
            :root {
@foreach ($cssVars as $k => $v)
                {{ $k }}: {{ $v }};
@endforeach
            }
        </style>
    @endif

    @stack('styles')

    <style>
        /* Minimal chrome for iframe embedding */
        html, body { height: 100%; }
        body {
            margin: 0;
            background: var(--light);
            color: var(--text-primary);
            overflow-y: auto;
        }
        .embed-shell {
            min-height: 100%;
            padding: 18px;
        }
        .embed-container {
            max-width: 1240px;
            margin: 0 auto;
        }
        @media (max-width: 576px) {
            .embed-shell { padding: 12px; }
        }
    </style>
</head>
<body class="embed">
    <div class="embed-shell">
        <div class="embed-container">
            @yield('content')
        </div>
    </div>

    @stack('scripts')
</body>
</html>
