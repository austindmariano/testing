<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
  protected $table = "academic_years";
  protected $fillable = ['academic_year', 'last_updated_by'];

  public function class_schedules(){
    return $this->hasMany('App\ClassSchedule');
  }
}
