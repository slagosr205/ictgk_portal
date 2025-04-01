<div>
    <!-- It is quality rather than quantity that matters. - Lucius Annaeus Seneca -->
    <label for="puesto_id">Seleccione un puesto</label>
    <select name="id_puesto" id="id_puesto" class="form-control border">
        <option value=""><----------></option>
        @foreach ($puestos as $puesto)
            <option value="{{$puesto->id}}">{{$puesto->nombrepuesto}}</option>
        @endforeach
        
        
    </select>
</div>