  <!-- It is quality rather than quantity that matters. - Lucius Annaeus Seneca -->



  <div class="row d-flex border ">

    @php

      use Jenssegers\Date\Date;

      Date::setLocale('es');

    @endphp

    

    <div class="col-md-3">

       @if ($infocandidatos->generoM_F=='M' || $infocandidatos->generoM_F=='m')

          <img class="rounded-circle" src="{{Storage::url('avatar.png')}}" alt="" width="64px"><br>

        @else

          <img class="rounded-circle" src="{{Storage::url('mujer.png')}}" alt="" width="64px"><br>

       @endif

        

        <span id="nombre">{{strtoupper($infocandidatos->nombre.' '.$infocandidatos->apellido)}}</span>

        <!-- Contenedor del menú contextual -->

        <button class="btn btn-success btnupdate" id="{{$infocandidatos->id}}" >Actualizar Ficha</button>

    </div>

   

    

   

  

    <div class="col-md-3">

      <label for="">Identidad:</label><br>

      <span id="identidad"><strong>{{$infocandidatos->identidad}}</strong></span><br>

      <label for="">Genero:</label><br>

      <span id="genero"><strong>{{$infocandidatos->generoM_F}}</strong></span><br>

      <label for="" class="py-3">Telefono:</label>

      <input class="form-control border " id="telefono" value="{{$infocandidatos->telefono}}">

    </div>

  

  

    <div class="col-md-3">

      <label for="">Fecha Nacimiento:</label><br>

      <span id="fechaNacimiento"><strong> {{$infocandidatos->fecha_nacimiento}}</strong></span><br>

      <label for="">Correo:</label>

      <input class="form-control border" id="correo" value="{{$infocandidatos->correo}}">

      <label for="" class="py-3">Direccion:</label>

      <input class="form-control border" id="direccion" value="{{$infocandidatos->direccion}}" >

    </div>

   

    {{--Colocar la informacion laboral del --}}

   

    <div class="col-md-2">

      

      @switch($infocandidatos->activo)

          @case('n')

              <img  src="{{Storage::url('informacion.png')}}" alt="" width="64px">

            @break

          @case('s')

              <img  src="{{Storage::url('cheque.png')}}" alt="" width="64px">

            @break

          @default

          {{--<img  src="{{Storage::url('cancelar.png')}}" alt="" width="64px">--}}

      @endswitch

      

    </div>



    

    

</div>

