<?php

namespace App\MySql;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
  use SoftDeletingTrait;
  protected $table = "Lessons";
  protected $connection = 'mysql';
  protected $dates = ['deleted_at'];

  public function unit()
  {
    return $this->belongsTo("Unit", "idUnit");
  }

}
