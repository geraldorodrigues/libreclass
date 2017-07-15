<?php

namespace App\MongoDb;

class Period extends \Moloquent
{
	protected $fillable = ['name', 'course_id'];

	public function course()
	{
		return $this->belongsTo('Course', 'course_id');
	}

	public function disciplines()
	{
		return $this->hasMany('Discipline', 'period_id');
	}

	/*public function getCourse()
	{
		return Course::find($this->idCourse);
	}*/

}
