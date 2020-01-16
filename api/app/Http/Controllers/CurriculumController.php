<?php

namespace App\Http\Controllers;

use App\Curriculum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Validator;

use App\ActivityLog;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;

class CurriculumController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      $user = Auth::user();
      //Check if user has permission to view curriculum records.
      $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges($user->id, Config::get('settings.curriculum_management'), 'read_priv');
      if ($isAuthorized) {
        // NOTE:  Added a filter for College and Senior High Curriculums (November 6, 2019)
        if($request->query() != null){
            if($request->query('level')=="college") {
                // return only those curriculums for college
                $curriculums = Curriculum::with('course', 'curriculum_subjects')->where('course_id', '!=', null)->get();
            } else if($request->query('level')=="shs") {
                // return only those curriculums for shs
                $curriculums = Curriculum::with('strand', 'curriculum_subjects')->where('strand_id', '!=', null)->get();
            } else {
              return response()->json([
                  'message' => 'Invalid parameter or parameter value. Please refer to the API documentation.'
              ],400);     //400: Bad request
            }
        }else{
            $curriculums = Curriculum::orderBy('id', 'DESC')->with('curriculum_subjects')->get();
        }

        //$curriculums = Curriculum::all();
        //record in activity log
        $activityLog = ActivityLog::create([
            'user_id' => $user->id,
            'activity' => 'Viewed the list of curriculums.',
            'time' => Carbon::now()
        ]);
        // $curriculums->makeHidden(['course_id', 'strand_id']);
        return $curriculums;
      }else{
          //record in activity log
          $activityLog = ActivityLog::create([
              'user_id' => $user->id,
              'activity' => 'Attempted to view the list of curriculums.',
              'time' => Carbon::now()
          ]);
          return response()->json([
              'message' => 'You are not authorized to view curriculum records.'
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
      //  check if user have the priviledge to create curriculum record.
      $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges($user->id, Config::get('settings.curriculum_management'), 'create_priv');
      if ($isAuthorized) {
        $validator = Validator::make($request->all(),[
          'curriculum_title' => 'required|unique:curriculums,curriculum_title|string',
          'curriculum_desc' => 'required|string',
          'course_id' => 'nullable|numeric',
          'strand_id' => 'nullable|numeric'
        ]);

        // check if data if validator fails
        if ($validator->fails()) {
          return response()
          ->json([
            'message' => 'Failed to create new curriculum record.',
            'errors' => $validator->errors()
          ], 400); // 400: Bad request
        }
        else {
          $curriculum_data = $request->all();
          $curriculum_data['last_updated_by'] = Auth::user()->id;
          if (empty($curriculum_data['course_id']) && empty($curriculum_data['strand_id'])) {
            return response()
            ->json([
              'message' => 'Failed to create new curriculum record.',
              'errors' => [
                'curriculum' => ['The curriculum should belong to atleast one course or one strand.']
              ]
            ], 400); // 400: Bad request
          }else {
            try {
              $curriculum = Curriculum::create($curriculum_data);
              // check if record is successfully created.
              if ($curriculum) {
                //record in activity log
                $activityLog = ActivityLog::create([
                    'user_id' => $user->id,
                    'activity' => 'Created a new curriculum.',
                    'time' => Carbon::now()
                ]);
                return response()->json(['message' => 'New curriculum record successfully created.'], 200);
              }else {
                return response()->json(['message' => 'Failed to create new curriculum record.'], 500); // server error
              }
            } catch (Exception $e) {
              report($e);
              return false;
            }
          }
        }
      }else{
          //record in activity log
          $activityLog = ActivityLog::create([
              'user_id' => $user->id,
              'activity' => 'Attempted to create a new curriculum.',
              'time' => Carbon::now()
          ]);
          return response()->json([
              'message' => 'You are not authorized to create curriculum records.'
          ], 401); // 401: Unauthorized
      }

    } // end of function store()

    /**
     * Display the specified resource.
     *
     * @param  \App\Curriculum  $curriculum
     * @return \Illuminate\Http\Response
     */
    public function show(Curriculum $curriculum)
    {
      $user = Auth::user();
      //Check if user has permission to view instructors
      $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges($user->id, Config::get('settings.curriculum_management'), 'read_priv');

      if($isAuthorized){
        //record in activity log
        $activityLog = ActivityLog::create([
            'user_id' => $user->id,
            'activity' => 'Viewed the details of ' . $curriculum->curriculum_title . '.',
            'time' => Carbon::now()
        ]);

        if($curriculum->course_id != null){
            $curriculum = Curriculum::where('id', $curriculum->id)->with('course', 'curriculum_subjects')->get();
        }elseif($curriculum->strand_id != null){
            $curriculum = Curriculum::where('id', $curriculum->id)->with('strand', 'curriculum_subjects')->get();
        }
        return $curriculum;
      }else{
          //record in activity log
          $activityLog = ActivityLog::create([
              'user_id' => $user->id,
              'activity' => 'Attempted to view the details of ' . $curriculum->curriculum_title . '.',
              'time' => Carbon::now()
          ]);
          return response()->json([
              'message' => 'You are not authorized to view curriculum records.'
          ],401); // 401 Unauthorized
      }
    } // end of function show()

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Curriculum  $curriculum
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Curriculum $curriculum)
    {
      $user = Auth::user();
      // check if user have the priviledge to update curriculum record.

      $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges($user->id, Config::get('settings.curriculum_management'), 'update_priv');

      if($isAuthorized){
        // Only submit this value if it was changed
        // Added to avoid unique contraint violation
        if($curriculum->curriculum_title == $request['curriculum_title']) {
          $newData = $request->except('curriculum_title');
        } else {
          $newData = $request->all();
        }
        $validator = Validator::make($newData,[
          'curriculum_title' => 'unique:curriculums,curriculum_title|string',
          'curriculum_desc' => 'string',
          'course_id' => 'nullable|numeric',
          'strand_id' => 'nullable|numeric'
        ]);

        // check if data if validator fails
        if ($validator->fails()) {
          return response()
          ->json([
            'message' => 'Failed to update curriculum record.',
            'errors' => $validator->errors()
          ], 400); // 400: Bad request
        }
        else {
          $curriculum_data = $request->all();
          $curriculum_data['last_updated_by'] = Auth::user()->id;
            try {
              $check = $curriculum->update($curriculum_data);
              // check if record is successfully updated.
              if ($check) {
                //record in activity log
                $activityLog = ActivityLog::create([
                    'user_id' => $user->id,
                    'activity' => 'Updated the curriculum ' . $curriculum->curriculum_title . '.',
                    'time' => Carbon::now()
                ]);
                return response()->json(['message' => 'Curriculum record successfully updated.'], 200);
              }else {
                return response()->json(['message' => 'Failed to update curriculum record.'], 500); // server error
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
              'activity' => 'Attempted to update the details of ' . $curriculum->curriculum_title . '.',
              'time' => Carbon::now()
          ]);
          return response()->json([
              'message' => 'You are not authorized to update curriculum records.'
          ], 401); // 401: Unauthorized
      }
    } // end of function update()

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Curriculum  $curriculum
     * @return \Illuminate\Http\Response
     */
    public function destroy(Curriculum $curriculum)
    {
      $user = Auth::user();
      //  check if user has the priviledge to delete curriculum record.
      $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges($user->id, Config::get('settings.curriculum_management'), 'delete_priv');
      if($isAuthorized){
        try {
          $curriculum->delete();
          //record in activity log
          $activityLog = ActivityLog::create([
              'user_id' => $user->id,
              'activity' => 'Deleted the curriculum ' . $curriculum->curriculum_title . '.',
              'time' => Carbon::now()
          ]);
          return response()->json(['message' => 'Curriculum record successfully deleted.'], 200);
        } catch (Exception $e) {
          report($e);
          return false;
        }
      }else{
          //record in activity log
          $activityLog = ActivityLog::create([
              'user_id' => $user->id,
              'activity' => 'Attempted to delete the curriculum ' . $curriculum->curriculum_title . '.',
              'time' => Carbon::now()
          ]);
          return response()->json([
              'message' => 'You are not authorized to delete curriculum records.'
          ],401); // 401: Unauthorized
      }

    } // end of function destroy()

    public function showCurriculumSubjects(Curriculum $curriculum){
      $user = Auth::user();
      //Check if user has permission to view instructors
      $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges($user->id, Config::get('settings.curriculum_management'), 'read_priv');

      if($isAuthorized){
        //record in activity log
        $activityLog = ActivityLog::create([
            'user_id' => $user->id,
            'activity' => 'Viewed the subjects of curriculum ' . $curriculum->curriculum_title . '.',
            'time' => Carbon::now()
        ]);
        return $curriculum->curriculum_subjects;
      }else{
          //record in activity log
          $activityLog = ActivityLog::create([
              'user_id' => $user->id,
              'activity' => 'Attempted to view the subjects of curriculum ' . $curriculum->curriculum_title . '.',
              'time' => Carbon::now()
          ]);
          return response()->json([
              'message' => 'You are not authorized to view curriculum records.'
          ],401); // 401: Unauthorized
      }
    } // end of function showCurriculumSubjects
}
