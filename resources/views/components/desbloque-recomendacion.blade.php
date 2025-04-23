<!-- The code you provided is creating a modal in HTML. Modals are used to display content or forms on
top of the current page without navigating away from it. In this specific modal: -->
<!-- Modal -->
<div class="modal fade" id="modaldebloqueoRecomendacion" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="staticBackdropLabel"><i class="ri-lock-unlock-fill p-4"></i>Desbloqueo de Recomendacion</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form  id="frmdesbloqueoRecomendacion" class="desbloquearRecomendacion">
           
            <div class="row">
                <div class="col-md-6">
                    <label for="id">Identidad</label>
                    <input class="form-control" type="text" name="identidad" id="identidad" readonly value="{{$identidad}}" >
                    <input type="hidden" name="empresaID" id="empresaID" value="{{$empresaID}}" >
                </div>
                
            </div>
            <div class="row">
                <div class="col-md-6">
                    <button type="button" class="btn btn-success mt-2  btndesbloqueoRecomendacion" data-empresaID="{{$empresaID}}" data-identidad="{{$identidad}}" >Desbloquear</button>
                </div>
            </div>
            
          </form>
        </div>
        
      </div>
    </div>
  </div>