<?php

namespace App\MySql;

use Illuminate\Database\Eloquent\Model;

class Suggestion extends Model
{

  protected $table = "Suggestions";
  protected $connection = 'mysql';
  public $timestamps = false;

}
