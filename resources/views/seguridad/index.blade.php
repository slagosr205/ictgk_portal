@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title"><i class="ri-forbid-2-line me-2"></i>Candidatos Restringidos del Parque</h4>
                </div>
            </div>
            <div class="card-body">
                <form id="filtroForm" method="GET" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="search" class="form-label">Buscar</label>
                            <input type="text" class="form-control border" id="search" name="search" 
                                placeholder="Identidad, nombre, apellido, teléfono, correo..." 
                                value="{{ request('search') }}">
                        </div>

                        <div class="col-md-2">
                            <label for="identidad" class="form-label">Identidad</label>
                            <input type="text" class="form-control border" id="identidad" name="identidad" value="{{ request('identidad') }}" placeholder="DNI">
                        </div>

                        <div class="col-md-2">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control border" id="nombre" name="nombre" value="{{ request('nombre') }}" placeholder="Nombre">
                        </div>

                        <div class="col-md-2">
                            <label for="apellido" class="form-label">Apellido</label>
                            <input type="text" class="form-control border" id="apellido" name="apellido" value="{{ request('apellido') }}" placeholder="Apellido">
                        </div>

                        <div class="col-md-3">
                            <label for="comentario" class="form-label">Comentario</label>
                            <input type="text" class="form-control border" id="comentario" name="comentario" value="{{ request('comentario') }}" placeholder="Motivo / comentario">
                        </div>

                        <div class="col-md-2">
                            <label for="genero" class="form-label">Género</label>
                            <select class="form-select border" id="genero" name="genero">
                                <option value="">Todos</option>
                                <option value="M" {{ request('genero') == 'M' ? 'selected' : '' }}>Masculino</option>
                                <option value="F" {{ request('genero') == 'F' ? 'selected' : '' }}>Femenino</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="text" class="form-control border" id="telefono" name="telefono" value="{{ request('telefono') }}" placeholder="Teléfono">
                        </div>

                        <div class="col-md-3">
                            <label for="correo" class="form-label">Correo</label>
                            <input type="text" class="form-control border" id="correo" name="correo" value="{{ request('correo') }}" placeholder="Correo">
                        </div>

                        <div class="col-md-2">
                            <label for="nac_desde" class="form-label">Nac. desde</label>
                            <input type="date" class="form-control border" id="nac_desde" name="nac_desde" value="{{ request('nac_desde') }}">
                        </div>

                        <div class="col-md-2">
                            <label for="nac_hasta" class="form-label">Nac. hasta</label>
                            <input type="date" class="form-control border" id="nac_hasta" name="nac_hasta" value="{{ request('nac_hasta') }}">
                        </div>

                        <div class="col-md-2">
                            <label for="per_page" class="form-label">Registros por página</label>
                            <select class="form-select" id="per_page" name="per_page">
                                <option value="15" {{ request('per_page') == '15' ? 'selected' : '' }}>15</option>
                                <option value="25" {{ request('per_page') == '25' ? 'selected' : '' }}>25</option>
                                <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label for="order_by" class="form-label">Ordenar por</label>
                            <select class="form-select" id="order_by" name="order_by">
                                <option value="updated_at" {{ request('order_by', 'updated_at') == 'updated_at' ? 'selected' : '' }}>Actualización</option>
                                <option value="identidad" {{ request('order_by') == 'identidad' ? 'selected' : '' }}>Identidad</option>
                                <option value="nombre" {{ request('order_by') == 'nombre' ? 'selected' : '' }}>Nombre</option>
                                <option value="apellido" {{ request('order_by') == 'apellido' ? 'selected' : '' }}>Apellido</option>
                                <option value="fecha_nacimiento" {{ request('order_by') == 'fecha_nacimiento' ? 'selected' : '' }}>Fecha nacimiento</option>
                            </select>
                        </div>

                        <div class="col-md-1">
                            <label for="order_dir" class="form-label">Dir</label>
                            <select class="form-select" id="order_dir" name="order_dir">
                                <option value="desc" {{ request('order_dir', 'desc') == 'desc' ? 'selected' : '' }}>Desc</option>
                                <option value="asc" {{ request('order_dir') == 'asc' ? 'selected' : '' }}>Asc</option>
                            </select>
                        </div>

                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="ri-search-line"></i> Buscar
                            </button>
                            <a href="{{ route('seguridad.index') }}" class="btn btn-outline-secondary">
                                <i class="ri-refresh-line"></i> Limpiar
                            </a>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover custom-table">
                        <thead>
                            <tr>
                                <th>Identidad</th>
                                <th>Nombre Completo</th>
                                <th>Teléfono</th>
                                <th>Correo</th>
                                <th>Género</th>
                                <th>Fecha Nacimiento</th>
                                <th>Fecha Bloqueo</th>
                                <th>Motivo</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        @include('components.seguridad-tabla', ['candidatos' => $data])
                    </table>
                </div>

                @include('components.seguridad-paginacion', ['candidatos' => $data])
            </div>
        </div>
    </div>
