<!-- Modal -->
<div class="modal fade" id="modalmodificarPuestos" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="staticBackdropLabel">Actualización de Puestos </h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form  method="POST" action="{{route('updatePosition')}}">
              @csrf
                <input type="hidden" name="puesto_id" id="puesto_id">
                <input type="hidden" name="departamento_id" id="departamento_id">
                <div class="row">
                    <div class="col-md-4">
                        <label for="nombre">Nombre:</label>
                        <input type="text" name="puestonombre" id="puestonombre" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label for="telefono">Departamento:</label>
                        <input type="text" name="departamentonombre" id="departamentonombre" class="form-control" readonly>
                    </div>
                </div>
                
    
                <button type="submit" class="btn btn-success">Actualizar</button>
                
              </form>
        </div>
        <div class="modal-footer">
          
        </div>
      </div>
    </div>
  </div>