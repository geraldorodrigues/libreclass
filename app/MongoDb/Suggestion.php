<?php

namespace App\MongoDb;

use Illuminate\Database\Eloquent\Model;

class Suggestion extends \Moloquent
{
  protected $hidden = ['_id'];
}
