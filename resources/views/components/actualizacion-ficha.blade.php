@php
    use Jenssegers\Date\Date;
    Date::setLocale('es');
    
    // Validar datos
    $infocandidatos = $infocandidatos ?? null;
    if (!$infocandidatos) {
        return;
    }
    
    // Determinar género
    $genero = strtoupper($infocandidatos->generoM_F ?? 'M');
    $esHombre = in_array($genero, ['M', 'MASCULINO']);
    
    // Estado del candidato
    $estado = $infocandidatos->activo ?? 'x';
@endphp

<div class="card shadow-sm ficha-personal-card">
    <div class="card-body">
        <div class="row g-4">
            {{-- Columna 1: Avatar y Nombre --}}
            <div class="col-lg-3 col-md-4">
                <div class="text-center">
                    {{-- Avatar --}}
                    <div class="avatar-container mb-3">
                        @if ($esHombre)
                            <img class="avatar-img" 
                                 src="{{ Storage::url('avatar.png') }}" 
                                 alt="Avatar Masculino">
                        @else
                            <img class="avatar-img" 
                                 src="{{ Storage::url('mujer.png') }}" 
                                 alt="Avatar Femenino">
                        @endif
                        
                        {{-- Badge de estado --}}
                        <div class="avatar-badge">
                            @switch($estado)
                                @case('n')
                                    <span class="badge bg-secondary">
                                        <i class="ri-information-line"></i>
                                    </span>
                                    @break
                                @case('s')
                                    <span class="badge bg-success">
                                        <i class="ri-check-line"></i>
                                    </span>
                                    @break
                                @default
                                    <span class="badge bg-warning">
                                        <i class="ri-alert-line"></i>
                                    </span>
                            @endswitch
                        </div>
                    </div>

                    {{-- Campo de Nombre Completo (Unificado Visualmente) --}}
                        <div class="info-group text-start mb-3">
                            <label class="info-label">
                                <i class="ri-user-line me-2 text-muted"></i>
                                Nombre Completo
                            </label>
                            <div class="nombre-completo-container">
                                <input type="text" 
                                    class="form-control form-control-sm nombre-input" 
                                    id="nombre" 
                                    value="{{ $infocandidatos->nombre }}"
                                    placeholder="Nombre"
                                    style="text-transform: uppercase;">
                                <input type="text" 
                                    class="form-control form-control-sm apellido-input" 
                                    id="apellido" 
                                    value="{{ $infocandidatos->apellido }}"
                                    placeholder="Apellido"
                                    style="text-transform: uppercase;">
                            </div>
                        </div>

                    {{-- Botón actualizar --}}
                    <button class="btn btn-primary w-100 btnupdate" 
                            id="{{ $infocandidatos->id }}"
                            data-bs-toggle="tooltip"
                            title="Actualizar información del colaborador">
                        <i class="ri-refresh-line me-2"></i>
                        Actualizar Ficha
                    </button>
                </div>
            </div>

            {{-- Columna 2: Información Personal --}}
            <div class="col-lg-3 col-md-4">
                <div class="info-group">
                    <label class="info-label">
                        <i class="ri-id-card-line me-2 text-muted"></i>
                        Identidad
                    </label>
                    <p class="info-value" id="identidad">
                        {{ $infocandidatos->identidad }}
                    </p>
                </div>

                <div class="info-group">
                    <label class="info-label">
                        <i class="ri-genderless-line me-2 text-muted"></i>
                        Género
                    </label>
                    <p class="info-value" id="genero">
                        {{ $esHombre ? 'Masculino' : 'Femenino' }}
                    </p>
                </div>

                <div class="info-group">
                    <label class="info-label">
                        <i class="ri-calendar-line me-2 text-muted"></i>
                        Fecha de Nacimiento
                    </label>
                    <p class="info-value" id="fechaNacimiento">
                        @if($infocandidatos->fecha_nacimiento)
                            {{ Date::parse($infocandidatos->fecha_nacimiento)->format('l j F Y') }}
                        @else
                            No especificada
                        @endif
                    </p>
                </div>
            </div>

            {{-- Columna 3: Información de Contacto (Editable) --}}
            <div class="col-lg-3 col-md-4">
                <div class="info-group">
                    <label for="telefono" class="info-label">
                        <i class="ri-phone-line me-2 text-muted"></i>
                        Teléfono
                    </label>
                    <input type="tel" 
                           class="form-control form-control-sm border" 
                           id="telefono" 
                           value="{{ $infocandidatos->telefono }}"
                           placeholder="0000-0000"
                           maxlength="9">
                </div>

                <div class="info-group">
                    <label for="correo" class="info-label">
                        <i class="ri-mail-line me-2 text-muted"></i>
                        Correo Electrónico
                    </label>
                    <input type="email" 
                           class="form-control form-control-sm border" 
                           id="correo" 
                           value="{{ $infocandidatos->correo }}"
                           placeholder="ejemplo@correo.com">
                </div>

                <div class="info-group">
                    <label for="direccion" class="info-label">
                        <i class="ri-map-pin-line me-2 text-muted"></i>
                        Dirección
                    </label>
                    <textarea class="form-control form-control-sm border" 
                              id="direccion" 
                              rows="2"
                              placeholder="Ingrese la dirección">{{ $infocandidatos->direccion }}</textarea>
                </div>
            </div>

            {{-- Columna 4: Estado --}}
            <div class="col-lg-3 col-md-12">
                <div class="estado-container">
                   
                   
                    @switch($estado)
                        @case('n')
                            <div class="w-50 d-flex flex-column align-items-center text-center">
                                 <label class="info-label text-center d-block mb-3">
                                    <i class="ri-shield-check-line me-2"></i>
                                    Estado del Colaborador
                                </label>
                                <img src="{{ Storage::url('informacion.png') }}" 
                                     alt="Inactivo" 
                                     class="img-fluid mb-1" width="100px">
                                <strong class="estado-text mb-0">Inactivo para ingresar</strong>
                                <small class="text-muted">El colaborador está actualmente trabajando</small>
                            </div>
                            @break
                        @case('s')
                            <div class="estado-badge estado-activo">
                                <img src="{{ Storage::url('cheque.png') }}" 
                                     alt="Activo" 
                                     class="estado-icon" width="100px">
                                <p class="estado-text mb-0">Activo</p>
                                <small class="text-muted">El colaborador está actualmente trabajando</small>
                            </div>
                            @break
                        @default
                            <div class="estado-badge estado-pendiente">
                                <i class="ri-alert-line estado-icon-text"></i>
                                <p class="estado-text mb-0">Estado Pendiente</p>
                                <small class="text-muted">Contacte con Recursos Humanos</small>
                            </div>
                    @endswitch
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Card Principal */
    .ficha-personal-card {
        border: 1px solid #e0e0e0;
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
    }

    .ficha-personal-card .card-body {
        padding: 1.5rem;
    }

    /* Avatar */
    .avatar-container {
        position: relative;
        display: inline-block;
    }

    .avatar-img {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 50%;
        border: 3px solid #e9ecef;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }

    .avatar-img:hover {
        transform: scale(1.05);
    }

    .avatar-badge {
        position: absolute;
        bottom: 0;
        right: 0;
        background: white;
        border-radius: 50%;
        padding: 2px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .avatar-badge .badge {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
    }

    /* Nombre del Colaborador */
    .nombre-colaborador {
        font-size: 1rem;
        font-weight: 600;
        color: #212529;
        margin-bottom: 1rem;
        line-height: 1.4;
        word-break: break-word;
    }

    /* Grupos de Información */
    .info-group {
        margin-bottom: 1.25rem;
    }

    .info-label {
        font-size: 0.85rem;
        font-weight: 600;
        color: #6c757d;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
    }

    .info-value {
        font-size: 0.95rem;
        font-weight: 500;
        color: #212529;
        margin: 0;
        padding: 0.5rem;
        background-color: #f8f9fa;
        border-radius: 0.375rem;
        border: 1px solid #e9ecef;
    }

    /* Inputs Editables */
    .info-group .form-control {
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        font-size: 0.9rem;
    }

    .info-group .form-control:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
    }

    .info-group textarea.form-control {
        resize: vertical;
        min-height: 60px;
    }

    /* Estado Container */
    .estado-container {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        height: 100%;
        min-height: 200px;
    }

    .estado-badge {
        text-align: center;
        padding: 1.5rem;
        border-radius: 0.5rem;
        width: 100%;
    }

    .estado-activo {
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        border: 2px solid #28a745;
    }

    .estado-inactivo {
        background: linear-gradient(135deg, #e2e3e5 0%, #d6d8db 100%);
        border: 2px solid #6c757d;
    }

    .estado-pendiente {
        background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
        border: 2px solid #ffc107;
    }

    .estado-icon {
        width: 64px;
        height: 64px;
        margin-bottom: 0.75rem;
        filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
    }

    .estado-icon-text {
        font-size: 64px;
        color: #ffc107;
        margin-bottom: 0.75rem;
    }

    .estado-text {
        font-size: 1.1rem;
        font-weight: 600;
        color: #212529;
        margin-bottom: 0.5rem;
    }

    /* Botón Actualizar */
    .btnupdate {
        font-weight: 500;
        padding: 0.6rem 1.25rem;
        transition: all 0.3s ease;
    }

    .btnupdate:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.2);
    }

    /* Responsive */
    @media (max-width: 992px) {
        .estado-container {
            min-height: auto;
            margin-top: 1rem;
        }

        .avatar-img {
            width: 80px;
            height: 80px;
        }

        .nombre-colaborador {
            font-size: 0.95rem;
        }
    }

    @media (max-width: 768px) {
        .ficha-personal-card .card-body {
            padding: 1rem;
        }

        .info-group {
            margin-bottom: 1rem;
        }

        .avatar-img {
            width: 70px;
            height: 70px;
        }

        .estado-icon {
            width: 48px;
            height: 48px;
        }

        .estado-icon-text {
            font-size: 48px;
        }
    }

    /* Animaciones */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .ficha-personal-card {
        animation: fadeIn 0.3s ease-in-out;
    }

    /* Hover Effects */
    .info-value {
        transition: background-color 0.2s ease;
    }

    .info-value:hover {
        background-color: #e9ecef;
    }

    /* Campo de Nombre Completo Unificado */
