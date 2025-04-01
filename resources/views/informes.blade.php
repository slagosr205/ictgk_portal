@extends('layouts.app')

@section('informes')

<div class="row">
    <div class="col-md-2 py-2">
        <button type="button" class="btn btn-outline-dark" id="btnContrast"><i class="ri-contrast-2-fill"></i></button>
    </div>
</div>
<div class="row px-2">
    <div class="col-lg-3 col-md-6 col-sm-6 ">
        <div class="card  mb-2 ">
            <div class="card-header p-3 pt-2">
            <div class="icon icon-lg icon-shape bg-gradient-primary shadow-primary shadow text-center border-radius-xl mt-n4 position-absolute">
                <i class="ri-team-fill"></i>
            </div>
                <div class="text-end pt-1">
                    <p class="text-sm mb-0 text-capitalize">Población Activa</p>
                    <h4 class="mb-0">{{$poblacionTotal[0]->poblacionActiva}}</h4>
                </div>
                </div>
                    <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <p class="mb-0"><span class="text-success text-sm font-weight-bolder">+55% </span>mas que el mes pasado</p>
                </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 col-sm-6 ">
        <div class="card  mb-2">
            <div class="card-header p-3 pt-2">
            <div class="icon icon-lg icon-shape bg-gradient-primary shadow-primary shadow text-center border-radius-xl mt-n4 position-absolute">
            <i class="ri-user-received-2-fill"></i>
            </div>
                <div class="text-end pt-1">
                    <p class="text-sm mb-0 text-capitalize">Egresos Totales</p>
                    <h4 class="mb-0">{{$egresosTotales[0]->egresos}}</h4>
                </div>
                </div>
                    <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <p class="mb-0"><span class="text-success text-sm font-weight-bolder">+10% </span>mas que el mes pasado</p>
                </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 col-sm-6">
        <div class="card  mb-2">
            <div class="card-header p-3 pt-2">
            <div class="icon icon-lg icon-shape bg-gradient-primary shadow-primary shadow text-center border-radius-xl mt-n4 position-absolute">
                <i class="ri-hourglass-fill"></i>
            </div>
                <div class="text-end pt-1">
                    <p class="text-sm mb-0 text-capitalize">Prom. Antiguedad</p>
                    <h4 class="mb-0">{{$promedioAntiguedad[0]->promedio_antiguedad}} mes</h4>
                </div>
                </div>
                    <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <p class="mb-0"><span class="text-success text-sm font-weight-bolder">+55% </span>than last week</p>
                </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 col-sm-6">
        <div class="card  mb-2">
            <div class="card-header p-3 pt-2">
            <div class="icon icon-lg icon-shape bg-gradient-primary shadow-primary shadow text-center border-radius-xl mt-n4 position-absolute">
                <i class="ri-bar-chart-fill"></i>
            </div>
                <div class="text-end pt-1">
                    <p class="text-sm mb-0 text-capitalize">Prom. Ingreso Men.</p>
                    <h4 class="mb-0">{{$promedioIngresos[0]->promedioIngreso}} colab.</h4>
                </div>
                </div>
                    <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <p class="mb-0"><span class="text-success text-sm font-weight-bolder">+55% </span>than last week</p>
                </div>
        </div>
    </div>
</div>

<div class="row ">

    <div class="col-md-6 card bg-gradient-dark shadow-dark border-radius-lg py-3 px-2 ">
        <h3 class="text-center card-title">Ingresos por Empresas</h3>
        <div class="card-body">
            <canvas class="grafico" id="ingresoxempresa" >Gráfico 1</canvas>
        </div>
        
    </div>
    
    <div class="col-md-6 bg-gradient-dark shadow-dark border-radius-lg py-3 pe-1">
    <h3 class="text-center">Egresos por Empresas</h3>
        <canvas class="grafico" id="egresoxempresa">Gráfico 2</canvas>
    </div>
</div>
<br>
<div class="row">

    <div class="col-md-6 bg-gradient-dark shadow-dark border-radius-lg py-3 pe-1">
        <h3 class="text-center card-title">Renuncias por Genero</h3>
        <canvas class="grafico" id="renunciaxrangoedad">Gráfico 2</canvas>
    </div>
    <div class="col-md-6 bg-gradient-dark shadow-dark border-radius-lg py-3 pe-1">
        <h3 class="text-center card-title">Estado por Genero/Edad</h3>
        <canvas class="grafico px-4" id="estadopoblacion">Gráfico 2</canvas>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-6 bg-gradient-dark shadow-dark border-radius-lg py-3 pe-1">
        <h3 class="text-center card-title">Ingresos/Egresos por periodos</h3>
    <select   id="periodSelector">
            <option value="day">Día</option>
            <option value="month">Mes</option>
            <option value="bimester">Bimestre</option>
            <option value="trimester">Trimestre</option>
            <option value="year">Año</option>
        </select>
        <input  type="text" id="datePicker" placeholder="Select Date Range">
        <canvas class="grafico px-4" id="grafica6">Gráfico 2</canvas>
    </div>

    <div class="col-md-6 bg-gradient-dark shadow-dark border-radius-lg py-3 pe-1 p-2">
        <h3 class="text-center card-title">Monitoreo de Sesion</h3>
       
            <table id="usersTable" class="display">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Empresa ID</th>
                        <th>Perfil ID</th>
                        <th>Última Sesión</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Los datos se cargarán aquí automáticamente -->
                </tbody>
            </table>
        
    </div>

</div>

@endsection
