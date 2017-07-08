<?php

namespace App\MySql;

use Illuminate\Database\Eloquent\Model;

class ExamsValue extends Model
{
  protected $table = "ExamsValues";
  protected $connection = 'mysql';

  public static function getValue($user, $exam)
  {
    $out = DB::select("select ExamsValues.value "
      . "from ExamsValues, Attends "
      . "where ExamsValues.idExam=? and ExamsValues.idAttend=Attends.id and Attends.idUser=?",
      [$exam, $user]);

    return count($out) ? $out[0]->value : "";
  }

}
