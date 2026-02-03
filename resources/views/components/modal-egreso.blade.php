{{-- resources/views/components/modal-egreso.blade.php --}}

<div class="modal fade" id="modalEgreso" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="ri-logout-box-r-line me-2"></i>
                    Procesar Egreso de Colaboradores
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form id="formEgreso">
                <div class="modal-body">
                    <!-- Empleados Seleccionados -->
                    <div class="alert alert-info">
                        <i class="ri-information-line me-2"></i>
                        <strong>Empleados seleccionados:</strong>
                        <span id="cantidadSeleccionados">0</span>
                    </div>

                    <div class="mb-3" id="listaEmpleadosSeleccionados">
                        <!-- Se llenará dinámicamente -->
                    </div>

                    <hr>

                    <!-- Fecha de Egreso y Motivo -->
                    <div class="row">
                        <div class="col-md-6">
                            <label for="fechaEgreso" class="form-label">
                                Fecha de Egreso <span class="text-danger">*</span>
                            </label>
                            <input
                                type="date"
                                class="form-control border"
                                id="fechaEgreso"
                                name="fecha_egreso"
                                required>
                            <small class="form-text text-muted">
                                Fecha efectiva de salida del colaborador
                            </small>
                        </div>

                        <div class="col-md-6">
                            <label for="motivoEgreso" class="form-label">
                                Motivo de Egreso <span class="text-danger">*</span>
                            </label>
                            <select class="form-select border" id="motivoEgreso" name="motivo_egreso" required>
                                <option value="">Seleccione un motivo...</option>
                                <option value="Abandono de Labores">Abandono de Labores</option>
                                <option value="Conflictos de Horarios">Conflictos de Horarios</option>
                                <option value="Motivos de estudios">Motivos de Estudios</option>
                                <option value="Nueva Oportunidad Laboral">Nueva Oportunidad Laboral</option>
                                <option value="Enfermedad">Enfermedad</option>
                                <option value="Bajo rendimiento">Bajo Rendimiento</option>
                                <option value="Otros">Otros</option>
                            </select>
                        </div>
                    </div>

                    <!-- Recomendado y Recontratación -->
                    <div class="row mt-3">
                        {{-- Tipo de egreso --}}
                        <div class="col-md-6 ">
                            <label for="tipo_egreso" class="form-label">
                                <i class="ri-arrow-left-right-line me-1 text-muted"></i>
                                Forma de Egreso <span class="text-danger">*</span>
                            </label>
                            <select 
                                name="tipo_egreso"
                                id="tipo_egreso"
                                class="form-select border"
                                required>
                                <option value="">Seleccione un tipo...</option>
                                <option value="Voluntario">Voluntario</option>
                                <option value="Involuntario">Involuntario</option>
                            </select>
                           
                        </div>

                        <div class="col-md-6">
                            <label for="recomendado" class="form-label">
                                ¿Recomendado? <span class="text-danger">*</span>
                            </label>
                            <div class="d-flex gap-3">
                                <div class="form-check form-check-inline">
                                    <input
                                        class="form-check-input"
                                        type="radio"
                                        name="recomendado"
                                        id="recomendadoSi"
                                        value="s"
                                        required>
                                    <label class="form-check-label" for="recomendadoSi">
                                        <i class="ri-thumb-up-line text-success me-1"></i>
                                        Sí
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input
                                        class="form-check-input"
                                        type="radio"
                                        name="recomendado"
                                        id="recomendadoNo"
                                        value="n"
                                        required>
                                    <label class="form-check-label" for="recomendadoNo">
                                        <i class="ri-thumb-down-line text-danger me-1"></i>
                                        No
                                    </label>
                                </div>
                            </div>
                            <small class="form-text text-muted">
                                ¿El empleado es recomendable para futuras contrataciones?
                            </small>
                        </div>


                    </div>

                    <!-- Comentarios -->
                    <div class="mt-3">
                        <label for="comentariosEgreso" class="form-label">
                            Comentarios Adicionales
                        </label>
                        <textarea
                            class="form-control border"
                            id="comentariosEgreso"
                            name="comentarios"
                            rows="3"
                            maxlength="1000"
                            placeholder="Detalles adicionales sobre el egreso, motivos específicos, observaciones..."></textarea>
                        <small class="form-text text-muted">
                            Máximo 1000 caracteres
                        </small>
                    </div>

                    <!-- Advertencia -->
                    <div class="alert alert-warning mt-3 mb-0">
                        <i class="ri-alert-line me-2"></i>
                        <strong>Importante:</strong> Esta acción marcará al colaborador como inactivo y no podrá revertirse desde este módulo.
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ri-close-line me-2"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-danger" id="btnConfirmarEgreso">
                        <i class="ri-check-line me-2"></i>Confirmar Egresos
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>