@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border">
                <h3 class="card-header text-center">{{ __('Registrar Nuevo Usuario') }}</h3>

                <div class="card-body">
                    <form method="POST" action="{{ route('register2') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Nombre') }}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control border @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Correo electronico') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control border @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end ">{{ __('Clave') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control border @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-end">{{ __('Confirme Clave') }}</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control border" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="txtempresas" class="col-md-4 col-form-label text-md-end">{{ __('Seleccione Empresa:') }}</label>

                            <div class="col-md-6">
                                <div class="input-group mb-3">
                                    <select name="empresas" id="empresas" class="form-control border">
                                        <option value=""><------------></option>
                                        @foreach ($empresas as $empresa)
                                            <option value="{{$empresa['id']}}">{{$empresa['nombre']}}</option>
                                        @endforeach
                                    </select>
                                    <button class="btn btn-outline-secondary" type="button" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                                        <strong>+</strong> 
                                     </button>
                                </div>
                               
                                <div class="col-md-6">
                                    <label for="rol">Seleccione un rol:</label>
                                    <div class="input-group mb-3">
                                        
                                        <select name="rol" id="rol" class="form-control border">
                                            <option value=""><------------></option>
                                            @foreach ($roles as $rol)
                                                <option value="{{$rol['id']}}">{{$rol['perfilesdescrip']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                  
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Register') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<x-modal-empresas />
<div class="row">
    <div class="col-md-12">
        <table class="table table-striped mt-4 border" id="dtUsuarios">
            <thead>
            <tr>
                <th>ID</th>
                <th>Usuarios</th>
                <th>cuenta</th>
                <th>Empresa</th>
                <th>Perfil</th>
                <th>Fecha de Creación</th>
                <th>Fecha de Actualización</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($usuarios as $us)
                <tr>
                    <td>{{$us->id}}</td>
                    <td>{{$us->name}}</td>
                    <td>{{$us->email}}</td>
                    <td>{{$us->nombre}}</td>
                    <td>{{$us->perfilesdescrip}}</td>
                    <td>{{\Carbon\Carbon::parse($us->created_at)->isoFormat('LL LTS')}}</td>
                    <td>{{\Carbon\Carbon::parse($us->updated_at)->isoFormat('LL LTS')}}</td>
                    
                    <td>
                        <form id="update-status-form-{{ $us->id }}" action="{{ route('users.updateStatus', $us->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <select name="status" class="form-select" onchange="document.getElementById('update-status-form-{{ $us->id }}').submit();">
                                <option value="1" {{ $us->status == 1 ? 'selected' : '' }}>Activo</option>
                                <option value="0" {{ $us->status == 0 ? 'selected' : '' }}>Inactivo</option>
                            </select>
                        </form>
                    </td>
                   
                    
                </tr>
            @endforeach
        </tbody>
        </table>
    </div>
</div>

@if (session('registro'))
    <div class="row">
        <div class="col-md-4">
            <div class="alert alert-success">
                <strong>{{session('registro')}}</strong>
            </div>
        </div>
    </div>
    
@endif
@endsection
