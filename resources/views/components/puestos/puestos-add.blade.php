
<!-- Modal -->
<div class="modal fade" id="modalagrPuesto" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="staticBackdropLabel">Crear Nuevo Puesto</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form action="{{route('insertPositions')}}" method="POST">
                @csrf
                <label for="nombrepuesto">Nombre del Puesto:</label>
                <input type="text" id="nombrepuesto" name="nombrepuesto" required>
                <label for="departamento_id">Seleccione Departamento:</label>
                <select name="departamento_id" id="departamento_id" class="form-select">
                    <option value=""><----------></option>
                    @foreach ($departamentos as $departamento)
                        <option value="{{$departamento->id}}">{{$departamento->nombredepartamento}}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-success mt-4" data-bs-dismiss="modal"><i class="ri-save-line"></i>Guardar</button>
            </form>
        </div>
        <div class="modal-footer">
          <p id="mensajeerrorspuesto"></p>
         
        </div>
      </div>
    </div>
  </div>