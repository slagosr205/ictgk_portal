<div class="container mt-5">
    <h2>Crear Nueva Tabla</h2>
    <form id="frmtable">
      @csrf
      <div class="form-group">
        <label for="tableName">Nombre de la tabla:</label>
        <input type="text" class="form-control" id="tableName" name="table_name" required>
      </div>
      
      <h3>Columnas</h3>
      <div class="columnas">
          <div class="col">
              <div class="form-row align-items-center columna">
            <input type="text" class="form-control" name="columns[][name]" placeholder="Nombre de la columna" required>
          </div>
          <div class="col">
            <select class="form-select" name="columns[][type]" required>
              <option value="string">String</option>
              <option value="integer">Integer</option>
              <option value="text">Text</option>
              <!-- Agrega más opciones según tus necesidades -->
            </select>
          </div>
          <div class="col-auto">
            <button type="button" class="btn btn-danger eliminar-columna">Eliminar</button>
          </div>
        </div>
      </div>
      
      <button type="button" class="btn btn-primary" id="agregar-columna">Agregar Columna</button>
      <button type="submit" class="btn btn-success">Crear Tabla</button>
    </form>
  </div>
