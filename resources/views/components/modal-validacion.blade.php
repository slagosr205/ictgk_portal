<!-- Modal de Validación de Resultados -->
<div class="modal fade" id="modalValidacion" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <!-- Header -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="ri-file-list-3-line me-2"></i>
                    Resultados de Validación
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body">
                <!-- Resumen de Estadísticas -->
                <div class="alert alert-info d-flex align-items-center mb-4 border-0 shadow-sm">
                    <div class="flex-shrink-0">
                        <i class="ri-information-line fs-2"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="alert-heading mb-2">Resumen de Validación</h6>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge bg-secondary fs-6">
                                <i class="ri-file-list-line me-1"></i>
                                Total: <strong id="estadTotal">0</strong>
                            </span>
                            <span class="badge bg-success fs-6">
                                <i class="ri-checkbox-circle-line me-1"></i>
                                Válidos: <strong id="estadValidos">0</strong>
                            </span>
                            <span class="badge bg-danger fs-6">
                                <i class="ri-error-warning-line me-1"></i>
                                Con Errores: <strong id="estadErrores">0</strong>
                            </span>
                            <span class="badge bg-warning text-dark fs-6">
                                <i class="ri-alert-line me-1"></i>
                                Con Advertencias: <strong id="estadAdvertencias">0</strong>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Barra de Filtros -->
                <div class="d-flex flex-wrap gap-2 mb-3 align-items-center">
                    <div class="btn-group" role="group">
                        <input type="radio" class="btn-check" name="filtroEstado" id="filtroTodos" value="todos" checked>
                        <label class="btn btn-outline-primary" for="filtroTodos">
                            <i class="ri-file-list-line me-1"></i>Todos
                        </label>
                        
                        <input type="radio" class="btn-check" name="filtroEstado" id="filtroValidos" value="validos">
                        <label class="btn btn-outline-success" for="filtroValidos">
                            <i class="ri-checkbox-circle-line me-1"></i>Válidos
                        </label>
                        
                        <input type="radio" class="btn-check" name="filtroEstado" id="filtroErrores" value="errores">
                        <label class="btn btn-outline-danger" for="filtroErrores">
                            <i class="ri-error-warning-line me-1"></i>Con Errores
                        </label>
                    </div>

                    <div class="ms-auto">
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="btnExpandirTodos">
                            <i class="ri-arrow-down-s-line me-1"></i>Expandir
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="btnContraerTodos">
                            <i class="ri-arrow-up-s-line me-1"></i>Contraer
                        </button>
                    </div>
                </div>

                <!-- Tabla de Registros -->
                <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                    <table class="table table-bordered table-hover table-sm mb-0">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th style="width: 50px;" class="text-center">Fila</th>
                                <th style="width: 60px;" class="text-center">Estado</th>
                                <th style="min-width: 150px;">Nombre</th>
                                <th style="min-width: 120px;">Identidad</th>
                                <th style="min-width: 150px;">Empresa</th>
                                <th style="min-width: 150px;">Puesto</th>
                                <th style="width: 110px;">Fecha Ingreso</th>
                                <th style="min-width: 250px;">Errores/Advertencias</th>
                                <th style="width: 100px;" class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tablaValidacion">
                            <!-- Se llena dinámicamente con JavaScript -->
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="ri-file-search-line fs-1 d-block mb-2"></i>
                                    <p class="mb-0">Cargando datos...</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ri-close-line me-2"></i>Cancelar
                </button>
                <button type="button" class="btn btn-success" id="btnConfirmarImportacion">
                    <i class="ri-check-line me-2"></i>
                    Confirmar Importación (<span id="contadorValidos">0</span> registros)
                </button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
/* Estilos del modal de validación */
#modalValidacion .modal-dialog {
    max-width: 95%;
}

#modalValidacion .table-responsive {
    border-radius: 8px;
    border: 1px solid #dee2e6;
}

#modalValidacion .sticky-top {
    position: sticky;
    top: 0;
    z-index: 10;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

#modalValidacion .table-hover tbody tr:hover {
    background-color: rgba(0,0,0,0.02);
    cursor: pointer;
}

#modalValidacion .badge {
    padding: 0.5rem 0.75rem;
}

.btn-check:checked + .btn-outline-primary {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.btn-check:checked + .btn-outline-success {
    background-color: #198754;
    border-color: #198754;
}

.btn-check:checked + .btn-outline-danger {
    background-color: #dc3545;
    border-color: #dc3545;
}
</style>
@endpush