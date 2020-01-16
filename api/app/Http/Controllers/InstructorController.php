<?php

namespace App\Http\Controllers;

use App\Instructor;
use App\ActivityLog;
use App\InstructorAvailability;
use App\InstructorPreferredSubject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;

class InstructorController extends Controller
{
    use AuthenticatesUsers;

    // for testing purpose only
    /* public function settings(){
        return response()->json([
            'message' => Config::get('settings')
        ]);
    } */

    /* TODO: Check if we can move checking of permission/privilege into a middleware
             or service provider so we can minimize the length of code in controllers */

    /**
     * Returns an array containing all instructor records
     *
     * @return array of Instructor objects
     */
    public function index(Request $request){
        $user = Auth::user();
        //Check if user has permission to view instructors
        $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges($user->id, Config::get('settings.instructor_management'), 'read_priv');

        if($isAuthorized){
            if($request->query() != null){
                if($request->query('sort')!=null){
                    $instructors = Instructor::with('availabilities','preferred_subjects.subject:id,subject_code,subject_description,units,lec,lab')->orderBy($request->query('sort'))->get();
                }else{
                    $instructors = Instructor::with('availabilities','preferred_subjects.subject:id,subject_code,subject_description,units,lec,lab')->where($request->query())->get();
                }
            }else{
                $instructors = Instructor::with('availabilities', 'preferred_subjects')->get();
            }
            //record in activity log
            $activityLog = ActivityLog::create([
                'user_id' => $user->id,
                'activity' => 'Viewed the list of instructors.',
                'time' => Carbon::now()
            ]);
            return $instructors;
        }else{
            //record in activity log
            $activityLog = ActivityLog::create([
                'user_id' => $user->id,
                'activity' => 'Attempted to view list of instructors.',
                'time' => Carbon::now()
            ]);
            return response()->json([
                'message' => 'You are not authorized to view instructor records.'
            ],401);
        }

    }

    /**
     * Returns a specific instructor record
     *
     * @param Instructor $instructor
     * @return Instructor
     */
    public function show(Instructor $instructor){
        $user = Auth::user();
        //Check if user has permission to view instructors
        $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges($user->id, Config::get('settings.instructor_management'), 'read_priv');

        if($isAuthorized){
            //record in activity log
            $activityLog = ActivityLog::create([
                'user_id' => $user->id,
                'activity' => 'Viewed instructor record of ' . $instructor->last_name . ', ' . $instructor->first_name . '.',
                'time' => Carbon::now()
            ]);
            $instructor['availabilities'] = $instructor->availabilities()->get();
            $instructor['preferred_subjects'] = $instructor->preferred_subjects()->get();
            return $instructor;
        }else{
            //record in activity log
            $activityLog = ActivityLog::create([
                'user_id' => $user->id,
                'activity' => 'Attempted to view instructor record of ' . $instructor->last_name . ', ' . $instructor->first_name . '.',
                'time' => Carbon::now()
            ]);
            return response()->json([
                'message' => 'You are not authorized to view instructor records.'
            ],401);
        }
    }

