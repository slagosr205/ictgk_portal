<!-- Modal de Edición de Registro -->
<div class="modal fade" id="modalEdicion" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- Header -->
            <div class="modal-header bg-warning">
                <h5 class="modal-title">
                    <i class="ri-edit-line me-2"></i>
                    Editar Registro - Fila <span id="editNumeroFila" class="fw-bold">0</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- Form -->
            <form id="formEditarRegistro">
                <div class="modal-body">
                    <input type="hidden" id="editIndice">

                    <!-- Datos del Candidato -->
                    <h6 class="border-bottom pb-2 mb-3">
                        <i class="ri-user-line me-2"></i>Datos del Candidato
                    </h6>

                    <div class="row g-3">
                        <!-- Identidad -->
                        <!-- Identidad -->
                        <div class="col-md-6">
                            <label for="editIdentidad" class="form-label">
                                Identidad <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                class="form-control"
                                id="editIdentidad"
                                placeholder="0000-0000-00000"
                                maxlength="15"
                                required>
                            <small class="form-text text-muted">
                                Formato: 0000-0000-00000 (se guardará sin guiones)
                            </small>
                        </div>
                        <!-- Nombre -->
                        <div class="col-md-6">
                            <label for="editNombre" class="form-label">
                                Nombre <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="editNombre" required>
                        </div>

                        <!-- Apellido -->
                        <div class="col-md-6">
                            <label for="editApellido" class="form-label">
                                Apellido <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="editApellido" required>
                        </div>

                        <!-- Género -->
                        <div class="col-md-6">
                            <label for="editGenero" class="form-label">Género</label>
                            <select class="form-select" id="editGenero">
                                <option value="">Seleccione...</option>
                                <option value="M">Masculino</option>
                                <option value="F">Femenino</option>
                            </select>
                        </div>

                        <!-- Fecha Nacimiento -->
                        <div class="col-md-6">
                            <label for="editFechaNacimiento" class="form-label">
                                Fecha de Nacimiento
                            </label>
                            <input type="date" class="form-control" id="editFechaNacimiento">
                            <small class="form-text text-muted">Debe ser mayor de 18 años</small>
                        </div>

                        <!-- Teléfono -->
                        <div class="col-md-6">
                            <label for="editTelefono" class="form-label">Teléfono</label>
                            <input
                                type="text"
                                class="form-control"
                                id="editTelefono"
                                placeholder="+50412345678">
                        </div>

                        <!-- Correo -->
                        <div class="col-md-12">
                            <label for="editCorreo" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" id="editCorreo">
                        </div>

                        <!-- Dirección -->
                        <div class="col-md-12">
                            <label for="editDireccion" class="form-label">Dirección</label>
                            <textarea class="form-control" id="editDireccion" rows="2"></textarea>
                        </div>
                    </div>

                    <!-- Datos del Ingreso -->
                    <h6 class="border-bottom pb-2 mb-3 mt-4">
                        <i class="ri-briefcase-line me-2"></i>Datos del Ingreso
                    </h6>

                    <div class="row g-3">
                        <!-- Empresa -->
                        <div class="col-md-6">
                            <label for="editEmpresa" class="form-label">
                                Empresa <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="editEmpresa" required>
                                <option value="">Seleccione una empresa...</option>
                            </select>
                        </div>

                        <!-- Puesto -->
                        <div class="col-md-6">
                            <label for="editPuesto" class="form-label">
                                Puesto <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="editPuesto" required>
                                <option value="">Seleccione un puesto...</option>
                            </select>
                            <small class="form-text text-muted" id="helpPuesto">
                                Primero seleccione una empresa
                            </small>
                        </div>

                        <!-- Fecha Ingreso -->
                        <div class="col-md-6">
                            <label for="editFechaIngreso" class="form-label">
                                Fecha de Ingreso <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control" id="editFechaIngreso" required>
                        </div>

                        <!-- Fecha Egreso -->
                        <div class="col-md-6">
                            <label for="editFechaEgreso" class="form-label">
                                Fecha de Egreso
                            </label>
                            <input type="date" class="form-control" id="editFechaEgreso">
                            <small class="form-text text-muted">
                                Dejar vacío si está activo
                            </small>
                        </div>

                        <!-- Área -->
                        <div class="col-md-12">
                            <label for="editArea" class="form-label">Área</label>
                            <input type="text" class="form-control" id="editArea">
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ri-close-line me-2"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ri-save-line me-2"></i>Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    #modalEdicion .modal-body {
        max-height: 70vh;
        overflow-y: auto;
    }

    #modalEdicion h6 {
        color: #072132;
        font-weight: 600;
    }

    #modalEdicion .form-label {
        font-weight: 500;
        color: #495057;
    }

    #modalEdicion .form-control:focus,
    #modalEdicion .form-select:focus {
        border-color: #ffc107;
        box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
    }
</style>
@endpush