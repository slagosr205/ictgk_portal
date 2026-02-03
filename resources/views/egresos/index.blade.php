{{-- resources/views/egresos/index.blade.php --}}

@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-gradient-danger">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="ri-logout-box-line fs-3 me-3 text-white"></i>
                            <div>
                                <h4 class="mb-0 text-white">Gestión de Egresos</h4>
                                <small class="text-white">Proceso de salida de colaboradores</small>
                            </div>
                        </div>
                        <div>
                            <span class="badge bg-light text-dark fs-6" id="totalEmpleados">0 empleados activos</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">
                            <i class="ri-filter-3-line me-2"></i>Filtros de Búsqueda
                        </h5>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnLimpiarFiltros">
                            <i class="ri-refresh-line me-1"></i>Limpiar Filtros
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form id="formFiltros">
                        <div class="row g-3">
                            @if($esAdmin)
                            <!-- Selector de Empresa (solo para admin) -->
                            <div class="col-md-3">
                                <label for="filtroEmpresa" class="form-label">
                                    <i class="ri-building-line me-1"></i>Empresa
                                </label>
                                <select class="form-select border p-2" id="filtroEmpresa" name="empresa_id">
                                    <option value="">Todas las empresas</option>
                                    @foreach($empresas as $empresa)
                                        <option value="{{ $empresa->id }}">{{ $empresa->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif

                            <!-- Búsqueda General -->
                            <div class="col-md-{{ $esAdmin ? '3' : '4' }}">
                                <label for="filtroBusqueda" class="form-label">
                                    <i class="ri-search-line me-1"></i>Buscar
                                </label>
                                <input 
                                    type="text" 
                                    class="form-control border p-2" 
                                    id="filtroBusqueda" 
                                    name="busqueda"
                                    placeholder="Nombre, apellido o identidad..."
                                >
                            </div>

                            <!-- Departamento -->
                            <div class="col-md-{{ $esAdmin ? '3' : '4' }}">
                                <label for="filtroDepartamento" class="form-label">
                                    <i class="ri-folder-line me-1"></i>Departamento
                                </label>
                                <select class="form-select border p-2" id="filtroDepartamento" name="departamento_id">
                                    <option value="">Todos</option>
                                </select>
                            </div>

                            <!-- Puesto -->
                            <div class="col-md-{{ $esAdmin ? '3' : '4' }}">
                                <label for="filtroPuesto" class="form-label">
                                    <i class="ri-briefcase-line me-1"></i>Puesto
                                </label>
                                <select class="form-select border p-2" id="filtroPuesto" name="puesto_id">
                                    <option value="">Todos</option>
                                </select>
                            </div>

                            <!-- Área -->
                            <div class="col-md-3">
                                <label for="filtroArea" class="form-label">
                                    <i class="ri-organization-chart me-1"></i>Área
                                </label>
                                <select class="form-select border p-2" id="filtroArea" name="area">
                                    <option value="">Todas</option>
                                </select>
                            </div>

                            <!-- Fecha Ingreso Desde -->
                            <div class="col-md-3">
                                <label for="filtroFechaDesde" class="form-label">
                                    <i class="ri-calendar-line me-1"></i>Ingreso desde
                                </label>
                                <input 
                                    type="date" 
                                    class="form-control border p-2" 
                                    id="filtroFechaDesde" 
                                    name="fecha_ingreso_desde"
                                >
                        </div>
                            <!-- Fecha Ingreso Hasta -->
                            <div class="col-md-3">
                                <label for="filtroFechaHasta" class="form-label">
                                    <i class="ri-calendar-line me-1"></i>Ingreso hasta
                                </label>
                                <input 
                                    type="date" 
                                    class="form-control border  p-2" 
                                    id="filtroFechaHasta" 
                                    name="fecha_ingreso_hasta"
                                >
                            </div>

                            <!-- Registros por página -->
                            <div class="col-md-3">
                                <label for="filtroPerPage" class="form-label">
                                    <i class="ri-list-check me-1"></i>Mostrar
                                </label>
                                <select class="form-select border p-2" id="filtroPerPage" name="per_page">
                                    <option value="25">25 registros</option>
                                    <option value="50" selected>50 registros</option>
                                    <option value="100">100 registros</option>
                                    <option value="200">200 registros</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <button type="submit" class="btn btn-info">
                                    <i class="ri-search-line me-2"></i>Buscar Empleados
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Empleados -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="mb-0">
                                <i class="ri-team-line me-2"></i>Empleados Activos
                            </h5>
                            <small class="text-muted" id="infoSeleccion">
                                Selecciona empleados para procesar su egreso
                            </small>
                        </div>
                        <div>
                            <button type="button" class="btn btn-danger" id="btnProcesarEgresos" disabled>
                                <i class="ri-logout-box-r-line me-2"></i>
                                Procesar Egresos (<span id="countSeleccionados">0</span>)
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <!-- Loading -->
                    <div id="loadingEmpleados" class="text-center py-5" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="mt-3 text-muted">Cargando empleados...</p>
                    </div>

                    <!-- Tabla -->
                    <div class="table-responsive" id="contenedorTabla">
                        <table class="table table-hover table-sm mb-0" id="tablaEmpleados">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th width="50">
                                        <input type="checkbox" class="form-check-input" id="checkTodos">
                                    </th>
                                    <th width="120">Identidad</th>
                                    <th>Nombre Completo</th>
                                    <th>Puesto</th>
                                    <th>Departamento</th>
                                    @if($esAdmin)
                                    <th>Empresa</th>
                                    @endif
                                    <th>Área</th>
                                    <th>Fecha Ingreso</th>
                                    <th>Antigüedad</th>
                                    <th width="100">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyEmpleados">
                                <tr>
                                    <td colspan="10" class="text-center text-muted py-5">
                                        <i class="ri-search-line fs-1 d-block mb-2"></i>
                                        <p>Usa los filtros para buscar empleados</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <div id="infoPaginacion" class="text-muted small">
                                <!-- Mostrando X a Y de Z registros -->
                            </div>
                            <nav id="paginacionNav">
                                <!-- Botones de paginación -->
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Egreso -->
<x-modal-egreso />

@endsection

@push('styles')
<style>
.bg-gradient-danger {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
}

.sticky-top {
    position: sticky;
    top: 0;
    z-index: 10;
}

.table-hover tbody tr:hover {
    background-color: #f8f9fa;
    cursor: pointer;
}

.table tbody tr.selected {
    background-color: #cfe2ff !important;
}

.badge-antiguedad {
    font-size: 0.85rem;
    font-weight: 500;
}
</style>
@endpush

@push('scripts')

    {{-- SOLO cargar el script en esta vista --}}
    @vite(['resources/js/egresos.js'])
@endpush