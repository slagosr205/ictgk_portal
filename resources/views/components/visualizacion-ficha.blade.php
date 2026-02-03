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

// Formatear fechas
$fechaNacimiento = $infocandidatos->fecha_nacimiento ?
Date::parse($infocandidatos->fecha_nacimiento)->format('l j F Y') :
'No especificada';

$fechaRegistro = $infocandidatos->created_at ?
Date::parse($infocandidatos->created_at)->format('l j F Y - h:i A') :
'No especificada';
@endphp

<div class="card shadow-sm ficha-visualizacion-card">
  <div class="card-body">
    <div class="d-flex align-items-center mb-3">
      <i class="ri-eye-line me-2 text-primary"></i>
      <h6 class="mb-0 text-muted">Vista de Información del Colaborador</h6>
    </div>

    <div class="row g-4">
      {{-- Columna 1: Avatar y Nombre --}}
      <div class="col-lg-3 col-md-6">
        <div class="text-center perfil-section">
          {{-- Avatar --}}
          <div class="avatar-container-viz mb-3">
            @if ($esHombre)
            <img class="avatar-img-viz"
              src="{{ Storage::url('avatar.png') }}"
              alt="Avatar Masculino">
            @else
            <img class="avatar-img-viz"
              src="{{ Storage::url('mujer.png') }}"
              alt="Avatar Femenino">
            @endif

            {{-- Badge de estado --}}
            <div class="avatar-badge-viz">
              @switch($estado)
              @case('n')
              <span class="badge bg-secondary" data-bs-toggle="tooltip" title="Inactivo para ingresar a trabajar">
                <i class="ri-information-line"></i>
              </span>
              @break
              @case('s')
              <span class="badge bg-success" data-bs-toggle="tooltip" title="Activo para ingresar a trabajar">
                <i class="ri-check-line"></i>
              </span>
              @break
              @default
              <span class="badge bg-warning" data-bs-toggle="tooltip" title="Pendiente">
                <i class="ri-alert-line"></i>
              </span>
              @endswitch
            </div>
          </div>

          {{-- Nombre --}}
          <h6 class="nombre-colaborador-viz mb-2" id="nombre">
            <strong>{{ strtoupper($infocandidatos->nombre . ' ' . $infocandidatos->apellido) }}</strong>
          </h6>

          {{-- Badge de género --}}
          <span class="badge {{ $esHombre ? 'bg-primary' : 'bg-info' }} badge-genero">
            <i class="ri-genderless-line me-1"></i>
            <strong>{{ $esHombre ? 'Masculino' : 'Femenino' }}</strong>
          </span>
        </div>
      </div>

      {{-- Columna 2: Información Personal --}}
      <div class="col-lg-3 col-md-6">
        <div class="info-section">
          <div class="info-item-viz">
            <label class="info-label-viz">
              <i class="ri-id-card-line me-2"></i>
              Identidad
            </label>
            <div class="info-value-viz" id="identidad">
              <strong>{{ $infocandidatos->identidad }}</strong>
            </div>
          </div>

          <div class="info-item-viz">
            <label class="info-label-viz">
              <i class="ri-genderless-line me-2"></i>
              Género
            </label>
            <div class="info-value-viz" id="genero">
              <strong>{{ strtoupper($infocandidatos->generoM_F) }}</strong>
            </div>
          </div>
        </div>
      </div>

      {{-- Columna 3: Fechas --}}
      <div class="col-lg-3 col-md-6">
        <div class="info-section">
          <div class="info-item-viz">
            <label class="info-label-viz">
              <i class="ri-calendar-line me-2"></i>
              Fecha de Nacimiento
            </label>
            <div class="info-value-viz" id="fechaNacimiento">
              <strong>{{ $fechaNacimiento }}</strong>
            </div>
          </div>

          <div class="info-item-viz">
            <label class="info-label-viz">
              <i class="ri-time-line me-2"></i>
              Fecha de Registro
            </label>
            <div class="info-value-viz">
              <strong>{{ $fechaRegistro }}</strong>
            </div>
          </div>
        </div>
      </div>

      {{-- Columna 4: Estado Visual --}}
      <div class="col-lg-3 col-md-6">
        <div class="flex-column ">
          {{-- Estado Visual --}}
          @switch($estado)
          @case('n')
          <div class="w-50">
            <img src="{{ Storage::url('informacion.png') }}"
              alt="Inactivo"
              class="img-fluid">
            <p class="estado-visual-text mb-1">Inactivo</p>
            <small class="text-muted">Está trabajando actualmente</small>
          </div>
          @break
          @case('s')
          <div class="w-50 d-flex flex-column align-items-center text-center">
            <img src="{{ Storage::url('cheque.png') }}"
              alt="Activo"
              class="img-fluid mb-1"
              style="max-width: 60px;">

            <p class="estado-visual-text mb-0">Activo</p>
            <small class="text-muted">Para ingresar</small>
          </div>
          @break
          @default
          <div class="w-50">
            <img src="{{ Storage::url('alerta.png') }}"
              alt="Pendiente"
              class="img-fluid">
            <p class="estado-visual-text mb-1">Pendiente</p>
            <small class="text-muted">Contacte RRHH</small>
          </div>
          @endswitch
        </div>
      </div>
    </div>
  </div>