</div>

<!-- Motivo Modal (single instance) -->
<div class="modal fade" id="segMotivoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content seg-modal">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title mb-0"><i class="ri-message-2-line me-2"></i>Motivo</h5>
                    <div class="small text-muted" id="segMotivoMeta"></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="seg-motivo-box" id="segMotivoText"></div>
                <div class="small text-muted mt-2" id="segMotivoFecha"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Historial Modal (single instance) -->
<div class="modal fade" id="segHistorialModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content seg-modal">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title mb-0"><i class="ri-history-line me-2"></i>Historial</h5>
                    <div class="small text-muted" id="segHistMeta"></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="seg-timeline" id="segHistBody"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Ficha Modal (iframe) -->
<div class="modal fade" id="segFichaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content seg-modal seg-ficha-modal">
            <div class="modal-header seg-ficha-header">
                <div class="seg-ficha-head">
                    <div class="seg-ficha-avatar" id="segFichaAvatar">?</div>
                    <div class="seg-ficha-head-text">
                        <div class="seg-ficha-title">
                            <i class="ri-file-user-line me-2"></i>
                            <span id="segFichaTitle">Ficha del candidato</span>
                        </div>
                        <div class="seg-ficha-sub" id="segFichaMeta"></div>
                    </div>
                </div>
                <div class="seg-ficha-actions">
                    <a class="btn btn-sm seg-btn seg-btn-ghost" id="segFichaOpenNew" href="#" target="_blank" rel="noopener">
                        <i class="ri-external-link-line me-1"></i>Abrir
                    </a>
                    <button type="button" class="btn btn-sm seg-btn seg-btn-solid" data-bs-dismiss="modal">
                        <i class="ri-close-line"></i>
                    </button>
                </div>
            </div>
            <div class="modal-body p-0">
                <div class="seg-iframe-wrap">
                    <div class="seg-iframe-loading" id="segFichaLoading">
                        <div class="text-center">
                            <div class="spinner-border" role="status"></div>
                            <div class="mt-2 small text-muted">Cargando ficha...</div>
                        </div>
                    </div>
                    <iframe id="segFichaFrame" class="seg-iframe" src="about:blank" scrolling="yes" title="Ficha del candidato"></iframe>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--primary-gradient);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.875rem;
}

.empty-state {
    padding: 3rem 0;
}

.btn-group .btn {
    border-radius: 8px !important;
    margin: 0 2px;
}

.bg-pink {
    background-color: #e83e8c !important;
    color: white;
}

.seg-modal {
    border-radius: 16px;
    overflow: hidden;
}

.seg-motivo-box {
    background: rgb(var(--brand-primary-rgb) / 0.08);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 12px 14px;
    color: var(--text-primary);
    white-space: pre-wrap;
}

.seg-timeline {
    display: grid;
    gap: 10px;
}

.seg-timeline-item {
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 12px 14px;
    background: white;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
}

.seg-timeline-item .left {
    min-width: 0;
}

.seg-timeline-item .type {
    font-weight: 700;
}

.seg-timeline-item .comment {
    color: var(--text-secondary);
}

.seg-iframe-wrap {
    position: relative;
    height: min(75vh, 820px);
    background: radial-gradient(circle at 20% 10%, rgb(var(--brand-primary-rgb) / 0.08), transparent 50%),
                radial-gradient(circle at 80% 30%, rgb(var(--brand-dark-rgb) / 0.10), transparent 55%),
                #f8f9fa;
}

.seg-iframe {
    width: 100%;
    height: 100%;
    border: 0;
    display: block;
}

