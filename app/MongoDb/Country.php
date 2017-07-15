<?php

namespace App\MongoDb;

use Illuminate\Database\Eloquent\Model;

class Country extends \Moloquent
{
  protected $hidden = ['_id'];
}
