<?php

namespace App\Http\Controllers;

use App\StudentRequirement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Validator;

use App\ActivityLog;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;

class StudentRequirementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $user = Auth::user();
      //Check if user has permission to view student requirements records.
      $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges($user->id, Config::get('settings.requirements_management'), 'read_priv');
      if ($isAuthorized) {
        //record in activity log
        $activityLog = ActivityLog::create([
            'user_id' => $user->id,
            'activity' => 'Viewed the list of student requirements.',
            'time' => Carbon::now()
        ]);
         $requirements = StudentRequirement::orderBy('id', 'DESC')->get();
         return $requirements;

      }else{
          //record in activity log
          $activityLog = ActivityLog::create([
              'user_id' => $user->id,
              'activity' => 'Attempted to view the list of student requirements.',
              'time' => Carbon::now()
          ]);
          return response()->json([
              'message' => 'You are not authorized to view student requirements records.'
          ],401); // 401: Unauthorized
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
      //check if user have the priviledge to create student requirements record.
      $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges($user->id, Config::get('settings.requirements_management'), 'create_priv');
      if ($isAuthorized) {
        //data validation
        $validator = Validator::make($request->all(),[
            'student_number' => 'required|string',
            'tor' => 'nullable|numeric',
            'good_moral' => 'nullable|numeric',
            'form_137' => 'nullable|numeric',
            'birth_cercificate' => 'nullable|numeric',
        ]);


        // check if data if validator fails
        if ($validator->fails()) {
          return response()
          ->json([
            'message' => 'Failed to create new studen requirement record.',
            'errors' => $validator->errors()
          ], 400); // 400: Bad request
        }
        else {
          $requirement_data = $request->all();
          $requirement_data['last_updated_by'] = Auth::user()->id;
          try {
            $requirements = StudentRequirement::create($requirement_data);
            // check if record is successfully created.
            if ($requirements) {
              //record in activity log
              $activityLog = ActivityLog::create([
                  'user_id' => $user->id,
                  'activity' => 'Created a new student requirement.',
                  'time' => Carbon::now()
              ]);
              return response()->json(['message' => 'New student requirement successfully created.'], 200);
            }else {
              return response()->json(['message' => 'Failed to create new student requirement record.'], 500); // server error
            }
          } catch (Exception $e) {
            report($e);
            return false;
          }
        }
      } else{
          //record in activity log
          $activityLog = ActivityLog::create([
              'user_id' => $user->id,
              'activity' => 'Attempted to create a new student requirement.',
              'time' => Carbon::now()
          ]);
          return response()->json([
              'message' => 'You are not authorized to create student requirement records.'
          ],401); //401: Unauthorized
      }
    } // end of function store()

    /**
     * Display the specified resource.
     *
     * @param  \App\StudentRequirement  $studentRequirement
     * @return \Illuminate\Http\Response
     */
    public function show(StudentRequirement $studentRequirement)
    {
      $user = Auth::user();
      //Check if user has permission to view instructors
      $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges($user->id, Config::get('settings.requirements_management'), 'read_priv');

      if($isAuthorized){
        //record in activity log
        $activityLog = ActivityLog::create([
            'user_id' => $user->id,
            'activity' => 'Viewed the details of ' . $studentRequirement->student_number . '.',
            'time' => Carbon::now()
        ]);
        // display specific student requirements.
        return $studentRequirement;
      }else{
          //record in activity log
          $activityLog = ActivityLog::create([
              'user_id' => $user->id,
              'activity' => 'Attempted to view the details of ' . $studentRequirement->student_number . '.',
              'time' => Carbon::now()
          ]);
          return response()->json([
              'message' => 'You are not authorized to view student requirements records.'
          ],401); //401: Unauthorized
      }
    } // end of function show()

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\StudentRequirement  $studentRequirement
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, StudentRequirement $studentRequirement)
    {
      $user = Auth::user();
      //check if user have the priviledge to update course record.
      $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges($user->id, Config::get('settings.requirements_management'), 'update_priv');
      if($isAuthorized){
        //data validation
        $validator = Validator::make($request->all(),[
          'student_number' => 'required|string',
          'tor' => 'nullable|numeric',
          'good_moral' => 'nullable|numeric',
          'form_137' => 'nullable|numeric',
          'birth_cercificate' => 'nullable|numeric',
        ]);

        // check if data if validator fails
        if ($validator->fails()) {
          return response()
          ->json([
            'message' => 'Failed to update student requirement record.',
            'errors' => $validator->errors()
          ], 400); // 400: Bad request
        }
        else {
          $requirement_data = $request->all();
          $requirement_data['last_updated_by'] = Auth::user()->id;
          try {
             $check = $studentRequirement->update($requirement_data);

            // check if record is successfully updated.
            if ($check) {
              //record in activity log
              $activityLog = ActivityLog::create([
                  'user_id' => $user->id,
                  'activity' => 'Updated the requirement record of student ' . $studentRequirement->student_number . '.',
                  'time' => Carbon::now()
              ]);
              return response()->json(['message' => 'Student requirement record successfully updated.'], 200);
            }else {
              return response()->json(['message' => 'Failed to update student requirement record.'], 500); // server error
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
              'activity' => 'Attempted to update the requirement record of student ' . $studentRequirement->student_number . '.',
              'time' => Carbon::now()
          ]);
          return response()->json([
              'message' => 'You are not authorized to update student requirement records.'
          ],401); //401: Unauthorized
      }
    } // end of function update()

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\StudentRequirement  $studentRequirement
     * @return \Illuminate\Http\Response
     */
    public function destroy(StudentRequirement $studentRequirement)
    {
      $user = Auth::user();
      //check if user has the priviledge to delete requirement record.
      $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges($user->id, Config::get('settings.requirements_management'), 'delete_priv');
      if($isAuthorized){
        try {
          $studentRequirement->delete();
          //record in activity log
          $activityLog = ActivityLog::create([
              'user_id' => $user->id,
              'activity' => 'Deleted the requirement record of student ' . $studentRequirement->student_number . '.',
              'time' => Carbon::now()
          ]);
          return response()->json(['message' => 'Student requirement record successfully deleted.'], 200);
        } catch (Exception $e) {
          report($e);
          return false;
        }
      }else{
          //record in activity log
          $activityLog = ActivityLog::create([
              'user_id' => $user->id,
              'activity' => 'Attempted to delete the requirement record of student ' . $studentRequirement->student_number . '.',
              'time' => Carbon::now()
          ]);
          return response()->json([
              'message' => 'You are not authorized to delete student requirements records.'
          ],401); //401: Unauthorized
      }
    } // end of function destroy()
}
