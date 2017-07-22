<?php

namespace App\MySql;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
  protected $table = "Countries";
  protected $connection = 'mysql';
  public $timestamps = false;
}
