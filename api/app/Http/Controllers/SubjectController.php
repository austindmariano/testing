<?php

namespace App\Http\Controllers;

use App\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use App\ActivityLog;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;

class SubjectController extends Controller
{
    /**
     * Display a listing of all subject records.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        //Check if user has permission to view subjects
        $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges($user->id, Config::get('settings.subject_management'), 'read_priv');
        if ($isAuthorized) {
            if($request->query() != null){
                if($request->query('sort')!=null){
                    if($request->query('sort')!=""){
                        $subjects = Subject::orderBy($request->query('sort'))->get();
                    }else{
                        $subjects = Subject::orderBy('subject_code', 'asc')->get();
                    }
                }else{
                    $subjects = Subject::where($request->query())->get();
                }
            }else{
                // return all subjects
                 // $subjects = Subject::all();
                 // return all subject in DESC using ID
                 $subjects = Subject::orderBy('id', 'DESC')->get();;

            }
            //record in activity log
            $activityLog = ActivityLog::create([
                'user_id' => $user->id,
                'activity' => 'Viewed the list of subjects.',
                'time' => Carbon::now()
            ]);
            return $subjects;
        }else{
            //record in activity log
            $activityLog = ActivityLog::create([
                'user_id' => $user->id,
                'activity' => 'Attempted to view the list of subjects.',
                'time' => Carbon::now()
            ]);
            return response()->json([
                'message' => 'You are not authorized to view subject records.'
            ],401);     //401: Unauthorized
        }
    } //end of function index()

    /**
     * Create new subject record.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //check if user has priviledge to create subject record
        $user = Auth::user();
        //Check if user has permission to view instructors
        $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges($user->id, Config::get('settings.subject_management'), 'create_priv');
        if ($isAuthorized) {
            $validator = Validator::make($request->all(),[
                'subject_code' => 'required|unique:subjects,subject_code',
                'subject_description' => 'required|string',
                'units' => 'required|numeric',
                'lec' => 'required|numeric',
                'lab' => 'required|numeric',
                'active' => 'required|boolean',
            ]);

            // if data validation fails
            if ($validator->fails()) {
                return response()
                ->json([
                    'message' => 'Failed to create a new subject.',
                    'errors' => $validator->errors()
                ], 400); // Bad request, returns an error message
            }
            else {
                try {
                    $subjectData = $request->all();
                    $subjectData['last_updated_by'] = Auth::user()->id;
                    $subject = Subject::create($subjectData);
                    if ($subject) {
                        //record in activity log
                        $activityLog = ActivityLog::create([
                            'user_id' => $user->id,
                            'activity' => 'Created a new subject.',
                            'time' => Carbon::now()
                        ]);
                        return response()->json(['message' => 'New subject record successfully created.'], 201);
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
                'activity' => 'Attempted to create a new subject.',
                'time' => Carbon::now()
            ]);
            return response()->json([
                'message' => 'You are not authorized to create subject records.'
            ],401); // 401: Unauthorized
        }

    } // end of function store()

    /**
     * Display the specified subject record.
     *
     * @param  \App\Subject  $subject
     * @return \Illuminate\Http\Response
     */
    public function show(Subject $subject)
    {
        $user = Auth::user();
        //Check if user has permission to view instructors
        $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges($user->id, Config::get('settings.subject_management'), 'read_priv');

        if($isAuthorized){
            //record in activity log
            $activityLog = ActivityLog::create([
                'user_id' => $user->id,
                'activity' => 'Viewed the details of ' . $subject->subject_description . '.',
                'time' => Carbon::now()
            ]);
            return $subject;
        }else{
            //record in activity log
            $activityLog = ActivityLog::create([
                'user_id' => $user->id,
                'activity' => 'Attempted to view the details of ' . $subject->subject_description . '.',
                'time' => Carbon::now()
            ]);
            return response()->json([
                'message' => 'You are not authorized to view subject records.'
            ],401); // 401: Unauthorized
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Subject  $subject
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Subject $subject)
    {
        $user = Auth::user();
        //Check if user has permission to view instructors
        $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges($user->id, Config::get('settings.subject_management'), 'update_priv');

        if($isAuthorized){
            if($subject->subject_code == $request['subject_code']){
              $newData = $request->except('subject_code');
            } else {
              $newData = $request->all();
            }
            $validator = Validator::make($newData,[
                'subject_code' => 'nullable|unique:subjects,subject_code',
                'subject_description' => 'nullable|string',
                'units' => 'nullable|numeric',
                'lec' => 'nullable|numeric',
                'lab' => 'nullable|numeric',
                'active' => 'nullable|boolean|numeric',
            ]);

            // if data validation fails
            if ($validator->fails()) {
                return response()
                ->json([
                    'message' => 'Failed to update subject record.',
                    'errors' => $validator->errors()
                ], 400); // 400: Bad request, returns an error message
            }
            else {
                $newData['last_updated_by'] = Auth::user()->id;
                try {
                    $subject->update($newData);
                    //record in activity log
                    $activityLog = ActivityLog::create([
                        'user_id' => $user->id,
                        'activity' => 'Updated the subject ' . $subject->subject_description . '.',
                        'time' => Carbon::now()
                    ]);
                    return response()->json(["message" => "Subject record succesfully updated."], 200);
                } catch (Exception $e) {
                    report($e);
                    return false;
                }
            }
        }else{
            //record in activity log
            $activityLog = ActivityLog::create([
                'user_id' => $user->id,
                'activity' => 'Attempted to update the details of ' . $subject->subject_description . '.',
                'time' => Carbon::now()
            ]);
            return response()->json([
                'message' => 'You are not authorized to update subject records.'
            ],401); // 401: Unauthorized
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Subject  $subject
     * @return \Illuminate\Http\Response
     */
    public function destroy(Subject $subject)
    {
        $user = Auth::user();
        //Check if user has a privilege to delete records
        $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges($user->id, Config::get('settings.subject_management'), 'delete_priv');
        if($isAuthorized){
            $subject->delete();
            //record in activity log
            $activityLog = ActivityLog::create([
                'user_id' => $user->id,
                'activity' => 'Deleted the subject ' . $subject->subject_description . '.',
                'time' => Carbon::now()
            ]);
            return response()->json(["message" => "Subject record successfully deleted."], 200);
        }else{
            //record in activity log
            $activityLog = ActivityLog::create([
                'user_id' => $user->id,
                'activity' => 'Attempted to delete the subject ' . $subject->subject_description . '.',
                'time' => Carbon::now()
            ]);
            return response()->json([
                'message' => 'You are not authorized to delete subject records.'
            ],401); // 401: Unauthorized
        }
    }

    public function showSubjectCurriculums(Subject $subject){
        $user = Auth::user();
        //Check if user has a privilege to read records
        $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges($user->id, Config::get('settings.subject_management'), 'read_priv');
        if ($isAuthorized) {
            //record in activity log
            $activityLog = ActivityLog::create([
                'user_id' => $user->id,
                'activity' => 'Viewed the subject curriculums.',
                'time' => Carbon::now()
            ]);
            return $subject->curriculum;
        }else{
            //record in activity log
            $activityLog = ActivityLog::create([
                'user_id' => $user->id,
                'activity' => 'Attempted to view the subject curriculums.',
                'time' => Carbon::now()
            ]);
            return response()->json([
                'message' => 'You are not authorized to view the subject curriculums.'
            ],401); // 401: Unauthorized
        }

    }
}
