@extends('layouts.app')

@section('perfiles')



    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <button class="btn btn-success py-4 mb-4" data-bs-toggle="modal" data-bs-target="#modaladdperfil"><i class="ri-apps-2-add-fill"></i>Nuevo</button>
            </div>
        </div>
        <div class="row">
            <table id="tbperfiles" class="table table-striped">
                <thead>
                    
                    <tr>
                        <th>ID</th>
                        <th>Descripcion</th>
                        <th>Ingreso</th>
                        <th>Egresos</th>
                        <th>Bloqueo</th>
                        <th>Gestión Tablas</th>
                        <th>visualizar Informes</th>
                        
                    </tr>
                </thead>
                <tbody>
                    @foreach ($allperfils as $pf)
                        <tr >
                            <td class="idRole">{{$pf->id}}</td>
                            <td>{{$pf->perfilesdescrip}}</td>
                            @if ($pf->ingreso==0)
                                <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input chkpermiso" type="checkbox" role="switch" id="chk_ingreso">
                                        <label class="form-check-label" for="chk_ingreso">No permitido</label>
                                    </div>
                                </td>
                            @else
                                <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input chkpermiso" type="checkbox" role="switch" id="chk_ingreso" checked>
                                        <label class="form-check-label" for="chk_ingreso">Permitido</label>
                                    </div>
                                </td>
                            @endif
                            @if ($pf->egreso==0)
                            <td><div class="form-check form-switch">
                                <input class="form-check-input chkpermiso" type="checkbox" role="switch" id="chk_egreso">
                                <label class="form-check-label" for="chk_egreso">No permitido</label>
                              </div></td>
                            @else
                            <td><div class="form-check form-switch">
                                <input class="form-check-input chkpermiso" type="checkbox" role="switch" id="chk_egreso" checked>
                                <label class="form-check-label" for="chk_egreso">Permitido</label>
                              </div></td>
                            @endif
                            @if ($pf->bloqueocolaborador==0)
                            <td><div class="form-check form-switch">
                                <input class="form-check-input chkpermiso" type="checkbox" role="switch" id="chk_bloqueocolaborador">
                                <label class="form-check-label" for="chk_bloqueocolaborador">No permitido</label>
                              </div></td>
                        @else
                            <td><div class="form-check form-switch">
                                <input class="form-check-input chkpermiso" type="checkbox" role="switch" id="chk_bloqueocolaborador" checked>
                                <label class="form-check-label" for="chk_bloqueocolaborador">Permitido</label>
                              </div></td>
                            @endif
                            @if ($pf->gestiontablas==0)
                            <td><div class="form-check form-switch">
                                <input class="form-check-input chkpermiso" type="checkbox" role="switch" id="chk_gestiontablas">
                                <label class="form-check-label" for="chk_gestiontablas">No permitido</label>
                              </div></td>
                        @else
                            <td><div class="form-check form-switch">
                                <input class="form-check-input chkpermiso" type="checkbox" role="switch" id="chk_gestiontablas" checked>
                                <label class="form-check-label" for="chk_gestiontablas">Permitido</label>
                              </div></td>
                            @endif
                            @if ($pf->visualizarinformes==0)
                            <td><div class="form-check form-switch">
                                <input class="form-check-input chkpermiso" type="checkbox" role="switch" id="chk_visualizarinformes">
                                <label class="form-check-label" for="chk_visualizarinformes">No permitido</label>
                              </div></td>
                            @else
                                <td><div class="form-check form-switch">
                                    <input class="form-check-input chkpermiso" type="checkbox" role="switch" id="chk_visualizarinformes" checked>
                                    <label class="form-check-label" for="chk_visualizarinformes">Permitido</label>
                                </div></td>
                            @endif
                                
                        </tr>
                    @endforeach
                    
                </tbody>
            </table>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modaladdperfil" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalLabel">Crear Perfil</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          {{--inicio---}}
            <form action="{{route('registrarperfil')}}" method="post">
                @csrf
                <div class="form-row">
                    <label for="perfilesdescrip">Descripcion</label>
                    <input type="text" name="perfilesdescrip" id="perfilesdescrip" class="form-control">
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label for="ingreso">Hacer Ingresos</label>
                        <select name="ingreso" id="ingreso" class="form-select">
                            <option value="1">permitido</option>
                            <option value="0">no permitido</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="egreso">Hacer Egresos</label>
                        <select name="egreso" id="egreso" class="form-select">
                            <option value="1">permitido</option>
                            <option value="0">no permitido</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <label for="ingreso">Bloquear Ingresos</label>
                        <select name="bloqueocolaborador" id="bloqueocolaborador" class="form-select">
                            <option value="1">permitido</option>
                            <option value="0">no permitido</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="calendarioentrevistas">Gestión de Tablas</label>
                        <select name="gestiontablas" id="gestiontablas" class="form-select">
                            <option value="1">permitido</option>
                            <option value="0">no permitido</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="visualizarinformes">Visualizar Informes</label>
                        <select name="visualizarinformes" id="visualizarinformes" class="form-select">
                            <option value="1">permitido</option>
                            <option value="0">no permitido</option>
                        </select>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-4 ">
                        <button class="btn btn-success " >Guardar</button>
                    </div>
                </div>
            </form>
          {{--final---}}
        </div>
        
      </div>
    </div>
  </div>
@endsection