.seg-iframe-loading {
    position: absolute;
    inset: 0;
    display: grid;
    place-items: center;
    background: rgba(248, 249, 250, 0.94);
    z-index: 2;
}

.seg-ficha-modal {
    border: 1px solid rgba(0,0,0,0.08);
    box-shadow: 0 22px 70px rgba(0,0,0,0.25);
}

.seg-ficha-header {
    padding: 0;
    border-bottom: 0;
}

.seg-ficha-head {
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 0;
}

.seg-ficha-head-text {
    min-width: 0;
}

.seg-ficha-title {
    font-weight: 800;
    color: #fff;
    letter-spacing: 0.2px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.seg-ficha-sub {
    color: rgba(255,255,255,0.82);
    font-size: 12.5px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.seg-ficha-avatar {
    width: 42px;
    height: 42px;
    border-radius: 14px;
    display: grid;
    place-items: center;
    font-weight: 800;
    color: #fff;
    background: rgb(var(--brand-primary-rgb) / 0.22);
    border: 1px solid rgb(255 255 255 / 0.16);
    box-shadow: 0 10px 24px rgb(var(--brand-primary-rgb) / 0.25);
    flex: 0 0 auto;
}

.seg-ficha-header {
    background: linear-gradient(135deg, var(--brand-dark), var(--brand-secondary));
    padding: 14px 16px;
}

.seg-ficha-actions {
    display: flex;
    gap: 8px;
    align-items: center;
}

.seg-btn {
    border-radius: 12px;
    padding: 8px 10px;
    line-height: 1;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-weight: 700;
}

.seg-btn-ghost {
    background: rgba(255,255,255,0.10);
    border: 1px solid rgba(255,255,255,0.16);
    color: rgba(255,255,255,0.92);
}

.seg-btn-ghost:hover {
    background: rgba(255,255,255,0.14);
    color: #fff;
}

.seg-btn-solid {
    background: rgba(255,255,255,0.92);
    border: 1px solid rgba(0,0,0,0.06);
    color: rgba(0,0,0,0.75);
}

.seg-btn-solid:hover {
    background: #fff;
}

@media (max-width: 576px) {
    .seg-ficha-title {
        font-size: 14px;
    }
    .seg-ficha-avatar {
        width: 38px;
        height: 38px;
        border-radius: 12px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    let debounceTimer;
    const form = document.getElementById('filtroForm');

    function buildUrl(baseUrl = null) {
        const url = baseUrl ? new URL(baseUrl) : new URL(window.location.href);
        const formData = new FormData(form);

        // reset page whenever filters change
        url.searchParams.set('page', '1');
        for (const [key, value] of formData.entries()) {
            if (value === null || String(value).trim() === '') {
                url.searchParams.delete(key);
            } else {
                url.searchParams.set(key, String(value));
            }
        }
        return url;
    }

    function initTooltips() {
        const TooltipClass = window.bootstrap && window.bootstrap.Tooltip;
        if (!TooltipClass) return;
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(function (tooltipTriggerEl) {
            // dispose existing instance if any
            const existing = TooltipClass.getInstance(tooltipTriggerEl);
            if (existing) existing.dispose();
            new TooltipClass(tooltipTriggerEl);
        });
    }

    // Actions (motivo/historial/ficha) - event delegation (works after AJAX updates)
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('[data-action]');
        if (!btn) return;

        const action = btn.getAttribute('data-action');
        const bs = window.bootstrap;
        if (!bs || !bs.Modal) return;

        if (action === 'motivo') {
            const modalEl = document.getElementById('segMotivoModal');
            document.getElementById('segMotivoMeta').textContent = `${btn.dataset.identidad || ''} | ${btn.dataset.nombre || ''}`.trim();
            document.getElementById('segMotivoText').textContent = btn.dataset.motivo || 'Sin motivo';
            document.getElementById('segMotivoFecha').textContent = btn.dataset.fecha ? `Fecha bloqueo: ${btn.dataset.fecha}` : '';
            bs.Modal.getOrCreateInstance(modalEl).show();
        }

        if (action === 'historial') {
            const raw = btn.getAttribute('data-history');
            let history = [];
            try { history = raw ? JSON.parse(raw) : []; } catch (_) { history = []; }

            const modalEl = document.getElementById('segHistorialModal');
            document.getElementById('segHistMeta').textContent = `${btn.dataset.identidad || ''} | ${btn.dataset.nombre || ''}`.trim();

            const body = document.getElementById('segHistBody');
            body.innerHTML = '';

            if (!history || history.length === 0) {
                body.innerHTML = '<div class="text-muted">Sin historial</div>';
            } else {
                history.forEach((ev) => {
                    const isBloqueo = !!ev.fechaBloqueo;
                    const fecha = (isBloqueo ? ev.fechaBloqueo : ev.fechaDesbloqueo) || 'Sin fecha';
                    const comment = ev.comentarios || 'Sin comentario';

                    const el = document.createElement('div');
                    el.className = 'seg-timeline-item';
                    el.innerHTML = `
                        <div class="left">
                            <div class="type">${isBloqueo ? 'Bloqueo' : 'Desbloqueo'}</div>
                            <div class="comment">${escapeHtml(comment)}</div>
                        </div>
                        <div>
                            <span class="badge ${isBloqueo ? 'bg-danger' : 'bg-success'}">${escapeHtml(fecha)}</span>
                        </div>
                    `;
                    body.appendChild(el);
                });
            }

            bs.Modal.getOrCreateInstance(modalEl).show();
        }

        if (action === 'ficha') {
            const url = btn.getAttribute('data-ficha-url');
            if (!url) return;
            const modalEl = document.getElementById('segFichaModal');
            const iframe = document.getElementById('segFichaFrame');
            const loading = document.getElementById('segFichaLoading');
            const openNew = document.getElementById('segFichaOpenNew');
            const avatar = document.getElementById('segFichaAvatar');
            const title = document.getElementById('segFichaTitle');
            const fullUrl = btn.getAttribute('data-ficha-full-url') || url;

            document.getElementById('segFichaMeta').textContent = `${btn.dataset.identidad || ''} | ${btn.dataset.nombre || ''}`.trim();
            if (title) title.textContent = btn.dataset.nombre ? btn.dataset.nombre : 'Ficha del candidato';
            if (avatar) avatar.textContent = initials(btn.dataset.nombre || btn.dataset.identidad || '?');
            // Open full ficha (not embed)
            openNew.href = fullUrl;

            loading.style.display = 'grid';
            iframe.onload = () => { loading.style.display = 'none'; };
            iframe.src = url;

            bs.Modal.getOrCreateInstance(modalEl).show();
        }
    });

    function initials(input) {
        const s = String(input || '').trim();
        if (!s) return '?';
        const parts = s.split(/\s+/).filter(Boolean);
        const a = (parts[0] || '').slice(0, 1).toUpperCase();
        const b = (parts[1] || '').slice(0, 1).toUpperCase();
        return (a + b) || a || '?';
    }

    function escapeHtml(str) {
        return String(str)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function loadData(urlObj) {
        fetch(urlObj, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            }
        })
        .then(r => r.text())
        .then(html => {
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;

            const newTbody = tempDiv.querySelector('#seguridadTableBody');
            const newPagination = tempDiv.querySelector('#seguridadPaginationContainer');
            const currentTbody = document.querySelector('#seguridadTableBody');
            const currentPagination = document.querySelector('#seguridadPaginationContainer');

            if (newTbody && currentTbody) currentTbody.replaceWith(newTbody);
            if (newPagination && currentPagination) currentPagination.replaceWith(newPagination);

            window.history.pushState({}, '', urlObj);
            initPaginationEvents();
            initTooltips();
        })
        .catch(err => {
            console.error(err);
            // fallback: full submit
            form.submit();
        });
    }

    function initPaginationEvents() {
        const links = document.querySelectorAll('.pagination a');
        links.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.href;
                if (!url) return;
                const urlObj = new URL(url);
                // keep current filters
                const merged = buildUrl(urlObj);
                // keep page from clicked link
                merged.searchParams.set('page', urlObj.searchParams.get('page') || '1');
                loadData(merged);
            });
        });
    }

    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            loadData(buildUrl());
        });

        // auto reload on changes (debounced for text inputs)
        form.querySelectorAll('input[type="text"]').forEach(inp => {
            inp.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => loadData(buildUrl()), 450);
            });
        });
        form.querySelectorAll('select, input[type="date"]').forEach(el => {
            el.addEventListener('change', () => loadData(buildUrl()));
        });
    }

    initPaginationEvents();
    initTooltips();
});
</script>
