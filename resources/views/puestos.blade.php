@extends('layouts.app')

@section('puestos')
@php
    $isAdmin = auth()->check() && auth()->user()->perfil_id === 1;
@endphp

<div class="puestos-page" id="dtpuestos" data-is-admin="{{ $isAdmin }}" data-user-empresa-id="{{ auth()->check() && !$isAdmin ? auth()->user()->empresa_id : '' }}">
    <div class="page-header">
        <div class="header-info">
            <div class="header-icon">
                <i class="ri-briefcase-3-line"></i>
            </div>
            <div>
                <h2>Gestión de Puestos</h2>
                <p>Administra los puestos de trabajo y sus departamentos.</p>
            </div>
        </div>
        <div class="header-actions">
            <button class="btn-modern btn-primary" data-bs-toggle="modal" data-bs-target="#nuevopuesto">
                <i class="ri-sticky-note-add-fill"></i>
                <span>Nuevo puesto</span>
            </button>
            @if ($isAdmin)
            <button class="btn-modern btn-secondary" data-bs-toggle="modal" data-bs-target="#puestoMasivo">
                <i class="ri-upload-2-line"></i>
                <span>Carga masiva</span>
            </button>
            @endif
        </div>
    </div>

    <div class="table-card">
        <div class="card-header">
            <h3>Puestos registrados</h3>
            <p>Consulta rápida de puestos, departamentos y fechas.</p>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ url()->current() }}" class="table-filters" id="puestosFilterForm">
                <div class="filter-group">
                    <label for="search" class="form-label">Buscar</label>
                    <input type="text" id="search" name="search" class="form-control" value="{{ request('search') }}" placeholder="Nombre del puesto o departamento">
                </div>
                <div class="filter-group">
                    <label for="departamento_filtro" class="form-label">Departamento</label>
                    <select id="departamento_filtro" name="departamento" class="form-select">
                        <option value="">Todos</option>
                        @foreach($departamentos as $depto)
                            <option value="{{ $depto->id }}" {{ request('departamento') == $depto->id ? 'selected' : '' }}>{{ $depto->nombredepartamento }}</option>
                        @endforeach
                    </select>
                </div>
                @if($isAdmin)
                <div class="filter-group">
                    <label for="empresa_filtro" class="form-label">Empresa</label>
                    <select id="empresa_filtro" name="empresa_id" class="form-select">
                        <option value="">Todas</option>
                        @foreach($empresas as $empresa)
                            <option value="{{ $empresa->id }}" {{ request('empresa_id') == $empresa->id ? 'selected' : '' }}>{{ $empresa->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div class="filter-group">
                    <label for="per_page" class="form-label">Registros</label>
                    <select id="per_page" name="per_page" class="form-select">
                        <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                        <option value="15" {{ request('per_page') == 15 || !request('per_page') ? 'selected' : '' }}>15</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>
                <div class="filter-actions">
                    <button type="submit" class="btn-modern btn-primary">
                        <i class="ri-filter-2-line"></i>
                        <span>Filtrar</span>
                    </button>
                    <a href="{{ url()->current() }}" class="btn-modern btn-secondary">
                        <i class="ri-refresh-line"></i>
                        <span>Limpiar</span>
                    </a>
                </div>
            </form>
            <div class="table-wrapper" id="puestosTableWrapper">
                <table id="tbpuestos" class="modern-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Departamento</th>
                            <th>Empresa</th>
                            <th>Creado</th>
                            <th>Última Actualización</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($puestos as $ps)
                        <tr data-id="{{$ps->id}}">
                            <td data-campo="id"><span class="badge-id">{{$ps->id}}</span></td>
                            <td data-campo="nombrepuesto">{{$ps->nombrepuesto}}</td>
                            <td data-campo="nombredepartamento">{{$ps->nombredepartamento}}</td>
                            <td data-campo="empresa_nombre">{{$ps->empresa_nombre ?? 'N/A'}}</td>
                            <td data-campo="created_at">{{$ps->created_at}}</td>
                            <td data-campo="updated_at">{{$ps->updated_at}}</td>
                            <td>
                                <button type="button" class="btn-modern btn-warning btn-consulta" data-id="{{$ps->id}}" data-bs-toggle="modal" data-bs-target="#modificarpuesto">
                                    <i class="ri-pencil-line"></i>
                                    <span>Modificar</span>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="table-scroll-actions">
                <button type="button" class="btn-scroll" id="puestosScrollLeft" aria-label="Desplazar izquierda">
                    <i class="ri-arrow-left-s-line"></i>
                </button>
                <button type="button" class="btn-scroll" id="puestosScrollRight" aria-label="Desplazar derecha">
                    <i class="ri-arrow-right-s-line"></i>
                </button>
            </div>
            <div class="pagination-container">
                <div class="pagination-info">
                    Mostrando <strong>{{$puestos->firstItem() ?? 0}}</strong> a <strong>{{$puestos->lastItem() ?? 0}}</strong> de <strong>{{$puestos->total()}}</strong> registros
                </div>
                <div class="pagination-controls">
                    {{ $puestos->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para crear -->
    <div class="modal fade" id="nuevopuesto" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="nuevopuestoLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content modal-modern">
                <div class="modal-header modal-header-modern">
                    <div class="modal-header-info">
                        <div class="modal-icon">
                            <i class="ri-briefcase-3-line"></i>
                        </div>
                        <div>
                            <h1 class="modal-title fs-5" id="nuevopuestoLabel">Agregar Puesto</h1>
                            <p>Completa los datos para registrar el puesto.</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body modal-body-modern">
                    <form action="{{route('insertPositions')}}" method="POST">
                        @csrf
                        <div class="form-grid">
                            <div class="form-field">
                                <label for="nombrepuesto">Nombre del Puesto</label>
                                <input type="text" id="nombrepuesto" name="nombrepuesto" class="form-control" required>
                            </div>
                            <div class="form-field">
                                <label for="departamento_id">Departamento</label>
                                <select name="departamento_id" id="departamento_id" class="form-select departamentos-select" required>
                                    <option value="">Seleccionar departamento</option>
                                    @foreach ($departamentos as $departamento)
                                        <option value="{{$departamento->id}}" data-empresa="{{ $departamento->empresa_id ?? '' }}">{{$departamento->nombredepartamento}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer modal-footer-modern">
                            <button type="button" class="btn-modern btn-secondary" data-bs-dismiss="modal">
                                <i class="ri-close-line"></i>
                                <span>Cancelar</span>
                            </button>
                            <button type="submit" class="btn-modern btn-primary">
                                <i class="ri-save-3-line"></i>
                                <span>Guardar</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Modificar -->
    <div class="modal fade" id="modificarpuesto" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modificarpuestoLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content modal-modern">
                <div class="modal-header modal-header-modern">
                    <div class="modal-header-info">
                        <div class="modal-icon warning">
                            <i class="ri-edit-line"></i>
                        </div>
                        <div>
                            <h1 class="modal-title fs-5" id="modificarpuestoLabel">Actualizar Puesto</h1>
                            <p>Modifica los datos y guarda los cambios.</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body modal-body-modern">
                    <form method="POST" action="{{route('updatePosition')}}">
                        @csrf
                        <input type="hidden" name="puesto_id" id="puesto_id">
                        <div class="form-grid">
                            <div class="form-field">
                                <label for="puestonombre">Nombre del Puesto</label>
                                <input type="text" name="puestonombre" id="puestonombre" class="form-control" required>
                            </div>
                            <div class="form-field">
                                <label for="departamento_id_mod">Departamento</label>
                                <select name="departamento_id" id="departamento_id_mod" class="form-select" required>
                                    @foreach ($departamentos as $departamento)
                                        <option value="{{$departamento->id}}">{{$departamento->nombredepartamento}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer modal-footer-modern">
                            <button type="button" class="btn-modern btn-secondary" data-bs-dismiss="modal">
                                <i class="ri-close-line"></i>
                                <span>Cancelar</span>
                            </button>
                            <button type="submit" class="btn-modern btn-primary">
                                <i class="ri-refresh-line"></i>
                                <span>Actualizar</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if ($isAdmin)
    <div class="modal fade" id="puestoMasivo" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="puestoMasivoLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content modal-modern">
                <div class="modal-header modal-header-modern">
                    <div class="modal-header-info">
                        <div class="modal-icon">
                            <i class="ri-upload-2-line"></i>
                        </div>
                        <div>
                            <h1 class="modal-title fs-5" id="puestoMasivoLabel">Carga masiva de puestos</h1>
                            <p>Ingresa los puestos indicando el departamento al que pertenecen.</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body modal-body-modern">
                    <form method="post" id="insertPuestoBulk" action="{{ route('insertPositionsBulk') }}" enctype="multipart/form-data">
                        @csrf
                        @if($isAdmin)
                        <div class="form-field full mb-3">
                            <label for="empresa_bulk">Empresa</label>
                            <select name="empresa_id" id="empresa_bulk" class="form-select">
                                <option value="">Seleccionar empresa</option>
                                @foreach($empresas as $empresa)
                                    <option value="{{ $empresa->id }}">{{ $empresa->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        <div class="bulk-toolbar">
                            <button type="button" class="btn-modern btn-secondary" id="addBulkRowPuesto">
                                <i class="ri-add-line"></i>
                                <span>Agregar fila</span>
                            </button>
                            <button type="button" class="btn-modern btn-secondary" id="clearBulkRowsPuesto">
                                <i class="ri-delete-bin-6-line"></i>
                                <span>Limpiar filas</span>
                            </button>
                        </div>
                        <div class="bulk-table-wrapper">
                            <table class="bulk-table" id="bulkPuestosTable">
                                <thead>
                                    <tr>
                                        <th>Nombre del Puesto</th>
                                        <th>Departamento</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="bulk-row">
                                        <td><input type="text" name="rows[0][nombrepuesto]" class="form-control" required></td>
                                        <td>
                                            <select name="rows[0][departamento_id]" class="form-select departamentos-select" required>
                                                <option value="">Seleccionar</option>
                                                @if($isAdmin)
                                                    @foreach($departamentos as $departamento)
                                                        <option value="{{ $departamento->id }}" data-empresa="{{ $departamento->empresa_id ?? '' }}">{{ $departamento->nombredepartamento }} ({{ $departamento->empresa_nombre }})</option>
                                                    @endforeach
                                                @else
                                                    @foreach($departamentos as $departamento)
                                                        <option value="{{ $departamento->id }}">{{ $departamento->nombredepartamento }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </td>
                                        <td>
                                            <button type="button" class="btn-remove-row" aria-label="Eliminar fila">
                                                <i class="ri-close-line"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="modal-footer modal-footer-modern">
                            <button type="button" class="btn-modern btn-secondary" data-bs-dismiss="modal">
                                <i class="ri-close-line"></i>
                                <span>Cancelar</span>
                            </button>
                            <button type="submit" class="btn-modern btn-primary">
                                <i class="ri-upload-2-line"></i>
                                <span>Procesar</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if ($errors->any())
        <div class="alert-card error">
            <i class="ri-error-warning-line"></i>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{$error}}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('successPositions'))
        <div class="alert-card success">
            <i class="ri-check-line"></i>
            <span>{{ session('successPositions') }}</span>
        </div>
    @endif

    @if (session('updatedPositions'))
        <div class="alert-card success">
            <i class="ri-check-line"></i>
            <span>{{ session('updatedPositions') }}</span>
        </div>
    @endif

    @if (session('updatedPositionserror'))
        <div class="alert-card error">
            <i class="ri-error-warning-line"></i>
            <span>{{ session('updatedPositionserror') }}</span>
        </div>
    @endif
</div>

@section('puestosjs')
    @vite(['resources/js/libpuestos/puestos.js'])
@stop

<style>

.puestos-page {
    max-width: 1200px;
    margin: 2rem auto;
    padding: 0 1.5rem 2rem;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1.5rem;
    background: linear-gradient(135deg, rgb(var(--brand-primary-rgb) / 0.12), rgb(var(--brand-dark-rgb) / 0.08));
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
}

.header-info {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.header-icon {
    width: 54px;
    height: 54px;
    border-radius: 12px;
    background: var(--primary);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.header-info h2 {
    margin: 0;
    color: var(--text-primary);
    font-size: 1.5rem;
}

.header-info p {
    margin: 0.25rem 0 0;
    color: var(--text-secondary);
}

.header-actions {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.btn-modern {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.6rem 1.1rem;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.85rem;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-modern.btn-primary {
    background: var(--primary);
    color: white;
}

.btn-modern.btn-primary:hover {
    background: #2aaa5e;
    transform: translateY(-1px);
}

.btn-modern.btn-secondary {
    background: white;
    color: var(--text-primary);
    border: 1px solid var(--border);
}

.btn-modern.btn-secondary:hover {
    border-color: var(--primary);
    color: var(--primary);
    background: rgb(var(--brand-primary-rgb) / 0.08);
}

.btn-modern.btn-warning {
    background: var(--warning);
    color: var(--dark);
}

.btn-modern.btn-warning:hover {
    background: #c9ce3a;
    transform: translateY(-1px);
}

.table-card {
    background: white;
    border: 1px solid var(--border);
    border-radius: 16px;
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.06);
    overflow: hidden;
}

.card-header {
    padding: 1.25rem 1.5rem;
    background: var(--dark);
    color: white;
}

.card-header h3 {
    margin: 0;
    font-size: 1.125rem;
}

.card-header p {
    margin: 0.35rem 0 0;
    opacity: 0.85;
    font-size: 0.9rem;
}

.card-body {
    padding: 1.5rem;
    background: white;
}

.table-wrapper {
    overflow-x: auto;
}

.modern-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 900px;
}

.modern-table thead {
    background: var(--dark);
    color: white;
}

.modern-table th,
.modern-table td {
    padding: 0.85rem 1rem;
    text-align: left;
    font-size: 0.85rem;
}

.modern-table tbody tr {
    border-bottom: 1px solid var(--border);
}

.modern-table tbody tr:hover {
    background: var(--light);
}

.badge-id {
    display: inline-block;
    padding: 0.2rem 0.6rem;
    background: rgb(var(--brand-primary-rgb) / 0.12);
    color: var(--primary);
    border-radius: 999px;
    font-weight: 600;
    font-size: 0.75rem;
}

.table-scroll-actions {
    display: flex;
    justify-content: flex-end;
    gap: 0.5rem;
    margin-top: 0.75rem;
}

.btn-scroll {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    border: 1px solid var(--border);
    background: white;
    color: var(--text-primary);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.btn-scroll:hover {
    border-color: var(--primary);
    color: var(--primary);
    background: rgb(var(--brand-primary-rgb) / 0.08);
}

.pagination-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 1rem;
    flex-wrap: wrap;
    gap: 0.75rem;
}

.pagination-info {
    font-size: 0.875rem;
    color: var(--text-secondary);
}

.pagination-controls .pagination {
    margin: 0;
}

.table-filters {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 1rem;
    padding: 1rem;
    margin-bottom: 1rem;
    background: var(--light);
    border: 1px solid var(--border);
    border-radius: 12px;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 0.4rem;
}

.filter-actions {
    display: flex;
    align-items: flex-end;
    gap: 0.75rem;
    grid-column: 1 / -1;
    justify-content: flex-end;
}

.modal-modern {
    border: none;
    border-radius: 14px;
    overflow: hidden;
}

.modal-header-modern {
    background: var(--dark);
    color: white;
    padding: 1.25rem 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.modal-icon {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    background: var(--primary);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.modal-icon.warning {
    background: #f39c12;
}

.modal-header-modern p {
    margin: 0.25rem 0 0;
    font-size: 0.85rem;
    opacity: 0.9;
}

.modal-body-modern {
    background: var(--light);
    padding: 1.5rem;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 1rem;
}

.form-field {
    display: flex;
    flex-direction: column;
    gap: 0.4rem;
}

.form-field label {
    font-weight: 600;
    font-size: 0.85rem;
    color: var(--text-primary);
}

.form-control {
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 0.65rem 0.85rem;
    font-size: 0.9rem;
    background: white;
}

.form-control:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgb(var(--brand-primary-rgb) / 0.12);
}

.form-select {
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 0.65rem 0.85rem;
    font-size: 0.9rem;
    background: white;
}

.form-select:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgb(var(--brand-primary-rgb) / 0.12);
}

.modal-footer-modern {
    border-top: 1px solid var(--border);
    padding: 1rem 1.5rem;
    background: white;
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
}

.alert-card {
    margin-top: 1rem;
    padding: 0.85rem 1rem;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(231, 76, 60, 0.08);
    color: var(--text-primary);
    border: 1px solid rgba(231, 76, 60, 0.25);
}

.alert-card.success {
    background: rgba(39, 174, 96, 0.08);
    border-color: rgba(39, 174, 96, 0.25);
}

.alert-card ul {
    margin: 0;
}

@media (max-width: 992px) {
    .page-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .form-grid {
        grid-template-columns: 1fr;
    }

    .table-filters {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 768px) {
    .table-filters {
        grid-template-columns: 1fr;
    }

    .filter-actions {
        align-items: stretch;
        flex-direction: column;
    }
}
</style>

@endsection