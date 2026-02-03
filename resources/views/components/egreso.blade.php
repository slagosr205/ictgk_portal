<div class="container-fluid py-4">
    @php
        $perfil = auth()->user()->perfil ?? null;

        
        $informacionlaboral = $informacionlaboral ?? [];
        $empresas = $empresas ?? [];
        
        // Validar que hay información laboral
        if (empty($informacionlaboral)) {
            return;
        }
        
        $primerRegistro = $informacionlaboral[0] ?? null;
       
        $esAdmin = $perfil['perfilesdescrip']==='admin' ?? false;
           
        
        $estadoLaboral = $primerRegistro && isset($primerRegistro['activo']) && $primerRegistro['activo'] === 's';

       
        
    @endphp

    <div class="card shadow-sm">
        <div class="card-header bg-light border-bottom">
            <h5 class="mb-0 text-dark">
                <i class="ri-user-unfollow-line me-2"></i>
                Registro de Egreso de Colaborador
            </h5>
        </div>

        <div class="card-body">
            <form action="{{ route('hacerEgresos') }}" method="POST" id="formEgreso">
                @csrf

                {{-- Información de la empresa actual --}}
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="alert alert-light border d-flex align-items-center" role="alert">
                            <i class="ri-information-line me-2 fs-4 text-secondary"></i>
                            <div>
                                <strong>Empresa Actual:</strong>
                                
                                    @if ($estadoLaboral)
                                    
                                            <input type="hidden" id="id_empresa" name="id_empresa" value="{{ $empresaActual['id'] ?? '' }}">
                                            <span class="ms-2">{{ $empresaActual['nombre'] ?? 'No especificada' }}</span>
                                    @endif
                               
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Campo oculto de identidad --}}
                <input type="hidden" 
                       id="identidad" 
                       name="identidad" 
                       value="{{ $primerRegistro['identidad'] ?? '' }}">

                {{-- Sección: Información del Egreso --}}
                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <h6 class="text-secondary border-bottom pb-2 mb-3">
                            <i class="ri-file-list-3-line me-2"></i>
                            Información del Egreso
                        </h6>
                    </div>

                    {{-- Tipo de egreso --}}
                    <div class="col-md-6 col-lg-3">
                        <label for="tipo_egreso" class="form-label">
                            <i class="ri-arrow-left-right-line me-1 text-muted"></i>
                            Forma de Egreso <span class="text-danger">*</span>
                        </label>
                        <select name="tipo_egreso" 
                                id="tipo_egreso" 
                                class="form-select" 
                                required>
                            <option value="">Seleccione...</option>
                            <option value="Voluntario">Voluntario</option>
                            <option value="Involuntario">Involuntario</option>
                        </select>
                        <div class="invalid-feedback">
                            Por favor seleccione la forma de egreso
                        </div>
                    </div>

                    {{-- Fecha de egreso --}}
                    <div class="col-md-6 col-lg-3">
                        <label for="tiempo" class="form-label">
                            <i class="ri-calendar-line me-1 text-muted"></i>
                            Fecha de Egreso <span class="text-danger">*</span>
                        </label>
                        <input type="date" 
                               name="tiempo" 
                               id="tiempo" 
                               class="form-control border"
                               max="{{ date('Y-m-d') }}"
                               required>
                        <div class="invalid-feedback">
                            Por favor ingrese la fecha de egreso
                        </div>
                    </div>

                    {{-- Motivo de egreso --}}
                    <div class="col-md-6 col-lg-3">
                        <label for="forma_egreso" class="form-label">
                            <i class="ri-question-line me-1 text-muted"></i>
                            Motivo de Egreso <span class="text-danger">*</span>
                        </label>
                        <select name="forma_egreso" 
                                id="forma_egreso" 
                                class="form-select" 
                                required>
                            <option value="">Seleccione...</option>
                            <option value="Abandono de Labores">Abandono de Labores</option>
                            <option value="Conflictos de Horarios">Conflictos de Horarios</option>
                            <option value="Motivos de estudios">Motivos de Estudios</option>
                            <option value="Nueva Oportunidad Laboral">Nueva Oportunidad Laboral</option>
                            <option value="Enfermedad">Enfermedad</option>
                            <option value="Bajo rendimiento">Bajo Rendimiento</option>
                            <option value="Otros">Otros</option>
                        </select>
                        <div class="invalid-feedback">
                            Por favor seleccione el motivo de egreso
                        </div>
                    </div>

                    {{-- Recomendado --}}
                    <div class="col-md-6 col-lg-3">
                        <label for="recomendado" class="form-label">
                            <i class="ri-thumb-up-line me-1 text-muted"></i>
                            Recomendado <span class="text-danger">*</span>
                        </label>
                        <select name="recomendado" 
                                id="recomendado" 
                                class="form-select" 
                                required>
                            <option value="">Seleccione...</option>
                            <option value="s">Sí</option>
                            <option value="n">No</option>
                        </select>
                        <div class="invalid-feedback">
                            Por favor indique si es recomendado
                        </div>
                        <small class="form-text text-muted">
                            ¿Contrataría nuevamente a este colaborador?
                        </small>
                    </div>
                </div>

                {{-- Sección: Comentarios adicionales --}}
                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <h6 class="text-secondary border-bottom pb-2 mb-3">
                            <i class="ri-message-3-line me-2"></i>
                            Comentarios Adicionales
                        </h6>
                    </div>

                    <div class="col-12">
                        <label for="comentarios" class="form-label">
                            <i class="ri-edit-line me-1 text-muted"></i>
                            Observaciones
                        </label>
                        <textarea name="comentarios" 
                                  id="comentarios" 
                                  class="form-control border" 
                                  rows="5"
                                  placeholder="Ingrese cualquier información adicional relevante sobre el egreso del colaborador..."
                                  maxlength="1000"></textarea>
                        <div class="form-text text-muted">
                            <span id="caracteresRestantes">1000</span> caracteres restantes
                        </div>
                    </div>
                </div>

                {{-- Botones de acción --}}
                <div class="row">
                    <div class="col-12">
                        <hr class="my-4">
                        <div class="d-flex gap-2 justify-content-end flex-wrap">
                            <button type="button" 
                                    class="btn btn-light border" 
                                    onclick="limpiarFormulario()">
                                <i class="ri-refresh-line me-2"></i>
                                Limpiar
                            </button>
                            <button type="submit" 
                                    class="btn btn-primary">
                                <i class="ri-save-fill me-2"></i>
                                Guardar Egreso
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    .card {
        border: 1px solid #e0e0e0;
        border-radius: 0.5rem;
    }

    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #e0e0e0;
        padding: 1rem 1.5rem;
    }

    .form-label {
        font-weight: 500;
        margin-bottom: 0.5rem;
        color: #495057;
        font-size: 0.95rem;
    }

    .form-control,
    .form-select {
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
    }

    .alert-light {
        background-color: #fafafa;
        border-color: #e0e0e0;
    }

    .text-danger {
        color: #dc3545;
    }

    .btn-primary {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }

    .btn-primary:hover {
        background-color: #0b5ed7;
        border-color: #0a58ca;
    }

    .btn-light {
        background-color: #f8f9fa;
        border-color: #dee2e6;
        color: #495057;
    }

    .btn-light:hover {
        background-color: #e9ecef;
        border-color: #ced4da;
    }

    /* Mejorar apariencia de selects en móvil */
    @media (max-width: 768px) {
        .form-select,
        .form-control {
            font-size: 16px; /* Prevenir zoom en iOS */
        }
        
        .d-flex.gap-2 {
            width: 100%;
        }
        
        .d-flex.gap-2 button {
            flex: 1;
        }
    }

    /* Animación sutil para los inputs */
    .form-control,
    .form-select {
        transition: all 0.2s ease;
    }

    .form-control:hover,
    .form-select:hover {
        border-color: #adb5bd;
    }

    /* Estilo para los iconos */
    .ri-information-line,
    .ri-file-list-3-line,
    .ri-message-3-line {
        color: #6c757d;
    }

    /* Sombra sutil para el card */
    .shadow-sm {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('formEgreso');
        const comentarios = document.getElementById('comentarios');
        const caracteresRestantes = document.getElementById('caracteresRestantes');
        
        // Contador de caracteres
        if (comentarios && caracteresRestantes) {
            comentarios.addEventListener('input', function() {
                const restantes = 1000 - this.value.length;
                caracteresRestantes.textContent = restantes;
                
                if (restantes < 100) {
                    caracteresRestantes.classList.add('text-warning');
                } else {
                    caracteresRestantes.classList.remove('text-warning');
                }
            });
        }
        
        // Validación del formulario con Bootstrap
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            } else {
                // Confirmación antes de enviar
                event.preventDefault();
                
                Swal.fire({
                    title: '¿Confirmar egreso?',
                    text: 'Esta acción registrará el egreso del colaborador',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#0d6efd',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, confirmar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            }
            
            form.classList.add('was-validated');
        });
        
        // Deshabilitar fechas futuras
        const fechaEgreso = document.getElementById('tiempo');
        if (fechaEgreso) {
            const hoy = new Date().toISOString().split('T')[0];
            fechaEgreso.setAttribute('max', hoy);
        }
        
        // Lógica condicional: si es involuntario, sugerir no recomendado
        const tipoEgreso = document.getElementById('tipo_egreso');
        const recomendado = document.getElementById('recomendado');
        
        if (tipoEgreso && recomendado) {
            tipoEgreso.addEventListener('change', function() {
                if (this.value === 'Involuntario') {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'info',
                        title: 'Sugerencia: Egreso involuntario generalmente implica no recomendado',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                }
            });
        }
    });
    
    // Función para limpiar el formulario
    function limpiarFormulario() {
        const form = document.getElementById('formEgreso');
        
        Swal.fire({
            title: '¿Limpiar formulario?',
            text: 'Se perderán todos los datos ingresados',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#6c757d',
            cancelButtonColor: '#0d6efd',
            confirmButtonText: 'Sí, limpiar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                form.reset();
                form.classList.remove('was-validated');
                
                // Actualizar contador de caracteres
                const caracteresRestantes = document.getElementById('caracteresRestantes');
                if (caracteresRestantes) {
                    caracteresRestantes.textContent = '1000';
                    caracteresRestantes.classList.remove('text-warning');
                }
                
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Formulario limpiado',
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        });
    }
</script>
@endpush