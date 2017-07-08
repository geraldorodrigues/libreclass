<?php

namespace App\MySql;

use Illuminate\Database\Eloquent\Model;

class DescriptiveExam extends Model
{
  protected $table = "DescriptiveExams";
  protected $connection = 'mysql';

  public function student()
  {
    return $this->belongsTo('Attend', 'idAttend')->first()->getUser();
  }

}
