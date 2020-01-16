<?php

namespace App\Http\Controllers;

use App\Track;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Validator;

use App\ActivityLog;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;

class TrackController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $user = Auth::user();
      //Check if user has permission to view track records.
      $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges($user->id, Config::get('settings.track_management'), 'read_priv');
      if ($isAuthorized) {
        $tracks = Track::orderBy('id', 'DESC')->with('strands')->get();
          //record in activity log
          $activityLog = ActivityLog::create([
              'user_id' => $user->id,
              'activity' => 'Viewed the list of tracks.',
              'time' => Carbon::now()
          ]);
          $tracks = Track::select('*')
          ->orderBy('id', 'DESC')
          ->with('strands')
          ->get();
        return $tracks;
      }else{
          //record in activity log
          $activityLog = ActivityLog::create([
              'user_id' => $user->id,
              'activity' => 'Attempted to view the list of tracks.',
              'time' => Carbon::now()
          ]);
          return response()->json([
              'message' => 'You are not authorized to view track records.'
          ],401);     //401: Unauthorized
      }

    } // end of function index()

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {  $user = Auth::user();
      // check if user have the priviledge to create track record.
      $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges($user->id, Config::get('settings.track_management'), 'create_priv');
      if ($isAuthorized) {
        //data validation
        $validator = Validator::make($request->all(),[
          'track_code' => 'required|unique:tracks,track_code',
          'track_desc' => 'required|string',
          'active' => 'required|numeric',
        ]);

        // check if data if validator fails
        if ($validator->fails()) {
          return response()
          ->json([
            'message' => 'Failed to create new track record.',
            'errors' => $validator->errors()
          ], 400); // 400: Bad request
        }
        else {
          $track_data = $request->all();
          $track_data['last_updated_by'] = Auth::user()->id;
          try {
            $track = Track::create($track_data);
            // check if record is successfully created.
            if ($track) {
              //record in activity log
              $activityLog = ActivityLog::create([
                  'user_id' => $user->id,
                  'activity' => 'Created a new track.',
                  'time' => Carbon::now()
              ]);
              return response()->json(['message' => 'New track record successfully created.'], 200);
            }else {
              return response()->json(['message' => 'Failed to create new track record.'], 500); // server error
            }
          } catch (Exception $e) {
            report($e);
            return false;
          }
        }
      }else{
          //record in activity log
          $activityLog = ActivityLog::create([
              'user_id' => $user->id,
              'activity' => 'Attempted to create a new track.',
              'time' => Carbon::now()
          ]);
          return response()->json([
              'message' => 'You are not authorized to create track records.'
          ],401); // 401: Unauthorized
      }
    } // end of function store()

    /**
     * Display the specified resource.
     *
     * @param  \App\Track  $track
     * @return \Illuminate\Http\Response
     */
    public function show(Track $track)
    {
      $user = Auth::user();
      //Check if user has permission to view instructors
      $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges($user->id, Config::get('settings.track_management'), 'read_priv');

      if($isAuthorized){
        $activityLog = ActivityLog::create([
            'user_id' => $user->id,
            'activity' => 'Viewed the details of ' . $track->track_desc . '.',
            'time' => Carbon::now()
        ]);
        $track_data = Track::select('*')
        ->where('id', $track->id)
        ->with('strands')
        ->get();

        return $track_data  ;
      }else{
          //record in activity log
          $activityLog = ActivityLog::create([
              'user_id' => $user->id,
              'activity' => 'Attempted to view the details of ' . $track->track_desc . '.',
              'time' => Carbon::now()
          ]);
          return response()->json([
              'message' => 'You are not authorized to view track records.'
          ],401); // 401: Unauthorized
      }

    } // end of function show()

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Track  $track
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Track $track)
    {
      $user = Auth::user();
      //  check if user have the priviledge to update track record.
      $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges($user->id, Config::get('settings.track_management'), 'update_priv');

      if($isAuthorized){
        //data validation
        $validator = Validator::make($request->all(),[
          'track_code' => 'unique:tracks,track_code',
          'track_desc' => 'string',
          'active' => 'numeric',
        ]);

        // check if data if validator fails
        if ($validator->fails()) {
          return response()
          ->json([
            'message' => 'Failed to update track record.',
            'errors' => $validator->errors()
          ], 400); // 400: Bad request
        }
        else {
          $track_data = $request->all();
          $track_data['last_updated_by'] = Auth::user()->id;
          try {
             $check = $track->update($track_data);
            // check if record is successfully updated.
            if ($check) {
              //record in activity log
              $activityLog = ActivityLog::create([
                  'user_id' => $user->id,
                  'activity' => 'Updated the track ' . $track->track_desc . '.',
                  'time' => Carbon::now()
              ]);
              return response()->json(['message' => 'Track record successfully updated.'], 200);
            }else {
              return response()->json(['message' => 'Failed to update track record.'], 500); // server error
            }
          } catch (Exception $e) {
            report($e);
            return false;
          }
        }
      }else{
          //record in activity log
          $activityLog = ActivityLog::create([
              'user_id' => $user->id,
              'activity' => 'Attempted to update the details of ' . $track->track_desc . '.',
              'time' => Carbon::now()
          ]);
          return response()->json([
              'message' => 'You are not authorized to update track records.'
          ],401); // 401: Unauthorized
      }

    } // end of function update()

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Track  $track
     * @return \Illuminate\Http\Response
     */
    public function destroy(Track $track)
    {
      $user = Auth::user();
      //  check if user has the priviledge to delete track record.
      $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges($user->id, Config::get('settings.track_management'), 'delete_priv');
      if($isAuthorized){
        try {
          $track->delete();
          $activityLog = ActivityLog::create([
              'user_id' => $user->id,
              'activity' => 'Deleted the track ' . $track->track_desc . '.',
              'time' => Carbon::now()
          ]);
          return response()->json(['message' => 'Track record successfully deleted.'], 200);
        } catch (Exception $e) {
          report($e);
          return false;
        }
      }else{
          //record in activity log
          $activityLog = ActivityLog::create([
              'user_id' => $user->id,
              'activity' => 'Attempted to delete the track ' . $track->track_desc . '.',
              'time' => Carbon::now()
          ]);
          return response()->json([
              'message' => 'You are not authorized to delete track records.'
          ],401); // 401: Unauthorized
      }

    } // end of function destroy()
}
