<?php

namespace App\Http\Controllers;

use App\Enrollment;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $user = Auth::user();
      //Check if user has permission to view enrollment records.
      $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges($user->id, Config::get('settings.enrollment_management'), 'read_priv');
      if ($isAuthorized) {
        //record in activity log
        $activityLog = ActivityLog::create([
            'user_id' => $user->id,
            'activity' => 'Viewed the list of enrollment record.',
            'time' => Carbon::now()
        ]);
         $enrollments = Enrollment::orderBy('id', 'DESC')->get();
         return $enrollments;

      }else{
          //record in activity log
          $activityLog = ActivityLog::create([
              'user_id' => $user->id,
              'activity' => 'Attempted to view the list of enrollment records.',
              'time' => Carbon::now()
          ]);
          return response()->json([
              'message' => 'You are not authorized to view enrollment records.'
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
      //check if user have the priviledge to create enrollment record.
      $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges($user->id, Config::get('settings.enrollment_management'), 'create_priv');
      if ($isAuthorized) {
        //data validation
        $validator = Validator::make($request->all(),[
          'academic_year_id' => 'required|numeric',
          'semester_id' => 'required|numeric',
          'student_id' => 'required|numeric',
          'year_level' => 'required|numeric',
          'curriculum_id' => 'required|numeric',
          'course_id' => 'nullable|numeric',
          'strand_id' => 'nullable|numeric'
        ]);

        // check if data if validator fails
        if ($validator->fails()) {
          return response()
          ->json([
            'message' => 'Failed to create new enrollment record.',
            'errors' => $validator->errors()
          ], 400); // 400: Bad request
        }
        else {
          $enrollment_data = $request->all();
          $enrollment_data['last_updated_by'] = Auth::user()->id;
          try {
            $enrollment = Enrollment::create($enrollment_data);
            // check if record is successfully created.
            if ($enrollment) {
              //record in activity log
              $activityLog = ActivityLog::create([
                  'user_id' => $user->id,
                  'activity' => 'Created a new enrollment record.',
                  'time' => Carbon::now()
              ]);
              return response()->json(['message' => 'New enrollment record successfully created.'], 200);
            }else {
              return response()->json(['message' => 'Failed to create new enrollment record.'], 500); // server error
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
              'activity' => 'Attempted to create a new enrollment record.',
              'time' => Carbon::now()
          ]);
          return response()->json([
              'message' => 'You are not authorized to create enrollment records.'
          ],401); //401: Unauthorized
      }

  } // end of function store()

  /**
   * Display the specified resource.
   *
   * @param  \App\Enrollment  $enrollment
   * @return \Illuminate\Http\Response
   */
  public function show(Enrollment $enrollment)
  {
      $user = Auth::user();
      //Check if user has permission to view instructors
      $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges($user->id, Config::get('settings.enrollment_management'), 'read_priv');

      if($isAuthorized){
        //record in activity log
        $activityLog = ActivityLog::create([
            'user_id' => $user->id,
            'activity' => 'Viewed the details of ' . $enrollment->student_id . '.',
            'time' => Carbon::now()
        ]);
        // display specific enrollment record.
        return $enrollment;
      }else{
          //record in activity log
          $activityLog = ActivityLog::create([
              'user_id' => $user->id,
              'activity' => 'Attempted to view the details of ' . $enrollment->student_id . '.',
              'time' => Carbon::now()
          ]);
          return response()->json([
              'message' => 'You are not authorized to view enrollment records.'
          ],401); //401: Unauthorized
      }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Enrollment  $enrollment
     * @return \Illuminate\Http\Response
     */
    public function show(Enrollment $enrollment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Enrollment  $enrollment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Enrollment $enrollment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Enrollment  $enrollment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Enrollment $enrollment)
    {
      $user = Auth::user();
      //check if user has the priviledge to delete enrollment record.
      $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges($user->id, Config::get('settings.enrollment_management'), 'delete_priv');
      if($isAuthorized){
        try {
          $enrollment->delete();
          //record in activity log
          $activityLog = ActivityLog::create([
              'user_id' => $user->id,
              'activity' => 'Deleted the enrollement record of student ' . $enrollment->student_id . '.',
              'time' => Carbon::now()
          ]);
          return response()->json(['message' => 'Enrollment record successfully deleted.'], 200);
        } catch (Exception $e) {
          report($e);
          return false;
        }
      }else{
          //record in activity log
          $activityLog = ActivityLog::create([
              'user_id' => $user->id,
              'activity' => 'Attempted to delete the enrollment record ' . $enrollment->student_id . '.',
              'time' => Carbon::now()
          ]);
          return response()->json([
              'message' => 'You are not authorized to delete enrollment records.'
          ],401); //401: Unauthorized
      }
    } // end of function destroy()
}
