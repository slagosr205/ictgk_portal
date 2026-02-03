<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Empresas;
use App\Models\PerfilModel;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
       // $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */

     public function registroNuevo(Request $request)
     {
         $data = $request->all();
         
         $this->validator($data);
         $this->create($data);

         return redirect()->back()->with(['registro'=>'se registro con exito el nuevo usuario']);
     }
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'empresa_id'=> $data['empresas'],
            'perfil_id'=>$data['rol'],
            'status'=>1
        ]);
    }

    public function showRegistrationForm(Request $request)
    {
        $empresas = Empresas::all();
        $roles=PerfilModel::all();
        $search = $request->input('search');
        $empresaId = $request->input('empresa_id');
        $perfilId = $request->input('perfil_id');
        $status = $request->input('status');
        $perPage = (int) $request->input('per_page', 15);
        if ($perPage <= 0) {
            $perPage = 15;
        }

        $usuarios=User::select('users.*','empresas.nombre','perfiles.perfilesdescrip')
        ->join('empresas','users.empresa_id','=','empresas.id')
        ->join('perfiles','users.perfil_id','=','perfiles.id')
        ->when($search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                    ->orWhere('users.email', 'like', "%{$search}%");
            });
        })
        ->when($empresaId, function ($query, $empresaId) {
            $query->where('users.empresa_id', $empresaId);
        })
        ->when($perfilId, function ($query, $perfilId) {
            $query->where('users.perfil_id', $perfilId);
        })
        ->when($status !== null && $status !== '', function ($query) use ($status) {
            $query->where('users.status', $status);
        })
        ->paginate($perPage)
        ->appends($request->query());
        return view('auth.register', ['empresas' => $empresas,'roles'=>$roles,'usuarios'=>$usuarios]);
    }

    public function updateStatus(Request $request, $id)
    {
        $user = User::find($id);
        $user->status = $request->input('status');
        $user->save();

        return redirect()->back()->with('success', 'User status updated successfully.');
    }

    public function updatePassword(Request $request, $id)
    {
        if (!auth()->check() || auth()->user()->perfil_id !== 1) {
            abort(403);
        }

        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::findOrFail($id);
        $user->password = Hash::make($request->input('password'));
        $user->save();

        return redirect()->back()->with('success', 'Contraseña actualizada correctamente.');
    }
}
