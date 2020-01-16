<?php

namespace App\Http\Controllers;

use App\StudentSchedule;
use App\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Validator;

use App\ActivityLog;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;

class StudentScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $user = Auth::user();
      //Check if user has permission to view student schedule records.
      $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges($user->id, Config::get('settings.student_schedule_management'), 'read_priv');
      if ($isAuthorized) {
        //record in activity log
        $activityLog = ActivityLog::create([
            'user_id' => $user->id,
            'activity' => 'Viewed the list of student schedule.',
            'time' => Carbon::now()
        ]);
         $student_schedules = StudentSchedule::orderBy('id', 'DESC')->get();
         return $student_schedules;

      }else{
          //record in activity log
          $activityLog = ActivityLog::create([
              'user_id' => $user->id,
              'activity' => 'Attempted to view the list of student schedule.',
              'time' => Carbon::now()
          ]);
          return response()->json([
              'message' => 'You are not authorized to view student schedule records.'
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
      //check if user have the priviledge to create student schedule record.
      $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges($user->id, Config::get('settings.student_schedule_management'), 'create_priv');
      if ($isAuthorized) {
        //data validation
        $validator = Validator::make($request->all(),[
          'schedule_id' => 'required|numeric',
          'prelim_grade' => 'nullable|numeric',
          'midterm_grade' => 'nullable|numeric',
          'prefinal_grade' => 'nullable|numeric',
          'final_grade' => 'nullable|numeric',
          'semestral' => 'nullable|numeric',
          'remarks' => 'nullable|numeric',
          'figure' => 'nullable|numeric'
        ]);

        // check if data if validator fails
        if ($validator->fails()) {
          return response()
          ->json([
            'message' => 'Failed to create new student schedule record.',
            'errors' => $validator->errors()
          ], 400); // 400: Bad request
        }
        else {
          $student_data = $request->all();
          $student_data['enrollment_id'] = $this->getEnrollement()[0]->id;
          $student_data['last_updated_by'] = Auth::user()->id;
          try {
              $schedule = StudentSchedule::create($student_data);
            // check if record is successfully created.
            if ($schedule) {
              //record in activity log
              $activityLog = ActivityLog::create([
                  'user_id' => $user->id,
                  'activity' => 'Created a new student schedule.',
                  'time' => Carbon::now()
              ]);
              return response()->json(['message' => 'New student schedule record successfully created.'], 200);
            }else {
              return response()->json(['message' => 'Failed to create new student schedule record.'], 500); // server error
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
              'activity' => 'Attempted to create a new student schedule.',
              'time' => Carbon::now()
          ]);
          return response()->json([
              'message' => 'You are not authorized to create student schedule records.'
          ],401); //401: Unauthorized
      }
    } // end of function store()

    /**
     * Display the specified resource.
     *
     * @param  \App\StudentSchedule  $studentSchedule
     * @return \Illuminate\Http\Response
     */
    public function show(StudentSchedule $studentSchedule)
    {
      $user = Auth::user();
      //Check if user has permission to view instructors
      $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges($user->id, Config::get('settings.student_schedule_management'), 'read_priv');

      if($isAuthorized){
        //record in activity log
        $activityLog = ActivityLog::create([
            'user_id' => $user->id,
            'activity' => 'Viewed the details of ' . $studentSchedule->id . '.',
            'time' => Carbon::now()
        ]);
        return $studentSchedule;
      }else{
          //record in activity log
          $activityLog = ActivityLog::create([
              'user_id' => $user->id,
              'activity' => 'Attempted to view the details of ' . $studentSchedule->id . '.',
              'time' => Carbon::now()
          ]);
          return response()->json([
              'message' => 'You are not authorized to view student schedule records.'
          ],401); //401: Unauthorized
      }
    } // end function show()

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\StudentSchedule  $studentSchedule
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, StudentSchedule $studentSchedule)
    {
      $user = Auth::user();
      //check if user have the priviledge to update stuent schedule record.
      $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges($user->id, Config::get('settings.student_schedule_management'), 'update_priv');
      if($isAuthorized){
        //data validation
        $validator = Validator::make($request->all(),[

        ]);

        // check if data if validator fails
        if ($validator->fails()) {
          return response()
          ->json([
            'message' => 'Failed to update student schedule record.',
            'errors' => $validator->errors()
          ], 400); // 400: Bad request
        }
        else {
          $schedule_data = $request->all();
          $schedule_data['last_updated_by'] = Auth::user()->id;
          try {
             $check = $studentSchedule->update($schedule_data);

            // check if record is successfully updated.
            if ($check) {
              //record in activity log
              $activityLog = ActivityLog::create([
                  'user_id' => $user->id,
                  'activity' => 'Updated the student schedule ' . $studentSchedule->id . '.',
                  'time' => Carbon::now()
              ]);
              return response()->json(['message' => 'Student schedule record successfully updated.'], 200);
            }else {
              return response()->json(['message' => 'Failed to update student schedule record.'], 500); // server error
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
              'activity' => 'Attempted to update the details of ' . $studentSchedule->id . '.',
              'time' => Carbon::now()
          ]);
          return response()->json([
              'message' => 'You are not authorized to update student schedule records.'
          ],401); //401: Unauthorized
      }
    } // end of function update()

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\StudentSchedule  $studentSchedule
     * @return \Illuminate\Http\Response
     */
    public function destroy(StudentSchedule $studentSchedule)
    {
      $user = Auth::user();
      //check if user has the priviledge to delete course record.
      $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges($user->id, Config::get('settings.student_schedule_management'), 'delete_priv');
      if($isAuthorized){
        try {
          $studentSchedule->delete();
          //record in activity log
          $activityLog = ActivityLog::create([
              'user_id' => $user->id,
              'activity' => 'Deleted the student schedule record  ' . $studentSchedule->id . '.',
              'time' => Carbon::now()
          ]);
          return response()->json(['message' => 'Student schedule record successfully deleted.'], 200);
        } catch (Exception $e) {
          report($e);
          return false;
        }
      }else{
          //record in activity log
          $activityLog = ActivityLog::create([
              'user_id' => $user->id,
              'activity' => 'Attempted to delete the student schedule record ' . $studentSchedule->id . '.',
              'time' => Carbon::now()
          ]);
          return response()->json([
              'message' => 'You are not authorized to delete student schedule records.'
          ],401); //401: Unauthorized
      }
    } // end of function destroy()

    public function getEnrollement(){
      return Enrollment::orderBy('id', 'DESC')->take(1)->get();
    }
}
