<?php

use App\Exports\PuestosExport;
use App\Http\Controllers\CandidatosController;
use App\Http\Controllers\InformesController;
use App\Models\Candidatos;
use App\Models\Egresos;
use App\Models\PerfilModel;
use App\Models\PuestosModel;
use Faker\Provider\ar_EG\Company;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;
use League\Csv\Writer;
use App\Exports\ExportTemplate;
use App\Exports\ExportTemplateCandidate;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BloqueoController;
use App\Models\Empresas;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
/*Route::post('login', [LoginController::class, 'attemptLogin'])->name('login');
Route::get('register',function(){
  $empresas = Empresas::all();
        $roles=PerfilModel::all();
        $usuarios=User::select('users.*','empresas.nombre','perfiles.perfilesdescrip')->join('empresas','users.empresa_id','=','empresas.id')
        ->join('perfiles','users.perfil_id','=','perfiles.id')
        ->get();
  return view('auth.register',['empresas' => $empresas,'roles'=>$roles,'usuarios'=>$usuarios]);
})->name('register');
Route::post('logout',function(){
  Auth::logout();
  return redirect('/');
})->name('logout');*/
Auth::routes();
Route::get('/', function () {
  // Verifica si el usuario está autenticado
  if (Auth::check()) {
      // Obtén el perfil del usuario autenticado
      $perfilUsers = PerfilModel::where('id', Auth::user()->perfil_id)->get();
      // Redirige a la página de inicio con el perfil del usuario
      return view('home')->with('perfilUsers', $perfilUsers);
  }

  // Redirige a la página de inicio de sesión si el usuario no está autenticado
  return view('auth.login');
});


Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware(['auth','check.session'])->group(function () {
Route::get('/dmtables',function(){
  $data=collect();
  $perfil=PerfilModel::select('perfilesdescrip')->where('id',auth()->user()->perfil_id)->get();
  
  if($perfil[0]['perfilesdescrip']==='admin')
  {
    $data = Candidatos::all()->map(function ($candidato) {
      // Obtenemos la identidad del candidato actual
      $identidad = $candidato->identidad;
  
      // Realizamos la consulta para obtener el registro correspondiente en la tabla 'Egresos'
      $egreso = Egresos::select('ComenProhibir',DB::raw('activo as activo_ingreso'))->where('identidad', $identidad)
      ->where('activo','s')
      ->first();
  
      // Verificamos si se encontró un registro en Egresos para este candidato
      if ($egreso) {
          // Si se encontró, asignamos el valor de 'ComenProhibir' al candidato
          $candidato->comentarios = json_decode($candidato->comentarios,true);
          $candidato->activo_ingreso=$egreso->activo_ingreso;
      } else {
          // Si no se encontró, puedes asignar un valor por defecto o nulo según necesites
          $candidato->comentarios = json_decode($candidato->comentarios,true); // Por ejemplo, asignamos null si no se encuentra
      }
        
      return $candidato;
  });


  }else{
    $data=Candidatos::select('candidatos.*',DB::raw('egresos_ingresos.activo as activo_ingreso'))->join('egresos_ingresos','egresos_ingresos.identidad','=','candidatos.identidad')
    ->where('egresos_ingresos.id_empresa','=',auth()->user()->empresa_id)
   ->where('egresos_ingresos.activo','=','s')
    ->get();
  }
 
 
  return view('Table-inf',compact('data'));
})->name('candidatos');

Route::post('/carga-masiva',[App\Http\Controllers\CandidatosController::class,'recibirCsvCandidatos'])->name('cargar');
Route::get('/informes',[App\Http\Controllers\InformesController::class, 'GetInformes'])->name('informes');
Route::get('/infopersonal/{dni}',[App\Http\Controllers\CandidatosController::class, 'GetIndividualInfo'])->name('infopersonal');
Route::post('/actualizacion-ficha',[App\Http\Controllers\CandidatosController::class,'Actualizacion_ficha'])->name('actualizacion_ficha');

/**Recibir ingresos masivos, hara validacion de todos los ingresos 
 * 1. Validar que existan, sino existen en la tabla candidatos entonces que se pueda registrar primero
 * 2. Si existen en la tabla candidato que solo se agreguen en la tabla de ingresos
*/

Route::post('/ingresos-masivos',[App\Http\Controllers\CandidatosController::class,'importarIngresos'])->name('cargaIng');
Route::post('/ingresos',[App\Http\Controllers\CandidatosController::class,'hacerIngreso'])->name('hacerIgresos');
Route::post('/egresos',[App\Http\Controllers\CandidatosController::class,'hacerEgreso'])->name('hacerEgresos');
Route::post('/ingresos-nuevos',[App\Http\Controllers\CandidatosController::class,'insertarCandidato'])->name('insertarCandidato');



Route::get('/perfiles',[App\Http\Controllers\PerfilController::class,'show'])->name('seccion-perfiles');

Route::post('/registrar-perfil',[App\Http\Controllers\PerfilController::class,'create'])->name('registrarperfil');

Route::post('/update-role',[App\Http\Controllers\PerfilController::class,'update'])->name('updateRole');
/*Rutas de Empresas */
Route::get('/empresas',[App\Http\Controllers\EmpresasController::class,'index'])->name('empresas');
Route::post('/insert-company',[App\Http\Controllers\EmpresasController::class,'insertCompany'])->name('insertCompany');
Route::get('/consulting-company/{id}',[App\Http\Controllers\EmpresasController::class,'consultingCompany'])->name('consultingCompany');
Route::post('/update-company',[App\Http\Controllers\EmpresasController::class,'updateCompany'])->name('updateCompany');
Route::post('/update-company-state',[App\Http\Controllers\EmpresasController::class,'updateCompanyState'])->name('updateCompanyState');
/**Rutas de departamentos */
Route::get('/departamentos',[App\Http\Controllers\DepartamentosController::class,'index'])->name('departamentos');
Route::post('/insert-departament',[App\Http\Controllers\DepartamentosController::class,'insertDepartament'])->name('insertDepartament');
/*Rutas de Puestos */
Route::get('/puestos',[App\Http\Controllers\PuestosController::class,'index'])->name('puestos');

Route::get('/plantilla-ingresos',function(){
  //
 
  $excel=new ExportTemplate();
 

  return Excel::download($excel,'plantilla_ingresos.xlsx');
})->name('downloadTemplateIn')->middleware('verificar_perfil');

Route::get('/plantilla-candidatos',function(){
  $exportcandidate=new ExportTemplateCandidate();
  return Excel::download($exportcandidate,'plantilla_candidatos.xlsx');
})->name('downloadTemplateCandidate');



Route::post('/register2',[App\Http\Controllers\Auth\RegisterController::class,'registroNuevo'])->name('register2');

Route::post('/bloqueo',[App\Http\Controllers\CandidatosController::class,'lockCandidate'])->name('lockCandidate');
Route::post('/desbloqueo',[App\Http\Controllers\CandidatosController::class,'unlockCandidate'])->name('unlockCandidate');
Route::post('/solicitud-desbloqueo-recomendacion',[App\Http\Controllers\CandidatosController::class,'enviarSolicitudRecomendacion'])->name('SolicitudDesbRecom');
Route::post('/desbloquear-recomendacion',[App\Http\Controllers\CandidatosController::class,'desbloquearRecomendacion'])->name('desbloqueoRecomendacion');

Route::get('/consulta-puesto/{id}',[App\Http\Controllers\PuestosController::class,'dataPuestos'])->name('consultaPuesto');
Route::get('/consultaPuestosxEmpresas/{idEmpresas}',[App\Http\Controllers\PuestosController::class,'puestosxEmpresas'])->name('consultaPuestosxEmpresas');
Route::post('/insert-positions',[App\Http\Controllers\PuestosController::class,'create'])->name('insertPositions');
Route::post('/update-positions',[App\Http\Controllers\PuestosController::class,'updatePosition'])->name('updatePosition');

Route::post('/export-data-output',[App\Http\Controllers\CandidatosController::class,'exportarEgresos'])->name('exportOutput');
Route::post('/import-data-output',[App\Http\Controllers\CandidatosController::class,'importarEgresos'])->name('importOutput');
Route::get('/consulting-departament/{departamento_id}',[App\Http\Controllers\DepartamentosController::class,'show'])->name('consultingDepartament');
Route::post('/update-departament',[App\Http\Controllers\DepartamentosController::class,'updateDepartament'])->name('updateDepartament');
Route::patch('/users/{id}/update-status', [App\Http\Controllers\Auth\RegisterController::class, 'updateStatus'])->name('users.updateStatus');
Route::get('/ingresosxempresas',[App\Http\Controllers\InformesController::class,'GetData'])->name('ingresosxempresas');
Route::get('/egresosxempresas',[App\Http\Controllers\InformesController::class,'GetDataOut'])->name('egresosxempresas');
Route::get('/edadesxestado',[App\Http\Controllers\InformesController::class,'GetDataState'])->name('edadesxestado');
Route::get('/egresosxingresos',[App\Http\Controllers\InformesController::class,'IngresosxEgresos'])->name('egresosxingresos');
Route::get('/renunciasxGenero',[App\Http\Controllers\InformesController::class,'RenunciasxGenero'])->name('renunciasxGenero');
Route::get('/monitor-sesion',[App\Http\Controllers\InformesController::class,'GetLastSessionUser'])->name('monitorSesiones');

Route::post('/bloqueo-parqueo',[BloqueoController::class,'recibirBloqueos'])->name('blockPark');
});
 

   
/*use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SessionsController;
            

Route::get('/', function () {return redirect('sign-in');})->middleware('guest');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('sign-up', [RegisterController::class, 'create'])->middleware('guest')->name('register');
Route::post('sign-up', [RegisterController::class, 'store'])->middleware('guest');
Route::get('sign-in', [SessionsController::class, 'create'])->middleware('guest')->name('login');
Route::post('sign-in', [SessionsController::class, 'store'])->middleware('guest');
Route::post('verify', [SessionsController::class, 'show'])->middleware('guest');
Route::post('reset-password', [SessionsController::class, 'update'])->middleware('guest')->name('password.update');
Route::get('verify', function () {
	return view('sessions.password.verify');
})->middleware('guest')->name('verify'); 
Route::get('/reset-password/{token}', function ($token) {
	return view('sessions.password.reset', ['token' => $token]);
})->middleware('guest')->name('password.reset');

Route::post('sign-out', [SessionsController::class, 'destroy'])->middleware('auth')->name('logout');
Route::get('profile', [ProfileController::class, 'create'])->middleware('auth')->name('profile');
Route::post('user-profile', [ProfileController::class, 'update'])->middleware('auth');
Route::group(['middleware' => 'auth'], function () {
	Route::get('billing', function () {
		return view('pages.billing');
	})->name('billing');
	Route::get('tables', function () {
		return view('pages.tables');
	})->name('tables');
	Route::get('rtl', function () {
		return view('pages.rtl');
	})->name('rtl');
	Route::get('virtual-reality', function () {
		return view('pages.virtual-reality');
	})->name('virtual-reality');
	Route::get('notifications', function () {
		return view('pages.notifications');
	})->name('notifications');
	Route::get('static-sign-in', function () {
		return view('pages.static-sign-in');
	})->name('static-sign-in');
	Route::get('static-sign-up', function () {
		return view('pages.static-sign-up');
	})->name('static-sign-up');
	Route::get('user-management', function () {
		return view('pages.laravel-examples.user-management');
	})->name('user-management');
	Route::get('user-profile', function () {
		return view('pages.laravel-examples.user-profile');
	})->name('user-profile');
});*/