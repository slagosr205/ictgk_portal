@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary-dark text-white">
                    <div class="d-flex align-items-center">
                        <i class="ri-file-upload-line fs-3 me-3"></i>
                        <div>
                            <h4 class="mb-0 text-white">Validador de Importación Masiva</h4>
                            <small class="opacity-75 text-white">Sistema de validación de archivos CSV</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tarjeta de Carga -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <!-- Alert de Instrucciones -->
                    <div class="alert-custom alert-info-custom mb-4">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <i class="ri-information-line fs-3 text-info"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="alert-heading mb-2 text-dark">
                                    <i class="ri-lightbulb-line me-2"></i>Instrucciones de uso
                                </h5>
                                <ol class="mb-0 ps-3 text-muted">
                                    <li class="mb-2">Seleccione la cantidad de filas que necesita en la plantilla</li>
                                    <li class="mb-2">Descargue la plantilla Excel haciendo clic en el botón verde</li>
                                    <li class="mb-2">Complete los datos en el archivo descargado</li>
                                    <li class="mb-2">Guarde como CSV: <code>Archivo → Guardar como → CSV (delimitado por comas)</code></li>
                                    <li class="mb-2">Suba el archivo CSV usando el selector de archivos</li>
                                    <li>Confirme la importación de los registros válidos</li>
                                </ol>
                            </div>
                        </div>
                    </div>

                    <!-- Formulario de Carga -->
                    <form id="formValidarArchivo" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-3">
                            <!-- Selector de Archivo -->
                            <div class="col-lg-8">
                                <label for="archivo" class="form-label fw-semibold text-dark">
                                    <i class="ri-file-line me-2"></i>Seleccione el archivo CSV
                                </label>
                                <div class="custom-file-input">
                                    <span class="file-icon">
                                        <i class="ri-upload-cloud-line"></i>
                                    </span>
                                    <input
                                        type="file"
                                        class="form-control"
                                        id="archivo"
                                        name="archivo"
                                        accept=".csv,.txt"
                                        required>
                                </div>
                                <div class="form-text mt-2">
                                    <i class="ri-information-line me-1"></i>
                                    Formatos: <strong>CSV, TXT</strong> | Tamaño máximo: <strong>10MB</strong>
                                </div>
                            </div>

                            <!-- Botón Validar -->
                            <div class="col-lg-4">
                                <label class="form-label d-block fw-semibold">&nbsp;</label>
                                <button type="submit" class="btn-custom btn-primary-custom w-100" id="btnValidar">
                                    <i class="ri-check-line me-2"></i>Validar Archivo
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Separator -->
                    <div class="separator-custom my-4"></div>

                    <!-- Sección de Descarga de Plantilla -->
                    <div class="row">
                        <div class="col-12">
                            <div class="download-card">
                                <div class="download-card-header">
                                    <i class="ri-download-cloud-line me-2"></i>
                                    Descargar Plantilla Excel
                                </div>
                                <div class="download-card-body">
                                    <form id="formDescargarPlantilla">
                                        <div class="row g-3">
                                            <!-- Selector de Cantidad de Filas -->
                                            <div class="col-md-4">
                                                <label for="cantidadFilas" class="form-label fw-semibold text-dark">
                                                    <i class="ri-file-list-3-line me-2"></i>
                                                    Cantidad de filas
                                                </label>
                                                <select class="form-select form-select-lg select-custom" id="cantidadFilas" name="filas">
                                                    <option value="10" selected>10 filas</option>
                                                    <option value="25">25 filas</option>
                                                    <option value="50">50 filas</option>
                                                    <option value="100">100 filas</option>
                                                    <option value="200">200 filas</option>
                                                    <option value="500">500 filas</option>
                                                    <option value="1000">1,000 filas</option>
                                                </select>
                                            </div>

                                            <!-- Botón de Descarga -->
                                            <div class="col-md-8">
                                                <label class="form-label fw-semibold text-dark d-block">
                                                    <i class="ri-arrow-down-line me-2"></i>
                                                    Acción
                                                </label>
                                                <button type="submit" class="btn-custom btn-success-custom w-100" id="btnDescargarPlantilla">
                                                    <i class="ri-download-line me-2"></i>
                                                    Generar y Descargar Plantilla
                                                </button>
                                            </div>
                                        </div>
                                    </form>

                                    <!-- Info adicional -->
                                    <div class="download-info mt-3">
                                        <i class="ri-information-line me-2"></i>
                                        La plantilla incluye dropdowns, validaciones y calendario para fechas
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información Adicional -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="info-card">
                                <h6 class="info-card-title">
                                    <i class="ri-file-list-line me-2"></i>Campos incluidos en la plantilla:
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <ul class="info-list">
                                            <li>
                                                <i class="ri-checkbox-circle-line"></i>
                                                <strong>identidad</strong> - 0000-0000-00000
                                            </li>
                                            <li>
                                                <i class="ri-checkbox-circle-line"></i>
                                                <strong>empresa</strong> - Se llena automáticamente
                                            </li>
                                            <li>
                                                <i class="ri-checkbox-circle-line"></i>
                                                <strong>fechaIngreso</strong> - Con calendario
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-md-4">
                                        <ul class="info-list">
                                            <li>
                                                <i class="ri-checkbox-circle-line"></i>
                                                <strong>puesto</strong> - Dropdown con puestos
                                            </li>
                                            <li>
                                                <i class="ri-checkbox-circle-line"></i>
                                                <strong>área</strong> - Dropdown de áreas
                                            </li>
                                            <li>
                                                <i class="ri-checkbox-circle-line"></i>
                                                <strong>nombre, apellido</strong>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-md-4">
                                        <ul class="info-list">
                                            <li>
                                                <i class="ri-checkbox-circle-line"></i>
                                                <strong>género</strong> - M/F (dropdown)
                                            </li>
                                            <li>
                                                <i class="ri-checkbox-circle-line"></i>
                                                <strong>fecha_nacimiento</strong> - Con calendario
                                            </li>
                                            <li>
                                                <i class="ri-checkbox-circle-line"></i>
                                                <strong>recontrataria</strong> - s/n
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Loader -->
<div class="modal fade" id="loaderModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content loader-modal-content">
            <div class="modal-body text-center p-5">
                <div class="mb-4">
                    <div class="spinner-custom">
                        <div class="spinner-border text-success" role="status" style="width: 4rem; height: 4rem;">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                    </div>
                </div>
                <h5 class="mb-3 text-dark">
                    <i class="ri-file-excel-2-line text-success me-2"></i>
                    Generando Plantilla Excel
                </h5>
                <p class="text-muted mb-0">
                    Por favor espere mientras se genera su plantilla con <strong id="filasCount" class="text-success">10</strong> filas...
                </p>
                <div class="progress-custom mt-4">
                    <div class="progress-bar-custom"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Incluir Modales -->
