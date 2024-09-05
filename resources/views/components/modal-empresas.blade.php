<!-- Modal -->
<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="staticBackdropLabel">Registro de empresas</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form action="" method="post">
                @csrf
          <div class="row">
            <div class="col-md-6">
                <label for="">Nombre Empresa: </label>
                <input type="text" class="form-control">
            </div>
            <div class="col-md-6">
                <label for="">Direccion: </label>
                <input type="text" class="form-control">
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
                <label for="">Telefono: </label>
                <input type="text" class="form-control">
            </div>
            <div class="col-md-6">
                <label for="">Contacto: </label>
                <input type="text" class="form-control">
            </div>
          </div>
          <div class="row">
            
            <div class="col-md-4">
                <label for="">Correo: </label>
                <input type="text" class="form-control">
            </div>
            <div class="col-md-4">
                <label for="">Logo: </label>
                <input class="form-control" type="file" name="" id="">
            </div>
            <div class="col-md-4"><br><button type="submit" class="btn btn-primary">Grabar</button></div>
          </div>
        </form>
        </div>
       <div class="modal-footer"></div>
      </div>
    </div>
  </div>