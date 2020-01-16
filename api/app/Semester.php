<?php

namespace App;

use App\CurriculumSubject;
use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    protected $fillable = ['semester', 'last_updated_by'];

    public function curriculum_subjects(){
      return $this->hasMany('App\CurriculumSubject');
    }

    public function class_schedules(){
      return $this->hasMany('App\ClassSchedule');
    }
}
