<?php

namespace App;
use App\Course;
use Illuminate\Database\Eloquent\Model;

class Prerequisite extends Model
{
    protected $table = "prerequisites";
    protected $fillable = ['curriculum_subject_id', 'subject_id'];

    public function subject(){
        return $this->belongsTo('App\Subject');
    }

    public function curriculum_subject(){
        return $this->belongsTo('App\CurriculumSubject');
    }
}
