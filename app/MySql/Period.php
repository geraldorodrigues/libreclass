<?php

namespace App\MySql;

use Illuminate\Database\Eloquent\Model;

class Period extends Model
{
  protected $table = 'Periods';
  protected $connection = 'mysql';
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