    /**
     * Adds a new instructor record
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request){
        $user = Auth::user();
        //check first if user has permission to create Instructor records before proceeding
        $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges($user->id, Config::get('settings.instructor_management'), 'create_priv');

        if($isAuthorized){
            //validate request
            $validator = Validator::make($request->all(),[
                'employee_id' => 'required|unique:instructors',
                'first_name' => 'required|string',
                'middle_name' => 'nullable|string',
                'last_name' => 'required|string',
                'birth_date' => 'required|date',
                'gender' => 'required|string|in:"Male", "Female"',
                'email' => 'nullable|email|unique:instructors',
                'contact_no' => 'required|string',
                'address' => 'required|string',
                'city' => 'nullable|string',
                'postal_code' => 'required',
                'province' => 'required',
                'work_experience' => 'nullable',
                'certifications' => 'nullable',
                'educational_attainment' => 'nullable',
                'active' => 'required|boolean'
            ]);

            //if validation fails
            if ($validator->fails()){
                return response()->json([
                    'message' => 'Failed to create a new instructor record.',
                    'errors' => $validator->errors()
                ],400);    //Bad request
            }

            //create a new instructor record
            $instructorData = $request->all();
            $instructorData['last_updated_by'] = Auth::user()->id;
            $instructor = Instructor::create($instructorData);

            if($instructor){
                //record in activity log
                $activityLog = ActivityLog::create([
                    'user_id' => $user->id,
                    'activity' => 'Added new instructor record for ' . $instructor->last_name . ', ' . $instructor->first_name . '.',
                    'time' => Carbon::now()
                ]);

                //TODO:  create a user account for instructor
                //NOTE:  added last_insert_id on 10/7/2019 to be able to continue
                //       adding time availability and preferred subjects to newly
                //       added instructor
                return response()->json([
                    'message' => 'Instructor record successfully created.',
                    'last_insert_id' => $instructor->id
                ],200);    //instructor created
            }
        }else{
            //record in activity log
            $activityLog = ActivityLog::create([
                'user_id' => $user->id,
                'activity' => 'Attempted to add new instructor record.',
                'time' => Carbon::now()
            ]);
            return response()->json([
                'message' => 'You are not authorized to create instructor records.'
            ],401);
        }

    }

    /**
     * Updates a specific instructor record
     *
     * @param Request $request
     * @param Instructor $instructor
     * @return \Illuminate\Http\JsonResponse
    */
    public function update(Request $request, Instructor $instructor){
        $user = Auth::user();
        //check first if user has permission to update Instructor records before proceeding
        $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges($user->id, Config::get('settings.instructor_management'), 'update_priv');

        if($isAuthorized){
            // Only submit these values if they were changed
            // Added to avoid unique contraint violation
            if($instructor->employee_id == $request['employee_id']
                && $instructor->email == $request['email']) {
              $newData = $request->except('employee_id', 'email');
            } else if($instructor->employee_id == $request['employee_id']) {
              $newData = $request->except('employee_id');
            } else if($instructor->email == $request['email']){
              $newData = $request->except('email');
            } else {
              $newData = $request->all();
            }
            $validator = Validator::make($newData,[
                'employee_id' => 'nullable|unique:instructors',
                'first_name' => 'nullable|string',
                'middle_name' => 'nullable|string',
                'last_name' => 'nullable|string',
                'birth_date' => 'nullable|date',
                'gender' => 'nullable|string|in:"Male", "Female"',
                'email' => 'nullable|email|unique:instructors',
                'contact_no' => 'nullable|string',
                'address' => 'nullable|string',
                'city' => 'nullable|string',
                'postal_code' => 'nullable',
                'province' => 'nullable|string',
                'work_experience' => 'nullable|string',
                'certifications' => 'nullable|string',
                'educational_attainment' => 'nullable|string',
                'active' => 'nullable|boolean|numeric'
            ]);

            //if validation fails
            if ($validator->fails()){
                return response()->json([
                    'message' => 'Failed to create a new instructor record.',
                    'errors' => $validator->errors()
                ],400);    //Bad request
            }

            $newData['last_updated_by'] = Auth::user()->id;
            $instructor->update($newData);

            //record in activity log
            $activityLog = ActivityLog::create([
                'user_id' => $user->id,
                'activity' => 'Updated instructor record of ' . $instructor->last_name . ', ' . $instructor->first_name . '.',
                'time' => Carbon::now()
            ]);

            return response()->json([
                'message' => 'Instructor record successfully updated.'
            ], 200);
        }else{
            //record in activity log
            $activityLog = ActivityLog::create([
                'user_id' => $user->id,
                'activity' => 'Attempted to update instructor record of ' . $instructor->last_name . ', ' . $instructor->first_name . '.',
                'time' => Carbon::now()
            ]);
            return response()->json([
                'message' => 'You are not authorized to update instructor records.'
            ],401);
        }
    }

