<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DescriptiveExam extends Model
{
  protected $table = "DescriptiveExams";

  public function student()
  {
    return $this->belongsTo('Attend', 'idAttend')->first()->getUser();
  }

}
