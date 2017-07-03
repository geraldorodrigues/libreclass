<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Classe extends Model
{
  protected $table = "Classes";
  protected $fillable = ['name', 'idPeriod', 'class'];

  public function period()
  {
    return $this->belongsTo('Period', 'idPeriod');
  }

  public function getPeriod()
  {
    return Period::find($this->idPeriod);
  }

  public function fullName()
  {
    return "[$this->class] $this->name";
  }
}