    /**
     * Deletes a specific instructor record
     *
     * @param Instructor $instructor
     * @return \Illuminate\Http\JsonResponse
    */
    public function destroy(Instructor $instructor){
        $user = Auth::user();
        //check first if user has permission to delete Instructor records before proceeding
        $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges($user->id, Config::get('settings.instructor_management'), 'delete_priv');

        if($isAuthorized){
            $instructor->delete();

            //record in activity log
            $activityLog = ActivityLog::create([
                'user_id' => $user->id,
                'activity' => 'Deleted instructor record of ' . $instructor->last_name . ', ' . $instructor->first_name . '.',
                'time' => Carbon::now()
            ]);

            return response()->json([
                'message' => 'Instructor record successfully deleted.'
            ], 200);
        }else{
            //record in activity log
            $activityLog = ActivityLog::create([
                'user_id' => $user->id,
                'activity' => 'Attempted to delete instructor record of ' . $instructor->last_name . ', ' . $instructor->first_name . '.',
                'time' => Carbon::now()
            ]);
            return response()->json([
                'message' => 'You are not authorized to delete instructor records.'
            ],401);
        }

    }

    /**
     * Returns the time availabilities of instructors
     *
     * @param Instructor $instructor
     * @return array of InstructorAvailability
    */
    public function availabilities(Instructor $instructor=null){
        $user = Auth::user();
        //Check if user has permission to view instructors
        $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges($user->id, Config::get('settings.instructor_management'), 'read_priv');

        if($isAuthorized){
            if($instructor == null)
            //use this to display all instructor with all their time availabilities
            //$availabilities = Instructor::with('availabilities')->get();

            //use this to display all time availabilities only
            /*$availabilities = InstructorAvailability::select(array('id', 'instructor_id', 'day', 'time_start', 'time_end'))
                                ->where('academic_year_id', \Config::get('settings.current_ay'))
                                ->where('semester_id', \Config::get('settings.current_sem'))->get();*/
                $availabilities = InstructorAvailability::select(array('id', 'instructor_id', 'day', 'time_start', 'time_end'))->get();
            else
                $availabilities = $instructor->availabilities()->get();

            //record in activity log
            $activityLog = ActivityLog::create([
                'user_id' => $user->id,
                'activity' => 'Viewed instructor availabilities.',
                'time' => Carbon::now()
            ]);
            return $availabilities;

        }else{
            //record in activity log
            $activityLog = ActivityLog::create([
                'user_id' => $user->id,
                'activity' => 'Attempted to view instructor availabilities.',
                'time' => Carbon::now()
            ]);
            return response()->json([
                'message' => 'You are not authorized to view instructor records.'
            ],401);
        }

    }

    public function addAvailability(Request $request, Instructor $instructor){
        $user = Auth::user();
        //Check if user has permission to add instructor records
        $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges($user->id, Config::get('settings.instructor_management'), 'create_priv');

        if($isAuthorized){
            //validate request
            $validator = Validator::make($request->all(),[
                'academic_year_id' => 'required|integer',
                'semester_id' => 'required|integer',
                'day' => 'required|string',
                'time_start' => 'required|date_format:H:i',
                'time_end' => 'required|date_format:H:i',
                'active' => 'required|boolean'
            ]);

            //if validation fails
            if ($validator->fails()){
                return response()->json([
                    'message' => 'Failed to add instructor\'s time availability.',
                    'errors' => $validator->errors()
                ],400);    //Bad request
            }
            $request['last_updated_by'] = Auth::user()->id;
            $availability = $instructor->availabilities()->create($request->all());

            if($availability){
                //record in activity log
                $activityLog = ActivityLog::create([
                    'user_id' => $user->id,
                    'activity' => 'Added new time availability for ' . $instructor->last_name . ', ' . $instructor->first_name . '.',
                    'time' => Carbon::now()
                ]);
                return response()->json([
                    "message"=>"Time availability successfully recorded."
                ], 200);
            }
        }else{
            //record in activity log
            $activityLog = ActivityLog::create([
                'user_id' => $user->id,
                'activity' => 'Attempted to add new time availability for ' . $instructor->last_name . ', ' . $instructor->first_name . '.',
                'time' => Carbon::now()
            ]);
            return response()->json([
                'message' => 'You are not authorized to create instructor records.'
            ],401);
        }


    }

