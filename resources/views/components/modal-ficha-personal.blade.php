<!-- Button trigger modal -->

  
  <!-- Modal -->
<div class="modal fade" id="importInputCandidate" data-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="staticBackdropLabel">Ingresos Masivos</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="alert alert-info">
          <strong> El formato archivo a subir debe ser .CSV</strong>
        </div>
        <form  id="frmimportInputCandidate">
          
        <div class="row">
          <div class="col-md-8">
            <input type="file" name="archivo_csv" id="archivo_csv" class="form-control border" accept=".csv">
          </div>
        </div>
        <div class="row">
            <div class="col-md-8">
              <button type="submit" class="btn btn-warning mt-2" id="btnRegistraIngresos" >Guardar</button>
            </div>
        </div>
      </form>
      </div>
      <div class="modal-footer">
        
        
      </div>
    </div>
  </div>
</div>