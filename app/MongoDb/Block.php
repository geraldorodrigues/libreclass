<?php

namespace App\MongoDb;

use Illuminate\Database\Eloquent\Model;

class Block extends \Moloquent
{
  protected $hidden = ['_id'];
}
