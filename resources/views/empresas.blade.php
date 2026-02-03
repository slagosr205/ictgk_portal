@extends('layouts.app')

@section('empresas')
@php
    $isAdmin = auth()->check() && auth()->user()->perfil_id === 1;
@endphp

<div class="empresas-page" id="dtempresas">
    <div class="page-header">
        <div class="header-info">
            <div class="header-icon">
                <i class="ri-building-2-line"></i>
            </div>
            <div>
                <h2>Gestión de Empresas</h2>
                <p>Administra empresas, contactos y estado en un solo módulo.</p>
            </div>
        </div>
        <div class="header-actions">
            <button class="btn-modern btn-primary" data-bs-toggle="modal" data-bs-target="#nuevaempresa">
                <i class="ri-sticky-note-add-fill"></i>
                <span>Nueva empresa</span>
            </button>
            @if ($isAdmin)
                <button class="btn-modern btn-secondary" data-bs-toggle="modal" data-bs-target="#empresaMasiva">
                    <i class="ri-upload-2-line"></i>
                    <span>Carga masiva</span>
                </button>
            @endif
        </div>
    </div>

    <div class="table-card">
        <div class="card-header">
            <h3>Empresas registradas</h3>
            <p>Consulta rápida de estado, contacto y datos principales.</p>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ url()->current() }}" class="table-filters" id="empresasFilterForm">
                <div class="filter-group">
                    <label for="search" class="form-label">Buscar</label>
                    <input type="text" id="search" name="search" class="form-control" value="{{ request('search') }}" placeholder="Nombre, correo, contacto o teléfono">
                </div>
                <div class="filter-group">
                    <label for="estadoFiltro" class="form-label">Estado</label>
                    <select id="estadoFiltro" name="estado" class="form-select">
                        <option value="">Todos</option>
                        <option value="a" {{ request('estado') === 'a' ? 'selected' : '' }}>Activo</option>
                        <option value="n" {{ request('estado') === 'n' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                </div>
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
            <div class="table-wrapper" id="empresasTableWrapper">
                <table id="tbempresas" class="modern-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Empresa</th>
                            <th>Teléfonos</th>
                            <th>Contacto</th>
                            <th>Correo</th>
                            <th>Estado</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($empresas as $em)
                        <tr data-id="{{$em->id}}">
                            <td data-campo="id"><span class="badge-id">{{$em->id}}</span></td>
                            <td data-campo="nombre">{{$em->nombre}}</td>
                            <td data-campo="telefonos">{{$em->telefonos}}</td>
                            <td data-campo="contacto">{{$em->contacto}}</td>
                            <td data-campo="correo">{{$em->correo}}</td>
                            <td data-campo="estado">
                                <div class="form-check form-switch">
                                    <input class="form-check-input chkactivoEmpresa" type="checkbox" role="switch" id="flexSwitchCheckChecked-{{$em->id}}" {{ $em->estado === 'a' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="flexSwitchCheckChecked-{{$em->id}}">{{ $em->estado === 'a' ? 'Activo' : 'Inactivo' }}</label>
                                </div>
                            </td>
                            <td>
                                <button type="button" class="btn-modern btn-warning btn-consulta" data-id="{{$em->id}}" data-bs-toggle="modal" data-bs-target="#modificarempresa">
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
                <button type="button" class="btn-scroll" id="empresasScrollLeft" aria-label="Desplazar izquierda">
                    <i class="ri-arrow-left-s-line"></i>
                </button>
                <button type="button" class="btn-scroll" id="empresasScrollRight" aria-label="Desplazar derecha">
                    <i class="ri-arrow-right-s-line"></i>
                </button>
            </div>
            <div class="pagination-container">
                <div class="pagination-info">
                    Mostrando <strong>{{$empresas->firstItem() ?? 0}}</strong> a <strong>{{$empresas->lastItem() ?? 0}}</strong> de <strong>{{$empresas->total()}}</strong> registros
                </div>
                <div class="pagination-controls">
                    {{ $empresas->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para crear -->
    <div class="modal fade" id="nuevaempresa" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="nuevaempresaLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content modal-modern">
                <div class="modal-header modal-header-modern">
                    <div class="modal-header-info">
                        <div class="modal-icon">
                            <i class="ri-building-2-line"></i>
                        </div>
                        <div>
                            <h1 class="modal-title fs-5" id="nuevaempresaLabel">Agregar Empresa</h1>
                            <p>Completa los datos principales para registrar la empresa.</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body modal-body-modern">
                    <form method="post" id="insertCompany" enctype="multipart/form-data">
                        <div class="form-grid">
                            <div class="form-field">
                                <label for="nombre">Nombre</label>
                                <input type="text" name="nombre" id="nombre" class="form-control" required>
                            </div>
                            <div class="form-field">
                                <label for="telefonos">Teléfono</label>
                                <input type="text" name="telefonos" id="telefonos" class="form-control" required>
                            </div>
                            <div class="form-field full">
                                <label for="correo">Correo</label>
                                <input type="email" name="correo" id="correo" class="form-control" required>
                            </div>
                            <div class="form-field full">
                                <label for="contacto">Contacto</label>
                                <input type="text" name="contacto" id="contacto" class="form-control" required>
                            </div>
                            <div class="form-field">
                                <label for="direccion">Dirección</label>
                                <input type="text" name="direccion" id="direccion" class="form-control" required>
                            </div>
                            <div class="form-field">
                                <label for="logo">Logo de empresa</label>
                                <input type="file" name="logo" id="logo" class="form-control" accept="image/*" required>
                            </div>
                        </div>
                        <div class="modal-footer modal-footer-modern">
                            <button type="button" class="btn-modern btn-secondary" data-bs-dismiss="modal">
                                <i class="ri-close-line"></i>
                                <span>Cancelar</span>
                            </button>
                            <button type="submit" class="btn-modern btn-primary" id="enviarDatosEmpresa">
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
    <div class="modal fade" id="modificarempresa" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modificarempresaLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content modal-modern">
                <div class="modal-header modal-header-modern">
                    <div class="modal-header-info">
                        <div class="modal-icon warning">
                            <i class="ri-edit-line"></i>
                        </div>
                        <div>
                            <h1 class="modal-title fs-5" id="modificarempresaLabel">Actualizar Empresa</h1>
                            <p>Modifica los datos y guarda los cambios.</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body modal-body-modern">
                    <form method="post" id="updateCompany" enctype="multipart/form-data">
                        <input type="hidden" name="id" id="id_empresa">
                        <div class="form-grid">
                            <div class="form-field">
                                <label for="modnombre">Nombre</label>
                                <input type="text" name="nombre" id="modnombre" class="form-control" required>
                            </div>
                            <div class="form-field">
                                <label for="modtelefonos">Teléfono</label>
                                <input type="text" name="telefonos" id="modtelefonos" class="form-control" required>
                            </div>
                            <div class="form-field full">
                                <label for="modcorreo">Correo</label>
                                <input type="email" name="correo" id="modcorreo" class="form-control" required>
                            </div>
                            <div class="form-field full">
                                <label for="modcontacto">Contacto</label>
                                <input type="text" name="contacto" id="modcontacto" class="form-control" required>
                            </div>
                            <div class="form-field">
                                <label for="moddireccion">Dirección</label>
                                <input type="text" name="direccion" id="moddireccion" class="form-control" required>
                            </div>
                            <div class="form-field">
                                <label for="modlogo">Logo de empresa</label>
                                <input type="file" name="logo" id="modlogo" class="form-control" accept="image/*">
                            </div>
                            <div class="form-field full">
                                <label>Estado</label>
                                <div id="estado"></div>
                            </div>
                        </div>
                        <div class="modal-footer modal-footer-modern">
                            <button type="button" class="btn-modern btn-secondary" data-bs-dismiss="modal">
                                <i class="ri-close-line"></i>
                                <span>Cancelar</span>
                            </button>
                            <button type="submit" class="btn-modern btn-primary" id="ActualizarDatosEmpresa">
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
    <div class="modal fade" id="empresaMasiva" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="empresaMasivaLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content modal-modern">
                <div class="modal-header modal-header-modern">
                    <div class="modal-header-info">
                        <div class="modal-icon">
                            <i class="ri-upload-2-line"></i>
                        </div>
                        <div>
                            <h1 class="modal-title fs-5" id="empresaMasivaLabel">Carga masiva de empresas</h1>
                            <p>Pega un CSV con los campos: nombre, direccion, telefonos, contacto, correo, estado, logo.</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body modal-body-modern">
                    <form method="post" id="insertCompanyBulk" enctype="multipart/form-data">
                        <div class="bulk-toolbar">
                            <button type="button" class="btn-modern btn-secondary" id="addBulkRow">
                                <i class="ri-add-line"></i>
                                <span>Agregar fila</span>
                            </button>
                            <button type="button" class="btn-modern btn-secondary" id="clearBulkRows">
                                <i class="ri-delete-bin-6-line"></i>
                                <span>Limpiar filas</span>
                            </button>
                        </div>
                        <div class="bulk-table-wrapper">
                            <table class="bulk-table" id="bulkCompaniesTable">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Dirección</th>
                                        <th>Teléfonos</th>
                                        <th>Contacto</th>
                                        <th>Correo</th>
                                        <th>Estado</th>
                                        <th>Logo (URL)</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="bulk-row">
                                        <td><input type="text" name="rows[0][nombre]" class="form-control" required></td>
                                        <td><input type="text" name="rows[0][direccion]" class="form-control" required></td>
                                        <td><input type="text" name="rows[0][telefonos]" class="form-control" required></td>
                                        <td><input type="text" name="rows[0][contacto]" class="form-control" required></td>
                                        <td><input type="email" name="rows[0][correo]" class="form-control" required></td>
                                        <td>
                                            <select name="rows[0][estado]" class="form-select">
                                                <option value="a">Activo</option>
                                                <option value="n">Inactivo</option>
                                            </select>
                                        </td>
                                        <td><input type="text" name="rows[0][logo]" class="form-control" placeholder="https://..."></td>
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
                            <p class="csv-note">CSV opcional (si no deseas escribir filas manuales).</p>
                            <textarea id="csv_text" name="csv_text" rows="4" class="form-control" placeholder="nombre,direccion,telefonos,contacto,correo,estado,logo"></textarea>
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
</div>

@section('empresasjs')
    @vite(['resources/js/empresas.js'])
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

.empresas-page {
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
    min-width: 920px;
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