    public function updateAvailability(Request $request, Instructor $instructor, InstructorAvailability $instructoravailability){
        $user = Auth::user();
        //Check if user has permission to update instructor records
        $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges($user->id, Config::get('settings.instructor_management'), 'update_priv');

        if($isAuthorized){
            //validate request
            $validator = Validator::make($request->all(),[
                'academic_year_id' => 'nullable|integer',
                'semester_id' => 'nullable|integer',
                'day' => 'nullable|string',
                'time_start' => 'nullable|date_format:H:i',
                'time_end' => 'nullable|date_format:H:i',
                'active' => 'nullable|boolean'
            ]);

            //if validation fails
            if ($validator->fails()){
                return response()->json([
                    'message' => 'Failed to update instructor\'s time availability.',
                    'errors' => $validator->errors()
                ],400);    //Bad request
            }

            $availabilityData = $request->all();
            $availabilityData['last_updated_by'] = Auth::user()->id;
            $instructoravailability->update($availabilityData);

            //record in activity log
            $activityLog = ActivityLog::create([
                'user_id' => $user->id,
                'activity' => 'Updated time availability of ' . $instructor->last_name . ', ' . $instructor->first_name . '.',
                'time' => Carbon::now()
            ]);

            return response()->json([
                'message' => 'Instructor time availability successfully updated.'
            ], 200);
        }else{
            //record in activity log
            $activityLog = ActivityLog::create([
                'user_id' => $user->id,
                'activity' => 'Attempted to update time availability of ' . $instructor->last_name . ', ' . $instructor->first_name . '.',
                'time' => Carbon::now()
            ]);

            return response()->json([
                'message' => 'You are not authorized to update instructor records.'
            ],401);
        }

    }

    /**
     * Deletes a specific instructor time availability
     *
     * @param Instructor $instructor
     * @param InstructorA $instructorAvailability
     * @return \Illuminate\Http\JsonResponse
    */
    public function deleteAvailability(Instructor $instructor, InstructorAvailability $instructoravailability){
        $user = Auth::user();
        //Check if user has permission to delete instructor records
        $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges($user->id, Config::get('settings.instructor_management'), 'delete_priv');

        if($isAuthorized){
            $instructoravailability->delete();

            //record in activity log
            $activityLog = ActivityLog::create([
                'user_id' => $user->id,
                'activity' => 'Deleted time availability of ' . $instructor->last_name . ', ' . $instructor->first_name . '.',
                'time' => Carbon::now()
            ]);

            return response()->json([
                'message' => 'Instructor time availability successfully deleted.'
            ], 200);
        }else{
            //record in activity log
            $activityLog = ActivityLog::create([
                'user_id' => $user->id,
                'activity' => 'Attempted to update time availability of ' . $instructor->last_name . ', ' . $instructor->first_name . '.',
                'time' => Carbon::now()
            ]);
            return response()->json([
                'message' => 'You are not authorized to delete instructor records.'
            ],401);
        }

    }

