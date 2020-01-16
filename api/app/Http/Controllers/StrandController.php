<?php

namespace App\Http\Controllers;

use App\Strand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Validator;

use App\ActivityLog;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;

class StrandController extends Controller
{
    /**
     * Display a listing of all strand records.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $user = Auth::user();
      //Check if user has permission to view strands.
      $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges($user->id, Config::get('settings.strand_management'), 'read_priv');
      if ($isAuthorized) {
        $strands = Strand::all();
        //record in activity log
        $activityLog = ActivityLog::create([
            'user_id' => $user->id,
            'activity' => 'Viewed the list of strands.',
            'time' => Carbon::now()
        ]);
        return $strands;
      }else{
          //record in activity log
          $activityLog = ActivityLog::create([
              'user_id' => $user->id,
              'activity' => 'Attempted to view the list of strands.',
              'time' => Carbon::now()
          ]);
          return response()->json([
              'message' => 'You are not authorized to view strand records.'
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
    {
      $user = Auth::user();
      //check if user have the priviledge to create strand record.
      $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges($user->id, Config::get('settings.strand_management'), 'create_priv');
      if ($isAuthorized) {
        //data validation
        $validator = Validator::make($request->all(),[
          'strand_code' => 'required|unique:strands,strand_code',
          'strand_desc' => 'required|string',
          'track_id' => 'required|numeric',
          'active' => 'required|numeric'
        ]);

        // check if data if validator fails
        if ($validator->fails()) {
          return response()
          ->json([
            'message' => 'Failed to create new strand record.',
            'errors' => $validator->errors()
          ], 400); // 400: Bad request
        }
        else {
          $strand_data = $request->all();
          $strand_data['last_updated_by'] = Auth::user()->id;
          try {
            $strand = Strand::create($strand_data);
            // check if record is successfully created.
            if ($strand) {
              //record in activity log
              $activityLog = ActivityLog::create([
                  'user_id' => $user->id,
                  'activity' => 'Created a new strand.',
                  'time' => Carbon::now()
              ]);
              return response()->json(['message' => 'New strand record successfully created.'], 200);
            }else {
              return response()->json(['message' => 'Failed to create new strand record.'], 500); // server error
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
              'activity' => 'Attempted to create a new strand.',
              'time' => Carbon::now()
          ]);
          return response()->json([
              'message' => 'You are not authorized to create strand records.'
          ],401); // 401: Unauthorized
      }

    } // end of function store()

    /**
     * Display the specified resource.
     *
     * @param  \App\Strand  $strand
     * @return \Illuminate\Http\Response
     */
    public function show(Strand $strand)
    {
      $user = Auth::user();
      //Check if user has permission to view strand records.
      $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges($user->id, Config::get('settings.strand_management'), 'read_priv');
      if($isAuthorized){
        $activityLog = ActivityLog::create([
            'user_id' => $user->id,
            'activity' => 'Viewed the details of ' . $strand->strand_desc . '.',
            'time' => Carbon::now()
        ]);
        return $strand;
      }else{
          //record in activity log
          $activityLog = ActivityLog::create([
              'user_id' => $user->id,
              'activity' => 'Attempted to view the details of ' . $strand->strand_desc . '.',
              'time' => Carbon::now()
          ]);
          return response()->json([
              'message' => 'You are not authorized to view strand records.'
          ],401); // 401: Unauthorized
      }

    } // end of function show()

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Strand  $strand
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Strand $strand)
    {
      $user = Auth::user();
      // check if user have the priviledge to update strand record.
      $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges($user->id, Config::get('settings.strand_management'), 'update_priv');
      if($isAuthorized){
        //data validation
        $validator = Validator::make($request->all(),[
          'strand_code' => 'unique:strands,strand_code',
          'strand_desc' => 'string',
          'track_id' => 'numeric',
          'active' => 'numeric'
        ]);

        // check if data if validator fails
        if ($validator->fails()) {
          return response()
          ->json([
            'message' => 'Failed to update strand record.',
            'errors' => $validator->errors()
          ], 400); // 400: Bad request
        }
        else {
          $strand_data = $request->all();
          $strand_data['last_updated_by'] = Auth::user()->id;
          try {
             $check = $strand->update($strand_data);
            // check if record is successfully updated.
            if ($check) {
              $activityLog = ActivityLog::create([
                  'user_id' => $user->id,
                  'activity' => 'Updated the strand ' . $strand->strand_desc . '.',
                  'time' => Carbon::now()
              ]);
              return response()->json(['message' => 'Strand record successfully updated.'], 200);
            }else {
              return response()->json(['message' => 'Failed to update strand record.'], 500); // server error
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
              'activity' => 'Attempted to update the details of ' . $strand->strand_desc . '.',
              'time' => Carbon::now()
          ]);
          return response()->json([
              'message' => 'You are not authorized to update strand records.'
          ],401); // 401: Unauthorized
      }

    } // end of function update()

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Strand  $strand
     * @return \Illuminate\Http\Response
     */
    public function destroy(Strand $strand)
    {
      $user = Auth::user();
      //check if user has the priviledge to delete strand record.
      $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges($user->id, Config::get('settings.strand_management'), 'delete_priv');
      if($isAuthorized){
        try {
          $strand->delete();
          $activityLog = ActivityLog::create([
              'user_id' => $user->id,
              'activity' => 'Deleted the strand ' . $strand->strand_desc . '.',
              'time' => Carbon::now()
          ]);
          return response()->json(['message' => 'Strand record successfully deleted.'], 200);
        } catch (Exception $e) {
          report($e);
          return false;
        }
      }else{
          //record in activity log
          $activityLog = ActivityLog::create([
              'user_id' => $user->id,
              'activity' => 'Attempted to delete the strand ' . $strand->strand_desc . '.',
              'time' => Carbon::now()
          ]);
          return response()->json([
              'message' => 'You are not authorized to delete strand records.'
          ],401); // 401: Unauthorized
      }

    } // end of function destroy()
}
