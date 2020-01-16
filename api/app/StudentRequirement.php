<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StudentRequirement extends Model
{
  protected $fillable = ['student_number', 'tor', 'good_moral', 'form_137', 'birth_cercificate'];


}