    /**
     * Returns instructors' preferred subjects
     *
     * @param Instructor $instructor
     * @return array of InstructorPreferredSubject
    */
    public function preferred_subjects(Instructor $instructor=null){
        $user = Auth::user();
        //Check if user has permission to view instructor records
        $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges($user->id, Config::get('settings.instructor_management'), 'read_priv');

        if($isAuthorized){
            if($instructor == null)
            //use this to display all instructor with all their preferred subjects
            //$preferred_subjects = Instructor::with('preferred_subjects.subject:id, subject_code, subject_description, units, lec, lab')->get();

            //use this to display all preferred subjects only
            // $preferred_subjects = InstructorPreferredSubject::select(array('id', 'instructor_id', 'subject_id'))
            //                     ->where('academic_year_id', \Config::get('settings.current_ay'))
            //                     ->where('semester_id', \Config::get('settings.current_sem'))->get();
            // TODO:  FIX THIS SO THAT ONLY THE PREFERRED SUBJECT ID, INSTRUCTOR ID, INSTRUCTOR NAME
            //        SUBJECT ID, SUBJECT CODE, SUBJECT DESCRIPTION, ACADEMIC YEAR & SEMESTER APPEAR
            $preferred_subjects = InstructorPreferredSubject::with(['Instructor','Subject', 'AcademicYear', 'Semester'])
              ->select(array('id', 'instructor_id', 'subject_id', 'academic_year_id', 'semester_id'))
              ->get();

            else
                $preferred_subjects = $instructor->preferred_subjects()->get();

            //record in activity log
            $activityLog = ActivityLog::create([
                'user_id' => $user->id,
                'activity' => 'Viewed preferred subjects of instructors.',
                'time' => Carbon::now()
            ]);
            return $preferred_subjects;
        }else{
            //record in activity log
            $activityLog = ActivityLog::create([
                'user_id' => $user->id,
                'activity' => 'Attempted to view preferred subjects of instructors.',
                'time' => Carbon::now()
            ]);
            return response()->json([
                'message' => 'You are not authorized to view instructor records.'
            ],401);
        }

    }

    public function addPreferredSubject(Request $request, Instructor $instructor){
        $user = Auth::user();
        //Check if user has permission to create instructor records
        $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges($user->id, Config::get('settings.instructor_management'), 'create_priv');

        if($isAuthorized){
            $validator = Validator::make($request->all(),[
                'academic_year_id' => 'required|integer',
                'semester_id' => 'required|integer',
                'subject_id' => 'required|integer',
                'active' => 'required|boolean'
            ]);

            //if validation fails
            if ($validator->fails()){
                return response()->json([
                    'message' => 'Failed to add instructor\'s preferred subject.',
                    'errors' => $validator->errors()
                ],400);    //Bad request
            }
            $request['instructor_id'] = $instructor->id;
            $request['last_updated_by'] = Auth::user()->id;
            $preferred_subject = $instructor->preferred_subjects()->create($request->all());

            if($preferred_subject){
                //record in activity log
                $activityLog = ActivityLog::create([
                    'user_id' => $user->id,
                    'activity' => 'Added preferred subject for ' . $instructor->last_name . ', ' . $instructor->first_name . '.',
                    'time' => Carbon::now()
                ]);
                return response()->json([
                    "message"=>"Preferred subject successfully recorded."
                ], 200);
            }else{
                //record in activity log
                $activityLog = ActivityLog::create([
                    'user_id' => $user->id,
                    'activity' => 'Attempted to add preferred subject for ' . $instructor->last_name . ', ' . $instructor->first_name . '.',
                    'time' => Carbon::now()
                ]);
                return response()->json([
                    'message' => 'You are not authorized to create instructor records.'
                ],401);
            }
        }

    }

    public function updatePreferredSubject(Request $request, Instructor $instructor, InstructorPreferredSubject $preferredsubject){
        $user = Auth::user();
        //Check if user has permission to update instructor records
        $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges($user->id, Config::get('settings.instructor_management'), 'update_priv');

        if($isAuthorized){
            //validate request
            $validator = Validator::make($request->all(),[
                'academic_year_id' => 'integer',
                'semester_id' => 'integer',
                'subject_id' => 'integer',
                'active' => 'boolean'
            ]);

            //if validation fails
            if ($validator->fails()){
                return response()->json([
                    'message' => 'Failed to update instructor\'s preferred subject.',
                    'errors' => $validator->errors()
                ],400);    //Bad request
            }

            $preferredSubjectData = $request->all();
            $preferredSubjectData['last_updated_by'] = Auth::user()->id;
            $preferredsubject->update($preferredSubjectData);

            //record in activity log
            $activityLog = ActivityLog::create([
                'user_id' => $user->id,
                'activity' => 'Updated preferred subject of ' . $instructor->last_name . ', ' . $instructor->first_name . '.',
                'time' => Carbon::now()
            ]);

            return response()->json([
                'message' => 'Instructor preferred subject successfully updated.'
            ], 200);
        }else{
            //record in activity log
            $activityLog = ActivityLog::create([
                'user_id' => $user->id,
                'activity' => 'Attempted to update preferred subject of ' . $instructor->last_name . ', ' . $instructor->first_name . '.',
                'time' => Carbon::now()
            ]);
            return response()->json([
                'message' => 'You are not authorized to update instructor records.'
            ],401);
        }

    }

