<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

header('Access-Control-Allow-Origin:  *');
header('Access-Control-Allow-Methods:  POST, GET, OPTIONS, PUT, DELETE');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Origin, Authorization');

//all these routes will have 'v1/' in the url before the actual endpoint
Route::group(['prefix' => 'v1'], function(){
    Route::post('login', 'Auth\LoginController@login');

    //these next routes are inside the auth:api middleware
    //these means that user must be authenticated to be able to access these routes
    Route::group(['middleware' => ['auth:api']], function () {
        Route::get('settings', function(){
          return response()->json(Config::get('settings'));
        });
        Route::post('users', 'Auth\RegisterController@register');
        Route::post('logout', 'Auth\LoginController@logout');
        Route::get('users', 'UserController@index');
        Route::get('users/{user}', 'UserController@show');
        Route::put('users/{user}', 'UserController@update');
        Route::delete('users/{user}', 'UserController@destroy');
        Route::post('privileges', 'UserPrivilegeController@grantPrivilege');
        Route::put('privileges/{userprivilege}', 'UserPrivilegeController@updatePrivilege');
        Route::get('instructors', 'InstructorController@index');
        Route::get('instructors/{instructor}', 'InstructorController@show');
        Route::post('instructors', 'InstructorController@store');
        Route::put('instructors/{instructor}', 'InstructorController@update');
        Route::delete('instructors/{instructor}', 'InstructorController@destroy');
        Route::get('instructor_availabilities', 'InstructorController@availabilities');
        Route::get('instructors/{instructor}/availabilities', 'InstructorController@availabilities');
        Route::post('instructors/{instructor}/availabilities', 'InstructorController@addAvailability');
        Route::put('instructors/{instructor}/availabilities/{instructoravailability}', 'InstructorController@updateAvailability');
        Route::delete('instructors/{instructor}/availabilities/{instructoravailability}', 'InstructorController@deleteAvailability');
        Route::get('instructor_preferred_subjects', 'InstructorController@preferred_subjects');
        Route::get('instructors/{instructor}/preferred_subjects', 'InstructorController@preferred_subjects');
        Route::post('instructors/{instructor}/preferred_subjects', 'InstructorController@addPreferredSubject');
        Route::put('instructors/{instructor}/preferred_subjects/{preferredsubject}', 'InstructorController@updatePreferredSubject');
        Route::delete('instructors/{instructor}/preferred_subjects/{preferredsubject}', 'InstructorController@deletePreferredSubject');
        // Gets All Class Schedule of the specified instructor
        Route::get('instructors/{instructor}/class_schedules', 'InstructorController@instructorClass_Schedules');
        // Gets all class schedule of all instructors
        Route::get('instructors_class_schedules', 'InstructorController@instructors_schedules');



        //--------------------------------------------------------------------------
        //Beginning of Course Routes

        //This GET method will GET all the Course
        Route::get('/courses', 'CourseController@index' );

        //This POST method will ADD new Course
        Route::post('/courses', 'CourseController@store' );

        //This GET method will display a specified record
        Route::get('/courses/{course}', 'CourseController@show');

        //This PUT method will UPDATE a specified record
        Route::put('/courses/{course}', 'CourseController@update');

        //This DELETE method will DELETE a specified record
        Route::delete('/courses/{course}', 'CourseController@destroy');

        //This GET method will display curriculums of specified course
        Route::get('/courses/{course}/curriculums', 'CourseController@showCourseCurriculum');

        //This GET method will display class schedules of specified course
        // Route::get('/courses/{course}/class_schedule', 'CourseController@showCourseClassSchedule');

        //End of Course Routes
        //--------------------------------------------------------------------------


        //--------------------------------------------------------------------------
        //Beginning of Room Routes

        //This GET method will GET all  Rooms
        Route::get('/rooms', 'RoomController@index' );

        //This POST method will ADD new Room
        Route::post('/rooms', 'RoomController@store' );

        //This GET method will display the specified room
        Route::get('/rooms/{room}', 'RoomController@show');

        //This PUT method will UPDATE the specified room
        Route::put('/rooms/{room}', 'RoomController@update');

        //This DELETE method will DELETE the specified room
        Route::delete('/rooms/{room}', 'RoomController@destroy');
        //This DELETE method will DELETE the specified room
        Route::get('/rooms/{room}/schedules', 'RoomController@room_schedules');


        //End of Room Routes
        //--------------------------------------------------------------------------


        //--------------------------------------------------------------------------
        //Beginning of Subject Routes

        //This GET method will GET all the subjects
        Route::get('/subjects', 'SubjectController@index' );

        //This POST method will ADD new subject
        Route::post('/subjects', 'SubjectController@store' );

        //This GET method will display a specified subject
        Route::get('/subjects/{subject}', 'SubjectController@show');

        //This PUT method will UPDATE a specified subject
        Route::put('/subjects/{subject}', 'SubjectController@update');

        //This DELETE method will DELETE a specified subject
        Route::delete('/subjects/{subject}', 'SubjectController@destroy');

        //This GET method will display curriculums of specified subject
        Route::get('/subjects/{subject}/curriculums', 'SubjectController@showSubjectCurriculums');

        //End of Subject Routes
        //--------------------------------------------------------------------------



        //--------------------------------------------------------------------------
        //Beginning of Curriculum Routes

        //This GET method will GET all the curriculums
        Route::get('/curriculums', 'CurriculumController@index' );

        //This POST method will ADD new Curriculum
        Route::post('/curriculums', 'CurriculumController@store' );

        //This GET method will display a specified Curriculum
        Route::get('/curriculums/{curriculum}', 'CurriculumController@show');

        //This PUT method will UPDATE a specified Curriculum
        Route::put('/curriculums/{curriculum}', 'CurriculumController@update');

        //This DELETE method will DELETE a specified Curriculum
        Route::delete('/curriculums/{curriculum}', 'CurriculumController@destroy');

        //This GET method will display the subjects of the specified Curriculum
        Route::get('/curriculums/{curriculum}/subjects', 'CurriculumController@showCurriculumSubjects');

        //End of Curriculum Routes
        //--------------------------------------------------------------------------


        //--------------------------------------------------------------------------
        //Beginning of Curriculum Subject Routes

        //This GET method will return all of the subjects from all curriculums

        Route::get('/curriculum_subjects', 'CurriculumSubjectController@index');

        //This POST method will ADD new subject in a specified curriculum
        Route::post('/curriculum_subjects', 'CurriculumSubjectController@store');

        //This GET method will get subjects of the specified curriculum
        Route::get('/curriculum_subjects/{curriculum_subject}', 'CurriculumSubjectController@show');

        //This PUT method will edit specific subject of the specified curriculum
        Route::put('/curriculum_subjects/{curriculum_subject}', 'CurriculumSubjectController@update');

        // this DELETE method will delete the specified curriculum subject
        Route::delete('/curriculum_subjects/{curriculum_subject}', 'CurriculumSubjectController@destroy');

        // Route::get('/getSubject/{curriculum_subject}', 'CurriculumSubjectController@getSubject');

        //End of Curriculum Subject Routes
        //--------------------------------------------------------------------------


        //--------------------------------------------------------------------------
        //Beginning of Academic Years Routes

        //This GET method will show all of the academic year.
        Route::get('/academic_years', 'AcademicYearController@index');

        //This POST method will create a new academic year.
        Route::post('/academic_years', 'AcademicYearController@store');

        //This GET method will show the specified academic year.
        Route::get('/academic_years/{academic_year}', 'AcademicYearController@show');

        //This PUT method will UPDATE a specified academic year.
        Route::put('/academic_years/{academic_year}', 'AcademicYearController@update');

        //This DELETE method will DELETE a specified academic year.
        Route::delete('/academic_years/{academic_year}', 'AcademicYearController@destroy');

        //End of Academic Years Routes
        //--------------------------------------------------------------------------


        //--------------------------------------------------------------------------
        //Beginning of Semesters Routes

        //This GET method will show all of the semester.
        Route::get('/semesters', 'SemesterController@index');

        //This POST method will create a new semester.
        Route::post('/semesters', 'SemesterController@store');

        //This GET method will show the specified semester.
        Route::get('/semesters/{semester}', 'SemesterController@show');

        //This PUT method will UPDATE a specified semester.
        Route::put('/semesters/{semester}', 'SemesterController@update');

        //This DELETE method will DELETE a specified semester.
        Route::delete('/semesters/{semester}', 'SemesterController@destroy');

        //End of Semesters Routes
        //--------------------------------------------------------------------------

        //--------------------------------------------------------------------------
        //Beginning of Class Schedule Routes

        //This GET method will show all of the class schedule.
        Route::get('/class_schedules', 'ClassScheduleController@index');

        //This POST method will create a new class schedule.
        Route::post('/class_schedules', 'ClassScheduleController@store');

        //This GET method will show the specified class schedule.
        Route::get('/class_schedules/{class_schedule}', 'ClassScheduleController@show');

        //This PUT method will UPDATE a specified class schedule.
        Route::put('/class_schedules/{class_schedule}', 'ClassScheduleController@update');

        //This DELETE method will DELETE a specified class schedule.
        Route::delete('/class_schedules/{class_schedule}', 'ClassScheduleController@destroy');

        //End of Class Schedule Routes
        //--------------------------------------------------------------------------


        //--------------------------------------------------------------------------
        //Beginning of Track Routes

        //This GET method will show all of the Tracks
        Route::get('/tracks', 'TrackController@index');

        //This POST method will create a new Track.
        Route::post('/tracks', 'TrackController@store');

        //This GET method will show the specified Track.
        Route::get('/tracks/{track}', 'TrackController@show');

        //This PUT method will UPDATE a specified Track.
        Route::put('/tracks/{track}', 'TrackController@update');

        //This DELETE method will DELETE a specified Track.
        Route::delete('/tracks/{track}', 'TrackController@destroy');

        //End of Track Routes
        //--------------------------------------------------------------------------


        //--------------------------------------------------------------------------
        //Beginning of Strand Routes

        //This GET method will show all of the Tracks
        Route::get('/strands', 'StrandController@index');

        //This POST method will create a new Track.
        Route::post('/strands', 'StrandController@store');

        //This GET method will show the specified Track.
        Route::get('/strands/{strand}', 'StrandController@show');

        //This PUT method will UPDATE a specified Track.
        Route::put('/strands/{strand}', 'StrandController@update');

        //This DELETE method will DELETE a specified Track.
        Route::delete('/strands/{strand}', 'StrandController@destroy');

        //End of Strand Routes
        //--------------------------------------------------------------------------

        //--------------------------------------------------------------------------
        //Beginning of Student Routes

        //This GET method will show all of the Students
        Route::get('/students', 'StudentController@index');

        //This POST method will create a new student.
        Route::post('/students', 'StudentController@store');

        //This GET method will show the specified student.
        Route::get('/students/{student}', 'StudentController@show');

        //This PUT method will UPDATE a specified student.
        Route::put('/students/{student}', 'StudentController@update');

        //This DELETE method will DELETE a specified student.
        Route::delete('/students/{student}', 'StudentController@destroy');

        //End of Student Routes
        //--------------------------------------------------------------------------


        //--------------------------------------------------------------------------
        //Beginning of Student Requirements Routes

        //This GET method will show all of the student requirements
        Route::get('/student_requirements', 'StudentRequirementController@index');

        //This POST method will create a new student requirements.
        Route::post('/student_requirements', 'StudentRequirementController@store');

        //This GET method will show the specified student requirements.
        Route::get('/student_requirements/{student_requirement}', 'StudentRequirementController@show');

        //This PUT method will UPDATE a specified student requirements.
        Route::put('/student_requirements/{student_requirement}', 'StudentRequirementController@update');

        //This DELETE method will DELETE a specified student requirements.
        Route::delete('/student_requirements/{student_requirement}', 'StudentRequirementController@destroy');

        //End of Student Requirements Routes
        //--------------------------------------------------------------------------

        //--------------------------------------------------------------------------
        //Beginning of Student Schedule Routes

        //This GET method will show all of the student schedule
        Route::get('/student_schedules', 'StudentScheduleController@index');

        //This POST method will create a new student schedule.
        Route::post('/student_schedules', 'StudentScheduleController@store');

        //This GET method will show the specified student schedule.
        Route::get('/student_schedules/{student_schedule}', 'StudentScheduleController@show');

        //This PUT method will UPDATE a specified student schedule.
        Route::put('/student_schedules/{student_schedule}', 'StudentScheduleController@update');

        //This DELETE method will DELETE a specified student schedule.
        Route::delete('/student_schedules/{student_schedule}', 'StudentScheduleController@destroy');

        //End of Student Requirements Routes
        //--------------------------------------------------------------------------

        Route::get('/test', 'ClassScheduleController@test');

        // TRY Eloquent Modeling

        Route::get('/trycourse', 'CourseController@showCourseCurriculum');
  });
});
