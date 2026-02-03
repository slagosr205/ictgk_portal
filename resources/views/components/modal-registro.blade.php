<!-- Modal Registro de Candidato -->
<div class="modal fade" id="registerCandidate" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="registerCandidateLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title" id="registerCandidateLabel">
                    <i class="ri-user-add-line me-2"></i>
                    Registrar Nuevo Colaborador
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form method="POST" action="{{ route('insertarCandidato') }}" id="formNuevoCandidato">
                    @csrf

                    {{-- Sección: Datos Personales --}}
                    <div class="mb-4">
                        <h6 class="text-secondary border-bottom pb-2 mb-3">
                            <i class="ri-user-line me-2"></i>
                            Datos Personales
                        </h6>

                        <div class="row g-3">
                            {{-- DNI/Identidad --}}
                            <div class="col-md-4">
                                <label for="identidad" class="form-label">
                                    <i class="ri-id-card-line me-1 text-muted"></i>
                                    Número de Identidad <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('identidad') is-invalid @enderror border" 
                                       id="identidad" 
                                       name="identidad" 
                                       placeholder="0000-0000-00000"
                                       maxlength="15"
                                       required>
                                @error('identidad')
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                @enderror
                                <small class="form-text text-muted">Formato: 0000-0000-00000</small>
                            </div>

                            {{-- Nombre --}}
                            <div class="col-md-4">
                                <label for="nombre" class="form-label">
                                    <i class="ri-user-3-line me-1 text-muted"></i>
                                    Nombres <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control border" 
                                       id="nombre" 
                                       name="nombre"
                                       placeholder="Ingrese los nombres"
                                       maxlength="100"
                                       required>
                                <div class="invalid-feedback">
                                    Por favor ingrese los nombres
                                </div>
                            </div>

                            {{-- Apellidos --}}
                            <div class="col-md-4">
                                <label for="apellido" class="form-label">
                                    <i class="ri-user-3-line me-1 text-muted"></i>
                                    Apellidos <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control border" 
                                       id="apellido" 
                                       name="apellido"
                                       placeholder="Ingrese los apellidos"
                                       maxlength="100"
                                       required>
                                <div class="invalid-feedback">
                                    Por favor ingrese los apellidos
                                </div>
                            </div>

                            {{-- Fecha de Nacimiento --}}
                            <div class="col-md-3">
                                <label for="fecha_nacimiento" class="form-label">
                                    <i class="ri-calendar-line me-1 text-muted"></i>
                                    Fecha de Nacimiento <span class="text-danger">*</span>
                                </label>
                                <input type="date" 
                                       class="form-control border" 
                                       id="fecha_nacimiento" 
                                       name="fecha_nacimiento"
                                       max="{{ date('Y-m-d', strtotime('-18 years')) }}"
                                       required>
                                <div class="invalid-feedback">
                                    Por favor ingrese la fecha de nacimiento
                                </div>
                                <small class="form-text text-muted" id="edadCalculada"></small>
                            </div>

                            {{-- Género --}}
                            <div class="col-md-3">
                                <label for="generoM_F" class="form-label">
                                    <i class="ri-genderless-line me-1 text-muted"></i>
                                    Género <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" 
                                        id="generoM_F" 
                                        name="generoM_F" 
                                        required>
                                    <option value="">Seleccione...</option>
                                    <option value="m">Masculino</option>
                                    <option value="f">Femenino</option>
                                </select>
                                <div class="invalid-feedback">
                                    Por favor seleccione el género
                                </div>
                            </div>

                            {{-- Teléfono --}}
                            <div class="col-md-3">
                                <label for="telefono" class="form-label">
                                    <i class="ri-phone-line me-1 text-muted"></i>
                                    Teléfono <span class="text-danger">*</span>
                                </label>
                                <input type="tel" 
                                       class="form-control border" 
                                       id="telefono" 
                                       name="telefono"
                                       placeholder="0000-0000"
                                       maxlength="9"
                                       required>
                                <div class="invalid-feedback">
                                    Por favor ingrese el teléfono
                                </div>
                            </div>

                            {{-- Correo --}}
                            <div class="col-md-3">
                                <label for="correo" class="form-label">
                                    <i class="ri-mail-line me-1 text-muted"></i>
                                    Correo Electrónico <span class="text-danger">*</span>
                                </label>
                                <input type="email" 
                                       class="form-control  border" 
                                       id="correo" 
                                       name="correo"
                                       placeholder="ejemplo@correo.com"
                                       maxlength="100"
                                       required>
                                <div class="invalid-feedback">
                                    Por favor ingrese un correo válido
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Sección: Dirección --}}
                    <div class="mb-4">
                        <h6 class="text-secondary border-bottom pb-2 mb-3">
                            <i class="ri-map-pin-line me-2"></i>
                            Dirección
                        </h6>

                        <div class="row g-3">
                            <div class="col-12">
                                <label for="direccion" class="form-label">
                                    <i class="ri-road-map-line me-1 text-muted"></i>
                                    Dirección Completa <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control border" 
                                          id="direccion" 
                                          name="direccion" 
                                          rows="3"
                                          placeholder="Ingrese la dirección completa (calle, colonia, ciudad...)"
                                          maxlength="500"
                                          required></textarea>
                                <div class="form-text text-muted">
                                    <span id="caracteresRestantesDireccion">500</span> caracteres restantes
                                </div>
                                <div class="invalid-feedback">
                                    Por favor ingrese la dirección
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer bg-light border-top">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">
                    <i class="ri-close-line me-2"></i>
                    Cancelar
                </button>
                <button type="submit" form="formNuevoCandidato" class="btn btn-primary" id="btnGuardarCandidato">
                    <i class="ri-save-line me-2"></i>
                    Guardar Candidato
                </button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Estilos para el modal */
    .modal-content {
        border: none;
        border-radius: 0.5rem;
    }

    .modal-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #e0e0e0;
    }

    .modal-footer {
        background-color: #f8f9fa;
        border-top: 1px solid #e0e0e0;
    }

    .modal-body {
        padding: 1.5rem;
    }

    /* Estilos para inputs del modal */
    #registerCandidate .form-label {
        font-weight: 500;
        margin-bottom: 0.5rem;
        color: #495057;
        font-size: 0.95rem;
    }

    #registerCandidate .form-control,
    #registerCandidate .form-select {
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
    }

    #registerCandidate .form-control:focus,
    #registerCandidate .form-select:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
    }

    /* Prevenir zoom en iOS */
    @media (max-width: 768px) {
        #registerCandidate .form-control,
        #registerCandidate .form-select {
            font-size: 16px;
        }
    }

    /* Animación sutil */
    #registerCandidate .form-control,
    #registerCandidate .form-select {
        transition: all 0.2s ease;
    }

    #registerCandidate .form-control:hover,
    #registerCandidate .form-select:hover {
        border-color: #adb5bd;
    }