    /**
     * Deletes a specific instructor preferred subject
     *
     * @param Instructor $instructor
     * @param InstructorPreferredSubject $preferredsubject
     * @return \Illuminate\Http\JsonResponse
    */
    public function deletePreferredSubject(Instructor $instructor, InstructorPreferredSubject $preferredsubject){
        $user = Auth::user();
        //Check if user has permission to delete instructor records
        $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges($user->id, Config::get('settings.instructor_management'), 'delete_priv');

        if($isAuthorized){
            $preferredsubject->delete();

            //record in activity log
            $activityLog = ActivityLog::create([
                'user_id' => $user->id,
                'activity' => 'Deleted preferred subject of ' . $instructor->last_name . ', ' . $instructor->first_name . '.',
                'time' => Carbon::now()
            ]);

            return response()->json([
                'message' => 'Instructor preferred subject successfully deleted.'
            ], 200);
        }else{
            //record in activity log
            $activityLog = ActivityLog::create([
                'user_id' => $user->id,
                'activity' => 'Attempted to delete preferred subject of ' . $instructor->last_name . ', ' . $instructor->first_name . '.',
                'time' => Carbon::now()
            ]);
            return response()->json([
                'message' => 'You are not authorized to delete instructor records.'
            ],401);
        }
    }

    public function instructorClass_Schedules(Instructor $instructor){
      $user = Auth::user();
      //Check if user has permission to view class schedules records.
      $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges($user->id, Config::get('settings.instructor_management'), 'read_priv');


      if($isAuthorized){
        //record in activity log
        $activityLog = ActivityLog::create([
            'user_id' => $user->id,
            'activity' => 'Viewed the class schedule of ' . $instructor->first_name . ' ' .$instructor->last_name . '.',
            'time' => Carbon::now()
        ]);
        return $instructor->class_schedules;
      }else{
          //record in activity log
          $activityLog = ActivityLog::create([
              'user_id' => $user->id,
              'activity' => 'Attempted to view the class schedules of ' . $instructor->first_name . ' ' .$instructor->last_name . '.',
              'time' => Carbon::now()
          ]);
          return response()->json([
              'message' => 'You are not authorized to view class schedule records.'
          ],401);      //401: Unauthorized
      }

    } //end of function instructorClass_Schedules()

    public function instructors_schedules(){
      $user = Auth::user();
      //Check if user has permission to view class schedule records.
      $isAuthorized = app('App\Http\Controllers\UserPrivilegeController')->checkPrivileges($user->id, Config::get('settings.instructor_management'), 'read_priv');


      if($isAuthorized){
        //record in activity log
        $activityLog = ActivityLog::create([
            'user_id' => $user->id,
            'activity' => 'Viewed the class schedule of all instructors',
            'time' => Carbon::now()
        ]);
        $instructors = Instructor::with('class_schedules')->get();

        return $instructors;
      }else{
          //record in activity log
          $activityLog = ActivityLog::create([
              'user_id' => $user->id,
              'activity' => 'Attempted to view the class schedules of all instructors.',
              'time' => Carbon::now()
          ]);
          return response()->json([
              'message' => 'You are not authorized to view class schedule records.'
          ],401);      //401: Unauthorized
      }
    }

}
