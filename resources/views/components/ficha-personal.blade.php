  {{--- Datos generales--}}
 
  @php
      $misma_empresa=false;
      $itemEmpresas=[];
      $bloqueo_recomendado=false;
      $identidad=null;
      $empresaID=null;
      
      
            
  @endphp
  
  {{--Validando que el usuario sea de la misma compañia con el candidato--}}
  @foreach ($informacionlaboral as $il)
  
    @if ($il['id_empresa']==auth()->user()->empresa_id && $il['activo']=='s')
      @php
        $misma_empresa=true
        
      @endphp
    @endif
    
 @endforeach



@if ($misma_empresa )
<x-actualizacion-ficha :infocandidatos="$infocandidatos" />  
@else
  <x-visualizacion-ficha :infocandidatos="$infocandidatos" />   
@endif

{{--
  La validacion tiene que ser de la siguiente manera, 
  cuando el usuario busque los datos la 
  condicion:
  Se visualizara el historial si el candidato esta en verde
  No se visualizara si aparace en color amarillo o que este activo 's' 
  
  --}}
  
  <h3>Historial Laboral</h3>
  
      @switch($infocandidatos->activo)
        @case('n')
        
          @if ($misma_empresa || $perfil[0]->perfilesdescrip==='admin' )
            <x-historial-laboral :informacionLaboral="$informacionlaboral" :datosEmpresa="$datosEmpresaActual" />
            @if ($perfil[0]->egreso===1 )
              <x-egreso :informacionlaboral="$informacionlaboral" :empresas="$datosEmpresaActual" /> 
              @else
                <div class="alert alert-warning"><strong>Falta autorizacion para hacer egresos!</strong></div>
            @endif
          @else
            <div class="alert alert-warning">
              <span><strong>Actualmente se encuentra activo en otra empresa</strong></span>
            </div>
          @endif
        @break
        @case('s')
          
            <x-historial-laboral :informacionLaboral="$informacionlaboral" :datosEmpresa="$datosEmpresaActual" />
            {{---se visualizara este componente si el campo "recomendado" es "s" y el campo "bloquedo recomendado" es n, 
            caso contario se visualizara un mensaje de solictude Feedback donde aperecera un boton para enviar 
            correo solicitando el informacion porque no es recomendado--}}
            @foreach ($informacionlaboral as $dtem)
                
                @if ($dtem['recomendado']==='n')
                  @php
                            $identidad=$infocandidatos->identidad;
                            $empresaID=$dtem['id_empresa'];
                            $bloqueo_recomendado=true;
                  @endphp
                @endif
                
            @endforeach
            {{---Si el candidato en su ultimo trabajo le colocaron n en el campo recomendado en la tabla egresos_ingresos, el usuario de la compañia 
              que lo quiere contratar no podra por la condicion lo que si podra hacer es enviar un correo desde la aplicacion con los datos del usuario
              para que Reclutamiento de ALTIA pueda hacer la respectiva validacion, caso contrario el usuario podra obtener la vista de Ingreso para hacer la accion---}}
            @if (!$bloqueo_recomendado)
              @if ($perfil[0]->ingreso===1 )
                <x-ingreso :informacionlaboral="$informacionlaboral" :empresas="$datosEmpresaActual" :candidato="$infocandidatos"  /> 
              @else
                <div class="alert alert-warning">
                  <strong>Falta autorizacion para hacer Ingresos!</strong>
                </div>
              @endif
                
            @else
              <div class="row">
                <div class="col-md-2">
                  <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#enviarSolicitud">Solicitar información</button>
                </div>
              </div>
            @endif
            
           
             
            @break
        @default
        <div class="row">
          <div class="col-md-6">
            <div class="alert alert-warning">
              <strong>Favor contactarse con el Equipo de Recursos Humanos ALTIA <strong>portal.reclutamiento@altiabusinesspark.com</strong></strong>
              
           </div>
           <div class="col-md-6">
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#enviarSolicitud">Solicitar información</button>
            <x-enviar-solicitud :identidad="$infocandidatos->identidad" />
           </div>
          </div>
        </div>
        
    @endswitch
  
  {{--<x-historial-laboral :informacionLaboral="$informacionlaboral" />--}}
   
    
 {{---Modal para debloquear recomendacion---}}
 @if (!is_null($identidad) && !is_null($empresaID))
  <x-desbloque-recomendacion :identidad="$identidad" :empresaID="$empresaID" />
 @endif

 


