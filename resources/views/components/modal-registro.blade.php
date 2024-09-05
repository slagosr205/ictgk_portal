
  
  <!-- Modal -->
  <div class="modal fade" id="registerCandidate" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="staticBackdropLabel">Nuevo Colaborador</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="card-body2">
              <h3>Datos Generales</h3>
              <hr>
                <form class="form-container" method="POST" action="{{route('insertarCandidato')}}">
                  @csrf
                  <!-- 1st row: 4 inputs (2 select, 2 text) -->
                  <div class="row">
                    <div class="form-element">
                        <label for="identidad ">Nº de DNI</label>
                        <input type="text" id="identidad" name="identidad" placeholder="0000000000000" />
                      </div>
                      @error('identidad')
                        <div class="alert alert-warning">
                            <strong>{{$message}}</strong>
                        </div>
                    @enderror
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