<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InstructorAvailability extends Model
{
    protected $table = 'instructor_availabilities';
    protected $fillable = [
        'instructor_id', 'academic_year_id', 'semester_id', 'time_start',
        'time_end', 'day', 'active', 'last_updated_by'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'time_start' => 'time',
        'time_end' => 'time',
    ];

    public function instructor(){
        // return $this->belongsTo('App\Instructor')
        //   ->select(array('id', 'first_name', 'last_name', 'middle_name'));
        return $this->belongsTo('App\Instructor')->select('*');
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
