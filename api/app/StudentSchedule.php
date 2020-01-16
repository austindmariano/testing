<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StudentSchedule extends Model
{
  protected $fillable = ['enrollment_id', 'schedule_id', 'prelim_grade', 'midterm_grade',
  'prefinal_grade', 'final_grade', 'semestral', 'remarks', 'figure', 'last_updated_by'];
}
