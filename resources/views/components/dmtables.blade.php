@php
    use Jenssegers\Date\Date;
@endphp


<div class="container">
       <div class="row ">
        <div class="col-md-4">
            <!-- Button trigger modal -->
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#registerCandidate">
              <i class="ri-sticky-note-add-fill"></i>Nuevo Candidato
            </button>
            
        </div>
       
      <div class="col-md-2">
        @foreach ($perfil as $pu)
              @if ($pu->egreso===1)
                <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#importOut"><i class="ri-file-upload-line py-4"></i>Importar Egresos</button>
              @endif
        @endforeach
      </div>
        <x-import-candidate />
        <x-modal-importar-egresos />
    </div>
    <br>
    @if(session('mensaje'))
        <div hidden id="mensaje">
            {{ session('mensaje') }}
            
        </div>
    @endif

    @if(session('missingFields'))
      <div hidden id="missingFields">
          {{ 'campos obligatorios en la plantilla' }}
          
      </div>
    @endif

    @if(session('mensajeerror'))
        <div hidden id="mensajeerror">
            {{ session('mensajeerror') }}
            
        </div>
    @endif

    @if(session('error'))
        <div hidden id="error">{{ session('error') }}</div>
    @endif

    <x-modal-registro/>
    <table id="tbcandidatos" class="table table-striped display table-bordered border" style="width:100%">
        <thead>
            <tr>
                <th><button class="btn btn-info" id="btnEgresoMasivo"> <i class="ri-file-download-line py-4"></i> Exportar Egresos</button></th>
                <th>Identidad</th>
                <th>Nombre</th>
                
                <th>Telefono</th>
                <th>Correo</th>
                <th>Fecha Nacimiento</th>
                @foreach ($perfil as $pu)
                  @if ($pu->perfilesdescrip==='admin')
                    <th>Observaciones</th>
                  @else
                    <th>{{""}}</th>
                  @endif
                @endforeach
                <th>Accion</th>
            </tr>
        </thead>
        <tbody>
         
            @foreach ($candidatos as $candidato)
            <tr>
                @if ($candidato->activo==='n' && $candidato->activo_ingreso==='s')
                  <td><input type="checkbox" name="" id="" class="selectOutput" value="{{$candidato->identidad}}"></td>
                @else
                   <td></td> 
                @endif
                
                <td>{{$candidato->identidad}}</td>
                <td>{{$candidato->nombre.' '.$candidato->apellido}}</td>
                <td>{{$candidato->telefono}}</td>
                <td>{{$candidato->correo}}</td>
                <td>{{$candidato->fecha_nacimiento}}</td>
                @foreach ($perfil as $pu)
                  
                  @if ($pu->perfilesdescrip==='admin')
                      
                    <td>
                      @if (is_array($candidato['comentarios']) && isset($candidato['comentarios']))
                      <ul>
                        @foreach ($candidato->comentarios as $comentario)
                            
                                @if (isset($comentario['fechaBloqueo']))
                                <li>
                                  @php
                                      // Supongamos que $fecha es un objeto DateTime
                                      $fechaFormateadaBloqueo = new Date($comentario['fechaBloqueo']);
                                  @endphp

                                    <strong>Comentarios de Bloqueo:</strong> {{ $comentario['comentarios'] }}<br>
                                    <strong>Fecha de Bloqueo:</strong> {{ $fechaFormateadaBloqueo->format('j \\de F Y') }}
                                  </li>
                                @endif
                                @if (isset($comentario['fechaDesbloqueo']))
                                    <li>
                                      @php
                                      // Supongamos que $fecha es un objeto DateTime
                                        $fechaFormateadaDesbloqueo = new Date($comentario['fechaDesbloqueo']);
                                      @endphp
                                    <strong>Comentarios de desbloqueo:</strong> {{ $comentario['comentarios'] }}<br>
                                    <strong>Fecha de desbloqueo:</strong> {{ $fechaFormateadaDesbloqueo->format('j \\de F Y') }}
                                  </li>
                                @endif
                                
                            
                        @endforeach
                      </ul> 
                      @else
                          <span class="blockquote-footer">No hay comentarios disponibles.</span> 
                      @endif
                     
                    </td>
                  @else
                    <td>{{""}}</td>
                  @endif
                @endforeach
                <td>
                  
                  @foreach ($perfil as $pu)
                  @if ($pu->perfilesdescrip==='admin')
                      @if ($pu->bloqueocolaborador===1 && $candidato['activo']==='x')
                      <button class="btn btn-success btndesbloqueo" id="btndesbloqueo" data-bs-toggle="modal" data-bs-target="#unlockcandidate" value="{{$candidato['identidad']}}" ><i class="ri-lock-unlock-line"></i>desbloquear</button>
                    @else
                      <button type="button" class="btn btn-danger btnbloqueo" id="btnbloqueo" data-bs-toggle="modal" data-bs-target="#lockcandidate" value="{{$candidato['identidad']}}" >bloquear</button>
                    @endif
                  @else
                    {{"" }}
                  @endif
                     
                  @endforeach
                </td>
            </tr>
            @endforeach
            
            
            
        </tbody>
        
    </table>

    {{---modal de actualizacion--}}
     
  <!-- Modal -->
  <div class="modal fade" id="update-data" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="staticBackdropLabel">Nuevo Candidato</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="card-body2">
              <h3>Datos Generales</h3>
              <hr>
                <form class="form-container" method="POST" action="">
                  @csrf
                  <!-- 1st row: 4 inputs (2 select, 2 text) -->
                  <div class="row">
                    <div class="form-element">
                        <label for="identidad ">Nº de DNI</label>
                        <input type="text" id="identidad" name="identidad" placeholder="0000-0000-00000" />
                      </div>
                    <div class="form-element">
                      <label for="nombre">Nombre</label>
                      <input type="text" id="nombre" name="nombre"  />
                    </div>
                    <div class="form-element">
                      <label for="apellido">Apellidos</label>
                      <input type="text" id="apellido" name="apellido" />
                    </div>

                  </div>
              
              
                  <div class="row" id="CodigoGuarnicion">
                    <div class="form-element">
                      <label for="telefono">Telefono:</label>
                      <input type="text" id="telefono" name="telefono" required placeholder="0000-0000"/>
                    </div>
                    <div class="form-element">
                      <label for="correo">Correo:</label>
                      <input type="email" id="correo" name="correo" required />
                    </div>
                    <div class="form-element">
                      <label for="fecha_nacimiento">Fecha Nacimiento:</label>
                      <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required />
                    </div> 
                    <div class="form-element">
                      <label for="generoM_F">Genero</label>
                      <select name="generoM_F" id="generoM_F">
                        <option value="m">Masculino</option>
                        <option value="f">Femenino</option>
                      </select>
                    </div>
                  </div>
                  <h5>Direccion</h5>
                  
                  <div class="row" id="DNI">
                    <div class="form-element">
                      <label for="direccion">Calle:</label>
                      <textarea name="direccion" id="direccion" cols="30" rows="10"></textarea>
                    </div>
                         
                  </div>
              
              
                  <div class="row">
                    
                    
                    
                    
                  </div>
                  <div class="row">
                    <!-- 3rd row: 4 date inputs -->
                    
                    
                   
                  </div>
                  
                
              
                <!-- 5th row: 2 buttons -->
                <div class="button-container">
                  <button type="submit" class="btn btn-success BtnForm" id="Grabar" name="Grabar">Grabar</button>
                  
                </div>
              </form>
              </div>
        </div>
        <div class="modal-footer">
         
        </div>
      </div>
    </div>
  </div>


  <x-unlock-candidate />
  <x-lock-candidate />
</div>