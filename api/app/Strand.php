<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Strand extends Model
{
    protected $fillable = ['track_id', 'strand_code', 'strand_desc', 'active', 'last_updated_by'];

    public function track(){
      return $this->hasMany('App\Track')->orderBy('id', 'DESC');
    }
}
