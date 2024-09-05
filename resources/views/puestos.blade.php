@extends('layouts.app')

@section('puestos')
<div class="container">
<div class="row">
    <div class="col-md-4">
        <button class="btn btn-success mb-2" data-bs-toggle="modal" data-bs-target="#modalagrPuesto">Crear Puesto</button>
    </div>
    <div class="col-md-4">
        @if (session('successPositions'))
            <div id="successPositions"></div>
            @section('puestosjs')
                @vite(['resources/js/libpuestos/alertpuestos.js'])
            @stop
        @endif
    </div>
</div>
<div class="row">
<div class="col-md-12">
<table id="tbpuestos" class="table table-striped display" style="width:100%">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Departamento</th>
            <th>Creado</th>
            <th>Ultima Actualizacion</th>
            <th>Accion</th>
        </tr>
    </thead>
    {{------}}
    <tbody>
        @foreach ($puestos as $ps)
            <tr>
                <td>{{$ps->id}}</td>
                <td>{{$ps->nombrepuesto}}</td>
                <td>{{$ps->nombredepartamento}}</td>
                <td>{{$ps->created_at}}</td>
                <td>{{$ps->updated_at}}</td>
                <td><button class="btn btn-success btnActPuestos" id="{{'btnActPuestos'}}" value="{{$ps->id}}" data-bs-toggle="modal" data-bs-target="#modalmodificarPuestos">Actualizar</button></td>
            </tr>
        @endforeach
    </tbody>
</table>
</div>
</div>
<div class="row">
   
    <div class="col-md-4">
       
        @if ($errors->has('errorsPositions'))
            <div id="errorsPositions" hidden>
                <div class="alert alert-danger"  >
                    
                    <ul >
                        @foreach($errors->get('errorsPositions') as $error)
                            <li><strong>{{ $error }}</strong></li>
                            @section('puestosjs')
                                @vite(['resources/js/libpuestos/modalpuestos.js'])
                            @stop
                        @endforeach
                    </ul>
                </div>
            </div>
            
                
        @endif
    </div>
</div>
</div>

<x-puestos.created-modal />
<x-puestos.puestos-add :departament="$departamentos"/>
<div class="row">
    <div class="col-md-6">
        @if (session('updatedPositionserror'))
            <div class="alert alert-danger" id="updatedPositionserror">
                <span>{{session('updatedPositionserror')}}</span>
            </div>
            
        @endif
        
        @if (session('updatedPositions'))
            <div class="alert alert-success" id="updatedPositions">
                <span>{{session('updatedPositions')}}</span>
            </div>
            
        @endif
    
    </div>
</div>





@endsection