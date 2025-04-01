<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    //
    use AuthenticatesUsers;
    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware(['guest','update.last_session'])->except('logout');
    }

    protected function attemptLogin(Request $request)
    {
        $credentials = $this->credentials($request);

        $user = Auth::getProvider()->retrieveByCredentials($credentials);
       
        if ($user && Hash::check($request->password,$user->password)) {
            Auth::login($user, $request->filled('remember'));
            Log::info($user);
           
           /* $user2 = Auth::user();
            $user2->last_session=now();
            $user2->save();*/
            return redirect()->intended('/');
        }else{
           //dd(password_verify($request->password,$user->password));
          $valor=Hash::make('rodriguez');
          dd(Hash::check('rodriguez',$valor),$valor);
          Log::info('no llego');
        }

        return false;
    }

}
