@extends('layouts.app')

@section('departamentos')

<div class="container">
    <div class="row">
        <div class="col-md-4">
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#nuevodepartamento">
                <i class="ri-sticky-note-add-fill"></i>
                nuevo
            </button>
        </div>
    </div>

<br>
{{--DataTable--}}
<div class="row">
    <div class="col-md-12">
        <table id="tbdepartamentos" class="table table-striped ">
            <thead>
                <tr>
                    <th>ID Departamento</th>
                    <th>Departamento</th>
                    <th>Empresa</th>
                    <th>Fecha de Creación</th>
                    <th>Fecha de Actualización</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($departamentos as $item)
                    <tr>
                        <td>{{$item->id}}</td>
                        <td>{{$item->nombredepartamento}}</td>
                        <td>{{$item->empresa_nombre}}</td>
                        <td>{{\Carbon\Carbon::parse($item->created_at)->isoFormat('LL LTS')}}</td>
                        <td>{{\Carbon\Carbon::parse($item->updated_at)->isoFormat('LL LTS')}}</td>
                        <td><button class="btn btn-info btnInfoDepto" id="{{$item->id}}">Actualizar</button></td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                
            </tfoot>
        </table>
    </div>
</div>
<!-- Modal para crear -->
<div class="modal fade" id="nuevodepartamento" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="staticBackdropLabel">Agregar Departamentos</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form  id="insertDepartamento">
            
            <div class="row">
                <div class="col-md-4">
                    <label for="nombredepartamento">Nombre:</label>
                    <input type="text" name="nombredepartamento" id="nombredepartamento" class="form-control">
                </div>
                <div class="col-md-6">
                    <label for="telefono">Empresa:</label>
                    
                  
                       <input class="form-control" type="text" name="empresa_id" id="{{$empresas->id}}" value="{{$empresas->nombre}}" placeholder="{{$empresas->nombre}}" readonly  >
                   
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-4">
                    <button type="submit" class="btn btn-success">Grabar</button>
                </div>
            </div>
           
          </form>
        </div>
        <div class="modal-footer">
          
          
        </div>
      </div>
    </div>
  </div>
</div>
</div>

<!-- Modal para actualizar -->
<div class="modal fade" id="actualizardepartamento" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="staticBackdropLabel">Actualizar Departamentos</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form  id="updateDepartamento">
            <input type="hidden" id="updatedepartamento_id" >
            <div class="row">
                <div class="col-md-4">
                    <label for="nombredepartamento">Nombre:</label>
                    <input type="text" name="nombredepartamentoactual" id="nombredepartamentoactual" class="form-control">
                </div>
                
            </div>
            <br>
            <div class="row">
                <div class="col-md-4">
                    <button type="submit" class="btn btn-success">Actualizar</button>
                </div>
            </div>
           
          </form>
        </div>
        <div class="modal-footer">
          
          
        </div>
      </div>
    </div>
  </div>
</div>
</div>
@endsection