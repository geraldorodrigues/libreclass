<?php

namespace App\MongoDb;

use Illuminate\Database\Eloquent\Model;

class Lesson extends \Moloquent
{
  use SoftDeletingTrait;
  protected $dates = ['deleted_at'];

  public function unit()
  {
    return $this->belongsTo("Unit", "unit_id");
  }

}
