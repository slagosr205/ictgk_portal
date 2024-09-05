@extends('layouts.app')

@section('content')

    <div class="row flex-column justify-content-center align-items-center text-white">
        
            <div class="shadow-lg card mb-5 p-5 col-md-3 sombra " >
                <div class="card-header text-center border bg-gray-200" >
                    <img src="{{Storage::url('GV_LogoFinal_pantone.png')}}" alt="Logo" width="80%">
                    <h4 class="">{{ __('Login') }}</h4>
                </div>

                <div class="card-body mt-3">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-3 ">
                            <label for="email" class="col-form-label "><strong>{{ __('Ingresa tu cuenta') }}</strong></label>

                            
                                <input id="email" type="email" class="form-control border @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            
                        </div>

                        <div class="mb-3">
                            <label for="password" class="col-form-label "><strong>{{ __('Clave') }}</strong></label>

                            
                                <input id="password" type="password" class="form-control border @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6 ">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label " for="remember">
                                        {{ __('Recordarme') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            
                                <button type="submit" class="btn btn-info" >
                                    {{ __('Acceder') }}
                                </button>

                                @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Se olvidó tu clave?') }}
                                    </a>
                                @endif
                            
                        </div>
                    </form>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        
    </div>
    @if(session('session_expired'))
    <div class="alert alert-warning">
        {{ session('session_expired') }}
    </div>
    @endif
    
@endsection

    
