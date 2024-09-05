@extends('layouts.app')

@section('empresas')

<div class="container" id="dtempresas">
<div class="row">
    <div class="col-md-2">
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#nuevaempresa">
            <i class="ri-sticky-note-add-fill"></i>
            nuevo
        </button>
    </div>
</div>
<br>
<div class="row" >
    <div class="col-md-12">
    <table id="tbempresas" class="table table-striped ">
        <thead>
            <tr>
                <th>ID</th>
                <th>Empresa</th>
                <th>Telefonos</th>
                <th>Contactos</th>
                <th>correo</th>
                <th>estado</th>
                <th>accion</th>
                
            </tr>
        </thead>
        <tbody>
            @foreach ($empresas as $em)
            @php
                $id=$em->id;
            @endphp
            <tr data-id="{{$em->id}}">
                <td data-campo="id">{{$em->id}}</td>
                <td data-campo="nombre">{{$em->nombre}}</td>
                <td data-campo="telefonos">{{$em->telefonos}}</td>
                <td data-campo="contacto">{{$em->contacto}}</td>
               {{---<td data-campo="pin">{{$em->pin}}</td>---}}
                {{---<td data-campo="puesto">{{$em->puesto}}</td>---}}
                <td data-campo="correo">{{$em->correo}}</td>
                @if ($em->estado==='a')
                <td>
                    
                    <div class="form-check form-switch">
                        <input class="form-check-input chkactivoEmpresa" type="checkbox" role="switch" id="flexSwitchCheckChecked-{{$em->id}} " checked>
                        <label class="form-check-label" for="flexSwitchCheckChecked">{{'activo'}}</label>
                      </div>
                </td>
                
                @else
                <td>
                    <div class="form-check form-switch">
                        <input class="form-check-input chkactivoEmpresa" type="checkbox" role="switch" id="flexSwitchCheckChecked-{{$em->id}}" >
                        <label class="form-check-label" for="flexSwitchCheckChecked">{{'inactivo'}}</label>
                      </div>
                </td>
                
                @endif
                <td>
                    <button type="button" class="btn btn-warning btn-consulta" data-id="{{$em->id}}" data-bs-toggle="modal" data-bs-target="#modificarempresa" >Modificar</button>
                </td>
            </tr>
            @endforeach
            
        </tbody>
    </table>
</div>
</div>







<!-- Modal para crear -->
<div class="modal fade" id="nuevaempresa" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="staticBackdropLabel">Agregar Empresas</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form  method="post" id="insertCompany">
        
            <div class="row">
                <div class="col-md-4">
                    <label for="nombre">Nombre:</label>
                    <input type="text" name="nombre" id="nombre" class="form-control border">
                </div>
                <div class="col-md-4">
                    <label for="telefono">Telefono:</label>
                    <input type="text" name="telefonos" id="telefonos" class="form-control border">
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <label for="correo">Correo:</label>
                    <input type="email" name="correo" id="correo" class="form-control border">
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <label for="contacto">Contacto:</label>
                    <input type="text" name="contacto" id="contacto" class="form-control border">
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <label for="logo">Direccion</label>
                   <input type="text" name="direccion" id="direccion" class="form-control border">
                </div>
                <div class="col-md-4">
                    <label for="logo">Logo de Empresa</label>
                   <input type="file" name="logo" id="logo" class="form-control border">
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-4">
                    <button type="submit" class="btn btn-success " data-bs-dismiss="modal" aria-label="Close" id="enviarDatosEmpresa" >
                        Grabar
                    </button>
                </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          
         
        </div>
      </div>
    </div>
  </div>

  
<!-- Modal para Modificar -->
<div class="modal fade" id="modificarempresa" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true" >
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="staticBackdropLabel">Actualizacion de Empresas</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form  method="post" id="updateCompany">
            <input type="hidden" name="id" id="id_empresa">
            <div class="row">
                <div class="col-md-4">
                    <label for="nombre">Nombre:</label>
                    <input type="text" name="nombre" id="modnombre" class="form-control">
                </div>
                <div class="col-md-4">
                    <label for="telefono">Telefono:</label>
                    <input type="text" name="telefonos" id="modtelefonos" class="form-control">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <label for="correo">Correo:</label>
                    <input type="email" name="correo" id="modcorreo" class="form-control">
                </div>
                <div class="col-md-6">
                    <label for="estado">Estado</label>
                    <p id="estado"></p>
                </div>
                
            </div>
            <div class="row">
                <div class="col-md-8">
                    <label for="contacto">Contacto:</label>
                    <input type="text" name="contacto" id="modcontacto" class="form-control">
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <label for="logo">Direccion</label>
                   <input type="text" name="direccion" id="moddireccion" class="form-control">
                </div>
                <div class="col-md-4">
                    <label for="logo">Logo de Empresa</label>
                   <input type="file" name="logo" id="modlogo" class="form-control">
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-4">
                    <button type="submit" class="btn btn-success " data-bs-dismiss="modal" aria-label="Close" id="ActualizarDatosEmpresa" >
                        Actualizar
                    </button>
                </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          
         
        </div>
      </div>
    </div>
  </div>

  

  @if ($errors->any())
      <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{$error}}</li>
            @endforeach
        </ul>
      </div>
  @endif
</div>

@section('empresasjs')
    @vite(['resources/js/empresas.ajax.js'])
@stop
    
@endsection