@extends('layouts.app')

    @section('content')
    @guest
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <img src="{{Storage::url('ABP.png')}}" alt="" class="img-fluid">
                </div>
            </div>
        </div>
        @else
        <div class="row">
            <div class="col-md-4">
                <div class="input-group mb-2">
                    
                    <input class="form-control border" id="dni" aria-describedby="inputGroupFileAddon04" placeholder="0000000000000" >
                    <button class="btn btn-outline-success" type="button" id="btndni">Buscar</button>
                </div>
            </div>
            <div class="col-md-4 ">
                <!-- Button trigger modal -->
               @foreach ($perfilUsers as $pu)
                   @if ($pu->ingreso===1)
                        <button type="button" class="btn btn-info btn-block mb-2" data-bs-toggle="modal" data-bs-target="#importInputCandidate">
                            Importacion de Ingresos
                        </button>
                   @endif
               
                    
                    
                @endforeach
            </div>
            <div class="col-md-4">
                @foreach ($perfilUsers as $pu)
                    @if ($pu->ingreso===1)
                        <a href="{{route('downloadTemplateIn')}}" class="btn btn-success btn-block mb-2 mx-2" ><i class="ri-file-excel-2-fill"></i> Plantilla de Ingresos</a>
                    @endif
                @endforeach
            </div>
            {{--punto de entrada informacion individual--}}
            <p class="py-4" id="fichapersonal"></p>
            {{--visualizacion del estado de importacion---}}
            <p class="py-4" id="importacionPersonal"></p>
        </div>
        <br>
        
        <x-modal-registro />
        <x-modal-ficha-personal />
        
       
    @if (session('mensaje'))
        
        @switch(session('icon'))
            @case('success')
                <p id="{{session('icon')}}" hidden >{{session('mensaje')}}</p>   
                
                @break
            @case('warning')
                <p id="{{session('icon')}}" hidden >{{session('mensaje')}}</p>    
                @break
            @default
                <p id="{{session('icon')}}" hidden > {{session('mensaje')}}</p>   
        @endswitch

      <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>{{session('mensaje')}}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div> 
        
    @endif
   {{-- @if (session('msjIngreso'))
        <div class="alert alert-success">
            <strong>{{session('msjIngreso')}}</strong>
        </div>
    @endif --}}

    @if (session('successmail'))
        <div class="alert alert-success">
            <strong>{{session('successmail')}}</strong>
        </div>
    @endif

    @if (session('errorEmail'))
        <div class="alert alert-danger">
            <strong>{{session('errorEmail')}}</strong>
        </div>
    @endif
    
    
    @endguest

    
    @endsection

    
