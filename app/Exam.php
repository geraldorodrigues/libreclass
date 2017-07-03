<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
  protected $table = "Exams";

  public function unit()
  {
    return $this->belongsTo("Unit", "idUnit");
  }

  public function descriptive_exams()
  {
    $descriptive_exams = $this->hasMany("DescriptiveExam", "idExam")->get();
    foreach ($descriptive_exams as $key => $descriptive_exam) {
      $descriptive_exams[$key]['student'] = $descriptive_exam->student();
    }
    return $descriptive_exams;
  }

}
