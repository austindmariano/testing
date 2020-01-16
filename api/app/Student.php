<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
  protected $fillable = [
    'student_number', 'first_name', 'middle_name', 'last_name', 'suffix_name', 'gender',
    'address', 'civil_status', 'city', 'postal', 'province', 'telephone',
    'cellphone', 'email', 'birth_date', 'birth_place', 'father_name', 'mother_name',
    'contact_person', 'contact_address', 'contact_number', 'blood_type', 'photo_url', 'user_id',
    'active', 'academic_status', 'student_status', 'school_last_attended', 'school_address', 'last_track',
    'last_strand', 'last_course', 'created_at', 'updated_at', 'last_updated_by'
  ];

  public function enrollment(){
    return $this->hasMany('App\Enrollment')->select('*');
  }
}
