<?php

namespace App\MongoDb;

use Illuminate\Database\Eloquent\Model;

class ExamsValue extends \Moloquent
{
  protected $table = "ExamsValues";

  /*public static function getValue($user, $exam)
  {
    $out = DB::select("select ExamsValues.value "
      . "from ExamsValues, Attends "
      . "where ExamsValues.idExam=? and ExamsValues.idAttend=Attends.id and Attends.idUser=?",
      [$exam, $user]);

		$out = Attend::where('user_id',$user)->examsvalue->where('exam_id',$exam)->where('attend_id',$user)->get(['value']);

    return count($out) ? $out[0]->value : "";
  }*/

	public function student()
  {
    return $this->belongsTo('Attend', 'attend_id')->first()->getUser();
  }

}
