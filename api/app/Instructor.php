<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Instructor extends Model
{
    protected $fillable = [
      'employee_id', 'first_name', 'middle_name', 'last_name',
      'birth_date', 'gender', 'email', 'contact_no', 'address',
      'city', 'postal_code', 'province', 'work_experience',
      'certifications', 'educational_attainment', 'user_id', 'active',
      'last_updated_by'
    ];

    public function availabilities(){
        // return $this->hasMany('App\InstructorAvailability')
        //   ->select(array('instructor_availabilities.id', 'day', 'time_start', 'time_end', 'academic_year_id', 'academic_year', 'semester_id', 'semester'))
        //   ->join('academic_years', 'instructor_availabilities.academic_year_id', '=', 'academic_years.id')
        //   ->join('semesters', 'instructor_availabilities.semester_id', '=', 'semesters.id')
        //   ->where('active', 1);
        return $this->hasMany('App\InstructorAvailability')
          ->select(
            'id',
            'instructor_id',
            'day',
            'time_start',
            'time_end',
            'academic_year_id',
            'semester_id',
            'active',
            'last_updated_by'
          )->with('academicYear', 'semester');
    }

    public function preferred_subjects(){
        // return $this->hasMany('App\InstructorPreferredSubject')
        //   ->select(array('instructor_preferred_subjects.id', 'subject_id', 'subject_code', 'subject_description', 'academic_year_id', 'academic_year', 'semester_id', 'semester'))
        //   ->join('subjects', 'instructor_preferred_subjects.subject_id', '=', 'subjects.id')
        //   ->join('academic_years', 'instructor_preferred_subjects.academic_year_id', '=', 'academic_years.id')
        //   ->join('semesters', 'instructor_preferred_subjects.semester_id', '=', 'semesters.id')
        //   ->where('instructor_preferred_subjects.active', 1);
        return $this->hasMany('App\InstructorPreferredSubject')
          ->select(
            'id',
            'instructor_id',
            'subject_id',
            'academic_year_id',
            'semester_id',
            'active',
            'last_updated_by'
          );
    }

    public function class_schedules(){
      return $this->hasMany('App\ClassSchedule')->select('*')->with('instructor', 'subject', 'academic_year', 'semester');
    }
}
