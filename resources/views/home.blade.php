@extends('layouts.app')

@section('content')
@guest
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <img src="{{Storage::url('Altialogoblanco.svg')}}" alt="" class="img-fluid">
        </div>
    </div>
</div>
@else

{{-- Sección mejorada --}}
<div class="container-fluid">
    {{-- Sección del Logo --}}


    {{-- Sección de Búsqueda y Acciones --}}
    <div class="row g-3 align-items-end ">
        <div class="col-12">
            <div class="card shadow-sm search-card">
                <div class="card-body">
                    <div class="row g-3 align-items-end">

                        {{-- Búsqueda por DNI --}}
                        <div class="col-lg-4 col-md-6 ">
                            <label for="dni" class="dni-label">
                                <i class="ri-search-line"></i>
                                Buscar Colaborador por Identidad
                            </label>

                            <div class="dni-box">
                                <input type="text"
                                    id="dni"
                                    class="dni-input"
                                    placeholder="0000-0000-00000"
                                    maxlength="15">

                                <div class="dni-side-icon">
                                    <i class="ri-id-card-line"></i>
                                </div>

                                <button type="button" id="btndni" class="dni-btn">
                                    <i class="ri-search-2-line"></i>
                                    Buscar
                                </button>
                            </div>

                            <small class="dni-help">
                                Ingrese el número de identidad del colaborador
                            </small>
                        </div>



                        @foreach ($perfilUsers as $pu)
                        @if ($pu->ingreso === 1)

                        {{-- Importar --}}
                        <div class="col-lg-4 col-md-6 d-flex align-items-end mb-2">
                            <a href="{{ route('validador.index') }}"
                                class="btn btn-info btn-action w-100">
                                <i class="ri-file-upload-line me-2"></i>
                                Importar Ingresos
                            </a>
                        </div>

                        {{-- Descargar --}}
                        <div class="col-lg-4 col-md-6 d-flex align-items-end mb-2">
                            <a href="{{ route('egresos.index') }}"
                                class="btn btn-success btn-action w-100">
                                <i class="ri-file-excel-2-fill me-2"></i>
                                Egresos
                            </a>
                        </div>

                        @break
                        @endif
                        @endforeach

                    </div>
                </div>
            </div>
        </div>
    </div>




    {{-- Sección de Resultados --}}
    <div class="row mt-4">
        <div class="col-12">
            {{-- Información individual del colaborador --}}
            <div id="fichapersonal" class="resultado-container"></div>

            {{-- Estado de importación --}}
            <div id="importacionPersonal" class="resultado-container"></div>
        </div>
    </div>
</div>

<br>

<x-modal-registro />
<x-modal-ficha-personal />

@if (session('mensaje'))
@switch(session('icon'))
@case('success')
<p id="{{session('icon')}}" hidden>{{session('mensaje')}}</p>
@break
@case('warning')
<p id="{{session('icon')}}" hidden>{{session('mensaje')}}</p>
@break
@default
<p id="{{session('icon')}}" hidden> {{session('mensaje')}}</p>
@endswitch

<div class="alert alert-success alert-dismissible fade show" role="alert">
    <strong>{{session('mensaje')}}</strong>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if (session('msjIngreso') || session('successmail') || session('errorEmail'))
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999">
    @if (session('msjIngreso'))
    <div class="toast align-items-center text-bg-success border-0 show" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="5000">
        <div class="d-flex">
            <div class="toast-body">
                <i class="ri-checkbox-circle-line me-2"></i>
                <strong>{{session('msjIngreso')}}</strong>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
    @endif

    @if (session('successmail'))
    <div class="toast align-items-center text-bg-success border-0 show" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="5000">
        <div class="d-flex">
            <div class="toast-body">
                <i class="ri-mail-check-line me-2"></i>
                <strong>{{session('successmail')}}</strong>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
    @endif

    @if (session('errorEmail'))
    <div class="toast align-items-center text-bg-danger border-0 show" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="5000">
        <div class="d-flex">
            <div class="toast-body">
                <i class="ri-error-warning-line me-2"></i>
                <strong>{{session('errorEmail')}}</strong>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar todos los toasts
        const toastElList = document.querySelectorAll('.toast');
        const toastList = [...toastElList].map(toastEl => {
            const toast = new bootstrap.Toast(toastEl, {
                autohide: true,
                delay: 5000
            });
            toast.show();
            return toast;
        });
    });
