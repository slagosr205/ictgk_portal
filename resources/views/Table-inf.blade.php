@extends('layouts.app')

@section('table')

@if (session('datos'))
<div class="container">
    
    <div class="row">
        <div class="col-md-6">
            <div class="alert alert-warning alert-dimissible fade show" role="alert">
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                {{-- Mostrar los datos del CSV --}}
                <table class="table">
                    <thead>
                        <tr><th>Identidad</th><th>Nombre</th><th>Estado</th></tr>
                    </thead>
                        <tbody>
                            @foreach (session('datos') as $registros)
                            
                                {{-- Mostrar cada fila --}}
                            
                                <tr>
                                    <td>{{$registros['datos']['identidad']}}</td>
                                    <td>{{$registros['datos']['nombre'].' '.$registros['datos']['apellido']}}</td>
                                    @if ($registros['datos']['estado']==='error')
                                    <td>
                                        <button type="button" class="btn btn-secondary mensajeregistros" 
                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                            data-bs-custom-class="custom-tooltip"
                                            data-bs-title="Ya existe registro en la base de datos!">
                                            <i class="ri-error-warning-fill"></i>
                                        </button>
                                    </td>
                                        
                                    @else
                                        <td>
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                data-bs-custom-class="custom-tooltip"
                                                data-bs-title="This top tooltip is themed via CSS variables.">
                                                <i class="ri-check-double-line"></i>
                                            </button>
                                        </td>
                                    @endif
                                    
                                </tr>
                                
                            @endforeach
                        </tbody>
                    </table>
                    
            </div>
        </div>
    </div>
</div>

@endif
<x-dmtables :candidatos="$data"/>


@if (session('mensaje'))
    <div class="alert alert-success alert-dimissible fade show" role="alert">
        {{session('mensaje')}}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@endsection
