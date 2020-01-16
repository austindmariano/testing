<?php

namespace App\Http\Controllers;

use App\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Validator;

use App\ActivityLog;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;

class AcademicYearController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $user = Auth::user();
      //Check if user has permission to view academic year
      $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges($user->id, Config::get('settings.academic_year_management'), 'read_priv');
      if ($isAuthorized) {
        $academic_years = AcademicYear::all();
        //record in activity log
        $activityLog = ActivityLog::create([
            'user_id' => $user->id,
            'activity' => 'Viewed the list of academic years.',
            'time' => Carbon::now()
        ]);
        return $academic_years;
      }else {
        //record in activity log
        $activityLog = ActivityLog::create([
            'user_id' => $user->id,
            'activity' => 'Attempted to view the list of academic years.',
            'time' => Carbon::now()
        ]);
        return response()->json([
            'message' => 'You are not authorized to view academic years records.'
        ],401); //401: Unauthorized
      }
    } // end of function index()

    /**
     * Create new academic year record.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $user = Auth::user();
      // check if user has priviledge to create academic year record
      $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges($user->id, Config::get('settings.academic_year_management'), 'create_priv');
      if ($isAuthorized) {
        // check if data is not null and should be numeric
        $validator = Validator::make($request->all(),['academic_year' => 'required|numeric']);
        if ($validator->fails()) {
          return response()
          ->json([
            'message' => 'Failed to create new academic year record.',
            'errors' => $validator->errors()
          ], 400); // 400: Bad request
        }else {
          $academic_year_data = $request->all();
          // check if data is 4 digit number
          if (strlen($academic_year_data['academic_year']) != 4) {
            return response()
            ->json([
              'message' => 'Failed to create new academic year record.',
              'errors' => [ 'academic_year' => ['The academic year must consist of 4 digit number only.']]
            ], 400);  // 400: Bad request
          }else {
            $year = $academic_year_data['academic_year'] + 1;
            $academic_year_data['academic_year'] = $academic_year_data['academic_year'] . "-" . $year;

            $validator = Validator::make($academic_year_data,['academic_year' => 'unique:academic_years,academic_year']);
            if ($validator->fails()) {
              return response()
              ->json([
                'message' => 'Failed to create new academic year record.',
                'errors' => $validator->errors()
              ], 400);  // 400: Bad request
            }else {
              $academic_year_data['last_updated_by'] = Auth::user()->id;
              try {
                $academic_year = AcademicYear::create($academic_year_data);
                // check if record is successfully created.
                if ($academic_year) {
                  //record in activity log
                  $activityLog = ActivityLog::create([
                      'user_id' => $user->id,
                      'activity' => 'Created a new academic year.',
                      'time' => Carbon::now()
                  ]);
                  return response()->json(['message' => 'New academic year record successfully created.'], 200);
                }else {
                  return response()->json(['message' => 'Failed to create new academic year record.'], 500); // server error
                }
              } catch (Exception $e) {
                report($e);
                return false;
              }
            }
          }
        }
      } else {
        //record in activity log
        $activityLog = ActivityLog::create([
            'user_id' => $user->id,
            'activity' => 'Attempted to create a new academic year.',
            'time' => Carbon::now()
        ]);
        return response()->json([
            'message' => 'You are not authorized to create academic year records.'
        ], 401);  // 401: Unauthorized
      }

    } // end of function store()

    /**
     * Display the specified resource.
     *
     * @param  \App\AcademicYear  $academicYear
     * @return \Illuminate\Http\Response
     */
    public function show(AcademicYear $academic_year)
    {
      $user = Auth::user();
      //Check if user has permission to view instructors
      $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges($user->id, Config::get('settings.academic_year_management'), 'read_priv');

      if($isAuthorized){
          //record in activity log
          $activityLog = ActivityLog::create([
              'user_id' => $user->id,
              'activity' => 'Viewed the details of ' . $academic_year->academic_year . '.',
              'time' => Carbon::now()
          ]);
          return $academic_year;
      }else {
        //record in activity log
        $activityLog = ActivityLog::create([
            'user_id' => $user->id,
            'activity' => 'Attempted to view the details of ' . $academic_year->academic_year . '.',
            'time' => Carbon::now()
        ]);
        return response()->json([
            'message' => 'You are not authorized to view academic year records.'
        ],401); // 401: Unauthorized
      }
    } // end of function show()

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\AcademicYear  $academicYear
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AcademicYear $academic_year)
    {
      $user = Auth::user();
      //Check if user has permission to update academic year record.
      $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges($user->id, Config::get('settings.academic_year_management'), 'update_priv');

      if($isAuthorized){
        // check if data is not null and should be numeric
        $validator = Validator::make($request->all(),['academic_year' => 'numeric']);
        if ($validator->fails()) {
          return response()
          ->json([
            'message' => 'Failed to update academic year record.',
            'errors' => $validator->errors()
          ], 400); // 400: Bad request
        }else {
          $academic_year_data = $request->all();
          // check if data is 4 digit number
          if (strlen($request->academic_year) != 4) {
            return response()
            ->json([
              'message' => 'Failed to update academic year record.',
              'errors' => [ 'academic_year' => ['The academic year must consist of 4 digit number only.']]
            ], 400);  // 400: Bad request
          }else {
            $year = $academic_year_data['academic_year'] + 1;
            $academic_year_data['academic_year'] = $academic_year_data['academic_year'] . "-" . $year;

            $validator = Validator::make($academic_year_data,['academic_year' => 'unique:academic_years,academic_year']);
            if ($validator->fails()) {
              return response()
              ->json([
                'message' => 'Failed to update academic year record.',
                'errors' => $validator->errors()
              ], 400);  // 400: Bad request
            }else {
              $academic_year_data['last_updated_by'] = Auth::user()->id;
              try {
                $check = $academic_year->update($academic_year_data);
                // check if record is successfully updated.
                if ($check) {
                  //record in activity log
                  $activityLog = ActivityLog::create([
                      'user_id' => $user->id,
                      'activity' => 'Updated the academic year ' . $academic_year->academic_year . '.',
                      'time' => Carbon::now()
                  ]);
                  return response()->json(['message' => 'Academic year record successfully updated.'], 200);
                }else {
                  return response()->json(['message' => 'Failed to update academic year record.'], 500); // server error
                }
              } catch (Exception $e) {
                report($e);
                return false;
              }
            }
          }
        }
      }else {
        //record in activity log
        $activityLog = ActivityLog::create([
            'user_id' => $user->id,
            'activity' => 'Attempted to update the details of ' . $academic_year->academic_year . '.',
            'time' => Carbon::now()
        ]);
        return response()->json([
            'message' => 'You are not authorized to update academic year records.'
        ],401); // 401: Unauthorized
      }
    } // end of function update()

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\AcademicYear  $academicYear
     * @return \Illuminate\Http\Response
     */
    public function destroy(AcademicYear $academic_year)
    {
      $user = Auth::user();
      // check if user has the priviledge to delete course record.
      $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges($user->id, Config::get('settings.academic_year_management'), 'delete_priv');
      if($isAuthorized){
          $academic_year->delete();
        //record in activity log
        $activityLog = ActivityLog::create([
            'user_id' => $user->id,
            'activity' => 'Deleted the academic year ' . $academic_year->academic_year . '.',
            'time' => Carbon::now()
        ]);
        return response()->json(['message' => 'Academic year record successfully deleted.'], 200);
      }else {
        //record in activity log
        $activityLog = ActivityLog::create([
            'user_id' => $user->id,
            'activity' => 'Attempted to delete the academic year ' . $academic_year->academic_year . '.',
            'time' => Carbon::now()
        ]);
        return response()->json([
            'message' => 'You are not authorized to delete academic year records.'
        ],401); // 401: Unauthorized
      }
    } // end of function destroy()
}
