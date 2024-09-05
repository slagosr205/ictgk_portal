<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
class LoginController extends Controller
{
    //
    use AuthenticatesUsers;
    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function attemptLogin(Request $request)
    {
        $credentials = $this->credentials($request);

        $user = Auth::getProvider()->retrieveByCredentials($credentials);
       
        if ($user) {
            Auth::login($user, $request->filled('remember'));
            
            return redirect()->intended('/');
        }else{
           dd($user);
        }

        return false;
    }

}
