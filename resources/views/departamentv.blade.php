@extends('layouts.app')

@section('departamentos')
@php
    $isAdmin = $isAdmin ?? (auth()->check() && auth()->user()->perfil_id === 1);
@endphp

<div class="departamentos-page" id="dtdepartamentos">
    <div class="page-header">
        <div class="header-info">
            <div class="header-icon">
                <i class="ri-community-line"></i>
            </div>
            <div>
                <h2>Gestión de Departamentos</h2>
                <p>Administra departamentos por empresa con controles claros y rápidos.</p>
            </div>
        </div>
        <div class="header-actions">
            <button class="btn-modern btn-primary" data-bs-toggle="modal" data-bs-target="#nuevodepartamento">
                <i class="ri-sticky-note-add-fill"></i>
                <span>Nuevo departamento</span>
            </button>
            <button class="btn-modern btn-secondary" data-bs-toggle="modal" data-bs-target="#departamentoMasivo">
                <i class="ri-upload-2-line"></i>
                <span>Carga masiva</span>
            </button>
        </div>
    </div>

    <div class="table-card">
        <div class="card-header">
            <h3>Departamentos registrados</h3>
            <p>Visualiza empresa, fechas y acciones en un solo módulo.</p>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ url()->current() }}" class="table-filters" id="departamentosFilterForm">
                <div class="filter-group">
                    <label for="search" class="form-label">Buscar</label>
                    <input type="text" id="search" name="search" class="form-control" value="{{ request('search') }}" placeholder="Departamento o empresa">
                </div>
                @if ($isAdmin)
                <div class="filter-group">
                    <label for="empresa_id" class="form-label">Empresa</label>
                    <select id="empresa_id" name="empresa_id" class="form-select">
                        <option value="">Todas</option>
                        @foreach ($empresas as $empresa)
                            <option value="{{$empresa->id}}" {{ (string) request('empresa_id') === (string) $empresa->id ? 'selected' : '' }}>
                                {{$empresa->nombre}}
                            </option>
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

            <div class="table-wrapper" id="departamentosTableWrapper">
                <table id="tbdepartamentos" class="modern-table">
                    <thead>
                        <tr>
                            <th>ID Departamento</th>
                            <th>Departamento</th>
                            <th>Empresa</th>
                            <th>Fecha de Creación</th>
                            <th>Fecha de Actualización</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($departamentos as $item)
                            <tr data-id="{{$item->id}}">
                                <td><span class="badge-id">{{$item->id}}</span></td>
                                <td class="dep-nombre">{{$item->nombredepartamento}}</td>
                                <td class="dep-empresa">{{$item->empresa_nombre}}</td>
                                <td>{{\Carbon\Carbon::parse($item->created_at)->isoFormat('LL LTS')}}</td>
                                <td>{{\Carbon\Carbon::parse($item->updated_at)->isoFormat('LL LTS')}}</td>
                                <td>
                                    <button class="btn-modern btn-warning btnInfoDepto" data-id="{{$item->id}}">
                                        <i class="ri-pencil-line"></i>
                                        <span>Actualizar</span>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="table-scroll-actions">
                <button type="button" class="btn-scroll" id="departamentosScrollLeft" aria-label="Desplazar izquierda">
                    <i class="ri-arrow-left-s-line"></i>
                </button>
                <button type="button" class="btn-scroll" id="departamentosScrollRight" aria-label="Desplazar derecha">
                    <i class="ri-arrow-right-s-line"></i>
                </button>
            </div>
            <div class="pagination-container">
                <div class="pagination-info">
                    Mostrando <strong>{{$departamentos->firstItem() ?? 0}}</strong> a <strong>{{$departamentos->lastItem() ?? 0}}</strong> de <strong>{{$departamentos->total()}}</strong> registros
                </div>
                <div class="pagination-controls">
                    {{ $departamentos->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para crear -->
    <div class="modal fade" id="nuevodepartamento" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="nuevodepartamentoLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content modal-modern">
                <div class="modal-header modal-header-modern">
                    <div class="modal-header-info">
                        <div class="modal-icon">
                            <i class="ri-community-line"></i>
                        </div>
                        <div>
                            <h1 class="modal-title fs-5" id="nuevodepartamentoLabel">Agregar Departamento</h1>
                            <p>Define el nombre y asigna la empresa.</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body modal-body-modern">
                    <form id="insertDepartamento">
                        <div class="form-grid">
                            <div class="form-field">
                                <label for="nombredepartamento">Nombre</label>
                                <input type="text" name="nombredepartamento" id="nombredepartamento" class="form-control" required>
                            </div>
                            <div class="form-field">
                                <label for="empresa_id_select">Empresa</label>
                                @if ($isAdmin)
                                    <select name="empresa_id" id="empresa_id_select" class="form-select" required>
                                        <option value="">Seleccione</option>
                                        @foreach ($empresas as $empresa)
                                            <option value="{{$empresa->id}}">{{$empresa->nombre}}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <input class="form-control" type="text" value="{{$empresas->nombre}}" readonly>
                                    <input type="hidden" name="empresa_id" value="{{$empresas->id}}">
                                @endif
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

    <!-- Modal para actualizar -->
    <div class="modal fade" id="actualizardepartamento" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="actualizardepartamentoLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content modal-modern">
                <div class="modal-header modal-header-modern">
                    <div class="modal-header-info">
                        <div class="modal-icon warning">
                            <i class="ri-edit-line"></i>
                        </div>
                        <div>
                            <h1 class="modal-title fs-5" id="actualizardepartamentoLabel">Actualizar Departamento</h1>
                            <p>Actualiza el nombre del departamento.</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body modal-body-modern">
                    <form id="updateDepartamento">
                        <input type="hidden" id="updatedepartamento_id">
                        <div class="form-grid">
                            <div class="form-field full">
                                <label for="nombredepartamentoactual">Nombre</label>
                                <input type="text" name="nombredepartamentoactual" id="nombredepartamentoactual" class="form-control" required>
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

    <!-- Modal carga masiva -->
    <div class="modal fade" id="departamentoMasivo" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="departamentoMasivoLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content modal-modern">
                <div class="modal-header modal-header-modern">
                    <div class="modal-header-info">
                        <div class="modal-icon">
                            <i class="ri-upload-2-line"></i>
                        </div>
                        <div>
                            <h1 class="modal-title fs-5" id="departamentoMasivoLabel">Carga masiva de departamentos</h1>
                            <p>Agrega filas manualmente o usa CSV opcional.</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body modal-body-modern">
                    <form id="insertDepartamentoBulk">
                        @if ($isAdmin)
                        <div class="form-field full">
                            <label for="empresa_id_bulk">Empresa por defecto</label>
                            <select id="empresa_id_bulk" name="empresa_id" class="form-select">
                                <option value="">Seleccione</option>
                                @foreach ($empresas as $empresa)
                                    <option value="{{$empresa->id}}">{{$empresa->nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        <div class="bulk-toolbar">
                            <button type="button" class="btn-modern btn-secondary" id="addBulkRowDepto">
                                <i class="ri-add-line"></i>
                                <span>Agregar fila</span>
                            </button>
                            <button type="button" class="btn-modern btn-secondary" id="clearBulkRowsDepto">
                                <i class="ri-delete-bin-6-line"></i>
                                <span>Limpiar filas</span>
                            </button>
                        </div>
                        <div class="bulk-table-wrapper">
                            <table class="bulk-table" id="bulkDepartamentosTable">
                                <thead>
                                    <tr>
                                        <th>Departamento</th>
                                        @if ($isAdmin)
                                            <th>Empresa</th>
                                        @endif
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="bulk-row">
                                        <td><input type="text" name="rows[0][nombredepartamento]" class="form-control" required></td>
                                        @if ($isAdmin)
                                            <td>
                                                <select name="rows[0][empresa_id]" class="form-select">
                                                    <option value="">Empresa</option>
                                                    @foreach ($empresas as $empresa)
                                                        <option value="{{$empresa->id}}">{{$empresa->nombre}}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                        @endif
                                        <td>
                                            <button type="button" class="btn-remove-row" aria-label="Eliminar fila">
                                                <i class="ri-close-line"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="csv-optional">
                            <p class="csv-note">CSV opcional (encabezados: nombredepartamento{{ $isAdmin ? ',empresa_id' : '' }})</p>
                            <textarea id="csv_text" name="csv_text" rows="4" class="form-control" placeholder="nombredepartamento{{ $isAdmin ? ',empresa_id' : '' }}"></textarea>
                            <input type="file" id="csv_file" name="csv_file" class="form-control" accept=".csv,text/csv">
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
</div>

@section('departamentosjs')
    @vite(['resources/js/departamentos.js'])
@stop

<style>
:root {
    --primary: #32C36C;
    --light: #F6F7F8;
    --dark: #1A2A36;
    --danger: #e74c3c;
    --success: #32C36C;
    --warning: #dce442;
    --text-primary: #2c3e50;
    --text-secondary: #6c757d;
    --border: #e1e8ed;
}

.departamentos-page {
    max-width: 1200px;
    margin: 2rem auto;
    padding: 0 1.5rem 2rem;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1.5rem;
    background: linear-gradient(135deg, rgba(50, 195, 108, 0.12), rgba(26, 42, 54, 0.08));
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
    background: rgba(50, 195, 108, 0.08);
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
    background: rgba(50, 195, 108, 0.12);
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
    background: rgba(50, 195, 108, 0.08);
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

.form-field.full {
    grid-column: 1 / -1;
}

.form-field label {
    font-weight: 600;
    font-size: 0.85rem;
    color: var(--text-primary);
}

.form-control,
.form-select {
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 0.65rem 0.85rem;
    font-size: 0.9rem;
    background: white;
}

.form-control:focus,
.form-select:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(50, 195, 108, 0.12);
}

.modal-footer-modern {
    border-top: 1px solid var(--border);
    padding: 1rem 1.5rem;
    background: white;
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
}

.bulk-toolbar {
    display: flex;
    gap: 0.75rem;
    margin-bottom: 1rem;
    flex-wrap: wrap;
}

.bulk-table-wrapper {
    overflow-x: auto;
    border: 1px solid var(--border);
    border-radius: 10px;
    background: white;
    margin-bottom: 1rem;
}

.bulk-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 680px;
}

.bulk-table th,
.bulk-table td {
    padding: 0.6rem;
    border-bottom: 1px solid var(--border);
    font-size: 0.85rem;
}

.btn-remove-row {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    border: 1px solid var(--border);
    background: white;
    color: var(--danger);
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.csv-optional {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.csv-note {
    margin: 0;
    color: var(--text-secondary);
    font-size: 0.85rem;
}

.bulk-row.row-error {
    background: rgba(231, 76, 60, 0.08);
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
