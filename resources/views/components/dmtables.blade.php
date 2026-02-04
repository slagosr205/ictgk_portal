@php
    use Jenssegers\Date\Date;
@endphp

<div class="modern-table-container">
    <div class="table-actions-header">
        <div class="action-buttons-group">
            @foreach ($perfil as $pu)
                @if ((bool)$pu->ingreso)
                <button type="button" class="btn-modern btn-primary" data-bs-toggle="modal" data-bs-target="#registerCandidate">
                    <i class="ri-sticky-note-add-fill"></i>
                    <span>Nuevo Candidato</span>
                </button>
                @endif
                @if ((bool)$pu->egreso)
                <button class="btn-modern btn-primary" data-bs-toggle="modal" data-bs-target="#importOut">
                    <i class="ri-file-upload-line"></i>
                    <span>Importar Egresos</span>
                </button>
                @endif
                @if ((bool) $pu->bloqueocolaborador)
                <button class="btn-modern btn-warning" data-bs-toggle="modal" data-bs-target="#importBlockModal">
                    <i class="ri-spam-2-line"></i>
                    <span>Bloqueo Masivo</span>
                </button>
                @endif
            @endforeach
        </div>

        <div class="search-filter-group">
            <div class="search-box">
                <i class="ri-search-line"></i>
                <input type="text" id="searchTable" class="search-input" placeholder="Buscar...">
            </div>
        </div>
    </div>

@php
// Capturar y limpiar mensajes
$status = session('status', '');
$message = session('message', '');
if(!empty($status) && !empty($message)) {
    session()->forget(['status', 'message']);
}
@endphp

@if(!empty($status) && !empty($message))
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: '{{$status}}',
                title: '{{$message}}',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true
            });
        } else {
            setTimeout(function() {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: '{{$status}}',
                    title: '{{$message}}',
                    showConfirmButton: false,
                    timer: 4000,
                    timerProgressBar: true
                });
            }, 1000);
        }
    });
    </script>
