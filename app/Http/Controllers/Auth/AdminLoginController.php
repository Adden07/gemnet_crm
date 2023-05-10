<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\AdminAcl;
use App\Models\LoginFailLog;
use Illuminate\Support\Facades\RateLimiter;


class AdminLoginController extends Controller 
{
    use AuthenticatesUsers;

    protected $redirectTo = '/admin/login';

    public function __construct()
    {
        $this->middleware('guest:admin', ['except' => ['logout']]);
    }

    public function showLoginForm()
    {   
        return view('auth.admin.login');
    }

    public function login(\Illuminate\Http\Request $request)
    {   
        $a = $this->validate($request, [
            'username' => 'required|string',
            'password' => 'required'
        ]);

        if (Auth::guard('admin')->attempt(['username' => $request->username, 'password' => $request->password], $request->remember)) {
            
            RateLimiter::clear($request->ip().$request->username);//clear rate limiter

            if(Auth::guard('admin')->user()->is_active == 'disabled' || Auth::guard('admin')->user()->is_active == 'disabled_by_admin'){
                Auth::guard('admin')->logout();    
                return redirect()
                ->back()
                ->withInput($request->only($request->only('username', 'remember')))
                ->withErrors(['is_active' => 'Sorry you can not login your account is disabled']);
            }
            
            if(AdminAcl::where('admin_id',Auth::guard('admin')->user()->id)->exists()){
                if(AdminAcl::where('ip',$request->ip())->where('admin_id',Auth::guard('admin')->user()->id)->doesntExist()){
                    Auth::guard('admin')->logout();
                    
                    return redirect()
                    ->back()
                    ->withInput($request->only($request->only('username', 'remember')))
                    ->withErrors(['is_active' => "You are Not Authorized to Login with this IP (  {$request->ip()}  )"]);
                }
            }

            
            return redirect()->intended(url('/'));
        }else{
            
            LoginFailLog::create(['username'=>$request->username, 'ip'=>$request->ip()]); 

            RateLimiter::attempt($request->ip().$request->username, $perMinute=5,function(){},3600);
            if (RateLimiter::tooManyAttempts($request->ip().$request->username, $perMinute = 5)) {
                $seconds = RateLimiter::availableIn(($request->ip().$request->username));
                $seconds = gmdate("i:s", $seconds);
                return redirect()
                ->back()
                ->withInput($request->only($request->only('username', 'remember')))
                ->withErrors(['username'=>"Too many login attemps, you may try again $seconds"]);
            }
        }
        return redirect()
            ->back()
            ->withInput($request->only($request->only('username', 'remember')))
            ->withErrors(['username'=>'These credentials do not match our records.']);
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect(route('admin.login'));
    }
}
