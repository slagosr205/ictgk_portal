<!-- Modal -->
<div class="modal fade" id="enviarSolicitud" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="staticBackdropLabel">Envío de Solicitud</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="container">
              <form action="{{route('SolicitudDesbRecom')}}" method="post">
                @csrf
              <div class="row">
                    <input type="hidden" name="solID" id="solID" value="{{$identidad}}">
                    <div class="col-md-12">
                      <label for="">Mensaje:</label>
                      <textarea  class="form-control" name="mensaje" id="" cols="30" rows="10"></textarea>
                      <button type="submit" class="btn btn-primary mt-2"><i class="ri-send-plane-fill"></i>Enviar Solicitud</button>
                    </div>
                    
              </div>
            </form>
            </div>
    </div>

       
      </div>
    </div>
  </div>