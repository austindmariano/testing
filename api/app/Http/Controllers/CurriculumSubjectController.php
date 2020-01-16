<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\CurriculumSubject;
use App\Subject;
use App\Curriculum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use App\ActivityLog;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;

class CurriculumSubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      $user = Auth::user();
      //Check if user has permission to view curriculum subjects
      $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges($user->id, Config::get('settings.curriculum_management'), 'read_priv');
      if ($isAuthorized) {
          if($request->query() != null){
              if($request->query('sort')!=null){
                  $curriculum_subjects = CurriculumSubject::orderBy($request->query('sort'))->with('subject', 'curriculum')->get();
              }else{
                  $curriculum_subjects = CurriculumSubject::where($request->query())->with('subject', 'curriculum')->get();
              }
          }else{
            // $curriculum_subjects = CurriculumSubject::all();
            $curriculum_subjects = CurriculumSubject::with('subject', 'curriculum', 'semester')->orderBy('id', 'DESC')->get();
          }
          //record in activity log
          $activityLog = ActivityLog::create([
              'user_id' => $user->id,
              'activity' => 'Viewed the list of curriculum subjects.',
              'time' => Carbon::now()
          ]);
          return $curriculum_subjects;
      }else{
          //record in activity log
          $activityLog = ActivityLog::create([
              'user_id' => $user->id,
              'activity' => 'Attempted to view the list of curriculum subjects.',
              'time' => Carbon::now()
          ]);
          return response()->json([
              'message' => 'You are not authorized to view curriculum subject records.'
          ],401);     //401: Unauthorized
      }
    } // end of function index()

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Curriculum $curriculum)
    {
      $user = Auth::user();
      //check if user has priviledge to add curriculum subject record.
      $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges($user->id, Config::get('settings.curriculum_management'), 'create_priv');
      if ($isAuthorized) {
        $curriculum_subject_data = $request->all();
        // $curriculum_subject_data['curriculum_id'] = $curriculum->id;
        $validator = Validator::make($request->all(),[
          'subject_id' => 'required|numeric',
          'curriculum_id' => 'required|numeric',
          'year_level' => 'required|string'
        ]);

        // check fi data is validator
        if ($validator->fails()) {
          return response()
          ->json([
            'message' => 'Failed to create new curriculum subject record.',
            'errors' => $validator->errors()
          ], 400); // 400: Bad request
        }else {
          // $setting = DB::table('settings')->first();
          // $curriculum_subject_data['semester_id'] = $setting->current_semester;
          $curriculum_subject = CurriculumSubject::select('*')
          ->where('subject_id', $curriculum_subject_data['subject_id'])
          ->where('curriculum_id', $curriculum_subject_data['curriculum_id'])
          ->take(1)
          ->with('curriculum')
          ->get();

          // check if subject is already exist in the curriculum
           if($curriculum_subject->isNotEmpty()){
            $title = $curriculum_subject[0]->curriculum->curriculum_title;
             return response()->json([
               'message' => 'Failed to create curriculum subject record.',
               'error' => $title . " already have this subject"
             ], 400);
           }else{
             $curriculum_subject_data['last_updated_by'] = Auth::user()->id;
             try {
               $curriculum_subject = CurriculumSubject::create($curriculum_subject_data);
               if ($curriculum_subject) {
                 //record in activity log
                 $activityLog = ActivityLog::create([
                     'user_id' => $user->id,
                     'activity' => 'Created a new curriculum subject.',
                     'time' => Carbon::now()
                 ]);
                 return response()->json(['message' => 'New curriculum subject record successfully created.'], 200);
               }else {
                 return response()->json(['message' => 'Failed to create new curriculum subject record.'], 500); // server error
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
              'activity' => 'Attempted to create a new curriculum subject.',
              'time' => Carbon::now()
          ]);
          return response()->json([
              'message' => 'You are not authorized to create curriculum subject records.'
          ],401); // 401: Unauthorized
      }

    } // end of function store()

    /**
     * Display the specified resource.
     *
     * @param  \App\CurriculumSubject  $curriculumSubject
     * @return \Illuminate\Http\Response
     */
    public function show(CurriculumSubject $curriculum_subject)
    {
      $user = Auth::user();
      //Check if user has permission to view instructors
      $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges($user->id, Config::get('settings.curriculum_management'), 'read_priv');
      // subject code for logs
      $subject_code = DB::table('subjects')
      ->select('subject_code')
      ->where('id', $curriculum_subject->subject_id)->first();

      // curriculum title for logs
      $curriculum_title = DB::table('curriculums')
      ->select('curriculum_title')
      ->where('id', $curriculum_subject->curriculum_id)->first();

      if($isAuthorized){

          //record in activity log
          $activityLog = ActivityLog::create([
              'user_id' => $user->id,
              'activity' => 'Viewed the subject named ' . $subject_code->subject_code . ' from curriculum ' . $curriculum_title->curriculum_title . '.',
              'time' => Carbon::now()
          ]);
          // added this code
          $data = CurriculumSubject::select('*')
          ->where('id', $curriculum_subject->id)
          ->with('subject', 'semester', 'curriculum')
          ->get();
          // $data->makeHidden(['subject_id', 'semester_id', 'curriculum_id']);
          return $data;

          // return $curriculum_subject;
      }else{
          //record in activity log
          $activityLog = ActivityLog::create([
              'user_id' => $user->id,
              'activity' => 'Attempted to view the subject named ' . $subject_code->subject_code . ' from curriculum ' . $curriculum_title->curriculum_title . '.',
              'time' => Carbon::now()
          ]);
          return response()->json([
              'message' => 'You are not authorized to view curriculum subject records.'
          ],401); // 401: Unauthorized
      }
    } // end of funtion show()

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CurriculumSubject  $curriculumSubject
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CurriculumSubject $curriculum_subject)
    {
      $user = Auth::user();
      //Check if user has permission to updpate curriculum subject record.
      $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges($user->id, Config::get('settings.curriculum_management'), 'update_priv');

      // subject code for logs
      $subject_code = DB::table('subjects')
      ->select('subject_code')
      ->where('id', $curriculum_subject->subject_id)->first();

      // curriculum title for logs
      $curriculum_title = DB::table('curriculums')
      ->select('curriculum_title')
      ->where('id', $curriculum_subject->curriculum_id)->first();

      if($isAuthorized){
        $validator = Validator::make($request->all(),[
          'subject_id' => 'numeric',
          'curriculum_id' => 'numeric',
          'year_level' => 'string'
        ]);

        // check fi data is validator
        if ($validator->fails()) {
          return response()
          ->json([
            'message' => 'Failed to create new curriculum subject record.',
            'errors' => $validator->errors()
          ], 400); // 400: Bad request
        }else {

          $curriculum_subject_data = $request->all();
          // return $curriculum_subject;
          // $data = CurriculumSubject::select('*')
          // ->where('subject_id', $curriculum_subject[0]->curriculum->curriculum_title)
          // ->where('curriculum_id', $curriculum_subject[0]->subject->curriculum_title)
          // ->take(1)
          // ->with('curriculum')
          // ->get();
          // // check if subject is already exist in the curriculum
          // if($data->isNotEmpty()){
          //   $title = $curriculum_subject[0]->curriculum->curriculum_title;
          //    return response()->json([
          //      'message' => 'Failed to update curriculum subject record.',
          //      'error' => $title . " already have this subject"
          //    ], 400);
          // }else{
            $curriculum_subject_data['last_updated_by'] = Auth::user()->id;
            try {
              $check = $curriculum_subject->update($curriculum_subject_data);
              if ($check) {
                //record in activity log
                $activityLog = ActivityLog::create([
                    'user_id' => $user->id,
                    'activity' => 'Updated the curriculum subject named ' . $subject_code->subject_code . ' of  ' . $curriculum_title->curriculum_title. '.',
                    'time' => Carbon::now()
                ]);
                return response()->json(['message' => 'Curriculum subject record successfully updated.'], 200);
              }else {
                return response()->json(['message' => 'Failed to update curriculum subject record.'], 500); // server error
              }
            } catch (Exception $e) {
              report($e);
              return false;
            }
          // }
        }
      }else{
          //record in activity log
          $activityLog = ActivityLog::create([
              'user_id' => $user->id,
              'activity' => 'Attempted to update the curriculum subject named ' . $subject_code->subject_code . ' of  ' . $curriculum_title->curriculum_title. '.',
              'time' => Carbon::now()
          ]);
          return response()->json([
              'message' => 'You are not authorized to update curriculum subject records.'
          ],401); // 401: Unauthorized
      }

    } // end of function update()

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CurriculumSubject  $curriculumSubject
     * @return \Illuminate\Http\Response
     */
    public function destroy(CurriculumSubject $curriculum_subject)
    {
      $user = Auth::user();
      //Check if user has a privilege to delete records
      $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges($user->id, Config::get('settings.curriculum_management'), 'delete_priv');

      // subject code for logs
      $subject_code = DB::table('subjects')
      ->select('subject_code')
      ->where('id', $curriculum_subject->subject_id)->first();

      // curriculum title for logs
      $curriculum_title = DB::table('curriculums')
      ->select('curriculum_title')
      ->where('id', $curriculum_subject->curriculum_id)->first();

      if($isAuthorized){
        try {
          $curriculum_subject->delete();
          $activityLog = ActivityLog::create([
              'user_id' => $user->id,
              'activity' => 'Deleted the curriculum subject named ' . $subject_code->subject_code . ' of  ' . $curriculum_title->curriculum_title.   '.',
              'time' => Carbon::now()
          ]);
          return response()->json(['message' => 'Curriculum subject record successfully deleted.'], 200);
        } catch (Exception $e) {
          report($e);
          return false;
        }
      }else{
          //record in activity log
          $activityLog = ActivityLog::create([
              'user_id' => $user->id,
              'activity' => 'Attempted to delete the curriculum subject named ' . $subject_code->subject_code . ' of  ' . $curriculum_title->curriculum_title.  '.',
              'time' => Carbon::now()
          ]);
          return response()->json([
              'message' => 'You are not authorized to delete subject records.'
          ],401); // 401: Unauthorized
      }


    } // end of function destroy()

    // testing only
    public function getSubject(CurriculumSubject $curriculum_subject){
      $subject_code = DB::table('subjects')
      ->select('*')
      ->where('id', $curriculum_subject->subject_id)->first();
       return $subject_code->subject_code;
    }
}
