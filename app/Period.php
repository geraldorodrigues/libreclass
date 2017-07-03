<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Period extends Model
{
  protected $table = 'Periods';
  protected $fillable = ['name', 'idCourse'];

  public function course()
  {
    return $this->belongsTo('Course', 'idCourse');
  }

  public function disciplines()
  {
    return $this->hasMany('Discipline', 'idPeriod');
  }

  public function getCourse()
  {
    return Course::find($this->idCourse);
  }

}
