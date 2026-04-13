@extends('layouts.embed')

@section('content')
    @php
        use Jenssegers\Date\Date;
        Date::setLocale('es');

        $nombre = trim(($candidatos->nombre ?? '').' '.($candidatos->apellido ?? ''));
        $identidad = (string) ($candidatos->identidad ?? '');
        $correo = $candidatos->correo ?? null;
        $telefono = $candidatos->telefono ?? null;
        $direccion = $candidatos->direccion ?? null;
        $genero = strtoupper((string) ($candidatos->generoM_F ?? ''));
        $fechaNac = $candidatos->fecha_nacimiento ? Date::parse($candidatos->fecha_nacimiento)->format('d/m/Y') : null;

        $empresaById = collect($DatosEmpresa ?? [])->keyBy('id');

        $estado = (string) ($candidatos->activo ?? '');
        $estadoLabel = $estado === 's' ? 'Activo' : ($estado === 'n' ? 'Inactivo' : 'Restringido');
        $estadoClass = $estado === 's' ? 'badge bg-success' : ($estado === 'n' ? 'badge bg-secondary' : 'badge bg-danger');
        $estadoIcon = $estado === 's' ? 'ri-check-line' : ($estado === 'n' ? 'ri-information-line' : 'ri-forbid-2-line');

        $initials = '';
        if ($nombre !== '') {
            $parts = preg_split('/\s+/', $nombre);
            $initials = strtoupper(substr($parts[0] ?? '', 0, 1).substr($parts[1] ?? '', 0, 1));
        }
        if ($initials === '') {
            $initials = strtoupper(substr($identidad ?: '?', 0, 2));
        }
    @endphp

    <div class="viz-shell">
        <header class="viz-header">
            <div class="viz-header-left">
                <div class="viz-avatar">{{ $initials }}</div>
                <div class="viz-head-text">
                    <div class="viz-name">{{ $nombre !== '' ? $nombre : 'Candidato' }}</div>
                    <div class="viz-meta">
                        <span class="viz-chip"><i class="ri-id-card-line"></i>{{ $identidad }}</span>
                        @if ($correo)
                            <span class="viz-chip"><i class="ri-mail-line"></i>{{ $correo }}</span>
                        @endif
                        @if ($telefono)
                            <span class="viz-chip"><i class="ri-phone-line"></i>{{ $telefono }}</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="viz-header-right">
                <span class="{{ $estadoClass }} viz-badge"><i class="{{ $estadoIcon }} me-1"></i>{{ $estadoLabel }}</span>
            </div>
        </header>

        <section class="viz-section">
            <div class="viz-section-title">
                <i class="ri-user-3-line"></i>
                Informacion Personal
            </div>
            <div class="viz-grid">
                <div class="viz-kv">
                    <div class="k">Identidad</div>
                    <div class="v">{{ $identidad }}</div>
                </div>
                <div class="viz-kv">
                    <div class="k">Genero</div>
                    <div class="v">{{ $genero !== '' ? $genero : 'N/A' }}</div>
                </div>
                <div class="viz-kv">
                    <div class="k">Fecha nacimiento</div>
                    <div class="v">{{ $fechaNac ?? 'N/A' }}</div>
                </div>
                <div class="viz-kv">
                    <div class="k">Telefono</div>
                    <div class="v">{{ $telefono ?? 'N/A' }}</div>
                </div>
                <div class="viz-kv">
                    <div class="k">Correo</div>
                    <div class="v">{{ $correo ?? 'N/A' }}</div>
                </div>
                <div class="viz-kv viz-kv-wide">
                    <div class="k">Direccion</div>
                    <div class="v">{{ $direccion ?? 'N/A' }}</div>
                </div>
            </div>
        </section>

        <section class="viz-section">
            <div class="viz-section-title">
                <i class="ri-briefcase-4-line"></i>
                Historial Laboral
            </div>

            @if (empty($laboralInfo) || (is_countable($laboralInfo) && count($laboralInfo) === 0))
                <div class="viz-empty">Sin registros laborales.</div>
            @else
                <div class="viz-timeline">
                    @foreach ($laboralInfo as $il)
                        @php
                            $empresaNombre = '';
                            $empresaId = $il['id_empresa'] ?? null;
                            if ($empresaId && $empresaById->has($empresaId)) {
                                $empresaNombre = (string) ($empresaById[$empresaId]->nombre ?? '');
                            }

                            $puesto = $il['nombrepuesto'] ?? ($il['nombre_puesto'] ?? '');
                            $area = $il['area'] ?? '';
                            $forma = $il['forma_egreso'] ?? '';
                            $coment = $il['Comentario'] ?? ($il['comentario'] ?? '');

                            $fi = $il['fechaIngreso'] ?? null;
                            $fe = $il['fechaEgreso'] ?? null;
                            $fiFmt = $fi ? Date::parse($fi)->format('d/m/Y') : 'N/A';
                            $feFmt = $fe ? Date::parse($fe)->format('d/m/Y') : 'Actual';
                        @endphp

                        <div class="viz-item">
                            <div class="viz-item-left">
                                <div class="viz-dates">{{ $fiFmt }} <span class="sep">a</span> {{ $feFmt }}</div>
                                <div class="viz-company">{{ $empresaNombre !== '' ? $empresaNombre : 'Empresa' }}</div>
                            </div>
                            <div class="viz-item-right">
                                <div class="viz-role">{{ $puesto ?: 'Puesto' }}</div>
                                <div class="viz-subrow">
                                    @if ($area)
                                        <span class="viz-pill"><i class="ri-layout-2-line"></i>{{ $area }}</span>
                                    @endif
                                    @if ($forma)
                                        <span class="viz-pill"><i class="ri-logout-box-r-line"></i>{{ $forma }}</span>
                                    @endif
                                </div>
                                @if ($coment)
                                    <div class="viz-note">{{ $coment }}</div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
