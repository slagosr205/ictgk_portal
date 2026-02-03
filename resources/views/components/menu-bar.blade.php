<!-- Navbar Start -->
<nav class="navbar navbar-expand-lg navbar-dark sticky-top p-0 custom-navbar">
    <div class="container-fluid px-4">
        <!-- Logo -->
        <a href="/home" class="navbar-brand d-flex align-items-center py-3">
            <img src="{{ Storage::url('altialogoblanco.png') }}" width="160px" alt="Logo" class="img-fluid">
        </a>
        
        <!-- Título Portal -->
        <div class="d-none d-lg-flex align-items-center mx-auto">
            <h4 class="text-white mb-0 me-4">Portal de Reclutamiento</h4>
            <span class="badge bg-light text-dark px-3 py-2">{{ $logos[0]['nombre'] }}</span>
        </div>

        <!-- Toggle Button -->
        <button type="button" class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <!-- Menu Items -->
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <!-- Mobile Titles -->
            <div class="d-lg-none text-center py-3 border-bottom border-secondary">
                <h5 class="text-white mb-2">Portal de Reclutamiento</h5>
                <span class="badge bg-light text-dark">{{ $logos[0]['nombre'] }}</span>
            </div>

            <ul class="navbar-nav ms-auto align-items-lg-center">
                @guest
                    <!-- Guest Menu -->
                    <li class="nav-item">
                        <a href="/home" class="nav-link">
                            <i class="ri-home-4-line me-2"></i>Inicio
                        </a>
                    </li>
                    @if (Route::has('login'))
                        <li class="nav-item">
                            <a class="nav-link" href="/">
                                <i class="ri-login-box-line me-2"></i>{{ __('Login') }}
                            </a>
                        </li>
                    @endif
                @else
                    <!-- Authenticated Menu -->
                    <li class="nav-item">
                        <a href="/home" class="nav-link">
                            <i class="ri-home-4-line me-2"></i>Inicio
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="#" class="nav-link" data-bs-toggle="modal" data-bs-target="#historicoModal">
                            <i class="ri-history-line me-2"></i>Histórico
                        </a>
                    </li>
                    
                    @foreach ($perfil as $pu)
                        <!-- Gestión Dropdown -->
                        <li class=" dropdown custom-dropdown">
                            <a href="#" class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="ri-settings-3-line me-2"></i>Gestión
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end custom-dropdown-menu">
                                <li>
                                    <a href="{{ route('candidatos') }}" class="dropdown-item">
                                        <i class="ri-user-follow-line me-2"></i>
                                        <span>Candidatos</span>
                                    </a>
                                </li>
                                
                                @if ($pu->gestiontablas === 1)
                                    <li><hr class="dropdown-divider"></li>
                                    <li class="dropdown-header">Administración</li>
                                    <li>
                                        <a href="{{ route('empresas') }}" class="dropdown-item">
                                            <i class="ri-government-line me-2"></i>
                                            <span>Empresas</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('seccion-perfiles') }}" class="dropdown-item">
                                            <i class="ri-team-line me-2"></i>
                                            <span>Roles</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('register') }}" class="dropdown-item">
                                            <i class="ri-id-card-line me-2"></i>
                                            <span>{{ __('Registro Usuario') }}</span>
                                        </a>
                                    </li>
                                @endif
                                
                                <li><hr class="dropdown-divider"></li>
                                <li class="dropdown-header">Organización</li>
                                <li>
                                    <a href="{{ route('departamentos') }}" class="dropdown-item">
                                        <i class="ri-store-line me-2"></i>
                                        <span>Departamentos</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('puestos') }}" class="dropdown-item">
                                        <i class="ri-briefcase-line me-2"></i>
                                        <span>Puestos</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        
                        <!-- Informe Link -->
                        @if ($pu->visualizarinformes === 1)
                            <li class="nav-item">
                                <a href="{{ route('informes') }}" class="nav-link">
                                    <i class="ri-file-chart-line me-2"></i>Informe
                                </a>
                            </li>
                        @endif
                    @endforeach

                    <!-- User Dropdown -->
                    <li class=" dropdown custom-dropdown ms-lg-2">
                        <a href="#" class="nav-link dropdown-toggle user-dropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="d-flex align-items-center">
                                <div class="user-avatar me-2">
                                    <i class="ri-user-line"></i>
                                </div>
                                <span>{{ Auth::user()->name }}</span>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end custom-dropdown-menu">
                            <li class="dropdown-header">
                                <div class="text-center py-2">
                                    <div class="user-avatar-lg mb-2">
                                        <i class="ri-user-line"></i>
                                    </div>
                                    <strong>{{ Auth::user()->name }}</strong>
                                    <div class="small text-muted">{{ Auth::user()->email }}</div>
                                </div>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="ri-logout-box-line me-2"></i>
                                    <span>{{ __('Cerrar Sesión') }}</span>
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>
<!-- Navbar End -->