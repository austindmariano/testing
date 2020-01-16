<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = ['subject_code','subject_description','units', 'lec', 'lab', 'active', 'last_updated_by'];


    public function curriculum(){
      return $this->hasMany('App\CurriculumSubject');
    }

    public function instructors(){
        return $this->hasMany('InstructorPreferredSubject');
    }

    // public function class_schedules(){
    //     return $this->hasMany('App\ClassSchedule');
    // }
}