<x-modal-validacion />
<x-modal-edicion-registro />

@endsection

@push('styles')
<style>
  
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const formDescargar = document.getElementById('formDescargarPlantilla');
  
    const cantidadFilas = document.getElementById('cantidadFilas');
    const filasCount = document.getElementById('filasCount');

    // Actualizar contador
    cantidadFilas.addEventListener('change', function() {
        filasCount.textContent = this.value;
    });

    if (formDescargar) {
        formDescargar.addEventListener('submit', function(e) {
            e.preventDefault();

            const filas = cantidadFilas.value;
            filasCount.textContent = filas;

            console.log('Descargando plantilla con', filas, 'filas');

            // Mostrar loader
            loaderModal.show();

            // Crear iframe oculto para descarga
            const iframe = document.createElement('iframe');
            iframe.style.display = 'none';
            iframe.src = `{{ route('validador.plantilla') }}?filas=${filas}`;
            document.body.appendChild(iframe);

            // Ocultar loader después de 3 segundos
            setTimeout(() => {
                loaderModal.hide();
                document.body.removeChild(iframe);
                
                Swal.fire({
                    icon: 'success',
                    title: '¡Descarga Iniciada!',
                    html: `Plantilla con <strong>${filas} filas</strong> en descarga.<br>Revise su carpeta de descargas.`,
                    timer: 3000,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timerProgressBar: true
                });
            }, 3000);
        });
    }
});
</script>

{{-- Script de validación --}}
@vite(['resources/js/validador-importacion.js'])
@endpush