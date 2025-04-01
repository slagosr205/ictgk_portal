<div class="border mt-4">
    <!-- Waste no more time arguing what a good man should be, be one. - Marcus Aurelius -->
    

    <h5 class="px-2">Ingresos</h5>
    <hr>
    <div class="container-fluid">
    <form action="{{route('hacerIgresos')}}" method="post">
      @csrf
        <div class="row">
              @if ($perfil[0]['perfilesdescrip']!=="admin")

              <div class="col-md-2">
                <label for="empresas">Empresas Actual:</label>
                  @foreach ($empresas as $em)
                  @if ($em->id===auth()->user()->empresa_id)
                 
                  
                  <input type="hidden" id="id_empresa" name="id_empresa" value="{{$em->id}}">
                    <p><strong class="empresa_id" name="{{$em->id}}" >{{$em->nombre}}</strong></p>
                  @endif
                      
                  @endforeach
                  @empty($informacionlaboral)
                    <input type="hidden" id="identidad" name="identidad" value="{{ $candidato->identidad}}">
                    @else
                    <input type="hidden" id="identidad" name="identidad" value="{{ $informacionlaboral[0]['identidad']}}">
                  @endempty
                  
                   
                  
                </div>
                  
              @else
              <div class="col-md-2">
                <label for="empresas">Seleccione una Empresa:</label>
                  <select name="id_empresa" id="id_empresa" class="form-control border">
                    <option value="0"><------------></option>
                    @foreach ($empresas as $em)
                        <option value="{{$em->id}}">{{$em->nombre}}</option>
                    @endforeach

                  </select>
                  @empty($informacionlaboral)
                      <input type="hidden" id="identidad" name="identidad" value="{{ $candidato->identidad}}">
                    @else
                      <input type="hidden" id="identidad" name="identidad" value="{{ $informacionlaboral[0]['identidad']}}">
                  @endempty
                </div>
              @endif
             

              <div class="col-md-2">
                <label for="area">Fecha de Ingreso:</label>
                <input type="date" name="fecha_ingreso" id="fecha_ingreso" class="form-control border" required >
              </div>
              <div class="col-md-2">
                <label for="area">Area:</label>
                <select name="area" id="area" class="form-select">
                  <option value="0"><------------></option>
                  <option value="operativa">Operativa</option>
                  <option value="administrativa">Administrativa</option>
                </select> 
              </div>

              <div class="col-md-2">
               @if (auth()->user()->perfil_id!==1)
                <label for="area">Puesto:</label>
                <select name="id_puesto" id="id_puesto" class="form-select">
                  <option value="0"><------------</option>
                  @foreach ($puestos as $puesto)
                  <option value="{{$puesto->id}}">{{$puesto->nombrepuesto}}</option>
                  @endforeach
                  
                  
                  
                </select>
               @endif
               
              </div>
            
            <p id="positions-selected"></p>
        </div>
        <div class="row">
          <div class="col-md-12">

            <label for="comentarios">Comentarios: </label>
            <textarea  class="form-control border" name="comentarios" id="comentarios" cols="30" rows="10" placeholder="Coloque sus comentarios"></textarea>
          </div>
        </div>
        <div class="row">
          <div class="col-md-4">
            <button type="submit" class="btn btn-success mt-2" name="btngrabar" >Grabar</button>
          </div>
        </div>
        
        
      
    </form>

    </div>
    
      
    </div>