<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use App\User;
use App\ActivityLog;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

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
     * Creates a new user account
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request){
        //Check if user has permission to create new user accounts
        $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges(Auth::user()->id, Config::get('settings.user_management'), 'create_priv');

        if($isAuthorized){
            //validate request
            $validator = Validator::make($request->all(),[
                'username' => 'required|string|unique:users',
                'password' => 'required|min:6|confirmed',
                'email' => 'required|email|unique:users',
                'first_name' => 'required|string',
                'middle_name' => 'nullable|string',
                'last_name' => 'required|string',
                'role' => 'required|string'
            ]);

            //if validation fails
            if ($validator->fails()){
                return response()->json([
                    'message' => 'Failed to create a new user account.',
                    'errors' => $validator->errors()
                ],400);    //Bad request
            }

            //create a new user account
            $userData = $request->all();
            $userData['password'] = Hash::make($request->password);
            $userData['last_updated_by'] = Auth::user()->id;
            $user = User::create($userData);
            if($user){
                //record in activity log
                $activityLog = ActivityLog::create([
                    'user_id' => Auth::user()->id,
                    'activity' => 'Created user account for ' . $user->username . '.',
                    'time' => Carbon::now()
                ]);
                return response()->json([
                    'message' => 'User account successfully created.'
                ],200);    //user created
            }
        }else{
            //record in activity log
            $activityLog = ActivityLog::create([
                'user_id' => Auth::user()->id,
                'activity' => 'Attempted to create user account.',
                'time' => Carbon::now()
            ]);
            return response()->json([
                'message' => 'You are not authorized to create new user accounts.'
            ],401);
        }
    }

}
