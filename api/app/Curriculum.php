<?php

namespace App;
use App\Course;
use App\Strand;
use App\CurriculumSubject;
use Illuminate\Database\Eloquent\Model;

class Curriculum extends Model
{
    protected $table = "curriculums";
    protected $fillable = ['curriculum_title', 'curriculum_desc', 'last_updated_by', 'course_id', 'strand_id'];

    public function course(){
        return $this->belongsTo('App\Course')->select('id','course_code', 'course_desc', 'course_major');
    }

    public function strand(){
        return $this->belongsTo('App\Strand')->select('id', 'strand_code', 'strand_desc');
    }

    public function curriculum_subjects(){
        return $this->hasMany('App\CurriculumSubject')->with('subject', 'semester')->orderBy('id', 'DESC');
    }
}
