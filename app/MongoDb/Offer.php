<?php

namespace App\MongoDb;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

class Offer extends \Moloquent
{
	use SoftDeletes;
	protected $fillable = ['class_id', 'discipline_id', 'classroom', 'day_period'];

	public function discipline()
	{
		return $this->belongsTo('App\MongoDb\Discipline');
	}


	public function classe()
	{
		return $this->belongsTo('App\MongoDb\Classe', 'class_id');
	}

	public function lectures()
	{
		return $this->hasMany('App\MongoDb\Lecture');
	}

	public function getDiscipline()
	{
		return Discipline::find($this->idDiscipline);
	}

	public function getClass()
	{
		return Classe::find($this->idClass);
	}

	public function units()
	{
		return $this->hasMany('App\MongoDb\Unit');
	}

	public function lectures()
	{
		return $this->hasMany('App\MongoDb\Lecture');
	}


	// public function getFirstUnit()
	// {
	// 	return Unit::where("offer_id", $this->id)->first();
	// }
	//
	// public function getLastUnit()
	// {
	// 	return Unit::where("idOffer", $this->id)->orderBy("value", "desc")->first();
	// }


	/*public function qtdAbsences($idStudent)
	{
		return DB::select("SELECT COUNT(*) as 'qtd'
												FROM Units, Attends, Lessons, Frequencies
												WHERE Units.idOffer=? AND
															Units.id=Lessons.idUnit AND
															Lessons.id=Frequencies.idLesson AND
															Lessons.deleted_at IS NULL AND
															Frequencies.idAttend=Attends.id AND
															Frequencies.value='F' AND
															Attends.idUser=?", [$this->id, $idStudent])[0]->qtd;
	}

	public function qtdUnitAbsences($idStudent, $unitValue)
	{
		return DB::select("SELECT COUNT(*) as 'qtd'
												FROM Units, Attends, Lessons, Frequencies
												WHERE Units.idOffer = ? AND
															Units.value = ? AND
															Units.id = Lessons.idUnit AND
															Lessons.id = Frequencies.idLesson AND
															Lessons.deleted_at IS NULL AND
															Frequencies.idAttend = Attends.id AND
															Frequencies.value = 'F' AND
															Attends.idUser = ?", [$this->id, $unitValue, $idStudent])[0]->qtd;
	}

	public function qtdLessons()
	{
		return DB::select("SELECT COUNT(*) as 'qtd'
												FROM Units, Lessons
												WHERE Units.idOffer=? AND
															Units.id=Lessons.idUnit AND
															Lessons.deleted_at IS NULL", [$this->id])[0]->qtd;
	}

	public function qtdUnitLessons($unitValue)
	{
		return DB::select("SELECT COUNT(*) as 'qtd'
												FROM Units, Lessons
												WHERE Units.idOffer=? AND
															Units.value=? AND
															Units.id=Lessons.idUnit AND
															Lessons.deleted_at IS NULL", [$this->id, $unitValue])[0]->qtd;
	}

	public function getCourse()
	{
		$course = DB::select("SELECT Periods.idCourse FROM Classes, Periods WHERE ?=Classes.id AND Classes.idPeriod=Periods.id", [$this->idClass])[0]->idCourse;
		return Course::find($course);
	}

	public function getTeachers()
	{
		$teachers = [];
		$lectures = Lecture::where("idOffer", $this->id)->get();
		foreach ($lectures as $lecture) {
			$teachers[] = $lecture->getUser()->name;
		}
		return $teachers;
	}*/
}
