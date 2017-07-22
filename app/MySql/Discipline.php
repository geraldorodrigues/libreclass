<?php

namespace App\MySql;

use Illuminate\Database\Eloquent\Model;

class Discipline extends Model
{
  protected $table = 'Disciplines';
  protected $connection = 'mysql';
  protected $fillable = ['name', 'idPeriod'];

  public function period()
  {
    return $this->belongsTo('Period', 'idPeriod');
  }

  public function getPeriod()
  {
    return Period::find($this->idPeriod);
  }
}