</div>

@push('styles')
<style>
  /* Card Principal - Visualización */
  .ficha-visualizacion-card {
    border: 1px solid #e0e0e0;
    border-radius: 0.5rem;
    margin-bottom: 1.5rem;
    background: linear-gradient(to bottom, #ffffff 0%, #f8f9fa 100%);
    overflow: hidden;
    /* IMPORTANTE: Evita desbordamiento */
  }

  .ficha-visualizacion-card .card-body {
    padding: 1.5rem;
  }

  /* Sección de Perfil */
  .perfil-section {
    padding: 1rem;
    background: white;
    border-radius: 0.5rem;
    border: 1px solid #e9ecef;
    overflow: hidden;
    /* IMPORTANTE: Evita desbordamiento */
  }

  /* Avatar Visualización */
  .avatar-container-viz {
    position: relative;
    display: inline-block;
    max-width: 100%;
    /* IMPORTANTE: Limita el ancho */
  }

  .avatar-img-viz {
    width: 100px;
    height: 100px;
    max-width: 100%;
    /* IMPORTANTE: Responsive */
    object-fit: cover;
    border-radius: 50%;
    border: 3px solid #dee2e6;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
  }

  .avatar-img-viz:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
  }

  .avatar-badge-viz {
    position: absolute;
    bottom: 5px;
    right: 5px;
    background: white;
    border-radius: 50%;
    padding: 3px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
  }

  .avatar-badge-viz .badge {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
  }

  /* Nombre del Colaborador */
  .nombre-colaborador-viz {
    font-size: 1.1rem;
    font-weight: 600;
    color: #212529;
    line-height: 1.4;
    word-break: break-word;
  }

  .badge-genero {
    font-size: 0.85rem;
    padding: 0.4rem 0.8rem;
    font-weight: 500;
  }

  /* Sección de Información */
  .info-section {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
  }

  .info-item-viz {
    background: white;
    padding: 1rem;
    border-radius: 0.5rem;
    border: 1px solid #e9ecef;
    transition: all 0.2s ease;
    overflow: hidden;
    /* IMPORTANTE: Evita desbordamiento de texto largo */
  }

  .info-item-viz:hover {
    border-color: #0d6efd;
    box-shadow: 0 2px 8px rgba(13, 110, 253, 0.1);
  }

  .info-label-viz {
    font-size: 0.8rem;
    font-weight: 600;
    color: #6c757d;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .info-value-viz {
    font-size: 0.95rem;
    font-weight: 500;
    color: #212529;
    padding: 0.5rem;
    background: #f8f9fa;
    border-radius: 0.375rem;
    word-break: break-word;
    overflow-wrap: break-word;
    /* IMPORTANTE: Rompe palabras largas */
  }

  /* Estado Visual - CORREGIDO */
  .estado-visual-container {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 50%;
    min-height: 200px;
    max-height: 250px;
    /* IMPORTANTE: Limita altura máxima */
    overflow: hidden;
    /* IMPORTANTE: Evita desbordamiento */
  }

  .estado-visual {
    text-align: center;
    padding: 1rem;
    /* REDUCIDO de 1.5rem */
    border-radius: 0.5rem;
    width: 100%;
    max-width: 100%;
    /* IMPORTANTE: No excede contenedor */
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
  }

  .estado-visual:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
  }

  .estado-visual-activo {
    height: 20px;
    width: 20px;
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    border: 2px solid #28a745;
  }

  .estado-visual-inactivo {
    height: 20px;
    width: 20px;
    background: linear-gradient(135deg, #e2e3e5 0%, #d6d8db 100%);
    border: 2px solid #6c757d;
  }

  .estado-visual-pendiente {
    background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
    border: 2px solid #ffc107;
  }

  .estado-visual-icon {
    width: 40px;
    /* REDUCIDO de 70px */
    height: 40px;
    /* REDUCIDO de 70px */
    max-width: 80%;
    /* IMPORTANTE: Limita al 80% del contenedor */
    max-height: 80px;
    /* IMPORTANTE: Altura máxima */
    object-fit: contain;
    /* IMPORTANTE: Mantiene proporciones */
    margin-bottom: 0.5rem;
    /* REDUCIDO de 0.75rem */
    filter: drop-shadow(0 2px 6px rgba(0, 0, 0, 0.15));
  }

  .estado-visual-icon-text {
    font-size: 60px;
    /* REDUCIDO de 70px */
    color: #ffc107;
    margin-bottom: 0.5rem;
    /* REDUCIDO de 0.75rem */
    display: block;
    line-height: 1;
  }

  .estado-visual-text {
    font-size: 1rem;
    /* REDUCIDO de 1.2rem */
    font-weight: 600;
    color: #212529;
    margin-bottom: 0.25rem;
  }

  .estado-visual small {
    font-size: 0.8rem;
    display: block;
    margin-top: 0.25rem;
  }

  /* Responsive */
  @media (max-width: 992px) {
    .estado-visual-container {
      min-height: auto;
      max-height: none;
      margin-top: 1rem;
    }

    .avatar-img-viz {
      width: 80px;
      height: 80px;
    }

    .nombre-colaborador-viz {
      font-size: 1rem;
    }

    .estado-visual-icon {
      width: 50px;
      height: 50px;
    }

    .estado-visual-icon-text {
      font-size: 50px;
    }
  }

  @media (max-width: 768px) {
    .ficha-visualizacion-card .card-body {
      padding: 1rem;
    }

    .perfil-section {
      padding: 0.75rem;
    }

    .info-section {
      gap: 1rem;
    }

    .info-item-viz {
      padding: 0.75rem;
    }

    .avatar-img-viz {
      width: 40px;
      height: 40px;
    }

    .estado-visual-icon {
      width: 45px;
      height: 45px;
      max-height: 60px;
    }

    .estado-visual-icon-text {
      font-size: 45px;
    }

    .estado-visual {
      padding: 0.75rem;
    }

    .estado-visual-text {
      font-size: 0.9rem;
    }
  }

  /* Animaciones */
  @keyframes slideIn {
    from {
      opacity: 0;
      transform: translateX(-20px);
    }

    to {
      opacity: 1;
      transform: translateX(0);
    }
  }

  .ficha-visualizacion-card {
    animation: slideIn 0.4s ease-out;
  }

  /* Print Styles */
  @media print {
    .ficha-visualizacion-card {
      box-shadow: none;
      border: 1px solid #000;
    }

    .estado-visual {
      border: 1px solid #000;
      background: white !important;
    }

    .estado-visual-icon {
      max-width: 60px;
      max-height: 60px;
    }
  }
</style>
@endpush

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tooltips
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

    // Efecto de hover en las tarjetas de información
    const infoItems = document.querySelectorAll('.info-item-viz');
    infoItems.forEach(item => {
      item.addEventListener('mouseenter', function() {
        this.style.transform = 'translateX(5px)';
      });

      item.addEventListener('mouseleave', function() {
        this.style.transform = 'translateX(0)';
      });
    });
  });
</script>
@endpush