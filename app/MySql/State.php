<?php

namespace App\MySql;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
  protected $table = "States";
  protected $connection = 'mysql';
  public $timestamps = false;
}
