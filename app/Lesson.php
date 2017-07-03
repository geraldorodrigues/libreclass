<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
  use SoftDeletingTrait;
  protected $table = "Lessons";
  protected $dates = ['deleted_at'];

  public function unit()
  {
    return $this->belongsTo("Unit", "idUnit");
  }

}
