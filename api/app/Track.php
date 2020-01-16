<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Track extends Model
{
    protected $fillable = ['track_code', 'track_desc', 'active', 'last_updated_by'];
    // protected $fillable = ['track'];

    public function strands(){
      return $this->hasMany('App\Strand')->orderBy('id', 'DESC');
    }
}
