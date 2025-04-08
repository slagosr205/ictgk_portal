<!-- Modal -->
<div class="modal fade" id="importBlockModal" tabindex="-1" aria-labelledby="importBlockLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="importBlockLabel">Bloqueo Masivo de Candidatos</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
       <form action="{{route('blockPark')}}" method="post" enctype="multipart/form-data">
            @csrf
            <input type="file" name="archivo_csv" id="archivo_csv" accept=".csv" class="form-control border" >
             <button type="submit" class="btn btn-success mt-2">Enviar informacion</button>
       </form>
      </div>
      <div class="modal-footer">
       
      </div>
    </div>
  </div>
</div>