.nombre-completo-container {
    display: flex;
    position: relative;
    background: white;
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
    overflow: hidden;
    transition: all 0.2s ease;
}

.nombre-completo-container:focus-within {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
}

.nombre-completo-container .nombre-input,
.nombre-completo-container .apellido-input {
    border: none;
    border-radius: 0;
    padding: 0.5rem 0.75rem;
    font-size: 0.9rem;
    flex: 1;
    text-align: center;
    font-weight: 600;
    background: transparent;
}

.nombre-completo-container .nombre-input {
    border-right: 1px solid #e9ecef;
}

.nombre-completo-container .nombre-input:focus,
.nombre-completo-container .apellido-input:focus {
    outline: none;
    box-shadow: none;
    background-color: #f8f9fa;
}

.nombre-completo-container .nombre-input::placeholder,
.nombre-completo-container .apellido-input::placeholder {
    font-size: 0.8rem;
    font-weight: 400;
    color: #adb5bd;
}

/* Indicador visual sutil entre campos */
.nombre-completo-container::after {
    content: '';
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
    width: 1px;
    height: 60%;
    background: linear-gradient(to bottom, 
        transparent 0%, 
        #dee2e6 20%, 
        #dee2e6 80%, 
        transparent 100%);
    pointer-events: none;
}

/* Responsive para campos de nombre */
@media (max-width: 768px) {
    .nombre-completo-container .nombre-input,
    .nombre-completo-container .apellido-input {
        font-size: 0.85rem;
        padding: 0.4rem 0.5rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
    import { Modal, Toast, Tooltip } from 'bootstrap';
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar tooltips
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        [...tooltipTriggerList].map(tooltipTriggerEl => new Tooltip(tooltipTriggerEl));

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

        // Validar email en tiempo real
        const correoInput = document.getElementById('correo');
        if (correoInput) {
            correoInput.addEventListener('blur', function() {
                const email = this.value;
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                
                if (email && !emailRegex.test(email)) {
                    this.classList.add('is-invalid');
                    
                    // Crear o actualizar mensaje de error
                    let feedback = this.nextElementSibling;
                    if (!feedback || !feedback.classList.contains('invalid-feedback')) {
                        feedback = document.createElement('div');
                        feedback.className = 'invalid-feedback';
                        this.parentNode.appendChild(feedback);
                    }
                    feedback.textContent = 'Por favor ingrese un correo válido';
                } else {
                    this.classList.remove('is-invalid');
                }
            });
        }
    });
</script>
@endpush