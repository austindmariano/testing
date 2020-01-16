<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class InstructorPreferredSubject extends Pivot
{
    public $incrementing = true;
    protected $table = "instructor_preferred_subjects";
    protected $fillable = [
        'instructor_id', 'subject_id', 'academic_year_id', 'semester_id', 'active', 'last_updated_by'
    ];

    public function instructor(){
        // return $this->belongsTo('App\Instructor')
        //   ->select(array('id', 'first_name', 'last_name', 'middle_name'));
        return $this->belongsTo('App\Instructor')->select('*');
    }

    public function subject(){
        return $this->belongsTo('App\Subject')
          ->select(array('id', 'subject_code', 'subject_description'));
    }

    public function academicYear(){
      return $this->belongsTo('App\AcademicYear')
        ->select(array('id', 'academic_year'));
    }

    public function semester(){
      return $this->belongsTo('App\Semester')
        ->select(array('id', 'semester'));
    }
}
