@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">
    <style>
        .branding-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr);
            gap: 1rem;
        }
        @media (min-width: 992px) {
            .branding-grid {
                grid-template-columns: minmax(0, 1.35fr) minmax(0, 0.9fr);
            }
        }
        .palette-grid {
            display: grid;
            grid-template-columns: repeat(1, minmax(0, 1fr));
            gap: 0.75rem;
        }
        @media (min-width: 768px) {
            .palette-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
        .pal-item {
            border: 1px solid rgba(0,0,0,0.08);
            border-radius: 12px;
            padding: 0.75rem;
            background: #fff;
        }
        .pal-row {
            display: grid;
            grid-template-columns: 22px minmax(0, 1fr);
            gap: 0.6rem;
            align-items: start;
        }
        .pal-swatch {
            width: 22px;
            height: 22px;
            border-radius: 6px;
            border: 1px solid rgba(0,0,0,0.15);
            margin-top: 6px;
        }
        .pal-controls {
            display: grid;
            grid-template-columns: 46px minmax(0, 1fr);
            gap: 0.5rem;
            align-items: center;
        }
        .pal-controls input[type="color"] {
            width: 46px;
            height: 34px;
            padding: 0;
            border: none;
            background: transparent;
        }
        .preview-shell {
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid rgba(0,0,0,0.08);
            background: #fff;
        }
        .preview-navbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            background: var(--pv-secondary);
            color: white;
        }
        .preview-navbar .title {
            font-weight: 700;
            letter-spacing: 0.2px;
        }
        .preview-navbar .badge {
            background: rgba(255,255,255,0.92);
            color: rgba(0,0,0,0.75);
            border-radius: 999px;
            padding: 0.35rem 0.6rem;
            font-weight: 600;
        }
        .preview-login {
            padding: 1rem;
            min-height: 260px;
            background-color: var(--pv-light);
            background-image: var(--pv-login-bg);
            background-size: cover;
            background-position: center;
            display: grid;
            place-items: center;
        }
        .preview-card {
            width: min(420px, 100%);
            border-radius: 16px;
            border: 1px solid rgba(0,0,0,0.08);
            background: rgba(255,255,255,0.94);
            backdrop-filter: blur(6px);
            box-shadow: 0 12px 30px rgba(0,0,0,0.14);
            padding: 1rem;
        }
        .preview-card .btn-primary {
            background: var(--pv-primary);
            border-color: var(--pv-primary);
        }
        .preview-card .btn-outline {
            background: transparent;
            border: 1px solid var(--pv-accent);
            color: var(--pv-accent);
        }
        .preview-tiles {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.5rem;
        }
        .tile {
            border-radius: 12px;
            border: 1px solid rgba(0,0,0,0.08);
            padding: 0.6rem;
            background: #fff;
        }
        .tile .label {
            font-size: 12px;
            color: rgba(0,0,0,0.55);
            margin-bottom: 0.35rem;
        }
        .tile .value {
            font-weight: 700;
            font-size: 13px;
            color: rgba(0,0,0,0.78);
        }
        .tile .bar {
            height: 10px;
            border-radius: 999px;
            border: 1px solid rgba(0,0,0,0.12);
        }
    </style>

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h3 class="mb-1">Branding</h3>
            <div class="text-muted">Brand aplicado: <strong>{{ $currentBrandKey }}</strong></div>
            <div class="text-muted">APP_BRAND (deploy): <strong>{{ $envBrandKey }}</strong></div>
            @if (config('branding.runtime_switch'))
                @php $isRuntimeActive = (bool) ($brandingRow->is_active ?? false); @endphp
                <div class="text-muted">DB activo: <strong>{{ $isRuntimeActive ? $brandKey : $currentBrandKey }}</strong></div>
            @else
                <div class="text-muted small">Para activar desde la UI: <code>BRANDING_RUNTIME_SWITCH=true</code></div>
            @endif
        </div>

        <div class="d-flex flex-wrap gap-2">
            @foreach ($brandCatalog as $k)
                <a class="btn btn-sm {{ $k === $brandKey ? 'btn-primary' : 'btn-outline-primary' }}" href="{{ route('admin.branding.edit', ['brandKey' => $k]) }}">
                    {{ $k }}
                    @if ($k === $currentBrandKey)
                        <span class="ms-1">(aplicado)</span>
                    @endif
                </a>
            @endforeach

            @if (config('branding.runtime_switch'))
                <form method="POST" action="{{ route('admin.branding.activate', ['brandKey' => $brandKey]) }}">
                    @csrf
                    @method('PUT')
                    <button class="btn btn-sm {{ ($brandingRow->is_active ?? false) ? 'btn-success' : 'btn-outline-success' }}" type="submit">
                        {{ ($brandingRow->is_active ?? false) ? 'Activo' : 'Activar' }}
                    </button>
                </form>
            @endif
        </div>
    </div>

    @if ($brandKey !== $currentBrandKey)
        <div class="alert alert-warning">
            Estas editando <strong>{{ $brandKey }}</strong>, pero el brand aplicado actualmente es <strong>{{ $currentBrandKey }}</strong>.
            Para ver estos cambios en la aplicacion, haz click en <strong>Activar</strong>.
        </div>
    @endif

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Revisa los campos:</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="branding-grid">
        <div>
            <div class="card">
                <div class="card-header">
                    <strong>Configuracion</strong>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.branding.update', ['brandKey' => $brandKey]) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Nombre visible</label>
                            <input class="form-control" name="name" value="{{ old('name', $brandingRow->name) }}" data-preview="name" />
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Titulo del portal</label>
                            <input class="form-control" name="texts[portal_title]" value="{{ old('texts.portal_title', $brandingRow->texts['portal_title'] ?? '') }}" data-preview="portal_title" />
                            <div class="form-text">Texto que aparece en la barra superior.</div>
                        </div>

                        <hr />

                        @php
                            $p = $brandingRow->palette ?? [];
                            $fields = [
                                'primary' => 'Primary',
                                'secondary' => 'Secondary',
                                'accent' => 'Accent',
                                'light' => 'Light',
                                'dark' => 'Dark',
                                'danger' => 'Danger',
                                'success' => 'Success',
                                'warning' => 'Warning',
                                'text_primary' => 'Text Primary',
                                'text_secondary' => 'Text Secondary',
                                'border' => 'Border',
                            ];
                        @endphp

                        <div class="mb-2">
                            <div class="d-flex align-items-center justify-content-between">
                                <strong>Paleta</strong>
                                <span class="text-muted small">Selector + Hex (se actualiza el preview en vivo)</span>
                            </div>
                        </div>

                        <div class="palette-grid">
                            @foreach ($fields as $k => $label)
                                @php
                                    $val = old('palette.' . $k, $p[$k] ?? '');
                                    $val = is_string($val) ? trim($val) : '';
                                    $val = $val !== '' ? (str_starts_with($val, '#') ? $val : ('#' . $val)) : '';
                                @endphp
                                <div class="pal-item">
                                    <div class="pal-row">
                                        <div class="pal-swatch" data-swatch="{{ $k }}" style="background: transparent;"></div>
                                        <div>
                                            <div class="d-flex align-items-center justify-content-between">
                                                <label class="form-label mb-1">{{ $label }}</label>
                                                <span class="small text-muted" data-hex-label="{{ $k }}">{{ $val ?: '' }}</span>
                                            </div>
                                            <div class="pal-controls">
                                                <input type="color" value="{{ $val ?: '#000000' }}" data-color="{{ $k }}" />
                                                <input class="form-control" name="palette[{{ $k }}]" value="{{ $val }}" placeholder="#RRGGBB" data-hex="{{ $k }}" data-preview-color="{{ $k }}" />
                                            </div>
                                            <div class="form-text">Ej: #32C36C</div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <hr />

                        <div class="row g-2">
                            <div class="col-12 col-md-6">
                                <label class="form-label">Logo</label>
                                <input type="file" class="form-control" name="logo" accept="image/*,.svg" data-file-preview="logo" />
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">Logo (claro)</label>
                                <input type="file" class="form-control" name="logo_light" accept="image/*,.svg" data-file-preview="logo_light" />
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">Background (login)</label>
                                <input type="file" class="form-control" name="background" accept="image/*" data-file-preview="background" />
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">Favicon</label>
                                <input type="file" class="form-control" name="favicon" accept="image/*,.svg,.ico" data-file-preview="favicon" />
                            </div>
                        </div>

                        <div class="mt-3 d-flex flex-wrap gap-2">
                            <button class="btn btn-primary" type="submit">Guardar</button>
                            <a class="btn btn-outline-secondary" href="{{ url('/') }}">Volver</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div>
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <strong>Preview</strong>
                    <span class="small text-muted">En vivo (sin guardar)</span>
                </div>
                <div class="card-body">
                    @php
                        $assets = $resolvedBranding['assets'] ?? [];
                        $logo = $assets['logo_url'] ?? null;
                        $logoLight = $assets['logo_light_url'] ?? $logo;
                        $bg = $assets['background_url'] ?? null;
                    @endphp

                    @php
                        $cv = $resolvedBranding['cssVars'] ?? [];
                    @endphp
                    <div class="preview-shell" id="brandingPreview"
                        style="
                            --pv-primary: {{ $cv['--brand-primary'] ?? '#32C36C' }};
                            --pv-secondary: {{ $cv['--brand-secondary'] ?? '#1A2A36' }};
                            --pv-accent: {{ $cv['--brand-accent'] ?? '#DCE442' }};
                            --pv-light: {{ $cv['--brand-light'] ?? '#F6F7F8' }};
                            --pv-dark: {{ $cv['--brand-dark'] ?? '#1A2A36' }};
                            --pv-border: {{ $cv['--brand-border'] ?? '#E1E8ED' }};
                            --pv-text-primary: {{ $cv['--brand-text-primary'] ?? '#2C3E50' }};
                            --pv-text-secondary: {{ $cv['--brand-text-secondary'] ?? '#6C757D' }};
                            --pv-login-bg: {{ $bg ? "url('".$bg."')" : 'none' }};
                        "
                    >
                        <div class="preview-navbar">
                            <div class="d-flex align-items-center gap-2" style="min-width: 0;">
                                <img id="pvLogoLight" src="{{ $logoLight ?: '' }}" alt="logo" style="height: 30px; max-width: 140px; object-fit: contain" />
                                <div class="title text-truncate" id="pvPortalTitle">{{ $brandingRow->texts['portal_title'] ?? 'Portal de Reclutamiento' }}</div>
                            </div>
                            <div class="badge" id="pvBrandName">{{ $brandingRow->name }}</div>
                        </div>
                        <div class="preview-login" id="pvLogin">
                            <div class="preview-card">
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <img id="pvLogo" src="{{ $logo ?: '' }}" alt="logo" style="height: 34px; max-width: 160px; object-fit: contain" />
                                    <div>
                                        <div style="font-weight: 800; color: var(--pv-text-primary); line-height: 1.1">Login</div>
                                        <div style="color: var(--pv-text-secondary); font-size: 13px">Vista de ejemplo</div>
                                    </div>
                                </div>
                                <div class="d-grid gap-2">
                                    <button type="button" class="btn btn-primary">Acceder</button>
                                    <button type="button" class="btn btn-outline">Recuperar clave</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <div class="preview-tiles">
                            <div class="tile">
                                <div class="label">Primary</div>
                                <div class="bar" id="barPrimary" style="background: var(--pv-primary)"></div>
                                <div class="value" id="txtPrimary"></div>
                            </div>
                            <div class="tile">
                                <div class="label">Accent</div>
                                <div class="bar" id="barAccent" style="background: var(--pv-accent)"></div>
                                <div class="value" id="txtAccent"></div>
                            </div>
                            <div class="tile">
                                <div class="label">Secondary</div>
                                <div class="bar" id="barSecondary" style="background: var(--pv-secondary)"></div>
                                <div class="value" id="txtSecondary"></div>
                            </div>
                            <div class="tile">
                                <div class="label">Border</div>
                                <div class="bar" id="barBorder" style="background: var(--pv-border)"></div>
                                <div class="value" id="txtBorder"></div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <div class="small text-muted mb-2">Assets guardados para <strong>{{ $brandKey }}</strong>:</div>
                        <div class="small" style="white-space: pre-wrap; font-family: ui-monospace, SFMono-Regular, Menlo, monospace;">
