<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClassSchedule extends Model
{
    protected $table = "class_schedules";
    protected $fillable = ['day', 'time_start', 'time_end', 'subject_id', 'room_id', 'instructor_id', 'block', 'batch', 'class_type', 'last_updated_by', 'academic_year_id', 'semester_id'];

    public function room(){
      return $this->belongsTo('App\Room')->select('id','room_number', 'room_name', 'room_capacity');
    }

    public function instructor(){
      return $this->belongsTo('App\Instructor')->select('id', 'first_name', 'middle_name', 'last_name');
    }

    // public function subject(){
    //   return $this->belongsTo('App\Subject')->select('id', 'subject_code', 'subject_description');
    // }

    public function subject(){
      return $this->belongsTo('App\CurriculumSubject')->select('id', 'subject_id', 'curriculum_id', 'semester_id')->with('subject', 'curriculum', 'semester');
    }

    public function semester(){
        return $this->belongsTo('App\Semester')->select('id', 'semester');
    }

    public function academic_year(){
        return $this->belongsTo('App\AcademicYear')->select('id', 'academic_year');
    }
}