</script>
@endpush
@endif

@endguest
@endsection

@push('styles')
<style>
  


    /* Logo Header */
    .logo-container {
        padding: 2rem 0;
        margin-bottom: 1rem;
    }

    .logo-header {
        max-width: 300px;
        height: auto;
        filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
        transition: transform 0.3s ease;
    }

    .logo-header:hover {
        transform: scale(1.05);
    }

    /* Card de Búsqueda */
    .search-card {
        border: 1px solid #e0e0e0;
        border-radius: 0.5rem;
        background: linear-gradient(to bottom, #ffffff 0%, #f8f9fa 100%);
    }

    .search-card .card-body {
        padding: 1.5rem;
    }

    /* Input Group Mejorado */
    .input-group .input-group-text {
        border: 1px solid #ced4da;
        background-color: #f8f9fa;
        color: #6c757d;
    }

    .input-group .form-control {
        border: 1px solid #ced4da;
    }

    .input-group .form-control:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
    }

    .input-group .btn-primary {
        background-color: #0d6efd;
        border-color: #0d6efd;
        font-weight: 500;
        padding: 0.5rem 1.5rem;
    }

    .input-group .btn-primary:hover {
        background-color: #0b5ed7;
        border-color: #0a58ca;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(13, 110, 253, 0.2);
    }

    /* Botones de Acción */
    .btn-action {
        padding: 0.75rem 1.5rem;
        font-weight: 500;
        border-radius: 0.375rem;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .btn-action i {
        font-size: 1.1rem;
    }

    .btn-info.btn-action {
        background-color: #0dcaf0;
        border-color: #0dcaf0;
        color: #000;
    }

    .btn-info.btn-action:hover {
        background-color: #31d2f2;
        border-color: #25cff2;
        color: #000;
    }

    .btn-success.btn-action {
        background-color: #198754;
        border-color: #198754;
    }

    .btn-success.btn-action:hover {
        background-color: #157347;
        border-color: #146c43;
    }

    /* Contenedor de Resultados */
    .resultado-container {
        min-height: 100px;
        margin-top: 1rem;
    }

    .resultado-container:empty {
        display: none;
    }

    /* Labels */
    .form-label {
        color: #495057;
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
    }

    .form-text {
        font-size: 0.85rem;
        margin-top: 0.25rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .logo-header {
            max-width: 200px;
        }

        .logo-container {
            padding: 1rem 0;
        }

        .search-card .card-body {
            padding: 1rem;
        }

        .btn-action {
            padding: 0.6rem 1rem;
            font-size: 0.9rem;
        }

        .input-group .form-control,
        .input-group .btn {
            font-size: 16px;
        }
    }

    @media (max-width: 576px) {
        .input-group {
            flex-direction: column;
        }

        .input-group .btn {
            width: 100%;
            margin-top: 0.5rem;
            border-radius: 0.375rem !important;
        }

        .input-group .input-group-text {
            border-radius: 0.375rem 0 0 0.375rem !important;
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

    .resultado-container>* {
        animation: fadeIn 0.3s ease-in-out;
    }

    .form-control:hover {
        border-color: #adb5bd;
    }

    .shadow-sm {
        box-shadow: 0 0.125rem 0.5rem rgba(0, 0, 0, 0.075) !important;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Formatear DNI automáticamente
        const dniInput = document.getElementById('dni');

        if (dniInput) {
            dniInput.addEventListener('input', function(e) {
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

            // Permitir búsqueda con Enter
            dniInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    document.getElementById('btndni').click();
                }
            });
        }

        // Validación del botón de búsqueda
        const btnBuscar = document.getElementById('btndni');
        if (btnBuscar) {
            btnBuscar.addEventListener('click', function() {
                const dni = dniInput.value.trim();

                if (!dni) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Campo vacío',
                        text: 'Por favor ingrese un número de identidad',
                        confirmButtonColor: '#0d6efd'
                    });
                    dniInput.focus();
                    return false;
                }

                // Validar formato básico
                if (dni.length < 13) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Formato incorrecto',
                        text: 'El número de identidad debe tener el formato: 0000-0000-00000',
                        confirmButtonColor: '#0d6efd'
                    });
                    dniInput.focus();
                    return false;
                }
            });
        }
    });
</script>
@endpush