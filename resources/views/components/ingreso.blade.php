<div class="container-fluid py-4">
  @php
  // $perfil = auth()->user()->perfil ?? null;
  $informacionlaboral = $informacionlaboral ?? [];
  $empresas = $empresas ?? [];
  $puestos = $puestos ?? [];
  $candidato = $candidato ?? null;

  $esAdmin = collect($perfil)
  ->contains(fn ($p) => ($p['perfilesdescrip'] ?? null) === 'admin');


  // Obtener identidad de forma segura
  $identidad = null;
  if (!empty($informacionlaboral) && isset($informacionlaboral[0]['identidad'])) {
  $identidad = $informacionlaboral[0]['identidad'];
  } elseif ($candidato && isset($candidato->identidad)) {
  $identidad = $candidato->identidad;
  }
  @endphp

  <div class="card shadow-sm">
    <div class="card-header bg-light border-bottom">
      <h5 class="mb-0 text-dark">
        <i class="ri-user-add-line me-2"></i>
        Registro de Ingreso de Colaborador
      </h5>
    </div>

    <div class="card-body">
      <form action="{{ route('hacerIgresos') }}" method="POST" id="formIngreso">
        @csrf

        {{-- Campo oculto de identidad --}}
        @if ($identidad)
        <input type="hidden" id="identidad" name="identidad" value="{{ $identidad }}">
        @endif

        {{-- Información de la empresa --}}
        <div class="row mb-4">
          <div class="col-12">
            @if (!$esAdmin)
            {{-- Usuario NO admin: mostrar empresa fija --}}
            <div class="alert alert-light border d-flex align-items-center" role="alert">
              <i class="ri-building-line me-2 fs-4 text-secondary"></i>
              <div>
                <strong>Empresa Actual:</strong>
                @foreach ($empresas as $em)
                @if (isset($em->id) && $em->id === auth()->user()->empresa_id)
                <input type="hidden" id="id_empresa" name="id_empresa" value="{{ $em->id }}">
                <span class="ms-2">{{ $em->nombre ?? 'No especificada' }}</span>
                @break
                @endif
                @endforeach
              </div>
            </div>
            @endif
          </div>
        </div>

        {{-- Sección: Información del Ingreso --}}
        <div class="row g-3 mb-4">
          <div class="col-12">
            <h6 class="text-secondary border-bottom pb-2 mb-3">
              <i class="ri-file-list-3-line me-2"></i>
              Información del Ingreso
            </h6>
          </div>

          {{-- Empresa (solo para admin) --}}
          @if ($esAdmin)
          <div class="col-md-6 col-lg-3">
            <label for="id_empresa" class="form-label">
              <i class="ri-building-line me-1 text-muted"></i>
              Empresa <span class="text-danger">*</span>
            </label>
            <select name="id_empresa"
              id="id_empresa"
              class="form-select border"
              required>
              <option value="">Seleccione...</option>
              @foreach ($empresas as $em)
              <option value="{{ $em->id }}">{{ $em->nombre }}</option>
              @endforeach
            </select>
            <div class="invalid-feedback">
              Por favor seleccione una empresa
            </div>
          </div>
          @endif

          {{-- Fecha de ingreso --}}
          <div class="col-md-6 col-lg-3">
            <label for="fecha_ingreso" class="form-label">
              <i class="ri-calendar-line me-1 text-muted"></i>
              Fecha de Ingreso <span class="text-danger">*</span>
            </label>
            <input type="date"
              name="fecha_ingreso"
              id="fecha_ingreso"
              class="form-control border"
              max="{{ date('Y-m-d') }}"
              required>
            <div class="invalid-feedback">
              Por favor ingrese la fecha de ingreso
            </div>
          </div>

          {{-- Área --}}
          <div class="col-md-6 col-lg-3">
            <label for="area" class="form-label">
              <i class="ri-group-line me-1 text-muted"></i>
              Área <span class="text-danger">*</span>
            </label>
            <select name="area"
              id="area"
              class="form-select"
              required>
              <option value="">Seleccione...</option>
              <option value="operativa">Operativa</option>
              <option value="administrativa">Administrativa</option>
            </select>
            <div class="invalid-feedback">
              Por favor seleccione el área
            </div>
          </div>

          {{-- Puesto (si no es perfil 1) --}}
          @if (auth()->user()->perfil_id !== 1)
          <div class="col-md-6 col-lg-3">
            <label for="id_puesto" class="form-label">
              <i class="ri-user-star-line me-1 text-muted"></i>
              Puesto <span class="text-danger">*</span>
            </label>
            <select name="id_puesto"
              id="id_puesto"
              class="form-select"
              required>
              <option value="">Seleccione...</option>
              @foreach ($puestos as $puesto)
              <option value="{{ $puesto->id }}">
                {{ $puesto->nombrepuesto ?? $puesto->nombre }}
              </option>
              @endforeach
            </select>

          </div>
          @else
          {{-- Puesto (si es perfil 1) --}}
           <div class="col-md-6 col-lg-3">
            <div class="invalid-feedback">
              Por favor seleccione el puesto
            </div>
            <div id="positions-selected" class="form-text"></div>
           </div>
        </div>

          @endif

         

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
              placeholder="Ingrese cualquier información adicional relevante sobre el ingreso del colaborador..."
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
                class="btn btn-primary"
                name="btngrabar">
                <i class="ri-save-fill me-2"></i>
                Registrar Ingreso
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
      font-size: 16px;
      /* Prevenir zoom en iOS */
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
  .ri-building-line,
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
    const form = document.getElementById('formIngreso');
    const comentarios = document.getElementById('comentarios');
    const caracteresRestantes = document.getElementById('caracteresRestantes');

    // Evitar duplicación de alertas - solo procesar una vez
    if (window.ingresoFormProcessed) return;
    window.ingresoFormProcessed = true;

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
          title: '¿Confirmar ingreso?',
          text: 'Esta acción registrará el ingreso del colaborador',
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
    const fechaIngreso = document.getElementById('fecha_ingreso');
    if (fechaIngreso) {
      const hoy = new Date().toISOString().split('T')[0];
      fechaIngreso.setAttribute('max', hoy);
    }

    // Validación de empresa para admin
    const empresaSelect = document.getElementById('id_empresa');
    if (empresaSelect) {
      empresaSelect.addEventListener('change', function() {
        if (this.value) {
          Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: `Empresa seleccionada: ${this.options[this.selectedIndex].text}`,
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true
          });
        }
      });
    }

    // Feedback visual al seleccionar puesto
    const puestoSelect = document.getElementById('id_puesto');
    const positionsSelected = document.getElementById('positions-selected');

    if (puestoSelect && positionsSelected) {
      puestoSelect.addEventListener('change', function() {
        if (this.value) {
          const puestoNombre = this.options[this.selectedIndex].text;
          positionsSelected.innerHTML = `
                        <small class="text-success">
                            <i class="ri-check-line"></i> 
                            Puesto seleccionado: <strong>${puestoNombre}</strong>
                        </small>
                    `;
        } else {
          positionsSelected.innerHTML = '';
        }
      });
    }

    // Validación de área seleccionada
    const areaSelect = document.getElementById('area');
    if (areaSelect) {
      areaSelect.addEventListener('change', function() {
        const area = this.value;
        if (area === 'operativa') {
          console.log('Área operativa seleccionada');
        } else if (area === 'administrativa') {
          console.log('Área administrativa seleccionada');
        }
      });
    }
  });

  // Función para limpiar el formulario
  function limpiarFormulario() {
    const form = document.getElementById('formIngreso');

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

        // Limpiar feedback del puesto
        const positionsSelected = document.getElementById('positions-selected');
        if (positionsSelected) {
          positionsSelected.innerHTML = '';
        }

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