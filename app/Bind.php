<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bind extends Model
{
  protected $table = "Binds";
  public $timestamps = false;

  public function discipline()
  {
    return $this->hasOne('Discipline', 'idDiscipline');
  }
}