logo: {{ $brandingRow->assets['logo'] ?? '' }}
logo_light: {{ $brandingRow->assets['logo_light'] ?? '' }}
background: {{ $brandingRow->assets['background'] ?? '' }}
favicon: {{ $brandingRow->assets['favicon'] ?? '' }}
                        </div>
                    </div>

                    <div class="mt-3 small text-muted">
                        Nota: el deploy sigue seleccionando empresa con <code>APP_BRAND</code>. Este panel edita los valores guardados para cada brand.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        const preview = document.getElementById('brandingPreview')
        if (!preview) return

        const $ = (sel) => document.querySelector(sel)

        function normalizeHex(input) {
            let v = (input || '').toString().trim()
            if (!v) return ''
            if (!v.startsWith('#')) v = '#' + v
            // keep only 0-9a-f and '#'
            v = '#' + v.slice(1).replace(/[^0-9a-fA-F]/g, '').slice(0, 6)
            if (v.length === 1) return ''
            return v
        }

        function setPreviewVar(key, hex) {
            if (!hex) return
            preview.style.setProperty(key, hex)
        }

        function updateTiles() {
            const getVar = (name) => getComputedStyle(preview).getPropertyValue(name).trim()
            const primary = getVar('--pv-primary')
            const secondary = getVar('--pv-secondary')
            const accent = getVar('--pv-accent')
            const border = getVar('--pv-border')

            const setText = (id, v) => {
                const el = document.getElementById(id)
                if (el) el.textContent = v
            }
            setText('txtPrimary', primary)
            setText('txtSecondary', secondary)
            setText('txtAccent', accent)
            setText('txtBorder', border)
        }

        function wirePalette() {
            document.querySelectorAll('[data-hex]').forEach((hexInput) => {
                const key = hexInput.getAttribute('data-hex')
                const colorInput = document.querySelector(`[data-color="${key}"]`)
                const swatch = document.querySelector(`[data-swatch="${key}"]`)
                const label = document.querySelector(`[data-hex-label="${key}"]`)

                const pvMap = {
                    primary: '--pv-primary',
                    secondary: '--pv-secondary',
                    accent: '--pv-accent',
                    light: '--pv-light',
                    dark: '--pv-dark',
                    border: '--pv-border',
                    text_primary: '--pv-text-primary',
                    text_secondary: '--pv-text-secondary',
                }

                const apply = (raw) => {
                    const hex = normalizeHex(raw)
                    if (hex) {
                        hexInput.value = hex
                        if (colorInput) colorInput.value = hex
                        if (swatch) swatch.style.background = hex
                        if (label) label.textContent = hex

                        if (pvMap[key]) setPreviewVar(pvMap[key], hex)
                        updateTiles()
                    }
                }

                hexInput.addEventListener('input', (e) => apply(e.target.value))
                hexInput.addEventListener('blur', (e) => apply(e.target.value))
                if (colorInput) {
                    colorInput.addEventListener('input', (e) => apply(e.target.value))
                }

                // init
                apply(hexInput.value)
            })
        }

        function wireTextPreview() {
            const name = document.querySelector('[data-preview="name"]')
            const portal = document.querySelector('[data-preview="portal_title"]')
            const pvBrandName = document.getElementById('pvBrandName')
            const pvPortalTitle = document.getElementById('pvPortalTitle')

            if (name && pvBrandName) {
                const apply = () => (pvBrandName.textContent = name.value || '')
                name.addEventListener('input', apply)
                apply()
            }
            if (portal && pvPortalTitle) {
                const apply = () => (pvPortalTitle.textContent = portal.value || 'Portal de Reclutamiento')
                portal.addEventListener('input', apply)
                apply()
            }
        }

        function wireFilePreview() {
            const pvLogo = document.getElementById('pvLogo')
            const pvLogoLight = document.getElementById('pvLogoLight')
            const pvLogin = document.getElementById('pvLogin')

            function setImg(imgEl, file) {
                if (!imgEl || !file) return
                const url = URL.createObjectURL(file)
                imgEl.src = url
            }
            function setBg(el, file) {
                if (!el || !file) return
                const url = URL.createObjectURL(file)
                preview.style.setProperty('--pv-login-bg', `url('${url}')`)
            }

            document.querySelectorAll('[data-file-preview]').forEach((input) => {
                const key = input.getAttribute('data-file-preview')
                input.addEventListener('change', () => {
                    const file = input.files && input.files[0]
                    if (!file) return
                    if (key === 'logo') setImg(pvLogo, file)
                    if (key === 'logo_light') setImg(pvLogoLight, file)
                    if (key === 'background') setBg(pvLogin, file)
                })
            })
        }

        wirePalette()
        wireTextPreview()
        wireFilePreview()
        updateTiles()
    })();
</script>
@endsection