@endif

    @if(session('missingFields'))
        <div hidden id="missingFields">campos obligatorios en la plantilla</div>
    @endif

    @if(session('mensajeerror'))
        <div hidden id="mensajeerror">{{ session('mensajeerror') }}</div>
    @endif

    @if(session('error'))
        <div hidden id="error">{{ session('error') }}</div>
    @endif

    <x-modal-registro/>

    {{-- Loading Spinner --}}
    <div id="loadingSpinner" class="loading-overlay" style="display: none;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Cargando...</span>
        </div>
    </div>

    <div class="table-scroll-top" id="tableScrollTop" aria-hidden="true">
        <div class="table-scroll-track" id="tableScrollTrack"></div>
    </div>
    <div class="table-wrapper" id="tableWrapper">
        <table  class="modern-table">
            <thead>
                <tr>
                    <th class="th-checkbox">
                        <button class="btn-export" id="btnEgresoMasivo" title="Exportar Egresos">
                            <i class="ri-file-download-line"></i>
                        </button>
                    </th>
                    <th class="th-sortable">
                        <span>Identidad</span>
                        <i class="ri-arrow-up-down-line"></i>
                    </th>
                    <th class="th-sortable">
                        <span>Nombre</span>
                        <i class="ri-arrow-up-down-line"></i>
                    </th>
                    <th class="th-sortable">
                        <span>Teléfono</span>
                        <i class="ri-arrow-up-down-line"></i>
                    </th>
                    <th class="th-sortable">
                        <span>Correo</span>
                        <i class="ri-arrow-up-down-line"></i>
                    </th>
                    <th class="th-sortable">
                        <span>Fecha Nacimiento</span>
                        <i class="ri-arrow-up-down-line"></i>
                    </th>
                    @foreach ($perfil as $pu)
                        @if ($pu->perfilesdescrip==='admin')
                            <th>Observaciones</th>
                        @else
                            <th></th>
                        @endif
                    @endforeach
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                @forelse ($candidatos as $candidato)
                <tr class="table-row">
                    @if ($candidato->activo==='n' && $candidato->activo_ingreso==='s')
                        <td class="td-checkbox">
                            <input type="checkbox" class="selectOutput modern-checkbox" value="{{$candidato->identidad}}">
                        </td>
                    @else
                        <td class="td-checkbox"></td> 
                    @endif
                    
                    <td class="td-id">
                        <span class="badge-id">{{$candidato->identidad}}</span>
                    </td>
                    <td class="td-name">
                        <div class="name-cell">
                            <div class="avatar">
                                {{strtoupper(substr($candidato->nombre, 0, 1))}}{{strtoupper(substr($candidato->apellido ?? '', 0, 1))}}
                            </div>
                            <span>{{$candidato->nombre.' '.$candidato->apellido}}</span>
                        </div>
                    </td>
                    <td class="td-phone">
                        <i class="ri-phone-line icon-subtle"></i>
                        {{$candidato->telefono}}
                    </td>
                    <td class="td-email">
                        <i class="ri-mail-line icon-subtle"></i>
                        {{$candidato->correo}}
                    </td>
                    <td class="td-date">{{$candidato->fecha_nacimiento}}</td>
                    
                    @foreach ($perfil as $pu)
                        @if ($pu->perfilesdescrip==='admin')
                            <td class="td-comments">
                                @php
                                    $comentariosRaw = $candidato->comentarios ?? [];

                                    if (is_string($comentariosRaw)) {
                                        $comentarios = json_decode($comentariosRaw, true) ?: [];
                                    } elseif (is_object($comentariosRaw)) {
                                        $comentarios = (array) $comentariosRaw;
                                    } else {
                                        $comentarios = is_array($comentariosRaw) ? $comentariosRaw : [];
                                    }

                                    // Filtrar solo los elementos que son arrays/objetos con fechas
                                    $eventos = collect($comentarios)->filter(function ($item) {
                                        if (is_object($item)) {
                                            $item = (array) $item;
                                        }
                                        return is_array($item) && (isset($item['fechaBloqueo']) || isset($item['fechaDesbloqueo']));
                                    })->map(function ($item) {
                                        return (array) $item;
                                    })->values();
                                    
                                    // Si hay comentario directo en el objeto/array raíz, agregarlo
                                    if (isset($comentarios['comentarios']) && (isset($comentarios['fechaBloqueo']) || isset($comentarios['fechaDesbloqueo']))) {
                                        $eventos->push([
                                            'comentarios' => $comentarios['comentarios'],
                                            'fechaBloqueo' => $comentarios['fechaBloqueo'] ?? null,
                                            'fechaDesbloqueo' => $comentarios['fechaDesbloqueo'] ?? null,
                                        ]);
                                    }
                                @endphp
                               
                                @if($eventos->count() > 0)
                                    <button type="button" 
                                            class="btn-comments" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalComentarios{{$candidato->identidad}}">
                                        <i class="ri-history-line"></i>
                                        <span>{{$eventos->count()}} evento(s)</span>
                                    </button>

                                    {{-- Modal Timeline --}}
                                    <div class="modal fade" id="modalComentarios{{$candidato->identidad}}" tabindex="-1">
                                        <div class="modal-dialog modal-dialog-centered modal-lg">
                                            <div class="modal-content timeline-modal">
                                                <div class="modal-header-timeline">
                                                    <div class="header-info">
                                                        <i class="ri-history-line"></i>
                                                        <div>
                                                            <h5>Historial de Eventos</h5>
                                                            <p>{{$candidato->nombre.' '.$candidato->apellido}}</p>
                                                        </div>
                                                    </div>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body-timeline">
                                                    <div class="timeline">
                                                        @foreach ($eventos as $index => $comentario)
                                                            @php
                                                                $isBloqueo = isset($comentario['fechaBloqueo']);
                                                                $fecha = $isBloqueo ? $comentario['fechaBloqueo'] : ($comentario['fechaDesbloqueo'] ?? null);
                                                                $fechaFormateada = $fecha ? (new Date($fecha))->format('j \\de F Y') : 'Sin fecha';
                                                            @endphp
                                                            
                                                            <div class="timeline-item {{$isBloqueo ? 'item-danger' : 'item-success'}}">
                                                                <div class="timeline-marker">
                                                                    <span>{{$index + 1}}</span>
                                                                </div>
                                                                @if (!$loop->last)
                                                                    <div class="timeline-line"></div>
                                                                @endif
                                                                <div class="timeline-card">
                                                                    <div class="card-icon {{$isBloqueo ? 'icon-danger' : 'icon-success'}}">
                                                                        <i class="{{$isBloqueo ? 'ri-lock-line' : 'ri-lock-unlock-line'}}"></i>
                                                                    </div>
                                                                    <div class="card-content">
                                                                        <h6>{{$isBloqueo ? 'Bloqueo de Acceso' : 'Desbloqueo de Acceso'}}</h6>
                                                                        <p class="date"><i class="ri-calendar-line"></i>{{$fechaFormateada}}</p>
                                                                        <p class="comment">
                                                                            <i class="ri-message-3-line me-2"></i>
                                                                            {{$comentario['comentarios'] ?? 'Sin comentario'}}
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                <div class="modal-footer-timeline">
                                                    <button type="button" class="btn-modern btn-secondary" data-bs-dismiss="modal">
                                                        <i class="ri-close-line"></i>
                                                        <span>Cerrar</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted-subtle">Sin historial</span>
                                @endif
                            </td>
                        @else
                            <td></td>
                        @endif
                    @endforeach
                    
                    <td class="td-actions">
                        @foreach ($perfil as $pu)
                            @if ($pu->perfilesdescrip==='admin')
                                @if ($pu->bloqueocolaborador===1 && $candidato->activo==='x')
                                    <button class="btn-action btn-success btndesbloqueo" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#unlockcandidate" 
                                            value="{{$candidato->identidad}}">
                                        <i class="ri-lock-unlock-line"></i>
                                        <span>Desbloquear</span>
                                    </button>
                                @else
                                    <button type="button" 
                                            class="btn-action btn-danger btnbloqueo" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#lockcandidate" 
                                            value="{{$candidato->identidad}}">
                                        <i class="ri-lock-line"></i>
                                        <span>Bloquear</span>
                                    </button>
                                @endif
                            @endif
                        @endforeach
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-5">
                        <div class="empty-state-inline">
                            <i class="ri-inbox-line" style="font-size: 3rem; color: #bdc3c7;"></i>
                            <p class="mt-2 mb-0">No se encontraron registros</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginación --}}
    <div class="pagination-container" id="paginationContainer">
        <div class="pagination-info">
            <span>Mostrando <strong>{{$candidatos->firstItem() ?? 0}}</strong> a <strong>{{$candidatos->lastItem() ?? 0}}</strong> de <strong>{{$candidatos->total()}}</strong> registros</span>
        </div>
        <div class="pagination-controls">
            {{ $candidatos->links('pagination::bootstrap-5') }}
        </div>
        <div class="per-page-selector">
            <label>Registros por página:</label>
            <select id="perPageSelect" class="form-select form-select-sm">
                <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                <option value="15" {{ request('per_page') == 15 || !request('per_page') ? 'selected' : '' }}>15</option>
                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
            </select>
        </div>
    </div>

    {{-- Modal de Actualización --}}
    <div class="modal fade" id="update-data" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">
                        <i class="ri-user-add-line me-2"></i>
                        Actualizar Candidato
                    </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="form-section">
                        <h3><i class="ri-file-list-3-line me-2"></i>Datos Generales</h3>
                        <hr>
                        <form class="modern-form" method="POST" action="">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="identidad" class="form-label">N° de DNI</label>
                                    <input type="text" class="form-control" id="identidad" name="identidad" placeholder="0000-0000-00000" />
                                </div>
                                <div class="col-md-4">
                                    <label for="nombre" class="form-label">Nombre</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" />
                                </div>
                                <div class="col-md-4">
                                    <label for="apellido" class="form-label">Apellidos</label>
                                    <input type="text" class="form-control" id="apellido" name="apellido" />
                                </div>
                            </div>

                            <div class="row g-3 mt-2">
                                <div class="col-md-3">
                                    <label for="telefono" class="form-label">Teléfono</label>
                                    <input type="text" class="form-control" id="telefono" name="telefono" placeholder="0000-0000"/>
                                </div>
                                <div class="col-md-3">
                                    <label for="correo" class="form-label">Correo</label>
                                    <input type="email" class="form-control" id="correo" name="correo" />
                                </div>
                                <div class="col-md-3">
                                    <label for="fecha_nacimiento" class="form-label">Fecha Nacimiento</label>
                                    <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" />
                                </div>
                                <div class="col-md-3">
                                    <label for="generoM_F" class="form-label">Género</label>
                                    <select name="generoM_F" id="generoM_F" class="form-select">
                                        <option value="m">Masculino</option>
                                        <option value="f">Femenino</option>
                                    </select>
                                </div>
                            </div>

                            <h5 class="mt-4"><i class="ri-map-pin-line me-2"></i>Dirección</h5>
                            <div class="row g-3 mt-1">
                                <div class="col-12">
                                    <label for="direccion" class="form-label">Calle</label>
                                    <textarea name="direccion" id="direccion" class="form-control" rows="3"></textarea>
                                </div>
                            </div>

                            <div class="form-actions mt-4">
                                <button type="submit" class="btn-modern btn-primary" id="Grabar" name="Grabar">
                                    <i class="ri-save-line"></i>
                                    <span>Grabar</span>
                                </button>
                                <button type="button" class="btn-modern btn-secondary" data-bs-dismiss="modal">
                                    <i class="ri-close-line"></i>
                                    <span>Cancelar</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-import-candidate />
    <x-modal-importar-egresos />
    <x-import-block-candidate />
    <x-unlock-candidate />
    <x-lock-candidate />

    @if (session('response'))
    <div class="notification-card mt-4" id="alerts-container">
        <div class="notification-header">
            <strong><i class="ri-notification-3-line me-2"></i>Notificaciones de Proceso</strong>
            <button type="button" class="btn-close" onclick="document.getElementById('alerts-container').remove();"></button>
        </div>
        <div class="notification-body">
            <table class="notification-table">
                <thead>
                    <tr>
                        <th style="width: 60px;">Estado</th>
                        <th style="width: 200px;">Candidato</th>
                        <th>Mensaje</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach (session('response')['response'] as $resp)
                    <tr>
                        <td class="text-center">
                            @if (($resp['status'] ?? 'info') === 'success')
                                <i class="ri-check-fill status-icon icon-success"></i>
                            @elseif (($resp['status'] ?? 'info') === 'error' || ($resp['status'] ?? 'info') === 'danger')
                                <i class="ri-thumb-down-fill status-icon icon-danger"></i>
                            @elseif (($resp['status'] ?? 'info') === 'warning')
                                <i class="ri-information-off-fill status-icon icon-warning"></i>
                            @else
                                <i class="ri-information-fill status-icon icon-info"></i>
                            @endif
                        </td>
                        <td>
                            @if (isset($resp['nombre']))
                                <strong>{{ $resp['nombre'].' '.$resp['apellido'] }}</strong>
                            @else
                                <span class="text-muted-subtle">Sin identidad</span>
                            @endif 
                        </td>
                        <td>{{ $resp['message'] ?? 'Sin mensaje.' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

<style>
:root {
    --primary: #32C36C;
    --light: #F6F7F8;
    --dark: #1A2A36;
    --color-tertiary: #dce442;
    --danger: #e74c3c;
    --success: #32C36C;
    --warning: #dce442;
    --text-primary: #2c3e50;
    --text-secondary: #6c757d;
    --border: #e1e8ed;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

.modern-table-container {
    max-width: 100%;
    margin: 2rem auto;
    padding: 0 1.5rem;
}

/* Header de Acciones */
.table-actions-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.action-buttons-group {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.btn-modern {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.625rem 1.25rem;
    border: none;
    border-radius: 8px;
    font-weight: 500;
    font-size: 0.875rem;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
}

.btn-modern:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.btn-modern.btn-primary {
    background: var(--primary);
    color: white;
}

.btn-modern.btn-primary:hover {
    background: #2aaa5e;
}

.btn-modern.btn-warning {
    background: var(--color-tertiary);
    color: var(--dark);
}

.btn-modern.btn-warning:hover {
    background: #c9ce3a;
}

.btn-modern.btn-secondary {
    background: var(--light);
    color: var(--text-primary);
    border: 1px solid var(--border);
}

.btn-modern.btn-secondary:hover {
    background: #e9ecef;
}

/* Search Box */
.search-filter-group {
    display: flex;
    gap: 1rem;
}

.search-box {
    position: relative;
    display: flex;
    align-items: center;
}

.search-box i {
    position: absolute;
    left: 1rem;
    color: var(--text-secondary);
    pointer-events: none;
}

.search-input {
    padding: 0.625rem 1rem 0.625rem 2.75rem;
    border: 1px solid var(--border);
    border-radius: 8px;
    font-size: 0.875rem;
    width: 300px;
    transition: all 0.3s ease;
}

.search-input:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(50, 195, 108, 0.1);
}

/* Loading Overlay */
.loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.9);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    border-radius: 12px;
}

.table-wrapper {
    position: relative;
    background: white;
    border-radius: 12px;
    overflow-x: auto;
    overflow-y: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

/* Top Scrollbar */
.table-scroll-top {
    position: relative;
    height: 16px;
    margin-bottom: 10px;
    border-radius: 999px;
    background: linear-gradient(90deg, rgba(50, 195, 108, 0.08), rgba(26, 42, 54, 0.08));
    overflow: hidden;
    box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.08);
}

.table-scroll-track {
    height: 100%;
}

.table-scroll-top::-webkit-scrollbar {
    height: 10px;
}

.table-scroll-top::-webkit-scrollbar-track {
    background: rgba(26, 42, 54, 0.08);
    border-radius: 999px;
}

.table-scroll-top::-webkit-scrollbar-thumb {
    background: linear-gradient(180deg, var(--primary), #2aaa5e);
    border-radius: 999px;
}

.table-scroll-top::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(180deg, #2aaa5e, #229b55);
}

.table-scroll-top {
    overflow-x: auto;
    overflow-y: hidden;
}

/* Modern Table */
.modern-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    min-width: 980px;
}

.modern-table thead {
    background: var(--dark);
    color: white;
}

.modern-table thead th {
    padding: 1rem 1.25rem;
    text-align: left;
    font-weight: 600;
    font-size: 0.8125rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    white-space: nowrap;
}

.th-sortable {
    cursor: pointer;
    user-select: none;
}

.th-sortable:hover {
    background: rgba(255, 255, 255, 0.05);
}

.th-sortable span {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.th-sortable i {
    font-size: 0.875rem;
    opacity: 0.6;
}

.th-checkbox {
    width: 80px;
    text-align: center;
}

.btn-export {
    background: var(--primary);
    color: white;
    border: none;
    padding: 0.5rem 0.875rem;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-export:hover {
    background: #2aaa5e;
    transform: scale(1.05);
}

/* Table Body */
.modern-table tbody tr {
    background: white;
    transition: all 0.2s ease;
    border-bottom: 1px solid var(--light);
}

.modern-table tbody tr:hover {
    background: var(--light);
}

.modern-table tbody td {
    padding: 1rem 1.25rem;
    font-size: 0.875rem;
    color: var(--text-primary);
    vertical-align: middle;
}

.td-checkbox {
    text-align: center;
}

.modern-checkbox {
    width: 18px;
    height: 18px;
    cursor: pointer;
    accent-color: var(--primary);
}

.td-id .badge-id {
    display: inline-block;
    padding: 0.375rem 0.75rem;
    background: rgba(50, 195, 108, 0.1);
    color: var(--primary);
    border-radius: 6px;
    font-weight: 600;
    font-size: 0.8125rem;
}

/* Name Cell con Avatar */
.name-cell {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: var(--primary);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.75rem;
    flex-shrink: 0;
}

.icon-subtle {
    color: var(--text-secondary);
    margin-right: 0.375rem;
    font-size: 0.875rem;
}

/* Botón de Comentarios */
.btn-comments {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: var(--light);
    border: 1px solid var(--border);
    border-radius: 6px;
    color: var(--text-primary);
    font-size: 0.8125rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-comments:hover {
    background: white;
    border-color: var(--primary);
    color: var(--primary);
    transform: translateY(-2px);
    box-shadow: 0 2px 8px rgba(50, 195, 108, 0.2);
}

.text-muted-subtle {
    color: var(--text-secondary);
    font-size: 0.8125rem;
}

/* Botones de Acción */
.btn-action {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 6px;
    font-size: 0.8125rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-action.btn-success {
    background: var(--success);
    color: white;
}

.btn-action.btn-success:hover {
    background: #2aaa5e;
    transform: translateY(-2px);
}

.btn-action.btn-danger {
    background: var(--danger);
    color: white;
}

.btn-action.btn-danger:hover {
    background: #c0392b;
    transform: translateY(-2px);
}

/* Timeline Modal */
.timeline-modal {
    border: none;
    border-radius: 12px;
    overflow: hidden;
}

.modal-header-timeline {
    background: var(--dark);
    color: white;
    padding: 1.5rem;
    border: none;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.header-info {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.header-info i {
    font-size: 2rem;
}

.header-info h5 {
    margin: 0;
    font-size: 1.125rem;
    font-weight: 600;
}

.header-info p {
    margin: 0.25rem 0 0;
    font-size: 0.875rem;
    opacity: 0.9;
}

.modal-body-timeline {
    padding: 2rem;
    background: var(--light);
    max-height: 500px;
    overflow-y: auto;
}

/* Timeline */
.timeline {
    position: relative;
}

.timeline-item {
    position: relative;
    padding-left: 70px;
    margin-bottom: 1.5rem;
}

.timeline-marker {
    position: absolute;
    left: 0;
    top: 0;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: white;
    border: 3px solid;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.875rem;
    z-index: 2;
}

.item-danger .timeline-marker {
    border-color: var(--danger);
    color: var(--danger);
}

.item-success .timeline-marker {
    border-color: var(--success);
    color: var(--success);
}

.timeline-line {
    position: absolute;
    left: 19px;
    top: 40px;
    width: 2px;
    height: calc(100% + 1.5rem);
    background: var(--border);
    z-index: 1;
}

.timeline-card {
    display: flex;
    gap: 1rem;
    background: white;
    padding: 1.25rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
}

.timeline-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transform: translateX(4px);
}

.card-icon {
    width: 42px;
    height: 42px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    color: white;
    flex-shrink: 0;
}

.icon-danger {
    background: var(--danger);
}

.icon-success {
    background: var(--success);
}

.card-content {
    flex: 1;
}

.card-content h6 {
    margin: 0 0 0.5rem;
    font-size: 0.9375rem;
    font-weight: 600;
    color: var(--text-primary);
}

.card-content .date {
    display: flex;
    align-items: center;
    gap: 0.375rem;
    margin: 0 0 0.625rem;
    font-size: 0.8125rem;
    color: var(--text-secondary);
}

.card-content .comment {
    margin: 0;
    font-size: 0.875rem;
    line-height: 1.5;
    color: var(--text-primary);
    display: flex;
    align-items: start;
    gap: 0.375rem;
}

.card-content .comment i {
    color: #3498db;
    margin-top: 2px;
    flex-shrink: 0;
}

.modal-footer-timeline {
    border-top: 1px solid var(--border);
    padding: 1rem 1.5rem;
    background: white;
}

/* Paginación */
.pagination-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem;
    background: white;
    border-top: 1px solid var(--border);
    flex-wrap: wrap;
    gap: 1rem;
}

.per-page-selector {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.per-page-selector label {
    font-size: 0.875rem;
    margin: 0;
    white-space: nowrap;
}

.per-page-selector select {
    width: 80px;
    border: 1px solid var(--border);
    border-radius: 6px;
    padding: 0.375rem 0.5rem;
    font-size: 0.875rem;
}

.pagination-info {
    font-size: 0.875rem;
    color: var(--text-secondary);
}

.pagination-info strong {
    color: var(--text-primary);
    font-weight: 600;
}

/* Notification Card */
.notification-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.notification-header {
    background: var(--dark);
    color: white;
    padding: 1rem 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.notification-body {
    padding: 0;
}

.notification-table {
    width: 100%;
    border-collapse: collapse;
}

.notification-table thead {
    background: var(--light);
}

.notification-table th {
    padding: 0.875rem 1rem;
    text-align: left;
    font-size: 0.8125rem;
    font-weight: 600;
    color: var(--text-primary);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.notification-table tbody tr {
    border-bottom: 1px solid var(--light);
}

.notification-table tbody tr:hover {
    background: var(--light);
}

.notification-table td {
    padding: 0.875rem 1rem;
    font-size: 0.875rem;
}

.status-icon {
    font-size: 1.5rem;
}

.icon-success {
    color: var(--success);
}

.icon-danger {
    color: var(--danger);
}

.icon-warning {
    color: var(--warning);
}

.icon-info {
    color: #3498db;
}

/* Form Section */
.form-section h3 {
    color: var(--text-primary);
    font-size: 1.125rem;
    font-weight: 600;
    margin-bottom: 1rem;
}

.modern-form .form-label {
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.modern-form .form-control,
.modern-form .form-select {
    border: 1px solid var(--border);
    border-radius: 6px;
    padding: 0.625rem 0.875rem;
    font-size: 0.875rem;
    transition: all 0.3s ease;
}

.modern-form .form-control:focus,
.modern-form .form-select:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(50, 195, 108, 0.1);
    outline: none;
}

.form-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
}

/* Scrollbar */
.modal-body-timeline::-webkit-scrollbar {
    width: 6px;
}

.modal-body-timeline::-webkit-scrollbar-track {
    background: var(--light);
}

.modal-body-timeline::-webkit-scrollbar-thumb {
    background: var(--primary);
    border-radius: 3px;
}

/* Empty State */
.empty-state-inline {
    padding: 2rem;
}

/* Responsive */
@media (max-width: 768px) {
    .table-actions-header {
        flex-direction: column;
        align-items: stretch;
    }
    
    .action-buttons-group {
        flex-direction: column;
    }
    
    .search-input {
        width: 100%;
    }
    
    .modern-table {
        font-size: 0.8125rem;
    }
    
    .modern-table thead th,
    .modern-table tbody td {
        padding: 0.75rem;
    }
    
    .pagination-container {
        flex-direction: column;
        text-align: center;
    }
    
    .timeline-item {
        padding-left: 50px;
    }
    
    .timeline-marker {
        width: 30px;
        height: 30px;
        font-size: 0.75rem;
    }
    
    .timeline-line {
        left: 14px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let debounceTimer;
    const searchInput = document.getElementById('searchTable');
    const tableWrapper = document.getElementById('tableWrapper');
    const loadingSpinner = document.getElementById('loadingSpinner');

    // Función para cargar datos con AJAX
    function loadData(url = null) {
        const searchValue = searchInput?.value || '';
        const perPageSelect = document.getElementById('perPageSelect');
        const perPage = perPageSelect?.value || 15;
        
        if (!url) {
            url = new URL(window.location.href);
            url.searchParams.set('search', searchValue);
            url.searchParams.set('per_page', perPage);
            url.searchParams.set('page', '1');
        }

        // Mostrar loading
        if (loadingSpinner) {
            loadingSpinner.style.display = 'flex';
        }

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            }
        })
        .then(response => response.text())
        .then(html => {
            // Reemplazar el tbody y la paginación
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;
            
            const newTbody = tempDiv.querySelector('#tableBody');
            const newPagination = tempDiv.querySelector('#paginationContainer');
            
            const currentTbody = document.querySelector('#tableBody');
            const currentPagination = document.querySelector('#paginationContainer');
            
            if (newTbody && currentTbody) {
                currentTbody.replaceWith(newTbody);
            }
            
            if (newPagination && currentPagination) {
                currentPagination.replaceWith(newPagination);
            }

            // Actualizar URL sin recargar
            window.history.pushState({}, '', url);
            
            // Reinicializar eventos
            initPaginationEvents();
            initPerPageSelect();
            initTooltips();
            
            // Ocultar loading
            if (loadingSpinner) {
                loadingSpinner.style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (loadingSpinner) {
                loadingSpinner.style.display = 'none';
            }
            alert('Error al cargar los datos. Por favor, recarga la página.');
        });
    }

    // Búsqueda con debounce
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                loadData();
            }, 500);
        });
    }

    // Cambio de registros por página
    function initPerPageSelect() {
        const newPerPageSelect = document.getElementById('perPageSelect');
        if (newPerPageSelect) {
            newPerPageSelect.addEventListener('change', function() {
                loadData();
            });
        }
    }

    // Eventos de paginación
    function initPaginationEvents() {
        const paginationLinks = document.querySelectorAll('.pagination a');
        paginationLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.href;
                if (url) {
                    loadData(url);
                    
                    // Scroll suave al inicio de la tabla
                    tableWrapper.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });
    }

    // Inicializar tooltips
    function initTooltips() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    // Exportar egresos
    const btnEgresoMasivo = document.getElementById('btnEgresoMasivo');
    if (btnEgresoMasivo) {
        btnEgresoMasivo.addEventListener('click', function() {
            const selectedCheckboxes = document.querySelectorAll('.selectOutput:checked');
            if (selectedCheckboxes.length === 0) {
                alert('Por favor seleccione al menos un candidato para exportar');
                return;
            }
            
            const identidades = Array.from(selectedCheckboxes).map(cb => cb.value);
            console.log('Exportando identidades:', identidades);
            // Aquí va tu lógica de exportación existente
        });
    }

    // Inicializar eventos al cargar
    initPaginationEvents();
    initPerPageSelect();
    initTooltips();

    // Scroll superior sincronizado
    const scrollTop = document.getElementById('tableScrollTop');
    const scrollTrack = document.getElementById('tableScrollTrack');

    function syncScrollWidths() {
        if (!scrollTop || !scrollTrack || !tableWrapper) return;
        const table = tableWrapper.querySelector('table');
        if (!table) return;
        scrollTrack.style.width = table.scrollWidth + 'px';
        scrollTop.style.display = table.scrollWidth > tableWrapper.clientWidth ? 'block' : 'none';
    }

    if (scrollTop && tableWrapper) {
        scrollTop.addEventListener('scroll', function () {
            tableWrapper.scrollLeft = scrollTop.scrollLeft;
        });
        tableWrapper.addEventListener('scroll', function () {
            scrollTop.scrollLeft = tableWrapper.scrollLeft;
        });
        window.addEventListener('resize', syncScrollWidths);
        syncScrollWidths();
    }
});
</script>
