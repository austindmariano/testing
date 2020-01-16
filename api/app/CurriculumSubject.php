<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CurriculumSubject extends Pivot
{
    protected $table = "curriculum_subjects";

    protected $fillable = ['subject_id', 'year_level', 'curriculum_id', 'semester_id', 'last_updated_by'];

    public function curriculum(){
        return $this->belongsTo('App\Curriculum')->select('id', 'curriculum_title', 'curriculum_desc', 'course_id')->with('course');
    }

    public function subject(){
        return $this->belongsTo('App\Subject')->select('id', 'subject_code', 'subject_description', 'units', 'lec', 'lab', 'active');
    }

    public function prerequisites(){
      return $this->hasMany('App\Prerequisite');
    }
    // added this
    public function class_schedules(){
        return $this->hasMany('App\ClassSchedule');
    }

    public function semester(){
      return $this->belongsTo('App\Semester')->select('id', 'semester');
    }
}
