@extends('layouts.app')

@section('content')
<div class="container-fluid d-flex align-items-center justify-content-center min-vh-100 py-6 mt-12">
    <div class="row  justify-content-center">
        
        @if(session('session_expired'))
            <div class="col-12 ">
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="ri-time-line me-2"></i>
                    {{ session('session_expired') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        @endif

        <div class="col-12 ">
            <div class="card shadow-lg sombre login-card-modern">
                <div class="card-header text-center bg-white border-0 pt-4 pb-3">
                    <img src="{{ Storage::url('logo__Altia.svg') }}" alt="Logo" class="img-fluid mb-3 login-logo">
                    <h4 class="text-dark mb-0">{{ __('Login') }}</h4>
                </div>

                <div class="card-body p-4">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label text-dark">
                                <strong>{{ __('Ingresa tu cuenta') }}</strong>
                            </label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="ri-mail-line"></i>
                                </span>
                                <input 
                                    id="email" 
                                    type="email" 
                                    class="form-control border-start-0 @error('email') is-invalid @enderror" 
                                    name="email" 
                                    value="{{ old('email') }}" 
                                    placeholder="correo@ejemplo.com"
                                    required 
                                    autocomplete="email" 
                                    autofocus
                                >
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label text-dark">
                                <strong>{{ __('Clave') }}</strong>
                            </label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="ri-lock-line"></i>
                                </span>
                                <input 
                                    id="password" 
                                    type="password" 
                                    class="form-control border-start-0 @error('password') is-invalid @enderror" 
                                    name="password" 
                                    placeholder="••••••••"
                                    required 
                                    autocomplete="current-password"
                                >
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <!-- Remember Me -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input 
                                    class="form-check-input" 
                                    type="checkbox" 
                                    name="remember" 
                                    id="remember" 
                                    {{ old('remember') ? 'checked' : '' }}
                                >
                                <label class="form-check-label text-dark" for="remember">
                                    {{ __('Recordarme') }}
                                </label>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid gap-3 mb-3">
                            <button type="submit" class="btn btn-info btn-lg">
                                <i class="ri-login-box-line me-2"></i>{{ __('Acceder') }}
                            </button>

                            @if (Route::has('password.request'))
                                <a class="btn btn-link text-decoration-none" href="{{ route('password.request') }}">
                                    <i class="ri-question-line me-1"></i>{{ __('¿Se olvidó tu clave?') }}
                                </a>
                            @endif
                        </div>

                        <!-- General Errors -->
                        @if ($errors->any() && !$errors->has('email') && !$errors->has('password'))
                            <div class="alert alert-danger" role="alert">
                                <strong>Error:</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </form>
                </div>
            </div>

            <!-- Footer Info -->
            <div class="text-center mt-3">
                <small class="text-white" style="text-shadow: 0 2px 4px rgba(0,0,0,0.5);">
                    <i class="ri-shield-check-line me-1"></i>
                    © {{ date('Y') }} Altia. Sistema seguro y protegido.
                </small>
            </div>
        </div>
        
    </div>
</div>
@endsection