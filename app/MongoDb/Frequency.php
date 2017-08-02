<?php

namespace App\MongoDb;

class Frequency extends \Moloquent
{
	protected $hidden = ['_id'];

	public function attend()
	{
		return $this->belongsTo('Attend', 'attend_id');
	}

	/*public static function getValue($user, $lesson)
	{
		$out = DB::select("select Frequencies.value "
			. "from Frequencies, Attends "
			. "where Frequencies.idLesson=? and Frequencies.idAttend=Attends.id and Attends.idUser=?",
			[$lesson, $user]);

		return count($out) ? $out[0]->value : "";
	}*/
}
