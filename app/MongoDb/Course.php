<?php

namespace App\MongoDb;

use Illuminate\Database\Eloquent\Model;

class Course extends \Moloquent
{
  protected $fillable = ['name', 'institution_id'];

  public function institution()
  {
    return $this->belongsTo('User', 'institution_id');
  }

  public function periods()
  {
    return $this->hasMany('Period', 'course_id');
  }

  public function getInstitution()
  {
    return User::find($this->idInstitution);
  }

}
