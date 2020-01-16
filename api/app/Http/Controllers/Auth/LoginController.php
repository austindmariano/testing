<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\ActivityLog;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
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
        $this->middleware('guest')->except('logout');
    }

    /**
     * Log users in if they have valid credentials
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request){
        if(Auth::attempt(['username' => $request->username, 'password' => $request->password])){
            $user = Auth::user();
            //record in activity log
            $activityLog = ActivityLog::create([
                'user_id' => $user->id,
                'activity' => 'Logged in to the system.',
                'time' => Carbon::now()
            ]);
            return response()->json([
                "message" => "successfully logged in.",
                "user" => $user,
                "token" => $user->createToken('Comteq Registration System')->accessToken
            ], 200);    //user is now logged in
        }else{
            return response()->json([
                "message" => "Unauthorized"
            ], 401);    //invalid credentials: unauthorized
        }
    }

    public function logout(){
        $user = Auth::user();
        if($user) {
            $user->token()->revoke();
            //record in activity log
            $activityLog = ActivityLog::create([
                'user_id' => $user->id,
                'activity' => 'Logged out of the system.',
                'time' => Carbon::now()
            ]);
            return response()->json([
                "message" => "successfully logged out."
            ], 200);    //user is now logged out
        }else{
            return response()->json([
                "message" => "Unauthorized"
            ], 401);    //invalid credentials: unauthorized
        }
    }
}
