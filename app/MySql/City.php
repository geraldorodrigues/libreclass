<?php

namespace App\MySql;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
  protected $table = "Cities";
  protected $connection = 'mysql';
  public $timestamps = false;
}
