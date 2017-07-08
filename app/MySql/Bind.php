<?php

namespace App\MySql;

use Illuminate\Database\Eloquent\Model;

class Bind extends Model
{
  protected $table = "Binds";
  protected $connection = 'mysql';
  public $timestamps = false;

  public function discipline()
  {
    return $this->hasOne('Discipline', 'idDiscipline');
  }
}
