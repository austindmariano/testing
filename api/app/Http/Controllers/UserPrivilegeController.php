<?php

namespace App\Http\Controllers;

use App\ActivityLog;
use App\UserPrivilege;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;


class UserPrivilegeController extends Controller
{
    public function grantPrivilege(Request $request){
        //Check if user has permission to manage user accounts
        $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges(Auth::user()->id, Config::get('settings.user_management'), 'create_priv');

        if ($isAuthorized) {
            $request['last_updated_by'] = Auth::user()->id;
            $privilege = UserPrivilege::create($request->all());

            if($privilege != null){
                //record in activity log
                $activityLog = ActivityLog::create([
                    'user_id' => Auth::user()->id,
                    'activity' => 'Granted some privileges to user ' . $request['user_id'] . '.',
                    'time' => Carbon::now()
                ]);
                return response()->json([
                    'message' => 'Privileges successfully granted to user.'
                ]);
            }
        }else{
            //record in activity log
            $activityLog = ActivityLog::create([
                'user_id' => Auth::user()->id,
                'activity' => 'Attempted to grant some privileges to user ' . $request['user_id'] . '.',
                'time' => Carbon::now()
            ]);
            return response()->json([
                'message' => 'You are not authorized to grant privileges to user.'
            ], 401);
        }

    }

    public function updatePrivilege(Request $request, UserPrivilege $userprivilege){
        //Check if user has permission to manage user accounts
        $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges(Auth::user()->id, Config::get('settings.user_management'), 'update_priv');
        if ($isAuthorized) {
            $request['last_updated_by'] = Auth::user()->id;
            $userprivilege->update($request->all());
            if($userprivilege != null){
                //record in activity log
                $activityLog = ActivityLog::create([
                    'user_id' => Auth::user()->id,
                    'activity' => 'Updated the privileges of user ' . $request['user_id'] . '.',
                    'time' => Carbon::now()
                ]);
                return response()->json([
                    'message' => 'Privileges successfully updated.'
                ]);
            }
        }else{
            $activityLog = ActivityLog::create([
                'user_id' => Auth::user()->id,
                'activity' => 'Attempted to pdate the privileges of user ' . $request['user_id'] . '.',
                'time' => Carbon::now()
            ]);
            return response()->json([
                'message' => 'You are not authorized to update privileges.'
            ], 401);
        }

    }

    public function checkPrivileges($user, $activity, $privilege){
        return \DB::table('user_privileges')
            ->where('user_id', $user)
            ->where('activity_id', $activity)
            ->where($privilege, 1)
            ->exists();
    }
}
