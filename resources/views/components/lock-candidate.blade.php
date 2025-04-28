{{---Modal para exclusion---}}

<div class="modal fade" id="lockcandidate" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="staticBackdropLabel">Bloqueo de Ingreso</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <form action="{{route('lockCandidate')}}" method="post">
              @csrf
              <input type="hidden" id="lockidentidad" name="identidad" value="">
             
              
              <div class="col-md-6">
                <label for="prohibirIngreso">Excluido: </label>
                <select class="form-select" name="prohibirIngreso" id="prohibirIngreso" >
                  <option value="s">SI</option>
                  <option value="n">NO</option>
                </select>
              </div>
              <div class="col-md-6">
                <label for="ComenProhibir">Comentarios de Bloqueo: </label>
                <textarea class="form-control" name="ComenProhibir" id="ComenProhibir" cols="30" rows="10"></textarea>
              </div>
              <div class="col-md-6">
                <button type="submit" class="btn btn-primary mt-2">Bloquear</button>
              </div>
              
            </form>
          </div>
        </div>
        <div class="modal-footer">
         
          
        </div>
      </div>
    </div>
  </div>

