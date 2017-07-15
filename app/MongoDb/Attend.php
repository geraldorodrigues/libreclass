<?php

namespace App\MongoDb;

use Illuminate\Database\Eloquent\Model;

class Attend extends \Moloquent
{
  protected $table = "Attends";
  protected $fillable = ['user_id', 'idUnit'];

  /*public function getUser()
  {
    return User::find($this->user_id);
  }

  public function getExamsValue($exam)
  {
    $examValue = ExamsValue::where("exam_id", $exam)->where("attend_id", $this->id)->first();
    if ($examValue) {
      return $examValue->value;
    } else {
      return null;
    }
  }

  public function getDescriptiveExam($exam)
  {
    $examDescriptive = DescriptiveExam::where("exam_id", $exam)->where("attend_id", $this->id)->first();
    if ($examDescriptive) {
      return ["description" => $examDescriptive->description, "approved" => $examDescriptive->approved];
    } else {
      return null;
    }
  }

  public function getUnit()
  {
    return Unit::find($this->idUnit);
  }*/
}
