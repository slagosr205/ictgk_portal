 <!-- Navbar Start -->
 <nav class="navbar navbar-expand-lg bg-white navbar-light sticky-top p-0" style="background-color: #072132 !important"  >
    <a href="/home" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
        <img src="{{Storage::url('altialogoblanco.png')}}" width="160px" alt="">
    </a>
   
    <h4 class="text-center text-white">Portal de Reclutamiento</h4>
    <h4 class="text-center text-white px-4"><strong>{{$logos[0]['nombre']}}</strong></h4>
    <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
        <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse " id="navbarCollapse">
        <div class="navbar-nav ms-auto ">
            @guest
                <a href="/home" class="nav-item nav-link active text-white">Inicio</a>
                @if (Route::has('login'))
                    <li class="nav-item">
                        <a class="nav-link text-white" href="/">{{ __('Login') }}</a>
                    </li>
                @endif

              {{---  @if (Route::has('register'))
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                    </li>
                @endif---}}
            @else
                    <!---Inicio de menu dinamico-->
                <a href="/home" class="nav-item nav-link active text-white">Inicio</a>
                <a href="#" class="nav-item nav-link active text-white" data-bs-toggle="modal" data-bs-target="#historicoModal">Historico</a>
                
                @foreach ($perfil as $pu)
               
                    
                
                <div class="dropdown mt-3" >

                    <a href="#" class="btn dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre style="background-color: #072132 !important"><strong class="text-white">Gestion</strong></a>
                    
                    <div class="dropdown-menu ">
                        <a href="{{route('candidatos')}}" class="dropdown-item"><i class="px-2 ri-user-follow-line"></i>Candidatos</a>
                        
                        @if ($pu->gestiontablas===1)
                            <a href="{{route('empresas')}}"         class="dropdown-item"><i  class=" px-2 ri-government-line"></i>Empresas</a>
                            <a href="{{route('seccion-perfiles')}}" class="dropdown-item"><i class="px-2 ri-team-line"></i>Roles</a>
                            <a href="{{ route('register') }}"       class="dropdown-item" ><i class="px-2 ri-id-card-line"></i>{{ __('Registro Usuario') }}</a>
                        @endif
                        
                        <a href="{{route('departamentos')}}"        class="dropdown-item"><i class="px-2 ri-store-line"></i>Departamentos</a>
                        <a href="{{route('puestos')}}"              class="dropdown-item"><i class="px-2 ri-team-line"></i>Puestos</a>
                        
                    </div>
                </div>
                
            
                    @if ($pu->visualizarinformes===1)
                        <a href="{{route('informes')}}" class="nav-item nav-link text-white">Informe</a>
                    @endif
                @endforeach

                <!--Final de menu dinamico-->
                <div class="dropdown mt-3">
                    <a href="#" class="btn dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre><strong class="text-white"><i class="px-2 ri-user-received-line"></i>{{ Auth::user()->name }}</strong></a>
                    <div class="dropdown-menu ">
                        <a class="dropdown-item " href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();"><i class="ri-door-open-line"></i><strong>
                                                             {{ __('Logout') }}
                                                     </strong>      
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </div>
                
            @endguest
        </div>
    </div>
</nav>
<!-- Navbar End -->