</style>
@endpush

@push('scripts')
<script>
    import Swal from 'sweetalert2';
    document.addEventListener('DOMContentLoaded', function() {
        const formCandidato = document.getElementById('formNuevoCandidato');
        const modalCandidato = document.getElementById('registerCandidate');
        
        // Validar formato de identidad (Honduras)
        const identidadInput = document.getElementById('identidad');
        if (identidadInput) {
            identidadInput.addEventListener('input', function(e) {
                // Remover caracteres no numéricos excepto guiones
                let value = e.target.value.replace(/[^\d-]/g, '');
                
                // Formato automático: 0000-0000-00000
                if (value.length > 4 && value.indexOf('-') === -1) {
                    value = value.slice(0, 4) + '-' + value.slice(4);
                }
                if (value.length > 9 && value.lastIndexOf('-') === 4) {
                    value = value.slice(0, 9) + '-' + value.slice(9);
                }
                
                e.target.value = value.slice(0, 15);
            });
        }

        // Formatear teléfono automáticamente
        const telefonoInput = document.getElementById('telefono');
        if (telefonoInput) {
            telefonoInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/[^\d-]/g, '');
                
                // Formato: 0000-0000
                if (value.length > 4 && value.indexOf('-') === -1) {
                    value = value.slice(0, 4) + '-' + value.slice(4);
                }
                
                e.target.value = value.slice(0, 9);
            });
        }

        // Calcular edad automáticamente
        const fechaNacimiento = document.getElementById('fecha_nacimiento');
        const edadCalculada = document.getElementById('edadCalculada');
        
        if (fechaNacimiento && edadCalculada) {
            fechaNacimiento.addEventListener('change', function() {
                const fecha = new Date(this.value);
                const hoy = new Date();
                let edad = hoy.getFullYear() - fecha.getFullYear();
                const mes = hoy.getMonth() - fecha.getMonth();
                
                if (mes < 0 || (mes === 0 && hoy.getDate() < fecha.getDate())) {
                    edad--;
                }
                
                if (edad >= 0) {
                    edadCalculada.textContent = `Edad: ${edad} años`;
                    edadCalculada.classList.remove('text-danger');
                    edadCalculada.classList.add('text-success');
                    
                    if (edad < 18) {
                        edadCalculada.textContent = `Edad: ${edad} años (Menor de edad)`;
                        edadCalculada.classList.remove('text-success');
                        edadCalculada.classList.add('text-danger');
                    }
                } else {
                    edadCalculada.textContent = '';
                }
            });
        }

        // Contador de caracteres para dirección
        const direccion = document.getElementById('direccion');
        const caracteresRestantes = document.getElementById('caracteresRestantesDireccion');
        
        if (direccion && caracteresRestantes) {
            direccion.addEventListener('input', function() {
                const restantes = 500 - this.value.length;
                caracteresRestantes.textContent = restantes;
                
                if (restantes < 50) {
                    caracteresRestantes.classList.add('text-warning');
                } else {
                    caracteresRestantes.classList.remove('text-warning');
                }
            });
        }

        // Validación y envío del formulario
        if (formCandidato) {
            formCandidato.addEventListener('submit', function(event) {
                if (!formCandidato.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                } else {
                    event.preventDefault();
                    
                    // Confirmación antes de guardar
                    Swal.fire({
                        title: '¿Confirmar registro?',
                        text: 'Se guardará la información del nuevo colaborador',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#0d6efd',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Sí, guardar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Mostrar loading
                            Swal.fire({
                                title: 'Guardando...',
                                text: 'Por favor espere',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                            
                            formCandidato.submit();
                        }
                    });
                }
                
                formCandidato.classList.add('was-validated');
            });
        }

        // Limpiar formulario al cerrar modal
        if (modalCandidato) {
            modalCandidato.addEventListener('hidden.bs.modal', function() {
                if (formCandidato) {
                    formCandidato.reset();
                    formCandidato.classList.remove('was-validated');
                    
                    // Limpiar edad calculada
                    if (edadCalculada) {
                        edadCalculada.textContent = '';
                    }
                    
                    // Restaurar contador
                    if (caracteresRestantes) {
                        caracteresRestantes.textContent = '500';
                        caracteresRestantes.classList.remove('text-warning');
                    }
                }
            });
        }

        // Capitalizar nombres y apellidos automáticamente
        const nombreInput = document.getElementById('nombre');
        const apellidoInput = document.getElementById('apellido');
        
        [nombreInput, apellidoInput].forEach(input => {
            if (input) {
                input.addEventListener('blur', function() {
                    this.value = this.value
                        .toLowerCase()
                        .split(' ')
                        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                        .join(' ');
                });
            }
        });
    });
</script>
@endpush