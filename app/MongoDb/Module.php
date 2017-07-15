<?php

namespace App\MongoDb;

use Illuminate\Database\Eloquent\Model;

class Module extends \Moloquent
{
  protected $hidden = ["_id"];
}
