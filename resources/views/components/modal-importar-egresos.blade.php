<div class="modal fade" id="importOut" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="staticBackdropLabel">Importación de Egresos</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12 carga " id="elementImportOut">
                    <form id="frmImportOut">
                        @csrf
                        <label for="" class="text-center"><strong>Importación de Egresos</strong></label>
                            <input type="file" name="egresos_csv" id="egresos_csv" class="form-control" accept=".csv">
                        <br>
                        <button class="btn btn-success" type="submit"><i class="ri-sticky-note-add-fill"></i>Cargar Egresos</button>
                    </form>
                </div>
            </div>
        </div>
        
      </div>
    </div>
</div>