@endsection

@push('styles')
<style>
    .viz-shell {
        display: grid;
        gap: 14px;
    }

    .viz-header {
        border-radius: 18px;
        padding: 16px 16px;
        background: radial-gradient(circle at 20% 10%, rgb(var(--brand-primary-rgb) / 0.20), transparent 55%),
                    radial-gradient(circle at 80% 35%, rgb(var(--brand-dark-rgb) / 0.22), transparent 60%),
                    linear-gradient(135deg, var(--brand-dark), var(--brand-secondary));
        border: 1px solid rgb(255 255 255 / 0.10);
        box-shadow: 0 18px 55px rgb(var(--brand-dark-rgb) / 0.35);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        color: #fff;
    }
    .viz-header-left { display: flex; align-items: center; gap: 12px; min-width: 0; }
    .viz-avatar {
        width: 46px;
        height: 46px;
        border-radius: 16px;
        display: grid;
        place-items: center;
        font-weight: 900;
        background: rgb(var(--brand-primary-rgb) / 0.25);
        border: 1px solid rgb(255 255 255 / 0.16);
        flex: 0 0 auto;
    }
    .viz-head-text { min-width: 0; }
    .viz-name {
        font-weight: 900;
        letter-spacing: 0.2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .viz-meta {
        margin-top: 6px;
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        color: rgba(255,255,255,0.85);
        font-size: 12.5px;
    }
    .viz-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 10px;
        border-radius: 999px;
        background: rgb(255 255 255 / 0.10);
        border: 1px solid rgb(255 255 255 / 0.14);
        line-height: 1;
        max-width: 100%;
    }
    .viz-badge { border-radius: 999px; padding: 8px 10px; font-weight: 800; }

    .viz-section {
        border-radius: 18px;
        background: #fff;
        border: 1px solid var(--border);
        box-shadow: 0 12px 40px rgba(0,0,0,0.08);
        padding: 14px;
    }
    .viz-section-title {
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 900;
        color: var(--text-primary);
        margin-bottom: 10px;
    }
    .viz-section-title i { color: var(--brand-primary); }

    .viz-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
    }
    .viz-kv {
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 10px 12px;
        background: linear-gradient(180deg, #fff, rgb(var(--brand-primary-rgb) / 0.03));
    }
    .viz-kv-wide { grid-column: 1 / -1; }
    .viz-kv .k { font-size: 12px; color: var(--text-secondary); font-weight: 700; }
    .viz-kv .v { margin-top: 2px; font-weight: 800; color: var(--text-primary); word-break: break-word; }

    .viz-empty { color: var(--text-secondary); padding: 10px 2px; }

    .viz-timeline { display: grid; gap: 10px; }
    .viz-item {
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 12px;
        display: grid;
        grid-template-columns: 200px minmax(0, 1fr);
        gap: 12px;
        background: #fff;
    }
    .viz-dates { font-size: 12px; color: var(--text-secondary); font-weight: 700; }
    .viz-dates .sep { margin: 0 6px; }
    .viz-company { margin-top: 4px; font-weight: 900; color: var(--text-primary); }
    .viz-role { font-weight: 900; color: var(--text-primary); }
    .viz-subrow { margin-top: 6px; display: flex; flex-wrap: wrap; gap: 8px; }
    .viz-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 10px;
        border-radius: 999px;
        background: rgb(var(--brand-primary-rgb) / 0.08);
        border: 1px solid rgb(var(--brand-primary-rgb) / 0.18);
        color: var(--text-primary);
        font-size: 12px;
        font-weight: 700;
        line-height: 1;
    }
    .viz-note { margin-top: 8px; color: var(--text-secondary); }

    @media (max-width: 768px) {
        .viz-grid { grid-template-columns: 1fr; }
        .viz-item { grid-template-columns: 1fr; }
    }
</style>
@endpush
