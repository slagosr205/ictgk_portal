<div class="modal fade" id="unlockcandidate" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="staticBackdropLabel">Desbloqueo de Ingreso</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <form action="{{route('unlockCandidate')}}" method="POST">
              @csrf
              {{--
                1. Cuando se haga click en el boton desbloquear en el archivo app.js asignara el valor de la identidad del registro
                2. Lineas de ejecucion 409 al 421 en el app.js
                --}}
              <input type="hidden" id="modalidentidad" name="identidad" value="">
             
              
              <div class="col-md-6">
                <label for="prohibirIngreso">Desbloquear: </label>
                <select class="form-select" name="prohibirIngreso" id="prohibirIngreso" >
                  <option value="n">SI</option>
                </select>
              </div>
              <div class="col-md-6">
                <label for="ComenProhibir">Comentarios de Bloqueo: </label>
                <textarea class="form-control" name="ComenProhibir" id="ComenProhibir" cols="30" rows="10"></textarea>
              </div>
              <div class="col-md-6">
                <button type="submit" class="btn btn-primary mt-2">Grabar</button>
              </div>
              
            </form>
          </div>
        </div>
        <div class="modal-footer">
         
          
        </div>
      </div>
    </div>
  </div>