<!-- Modal -->
<div class="modal fade" id="importcandidate" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="staticBackdropLabel">Importacion de Colaboradores</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12 carga " id="elementImport">
                    <form action="{{ route('cargar') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <label for="" class="text-center"><strong>Importacion de Registros</strong></label>
                        <input type="file" name="archivo_csv" class="form-control" accept=".csv">
                        <br>
                        <button class="btn btn-success" type="submit"><i class="ri-sticky-note-add-fill"></i>Cargar</button>
                    </form>
                </div>
            </div>
        </div>
        
      </div>
    </div>
</div>