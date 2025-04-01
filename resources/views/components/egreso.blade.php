<div class="border">
    <!-- Waste no more time arguing what a good man should be, be one. - Marcus Aurelius -->
    @php
      $encontrado = false;
  
    @endphp


  <h5 class="px-2"><strong>Egresos</strong></h5>
  <hr>
    <div class="container-fluid">
      <form action="{{route('hacerEgresos')}}" method="post">
        @csrf
        <div class="row">
    
      <div class="col-md-2">
        <label for="empresas">Empresas Actual:</label>
        {{----Evaluando si el usuario es administrador------}}
        @if ($perfil[0]->perfilesdescrip!=='admin')
          @foreach ($empresas as $em)
            @if ($em->id===auth()->user()->empresa_id)
              <input type="hidden" id="id_empresa" name="id_empresa" value="{{$em->id}}">
              <p><strong name="{{$em->id}}">{{$em->nombre}}</strong></p>
            @endif
              
          @endforeach
        @else

          @foreach ($empresas as $em)
            @if ($informacionlaboral[0]['activo']==='s' && $em->id===$informacionlaboral[0]['id_empresa'])
              <input type="hidden" id="id_empresa" name="id_empresa" value="{{$em->id}}">
              <p><strong name="{{$em->id}}">{{$em->nombre}}</strong></p>
            @endif
            
          @endforeach
        @endif
        
    
      </div>
     {{--- <div class="col-md-2">
        <label for="area">Area:</label>
        @foreach ($empresas as $em)
        
            @foreach ($informacionlaboral as $if)
              @if (auth()->user()->empresa_id===$if['empresa_id'])
                <span><strong>{{$if['area']}}</strong></span>
                @php
                  $encontrado = true;
                @endphp
                @break
              @endif
            @endforeach
            @if($encontrado)
              @break
            @endif
        @endforeach
      </div>---}}

      {{--<div class="col-md-2">
        <label for="area">Puesto:</label>
        @foreach ($empresas as $em)
          
            @foreach ($informacionlaboral as $if)
              @if (auth()->user()->empresa_id===$if['empresa_id'])
                <span><strong>{{$if['puesto_nombre']}}</strong></span>
                @php
                  $encontrado = true;
                @endphp
                @break
              @endif
            @endforeach
            @if($encontrado)
              @break
            @endif
        @endforeach
      </div>---}}

   {{-------<div class="col-md-4 mx-auto">
      <label for="activo">Activo:</label>
      @foreach ($informacionlaboral as $if)
        @if ($if['fecha_Egreso']==='')
            <p><strong>SI</strong></p>
        @endif
      @endforeach
    </div>---}}
    
    </div>
    <div class="row">
      <input type="hidden" id="identidad" name="identidad" value="{{ $informacionlaboral[0]['identidad']}}">
    </div>
    <div class="row">
      <div class="col-md-2">
        <label for="egreso">Forma de egreso:</label>
        <select name="tipo_egreso" id="tipo_egreso" >
          <option value=""><------------></option>
          <option value="Voluntario">Voluntario</option>
          <option value="Involuntario">Involuntario</option>
        </select>
      </div>
      <div class="col-md-2">
        <label for="tiempo">Fecha de egreso:</label>
        <input type="date" name="tiempo" id="tiempo">
      </div>
      <div class="col-md-2 ">
        <label for="forma_egreso">Motivo de egreso:</label>
          <select name="forma_egreso" id="forma_egreso" >
            <option value=""><------------></option>
              <option value="Abandono de Labores">Abandono de Labores</option>
              <option value="Conflictos de Horarios">Conflictos de Horarios</option>
              <option value="Motivos de estudios">Motivos de estudios</option>
              <option value="Nueva Oportunida laboral">Nueva Oportunida laboral</option>
              <option value="Enfermedad">Enfermedad</option>
              <option value="Bajo rendimiento">Bajo rendimiento</option>
              <option value="Otros">Otros</option>
              
          </select>
        </div>
        <div class="col-md-2">
        <label for="recomendado">Recomendado:</label>
        <select name="recomendado" id="recomendado" >
          <option value="0"><------------></option>
          <option value="s">SI</option>
          <option value="n">NO</option>
        </select>
        </div>
    </div>
    <div class="row">
      <div class="col-md-3">
        <label for="comentarios">Comentarios: </label>
        <textarea name="comentarios" id="comentarios" cols="30" rows="10"></textarea>
      </div>
      <div class="col-md-3">
        @foreach ($perfil as $pu)
            @if ($pu->bloqueocolaborador===1)
              
            @endif
        @endforeach
       
      </div>
      
    </div>
    
    <br>
    <button type="submit" class="btn btn-success "><i class="ri-save-fill mx-2"></i>Guardar</button>
    </form>
    </div>
    
    
    </div>

    
