<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = ['room_number', 'room_name', 'room_type', 'room_type', 'room_capacity', 'active', 'last_updated_by'];

    public function class_schedules(){
      // original code
      // return $this->hasMany('App\ClassSchedule')->select('*')->orderBy('id', 'DESC');

      $data = $this->hasMany('App\ClassSchedule')->select('*')->orderBy('id', 'DESC')->with('subject');

      return $data;
    }
}
