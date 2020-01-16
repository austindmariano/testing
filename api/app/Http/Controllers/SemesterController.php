<?php

namespace App\Http\Controllers;

use App\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Validator;

use App\ActivityLog;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;

class SemesterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        //Check if user has permission to view semesters
        $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges($user->id, Config::get('settings.semester_management'), 'read_priv');

        if($isAuthorized){
            $semesters = Semester::orderBy('id', 'DESC')->get();
            //record in activity log
            $activityLog = ActivityLog::create([
                'user_id' => $user->id,
                'activity' => 'Viewed the list of semesters.',
                'time' => Carbon::now()
            ]);
            return $semesters;
        }else{
            //record in activity log
            $activityLog = ActivityLog::create([
                'user_id' => $user->id,
                'activity' => 'Attempted to view the list of semesters.',
                'time' => Carbon::now()
            ]);
            return response()->json([
                'message' => 'You are not authorized to view the list of semesters.'
            ],401);      //401: Unauthorized
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
        //check first if user has permission to create Semester records before proceeding
        $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges($user->id, Config::get('settings.semester_management'), 'create_priv');

        if($isAuthorized){
            // data validation
            $validator = Validator::make($request->all(),[
                'semester' => 'required|string|unique:semesters,semester'
            ]);

            // if validation fails
            if ($validator->fails()) {
                return response()->json([
                'message' => 'Failed to create new semester record.',
                'errors' => $validator->errors()
              ], 400); // 400: Bad request
            }
            else {
                try {
                    $semesterData = $request->all();
                    $semesterData['last_updated_by'] = Auth::user()->id;
                    $semester = Semester::create($semesterData);
                    if ($semester) {
                        return response()->json(["message" => "New semester record successfully created."], 200);
                    }
                } catch (Exception $e) {
                    report($e);
                    return false; // returns an error message
                }
            }
        }
    } // end of function store()

    /**
     * Display the specified resource.
     *
     * @param  \App\Semester  $semester
     * @return \Illuminate\Http\Response
     */
    public function show(Semester $semester)
    {
        $user = Auth::user();
        //check first if user has permission to view semester records before proceeding
        $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges($user->id, Config::get('settings.semester_management'), 'read_priv');

        if($isAuthorized){
            //record in activity log
            $activityLog = ActivityLog::create([
                'user_id' => $user->id,
                'activity' => 'Viewed ' . $semester->semester . '.',
                'time' => Carbon::now()
            ]);
            return $semester;
        }else{
            //record in activity log
            $activityLog = ActivityLog::create([
                'user_id' => $user->id,
                'activity' => 'Attempted to view ' . $semester->semester . '.',
                'time' => Carbon::now()
            ]);
            return response()->json([
                'message' => 'You are not authorized to view the list of semesters.'
            ],401);      //401: Unauthorized
        }
    }// end of function show()

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Semester  $semester
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Semester $semester)
    {
        $user = Auth::user();
        //check first if user has permission to update semester records before proceeding
        $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges($user->id, Config::get('settings.semester_management'), 'update_priv');
        if($isAuthorized){
            // validating data
            $validator = Validator::make($request->all(),[
                'semesters' => 'string|unique:semesters,semester'
            ]);

            // if validation fails
            if ($validator->fails()) {
                return response()
                ->json([
                'message' => 'Failed to create new semester record.',
                'errors' => $validator->errors()
              ], 400); // 400: Bad request
            }
            else {
                $semesterData = $request->all();
                $semesterData['last_updated_by'] = Auth::user()->id;
                try {
                    $semester->update($semesterData);
                    //record in activity log
                    $activityLog = ActivityLog::create([
                        'user_id' => $user->id,
                        'activity' => 'Updated ' . $semester->semester . '.',
                        'time' => Carbon::now()
                    ]);
                    return response()->json(["message" => "Semester record successfully updated."], 200);
                } catch (Exception $e) {
                    report($e);
                    return false; // returns an error message
                }
            }
        }else{
            //record in activity log
            $activityLog = ActivityLog::create([
                'user_id' => $user->id,
                'activity' => 'Attempted to update ' . $semester->semester . '.',
                'time' => Carbon::now()
            ]);
            return response()->json([
                'message' => 'You are not authorized to update the list of semesters.'
            ],401);      //401: Unauthorized
        }

    } // end of function update()

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Semester  $semester
     * @return \Illuminate\Http\Response
     */
    public function destroy(Semester $semester)
    {
        $user = Auth::user();
        //check first if user has permission to update semester records before proceeding
        $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges($user->id, Config::get('settings.semester_management'), 'delete_priv');
        if($isAuthorized){
            $semester->delete();
            //record in activity log
            $activityLog = ActivityLog::create([
                'user_id' => $user->id,
                'activity' => 'Deleted ' . $semester->semester . '.',
                'time' => Carbon::now()
            ]);
            return response()->json(['message' => 'Semester record successfully deleted.'], 200);
        }else{
            //record in activity log
            $activityLog = ActivityLog::create([
                'user_id' => $user->id,
                'activity' => 'Attempted to delete ' . $semester->semester . '.',
                'time' => Carbon::now()
            ]);
            return response()->json([
                'message' => 'You are not authorized to delete the list of semesters.'
            ],401);      //401: Unauthorized
        }

    } // end of function destroy()
}
