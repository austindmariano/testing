<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
  protected $fillable = ['academic_year_id', 'semester_id', 'student_id', 'year_level',
  'curriculum_id', 'created_at', 'updated_at', 'last_updated_by', 'course_id', 'strand_id'];
    
    public function student(){
      return $this->belongsTo('App\Student')->select('*');
    }
}
