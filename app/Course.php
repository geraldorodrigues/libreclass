<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
  protected $table = 'Courses';
  protected $fillable = ['name', 'idInstitution'];

  public function institution()
  {
    return $this->belongsTo('User', 'idInstitution');
  }

  public function periods()
  {
    return $this->hasMany('Period', 'idCourse');
  }

  public function getInstitution()
  {
    return User::find($this->idInstitution);
  }

}
