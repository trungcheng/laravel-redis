<?php

namespace App\Http\Controllers\Auth;

use Auth;
use Hash;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

class AuthController extends Controller
{

    protected $redirectLogin = '/';

    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'getLogout']);
    }

    public function getLogin()
    {
        return view('layouts.login');
    }

    public function postLogin(LoginRequest $request)
    {
        $remember = $request->get('remember_me') == 'on' ? true : false;
        if (Auth::attempt(['email' => $request->get('email'), 'password' => $request->get('password'), 'status' => 'Active'], $remember)) {
            return redirect()->intended($this->redirectPath());
        } else {
            return redirect($this->redirectLogin)->withErrors(trans('auth.login_error'));
        }
    }

    public function getLogout()
    {
        Auth::logout();
        return redirect($this->redirectLogin);
    }
}
