<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\ClassSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Validator;

use App\ActivityLog;
use App\CurriculumSubject;
use App\InstructorPreferredSubject;
use App\InstructorAvailability;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;

class ClassScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      $user = Auth::user();
      //Check if user has permission to view class schedule records.
      $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges($user->id, Config::get('settings.schedule_management'), 'read_priv');
      if ($isAuthorized) {

        //record in activity log
        $activityLog = ActivityLog::create([
            'user_id' => $user->id,
            'activity' => 'Viewed the list of class schedules.',
            'time' => Carbon::now()
        ]);
        // original from Dec 18 file
        $class_schedules = ClassSchedule::with('room','subject', 'instructor', 'semester', 'academic_year')->orderBy('id', 'DESC')->get();
        return $class_schedules;
        // $classes = ClassSchedule::orderBy('id', 'DESC')->get();
        // $myArr = [];
        // $i = 0;
        // foreach ($classes as $class) {
        //   $myArr[$i] = array(
        //     'id' => $class->id,
        //     'subject' => array(
        //       'curr_subject_id' => $class->subject->id,
        //       'subject_id' => $class->subject->subject_id,
        //       'subject_desc' => $class->subject->subject->subject_code
        //     ),
        //    'room' => array(
        //          'id' => $class->room->id,
        //       'room_number' => $class->room->room_number,
        //       'room_name' => $class->room->room_name,
        //          'room_capacity' => $class->room->room_capacity,
        //        ),
        //    'instructor_id' => array(
        //           'id' => $class->instructor->id,
        //           'first_name' => $class->instructor->first_name,
        //           'middle_name' => $class->instructor->middle_name,
        //           'last_name' => $class->instructor->last_name,
        //        ),
        //     'day' => $class->day,
        //     'time_start' => $class->time_start,
        //     'time_end' => $class->time_end,
        //   );
        //   $i++;
        // }
        // return $myArr;

      }else{
          //record in activity log
          $activityLog = ActivityLog::create([
              'user_id' => $user->id,
              'activity' => 'Attempted to view the list of class schedules.',
              'time' => Carbon::now()
          ]);
          return response()->json([
              'message' => 'You are not authorized to view class schedule records.'
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
      //  check if user have the priviledge to create class schedule record.
      $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges($user->id, Config::get('settings.schedule_management'), 'create_priv');
      if ($isAuthorized) {
        //data validation
        $validator = Validator::make($request->all(),[
          'day' => 'nullable|string',
          'time_start' => 'nullable|string',
          'time_end' => 'nullable|string',
          'subject_id' => 'required|numeric',
          'room_id' => 'nullable|numeric',
          'instructor_id' => 'nullable|numeric',
          'block' => 'required|numeric',
          'batch' => 'required|numeric',
          'class_type' => 'required|string'
        ]);

        // check if data if validator fails
        if ($validator->fails()) {
          return response()
          ->json([
            'message' => 'Failed to create new class schedule record.',
            'errors' => $validator->errors()
          ], 400); // 400: Bad request
        } else {
            $setting = DB::table('settings')->first();
            $class_schedule_data = $request->all();
            // $class_schedule_data['academic_year_id'] = $setting->current_academic_year;
            // $class_schedule_data['semester_id'] = $setting->current_semester;
            $class_schedule_data['last_updated_by'] = Auth::user()->id;
            // return $this->createSchedule($class_schedule_data, $user);
            return $this->conflictChecker($class_schedule_data,$user);
          }
      }else{
          //record in activity log
          $activityLog = ActivityLog::create([
              'user_id' => $user->id,
              'activity' => 'Attempted to create a new class schedule.',
              'time' => Carbon::now()
          ]);
          return response()->json([
              'message' => 'You are not authorized to create class schedule records.'
          ],401); // 401: Unauthorized
      }

    } // end of function store()

    public function createSchedule($class_schedule_data, $user){
      try {
        //convert 12 hour to 24 hour
        $class_schedule_data['time_start']  = date("H:i", strtotime($class_schedule_data['time_start']));
        $class_schedule_data['time_end']  = date("H:i", strtotime($class_schedule_data['time_end']));

        $class_schedule = ClassSchedule::create($class_schedule_data);
        // check if record is successfully created.
        if ($class_schedule) {
          //record in activity log
          $activityLog = ActivityLog::create([
              'user_id' => $user->id,
              'activity' => 'Created a new class schedule.',
              'time' => Carbon::now()
          ]);
          return response()->json(['message' => 'New class schedule record successfully created.'], 200);
        }else {
          return response()->json(['message' => 'Failed to create new class schedule record.'], 500); // server error
        }
      } catch (Exception $e) {
        report($e);
        return false;
      }
    } // end of function createSchedule()

    public function conflictChecker($class_schedule_data, $user){
      $schedule_conflicts = array();
      // loop through all conflict checkers
      for ($i=0; $i < 3; $i++) {
        // run room conflict checker
        if($i == 0){
          $room_data = $this->roomChecker($class_schedule_data, $user);
          //check if there is a conflict
          $dcd_room = json_decode($room_data);
          if(!empty($dcd_room)){
            // convert 24 hour to 12 hour
            $new_start  = date("g:iA", strtotime($room_data->time_start));
            $new_end  = date("g:iA", strtotime($room_data->time_end));

            $schedule_conflicts['room_conflict'] = "Room " . $room_data->room->room_number .
             " is already scheduled from " . $new_start . " to " . $new_end . ".";
          }
        } // end room checker
        // start of duplicate checker
        elseif($i == 1){

          $duplicate_data = $this->duplicateChecker($class_schedule_data, $user);
          //check if there is a conflict
          $dcd_duplicate = json_decode($duplicate_data);
          if(!empty($dcd_duplicate)){

            $schedule_conflicts['duplicate_conflict'] = "This subject is already have a schedule.";
          }
        } // end duplicate checker
        // start of instructor checker
        elseif($i == 2){
         $instructor_data = $this->instructorChecker($class_schedule_data, $user);
          //check if there is a conflict
          if(!empty($instructor_data)){
            $schedule_conflicts['instructor_conflict'] = $instructor_data;
          }
        }// end instructor checker
        elseif($i == 3){
          // code...
        }
      } // end of loop
      // return $schedule_conflicts;

      // check if there is returned conflicts
      if(!empty($schedule_conflicts)){
        // return all conflicts here
        return response()->json(['message' => 'Failed to create new class schedule.',
                                 'schedule_conflict' => $schedule_conflicts], 400); // bad request
      }elseif(empty($schedule_conflicts)){
        // run create schedule here
        // return response()->json(['message' => 'Proceed on creating schedule.'], 200);
         $verify_create = $this->createSchedule($class_schedule_data, $user);
         return $verify_create;
      }else{
        return response()->json(['message' => 'Failed to create new class schedule record.'], 500); // server error
      }
    }
    // end of function conflictChecker

    public function instructorChecker($class_schedule_data){

      $instructor_error = [];
      // 12 hour to 24 hour
      $time_start  = date("H:i", strtotime($class_schedule_data['time_start']));
      $time_end  = date("H:i", strtotime($class_schedule_data['time_end']));

      for ($j=0; $j <=3 ; $j++) {
        // check if subject is preffered by instructor
        if ($j == 0) {

            $preferred = CurriculumSubject::select('subject_id')
            ->where('id', $class_schedule_data['subject_id'])->get();

            $preferred_subject = InstructorPreferredSubject::select('*')
            ->where('academic_year_id', $class_schedule_data['academic_year_id'])
            ->where('semester_id', $class_schedule_data['semester_id'])
            ->where('instructor_id', $class_schedule_data['instructor_id'])
            ->where('subject_id', $preferred[0]->subject_id)
            ->get();

            if($preferred_subject->isNotEmpty()){
              // array_push($instructor_error, "This subject is not preffered by the selected instructor.");
              // $instructor_error['preffered_error'] = "This subject is not preffered by the selected instructor.";
              // $instructor_error['preffered_error'] = $preferred_subject;
            }else{
              $instructor_error['preffered_error'] = "This subject is not preffered by the selected instructor.";
            }
        }
        // check if if schedule is inside of instructor time availability
        elseif ($j == 1) {
          $time_start  = date("H:i", strtotime($class_schedule_data['time_start']));
          $time_end  = date("H:i", strtotime($class_schedule_data['time_end']));

          $instructor_availability = InstructorAvailability::select('*')
          ->where('academic_year_id', $class_schedule_data['academic_year_id'])
          ->where('semester_id', $class_schedule_data['semester_id'])
          ->where('instructor_id', $class_schedule_data['instructor_id'])
          ->where('day', $class_schedule_data['day'])
          ->with('instructor')
          ->get();

            // $availability = json_decode($instructor_availability, true);
            $availability = $instructor_availability[0];

            if ($time_end > $availability->time_end) {
              $time_start_here  = date("g:iA", strtotime($availability->time_start));
              $time_end_here  = date("g:iA", strtotime($availability->time_end));

              $instructor_error['availability_conflict'] = $availability->instructor->first_name . " " .
              $availability->instructor->last_name . "'s " . $availability->day . " time availability is from " . $time_start_here . " to " .
              $time_end_here;
            }
            elseif ($time_start < $availability->time_start) {
              $time_start_here  = date("g:iA", strtotime($availability->time_start));
              $time_end_here  = date("g:iA", strtotime($availability->time_end));

              $instructor_error['availability_conflict'] = $availability->instructor->first_name . " " .
              $availability->instructor->last_name . "'s " . $availability->day . " time availability is from " . $time_start_here . " to " .
              $time_end_here;
            }
        }
        // check for instructors other schedule
        elseif ($j == 2) {
          $instructor_schedule = ClassSchedule::select('*')
          ->where('academic_year_id', $class_schedule_data['academic_year_id'])
          ->where('semester_id', $class_schedule_data['semester_id'])
          ->where('instructor_id', $class_schedule_data['instructor_id'])
          ->where('day', $class_schedule_data['day'])
          ->where(function ($query) use ($time_start,$time_end) {
                   $query->whereBetween('time_start', [$time_start, $time_end])
                         ->orWhereBetween('time_end', [$time_start, $time_end]);
               })
          ->where('time_end', '!=', $time_start)
          ->Where('time_start', '!=', $time_end)
          ->take(1)
          ->with('instructor')
          ->get();

          if($instructor_schedule->isNotEmpty()){
            // 12 hour to 24 hour
            $schedule = $instructor_schedule[0];
            $time_start  = date("H:i", strtotime($class_schedule_data['time_start']));
            $time_end  = date("H:i", strtotime($class_schedule_data['time_end']));

            if (($time_start == $schedule->time_start) && ($time_end == $schedule->time_end)) {
              // return the conflict
              $time_start  = date("g:iA", strtotime($schedule->time_start));
              $time_end  = date("g:iA", strtotime($schedule->time_end));
               $instructor_error['schedule_conflict'] = $schedule->instructor->first_name . " " .
                    $schedule->instructor->last_name . " is already have a schedule from " .
                    $time_start . " to " . $time_end . ".";
            }
            else{
              $time_start  = date("g:iA", strtotime($schedule->time_start));
              $time_end  = date("g:iA", strtotime($schedule->time_end));

              $instructor_error['schedule_conflict'] = $schedule->instructor->first_name . " " .
                    $schedule->instructor->last_name . " is already have a schedule from " .
                    $time_start . " to " . $time_end . ".";
              // return $instructor_schedule;
            }
          }
        }
      }// end of loop
      return $instructor_error;
    }
    // end of function instructorChecker

    public function duplicateChecker($class_schedule_data){
      // get if there is the same schedule
      $class_schedule = ClassSchedule::select('*')
      ->where('academic_year_id', $class_schedule_data['academic_year_id'])
      ->where('semester_id', $class_schedule_data['semester_id'])
      ->where('subject_id', $class_schedule_data['subject_id'])
      ->where('block', $class_schedule_data['block'])
      ->where('batch', $class_schedule_data['batch'])
      ->where('class_type', $class_schedule_data['class_type'])
      ->get();

      // if there is a return data
      if($class_schedule->isNotEmpty()){
        return $duplicate_data = $class_schedule[0];
      }
    }
    // end of function duplicateChecker

    public function roomChecker($class_schedule_data){
      //convert 12 Hour format to 24 Hour format
      $room_time_start  = date("H:i", strtotime($class_schedule_data['time_start']));
      $room_time_end  = date("H:i", strtotime($class_schedule_data['time_end']));

      $room_schedule = ClassSchedule::select('*')
      ->where('academic_year_id', $class_schedule_data['academic_year_id'])
      ->where('semester_id', $class_schedule_data['semester_id'])
      ->where('room_id', $class_schedule_data['room_id'])
      ->where('day', $class_schedule_data['day'])
      ->where(function ($query) use ($room_time_start,$room_time_end) {
               $query->whereBetween('time_start', [$room_time_start, $room_time_end])
                     ->orWhereBetween('time_end', [$room_time_start, $room_time_end]);
           })
      // ->take(1)
      ->where('time_end', '!=', $room_time_start)
      ->Where('time_start', '!=', $room_time_end)
      ->with('room')
      ->get();

      if($room_schedule->isNotEmpty()){
        $room_data = $room_schedule[0];
        if ($room_time_start >= $room_data->time_end) {
          return null;
        }
        elseif ($room_time_end <= $room_data->time_start ) {
          return null;
        }
        // elseif($room_data->time_start == $room_time_start && $room_data->time_end == $room_time_end){
        elseif (($room_time_start == $room_data->time_start) AND ($room_time_end == $room_data->time_end)) {
          // return the conflict
          return $room_data;
        }
        else {
          return $room_data;
        }
      }
    }
    // end of function roomChecker

    public function studentChecker($class_schedule_data){

    }
    // end of function studentChecker

    /**
     * Display the specified resource.
     *
     * @param  \App\ClassSchedule  $classSchedule
     * @return \Illuminate\Http\Response
     */
    public function show(ClassSchedule $class_schedule)
    {
      $user = Auth::user();
      //Check if user has permission to view class schedule records.
      $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges($user->id, Config::get('settings.schedule_management'), 'read_priv');

      if($isAuthorized){
        //record in activity log
        $activityLog = ActivityLog::create([
            'user_id' => $user->id,
            'activity' => 'Viewed the details of ' . $class_schedule->id . '.',
            'time' => Carbon::now()
        ]);
        $class_schedule = ClassSchedule::select('*')->where('id', $class_schedule->id)->with('room', 'instructor', 'subject')->get();
        return $class_schedule;
      }else{
          //record in activity log
          $activityLog = ActivityLog::create([
              'user_id' => $user->id,
              'activity' => 'Attempted to view the details of ' . $class_schedule->id . '.',
              'time' => Carbon::now()
          ]);
          return response()->json([
              'message' => 'You are not authorized to view class schedule records.'
          ],401); // 401: Unauthorized
      }

    } // end of function show()

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ClassSchedule  $classSchedule
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ClassSchedule $class_schedule)
    {
      $user = Auth::user();
      //  check if user have the priviledge to update course record.
      $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges($user->id, Config::get('settings.schedule_management'), 'update_priv');

      if($isAuthorized){
        //data validation
        $validator = Validator::make($request->all(),[
          'day' => 'nullable|string',
          'time_start' => 'nullable|string',
          'time_end' => 'nullable|string',
          'subject_id' => 'numeric',
          'room_id' => 'nullable|numeric',
          'instructor_id' => 'nullable|numeric',
          'block' => 'numeric',
          'batch' => 'numeric',
          'class_type' => 'string'
        ]);

        // check if data if validator fails
        if ($validator->fails()) {
          return response()
          ->json([
            'message' => 'Failed to update class schedule record.',
            'errors' => $validator->errors()
          ], 400); // 400: Bad request
        }
        else {
          $class_schedule_data = $request->all();
          // $class_schedule_data['academic_year_id'] = $setting->current_academic_year;
          // $class_schedule_data['semester_id'] = $setting->current_semester;
          $class_schedule_data['last_updated_by'] = Auth::user()->id;
          try {
             $check = $class_schedule->update($class_schedule_data);
            // check if record is successfully updated.
            if ($check) {
              //record in activity log
              $activityLog = ActivityLog::create([
                  'user_id' => $user->id,
                  'activity' => 'Updated the class schedule ' . $class_schedule->id . '.',
                  'time' => Carbon::now()
              ]);
              return response()->json(['message' => 'Class schedule record successfully updated.'], 200);
            }else {
              return response()->json(['message' => 'Failed to update class schedule record.'], 500); // server error
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
              'activity' => 'Attempted to update the details of ' . $class_schedule->id . '.',
              'time' => Carbon::now()
          ]);
          return response()->json([
              'message' => 'You are not authorized to update class schedule records.'
          ],401); // 401: Unauthorized
      }

    } // end of function update()

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ClassSchedule  $classSchedule
     * @return \Illuminate\Http\Response
     */
    public function destroy(ClassSchedule $class_schedules)
    {
      $user = Auth::user();
      //  check if user has the priviledge to delete course record.
      $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges($user->id, Config::get('settings.schedule_management'), 'delete_priv');
      if($isAuthorized){
        try {
          $class_schedules->delete();
          //record in activity log
          $activityLog = ActivityLog::create([
              'user_id' => $user->id,
              'activity' => 'Deleted the class schedule ' . $class_schedules->id . '.',
              'time' => Carbon::now()
          ]);
          return response()->json(['message' => 'Class schedule record successfully deleted.'], 200);
        } catch (Exception $e) {
          report($e);
          return false;
        }
      }else{
          //record in activity log
          $activityLog = ActivityLog::create([
              'user_id' => $user->id,
              'activity' => 'Attempted to delete the class schedule ' . $class_schedules->id . '.',
              'time' => Carbon::now()
          ]);
          return response()->json([
              'message' => 'You are not authorized to delete class schedule records.'
          ],401); // 401: Unauthorized
      }

    } // end of function destroy()

    //testing only
    // public function getSem(){
    //   $setting = DB::table('settings')->first();
    //   return $setting->current_sem;
    // }
}
