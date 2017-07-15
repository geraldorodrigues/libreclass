<?php

namespace App\MongoDb;

use Illuminate\Database\Eloquent\Model;

class City extends \Moloquent
{
  protected $hidden = ['_id'];
}
